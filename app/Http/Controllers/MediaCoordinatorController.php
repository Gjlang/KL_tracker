<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MasterFile;
use App\Models\MediaCoordinatorTracking;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;



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

    // ====== Map builder (single-table) ======
    // Baca dari media_coordinator_trackings per-section, dan kembalikan map: [master_file_id => (object)payload]
    $mapForSection = function (string $section) use ($ids, $year, $month, $scope) {
        if (empty($ids)) return collect();

        $q = MediaCoordinatorTracking::query()
            ->whereIn('master_file_id', $ids)
            ->where('section', $section);

        // Kalau scope-nya bukan month_only, batasi ke tahun aktif
        if ($scope !== 'month_only') {
            $q->where('year', $year);
        }

        if ($month) {
            $q->where('month', $month);
            $rows = $q->get();
            return $rows->mapWithKeys(function ($r) {
                $payload = $r->payload ?? [];
                return [$r->master_file_id => (object) $payload];
            });
        }

        // Tanpa month → ambil baris terbaru per master_file_id
        $rows = $q->orderByDesc('updated_at')->get();
        $picked = [];
        foreach ($rows as $r) {
            $key = $r->master_file_id;
            if (!isset($picked[$key])) {
                $picked[$key] = (object) ($r->payload ?? []);
            }
        }
        return collect($picked);
    };

    // Build 5 map (dari table tunggal)
    $contentMap  = $mapForSection('content');
    $editingMap  = $mapForSection('editing');
    $scheduleMap = $mapForSection('schedule');
    $reportMap   = $mapForSection('report');
    $valueMap    = $mapForSection('valueadd');
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
        Log::info('Media upsert (single-table) IN', $request->all());

        // 1) Validasi dasar
        $data = $request->validate([
            'section'        => ['required', Rule::in(['content','editing','schedule','report','valueadd'])],
            'master_file_id' => ['required','integer','exists:master_files,id'],
            'year'           => ['nullable','integer','min:2000','max:2100'],
            'month'          => ['nullable','integer','min:1','max:12'],
            'field'          => ['required','string','max:64'],
            'value'          => ['nullable'],

            // (opsional) field snapshot kalau kamu kirim bersamaan
            'date_in_snapshot'  => ['sometimes','nullable','string','max:255'],
            'company_snapshot'  => ['sometimes','nullable','string','max:255'],
            'title'             => ['sometimes','nullable','string','max:255'],
            'client_bp'         => ['sometimes','nullable','string','max:255'],
            'x'                 => ['sometimes','nullable','string','max:255'],
            'material_reminder' => ['sometimes','nullable','string','max:255'],
            'material_received' => ['sometimes','nullable','string','max:255'],
            'video_done'        => ['sometimes','nullable','string','max:255'],
            'video_approval'    => ['sometimes','nullable','string','max:255'],
            'video_approved'    => ['sometimes','nullable','string','max:255'],
            'video_scheduled'   => ['sometimes','nullable','string','max:255'],
            'video_posted'      => ['sometimes','nullable','date'],
            'post_link'         => ['sometimes','nullable','string','max:255'],
        ]);

        // Default periode bila kosong
        $year  = (int)($data['year']  ?? now()->year);
        $month = (int)($data['month'] ?? now()->month);

        // 2) Mapping nama field dari UI → key JSON payload
        $fieldMap = [
            'meta_mgr' => 'meta_manager',
        ];
        $requestedField = $data['field'];
        $field = $fieldMap[$requestedField] ?? $requestedField;

        // 3) Whitelist per-section (key payload yang diizinkan)
        $allowed = [
            'content'  => ['total_artwork','pending','draft_wa','approved'],
            'editing'  => ['total_artwork','pending','draft_wa','approved'],
            'schedule' => ['total_artwork','crm','meta_manager','tiktok_ig_draft'],
            'report'   => ['pending','completed'],
            'valueadd' => ['quota','completed'],
        ];
        if (!in_array($field, $allowed[$data['section']], true)) {
            return response()->json(['ok' => false, 'error' => "Field '{$requestedField}' not allowed for section {$data['section']}"], 422);
        }

        // 4) Normalisasi tipe value (boolean/int/string) sesuai field
        $value = $data['value'];

        $isTruthy = static function ($v): bool {
            if (is_bool($v)) return $v;
            if (is_numeric($v)) return (int)$v === 1;
            $v = strtolower(trim((string)$v));
            return in_array($v, ['1','true','on','yes','y'], true);
        };

        if (in_array($field, ['draft_wa','approved','tiktok_ig_draft'], true)) {
            $value = $isTruthy($value) ? 1 : 0;
        } elseif ($field === 'completed' && $data['section'] === 'report') {
            $value = $isTruthy($value) ? 1 : 0;
        } elseif ($field === 'completed' && $data['section'] === 'valueadd') {
            $value = is_numeric($value) ? (int)$value : ($isTruthy($value) ? 1 : 0);
        } elseif (in_array($field, ['total_artwork','pending','crm','meta_manager','quota'], true)) {
            $value = ($value === '' || $value === null) ? null : (int)$value;
        }
        // sisanya biarkan (string/date/link, dll.)

        // 5) Ambil/siapkan 1 baris unik per (master_file_id, year, month, section)
        $row = MediaCoordinatorTracking::firstOrCreate(
            [
                'master_file_id' => (int)$data['master_file_id'],
                'year'           => $year,
                'month'          => $month,
                'section'        => $data['section'],
            ],
            [
                // inisialisasi snapshot bila dikirim pertama kali
                'date_in_snapshot'  => $data['date_in_snapshot']  ?? null,
                'company_snapshot'  => $data['company_snapshot']  ?? null,
                'title'             => $data['title']             ?? null,
                'client_bp'         => $data['client_bp']         ?? null,
                'x'                 => $data['x']                 ?? null,
                'material_reminder' => $data['material_reminder'] ?? null,
                'material_received' => $data['material_received'] ?? null,
                'video_done'        => $data['video_done']        ?? null,
                'video_approval'    => $data['video_approval']    ?? null,
                'video_approved'    => $data['video_approved']    ?? null,
                'video_scheduled'   => $data['video_scheduled']   ?? null,
                'video_posted'      => $data['video_posted']      ?? null,
                'post_link'         => $data['post_link']         ?? null,
                'payload'           => [],
            ]
        );

        // 6) Update JSON payload[field] = value
        $payload = $row->payload ?? [];
        $payload[$field] = $value;

        // 7) (opsional) perbarui snapshot bila dikirim di request ini
        $row->fill([
            'payload'           => $payload,
            'date_in_snapshot'  => $data['date_in_snapshot']  ?? $row->date_in_snapshot,
            'company_snapshot'  => $data['company_snapshot']  ?? $row->company_snapshot,
            'title'             => $data['title']             ?? $row->title,
            'client_bp'         => $data['client_bp']         ?? $row->client_bp,
            'x'                 => $data['x']                 ?? $row->x,
            'material_reminder' => $data['material_reminder'] ?? $row->material_reminder,
            'material_received' => $data['material_received'] ?? $row->material_received,
            'video_done'        => $data['video_done']        ?? $row->video_done,
            'video_approval'    => $data['video_approval']    ?? $row->video_approval,
            'video_approved'    => $data['video_approved']    ?? $row->video_approved,
            'video_scheduled'   => $data['video_scheduled']   ?? $row->video_scheduled,
            'video_posted'      => $data['video_posted']      ?? $row->video_posted,
            'post_link'         => $data['post_link']         ?? $row->post_link,
        ])->save();

        Log::info('Media upsert (single-table) OK', ['id' => $row->id, 'field' => $field, 'value' => $value]);

        return response()->json([
            'ok'    => true,
            'id'    => $row->id,
            'field' => $field,
            'value' => $value,
            'payload' => $row->payload,
        ]);
    } catch (ValidationException $e) {
        Log::warning('Media upsert validation failed', ['errors' => $e->errors()]);
        return response()->json(['ok' => false, 'errors' => $e->errors()], 422);
    } catch (\Throwable $e) {
        Log::error('Media upsert server error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
        return response()->json(['ok' => false, 'error' => 'Server error: '.$e->getMessage()], 500);
    }
}

}
