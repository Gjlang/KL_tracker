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
        Log::info('Upsert request received', $request->all());

        $data = $request->validate([
            'section'        => 'required|string|in:content,editing,schedule,report,valueadd',
            'master_file_id' => 'required|exists:master_files,id',
            'year'           => 'required|integer|min:2000|max:2100',
            'month'          => 'required|integer|min:1|max:12',
            'field'          => 'required|string',
            'value'          => 'nullable',
        ]);
         if (empty($data['year'])) {
            $data['year'] = (int) now()->year;
        }

        // Get model class
        $modelClass = \App\Models\MediaCoordinatorTracking::forSection($data['section']);

        // Validate field is allowed for this section
        $allowedFields = [
            'content'  => ['total_artwork', 'pending', 'draft_wa', 'approved'],
            'editing'  => ['total_artwork', 'pending', 'draft_wa', 'approved'],
            'schedule' => ['total_artwork', 'crm', 'meta_mgr', 'tiktok_ig_draft'],
            'report'   => ['pending', 'completed'],
            'valueadd' => ['quota', 'completed'],
        ];

        if (!in_array($data['field'], $allowedFields[$data['section']], true)) {
            return response()->json(['ok' => false, 'error' => 'Field not allowed'], 422);
        }

        // CRITICAL: Map UI field names to database column names
        $fieldMapping = [
            'meta_mgr' => 'meta_manager', // UI sends 'meta_mgr', DB expects 'meta_manager'
        ];
        $dbField = $data['field'];

        // Build unique keys
        $keys = [
            'master_file_id' => (int)$data['master_file_id'],
            'year' => (int)$data['year'],
        ];

        $keys['month'] = (int)$data['month'];

        Log::info('Looking for record', ['model' => $modelClass, 'keys' => $keys, 'field' => $dbField]);

        // Find or create record
        $record = $modelClass::firstOrNew($keys);

        // Process value based on field type
        $value = $data['value'];

        // Boolean fields
        if (in_array($data['field'], ['draft_wa', 'approved', 'tiktok_ig_draft'], true)) {
            $value = (bool)$value ? 1 : 0;
        }
        // Integer fields
        elseif (in_array($data['field'], ['total_artwork', 'pending', 'crm', 'meta_mgr', 'quota'], true)) {
            $value = ($value === '' || $value === null) ? null : (int)$value;
        }
        // Special case: valueadd completed can be integer
        elseif ($data['section'] === 'valueadd' && $data['field'] === 'completed') {
            $value = is_numeric($value) ? (int)$value : ((bool)$value ? 1 : 0);
        }
        // Report completed is boolean
        elseif ($data['field'] === 'completed') {
            $value = (bool)$value ? 1 : 0;
        }

        // Set the field value using the mapped database field name
        $record->{$dbField} = $value;

        Log::info('Saving record', [
            'dbField' => $dbField,
            'value' => $value,
            'record_exists' => $record->exists
        ]);

        $saved = $record->save();

        if ($saved) {
            Log::info('Record saved successfully', ['id' => $record->id]);
            return response()->json([
                'ok' => true,
                'id' => $record->id,
                'field' => $dbField,
                'value' => $value
            ]);
        } else {
            Log::error('Failed to save record');
            return response()->json(['ok' => false, 'error' => 'Failed to save'], 500);
        }

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::warning('Validation error', ['errors' => $e->errors()]);
        return response()->json(['ok' => false, 'errors' => $e->errors()], 422);
    } catch (\Exception $e) {
        Log::error('Upsert failed: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'request' => $request->all()
        ]);
        return response()->json([
            'ok' => false,
            'error' => 'Server error: ' . $e->getMessage()
        ], 500);
    }
}
}
