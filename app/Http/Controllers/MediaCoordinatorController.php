<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MasterFile;
use App\Models\MediaCoordinatorTracking;
use Illuminate\Support\Facades\Log;

class MediaCoordinatorController extends Controller
{
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
        $year  = (int)($request->get('year') ?: now()->year);
        $month = $request->filled('month') ? (int)$request->get('month') : null;

        // FIXED: Much more inclusive query - cast both sides to lowercase for better matching
        $masters = MasterFile::query()
            ->select(['id','company','client','product','product_category'])
            ->where(function ($q) {
                // Option 1: Include by product_category containing 'media'
                $q->whereRaw('LOWER(product_category) LIKE ?', ['%media%'])

                // Option 2: OR include by product name patterns (much more inclusive)
                ->orWhere(function ($qq) {
                    // Exact matches first (case insensitive)
                    foreach (self::SOCIAL_MEDIA_PRODUCTS as $product) {
                        $qq->orWhereRaw('LOWER(product) = ?', [strtolower($product)]);
                    }

                    // Then LIKE patterns for variations
                    $patterns = [
                        '%social%media%',           // any social media mention
                        '%tiktok%',                 // any tiktok mention
                        '%youtube%',                // any youtube mention
                        '%facebook%',               // facebook variations
                        '%instagram%',              // instagram variations
                        '%fb%ig%',                  // FB IG variations
                        '%ig%fb%',                  // IG FB variations
                        '%sponsored%',              // sponsored ads
                        '%ads%manager%',            // ads manager
                        '%meta%',                   // meta platform
                        '%giveaway%',               // giveaways
                        '%contest%',                // contests
                        '%xiaohongshu%',            // xiaohongshu
                        '%xhs%',                    // XHS abbreviation
                        '%content%calendar%',       // content calendar
                        '%posting%',                // posting related
                        '%management%',             // any management
                        '%mgmt%',                   // mgmt abbreviation
                        '%boost%',                  // boost services
                        '%report%',                 // reporting
                        '%value%add%',              // value add services
                    ];

                    foreach ($patterns as $pattern) {
                        $qq->orWhereRaw('LOWER(product) LIKE ?', [$pattern]);
                    }
                })

                // Option 3: OR include if any existing tracking data exists for this master_file_id
                ->orWhereExists(function ($query) {
                    $query->select(DB::raw(1))
                          ->from('content_calendars')
                          ->whereColumn('content_calendars.master_file_id', 'master_files.id');
                })
                ->orWhereExists(function ($query) {
                    $query->select(DB::raw(1))
                          ->from('artwork_editings')
                          ->whereColumn('artwork_editings.master_file_id', 'master_files.id');
                })
                ->orWhereExists(function ($query) {
                    $query->select(DB::raw(1))
                          ->from('posting_schedulings')
                          ->whereColumn('posting_schedulings.master_file_id', 'master_files.id');
                })
                ->orWhereExists(function ($query) {
                    $query->select(DB::raw(1))
                          ->from('media_reports')
                          ->whereColumn('media_reports.master_file_id', 'master_files.id');
                })
                ->orWhereExists(function ($query) {
                    $query->select(DB::raw(1))
                          ->from('media_value_adds')
                          ->whereColumn('media_value_adds.master_file_id', 'master_files.id');
                });
            })
            ->orderByRaw('COALESCE(company, client) ASC')
            ->distinct()
            ->get();

        // Debug: Log how many masters we found
        Log::info("MediaCoordinator: Found {$masters->count()} master files for year {$year}" . ($month ? ", month {$month}" : " (all months)"));

        if ($masters->isEmpty()) {
            // Debug: Let's see what's actually in the master_files table
            $sampleProducts = MasterFile::select(['product', 'product_category'])
                ->whereNotNull('product')
                ->distinct()
                ->limit(20)
                ->get();

            Log::info("MediaCoordinator: Sample products in database:", $sampleProducts->toArray());
        }

        $ids = $masters->pluck('id')->all();

        // Helper function remains the same
        $latestPerMaster = function (string $table) use ($ids, $year, $month) {
            if (empty($ids)) return collect();

            $q = DB::table($table)
                ->whereIn('master_file_id', $ids)
                ->where('year', $year);

            if ($month) {
                $q->where('month', $month);
                return $q->get()->keyBy('master_file_id');
            }

            $rows = $q->orderByDesc('updated_at')->get();
            $picked = [];
            foreach ($rows as $r) {
                if (!isset($picked[$r->master_file_id])) {
                    $picked[$r->master_file_id] = $r;
                }
            }
            return collect($picked);
        };

        // Build 5 lookup maps
        $contentMap  = $latestPerMaster('content_calendars');
        $editingMap  = $latestPerMaster('artwork_editings');
        $scheduleMap = $latestPerMaster('posting_schedulings');
        $reportMap   = $latestPerMaster('media_reports');
        $valueMap    = $latestPerMaster('media_value_adds');

        return view('coordinators.media', [
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

    // upsert method remains the same
    public function upsert(Request $request)
    {
        $data = $request->validate([
            'section'        => 'required|in:content,editing,schedule,report,valueadd',
            'master_file_id' => 'required|integer|exists:master_files,id',
            'year'           => 'required|integer|min:2000|max:2100',
            'month'          => 'required|integer|min:1|max:12',
            'field'          => 'required|string',
            'value'          => 'nullable',
        ]);

        $allowed = [
            'content'  => ['total_artwork','pending','draft_wa','approved'],
            'editing'  => ['total_artwork','pending','draft_wa','approved'],
            'schedule' => ['total_artwork','crm','meta_mgr','tiktok_ig_draft'],
            'report'   => ['pending','completed'],
            'valueadd' => ['quota','completed'],
        ];

        if (!in_array($data['field'], $allowed[$data['section']] ?? [], true)) {
            return response()->json(['ok'=>false,'error'=>'Invalid field'], 422);
        }

        $modelClass = MediaCoordinatorTracking::forSection($data['section']);

        $value = $data['value'];
        if (in_array($data['field'], ['draft_wa','approved','tiktok_ig_draft','completed'], true)) {
            if ($data['section'] === 'valueadd' && $data['field'] === 'completed') {
                $value = is_numeric($value) ? (int)$value : 0;
            } else {
                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                $value = (bool)$value;
            }
        } else {
            $value = is_string($value) ? mb_substr(trim($value), 0, 255) : ($value ?? '');
        }

        $modelClass::updateOrCreate(
            [
                'master_file_id' => (int)$data['master_file_id'],
                'year'           => (int)$data['year'],
                'month'          => (int)$data['month'],
            ],
            [$data['field'] => $value]
        );

        return response()->json(['ok'=>true]);
    }
}
