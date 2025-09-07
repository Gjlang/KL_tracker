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
    Log::info('EXPORT - Request params:', $request->all());

    $working      = $request->get('working');
    $activeSubcat = $this->normalizeSubcat($request->get('subcategory'));
    $key          = $this->storedToTab($activeSubcat);

    Log::info('EXPORT - Normalized:', [
        'activeSubcat' => $activeSubcat,
        'key' => $key,
        'working' => $working
    ]);

    $columnsBySubcat = [
    'print' => [
        'title_snapshot','company_snapshot','client_bp','x','edition','publication',
        'artwork_bp_client',
        'artwork_reminder','material_record','artwork_done',
        'send_chop_sign','chop_sign_approval',
        'park_in_file_server','remarks',
    ],
    'video' => [
        'title_snapshot','company_snapshot','client_bp','x','remarks','material_reminder_text',
        'video_done','pending_approval','video_approved',
        'video_scheduled','video_posted','post_link',
    ],
    'article' => [
        'title_snapshot','company_snapshot','client_bp','x','remarks','material_reminder_text',
        'article_done','article_approved','article_scheduled','article_posted',
        'post_link',
    ],
    'lb' => [
        'title_snapshot','company_snapshot','client_bp','x','remarks','material_reminder_text',
        'video_done','pending_approval','video_approved',
        'video_scheduled','video_posted','post_link',
    ],
    'em' => [
        'company_snapshot','client_bp','remarks',
        'em_date_write','em_date_to_post','em_post_date','em_qty','blog_link',
    ],
];

    $labels = [
        'title_snapshot'=>'Title','company_snapshot'=>'Company','client_bp'=>'Client/BP','x'=>'X (text)',
        'edition'=>'Edition','publication'=>'Publication','artwork_bp_client'=>'Artwork BP/Client',
        'artwork_reminder_date'=>'Artwork Reminder','material_received_date'=>'Material Received',
        'artwork_done'=>'Artwork Done','send_chop_sign_date'=>'Send Chop & Sign',
        'chop_sign_approval_date'=>'Chop & Sig Approval', 'park_in_file_server'=>'Park in File Server',
        'remarks'=>'Remarks', 'material_reminder_text'=>'Material Reminder (Text)',
        'video_done'=>'Video Done','pending_approval'=>'Pending Approval','video_approved'=>'Video Approved',
        'video_scheduled'=>'Video Scheduled','video_posted'=>'Video Posted','post_link'=>'Post Link',
        // ... other labels
    ];

    $fields = $columnsBySubcat[$key] ?? $columnsBySubcat['print'];
    $headersRow = array_merge(['ID'], array_map(fn($f) => $labels[$f] ?? Str::headline($f), $fields));

    // SIMPLE QUERY - Only filter by subcategory, NO DATE FILTERS for now
    $query = KltgCoordinatorList::query()
        ->leftJoin('master_files', 'kltg_coordinator_lists.master_file_id', '=', 'master_files.id')
        ->whereRaw('TRIM(UPPER(kltg_coordinator_lists.subcategory)) = ?', [strtoupper($activeSubcat)])
        ->select('kltg_coordinator_lists.*');

    // TEMPORARILY COMMENT OUT DATE FILTERS
    /*
    if ($month !== null) {
        // Date filtering logic...
    }
    if ($year !== null) {
        // Year filtering logic...
    }
    */

    // Working/completed filter (keep this)
    if ($working === 'working') {
        $query->where(fn($q) => $q->whereNull('kltg_coordinator_lists.park_in_file_server')
                                  ->orWhere('kltg_coordinator_lists.park_in_file_server',''));
    } elseif ($working === 'completed') {
        $query->whereNotNull('kltg_coordinator_lists.park_in_file_server')
              ->where('kltg_coordinator_lists.park_in_file_server','!=','');
    }

    $finalCount = $query->count();
    Log::info('EXPORT - Final query result:', [
        'count' => $finalCount,
        'sql' => $query->toSql(),
        'bindings' => $query->getBindings()
    ]);

    $filename = sprintf(
        'kltg_%s_%s.csv',
        strtolower($activeSubcat),
        now()->format('Ymd_His')
    );

    $headers = [
        'Content-Type'        => 'text/csv; charset=UTF-8',
        'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        'Cache-Control'       => 'no-store, no-cache',
    ];

    return response()->stream(function () use ($query, $fields, $headersRow, $activeSubcat) {
    $out = fopen('php://output', 'w');
    echo "\xEF\xBB\xBF"; // BOM for Excel

    // === Custom header rows ===
    fputcsv($out, [strtoupper($activeSubcat) . ' EXPORT REPORT']); // Title
    fputcsv($out, ['Generated at: ' . now()->format('Y-m-d H:i:s')]); // Timestamp
    fputcsv($out, []); // Empty row for spacing

    // === Column header row ===
    fputcsv($out, $headersRow);

    // === Data rows ===
    foreach ($query->orderByDesc('kltg_coordinator_lists.created_at')->cursor() as $row) {
        $values = [$row->id];
        foreach ($fields as $f) {
            $values[] = $this->valueForField($row, $f);
        }
        fputcsv($out, $values);
    }
    fclose($out);
}, 200, $headers);

}

