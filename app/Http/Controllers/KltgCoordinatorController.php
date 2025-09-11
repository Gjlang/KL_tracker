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
    // private const SUBCATS = ['KLTG','Video','Article','LB','EM'];

    /** Normalize any input (video/VIDEO) → Canonical ("Video") */
    private function normalizeSubcat(?string $s): string
    {
        $map = [
            'kltg' => 'KLTG', 'video' => 'Video', 'article' => 'Article',
            'lb'   => 'LB',   'em'    => 'EM',
        ];
        $k = strtolower(trim((string)($s ?? '')));
        return $map[$k] ?? 'KLTG';
    }


    // UI tab -> nilai yang DISIMPAN di DB
    private function tabToStored(string $tab): string
    {
        return match (strtolower(trim($tab))) {
            'print'   => 'KLTG',
            'video'   => 'Video',
            'article' => 'Article',
            'lb'      => 'LB',
            'em'      => 'EM',
            default   => 'KLTG',
        };
    }

    // nilai di DB -> UI tab
    private function storedToTab(string $stored): string
    {
        return match (trim($stored)) {
            'KLTG'    => 'print',
            'Video'   => 'video',
            'Article' => 'article',
            'LB'      => 'lb',
            'EM'      => 'em',
            default   => 'print',
        };
    }

    private function normalizeMonth($raw): ?int
{
    if ($raw === null || $raw === '') return null;
    $m = strtolower(trim((string)$raw));
    if (ctype_digit($m)) {
        $n = (int)$m;
        return ($n >= 1 && $n <= 12) ? $n : null;
    }
    $map = [
        'jan'=>1,'january'=>1,'feb'=>2,'february'=>2,'mar'=>3,'march'=>3,
        'apr'=>4,'april'=>4,'may'=>5,'jun'=>6,'june'=>6,'jul'=>7,'july'=>7,
        'aug'=>8,'august'=>8,'sep'=>9,'september'=>9,'oct'=>10,'october'=>10,
        'nov'=>11,'november'=>11,'dec'=>12,'december'=>12,
    ];
    return $map[$m] ?? null;
}



