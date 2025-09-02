<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MasterFile;
use App\Models\MediaCoordinatorTracking;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Schema;

class MediaCoordinatorController extends Controller
{
    /**
     * Map UI tabs -> subcategory strings stored in media_monthly_details.subcategory
     * (case-insensitive match in queries).
     */
    private const TAB_TO_CATEGORY = [
        'content'  => 'Content Calendar',
        'editing'  => 'Artwork Editing',
        'schedule' => 'Posting Scheduling',
        'report'   => 'Report Posting',
        'valueadd' => 'Value Add',
    ];

    // Canonical labels (untuk display / exact match kalau ada)
    private const SOCIAL_MEDIA_PRODUCTS = [
        'TikTok Management',
        'YouTube Management',
        'FB/IG Management',
        'FB Sponsored Ads',
        'TikTok Management Boost',
        'Giveaways/ Contest Management',
        'Xiaohongshu Management',
    ];

    public function index(Request $request)
{
    $activeTab = $request->get('tab', 'content');
    $scope     = $request->get('scope'); // month_year | month_only | year_only | all
    $year      = (int)($request->get('year') ?: now()->year);
    $month     = $request->filled('month') ? (int)$request->get('month') : null;

    // default scope yang masuk akal
    $scope = $scope ?: ($month ? 'month_year' : 'year_only');

    // tab -> label (untuk nanti jika dipakai simpan subcategory)
    $category = self::TAB_TO_CATEGORY[$activeTab] ?? self::TAB_TO_CATEGORY['content'];

    // ====== BASE LIST DARI master_files (MEDIA-ONLY) ======
    // Rule: product_category mengandung "media" (ci) ATAU product termasuk daftar produk media (ci)
    $masters = MasterFile::query()
        ->select(['id','company','client','product','product_category'])
        ->where(function ($q) {
            $q->whereRaw('LOWER(COALESCE(product_category, "")) LIKE ?', ['%media%'])
              ->orWhereIn(
                  DB::raw('LOWER(COALESCE(product, ""))'),
                  array_map('strtolower', self::SOCIAL_MEDIA_PRODUCTS) // atau MEDIA_PRODUCTS jika ini nama konstanta kamu
              );
        })
        ->orderByRaw('COALESCE(company, client) ASC')
        ->get();

    Log::info("MediaCoordinator: masters from master_files = {$masters->count()}");

    $ids = $masters->pluck('id')->all();

    // ====== Lookup per master (tetap sama) ======
    $latestPerMaster = function (string $table) use ($ids, $year, $month, $scope) {
        if (empty($ids)) return collect();

        $q = DB::table($table)->whereIn('master_file_id', $ids);

        // Kalau scope-nya bukan month_only, batasi ke tahun aktif
        if ($scope !== 'month_only') {
            $q->where('year', $year);
        }

        if ($month) {
            $q->where('month', $month);
            return $q->get()->keyBy('master_file_id');
        }

        // Tanpa month → ambil baris terbaru per master_file_id
        $rows = $q->orderByDesc('updated_at')->get();
        $picked = [];
        foreach ($rows as $r) {
            $key = $r->master_file_id;
            if (!isset($picked[$key])) $picked[$key] = $r;
        }
        return collect($picked);
    };

    // Build 5 map (tetap)
    $contentMap  = $latestPerMaster('content_calendars');
    $editingMap  = $latestPerMaster('artwork_editings');
    $scheduleMap = $latestPerMaster('posting_schedulings');
    $reportMap   = $latestPerMaster('media_reports');
    $valueMap    = $latestPerMaster('media_value_adds');

    // Label periode untuk Blade (tetap)
    $periodLabel = $this->formatPeriodLabel($scope, $month, $year);

    return view('coordinators.media', [
        'activeTab'      => $activeTab,
        'scope'          => $scope,
        'periodLabel'    => $periodLabel,
        'masters'        => $masters,
        'year'           => $year,
        'month'          => $month,
        'contentMap'     => $contentMap,
        'editingMap'     => $editingMap,
        'scheduleMap'    => $scheduleMap,
        'reportMap'      => $reportMap,
        'valueMap'       => $valueMap,
        'socialProducts' => self::SOCIAL_MEDIA_PRODUCTS,
    ]);
}

    private function formatPeriodLabel(string $scope, ?int $month, int $year): string
    {
        if ($scope === 'all') {
            return 'All Months (All Years)';
        }
        if ($scope === 'month_only' && $month) {
            return Carbon::create()->startOfYear()->month($month)->format('F') . ' (All Years)';
        }
        if ($scope === 'year_only') {
            return "All Months {$year}";
        }
        if ($month) {
            return Carbon::create($year, $month, 1)->format('F Y');
        }
        return "All Months {$year}";
    }