// public function export(Request $request)
// {
//     // delegate to your XLSX exporter so old routes keep working
//     return $this->exportXlsx($request);
// }

// public function exportXlsx(Request $request)
// {
//     // ---- 1) Build the same filters you already have ----
//     $working      = $request->get('working');
//     $activeSubcat = $this->normalizeSubcat($request->get('subcategory')); // 'KLTG'|'Video'|'Article'|'LB'|'EM'
//     $key          = $this->storedToTab($activeSubcat);                    // 'print'|'video'|'article'|'lb'|'em'

//     $monthRaw = trim((string) $request->get('month', ''));
//     $month    = $this->normalizeMonth($monthRaw); // 1..12 or null
//     $yearRaw  = $request->get('year');
//     $year     = ($yearRaw !== null && $yearRaw !== '' && ctype_digit((string)$yearRaw)) ? (int)$yearRaw : null;

//     // Columns per your real DB schema - Fixed field names to match valueForField mapping
//     $columnsBySubcat = [
//         'print' => [
//             'title_snapshot','company_snapshot','client_bp','x','edition','publication',
//             'artwork_bp_client','artwork_reminder','material_record','artwork_done',
//             'send_chop_sign','chop_sign_approval','park_in_file_server','remarks',
//         ],
//         'video' => [
//             'title_snapshot','company_snapshot','client_bp','x','remarks','material_reminder_text',
//             'video_done','pending_approval','video_approved','video_scheduled','video_posted','post_link',
//         ],
//         'article' => [
//             'title_snapshot','company_snapshot','client_bp','x','remarks','material_reminder_text',
//             'article_done','article_approved','article_scheduled','article_posted','post_link',
//         ],
//         'lb' => [
//             'title_snapshot','company_snapshot','client_bp','x','remarks','material_reminder_text',
//             'video_done','pending_approval','video_approved','video_scheduled','video_posted','post_link',
//         ],
//         'em' => [
//             'company_snapshot','client_bp','remarks','em_date_write','em_date_to_post','em_post_date','em_qty','blog_link',
//         ],
//     ];

//     $labels = [
//         'title_snapshot'=>'Title','company_snapshot'=>'Company','client_bp'=>'Client/BP','x'=>'X (text)',
//         'edition'=>'Edition','publication'=>'Publication','artwork_bp_client'=>'Artwork BP/Client',
//         'artwork_reminder'=>'Artwork Reminder','material_record'=>'Material Received',
//         'artwork_done'=>'Artwork Done','send_chop_sign'=>'Send Chop & Sign','chop_sign_approval'=>'Chop & Sig Approval',
//         'park_in_file_server'=>'Park in File Server','remarks'=>'Remarks','material_reminder_text'=>'Material Reminder (Text)',
//         'video_done'=>'Video Done','pending_approval'=>'Pending Approval','video_approved'=>'Video Approved',
//         'video_scheduled'=>'Video Scheduled','video_posted'=>'Video Posted','post_link'=>'Post Link',
//         'article_done'=>'Article Done','article_approved'=>'Article Approved','article_scheduled'=>'Article Scheduled','article_posted'=>'Article Posted',
//         'em_date_write'=>'EM Date Write','em_date_to_post'=>'EM Date To Post','em_post_date'=>'EM Post Date','em_qty'=>'EM Qty','blog_link'=>'Blog Link',
//     ];

//     $fields     = $columnsBySubcat[$key] ?? $columnsBySubcat['print'];
//     $headersRow = array_merge(['ID'], array_map(fn($f) => $labels[$f] ?? \Illuminate\Support\Str::headline($f), $fields));

//     // Base query (left join for forgiving month/year matching)
//     $query = \App\Models\KltgCoordinatorList::query()
//         ->leftJoin('master_files', 'kltg_coordinator_lists.master_file_id', '=', 'master_files.id')
//         ->whereRaw('TRIM(UPPER(kltg_coordinator_lists.subcategory)) = ?', [strtoupper($activeSubcat)])
//         ->select('kltg_coordinator_lists.*');