public function index(Request $request)
{
    $activeTab = $request->get('tab', 'print');   // print|video|article|lb|em

    // ========= Normalize filters =========
    $rawMonth = $request->get('month');
    $month = null;
    if ($rawMonth !== null && $rawMonth !== '') {
        if (is_numeric($rawMonth)) {
            $month = max(1, min(12, (int)$rawMonth));
        } else {
            try { $month = Carbon::parse('1 '.$rawMonth)->month; } catch (\Throwable $e) { $month = null; }
        }
    }
    $rawYear = $request->get('year');
    $year = ($rawYear !== null && $rawYear !== '' && ctype_digit((string)$rawYear)) ? (int)$rawYear : null;

    // Default ONLY if no params at all
    if (!$request->has('month') && !$request->has('year')) {
        $month = now()->month;
        $year  = now()->year;
    } elseif ($month && !$year) {
        // month_only mode — jangan auto isi year
        $year = null;
    }

    // Scope & UI label
    $scope = $month && $year ? 'month_year' : ($month ? 'month_only' : ($year ? 'year_only' : 'all'));
    if ($scope === 'month_year') {
        $periodLabel = Carbon::create($year, $month, 1)->format('F Y');
    } elseif ($scope === 'month_only') {
        $periodLabel = Carbon::create(null, $month, 1)->format('F'). ' (All Years)';
    } elseif ($scope === 'year_only') {
        $periodLabel = 'All Months ' . $year;
    } else {
        $periodLabel = 'All Months (All Years)';
    }

    // Map UI tab -> stored category di monthly table
    $storedSub = $this->tabToStored(strtolower($activeTab)); // "KLTG" | "Video" | "Article" | "LB" | "EM"

    // ========= Column definitions (unchanged) =========
    $columns = [
        'print' => [
            ['key'=>'title','label'=>'Title','type'=>'text'],
            ['key'=>'client_bp','label'=>'Client/BP','type'=>'text'],
            ['key'=>'x','label'=>'X (text)','type'=>'text'],
            ['key'=>'edition','label'=>'Edition','type'=>'text'],        // auto-set (read-only)
            ['key'=>'publication','label'=>'Publication','type'=>'text'],// auto-set (read-only)
            ['key'=>'artwork_party','label'=>'Artwork (BP/Client)','type'=>'text'],
            ['key'=>'artwork_reminder_date','label'=>'Artwork Reminder','type'=>'date'],
            ['key'=>'material_received_date','label'=>'Material Received','type'=>'date'],
            ['key'=>'artwork_done','label'=>'Artwork Done','type'=>'date'],
            ['key'=>'send_chop_sign_date','label'=>'Send Chop & Sign','type'=>'date'],
            ['key'=>'chop_sign_approval_date','label'=>'Chop & Sign Approval','type'=>'date'],
            ['key'=>'park_in_server_date','label'=>'Park in file server','type'=>'date'],
        ],
        'video' => [
            ['key'=>'title','label'=>'Title','type'=>'text'],
            ['key'=>'client_bp','label'=>'Client/BP','type'=>'text'],
            ['key'=>'x','label'=>'X (text)','type'=>'text'],
            ['key'=>'material_reminder_text','label'=>'Material Reminder','type'=>'text'],
            ['key'=>'material_received_date','label'=>'Material Received','type'=>'date'],
            ['key'=>'video_done_date','label'=>'Video Done','type'=>'date'],
            ['key'=>'pending_approval_date','label'=>'Pending Approval','type'=>'date'],
            ['key'=>'video_scheduled_date','label'=>'Video Scheduled','type'=>'date'],
            ['key'=>'video_posted_date','label'=>'Video Posted','type'=>'date'],
            ['key'=>'post_link','label'=>'Post Link','type'=>'text'],
        ],
        'lb' => [
            ['key'=>'title','label'=>'Title','type'=>'text'],
            ['key'=>'client_bp','label'=>'Client/BP','type'=>'text'],
            ['key'=>'x','label'=>'X (text)','type'=>'text'],
            ['key'=>'material_reminder_text','label'=>'Material Reminder','type'=>'text'],
            ['key'=>'material_received_date','label'=>'Material Received','type'=>'date'],
            ['key'=>'video_done_date','label'=>'Video Done','type'=>'date'],
            ['key'=>'pending_approval_date','label'=>'Pending Approval','type'=>'date'],
            ['key'=>'video_approved_date','label'=>'Video Approved','type'=>'date'],
            ['key'=>'video_scheduled_date','label'=>'Video Scheduled','type'=>'date'],
            ['key'=>'video_posted_date','label'=>'Video Posted','type'=>'date'],
            ['key'=>'post_link','label'=>'Post Link','type'=>'text'],
        ],
        'article' => [
            ['key'=>'title','label'=>'Title','type'=>'text'],
            ['key'=>'client_bp','label'=>'Client/BP','type'=>'text'],
            ['key'=>'x','label'=>'X (text)','type'=>'text'],
            ['key'=>'material_reminder_text','label'=>'Material Reminder','type'=>'text'],
            ['key'=>'material_received_date','label'=>'Material Received','type'=>'date'],
            ['key'=>'article_done_date','label'=>'Article Done','type'=>'date'],
            ['key'=>'pending_approval_date','label'=>'Pending Approval','type'=>'date'],
            ['key'=>'article_approved_date','label'=>'Article Approved','type'=>'date'],
            ['key'=>'article_scheduled_date','label'=>'Article Scheduled','type'=>'date'],
            ['key'=>'article_posted_date','label'=>'Article Posted','type'=>'date'],
            ['key'=>'post_link','label'=>'Post Link','type'=>'text'],
        ],
        'em' => [
            ['key'=>'client_bp','label'=>'Clients (from master_files)','type'=>'text'],
            ['key'=>'em_date_write','label'=>'Date Write','type'=>'date'],
            ['key'=>'em_date_to_post','label'=>'Date to post','type'=>'date'],
            ['key'=>'em_post_date','label'=>'Post date','type'=>'date'],
            ['key'=>'em_qty','label'=>'EM-qty','type'=>'text'],
            ['key'=>'blog_link','label'=>'Blog Link','type'=>'text'],
        ],
    ];

    $monthlyTable = 'kltg_monthly_details';

    // ========= Base rows: Get active companies from monthly details with month separation =========
    $rows = DB::table('kltg_monthly_details as md')
        ->join('master_files as mf', 'mf.id', '=', 'md.master_file_id')
        ->select([
            'mf.id as id',
    'mf.id as master_file_id',
    'mf.date',
    'mf.company as company_name',
    'mf.client',
    DB::raw("COALESCE(NULLIF(mf.product,''), '') as mf_title"),
    'mf.created_at', // ✅ tambahkan ini supaya $row->created_at ada
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
                        END
                    ,2,'0'),
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
        ->when($scope === 'month_year', function($q) use ($month, $year) {
            $monthName = strtolower(Carbon::create(null, $month, 1)->format('F'));
            $q->where(function($w) use ($month, $year, $monthName) {
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
        })
        ->when($scope === 'month_only', function($q) use ($month) {
            $monthName = strtolower(Carbon::create(null, $month, 1)->format('F'));
            $q->where(function($mq) use ($month, $monthName) {
                $mq->where('md.month', (int)$month)
                   ->orWhereRaw('CAST(md.month AS UNSIGNED) = ?', [(int)$month])
                   ->orWhereRaw('LOWER(md.month) = ?', [$monthName])
                   ->orWhereMonth('md.value_date', (int)$month);
            });
        })
        ->when($scope === 'year_only', function($q) use ($year) {
            $q->where(function($yq) use ($year) {
                $yq->where('md.year', (int)$year)
                   ->orWhereYear('md.value_date', (int)$year);
            });
        })
        ->groupBy('mf.id', 'mf.date', 'mf.company', 'mf.client', 'mf.product', 'md.year', 'md.month', 'md.value_date','mf.created_at')
        ->orderBy('activity_date', 'ASC')
        ->orderBy('mf.id', 'ASC')
        ->get();

    // ========= Existing coordinator values (filtered by month/year) =========
    $existing = collect();
if ($rows->isNotEmpty()) {
    $existing = KltgCoordinatorList::query()
        ->whereIn('master_file_id', $rows->pluck('master_file_id')->unique()->all())
        ->where('subcategory', $storedSub)
        ->get()
        ->keyBy(function($item) {
            return $item->master_file_id;
        });
}

    // ========= Edition/Publication (header rows) =========
    $editionPub = DB::table('kltg_monthly_details as d')
        ->selectRaw("
            d.master_file_id,
            MAX(CASE WHEN d.type = 'EDITION'     THEN NULLIF(COALESCE(d.value_text, d.value, ''), '') END) AS edition,
            MAX(CASE WHEN d.type = 'PUBLICATION' THEN NULLIF(COALESCE(d.value_text, d.value, ''), '') END) AS publication
        ")
        ->whereRaw('TRIM(UPPER(d.category)) = ?', ['KLTG'])
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

    // Inject ke rows (display only)
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

    return view('coordinators.kltg', [
        'rows'        => $rows,
        'existing'    => $existing,
        'columns'     => $columns,
        'activeTab'   => $activeTab,
        'month'       => $month,
        'year'        => $year,
        'scope'       => $scope,
        'periodLabel' => $periodLabel,
    ]);
}

    /**
     * UPSERT: create or update ONE row per (master_file_id, subcategory)
     * Accepts many fields at once (your current pattern).
     */

    public function upsert(Request $request)
{
    // 1) Normalize tab/subcategory
    $tab = strtolower((string)$request->input('subcategory', ''));
    $storedSub = $this->tabToStored($tab); // e.g. print -> KLTG, video -> Video, ...

    // 2) Validate basic payload
    $validated = $request->validate([
        'master_file_id' => ['required','integer', Rule::exists('master_files','id')],
        'subcategory'    => ['required', Rule::in(['print','video','article','lb','em'])],
        'field'          => ['required','string'],
        'value'          => ['nullable'],
        'force_clear'    => ['sometimes','boolean'], // optional: true untuk sengaja hapus nilai
    ]);

    $sub   = $validated['subcategory'];   // print|video|article|lb|em (untuk whitelist)
    $field = $validated['field'];
    $value = $validated['value'];

    // 3) Normalize *_date keys -> canonical DB columns
    $dateKeyMap = [
        'artwork_reminder_date'    => 'artwork_reminder',
        'material_received_date'   => 'material_record',
        'artwork_done'        => 'artwork_reminder', // fallback aman utk legacy UI
        'send_chop_sign_date'      => 'send_chop_sign',
        'chop_sign_approval_date'  => 'chop_sign_approval',
        'park_in_server_date'      => 'park_in_file_server',
        'video_done_date'          => 'video_done',
        'pending_approval_date'    => 'pending_approval',
        'video_approved_date'      => 'video_approved',
        'video_scheduled_date'     => 'video_scheduled',
        'video_posted_date'        => 'video_posted',
        'article_done_date'        => 'article_done',
        'article_approved_date'    => 'article_approved',
        'article_scheduled_date'   => 'article_scheduled',
        'article_posted_date'      => 'article_posted',
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

    // 5) Allowed DB columns per tab (sinkron dgn skema tabel)
    $allow = [
        'print' => [
            'title_snapshot','company_snapshot','client_bp','x','edition','publication',
            'artwork_bp_client','artwork_reminder','material_record',
            'send_chop_sign','chop_sign_approval','park_in_file_server','remarks'
        ],
        'video' => [
            'title_snapshot','company_snapshot','client_bp','x','remarks','material_reminder_text',
            'material_record','video_done','pending_approval','video_approved',
            'video_scheduled','video_posted','post_link'
        ],
        'article' => [
            'title_snapshot','company_snapshot','client_bp','x','remarks','material_reminder_text',
            'material_record','article_done','pending_approval','article_approved',
            'article_scheduled','article_posted','post_link'
        ],
        'lb' => [
            'title_snapshot','company_snapshot','client_bp','x','remarks','material_reminder_text',
            'material_record','video_done','pending_approval','video_approved',
            'video_scheduled','video_posted','post_link'
        ],
        'em' => [
            'company_snapshot','client_bp','remarks',
            'em_date_write','em_date_to_post','em_post_date','em_qty','blog_link'
        ],
    ];

    if (!in_array($column, $allow[$sub] ?? [], true)) {
        return response()->json([
            'ok' => false,
            'error' => "The selected field key '{$column}' is invalid for subcategory '{$sub}'",
            'allowed' => $allow[$sub] ?? [],
        ], 422);
    }

    // 6) Coerce types
    // x = varchar(500), tapi boleh terima boolean → simpan '1' atau '0'
    if ($column === 'x') {
        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
    }

    // daftar kolom DATE sesuai skema (jangan masukkan park_in_file_server karena itu varchar)
    $dateColumns = [
        'artwork_reminder','material_record','send_chop_sign','chop_sign_approval',
        'video_done','pending_approval','video_approved','video_scheduled','video_posted',
        'article_done','article_approved','article_scheduled','article_posted',
        'em_date_write','em_date_to_post','em_post_date',
    ];

    if (in_array($column, $dateColumns, true)) {
        if ($value === '' || $value === null) {
            // kosong → hanya clear jika force_clear=true
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
        // text/number
        if ($column === 'em_qty') {
            $value = ($value === '' || $value === null) ? null : (int)$value;
        } else {
            // Untuk kolom string biasa: jika '', jangan overwrite kecuali force_clear=true
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

    // 7) Keys untuk upsert (gunakan stored subcategory untuk DB)
    $keys = [
        'master_file_id' => (int)$validated['master_file_id'],
        'subcategory'    => $storedSub, // contoh tersimpan: 'KLTG'
    ];

    // 8) Pastikan kolom ada di tabel (defensive)
    $cols = Schema::getColumnListing('kltg_coordinator_lists');
    if (!in_array($column, $cols, true)) {
        return response()->json(['ok'=>false,'error'=>"Unknown column '{$column}' in table"], 422);
    }

    // 9) Upsert tanpa menimpa kolom lain
    //    - buat row jika belum ada (isi title/company dari request hanya saat insert perdana)
    $model = KltgCoordinatorList::firstOrCreate(
        $keys,
        [
            'company_snapshot' => $request->input('company') ?: null,
            'title_snapshot'   => $request->input('title') ?: null,
        ]
    );

    // update hanya kolom yang diedit
    $model->fill([$column => $value]);
    $model->save();

    return response()->json([
        'ok'     => true,
        'where'  => $keys,
        'column' => $column,
        'value'  => $value,
        'row'    => $model->fresh(),
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




  public function export(Request $request)
{
    // Log request parameters for debugging
    Log::info('EXPORT - Request params:', $request->all());

    // Get and normalize request parameters
    $working = $request->get('working');
    $activeSubcat = $this->normalizeSubcat($request->get('subcategory'));
    $key = $this->storedToTab($activeSubcat);

    Log::info('EXPORT - Normalized:', [
        'activeSubcat' => $activeSubcat,
        'key'          => $key,
        'working'      => $working,
    ]);

    // Build database query
    $query = $this->buildQuery($activeSubcat, $working);

    // Log query information
    $finalCount = $query->count();
    Log::info('EXPORT - Final query result:', [
        'count'    => $finalCount,
        'sql'      => $query->toSql(),
        'bindings' => $query->getBindings(),
    ]);

    // Generate filename with .xls extension
    $filename = sprintf(
        'kltg_%s_%s.xls',  // ← Changed to .xls for HTML-Excel format
        strtolower($activeSubcat),
        now()->format('Ymd_His')
    );

    // Set response headers for Excel
    $headers = [
        'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
        'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        'Cache-Control'       => 'no-store, no-cache',
    ];

    // Get fields for current subcategory (ordered columns)
    $columnsBySubcat = $this->getColumnsBySubcat();
    $fields = $columnsBySubcat[$key] ?? $columnsBySubcat['print'];

    // Get labels
    $labels = $this->getFieldLabels();

    // Generate titles
    $mainTitle = 'KLTG - COORDINATOR LIST - ' . $this->subcatTitle($activeSubcat);
    $monthLabel = request()->route() ? $this->monthFilterLabel(request()) : null;

    // Return streamed HTML-Excel response
    return response()->stream(
        function () use ($query, $fields, $labels, $mainTitle, $monthLabel, $working) {
            $this->streamXlsHtmlOutput($query, $fields, $labels, $mainTitle, $monthLabel, $working);
        },
        200,
        $headers
    );
}

/**
 * Stream HTML-XLS output to browser
 * Excel can open HTML files with styling and treat them as .xls files
 */
private function streamXlsHtmlOutput($query, array $fields, array $labels, string $mainTitle, ?string $monthLabel, ?string $working): void
{
    // BOM for UTF-8
    echo "\xEF\xBB\xBF";

    $colCount = count($fields);

    // ====== OPEN HTML + STYLE ======
    echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>
        table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; }
        th, td { border: 1px solid #CCCCCC; padding: 6px; vertical-align: middle; }
        thead th { background: #FFF7AE; font-weight: 700; text-align: center; }
        .title { background: #FFF200; font-weight: 700; text-align: center; font-size: 16px; }
        .meta { text-align: center; font-size: 12px; }
        tbody td { font-size: 11px; }
        .number { text-align: center; }
    </style></head><body>';

    // ====== TITLE ROWS (MERGED) ======
    echo '<table>';
    echo '<tr><td class="title" colspan="'.$colCount.'">'.htmlspecialchars($mainTitle, ENT_QUOTES, 'UTF-8').'</td></tr>';
    echo '<tr><td class="meta" colspan="'.$colCount.'">Generated: '.htmlspecialchars(now()->format('Y-m-d H:i:s'), ENT_QUOTES, 'UTF-8').'</td></tr>';

    if ($monthLabel) {
        echo '<tr><td class="meta" colspan="'.$colCount.'">Month: '.htmlspecialchars($monthLabel, ENT_QUOTES, 'UTF-8').'</td></tr>';
    }

    if ($working === 'working') {
        echo '<tr><td class="meta" colspan="'.$colCount.'">Status: Working</td></tr>';
    } elseif ($working === 'completed') {
        echo '<tr><td class="meta" colspan="'.$colCount.'">Status: Completed</td></tr>';
    }

    // Spacer row
    echo '<tr><td colspan="'.$colCount.'">&nbsp;</td></tr>';

    // ====== HEADER ROW ======
    echo '<thead><tr>';
    foreach ($fields as $field) {
        $label = $labels[$field] ?? Str::headline($field);
        echo '<th>'.htmlspecialchars($label, ENT_QUOTES, 'UTF-8').'</th>';
    }
    echo '</tr></thead>';

    // ====== DATA ROWS ======
    echo '<tbody>';
    $seq = 0;
    foreach ($query->orderByDesc('kltg_coordinator_lists.created_at')->cursor() as $row) {
        $seq++;
        echo '<tr>';
        foreach ($fields as $field) {
            if ($field === '__no') {
                echo '<td class="number">'.htmlspecialchars($seq, ENT_QUOTES, 'UTF-8').'</td>';
            } else {
                $value = $this->valueForField($row, $field);
                echo '<td>'.htmlspecialchars($value, ENT_QUOTES, 'UTF-8').'</td>';
            }
        }
        echo '</tr>';
    }
    echo '</tbody></table>';

    // ====== CLOSE HTML ======
    echo '</body></html>';
}

/**
 * Define columns for each subcategory
 * (Keep these as DB/field keys used by valueForField)
 */
private function getColumnsBySubcat(): array
{
    // Special tokens:
    // __no              -> running row number (1..N)
    // __date_created    -> master_files.created_at
    // __company         -> prefer company_snapshot; fallback to mf_company
    // __pic             -> master_files.client as "Person In Charge"
    // __clients_from_mf -> master_files.client as "Clients (from master_files)" (EM only)

    return [
        'print' => [ // KLTG / PRINT ARTWORK
            '__no','__date_created','__company','__pic',
            'title_snapshot','client_bp','x','edition','publication',
            'artwork_bp_client',
            'artwork_reminder_date','material_received_date','artwork_done_date',
            'send_chop_sign_date','chop_sign_approval_date','park_in_file_server',
        ],
        'video' => [
            '__no','__date_created','__company','__pic',
            'title_snapshot','client_bp','x',
            'material_reminder_text','material_record', // material received in DB is "material_record" per your map
            'video_done','pending_approval','video_scheduled','video_posted','post_link',
        ],
        'article' => [
            '__no','__date_created','__company','__pic',
            'title_snapshot','client_bp','x',
            'material_reminder_text','material_record',
            'article_done','pending_approval','article_approved','article_scheduled','article_posted','post_link',
        ],
        'lb' => [
            '__no','__date_created','__company','__pic',
            'title_snapshot','client_bp','x',
            'material_reminder_text','material_record',
            'video_done','pending_approval','video_approved','video_scheduled','video_posted','post_link',
        ],
        'em' => [
            '__no','__date_created','__company','__pic',
            '__clients_from_mf',
            'em_date_write','em_date_to_post','em_post_date','em_qty','blog_link',
        ],
    ];
}

/**
 * Define field labels for Excel headers
 * (Keys must match fields above so header lookup works)
 */
private function getFieldLabels(): array
{
    return [
        // special tokens
        '__no'               => 'No',
        '__date_created'     => 'Date Created',
        '__company'          => 'Company',
        '__pic'              => 'Person In Charge',
        '__clients_from_mf'  => 'Clients (from master_files)',

        // common/front columns
        'title_snapshot'     => 'Title',
        'client_bp'          => 'Client/BP',
        'x'                  => 'X (text)',
        'edition'            => 'Edition',
        'publication'        => 'Publication',

        // print-specific labels (include *both* raw & _date keys to be safe)
        'artwork_bp_client'        => 'Artwork (BP/Client)',
        'artwork_reminder'         => 'Artwork Reminder',
        'artwork_reminder_date'    => 'Artwork Reminder',
        'material_received_date'   => 'Material Received',
        'material_record'          => 'Material Received',
        'artwork_done'             => 'Artwork Done',
        'artwork_done_date'        => 'Artwork Done',
        'send_chop_sign'           => 'Send Chop & Sign',
        'send_chop_sign_date'      => 'Send Chop & Sign',
        'chop_sign_approval'       => 'Chop & Sign Approval',
        'chop_sign_approval_date'  => 'Chop & Sign Approval',
        'park_in_file_server'      => 'Park in file server',

        // video/article/lb
        'material_reminder_text'   => 'Material Reminder',
        'video_done'               => 'Video Done',
        'pending_approval'         => 'Pending Approval',
        'video_approved'           => 'Video Approved',
        'video_scheduled'          => 'Video Scheduled',
        'video_posted'             => 'Video Posted',
        'post_link'                => 'Post Link',

        'article_done'             => 'Article Done',
        'article_approved'         => 'Article Approved',
        'article_scheduled'        => 'Article Scheduled',
        'article_posted'           => 'Article Posted',

        // EM
        'em_date_write'            => 'Date Write',
        'em_date_to_post'          => 'Date to post',
        'em_post_date'             => 'Post date',
        'em_qty'                   => 'EM-qty',
        'blog_link'                => 'Blog Link',
    ];
}

/**
 * Build database query based on filters
 */
private function buildQuery(string $activeSubcat, ?string $working)
{
    $query = KltgCoordinatorList::query()
        ->join('master_files', 'kltg_coordinator_lists.master_file_id', '=', 'master_files.id')
        ->whereRaw('TRIM(UPPER(kltg_coordinator_lists.subcategory)) = ?', [strtoupper($activeSubcat)])
        ->select('kltg_coordinator_lists.*')
        ->addSelect([
            DB::raw('master_files.created_at as mf_created_at'),
            DB::raw('master_files.client as mf_client'),   // Person In Charge / Clients
            DB::raw('master_files.company as mf_company'), // optional fallback for Company
        ]);

    if ($working === 'working') {
        $query->where(function ($q) {
            $q->whereNull('kltg_coordinator_lists.park_in_file_server')
              ->orWhere('kltg_coordinator_lists.park_in_file_server', '');
        });
    } elseif ($working === 'completed') {
        $query->whereNotNull('kltg_coordinator_lists.park_in_file_server')
              ->where('kltg_coordinator_lists.park_in_file_server', '!=', '');
    }

    return $query;
}

/**
 * Get subcategory title for display
 */
private function subcatTitle(string $key): string
{
    $map = [
        'print' => 'PRINT ARTWORK',
        'video' => 'VIDEO',
        'article' => 'ARTICLE',
        'lb' => 'LB',
        'em' => 'EM',
    ];

    return $map[strtolower(trim($key))] ?? Str::upper($key);
}

/**
 * Generate month filter label for Excel
 */
private function monthFilterLabel(Request $request): ?string
{
    $rawMonth = $request->input('month');
    $rawYear = $request->input('year');

    if ($rawMonth === null && $rawYear === null) {
        return null;
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
            return Carbon::createFromDate(now()->year, $month, 1)->format('F');
        }
        if (!$month && $year) {
            return (string) $year;
        }
    } catch (\Throwable $e) {
        // Return null if date creation fails
    }

    return null;
}

/**
 * Get formatted value for a specific field
 */
protected function valueForField($row, $fieldKey)
{
    // --- special tokens first ---
    if ($fieldKey === '__date_created') {
        $v = $row->mf_created_at ?? null;
        if (empty($v)) return '';
        try {
            return Carbon::parse($v)->format('Y-m-d');
        } catch (\Throwable $e) {
            return (string)$v;
        }
    }

    if ($fieldKey === '__company') {
        // prefer snapshot; fallback to MF company if empty
        $snap = $row->company_snapshot ?? null;
        $mf   = $row->mf_company ?? null;
        return (string)($snap !== null && $snap !== '' ? $snap : ($mf ?? ''));
    }

    if ($fieldKey === '__pic') { // Person In Charge
        return (string)($row->mf_client ?? '');
    }

    if ($fieldKey === '__clients_from_mf') {
        return (string)($row->mf_client ?? '');
    }

    // --- existing mapping follows ---
    $fieldMapping = [
        'title_snapshot' => 'title_snapshot',
        'company_snapshot' => 'company_snapshot',
        'client_bp' => 'client_bp',
        'x' => 'x',
        'edition' => 'edition',
        'publication' => 'publication',
        'remarks' => 'remarks',

        'artwork_bp_client' => 'artwork_bp_client',
        'artwork_reminder_date' => 'artwork_reminder',
        'material_received_date' => 'material_record',
        'artwork_done_date' => 'artwork_done',
        'send_chop_sign_date' => 'send_chop_sign',
        'chop_sign_approval_date' => 'chop_sign_approval',
        'park_in_file_server' => 'park_in_file_server',

        'material_reminder_text' => 'material_reminder_text',
        'video_done' => 'video_done',
        'pending_approval' => 'pending_approval',
        'video_approved' => 'video_approved',
        'video_scheduled' => 'video_scheduled',
        'video_posted' => 'video_posted',
        'post_link' => 'post_link',

        'article_done' => 'article_done',
        'article_approved' => 'article_approved',
        'article_scheduled' => 'article_scheduled',
        'article_posted' => 'article_posted',

        'em_date_write' => 'em_date_write',
        'em_date_to_post' => 'em_date_to_post',
        'em_post_date' => 'em_post_date',
        'em_qty' => 'em_qty',
        'blog_link' => 'blog_link',
        'created_at' => 'mf_created_at', // legacy support if still referenced anywhere
    ];

    $dbColumn = $fieldMapping[$fieldKey] ?? $fieldKey;
    $value = $row->{$dbColumn} ?? '';

    // Date formatting (include mf_created_at + both raw/_date keys)
    $dateFields = [
        'artwork_reminder','material_record','artwork_done',
        'send_chop_sign','chop_sign_approval',
        'video_done','pending_approval','video_approved','video_scheduled','video_posted',
        'article_done','article_approved','article_scheduled','article_posted',
        'em_date_write','em_date_to_post','em_post_date',
        'mf_created_at',
    ];
    if (in_array($dbColumn, $dateFields, true) || in_array($fieldKey, $dateFields, true)) {
        if (empty($value)) return '';
        if (is_object($value) && method_exists($value, 'format')) {
            return $value->format('Y-m-d');
        }
        if (is_string($value)) {
            try { return Carbon::parse($value)->format('Y-m-d'); }
            catch (\Throwable $e) { return $value; }
        }
    }

    return (string)$value;
}

}