    // In MediaOngoingJobController.php, replace your upsert method with this:
    public function upsert(Request $request)
{
    try {
        Log::info('Media upsert: request in', $request->all());

        // 1) Validasi dasar
        $data = $request->validate([
            'section'        => ['required', Rule::in(['content','editing','schedule','report','valueadd'])],
            'master_file_id' => ['required','integer','exists:master_files,id'],
            'year'           => ['required','integer','min:2000','max:2100'],
            'month'          => ['required','integer','min:1','max:12'],
            'field'          => ['required','string','max:64'],
            'value'          => ['nullable'],
        ]);

        // 2) Mapping UI → DB (terutama meta_mgr → meta_manager)
        $fieldMapping = [
            'meta_mgr' => 'meta_manager',
        ];
        $requestedField = $data['field'];
        $dbField = $fieldMapping[$requestedField] ?? $requestedField;

        // 3) Allowed fields per section (pakai nama kolom DB akhir)
        $allowedBySection = [
            'content'  => ['total_artwork','pending','draft_wa','approved'],
            'editing'  => ['total_artwork','pending','draft_wa','approved'],
            'schedule' => ['total_artwork','crm','meta_manager','tiktok_ig_draft'],
            'report'   => ['pending','completed'],
            'valueadd' => ['quota','completed'],
        ];
        if (!in_array($dbField, $allowedBySection[$data['section']], true)) {
            return response()->json(['ok' => false, 'error' => "Field '{$requestedField}' not allowed for section {$data['section']}"], 422);
        }

        // 4) Tentukan model
        $modelClass = MCT::forSection($data['section']);

        // 5) Keys unik per slot (no overlap)
        $keys = [
            'master_file_id' => (int) $data['master_file_id'],
            'year'           => (int) $data['year'],
            'month'          => (int) $data['month'],
        ];

        // 6) Ambil/siapkan record slot
        /** @var \Illuminate\Database\Eloquent\Model $record */
        $record = $modelClass::firstOrNew($keys);

        // 7) Normalisasi nilai sesuai tipe
        $value = $data['value'];

        // helper truthy untuk string
        $isTruthy = static function ($v): bool {
            if (is_bool($v)) return $v;
            if (is_numeric($v)) return (int)$v === 1;
            $v = strtolower(trim((string)$v));
            return in_array($v, ['1','true','on','yes','y'], true);
        };

        // boolean fields
        if (in_array($dbField, ['draft_wa','approved','tiktok_ig_draft'], true)) {
            $value = $isTruthy($value) ? 1 : 0;
        }
        // report.completed = boolean
        elseif ($dbField === 'completed' && $data['section'] === 'report') {
            $value = $isTruthy($value) ? 1 : 0;
        }
        // valueadd.completed = bisa integer (progress) atau boolean
        elseif ($dbField === 'completed' && $data['section'] === 'valueadd') {
            $value = is_numeric($value) ? (int)$value : ($isTruthy($value) ? 1 : 0);
        }
        // integer-ish fields
        elseif (in_array($dbField, ['total_artwork','pending','crm','meta_manager','quota'], true)) {
            $value = ($value === '' || $value === null) ? null : (int) $value;
        }
        // sisanya biarkan apa adanya (string/date dll)

        // 8) Guard: pastikan kolom DB bisa diisi
        if (!in_array($dbField, (new $modelClass)->getFillable(), true)) {
            Log::warning('Rejected: not fillable', ['model' => $modelClass, 'field' => $dbField]);
            return response()->json([
                'ok' => false,
                'error' => "Field '{$dbField}' is not fillable on {$modelClass}",
            ], 422);
        }

        // 9) Set nilai + simpan
        // pastikan key ikut terisi kalau record baru
        $record->master_file_id = $keys['master_file_id'];
        $record->year           = $keys['year'];
        $record->month          = $keys['month'];
        $record->{$dbField}     = $value;

        Log::info('Media upsert: saving', ['model' => $modelClass, 'keys' => $keys, 'field' => $dbField, 'value' => $value, 'exists' => $record->exists]);

        $record->save();

        Log::info('Media upsert: saved OK', ['id' => $record->id]);

        return response()->json([
            'ok'    => true,
            'id'    => $record->id,
            'field' => $dbField,
            'value' => $value,
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::warning('Media upsert: validation failed', ['errors' => $e->errors()]);
        return response()->json(['ok' => false, 'errors' => $e->errors()], 422);

    } catch (\Throwable $e) {
        Log::error('Media upsert: server error '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
        return response()->json(['ok' => false, 'error' => 'Server error: '.$e->getMessage()], 500);
    }
}

}