//     if ($month !== null) {
//         $query->where(function($w) use ($month){
//             $w->orWhereMonth('master_files.date', (int)$month)
//               ->orWhereMonth('master_files.date_finish', (int)$month)
//               ->orWhereMonth('master_files.invoice_date', (int)$month)
//               ->orWhereMonth('master_files.created_at', (int)$month);
//             if (\Illuminate\Support\Facades\Schema::hasColumn('master_files','month')) {
//                 $w->orWhere('master_files.month', (int)$month)
//                   ->orWhereRaw('CAST(master_files.month AS UNSIGNED) = ?', [(int)$month]);
//             }
//         });
//     }
//     if ($year !== null) {
//         $query->where(function($w) use ($year){
//             if (\Illuminate\Support\Facades\Schema::hasColumn('master_files','year')) $w->orWhere('master_files.year', (int)$year);
//             $w->orWhereYear('master_files.date', (int)$year)
//               ->orWhereYear('master_files.date_finish', (int)$year)
//               ->orWhereYear('master_files.invoice_date', (int)$year)
//               ->orWhereYear('master_files.created_at', (int)$year);
//         });
//     }
//     if ($working === 'working') {
//         $query->where(fn($q) => $q->whereNull('kltg_coordinator_lists.park_in_file_server')
//                                   ->orWhere('kltg_coordinator_lists.park_in_file_server',''));
//     } elseif ($working === 'completed') {
//         $query->whereNotNull('kltg_coordinator_lists.park_in_file_server')
//               ->where('kltg_coordinator_lists.park_in_file_server','!=','');
//     }

//     $rows = $query->orderByDesc('kltg_coordinator_lists.created_at')->get();

//     // Debug: Check if we have data
//     if ($rows->isEmpty()) {
//         \Log::warning('No data found for export', [
//             'subcategory' => $activeSubcat,
//             'month' => $month,
//             'year' => $year,
//             'working' => $working
//         ]);
//     }

//     // ---- 2) Build XLSX with styling/merged header ----
//     $sheetTitle = strtoupper($activeSubcat) . ' EXPORT REPORT';
//     $subtitle   = 'Generated at: ' . now()->format('Y-m-d H:i:s');

//     $ss = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
//     $ws = $ss->getActiveSheet();
//     $ws->setTitle('Export');

//     // helper to convert 1-based column index to letters - using consistent function
//     $colCount = count($headersRow);
//     $lastCol = $this->colLetters($colCount);

//     // Row 1: big title, merged, yellow bg, centered bold
//     $ws->setCellValue("A1", $sheetTitle);
//     $ws->mergeCells("A1:{$lastCol}1");
//     $ws->getStyle("A1:{$lastCol}1")->applyFromArray([
//         'alignment' => ['horizontal'=>\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'=>\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
//         'font'      => ['bold'=>true, 'size'=>14],
//         'fill'      => ['fillType'=>\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor'=>['rgb'=>'FFFF00']],
//     ]);
//     $ws->getRowDimension(1)->setRowHeight(26);

//     // Row 2: subtitle, merged, yellow bg, centered
//     $ws->setCellValue("A2", $subtitle);
//     $ws->mergeCells("A2:{$lastCol}2");
//     $ws->getStyle("A2:{$lastCol}2")->applyFromArray([
//         'alignment' => ['horizontal'=>\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'=>\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
//         'font'      => ['bold'=>false, 'size'=>11],
//         'fill'      => ['fillType'=>\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor'=>['rgb'=>'FFFF00']],
//     ]);
//     $ws->getRowDimension(2)->setRowHeight(20);

//     // Empty spacer row 3
//     // Row 4: table headers
//     $startRow = 4;
//     for ($i = 0; $i < count($headersRow); $i++) {
//         $coord = $this->colLetters($i+1) . $startRow;
//         $ws->setCellValue($coord, (string)$headersRow[$i]);
//     }
//     $ws->getStyle("A{$startRow}:{$lastCol}{$startRow}")->applyFromArray([
//         'font'      => ['bold'=>true],
//         'alignment' => ['horizontal'=>\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
//         'borders'   => ['bottom'=>['borderStyle'=>\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
//         'fill'      => ['fillType'=>\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor'=>['rgb'=>'F2F2F2']],
//     ]);

//     // Data rows (from row 5)
//     $r = $startRow + 1;
//     foreach ($rows as $row) {
//         $values = [$row->id];
//         foreach ($fields as $f) {
//             $values[] = $this->valueForField($row, $f);
//         }

