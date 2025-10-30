<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterFile;
use App\Models\KltgCoordinatorList;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Schema; // 🔧 NEW: Added Schema facade

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use Carbon\Carbon;

class KltgCoordinatorController extends Controller
{

    public function store(Request $request)
    {
        // Backward-compat: route('coordinator.kltg.store') will behave like upsert
        return $this->upsert($request);
    }
    // /** Canonical subcategories stored in the SAME table */


    // UI tab -> nilai yang DISIMPAN di DB
    private function tabToStored(string $tab): string
    {
        return match (strtolower(trim($tab))) {
            'print'   => 'KLTG',
            'video'   => 'VIDEO',
            'article' => 'ARTICLE',
            'lb'      => 'LB',
            'em'      => 'EM',
            default   => 'KLTG',
        };
    }


public function inlineUpdate(Request $request)
{
    $data = $request->validate([
        'id'             => 'nullable|integer|min:1',
        'master_file_id' => 'required|integer|min:1',
        'subcategory'    => 'required|string|max:20',
        'year'           => 'required|integer',
        'month'          => 'required|integer|min:1|max:12',
        'column'         => 'required|string',
        'value'          => 'nullable',
    ]);

    // whitelist kolom yang boleh diubah
    $allowed = [
        'company_snapshot','client_bp','material_reminder_text','title_snapshot','x','edition',
        'publication','artwork_bp_client','artwork_reminder','material_record','artwork_done',
        'send_chop_sign','chop_sign_approval','park_in_file_server','collection_printer',
        'sent_to_client','approved_client','sent_to_printer','printed','delivered','remarks',
        'post_link','em_date_write','em_date_to_post','em_post_date','em_qty','blog_link',
        'video_done','pending_approval','video_approved','video_scheduled','video_posted',
        'article_done','article_approved','article_scheduled','article_posted',
    ];
    if (!in_array($data['column'], $allowed, true)) {
        return response()->json(['ok'=>false,'msg'=>'Column not allowed'], 422);
    }

    // pastikan slot ada (auto-create) lalu update kolom
    DB::table('kltg_coordinator_lists')->updateOrInsert(
        [
            'master_file_id' => $data['master_file_id'],
            'subcategory'    => $data['subcategory'],
            'year'           => $data['year'],
            'month'          => $data['month'],
        ],
        [
            $data['column']  => $data['value'],
            'updated_at'     => now(),
        ]
    );

    return response()->json(['ok'=>true]);
}




public function index(Request $request)
{
    $activeTab = $request->get('tab', 'print');   // print|video|article|lb|em
    $storedSub = $this->tabToStored(strtolower($activeTab)); // "KLTG" | "Video" | ...

    // ========= Validate (light) =========
    $request->validate([
        'month' => ['nullable', 'string'],
        'year'  => ['nullable', 'integer', 'between:2015,2100'],
        'tab'   => ['nullable', Rule::in(['print','video','article','lb','em'])],
        'company' => ['nullable', 'string', 'max:255'],
    ]);

    // ========= Normalize filters =========
    $rawMonth = $request->get('month');
    $month = null;
    if ($rawMonth !== null && $rawMonth !== '' && strcasecmp($rawMonth, 'all') !== 0) {
        if (is_numeric($rawMonth)) {
            $mi = (int)$rawMonth;
            $month = ($mi >= 1 && $mi <= 12) ? $mi : null;
        } else {
            try {
                $month = Carbon::parse('1 '.$rawMonth)->month;
            } catch (\Throwable $e) {
                $m = strtolower(trim((string)$rawMonth));
                $map = [
                    'jan'=>1,'january'=>1,'feb'=>2,'february'=>2,'mar'=>3,'march'=>3,
                    'apr'=>4,'april'=>4,'may'=>5,'jun'=>6,'june'=>6,'jul'=>7,'july'=>7,
                    'aug'=>8,'august'=>8,'sep'=>9,'september'=>9,'oct'=>10,'october'=>10,
                    'nov'=>11,'november'=>11,'dec'=>12,'december'=>12
                ];
                $month = $map[$m] ?? null;
            }
        }
    }

    $rawYear = $request->get('year');
    $year = ($rawYear !== null && $rawYear !== '' && ctype_digit((string)$rawYear)) ? (int)$rawYear : null;
    if ($year !== null && ($year < 2015 || $year > 2100)) {
        $year = null;
    }

    $company = $request->get('company');
    if ($company !== null && $company !== '') {
        $company = trim($company);
    } else {
        $company = null;
    }

    // Default ONLY if no params at all
    if (!$request->has('month') && !$request->has('year')) {
        $month = now()->month;
        $year  = now()->year;
    }

    // If month provided but year missing -> derive latest year that has this (subcategory, month)
    if ($month && !$year) {
        $derivedYear = DB::table('kltg_coordinator_lists')
            ->where('subcategory', $storedSub)
            ->where('month', $month)
            ->max('year');

        if (!$derivedYear) {
            // Try MD
            $derivedYear = DB::table('kltg_monthly_details')
                ->whereRaw('TRIM(UPPER(category)) = ?', [strtoupper($storedSub)])
                ->where(function($q) use ($month) {
                    $monthName = strtolower(Carbon::create(null, $month, 1)->format('F'));
                    $q->where('month', (int)$month)
                      ->orWhereRaw('CAST(month AS UNSIGNED) = ?', [$month])
                      ->orWhereRaw('LOWER(month) = ?', [$monthName])
                      ->orWhereMonth('value_date', $month);
                })
                ->max(DB::raw('COALESCE(NULLIF(year,0), YEAR(CURDATE()))'));
        }

        $year = $derivedYear ?: now()->year;
        Log::warning('KLTG: month provided without year; derived latest year', [
            'subcategory' => $storedSub, 'month' => $month, 'derived_year' => $year
        ]);
    }

    // Scope & UI label (no month_only anymore)
    $scope = $month && $year ? 'month_year' : ($year ? 'year_only' : 'all');
    if ($scope === 'month_year') {
        $periodLabel = Carbon::create($year, $month, 1)->format('F Y');
    } elseif ($scope === 'year_only') {
        $periodLabel = 'All Months ' . $year;
    } else {
        $periodLabel = 'All Months (All Years)';
    }

  // ========= Auto-create slot kosong dari kltg_monthly_details (untuk semua scope) =========
$candidates = DB::table('kltg_monthly_details as md')
    ->select(
        'md.master_file_id',
        DB::raw("UPPER(TRIM(md.category)) as subcategory"),
        DB::raw("
            MAX(COALESCE(NULLIF(md.year, 0), YEAR(md.value_date), YEAR(CURDATE()))) as year
        "),
        DB::raw("
            MAX(CASE
                WHEN md.month REGEXP '^[0-9]+$' THEN md.month+0
                WHEN LOWER(md.month)='january'   THEN 1
                WHEN LOWER(md.month)='february'  THEN 2
                WHEN LOWER(md.month)='march'     THEN 3
                WHEN LOWER(md.month)='april'     THEN 4
                WHEN LOWER(md.month)='may'       THEN 5
                WHEN LOWER(md.month)='june'      THEN 6
                WHEN LOWER(md.month)='july'      THEN 7
                WHEN LOWER(md.month)='august'    THEN 8
                WHEN LOWER(md.month)='september' THEN 9
                WHEN LOWER(md.month)='october'   THEN 10
                WHEN LOWER(md.month)='november'  THEN 11
                WHEN LOWER(md.month)='december'  THEN 12
                ELSE MONTH(md.value_date)
            END) as month
        ")
    )
    ->whereRaw('TRIM(UPPER(md.category)) = ?', [strtoupper($storedSub)])
    ->where(function($q) {
        $q->whereNotNull('md.value_date')
          ->orWhere(function($qq) {
              $qq->whereNotNull('md.month')
                 ->whereRaw("TRIM(md.month) <> ''")
                 ->whereRaw("TRIM(md.month) <> '0'")
                 ->whereRaw("TRIM(md.month) <> '00'");
          });
    });

// Apply scope filter
if ($scope === 'month_year') {
    $candidates->where(function($q) use ($year, $month) {
        $q->where('md.year', (int)$year)->orWhereYear('md.value_date', (int)$year);
    })
    ->where(function($q) use ($month) {
        $monthName = strtolower(Carbon::create(null, $month, 1)->format('F'));
        $q->where('md.month', (int)$month)
          ->orWhereRaw('CAST(md.month AS UNSIGNED) = ?', [(int)$month])
          ->orWhereRaw('LOWER(md.month) = ?', [$monthName])
          ->orWhereMonth('md.value_date', (int)$month);
    });
} elseif ($scope === 'year_only') {
    $candidates->where(function($q) use ($year) {
        $q->where('md.year', (int)$year)->orWhereYear('md.value_date', (int)$year);
    });
}

$candidates = $candidates->groupBy('md.master_file_id', 'md.category')->get();

foreach ($candidates as $c) {
    // Skip jika month/year tidak valid
    if (empty($c->month) || $c->month < 1 || $c->month > 12) continue;
    if (empty($c->year) || $c->year < 1900) continue;

    \App\Models\KltgCoordinatorList::updateOrInsert(
        [
            'master_file_id' => $c->master_file_id,
            'subcategory'    => $c->subcategory,
            'year'           => (int)$c->year,
            'month'          => (int)$c->month,
        ],
        ['updated_at' => now()]
    );
}

    $columns = [
    'print' => [
        ['key'=>'title','label'=>'Title','type'=>'text'],
        ['key'=>'client_bp','label'=>'Client/BP','type'=>'text'],
        ['key'=>'x','label'=>'X (text)','type'=>'text'],
        ['key'=>'edition','label'=>'Edition','type'=>'text'],
        ['key'=>'publication','label'=>'Publication','type'=>'text'],
        ['key'=>'artwork_party','label'=>'Artwork (BP/Client)','type'=>'text'],
        ['key'=>'artwork_reminder','label'=>'Material Reminder','type'=>'date'],
        ['key'=>'material_record','label'=>'Material Received','type'=>'date'],
        ['key'=>'artwork_done','label'=>'Artwork Done','type'=>'date'],
        ['key'=>'send_chop_sign','label'=>'Send Chop & Sign','type'=>'date'],
        ['key'=>'chop_sign_approval','label'=>'Chop & Sign Approval','type'=>'date'],
        ['key'=>'park_in_file_server','label'=>'Park in file server','type'=>'date'],
        ['key'=>'remarks','label'=>'Remarks','type'=>'text'],
    ],

    'video' => [
        ['key'=>'title','label'=>'Title','type'=>'text'],
        ['key'=>'client_bp','label'=>'Client/BP','type'=>'text'],
        ['key'=>'x','label'=>'X','type'=>'text'],
        ['key'=>'artwork_reminder','label'=>'Material Reminder','type'=>'date'],
        ['key'=>'material_record','label'=>'Material Received','type'=>'date'],
        ['key'=>'video_done','label'=>'Video Done','type'=>'date'],
        ['key'=>'pending_approval','label'=>'Pending Approval','type'=>'date'],
        ['key'=>'video_scheduled','label'=>'Video Scheduled','type'=>'date'],
        ['key'=>'video_posted','label'=>'Video Posted','type'=>'date'],
        ['key'=>'post_link','label'=>'Post Link','type'=>'text'],
        ['key'=>'remarks','label'=>'Remarks','type'=>'text'],
    ],

    'article' => [
        ['key'=>'title','label'=>'Title','type'=>'text'],
        ['key'=>'client_bp','label'=>'Client/BP','type'=>'text'],
        ['key'=>'x','label'=>'X','type'=>'text'],
        ['key'=>'artwork_reminder','label'=>'Material Reminder','type'=>'date'],
        ['key'=>'material_record','label'=>'Material Received','type'=>'date'],
        ['key'=>'article_done','label'=>'Article Done','type'=>'date'],
        ['key'=>'pending_approval','label'=>'Pending Approval','type'=>'date'],
        ['key'=>'article_approved','label'=>'Article Approved','type'=>'date'],
        ['key'=>'article_scheduled','label'=>'Article Scheduled','type'=>'date'],
        ['key'=>'article_posted','label'=>'Article Posted','type'=>'date'],
        ['key'=>'blog_link','label'=>'Blog Link','type'=>'text'],
        ['key'=>'remarks','label'=>'Remarks','type'=>'text'],
    ],

    'lb' => [
        ['key'=>'title','label'=>'Title','type'=>'text'],
        ['key'=>'client_bp','label'=>'Client/BP','type'=>'text'],
        ['key'=>'x','label'=>'X','type'=>'text'],
        ['key'=>'artwork_reminder','label'=>'Material Reminder (Date)','type'=>'date'],
        ['key'=>'material_record','label'=>'Material Received','type'=>'date'],
        ['key'=>'video_done','label'=>'Production Done','type'=>'date'],
        ['key'=>'pending_approval','label'=>'Pending Approval','type'=>'date'],
        ['key'=>'video_approved','label'=>'Approved','type'=>'date'],
        ['key'=>'video_scheduled','label'=>'Scheduled','type'=>'date'],
        ['key'=>'video_posted','label'=>'Installed/Posted','type'=>'date'],
        ['key'=>'park_in_file_server','label'=>'Park in File Server','type'=>'text'],
        ['key'=>'post_link','label'=>'Proof Link','type'=>'text'],
        ['key'=>'remarks','label'=>'Remarks','type'=>'text'],
    ],

    'em' => [
        ['key'=>'title','label'=>'Title','type'=>'text'],
        ['key'=>'client_bp','label'=>'Client/BP','type'=>'text'],
        ['key'=>'em_date_write','label'=>'Date Write','type'=>'date'],
        ['key'=>'em_date_to_post','label'=>'Date to Post','type'=>'date'],
        ['key'=>'em_post_date','label'=>'Post Date','type'=>'date'],
        ['key'=>'em_qty','label'=>'Qty','type'=>'text'],
        ['key'=>'blog_link','label'=>'Link','type'=>'text'],
        ['key'=>'remarks','label'=>'Remarks','type'=>'text'],
    ],
];



    $monthlyTable = 'kltg_monthly_details';

    // ========= A) KOORDINATOR (KCL) — anti-duplicate via MAX(id) per key =========
    $latestIds = DB::table('kltg_coordinator_lists')
        ->selectRaw('MAX(id) AS id')
        ->where('subcategory', $storedSub)
        ->when($scope === 'month_year', fn($q)=>$q->where('year', (int)$year)->where('month', (int)$month))
        ->when($scope === 'year_only',  fn($q)=>$q->where('year', (int)$year))
        ->groupBy('master_file_id','subcategory','year','month');

    $kclBase = DB::table('kltg_coordinator_lists as kcl')
        ->joinSub($latestIds, 'L', 'L.id', '=', 'kcl.id')
        ->join('master_files as mf', 'mf.id', '=', 'kcl.master_file_id')
        ->select([
            'mf.id as id',
            'mf.id as master_file_id',
            'mf.date',
            DB::raw('COALESCE(NULLIF(mf.company,""), "") as company_name'),
            DB::raw('COALESCE(NULLIF(mf.client,""), "") as client'),
            DB::raw('COALESCE(NULLIF(mf.product,""), "") as mf_title'),
            'mf.created_at',
            DB::raw("STR_TO_DATE(CONCAT(kcl.year,'-',LPAD(kcl.month,2,'0'),'-01'), '%Y-%m-%d') as activity_date"),
            DB::raw("kcl.year  as activity_year"),
            DB::raw("kcl.month as activity_month"),
        ])
        ->when($company, function($q) use ($company) {
            $q->where('mf.company', 'LIKE', '%' . $company . '%');
        })
        ->orderBy('kcl.year', 'desc')
        ->orderBy('kcl.month', 'desc')
        ->orderBy('kcl.master_file_id')
        ->orderBy('kcl.id', 'desc');

    // ========= B) MONTHLY DETAILS (MD) — unchanged logic, still yields 1 row per (mf, y, m) =========
   // ========= B) MONTHLY DETAILS (MD) — dengan anti-join untuk exclude slot yang sudah ada di KCL =========
$mdBase = DB::table('kltg_monthly_details as md')
    ->join('master_files as mf', 'mf.id', '=', 'md.master_file_id')
    ->select([
        'mf.id as id',
        'mf.id as master_file_id',
        // ... rest of selects (sama seperti sebelumnya)
        'mf.date',
        DB::raw("COALESCE(NULLIF(mf.company,''), '') as company_name"),
        DB::raw("COALESCE(NULLIF(mf.client,''),  '') as client"),
        DB::raw("COALESCE(NULLIF(mf.product,''), '') as mf_title"),
        'mf.created_at',
        DB::raw("
            COALESCE(
                md.value_date,
                STR_TO_DATE(
                    CONCAT(
                        COALESCE(NULLIF(md.year,0), YEAR(CURDATE())),
                        '-',
                        LPAD(
                            CASE
                                WHEN md.month REGEXP '^[0-9]+$' THEN md.month+0
                                WHEN LOWER(md.month)='january'   THEN 1
                                WHEN LOWER(md.month)='february'  THEN 2
                                WHEN LOWER(md.month)='march'     THEN 3
                                WHEN LOWER(md.month)='april'     THEN 4
                                WHEN LOWER(md.month)='may'       THEN 5
                                WHEN LOWER(md.month)='june'      THEN 6
                                WHEN LOWER(md.month)='july'      THEN 7
                                WHEN LOWER(md.month)='august'    THEN 8
                                WHEN LOWER(md.month)='september' THEN 9
                                WHEN LOWER(md.month)='october'   THEN 10
                                WHEN LOWER(md.month)='november'  THEN 11
                                WHEN LOWER(md.month)='december'  THEN 12
                                ELSE NULL
                            END, 2,'0'
                        ),
                        '-01'
                    ),
                    '%Y-%m-%d'
                )
            ) AS activity_date
        "),
        DB::raw("YEAR(COALESCE(md.value_date, STR_TO_DATE(CONCAT(COALESCE(NULLIF(md.year,0), YEAR(CURDATE())), '-', LPAD(CASE WHEN md.month REGEXP '^[0-9]+$' THEN md.month+0 WHEN LOWER(md.month)='january' THEN 1 WHEN LOWER(md.month)='february' THEN 2 WHEN LOWER(md.month)='march' THEN 3 WHEN LOWER(md.month)='april' THEN 4 WHEN LOWER(md.month)='may' THEN 5 WHEN LOWER(md.month)='june' THEN 6 WHEN LOWER(md.month)='july' THEN 7 WHEN LOWER(md.month)='august' THEN 8 WHEN LOWER(md.month)='september' THEN 9 WHEN LOWER(md.month)='october' THEN 10 WHEN LOWER(md.month)='november' THEN 11 WHEN LOWER(md.month)='december' THEN 12 ELSE NULL END ,2,'0'), '-01'), '%Y-%m-%d'))) AS activity_year"),
        DB::raw("MONTH(COALESCE(md.value_date, STR_TO_DATE(CONCAT(COALESCE(NULLIF(md.year,0), YEAR(CURDATE())), '-', LPAD(CASE WHEN md.month REGEXP '^[0-9]+$' THEN md.month+0 WHEN LOWER(md.month)='january' THEN 1 WHEN LOWER(md.month)='february' THEN 2 WHEN LOWER(md.month)='march' THEN 3 WHEN LOWER(md.month)='april' THEN 4 WHEN LOWER(md.month)='may' THEN 5 WHEN LOWER(md.month)='june' THEN 6 WHEN LOWER(md.month)='july' THEN 7 WHEN LOWER(md.month)='august' THEN 8 WHEN LOWER(md.month)='september' THEN 9 WHEN LOWER(md.month)='october' THEN 10 WHEN LOWER(md.month)='november' THEN 11 WHEN LOWER(md.month)='december' THEN 12 ELSE NULL END ,2,'0'), '-01'), '%Y-%m-%d'))) AS activity_month")
    ])
    ->whereRaw('TRIM(UPPER(md.category)) = ?', [strtoupper($storedSub)])
    ->where(function($q){
        $q->whereNotNull('md.value_date')
          ->orWhere(function($qq){
              $qq->whereNotNull('md.month')
                 ->whereRaw("TRIM(md.month) <> ''")
                 ->whereRaw("TRIM(md.month) <> '0'")
                 ->whereRaw("TRIM(md.month) <> '00'");
          });
    })
     ->when($company, function($q) use ($company) {
            $q->where('mf.company', 'LIKE', '%' . $company . '%');
        })
    // ANTI-JOIN: Exclude MD jika slot KCL sudah ada
    ->whereNotExists(function($subq) use ($storedSub, $scope, $year, $month) {
        $subq->select(DB::raw(1))
             ->from('kltg_coordinator_lists as kcl_check')
             ->whereColumn('kcl_check.master_file_id', 'md.master_file_id')
             ->where('kcl_check.subcategory', $storedSub);

        // Apply same scope filter untuk anti-join
        if ($scope === 'month_year') {
            $subq->where('kcl_check.year', (int)$year)
                 ->where('kcl_check.month', (int)$month);
        } elseif ($scope === 'year_only') {
            $subq->where('kcl_check.year', (int)$year);
        }

        // Match year dari MD
        $subq->where(function($yq) {
            $yq->whereColumn('kcl_check.year', DB::raw('COALESCE(NULLIF(md.year,0), YEAR(CURDATE()))'))
               ->orWhereColumn('kcl_check.year', DB::raw('YEAR(md.value_date)'));
        });

        // Match month dari MD
        $subq->where(function($mq) {
            $mq->whereColumn('kcl_check.month', DB::raw('
                CASE
                    WHEN md.month REGEXP \'^[0-9]+$\' THEN md.month+0
                    WHEN LOWER(md.month)=\'january\'   THEN 1
                    WHEN LOWER(md.month)=\'february\'  THEN 2
                    WHEN LOWER(md.month)=\'march\'     THEN 3
                    WHEN LOWER(md.month)=\'april\'     THEN 4
                    WHEN LOWER(md.month)=\'may\'       THEN 5
                    WHEN LOWER(md.month)=\'june\'      THEN 6
                    WHEN LOWER(md.month)=\'july\'      THEN 7
                    WHEN LOWER(md.month)=\'august\'    THEN 8
                    WHEN LOWER(md.month)=\'september\' THEN 9
                    WHEN LOWER(md.month)=\'october\'   THEN 10
                    WHEN LOWER(md.month)=\'november\'  THEN 11
                    WHEN LOWER(md.month)=\'december\'  THEN 12
                    ELSE MONTH(md.value_date)
                END
            '))
            ->orWhereColumn('kcl_check.month', DB::raw('MONTH(md.value_date)'));
        });
    });
    if ($scope === 'month_year') {
        $monthName = strtolower(Carbon::create(null, $month, 1)->format('F'));
        $mdBase->where(function($w) use ($month, $year, $monthName) {
            $w->where(function($yq) use ($year) {
                  $yq->where('md.year', (int)$year)
                     ->orWhereYear('md.value_date', (int)$year);
              })
              ->where(function($mq) use ($month, $monthName) {
                  $mq->where('md.month', (int)$month)
                     ->orWhereRaw('CAST(md.month AS UNSIGNED) = ?', [(int)$month])
                     ->orWhereRaw('LOWER(md.month) = ?', [$monthName])
                     ->orWhereMonth('md.value_date', (int)$month);
              });
        });
    } elseif ($scope === 'year_only') {
        $mdBase->where(function($yq) use ($year) {
            $yq->where('md.year', (int)$year)
               ->orWhereYear('md.value_date', (int)$year);
        });
    }

    // ---------- Execute ----------
    // ---------- Execute ----------
$kclRows = $kclBase->get();
$mdRows  = $mdBase
    ->groupBy('mf.id', 'mf.date', 'mf.company', 'mf.client', 'mf.product', 'md.year', 'md.month', 'md.value_date','mf.created_at')
    ->orderBy('activity_date', 'ASC')
    ->orderBy('mf.id', 'ASC')
    ->get();

// ---------- Merge + dedup per (master_file_id, activity_year, activity_month) ----------
// PERBAIKAN: KCL dulu (editable), baru MD (read-only)
$rows = $kclRows->concat($mdRows)
    ->filter(function($r){
        return !empty($r->activity_month) && !empty($r->activity_year);
    })
    ->unique(function($r){
        return $r->master_file_id.'|'.$r->activity_year.'|'.$r->activity_month;
    })
    ->sortBy([['activity_date', 'asc'], ['master_file_id','asc']])
    ->values();

    // ========= Existing coordinator values (strict to scope, no month_only) =========
    $existingQuery = \App\Models\KltgCoordinatorList::query()
        ->where('subcategory', $storedSub);

    if ($scope === 'month_year') {
        $existingQuery->where('year', (int)$year)->where('month', (int)$month);
    } elseif ($scope === 'year_only') {
        $existingQuery->where('year', (int)$year);
    }
    $existing = $existingQuery->get()
        ->keyBy(fn($item) => $item->master_file_id . '_' . $item->subcategory . '_' . $item->year . '_' . $item->month);

    // ========= Edition/Publication (header rows) =========
    $editionPub = DB::table('kltg_monthly_details as d')
        ->selectRaw("
            d.master_file_id,
            MAX(CASE WHEN d.type = 'EDITION'     THEN NULLIF(COALESCE(d.value_text, d.value, ''), '') END) AS edition,
            MAX(CASE WHEN d.type = 'PUBLICATION' THEN NULLIF(COALESCE(d.value_text, d.value, ''), '') END) AS publication
        ")
        ->whereRaw('TRIM(UPPER(d.category)) = ?', [strtoupper($storedSub)])
        ->where('d.field_type', 'text')
        ->where('d.status', 'ACTIVE')
        ->when($year, fn($q) => $q->where('d.year', (int)$year))
        ->where(function($q){
            $q->whereIn('d.month', [0,'0','','00'])
              ->orWhereNull('d.month');
        })
        ->groupBy('d.master_file_id')
        ->get()
        ->keyBy('master_file_id');

    $rows->transform(function ($row) use ($editionPub) {
        if (isset($editionPub[$row->master_file_id])) {
            $row->edition     = $editionPub[$row->master_file_id]->edition ?? null;
            $row->publication = $editionPub[$row->master_file_id]->publication ?? null;
        } else {
            $row->edition     = null;
            $row->publication = null;
        }
        return $row;
    });

    // ---- Debug ringkas ----
    Log::info('KLTG index union result', [
        'scope'        => $scope,
        'storedSub'    => $storedSub,
        'kcl_count'    => $kclRows->count(),
        'md_count'     => $mdRows->count(),
        'final_rows'   => $rows->count(),
        'existing_cnt' => $existing->count(),
        'month'        => $month,
        'year'         => $year,
    ]);

    // ========= Month options for view =========
    $months = collect(range(1,12))->map(fn($m) => [
        'value' => $m,
        'label' => date('F', mktime(0,0,0,$m,1))
    ]);
    $companies = DB::table('kltg_coordinator_lists as kcl')
        ->join('master_files as mf', 'mf.id', '=', 'kcl.master_file_id')
        ->where('kcl.subcategory', $storedSub)
        ->whereNotNull('mf.company')
        ->where('mf.company', '<>', '')
        ->distinct()
        ->orderBy('mf.company')
        ->pluck('mf.company');

    return view('coordinators.kltg', [
        'rows'        => $rows,
        'existing'    => $existing,
        'columns'     => $columns,
        'activeTab'   => $activeTab,
        'month'       => $month,
        'company'     => $company,
        'year'        => $year,
        'scope'       => $scope,
        'periodLabel' => $periodLabel,
        'months'      => $months,
        'companies'   => $companies,
    ]);
}



public function upsert(Request $request)
{
    // 1) Normalize tab/subcategory
    $tab = strtolower((string)$request->input('subcategory', ''));
    $storedSub = $this->tabToStored($tab); // e.g., print->KLTG, video->Video, ...

    // 2) Validate basic payload + YEAR/MONTH (WAJIB)
    $validated = $request->validate([
        'master_file_id' => ['required','integer', Rule::exists('master_files','id')],
        'subcategory'    => ['required', Rule::in(['print','video','article','lb','em'])],
        'field'          => ['required','string'],
        'value'          => ['nullable'],
        'force_clear'    => ['sometimes','boolean'],
        'year'           => ['required'],                 // bisa datang "2,025"
        'month'          => ['required','integer','min:1','max:12'],
    ]);

    // --- normalize year (hapus koma/dll -> int) ---
    $year  = (int) preg_replace('/[^0-9]/', '', (string)$validated['year']);
    $month = (int) $validated['month'];
    if ($year < 1900) {
        return response()->json(['ok'=>false,'error'=>'Invalid year'], 422);
    }

    $sub   = $validated['subcategory'];   // print|video|article|lb|em
    $field = $validated['field'];
    $value = $validated['value'];

    // 3) Normalize *_date keys -> canonical DB columns
    $dateKeyMap = [
        'artwork_reminder_date'   => 'artwork_reminder',
        'material_received_date'  => 'material_record',
        'artwork_done_date'       => 'artwork_done',
        'send_chop_sign_date'     => 'send_chop_sign',
        'chop_sign_approval_date' => 'chop_sign_approval',
        'park_in_server_date'     => 'park_in_file_server',
        'video_done_date'         => 'video_done',
        'pending_approval_date'   => 'pending_approval',
        'video_approved_date'     => 'video_approved',
        'video_scheduled_date'    => 'video_scheduled',
        'video_posted_date'       => 'video_posted',
        'article_done_date'       => 'article_done',
        'article_approved_date'   => 'article_approved',
        'article_scheduled_date'  => 'article_scheduled',
        'article_posted_date'     => 'article_posted',
    ];
    if (isset($dateKeyMap[$field])) $field = $dateKeyMap[$field];

    // 4) UI -> DB aliases
    $aliases = [
        'title'        => 'title_snapshot',
        'company'      => 'company_snapshot',
        'client_bp'    => 'client_bp',
        'x'            => 'x',
        'edition'      => 'edition',
        'publication'  => 'publication',
        'remarks'      => 'remarks',
        'artwork_party'      => 'artwork_bp_client',
        'material_received'  => 'material_record',
        'park_in_server'     => 'park_in_file_server',
        'material_reminder_text' => 'material_reminder_text',
        'post_link'              => 'post_link',
        'video_done'             => 'video_done',
        'pending_approval'       => 'pending_approval',
        'video_approved'         => 'video_approved',
        'video_scheduled'        => 'video_scheduled',
        'video_posted'           => 'video_posted',
        'article_done'           => 'article_done',
        'article_approved'       => 'article_approved',
        'article_scheduled'      => 'article_scheduled',
        'article_posted'         => 'article_posted',
        'em_date_write'          => 'em_date_write',
        'em_date_to_post'        => 'em_date_to_post',
        'em_post_date'           => 'em_post_date',
        'em_qty'                 => 'em_qty',
        'blog_link'              => 'blog_link',
    ];
    $column = $aliases[$field] ?? $field;

    // 5) Allowed DB columns per tab
$allow = [
 'print' => [
   'title_snapshot','company_snapshot','client_bp','x','edition','publication',
   'artwork_bp_client','artwork_reminder','material_record','send_chop_sign',
   'chop_sign_approval','park_in_file_server','remarks','artwork_done',

   // ✅ ADD STACKED FIELDS
   'artwork_reminder_status','artwork_reminder_color',
   'material_record_status','material_record_color',
   'artwork_done_status','artwork_done_color',
   'send_chop_sign_status','send_chop_sign_color',
   'chop_sign_approval_status','chop_sign_approval_color',
   'park_in_file_server_status','park_in_file_server_color',
 ],

'video' => [
   'title_snapshot','company_snapshot','client_bp','x','remarks','material_reminder_text',
   'artwork_reminder','material_record','video_done','pending_approval','video_approved',
   'video_scheduled','video_posted','post_link',

   // ✅ COMPLETE STACKED FIELDS
   'artwork_reminder_status','artwork_reminder_color',
   'material_reminder_text_status','material_reminder_text_color',
   'material_record_status','material_record_color',
   'video_done_status','video_done_color',
   'pending_approval_status','pending_approval_color',
   'video_approved_status','video_approved_color',
   'video_scheduled_status','video_scheduled_color',
   'video_posted_status','video_posted_color',  // ← INI YANG KURANG!
   'post_link_status','post_link_color',
 ],

'article' => [
   'title_snapshot','company_snapshot','client_bp','x','remarks','material_reminder_text',
   'artwork_reminder','material_record','article_done','pending_approval',
   'article_approved','article_scheduled','article_posted','post_link','blog_link',

   // ✅ COMPLETE STACKED FIELDS
   'artwork_reminder_status','artwork_reminder_color',
   'material_reminder_text_status','material_reminder_text_color',
   'material_record_status','material_record_color',
   'article_done_status','article_done_color',
   'pending_approval_status','pending_approval_color',
   'article_approved_status','article_approved_color',
   'article_scheduled_status','article_scheduled_color',
   'article_posted_status','article_posted_color',
   'post_link_status','post_link_color',
   'blog_link_status','blog_link_color',
 ],


'lb' => [
   'title_snapshot','company_snapshot','client_bp','x','remarks','material_reminder_text',
   'artwork_reminder','material_record','video_done','pending_approval','video_approved',
   'video_scheduled','video_posted','park_in_file_server','post_link',

   // ✅ COMPLETE STACKED FIELDS
   'artwork_reminder_status','artwork_reminder_color',
   'material_reminder_text_status','material_reminder_text_color',
   'material_record_status','material_record_color',
   'video_done_status','video_done_color',
   'pending_approval_status','pending_approval_color',
   'video_approved_status','video_approved_color',
   'video_scheduled_status','video_scheduled_color',
   'video_posted_status','video_posted_color',
   'park_in_file_server_status','park_in_file_server_color',
   'post_link_status','post_link_color',
 ],
'em' => [
   '__clients_from_mf','title_snapshot','company_snapshot','client_bp','remarks',
   'em_date_write','em_date_to_post','em_post_date','em_qty','blog_link',

   // ✅ ADD STACKED FIELDS
   'em_date_write_status','em_date_write_color',
   'em_date_to_post_status','em_date_to_post_color',
   'em_post_date_status','em_post_date_color',
 ],
];

    if (!in_array($column, $allow[$sub] ?? [], true)) {
        return response()->json([
            'ok' => false,
            'error' => "The selected field key '{$column}' is invalid for subcategory '{$sub}'",
            'allowed' => $allow[$sub] ?? [],
        ], 422);
    }

    $dateColumns = [
        'artwork_reminder','material_record','send_chop_sign','chop_sign_approval','artwork_done',
        'video_done','pending_approval','video_approved','video_scheduled','video_posted',
        'article_done','article_approved','article_scheduled','article_posted',
        'em_date_write','em_date_to_post','em_post_date',
    ];
    if (in_array($column, $dateColumns, true)) {
        if ($value === '' || $value === null) {
            if (!($validated['force_clear'] ?? false)) {
                return response()->json(['ok'=>true,'skipped'=>true,'reason'=>'empty_date_ignored'], 200);
            }
            $value = null;
        } else {
            try {
                $value = Carbon::parse((string)$value)->format('Y-m-d');
            } catch (\Throwable $e) {
                return response()->json(['ok'=>false,'error'=>'Invalid date'], 422);
            }
        }
    } else {
        if ($column === 'em_qty') {
            $value = ($value === '' || $value === null) ? null : (int)$value;
        } else {
            if ($value === '' || $value === null) {
                if (!($validated['force_clear'] ?? false)) {
                    return response()->json(['ok'=>true,'skipped'=>true,'reason'=>'empty_text_ignored'], 200);
                }
                $value = null;
            } else {
                $value = (string)$value;
            }
        }
    }

    // 7) Keys untuk upsert — **PER-BULAN**
    $keys = [
        'master_file_id' => (int)$validated['master_file_id'],
        'subcategory'    => $storedSub, // e.g. 'KLTG'
        'year'           => $year,
        'month'          => $month,
    ];

    // 8) Defensive: cek kolom
    $cols = Schema::getColumnListing('kltg_coordinator_lists');
    if (!in_array($column, $cols, true)) {
        return response()->json(['ok'=>false,'error'=>"Unknown column '{$column}' in table"], 422);
    }

    // 9) Upsert per slot bulan — tanpa menimpa kolom lain
    //    NB: hindari firstOrCreate hanya dengan (mf, subcategory) karena bikin overlap
    DB::table('kltg_coordinator_lists')->updateOrInsert(
        $keys,
        [$column => $value, 'updated_at' => now(), 'created_at' => now()]
    );

    // Return row terbaru (opsional)
    $row = DB::table('kltg_coordinator_lists')->where($keys)->first();

    return response()->json([
        'ok'     => true,
        'where'  => $keys,
        'column' => $column,
        'value'  => $value,
        'row'    => $row,
    ]);
}


/** Masters that DON'T yet have a coordinator row for the selected subcategory */
public function getEligibleMasterFiles(Request $request)
{
    $activeSubcat = $this->normalizeSubcat($request->get('subcategory'));

        $eligible = MasterFile::query()
            ->where(function($q) {
                $q->where('product_category', 'KLTG')
                  ->orWhere('product_category', 'like', '%KLTG%');
            })
            ->whereNotExists(function ($q) use ($activeSubcat) {
                $q->select(DB::raw(1))
                  ->from('kltg_coordinator_lists as k')
                  ->whereColumn('k.master_file_id', 'master_files.id')
                  ->where('k.subcategory', $activeSubcat);
            })
            ->orderBy('created_at','desc')
            ->limit(100)
            ->get(['id','company','product','month','created_at']);

        return response()->json(['eligible' => $eligible, 'subcategory' => $activeSubcat]);
    }
private function storedToTab(string $stored): string
{
    $map = [
        'KLTG' => 'print',
        'VIDEO' => 'video',
        'ARTICLE' => 'article',
        'LB' => 'lb',
        'EM' => 'em'
    ];

    return $map[$stored] ?? 'print';
}

private function kclBaseQuery(string $subcategory, ?int $year, ?int $month)
{
    // Subquery: ambil id terbaru per (mf, subcat, year, month)
    $latestIds = DB::table('kltg_coordinator_lists')
        ->selectRaw('MAX(id) AS id')
        ->where('subcategory', $subcategory)
        ->when($year, fn($q)=>$q->where('year', $year))
        ->when($month, fn($q)=>$q->where('month', $month))
        ->groupBy('master_file_id','subcategory','year','month');

    // Join balik ke tabel utama → 1 baris pasti unik per (mf,year,month)
    return DB::table('kltg_coordinator_lists as kcl')
        ->joinSub($latestIds, 'L', 'L.id', '=', 'kcl.id')
        // kalau perlu info perusahaan:
        // ->leftJoin('master_files as mf','mf.id','=','kcl.master_file_id')
        ->orderBy('kcl.year', 'desc')
        ->orderBy('kcl.month', 'desc')
        ->orderBy('kcl.master_file_id')
        ->orderBy('kcl.id', 'desc');
}



public function export(Request $request)
{
    // ---- Log request for debugging
    Log::info('EXPORT - Request params:', $request->all());

    // ---- Subcategory normalization (e.g., 'KLTG', 'Video', 'Article', 'LB', 'EM')
    $activeSubcat = $this->normalizeSubcat($request->get('subcategory'));
    $key          = $this->storedToTab($activeSubcat); // e.g., 'print' | 'video' | ...

    // ---- Month/Year normalization + scope
    [$month, $year, $scope] = $this->normalizeMonthYearScope($request);

    Log::info('EXPORT - Normalized:', [
        'activeSubcat' => $activeSubcat,
        'tabKey'       => $key,
        'month'        => $month,
        'year'         => $year,
        'scope'        => $scope,
    ]);

    // ---- Build export query from KCL (coordinator list) filtered by scope
    $query = $this->buildExportQuery($activeSubcat, $scope, $month, $year, $request->get('working'));

    // ---- Log SQL for visibility
    Log::info('EXPORT - Final query result:', [
        'count'    => $query->count(),
        'sql'      => $query->toSql(),
        'bindings' => $query->getBindings(),
    ]);

    // ---- Filename (.xls for HTML-Excel)
  $filename = sprintf(
    'kltg_%s_%s.xls',
    strtolower($activeSubcat),
    now()->format('Ymd_His')
);

$headers = [
    'Content-Type'        => 'application/vnd.ms-excel',
    'Content-Disposition' => 'attachment; filename="'.$filename.'"',
    'Pragma'              => 'no-cache',
    'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
    'Expires'             => '0',
];

    // ---- Field columns + labels
    $columnsBySubcat = $this->getColumnsBySubcat();
    $fields = $columnsBySubcat[$key] ?? $columnsBySubcat['print'];

    $labels = $this->getFieldLabels();

    // ---- Titles
    $mainTitle  = 'KLTG - COORDINATOR LIST - ' . $this->subcatTitle($activeSubcat);
    $monthLabel = $this->monthFilterLabel($request); // gunakan helpermu yang sudah ada

    return response()->stream(
        function () use ($query, $fields, $labels, $mainTitle, $monthLabel, $scope, $request) {
            $this->streamXlsHtmlOutput($query, $fields, $labels, $mainTitle, $monthLabel, $scope, $request->get('working'));
        },
        200,
        $headers
    );
}


private function normalizeSubcat(?string $subcat): string
{
    $map = [
        'print' => 'KLTG',
        'video' => 'VIDEO',
        'article' => 'ARTICLE',
        'lb' => 'LB',
        'em' => 'EM'
    ];

    $normalized = $map[strtolower($subcat ?? 'print')] ?? 'KLTG';
    return $normalized;
}



protected function normalizeMonthYearScope(Request $request): array
{
    $rawMonth = $request->get('month');
    $rawYear  = $request->get('year');

    $month = null;
    $year  = null;

    // --- Parse month (and possibly year if month came as YYYY-MM or YYYY-MM-DD)
    if ($rawMonth !== null && $rawMonth !== '') {
        $mstr = strtolower(trim((string) $rawMonth));

        // Pattern: YYYY-MM or YYYY-MM-DD
        if (preg_match('/^(\d{4})-(\d{2})(?:-\d{2})?$/', $mstr, $mm)) {
            $mi = (int) $mm[2];
            if ($mi >= 1 && $mi <= 12) {
                $month = $mi;
            }
            $yi = (int) $mm[1];
            if ($year === null && $yi >= 2015 && $yi <= 2100) {
                $year = $yi;
            }
        }
        // Pure digits (e.g., "9", "09")
        elseif (ctype_digit($mstr)) {
            $mi = (int) $mstr;
            $month = ($mi >= 1 && $mi <= 12) ? $mi : null;
        }
        // Month names
        else {
            $map = [
                'jan'=>1,'january'=>1,'feb'=>2,'february'=>2,'mar'=>3,'march'=>3,
                'apr'=>4,'april'=>4,'may'=>5,'jun'=>6,'june'=>6,'jul'=>7,'july'=>7,
                'aug'=>8,'august'=>8,'sep'=>9,'september'=>9,'oct'=>10,'october'=>10,
                'nov'=>11,'november'=>11,'dec'=>12,'december'=>12
            ];
            $month = $map[$mstr] ?? null;
        }
    }

    // --- Parse explicit year param if present
    if ($rawYear !== null && $rawYear !== '') {
        $ystr = trim((string) $rawYear);
        if (ctype_digit($ystr)) {
            $yi = (int) $ystr;
            if ($yi >= 2015 && $yi <= 2100) {
                $year = $yi;
            }
        }
    }

    // --- If month provided but year missing, derive a sensible year
    if ($month && !$year) {
        $activeTab   = strtolower((string) $request->get('tab', 'print'));
        $subcategory = method_exists($this, 'tabToStored')
            ? $this->tabToStored($activeTab)
            : $activeTab;

        $year = DB::table('kltg_coordinator_lists')
            ->where('subcategory', $subcategory)
            ->where('month', $month)
            ->max('year');

        if (!$year) {
            $year = now()->year; // fallback
        }

        Log::info('KLTG month filter: derived missing year', [
            'month' => $month,
            'derived_year' => $year,
            'subcategory' => $subcategory,
        ]);
    }

    // --- Scope: NEVER return 'month_only'
    $scope = 'all';
    if ($month && $year) {
        $scope = 'month_year';
    } elseif (!$month && $year) {
        $scope = 'year_only';
    }

    return [$month, $year, $scope];
}



protected function buildExportQuery(string $activeSubcat, string $scope, ?int $month, ?int $year, $working = null)
{
    $q = DB::table('kltg_coordinator_lists as kcl')
        ->join('master_files as mf', 'mf.id', '=', 'kcl.master_file_id')
        ->where('kcl.subcategory', $activeSubcat)
        ->select([
            'kcl.*',                         // semua kolom hasil autosave
            'mf.company as mf_company',      // opsional untuk judul/label tambahan
            'mf.client  as mf_client',
            'mf.product as mf_product',
            'mf.created_at as mf_created_at',
        ]);

    // Terapkan scope
    if ($scope === 'month_year') {
        $q->where('kcl.year',  (int)$year)
          ->where('kcl.month', (int)$month);
    } elseif ($scope === 'month_only') {
        $q->where('kcl.month', (int)$month);
    } elseif ($scope === 'year_only') {
        $q->where('kcl.year', (int)$year);
    }

    // Filter by working status if specified
    if ($working === 'working') {
        $q->where(function ($query) {
            $query->whereNull('kcl.park_in_file_server')
                  ->orWhere('kcl.park_in_file_server', '');
        });
    } elseif ($working === 'completed') {
        $q->whereNotNull('kcl.park_in_file_server')
          ->where('kcl.park_in_file_server', '!=', '');
    }

    // Urutan rapi
    $q->orderBy('kcl.year')->orderBy('kcl.month')->orderBy('kcl.master_file_id');

    return $q;
}


private function streamXlsHtmlOutput($query, array $fields, array $labels, string $mainTitle, ?string $monthLabel, ?string $scope, ?string $working = null): void
{
    // BOM for UTF-8
    echo "\xEF\xBB\xBF";

    $rows = $query->get();
    $totalRecords = $rows->count();

    // ✅ Group fields: combine base + status into single logical column
    $groupedFields = [];
    $processedFields = [];

    foreach ($fields as $field) {
        // Skip _status and _color fields (will be combined with base field)
        if (str_ends_with($field, '_status') || str_ends_with($field, '_color')) {
            continue;
        }

        // Skip if already processed
        if (in_array($field, $processedFields)) {
            continue;
        }

        $groupedFields[] = $field;
        $processedFields[] = $field;
    }

    $colCount = count($groupedFields);

    // ====== OPEN HTML WITH EXCEL NAMESPACES ======
    echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
    echo '<head>';
    echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
    echo '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Sheet1</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
    echo '<style>
        table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; }
        th, td { border: 1px solid #000000; padding: 8px; vertical-align: top; }
        .title { background: #FFFF00; font-weight: bold; text-align: center; font-size: 14px; }
        .header { background: #FFFF00; font-weight: bold; text-align: center; font-size: 11px; white-space: nowrap; }
        tbody td { font-size: 10px; }
        .number { text-align: center; }
    </style></head><body>';

    // ====== TITLE SECTION ======
    echo '<table>';
    echo '<tr><td class="title" colspan="'.$colCount.'">'.htmlspecialchars($mainTitle, ENT_QUOTES, 'UTF-8').'</td></tr>';

    $timestamp = now()->format('Y-m-d H:i:s');
    $filterText = $monthLabel ?: 'All Data';
    $workingText = $working === 'working' ? ' - Status: Working' : ($working === 'completed' ? ' - Status: Completed' : '');

    echo '<tr><td class="title" colspan="'.$colCount.'">';
    echo htmlspecialchars("Month Filter: {$filterText} - Generated: {$timestamp} - Total Records: {$totalRecords}{$workingText}", ENT_QUOTES, 'UTF-8');
    echo '</td></tr>';

    echo '<tr><td colspan="'.$colCount.'">&nbsp;</td></tr>';

    // ====== HEADER ROW ======
    echo '<tr>';
    foreach ($groupedFields as $field) {
        $label = $labels[$field] ?? Str::headline($field);
        echo '<th class="header">'.htmlspecialchars($label, ENT_QUOTES, 'UTF-8').'</th>';
    }
    echo '</tr>';

    // ====== DATA ROWS ======
    $seq = 0;
    foreach ($rows as $row) {
        $seq++;
        echo '<tr>';

        foreach ($groupedFields as $field) {
            if ($field === '__no') {
                echo '<td class="number">'.htmlspecialchars($seq, ENT_QUOTES, 'UTF-8').'</td>';
                continue;
            }

            // Check if this field has status/color
            $statusField = $field . '_status';
            $colorField = $field . '_color';

            $statusValue = $row->{$statusField} ?? '';
            $colorValue = $row->{$colorField} ?? '';
            $dateValue = $this->valueForField($row, $field);

            // Get the raw value for non-date fields
            $rawValue = $this->valueForField($row, $field);

            echo '<td style="vertical-align: top; padding: 4px; text-align: center;">';

// ✅ If has status, render stacked (status box on top, date below)
if ($statusValue && $colorValue) {
    $bgColor = strtoupper($colorValue); // Excel likes uppercase hex
    $textColor = $this->getContrastColor($bgColor);

    // ✅ Use nested table with bgcolor attribute (Excel-compatible)
    echo '<table width="100%" border="0" cellpadding="6" cellspacing="0">';
    echo '<tr>';
    echo '<td bgcolor="'.htmlspecialchars($bgColor, ENT_QUOTES, 'UTF-8').'" style="color: '.htmlspecialchars($textColor, ENT_QUOTES, 'UTF-8').'; font-weight: bold; font-size: 9pt; text-align: center; border: 1px solid #999;">';
    echo htmlspecialchars($statusValue, ENT_QUOTES, 'UTF-8');
    echo '</td>';
    echo '</tr>';

    // Date below (if exists)
    if ($dateValue) {
        echo '<tr>';
        echo '<td style="font-size: 10pt; color: #1f2937; text-align: center; padding-top: 4px; font-weight: normal;">';
        echo htmlspecialchars($dateValue, ENT_QUOTES, 'UTF-8');
        echo '</td>';
        echo '</tr>';
    }
    echo '</table>';
}
// ✅ If no color but has status (render without colored box)
elseif ($statusValue && !$colorValue) {
    echo '<table width="100%" border="0" cellpadding="6" cellspacing="0">';
    echo '<tr>';
    echo '<td bgcolor="#F3F4F6" style="color: #374151; font-weight: bold; font-size: 9pt; text-align: center; border: 1px solid #999;">';
    echo htmlspecialchars($statusValue, ENT_QUOTES, 'UTF-8');
    echo '</td>';
    echo '</tr>';

    if ($dateValue) {
        echo '<tr>';
        echo '<td style="font-size: 10pt; color: #1f2937; text-align: center; padding-top: 4px; font-weight: normal;">';
        echo htmlspecialchars($dateValue, ENT_QUOTES, 'UTF-8');
        echo '</td>';
        echo '</tr>';
    }
    echo '</table>';
}

// ✅ If no color but has status (render without colored box)
elseif ($statusValue && !$colorValue) {
    echo '<div style="background-color: #f3f4f6; color: #374151; padding: 8px 12px; font-weight: bold; font-size: 9pt; text-align: center; border: 1px solid #d1d5db; margin-bottom: 6px; border-radius: 4px;">';
    echo htmlspecialchars($statusValue, ENT_QUOTES, 'UTF-8');
                echo '</div>';

                if ($dateValue) {
                    echo '<div style="font-size: 10pt; color: #1f2937; text-align: center; font-weight: normal;">'.htmlspecialchars($dateValue, ENT_QUOTES, 'UTF-8').'</div>';
                }
            }
            // ✅ No status, just show raw value (date or text)
            else {
                echo '<div style="font-size: 10pt; color: #1f2937;">'.htmlspecialchars($rawValue, ENT_QUOTES, 'UTF-8').'</div>';
            }

            echo '</td>';
        }

        echo '</tr>';
    }

    echo '</table></body></html>';
}

/**
 * Calculate contrast color for text (black or white) based on background
 */
private function getContrastColor(string $hexColor): string
{
    // Remove # if present
    $hex = ltrim($hexColor, '#');

    // Handle short hex codes (e.g., #fff)
    if (strlen($hex) === 3) {
        $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    }

    // Validate hex
    if (strlen($hex) !== 6 || !ctype_xdigit($hex)) {
        return '#000000'; // Default to black for invalid colors
    }

    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));

    // Calculate relative luminance (WCAG formula)
    $brightness = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;

    // Return white text for dark backgrounds, black for light backgrounds
    return $brightness > 155 ? '#000000' : '#ffffff';
}



private function valueForField($row, string $field): string
{
    switch ($field) {
        case '__no':
            return ''; // This is handled in the caller

        case '__date_created':
            return $row->mf_created_at ?
                Carbon::parse($row->mf_created_at)->format('Y-m-d') : '';

        case '__company':
            return $row->company_snapshot ?: ($row->mf_company ?: '');

        case '__pic':
            return $row->mf_client ?: '';

        case '__clients_from_mf':
            return $row->mf_client ?: '';

        default:
            // Handle regular database fields
            $value = $row->{$field} ?? '';

            // Format dates
            if (is_object($value) && method_exists($value, 'format')) {
                return $value->format('Y-m-d');
            }

            // Handle date strings
            if (is_string($value) && $value !== '' && preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) {
                try {
                    return Carbon::parse($value)->format('Y-m-d');
                } catch (\Throwable $e) {
                    return $value;
                }
            }

            return (string) $value;
    }
}


private function getColumnsBySubcat(): array
{
    return [
        'print' => [
            '__no',
            '__date_created',
            '__company',
            '__pic',
            'title_snapshot',
            'client_bp',
            'x',
            'edition',
            'publication',
            'artwork_bp_client',
            'artwork_reminder',
            'artwork_reminder_status',
            'material_record',
            'material_record_status',
            'artwork_done',
            'artwork_done_status',
            'send_chop_sign',
            'send_chop_sign_status',
            'chop_sign_approval',
            'chop_sign_approval_status',
            'park_in_file_server',
            'park_in_file_server_status',
            'remarks',
        ],
        'video' => [
            '__no',
            '__date_created',
            '__company',
            '__pic',
            'title_snapshot',
            'client_bp',
            'x',
            'artwork_reminder',
            'artwork_reminder_status',
            'material_record',
            'material_record_status',
            'video_done',
            'video_done_status',
            'pending_approval',
            'pending_approval_status',
            'video_scheduled',
            'video_scheduled_status',
            'video_posted',
            'video_posted_status',
            'post_link',
            'remarks',
        ],
        'article' => [
            '__no',
            '__date_created',
            '__company',
            '__pic',
            'title_snapshot',
            'client_bp',
            'x',
            'artwork_reminder',
            'artwork_reminder_status',
            'material_record',
            'material_record_status',
            'article_done',
            'article_done_status',
            'pending_approval',
            'pending_approval_status',
            'article_approved',
            'article_approved_status',
            'article_scheduled',
            'article_scheduled_status',
            'article_posted',
            'article_posted_status',
            'blog_link',
            'remarks',
        ],
        'lb' => [
            '__no',
            '__date_created',
            '__company',
            '__pic',
            'title_snapshot',
            'client_bp',
            'x',
            'artwork_reminder',
            'artwork_reminder_status',
            'material_record',
            'material_record_status',
            'video_done',
            'video_done_status',
            'pending_approval',
            'pending_approval_status',
            'video_approved',
            'video_approved_status',
            'video_scheduled',
            'video_scheduled_status',
            'video_posted',
            'video_posted_status',
            'park_in_file_server',
            'park_in_file_server_status',
            'post_link',
            'remarks',
        ],
        'em' => [
            '__no',
            '__date_created',
            '__company',
            '__pic',
            '__clients_from_mf',
            'title_snapshot',
            'em_date_write',
            'em_date_write_status',
            'em_date_to_post',
            'em_date_to_post_status',
            'em_post_date',
            'em_post_date_status',
            'em_qty',
            'blog_link',
            'remarks',
        ],
    ];
}


private function getFieldLabels(): array
{
    return [
        '__no' => 'No',
        '__date_created' => 'Date Created',
        '__company' => 'Company',
        '__pic' => 'Person In Charge',
        '__clients_from_mf' => 'Clients',
        'title_snapshot' => 'Title',
        'client_bp' => 'Client/BP',
        'x' => 'X',
        'edition' => 'Edition',
        'publication' => 'Publication',
        'remarks' => 'Remarks',

        // Date fields (labels without "Status" - will be auto-removed in export)
        'artwork_reminder' => 'Material Reminder',
        'artwork_reminder_status' => 'Material Reminder',
        'material_record' => 'Material Received',
        'material_record_status' => 'Material Received',
        'artwork_done' => 'Artwork Done',
        'artwork_done_status' => 'Artwork Done',
        'send_chop_sign' => 'Send Chop & Sign',
        'send_chop_sign_status' => 'Send Chop & Sign',
        'chop_sign_approval' => 'Chop & Sign Approval',
        'chop_sign_approval_status' => 'Chop & Sign Approval',
        'park_in_file_server' => 'Park in File Server',
        'park_in_file_server_status' => 'Park in File Server',

        'artwork_bp_client' => 'Artwork (BP/Client)',
        'video_done' => 'Video Done',
        'video_done_status' => 'Video Done',
        'pending_approval' => 'Pending Approval',
        'pending_approval_status' => 'Pending Approval',
        'video_approved' => 'Video Approved',
        'video_approved_status' => 'Video Approved',
        'video_scheduled' => 'Video Scheduled',
        'video_scheduled_status' => 'Video Scheduled',
        'video_posted' => 'Video Posted',
        'video_posted_status' => 'Video Posted',
        'post_link' => 'Post Link',

        'article_done' => 'Article Done',
        'article_done_status' => 'Article Done',
        'article_approved' => 'Article Approved',
        'article_approved_status' => 'Article Approved',
        'article_scheduled' => 'Article Scheduled',
        'article_scheduled_status' => 'Article Scheduled',
        'article_posted' => 'Article Posted',
        'article_posted_status' => 'Article Posted',
        'blog_link' => 'Blog Link',

        'em_date_write' => 'Date Write',
        'em_date_write_status' => 'Date Write',
        'em_date_to_post' => 'Date to Post',
        'em_date_to_post_status' => 'Date to Post',
        'em_post_date' => 'Post Date',
        'em_post_date_status' => 'Post Date',
        'em_qty' => 'Qty',
    ];
}

private function subcatTitle(string $key): string
{
    $map = [
        'KLTG' => 'PRINT ARTWORK',
        'VIDEO' => 'VIDEO',
        'ARTICLE' => 'ARTICLE',
        'LB' => 'LB',
        'EM' => 'EM',
    ];

    return $map[$key] ?? Str::upper($key);
}


private function monthFilterLabel(Request $request): ?string
{
    $rawMonth = $request->input('month');
    $rawYear = $request->input('year');

    if ($rawMonth === null && $rawYear === null) {
        return 'All Data';
    }

    // Normalize month
    $month = null;
    if ($rawMonth !== null && $rawMonth !== '') {
        $m = strtolower(trim((string)$rawMonth));
        $monthMap = [
            'jan' => 1, 'january' => 1, 'feb' => 2, 'february' => 2,
            'mar' => 3, 'march' => 3, 'apr' => 4, 'april' => 4,
            'may' => 5, 'jun' => 6, 'june' => 6, 'jul' => 7, 'july' => 7,
            'aug' => 8, 'august' => 8, 'sep' => 9, 'september' => 9,
            'oct' => 10, 'october' => 10, 'nov' => 11, 'november' => 11,
            'dec' => 12, 'december' => 12,
        ];
        $month = ctype_digit($m) ? max(1, min(12, (int)$m)) : ($monthMap[$m] ?? null);
    }

    // Normalize year
    $year = null;
    if ($rawYear !== null && $rawYear !== '') {
        $year = (int) $rawYear;
        if ($year < 1900 || $year > 9999) {
            $year = null;
        }
    }

    // Build label
    try {
        if ($month && $year) {
            return Carbon::createFromDate($year, $month, 1)->format('F Y');
        }
        if ($month && !$year) {
            return Carbon::createFromDate(now()->year, $month, 1)->format('F') . ' (All Years)';
        }
        if (!$month && $year) {
            return 'All Months ' . $year;
        }
    } catch (\Throwable $e) {
        return 'Invalid Date Filter';
    }

    return 'All Data';
}

}


