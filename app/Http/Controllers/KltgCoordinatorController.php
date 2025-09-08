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

    // ========= Subquery: master_file_id aktif di bulan & kategori terpilih =========
    // Abaikan header rows (month 0/''/null). Cocokkan bulan via md.month (int/string/bulan nama) ATAU md.value_date.
    $activeIdsSub = DB::table("$monthlyTable as md")
        ->select('md.master_file_id')
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
        ->groupBy('md.master_file_id');

    // ========= Subquery: FIRST-IN (tanggal aktivitas paling awal) =========
    $firstInSub = DB::table("$monthlyTable as md")
        ->selectRaw("
            md.master_file_id,
            MIN(
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
              )
            ) AS first_in_at
        ")
        ->whereRaw('TRIM(UPPER(md.category)) = ?', [strtoupper($storedSub)])
        // ignore header rows (Edition/Publication)
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
        ->groupBy('md.master_file_id');

    // ========= Base rows =========
    $rowsQuery = MasterFile::query()
        ->where(function ($q) use ($storedSub) {
            $q->where(function ($qq) {
                $qq->where('product_category', 'KLTG')
                   ->orWhereRaw('LOWER(product_category) LIKE ?', ['%kltg%']);
            })
            ->orWhereExists(function ($q2) use ($storedSub) {
                $q2->selectRaw('1')
                   ->from('kltg_coordinator_lists as k')
                   ->whereColumn('k.master_file_id', 'master_files.id')
                   ->where('k.subcategory', $storedSub);
            });
        })
        ->select([
            'id',
            'date',
            'company as company_name',
            'client',
            DB::raw("COALESCE(NULLIF(product,''), '') as mf_title"),
        ])
        // join first_in_at ke master_files
        ->leftJoinSub($firstInSub, 'fa', function($join){
            $join->on('fa.master_file_id', '=', 'master_files.id');
        })
        ->addSelect(DB::raw('fa.first_in_at'));

    // ========= Filter tampil: strict by month kalau month dipilih =========
    if ($month) {
        // STRICT: hanya tampil master_file dengan activity di bulan tsb untuk kategori tab ini
        $rowsQuery->whereIn('master_files.id', $activeIdsSub);
    } else {
        // No month filter → keep logic inklusif sebelumnya
        $rowsQuery->where(function ($qq) use ($monthlyTable, $storedSub, $scope, $month, $year) {
            // (A) Ada activity monthly
            $qq->whereExists(function ($q) use ($monthlyTable, $storedSub, $scope, $month, $year) {
                $monthName = $month ? strtolower(Carbon::create(null, $month, 1)->format('F')) : null;

                $q->selectRaw('1')
                  ->from("$monthlyTable as md")
                  ->whereColumn('md.master_file_id', 'master_files.id')
                  ->whereRaw('TRIM(UPPER(md.category)) = ?', [strtoupper($storedSub)])
                  ->where(function($v){
                      $v->where('is_date', 1)
                        ->orWhereNotNull('value_date')
                        ->orWhereNotNull('month');
                  })
                  ->when($scope === 'month_year', function ($w) use ($month, $year, $monthName) {
                      $w->where(function($x) use ($year, $month, $monthName) {
                          $x->where(function($y) use ($year) {
                                $y->where('md.year', (int)$year)
                                  ->orWhereYear('md.value_date', (int)$year);
                            })
                            ->where(function($m) use ($month, $monthName) {
                                $m->where('md.month', (int)$month)
                                  ->orWhereRaw('CAST(md.month AS UNSIGNED) = ?', [(int)$month])
                                  ->orWhereRaw('LOWER(md.month) = ?', [$monthName])
                                  ->orWhereMonth('md.value_date', (int)$month);
                            });
                      });
                  })
                  ->when($scope === 'month_only', function ($w) use ($month, $monthName) {
                      $w->where(function($m) use ($month, $monthName) {
                          $m->where('md.month', (int)$month)
                            ->orWhereRaw('CAST(md.month AS UNSIGNED) = ?', [(int)$month])
                            ->orWhereRaw('LOWER(md.month) = ?', [$monthName])
                            ->orWhereMonth('md.value_date', (int)$month);
                      });
                  })
                  ->when($scope === 'year_only', function ($w) use ($year) {
                      $w->where(function($y) use ($year) {
                          $y->where('md.year', (int)$year)
                            ->orWhereYear('md.value_date', (int)$year);
                      })
                      ->where(function($m){
                          $m->whereNotNull('md.month')->orWhereNotNull('md.value_date');
                      });
                  });
            });

            // (B) OR ada coordinator row utk subcategory ini
            $qq->orWhereExists(function ($q) use ($storedSub) {
                $q->selectRaw('1')
                  ->from('kltg_coordinator_lists as k')
                  ->whereColumn('k.master_file_id', 'master_files.id')
                  ->where('k.subcategory', $storedSub);
            });
        });
    }

    // ========= ORDER: First-in duluan, stabil =========
    $rows = $rowsQuery
        ->orderByRaw('COALESCE(fa.first_in_at, master_files.created_at) ASC')
        ->orderBy('master_files.id', 'ASC') // tiebreaker stabil
        ->get();

    // ========= Existing coordinator values =========
    $existing = collect();
    if ($rows->isNotEmpty()) {
        $ids = $rows->pluck('id')->all();
        $existing = KltgCoordinatorList::query()
            ->whereIn('master_file_id', $ids)
            ->where('subcategory', $storedSub)
            ->get()
            ->keyBy('master_file_id');
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
        if (isset($editionPub[$row->id])) {
            $row->edition     = $editionPub[$row->id]->edition ?? null;
            $row->publication = $editionPub[$row->id]->publication ?? null;
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

    // Define columns for each subcategory
    $columnsBySubcat = $this->getColumnsBySubcat();

    // Define field labels for headers
    $labels = $this->getFieldLabels();

    // Get fields for current subcategory
    $fields = $columnsBySubcat[$key] ?? $columnsBySubcat['print'];

    // Always include created_at at the end
    if (!in_array('created_at', $fields, true)) {
        $fields[] = 'created_at';
    }

    // Create header row (ID + humanized labels)
    $headersRow = array_merge(['ID'], array_map(
        fn ($f) => $labels[$f] ?? Str::headline($f),
        $fields
    ));

    // Build database query
    $query = $this->buildQuery($activeSubcat, $working);

    // Log query information
    $finalCount = $query->count();
    Log::info('EXPORT - Final query result:', [
        'count'    => $finalCount,
        'sql'      => $query->toSql(),
        'bindings' => $query->getBindings(),
    ]);

    // Generate filename
    $filename = sprintf(
        'kltg_%s_%s.csv',
        strtolower($activeSubcat),
        now()->format('Ymd_His')
    );

    // Set response headers
    $headers = [
        'Content-Type'        => 'text/csv; charset=UTF-8',
        'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        'Cache-Control'       => 'no-store, no-cache',
    ];

    // Titles for CSV
    $tabTitle   = $this->subcatTitle($key);
    $brandTitle = 'KLTG - COORDINATOR LIST';
    $mainTitle  = $brandTitle . ' - ' . $tabTitle;
    $monthLabel = $this->monthFilterLabel($request);

    // Return streamed CSV response
    return response()->stream(
        function () use ($query, $fields, $headersRow, $activeSubcat, $mainTitle, $monthLabel, $working) {
            $this->streamCsvOutput($query, $fields, $headersRow, $mainTitle, $monthLabel, $working);
        },
        200,
        $headers
    );
}

/**
 * Define columns for each subcategory
 * (Keep these as DB/field keys used by valueForField)
 */
private function getColumnsBySubcat(): array
{
    return [
        'print' => [
            'title_snapshot', 'company_snapshot', 'client_bp', 'x', 'edition', 'publication',
            'artwork_bp_client', 'artwork_reminder', 'material_received_date', 'artwork_done',
            'send_chop_sign', 'chop_sign_approval', 'park_in_file_server', 'remarks',
        ],
        'video' => [
            'title_snapshot', 'company_snapshot', 'client_bp', 'x', 'remarks', 'material_reminder_text',
            'video_done', 'pending_approval', 'video_approved',
            'video_scheduled', 'video_posted', 'post_link',
        ],
        'article' => [
            'title_snapshot', 'company_snapshot', 'client_bp', 'x', 'remarks', 'material_reminder_text',
            'article_done', 'article_approved', 'article_scheduled', 'article_posted', 'post_link',
        ],
        'lb' => [
            'title_snapshot', 'company_snapshot', 'client_bp', 'x', 'remarks', 'material_reminder_text',
            'video_done', 'pending_approval', 'video_approved',
            'video_scheduled', 'video_posted', 'post_link',
        ],
        'em' => [
            'company_snapshot', 'client_bp', 'remarks',
            'em_date_write', 'em_date_to_post', 'em_post_date', 'em_qty', 'blog_link',
        ],
    ];
}

/**
 * Define field labels for CSV headers
 * (Keys must match fields above so header lookup works)
 */
private function getFieldLabels(): array
{
    return [
        'title_snapshot'           => 'Title',
        'company_snapshot'         => 'Company',
        'client_bp'                => 'Client/BP',
        'x'                        => 'X (text)',
        'edition'                  => 'Edition',
        'publication'              => 'Publication',
        'artwork_bp_client'        => 'Artwork BP/Client',

        // Match the field keys in getColumnsBySubcat():
        'artwork_reminder'         => 'Artwork Reminder',
        'material_received_date'   => 'Material Received',
        'artwork_done'             => 'Artwork Done',
        'send_chop_sign'           => 'Send Chop & Sign',
        'chop_sign_approval'       => 'Chop & Sig Approval',

        'park_in_file_server'      => 'Park in File Server',
        'remarks'                  => 'Remarks',

        'material_reminder_text'   => 'Material Reminder (Text)',
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

        'em_date_write'            => 'EM Date Write',
        'em_date_to_post'          => 'EM Date to Post',
        'em_post_date'             => 'EM Post Date',
        'em_qty'                   => 'EM Quantity',
        'blog_link'                => 'Blog Link',

        // New label for created_at so header is explicit
        'created_at'               => 'Created Date',
    ];
}


    /**
     * Build database query based on filters
     */
    private function buildQuery(string $activeSubcat, ?string $working)
    {
        $query = KltgCoordinatorList::query()
            ->leftJoin('master_files', 'kltg_coordinator_lists.master_file_id', '=', 'master_files.id')
            ->whereRaw('TRIM(UPPER(kltg_coordinator_lists.subcategory)) = ?', [strtoupper($activeSubcat)])
            ->select('kltg_coordinator_lists.*')
            ->addSelect(DB::raw('master_files.created_at as mf_created_at')); // ✅ pull MF created_at


        // Apply working/completed filter
        if ($working === 'working') {
            $query->where(fn($q) => $q->whereNull('kltg_coordinator_lists.park_in_file_server')
                                      ->orWhere('kltg_coordinator_lists.park_in_file_server', ''));
        } elseif ($working === 'completed') {
            $query->whereNotNull('kltg_coordinator_lists.park_in_file_server')
                  ->where('kltg_coordinator_lists.park_in_file_server', '!=', '');
        }

        return $query;
    }

    /**
     * Stream CSV output to browser
     */
    private function streamCsvOutput($query, $fields, $headersRow, $mainTitle, $monthLabel, $working)
    {
        $out = fopen('php://output', 'w');

        // Add BOM for Excel UTF-8 support
        echo "\xEF\xBB\xBF";

        // Write header information
        fputcsv($out, [$mainTitle]);
        fputcsv($out, ['Generated: ' . now()->format('Y-m-d H:i:s')]);

        // Add optional context rows
        if ($monthLabel) {
            fputcsv($out, ['Month: ' . $monthLabel]);
        }

        if ($working === 'working') {
            fputcsv($out, ['Status: Working']);
        } elseif ($working === 'completed') {
            fputcsv($out, ['Status: Completed']);
        }

        // Empty spacer row
        fputcsv($out, []);

        // Write column headers
        fputcsv($out, $headersRow);

        // Write data rows
        foreach ($query->orderByDesc('kltg_coordinator_lists.created_at')->cursor() as $row) {
            $values = [$row->id];
            foreach ($fields as $field) {
                $values[] = $this->valueForField($row, $field);
            }
            fputcsv($out, $values);
        }

        fclose($out);
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
     * Generate month filter label for CSV
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
        // Field mapping from form keys to database columns
        $fieldMapping = [
            // Basic fields
            'title_snapshot' => 'title_snapshot',
            'company_snapshot' => 'company_snapshot',
            'client_bp' => 'client_bp',
            'x' => 'x',
            'edition' => 'edition',
            'publication' => 'publication',
            'remarks' => 'remarks',

            // Print specific fields
            'artwork_bp_client' => 'artwork_bp_client',
            'artwork_reminder_date' => 'artwork_reminder',
            'material_received_date' => 'material_record',
            'artwork_done' => 'artwork_done',
            'send_chop_sign_date' => 'send_chop_sign',
            'chop_sign_approval_date' => 'chop_sign_approval',
            'park_in_file_server' => 'park_in_file_server',

            // Video/Article/LB fields
            'material_reminder_text' => 'material_reminder_text',
            'video_done' => 'video_done',
            'pending_approval' => 'pending_approval',
            'video_approved' => 'video_approved',
            'video_scheduled' => 'video_scheduled',
            'video_posted' => 'video_posted',
            'article_done' => 'article_done',
            'article_approved' => 'article_approved',
            'article_scheduled' => 'article_scheduled',
            'article_posted' => 'article_posted',
            'post_link' => 'post_link',

            // EM fields
            'em_date_write' => 'em_date_write',
            'em_date_to_post' => 'em_date_to_post',
            'em_post_date' => 'em_post_date',
            'em_qty' => 'em_qty',
            'blog_link' => 'blog_link',
            'created_at' => 'mf_created_at',
        ];

        // Get database column name
        $dbColumn = $fieldMapping[$fieldKey] ?? $fieldKey;
        $value = $row->{$dbColumn} ?? '';

        // Handle date field formatting
        $dateFields = [
            'artwork_reminder', 'material_record', 'artwork_done',
            'send_chop_sign', 'chop_sign_approval',
            'video_done', 'pending_approval', 'video_approved',
            'video_scheduled', 'video_posted',
            'article_done', 'article_approved', 'article_scheduled', 'article_posted',
            'em_date_write', 'em_date_to_post', 'em_post_date',
        ];

        if (in_array($fieldKey, $dateFields)) {
            if (empty($value)) {
                return '';
            }

            // Handle Carbon/DateTime objects
            if (is_object($value) && method_exists($value, 'format')) {
                return $value->format('Y-m-d');
            }

            // Handle string dates
            if (is_string($value)) {
                try {
                    return Carbon::parse($value)->format('Y-m-d');
                } catch (\Throwable $e) {
                    return $value;
                }
            }
        }

        return (string) $value;
    }

    /**
     * Additional helper methods would go here:
     * - normalizeSubcat()
     * - storedToTab()
     * These would need to be implemented based on your specific logic
     */

}