//         foreach ($values as $i => $val) {
//             $coord = $this->colLetters($i+1) . $r;
//             // Let PhpSpreadsheet handle the data type automatically
//             $ws->setCellValue($coord, $val);
//         }
//         $r++;
//     }

//     // Auto-size columns + freeze header
//     for ($i=1; $i<=$colCount; $i++) {
//         $ws->getColumnDimension($this->colLetters($i))->setAutoSize(true);
//     }
//     $ws->freezePane("A".($startRow+1)); // freeze above row 5

//     // Borders for data region
//     if ($r > $startRow+1) {
//         $ws->getStyle("A{$startRow}:{$lastCol}".($r-1))->applyFromArray([
//             'borders' => [
//                 'outline' => ['borderStyle'=>\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
//                 'inside'  => ['borderStyle'=>\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_HAIR],
//             ]
//         ]);
//     }

//     // ---- 3) Stream XLSX ----
//     $filename = sprintf('kltg_%s_%s_%s.xlsx',
//         strtolower($activeSubcat),
//         $this->monthLabel($month ?: 0),
//         $year ?? 'AllYears'
//     );

//     header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
//     header('Content-Disposition: attachment; filename="'.$filename.'"');
//     header('Cache-Control: max-age=0');

//     $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($ss);
//     $writer->save('php://output');
//     exit;
// }

// /** 1->A, 2->B, ... 27->AA */
// private function colLetters(int $i): string
// {
//     $s='';
//     while($i>0){
//         $m=($i-1)%26;
//         $s=chr(65+$m).$s;
//         $i=intdiv($i-1,26);
//     }
//     return $s;
// }

// /** "January"/"jan"/"01"/"1" -> 1..12; invalid -> null */
// private function normalizeMonth(?string $m): ?int
// {
//     if ($m === null || $m === '') return null;

//     if (preg_match('/^\d{1,2}$/', $m)) {
//         $n = (int) ltrim($m, '0');
//         if ($n === 0 && $m !== '0') { $n = (int) $m; }
//         return ($n >= 1 && $n <= 12) ? $n : null;
//     }

//     $map = [
//         'january'=>1,'jan'=>1,'february'=>2,'feb'=>2,'march'=>3,'mar'=>3,'april'=>4,'apr'=>4,
//         'may'=>5,'june'=>6,'jun'=>6,'july'=>7,'jul'=>7,'august'=>8,'aug'=>8,
//         'september'=>9,'sep'=>9,'october'=>10,'oct'=>10,'november'=>11,'nov'=>11,'december'=>12,'dec'=>12,
//     ];
//     return $map[strtolower(trim($m))] ?? null;
// }

// /** 1..12 -> 'January' etc. */
// private function monthLabel(int $m): string
// {
//     $L = [1=>'January','February','March','April','May','June','July','August','September','October','November','December'];
//     return $L[$m] ?? 'AllMonths';
// }

// protected function valueForField($row, $fieldKey)
// {
//     // Fixed field mapping - removed inconsistent _date suffixes
//     $fieldMapping = [
//         // Basic fields
//         'title_snapshot' => 'title_snapshot',
//         'company_snapshot' => 'company_snapshot',
//         'client_bp' => 'client_bp',
//         'x' => 'x',
//         'edition' => 'edition',
//         'publication' => 'publication',
//         'remarks' => 'remarks',

//         // KLTG/Print specific - fixed mapping
//         'artwork_bp_client' => 'artwork_bp_client',
//         'artwork_reminder' => 'artwork_reminder',
//         'material_record' => 'material_record',
//         'artwork_done' => 'artwork_done',
//         'send_chop_sign' => 'send_chop_sign',
//         'chop_sign_approval' => 'chop_sign_approval',
//         'park_in_file_server' => 'park_in_file_server',

//         // Video/Article/LB fields
//         'material_reminder_text' => 'material_reminder_text',
//         'video_done' => 'video_done',
//         'pending_approval' => 'pending_approval',
//         'video_approved' => 'video_approved',
//         'video_scheduled' => 'video_scheduled',
//         'video_posted' => 'video_posted',
//         'article_done' => 'article_done',
//         'article_approved' => 'article_approved',
//         'article_scheduled' => 'article_scheduled',
//         'article_posted' => 'article_posted',
//         'post_link' => 'post_link',

//         // EM fields
//         'em_date_write' => 'em_date_write',
//         'em_date_to_post' => 'em_date_to_post',
//         'em_post_date' => 'em_post_date',
//         'em_qty' => 'em_qty',
//         'blog_link' => 'blog_link',
//     ];

