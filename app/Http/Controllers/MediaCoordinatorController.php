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

    // upsert method remains the same
   public function upsert(Request $request)
{
    try {
        $data = $request->validate([
            'section'        => 'required|in:content,editing,schedule,report,valueadd',
            'master_file_id' => 'required|integer|exists:master_files,id',
            'year'           => 'nullable|integer|min:2000|max:2100',
            'month'          => 'nullable|integer|min:1|max:12', // <- boleh null
            'field'          => 'required|string',
            'value'          => 'nullable',
        ]);

        // Tab -> table (samakan dgn index())
        $TABLE_BY_SECTION = [
            'content'  => 'content_calendars',
            'editing'  => 'artwork_editings',
            'schedule' => 'posting_schedulings',
            'report'   => 'media_reports',
            'valueadd' => 'media_value_adds',
        ];

        // Field yang boleh per tab (UI)
        $ALLOWED = [
            'content'  => ['total_artwork','pending','draft_wa','approved'],
            'editing'  => ['total_artwork','pending','draft_wa','approved'],
            'schedule' => ['total_artwork','crm','meta_mgr','tiktok_ig_draft'],
            'report'   => ['pending','completed'],
            'valueadd' => ['quota','completed'], // <- sesuai kolom di screenshot
        ];

        // Alias UI -> kolom DB (kalau beda nama)
        $FIELD_ALIAS = [
            'schedule' => [
                'meta_mgr' => 'meta_manager', // kalau di DB namanya 'meta_manager'
            ],
        ];

        $section = $data['section'];
        $uiField = $data['field'];

        if (!isset($TABLE_BY_SECTION[$section])) {
            return response()->json(['ok'=>false,'error'=>'Unknown section'], 422);
        }
        if (!in_array($uiField, $ALLOWED[$section] ?? [], true)) {
            return response()->json(['ok'=>false,'error'=>'Invalid field'], 422);
        }

        $table  = $TABLE_BY_SECTION[$section];
        $column = $FIELD_ALIAS[$section][$uiField] ?? $uiField;

        // Jika alias di atas belum tepat, coba fallback otomatis:
        if (!Schema::hasColumn($table, $column)) {
            // fallback populer yang sering beda penamaan
            $fallbacks = [
                'meta_manager'    => 'meta_mgr',
                'meta_mgr'        => 'meta_manager',
            ];
            if (isset($fallbacks[$column]) && Schema::hasColumn($table, $fallbacks[$column])) {
                $column = $fallbacks[$column];
            }
        }

        // Normalisasi nilai
        $raw = $data['value'];
        $BOOL_FIELDS = ['draft_wa','approved','tiktok_ig_draft','completed'];
        $INT_FIELDS  = ['total_artwork','pending','crm','quota'];

        if (in_array($uiField, $BOOL_FIELDS, true)) {
            // khusus valueadd.completed dukung angka
            if ($section === 'valueadd' && $uiField === 'completed' && is_numeric($raw)) {
                $value = (int)$raw;
            } else {
                $truthy = ['1', 1, true, 'true', 'on', 'yes', 'checked'];
                $value  = in_array($raw, $truthy, true) ? 1 : 0;
            }
        } elseif (in_array($uiField, $INT_FIELDS, true)) {
            $value = ($raw === '' || $raw === null) ? null : (int)$raw;
        } else {
            $value = is_string($raw) ? mb_substr(trim($raw), 0, 255) : ($raw ?? null);
        }

        // Kunci upsert: year wajib ada (default ke tahun aktif), month opsional
        $year  = $data['year']  ?? (int) now()->year;
        $month = $data['month'] ?? null;

        $keys = [
            'master_file_id' => (int)$data['master_file_id'],
            'year'           => $year,
        ];
        if ($month !== null) {
            $keys['month'] = (int)$month;
        }

        $values = [
            $column      => $value,
            'updated_at' => now(),
        ];
        $exists = DB::table($table)->where($keys)->exists();
        if (!$exists) {
            $values['created_at'] = now();
        }

        DB::table($table)->updateOrInsert($keys, $values);

        return response()->json(['ok'=>true, 'table'=>$table, 'column'=>$column, 'value'=>$value, 'keys'=>$keys]);
    } catch (ValidationException $e) {
        Log::warning('coordinator.media.upsert 422', ['errors'=>$e->errors(), 'payload'=>$request->all()]);
        return response()->json(['ok'=>false, 'errors'=>$e->errors()], 422);
    } catch (\Throwable $e) {
        Log::error('coordinator.media.upsert fail: '.$e->getMessage(), ['payload'=>$request->all()]);
        return response()->json(['ok'=>false, 'error'=>$e->getMessage()], 500);
    }
}
}