//     // Get the actual database column name
//     $dbColumn = $fieldMapping[$fieldKey] ?? $fieldKey;

//     // Get the value from the row
//     $value = $row->{$dbColumn} ?? '';

//     // Handle date formatting for date fields
//     if (in_array($fieldKey, [
//         'artwork_reminder','material_record','artwork_done',
//         'send_chop_sign','chop_sign_approval',
//         'video_done','pending_approval','video_approved',
//         'video_scheduled','video_posted',
//         'article_done','article_approved','article_scheduled','article_posted',
//         'em_date_write','em_date_to_post','em_post_date',
//     ])) {
//         if (empty($value)) {
//             return '';
//         }

//         // If it's already a Carbon/DateTime object
//         if (is_object($value) && method_exists($value, 'format')) {
//             return $value->format('Y-m-d');
//         }

//         // If it's a string, try to parse it
//         if (is_string($value)) {
//             try {
//                 return \Carbon\Carbon::parse($value)->format('Y-m-d');
//             } catch (\Throwable $e) {
//                 return $value; // Return as-is if can't parse
//             }
//         }
//     }

//     // For non-date fields, return as string
//     return (string) $value;
// }


protected function valueForField($row, $fieldKey)
{
    // Mapping dari field key ke database column
    $fieldMapping = [
        // Basic fields
        'title_snapshot' => 'title_snapshot',
        'company_snapshot' => 'company_snapshot',
        'client_bp' => 'client_bp',
        'x' => 'x',
        'edition' => 'edition',
        'publication' => 'publication',
        'remarks' => 'remarks',

        // KLTG/Print specific (with _date suffix in form, without in DB)
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
    ];

    // Get the actual database column name
    $dbColumn = $fieldMapping[$fieldKey] ?? $fieldKey;

    // Get the value from the row
    $value = $row->{$dbColumn} ?? '';

    // Handle date formatting
    if (in_array($fieldKey, [
          'artwork_reminder','material_record','artwork_done',
        'send_chop_sign','chop_sign_approval',
        'video_done','pending_approval','video_approved',
        'video_scheduled','video_posted',
        'article_done','article_approved','article_scheduled','article_posted',
        'em_date_write','em_date_to_post','em_post_date',
    ])) {
        if (empty($value)) {
            return '';
        }

        // If it's already a Carbon/DateTime object
        if (is_object($value) && method_exists($value, 'format')) {
            return $value->format('Y-m-d');
        }

        // If it's a string, try to parse it
        if (is_string($value)) {
            try {
                return Carbon::parse($value)->format('Y-m-d');
            } catch (\Throwable $e) {
                return $value; // Return as-is if can't parse
            }
        }
    }

    // For non-date fields, return as string
    return (string) $value;
}

// /** "January"/"jan"/"01"/"1" -> 1..12; invalid -> null */
// private function normalizeMonth(?string $m): ?int
// {
//     if ($m === null || $m === '') return null;

//     // numeric string "1" .. "12" (accept "01")
//     if (preg_match('/^\d{1,2}$/', $m)) {
//         $n = (int) ltrim($m, '0');
//         if ($n === 0 && $m !== '0') { $n = (int) $m; }
//         return ($n >= 1 && $n <= 12) ? $n : null;
//     }

//     // month names/abbrevs
//     $map = [
//         'january'=>1,'jan'=>1,
//         'february'=>2,'feb'=>2,
//         'march'=>3,'mar'=>3,
//         'april'=>4,'apr'=>4,
//         'may'=>5,
//         'june'=>6,'jun'=>6,
//         'july'=>7,'jul'=>7,
//         'august'=>8,'aug'=>8,
//         'september'=>9,'sep'=>9,
//         'october'=>10,'oct'=>10,
//         'november'=>11,'nov'=>11,
//         'december'=>12,'dec'=>12,
//     ];
//     return $map[strtolower(trim($m))] ?? null;
// }

/** 1..12 -> 'January' etc. */
// private function monthLabel(int $m): string
// {
//     $L = [
//         1=>'January','February','March','April','May','June',
//         'July','August','September','October','November','December'
//     ];
//     return $L[$m] ?? 'AllMonths';
// }

// /** Return date as TEXT for Excel so it shows exactly like DB and never '########' */
// private function fmtDate($v): string
// {
//     if (empty($v)) return '';
//     try { $s = Carbon::parse($v)->format('Y-m-d'); }
//     catch (\Throwable $e) { $s = (string) $v; }
//     return "'" . $s; // leading apostrophe forces Excel to treat as text
// }
}
