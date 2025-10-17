<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\MasterFile;
use App\Imports\MasterFileImport;
use Carbon\Carbon;
use App\Exports\MasterFilesExport;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Http\RedirectResponse;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use App\Models\KltgMonthlyDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use App\Models\OutdoorItem;
use Illuminate\Support\Arr;
use App\Models\Billboard;



class MasterFileController extends Controller
{


    private function selectExistingColumns(array $wanted): array
    {
        $tableCols = Schema::getColumnListing((new MasterFile)->getTable());
        return array_values(array_unique(array_filter(
            array_merge(['id'], $wanted),
            fn($c) => in_array($c, $tableCols, true)
        )));
    }


    private function applyMonthFilter($query, $rawMonth)
    {
        $m = null;
        if ($rawMonth !== null && $rawMonth !== '' && (int)$rawMonth !== 0) {
            $s = trim((string)$rawMonth);
            $map = [
                'jan' => 1,
                'january' => 1,
                'feb' => 2,
                'february' => 2,
                'mar' => 3,
                'march' => 3,
                'apr' => 4,
                'april' => 4,
                'may' => 5,
                'jun' => 6,
                'june' => 6,
                'jul' => 7,
                'july' => 7,
                'aug' => 8,
                'august' => 8,
                'sep' => 9,
                'sept' => 9,
                'september' => 9,
                'oct' => 10,
                'october' => 10,
                'nov' => 11,
                'november' => 11,
                'dec' => 12,
                'december' => 12,
            ];
            if (ctype_digit($s)) {
                $m = max(1, min(12, (int)$s));
            } else {
                $k = strtolower($s);
                $m = $map[$k] ?? null;
            }
        }

        if (!$m) return $query; // All months

        $full = strtolower(Carbon::create(null, $m, 1)->format('F'));
        $abbr = strtolower(Carbon::create(null, $m, 1)->format('M'));

        return $query->where(function ($w) use ($m, $full, $abbr) {
            if (Schema::hasColumn('master_files', 'month')) {
                $w->where(function ($x) use ($m, $full, $abbr) {
                    $x->whereRaw('CAST(`month` AS UNSIGNED) = ?', [$m])
                        ->orWhereRaw('LOWER(`month`) = ?', [$full])
                        ->orWhereRaw('LOWER(`month`) = ?', [$abbr]);
                });
            }
            if (Schema::hasColumn('master_files', 'date')) {
                $w->orWhereRaw('MONTH(`date`) = ?', [$m]);
            }
            if (Schema::hasColumn('master_files', 'date_finish')) {
                $w->orWhereRaw('MONTH(`date_finish`) = ?', [$m]);
            }
        });
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // KLTG
    // ─────────────────────────────────────────────────────────────────────────────
    public function kltg(Request $request)
    {
        $rows = MasterFile::query()
            ->select([
                'master_files.id',   // required for row identity
                'created_at',
                'month',
                'company',
                'date',              // varchar
                'date_finish',       // date
                'barter',
                'product',
                'product_category',
                'kltg_industry',
                'kltg_x',
                'kltg_edition',
                'kltg_material_cbp',
                'kltg_print',
                'kltg_article',
                'kltg_video',
                'kltg_leaderboard',
                'kltg_qr_code',
                'kltg_blog',
                'kltg_em',
                'kltg_remarks',
            ])
            // classify as KLTG by product OR category (lowercased compare)
            ->where(function ($q) {
                $kltgSet = ['kltg', 'kltg listing', 'kltg quarter page'];
                $kltgSetLower = array_map('strtolower', $kltgSet);

                $q->whereIn(DB::raw('LOWER(product)'), $kltgSetLower)
                    ->orWhereIn(DB::raw('LOWER(product_category)'), $kltgSetLower);
            })
            // filters
            ->when($request->filled('month'), fn($q) => $this->applyMonthFilter($q, $request->get('month')))
            ->when(($search = trim((string) $request->get('search', ''))) !== '', function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('company', 'like', "%{$search}%")
                        ->orWhere('product', 'like', "%{$search}%")
                        ->orWhere('kltg_industry', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('date_from'), fn($q) => $q->whereDate('created_at', '>=', $request->get('date_from')))
            ->when($request->filled('date_to'),   fn($q) => $q->whereDate('created_at', '<=', $request->get('date_to')))
            ->latest('created_at')
            ->paginate(25)
            ->appends($request->query());

        $columns = [
            ['key' => 'created_at',        'label' => 'Date Created'],
            ['key' => 'month',             'label' => 'Month'],
            ['key' => 'company',           'label' => 'Company Name'],
            ['key' => 'date',              'label' => 'Start Date'],
            ['key' => 'date_finish',       'label' => 'End Date'],
            ['key' => 'barter',            'label' => 'Barter'],
            ['key' => 'product',           'label' => 'Product'],
            ['key' => 'product_category',  'label' => 'Category'],
            ['key' => 'kltg_industry',     'label' => 'Industry'],
            ['key' => 'kltg_x',            'label' => 'KLTG X'],
            ['key' => 'kltg_edition',      'label' => 'Edition'],
            ['key' => 'kltg_material_cbp', 'label' => 'Material C/BP'],
            ['key' => 'kltg_print',        'label' => 'Print'],
            ['key' => 'kltg_article',      'label' => 'Article'],
            ['key' => 'kltg_video',        'label' => 'Video'],
            ['key' => 'kltg_leaderboard',  'label' => 'Leaderboard'],
            ['key' => 'kltg_qr_code',      'label' => 'QR Code'],
            ['key' => 'kltg_blog',         'label' => 'Blog'],
            ['key' => 'kltg_em',           'label' => 'EM'],
            ['key' => 'kltg_remarks',      'label' => 'Remarks'],
        ];

        return view('dashboard.master.kltg', [
            'rows'          => $rows,
            'columns'       => $columns,
            'column_labels' => collect($columns)->pluck('label', 'key')->all(),
            'active'        => 'kltg',
        ]);
    }


    // ─────────────────────────────────────────────────────────────────────────────
    // OUTDOOR
    // ─────────────────────────────────────────────────────────────────────────────
    public function outdoor(Request $request)
    {
        $q = MasterFile::query()
            ->from('master_files as mf')
            ->leftJoin('outdoor_items as oi', 'oi.master_file_id', '=', 'mf.id')
            ->select([
                'mf.id',
                'mf.created_at',
                'mf.month',
                'mf.company',
                'mf.product',
                DB::raw('oi.site as location'),
                'mf.duration',

                // keep master-level dates (optional, for rows with no items)
                'mf.date',
                'mf.date_finish',

                // ✅ add child-level dates so the table can use them
                DB::raw('oi.start_date as start_date'),
                DB::raw('oi.end_date   as end_date'),

                DB::raw('oi.size as outdoor_size'),
                DB::raw('oi.district_council as outdoor_district_council'),
                DB::raw('oi.coordinates as outdoor_coordinates'),
                DB::raw('oi.id as outdoor_item_id'),
                'mf.remarks',
            ])
            ->where(function ($w) {
                $w->whereRaw('LOWER(mf.product_category) LIKE ?', ['%outdoor%'])
                    ->orWhereRaw('LOWER(mf.product) LIKE ?', ['%outdoor%'])
                    ->orWhereRaw('LOWER(mf.product) LIKE ?', ['%billboard%']);
            })
            ->when(($term = trim((string) $request->get('search', ''))) !== '', function ($w) use ($term) {
                $w->where(function ($qq) use ($term) {
                    $qq->where('mf.company', 'like', "%{$term}%")
                        ->orWhere('mf.product', 'like', "%{$term}%")
                        ->orWhere('oi.site', 'like', "%{$term}%");
                });
            });

        // (filters unchanged)
        $q = $this->applyMonthFilterForJoinedQuery($q, $request->get('month'));
        if ($from = $request->get('date_from')) $q->whereDate('mf.created_at', '>=', $from);
        if ($to   = $request->get('date_to'))   $q->whereDate('mf.created_at', '<=', $to);

        $rows = $q->orderByDesc('mf.created_at')
            ->paginate(25)
            ->appends($request->query());

        $columns = [
            ['key' => 'created_at',               'label' => 'CREATED AT'],
            ['key' => 'month',                    'label' => 'MONTH'],
            ['key' => 'company',                  'label' => 'COMPANY'],
            ['key' => 'product',                  'label' => 'PRODUCT'],
            ['key' => 'location',                 'label' => 'LOCATION'],
            ['key' => 'outdoor_district_council', 'label' => 'AREA'],
            ['key' => 'duration',                 'label' => 'DURATION'],
            ['key' => 'date',                     'label' => 'DATE'],          // shown as start_date for outdoor rows (via partial mapping)
            ['key' => 'date_finish',              'label' => 'DATE FINISH'],   // shown as end_date for outdoor rows
            ['key' => 'outdoor_size',             'label' => 'OUTDOOR SIZE'],
            ['key' => 'outdoor_coordinates',      'label' => 'OUTDOOR COORDINATES'],
            ['key' => 'remarks',                  'label' => 'REMARKS'],
        ];

        return view('dashboard.master.outdoor', [
            'rows'          => $rows,
            'columns'       => $columns,
            'column_labels' => collect($columns)->pluck('label', 'key')->all(),
            'active'        => 'outdoor',
            'paginator'     => $rows,
        ]);
    }




    // Special version for joined queries (outdoor method)
    private function applyMonthFilterForJoinedQuery($query, $rawMonth)
    {
        $m = null;
        if ($rawMonth !== null && $rawMonth !== '' && (int)$rawMonth !== 0) {
            $s = trim((string)$rawMonth);
            $map = [
                'jan' => 1,
                'january' => 1,
                'feb' => 2,
                'february' => 2,
                'mar' => 3,
                'march' => 3,
                'apr' => 4,
                'april' => 4,
                'may' => 5,
                'jun' => 6,
                'june' => 6,
                'jul' => 7,
                'july' => 7,
                'aug' => 8,
                'august' => 8,
                'sep' => 9,
                'sept' => 9,
                'september' => 9,
                'oct' => 10,
                'october' => 10,
                'nov' => 11,
                'november' => 11,
                'dec' => 12,
                'december' => 12,
            ];
            if (ctype_digit($s)) {
                $m = max(1, min(12, (int)$s));
            } else {
                $k = strtolower($s);
                $m = $map[$k] ?? null;
            }
        }

        if (!$m) return $query; // All months

        $full = strtolower(Carbon::create(null, $m, 1)->format('F'));
        $abbr = strtolower(Carbon::create(null, $m, 1)->format('M'));

        return $query->where(function ($w) use ($m, $full, $abbr) {
            // For joined queries, we need to specify the table alias 'mf'
            if (Schema::hasColumn('master_files', 'month')) {
                $w->where(function ($x) use ($m, $full, $abbr) {
                    $x->whereRaw('CAST(`mf`.`month` AS UNSIGNED) = ?', [$m])
                        ->orWhereRaw('LOWER(`mf`.`month`) = ?', [$full])
                        ->orWhereRaw('LOWER(`mf`.`month`) = ?', [$abbr]);
                });
            }
            if (Schema::hasColumn('master_files', 'date')) {
                $w->orWhereRaw('MONTH(`mf`.`date`) = ?', [$m]);
            }
            if (Schema::hasColumn('master_files', 'date_finish')) {
                $w->orWhereRaw('MONTH(`mf`.`date_finish`) = ?', [$m]);
            }
        });
    }



    public function index(Request $request)
    {
        // Debug: Log the incoming request parameters
        Log::info('Filter Request:', [
            'search' => $request->get('search'),
            'status' => $request->get('status'),
            'month' => $request->get('month'),
            'all_params' => $request->all()
        ]);

        $query = MasterFile::query();

        // Apply search filter - make sure we're using the right parameter name
        if ($request->filled('search') && !empty(trim($request->get('search')))) {
            $searchTerm = trim($request->get('search'));
            Log::info('Applying search filter:', ['term' => $searchTerm]);

            $query->where(function ($q) use ($searchTerm) {
                $q->where('company', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('product', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('status', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('client', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('month', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        // Apply status filter
        if ($request->filled('status') && !empty(trim($request->get('status')))) {
            $statusFilter = trim($request->get('status'));
            Log::info('Applying status filter:', ['status' => $statusFilter]);
            $query->where('status', $statusFilter);
        }

        // Apply month filter
        if ($request->filled('month') && !empty(trim($request->get('month')))) {
            $monthFilter = trim($request->get('month'));
            Log::info('Applying month filter:', ['month' => $monthFilter]);
            $query->where('month', $monthFilter);
        }

        // 🔧 UPDATED: Apply product category filter with fallback
        if ($request->filled('product_category')) {
            $hasPC = Schema::hasColumn('master_files', 'product_category');

            if ($hasPC) {
                $query->whereIn('product', match ($request->product_category) {
                    'Outdoor' => ['HM', 'TB', 'TTM', 'BB', 'Star', 'Flyers', 'Bunting', 'Signages', 'Newspaper'],
                    'Media' => ['FB IG Ad'],
                    'KLTG' => ['KLTG', 'KLTG listing', 'KLTG quarter page', 'NP'],
                    default => []
                });
            } else {
                // fallback: filter lewat product
                $cat = strtolower($request->product_category);
                if ($cat === 'outdoor') {
                    $query->where(function ($q) {
                        $q->whereIn('product', ['HM', 'TB', 'TTM', 'BB', 'Star', 'Flyers', 'Bunting', 'Signages', 'Newspaper'])
                            ->orWhereRaw('LOWER(product) LIKE ?', ['%outdoor%']);
                    });
                } elseif ($cat === 'kltg') {
                    $query->whereRaw('LOWER(product) LIKE ?', ['%kltg%']);
                } elseif ($cat === 'media') {
                    $query->where(function ($q) {
                        $q->whereRaw('LOWER(product) LIKE ?', ['%media%'])
                            ->orWhereIn('product', ['FB IG Ad', 'Facebook', 'Instagram']);
                    });
                }
            }

            Log::info('Applying product category filter:', ['category' => $request->product_category]);
        }

        // Debug: Log the final SQL query
        Log::info('Final Query SQL:', ['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);

        // Get paginated results with filters retained
        $masterFiles = $query->orderBy('date', 'desc')->paginate(25)->withQueryString();

        // Debug: Log the result count
        Log::info('Query Results:', ['count' => $masterFiles->count(), 'total' => $masterFiles->total()]);

        // Get stats for dashboard cards
        $totalJobs = MasterFile::count();
        $completedJobs = MasterFile::where('status', 'completed')->count();
        $ongoingJobs = MasterFile::where('status', 'ongoing')->count();
        $pendingJobs = MasterFile::where('status', 'pending')->count();

        // Get confirmation links data grouped by year
        $grouped = MasterFile::whereNotNull('date')
            ->orderBy('date', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->date)->format('Y');
            });

        // Get recent jobs (assuming you have a Job model)
        $recentJobs = collect(); // Empty collection as fallback
        if (class_exists('\App\Models\Job')) {
            $recentJobs = \App\Models\Job::orderBy('created_at', 'desc')->take(5)->get();
        }

        // 🔧 UPDATED: Monthly Ongoing data grouped by product_category with fallback
        $hasPC = Schema::hasColumn('master_files', 'product_category');
        $monthlyByCategory = MasterFile::orderBy('date')
            ->get()
            ->map(function ($item) use ($hasPC) {
                // Auto-fill missing category based on product name
                if ($hasPC && !$item->product_category) {
                    if (str_contains(strtolower($item->product), 'fb') || str_contains(strtolower($item->product), 'ig')) {
                        $item->product_category = 'Media';
                    } elseif (str_contains(strtolower($item->product), 'kltg')) {
                        $item->product_category = 'KLTG';
                    } else {
                        $item->product_category = 'Outdoor';
                    }
                } elseif (!$hasPC) {
                    // If no product_category column, create it dynamically
                    $product = strtolower((string)$item->product);
                    if (str_contains($product, 'kltg')) {
                        $item->product_category = 'KLTG';
                    } elseif (str_contains($product, 'fb') || str_contains($product, 'ig') || str_contains($product, 'media')) {
                        $item->product_category = 'Media';
                    } else {
                        $item->product_category = 'Outdoor';
                    }
                }
                return $item;
            })
            ->groupBy('product_category');

        // 🔧 NEW: Get media ongoing jobs data
        if (Schema::hasColumn('master_files', 'product_category')) {
            $mediaOngoingJobs = MasterFile::where('product_category', 'Media')->get();
        } else {
            // Fallback: infer "Media" by product keywords
            $mediaOngoingJobs = MasterFile::where(function ($q) {
                $q->whereRaw('LOWER(product) LIKE ?', ['%media%'])
                    ->orWhereRaw('LOWER(product) LIKE ?', ['%fb%'])
                    ->orWhereRaw('LOWER(product) LIKE ?', ['%ig%'])
                    ->orWhereIn('product', ['FB IG Ad', 'Facebook', 'Instagram']);
            })->get();
        }

        return view('dashboard', compact(
            'masterFiles',
            'totalJobs',
            'completedJobs',
            'ongoingJobs',
            'pendingJobs',
            'grouped',
            'recentJobs',
            'monthlyByCategory', // 🔧 UPDATED: Pass grouped data instead of flat data
            'mediaOngoingJobs' // 🔧 NEW: Pass media ongoing jobs data
        ));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $mf = MasterFile::findOrFail($id);

        // Validate only fields you actually allow to be edited from the form
        $data = $request->validate([
            'company'          => ['sometimes', 'string', 'max:255'],
            'client'           => ['sometimes', 'nullable', 'string', 'max:255'],
            'product'          => ['sometimes', 'nullable', 'string', 'max:255'],
            'product_category' => ['sometimes', 'nullable', 'string', 'max:50'],
            'month'            => ['sometimes', 'nullable', 'string', 'max:20'], // if stored as text; use integer if tinyint
            'date'             => ['sometimes', 'nullable', 'date'],
            'date_finish'      => ['sometimes', 'nullable', 'date'],
            'contact_number'   => 'nullable|string|max:20',
            'email'            => 'nullable|email|max:255',
            'duration'         => ['sometimes', 'nullable', 'string', 'max:255'],
            'status'           => ['sometimes', 'nullable', 'string', 'max:255'],
            'traffic'          => ['sometimes', 'nullable', 'string', 'max:255'],
            'job_number'       => ['sometimes', 'nullable', 'string', 'max:255'],
            'artwork'          => ['sometimes', 'nullable', 'string', 'max:255'],
            'invoice_date'     => ['sometimes', 'nullable', 'date'],
            'invoice_number'   => ['sometimes', 'nullable', 'string', 'max:255'],
            'location'         => ['sometimes', 'nullable', 'string', 'max:255'],
            'remarks'          => ['sometimes', 'nullable', 'string'],
            'sales_person'      => ['nullable', 'string', 'max:255'],

            // KLTG-only (all optional)
            'kltg_industry'      => ['nullable', 'string', 'max:255'],
            'kltg_x'             => ['nullable', 'string', 'max:255'],
            'kltg_edition'       => ['nullable', 'string', 'max:255'],
            'kltg_material_cbp'  => ['nullable', 'string', 'max:255'],
            'kltg_print'         => ['nullable', 'string', 'max:255'],
            'kltg_article'       => ['nullable', 'string', 'max:255'],
            'kltg_video'         => ['nullable', 'string', 'max:255'],
            'kltg_leaderboard'   => ['nullable', 'string', 'max:255'],
            'kltg_qr_code'       => ['nullable', 'string', 'max:255'],
            'kltg_blog'          => ['nullable', 'string', 'max:255'],
            'kltg_em'            => ['nullable', 'string', 'max:255'],
            'kltg_remarks'       => ['nullable', 'string', 'max:255'],

            // Outdoor-only
            'outdoor_size'             => ['nullable', 'string', 'max:255'],
            'outdoor_district_council' => ['nullable', 'string', 'max:255'],
            'outdoor_coordinates'      => ['nullable', 'string', 'max:255'],
        ]);

        // If you have custom parsing (e.g., month names), do it here before save.

        $mf->fill($data)->save();

        return back()->with('success', 'Master File updated successfully.');
    }

    // In MasterFil eController.php
    public function showMatrix($id)
    {
        // Fetch the MasterFile data
        $masterFile = MasterFile::findOrFail($id);

        // Pass the data to a view (create a matrix view or adjust the one you have)
        return view('masterfile.matrix', compact('masterFile'));
    }

    public function show($id)
    {
        $file = MasterFile::findOrFail($id);
        return view('masterfile.show', compact('file'));
    }

  public function create()
{
    // 1) Detect company-name column
    $col = Schema::hasColumn('client_companies', 'company') ? 'company'
        : (Schema::hasColumn('client_companies', 'company_name') ? 'company_name'
        : (Schema::hasColumn('client_companies', 'name') ? 'name' : null));

    // Fallback if no suitable column
    if (!$col) {
        return view('masterfile.create', [
            'companies'         => collect(),   // []
            'clientsByCompany'  => [],          // {}
            'display_column'    => 'company',   // not used but safe
        ]);
    }

    // 2) Companies as: [ id => "Company Name" ]
    $companies = DB::table('client_companies')
        ->select('id', DB::raw("$col as label"))
        ->whereNotNull($col)
        ->where($col, '!=', '')
        ->orderBy($col)
        ->get()
        ->map(function ($r) {
            $r->label = trim($r->label);
            return $r;
        })
        ->filter(fn($r) => $r->label !== '')
        ->unique(fn($r) => mb_strtolower($r->label))
        ->mapWithKeys(fn($r) => [$r->id => $r->label]);

    // For mapping company name -> id (case-insensitive)
    $nameToId = [];
    foreach ($companies as $id => $label) {
        $nameToId[mb_strtolower($label)] = (string) $id;
    }

    // 3) PIC from `clients` table → { "company_id": [ {id,name,phone,email}, ... ] }
    $clientsRaw = DB::table('clients')
        ->select('id', 'name', 'company_id', 'phone', 'email')   // ⬅️ added phone & email
        ->whereNotNull('company_id')
        ->orderBy('name')
        ->get();

    $byCompany = [];
    foreach ($clientsRaw as $c) {
        $cid = (string) $c->company_id;
        if (!isset($byCompany[$cid])) $byCompany[$cid] = [];
        if ($c->name && trim($c->name) !== '') {
            $byCompany[$cid][] = [
                'id'    => (string)$c->id,
                'name'  => trim($c->name),
                'phone' => $c->phone ?? '',     // ⬅️ include phone
                'email' => $c->email ?? '',     // ⬅️ include email
            ];
        }
    }

    // 4) Merge historic PIC from `master_files` (distinct client per company name)
    //    Only for companies that exist in dropdown
    if (Schema::hasTable('master_files')
        && Schema::hasColumn('master_files', 'company')
        && Schema::hasColumn('master_files', 'client')) {

        $mf = DB::table('master_files')
            ->select('company', 'client')
            ->whereNotNull('company')
            ->whereNotNull('client')
            ->get();

        // Group by company name (case-insensitive)
        $grouped = [];
        foreach ($mf as $row) {
            $companyName = trim((string)$row->company);
            $clientName  = trim((string)$row->client);
            if ($companyName === '' || $clientName === '') continue;
            $key = mb_strtolower($companyName);
            if (!isset($grouped[$key])) $grouped[$key] = [];
            $grouped[$key][$clientName] = true; // unique by client name
        }

        foreach ($grouped as $companyLower => $clientSet) {
            // Map company name to existing ID
            $cid = $nameToId[$companyLower] ?? null;
            if (!$cid) continue;
            if (!isset($byCompany[$cid])) $byCompany[$cid] = [];

            // Existing names (lowercased) to avoid duplicates
            $existingLower = array_map(
                fn($r) => mb_strtolower($r['name']),
                $byCompany[$cid]
            );

            foreach (array_keys($clientSet) as $nm) {
                if (!in_array(mb_strtolower($nm), $existingLower, true)) {
                    // Mark as "new" so store() can create Client if selected
                    // Historic records don't have phone/email, so leave empty
                    $byCompany[$cid][] = [
                        'id'    => 'new:' . $nm,
                        'name'  => $nm,
                        'phone' => '',          // ⬅️ empty for historic
                        'email' => '',          // ⬅️ empty for historic
                    ];
                }
            }
        }
    }

    return view('masterfile.create', [
        'companies'         => $companies,        // [id => label]
        'clientsByCompany'  => $byCompany,        // { "id": [ {id,name,phone,email}, ... ] }
        'display_column'    => $col,              // if you still reference it somewhere
    ]);
}

private function pickColumn(string $table, array $candidates): ?string
{
    foreach ($candidates as $c) {
        if (Schema::hasColumn($table, $c)) return $c;
    }
    return null;
}

public function getCompanyContacts(Request $request)
{
    $company = trim((string) $request->query('company', ''));
    $nameCol  = $this->pickColumn('client_companies', ['name','company','company_name']);
    $phoneCol = $this->pickColumn('client_companies', ['phone','contact','phone_number']);

    if (!$company || !$nameCol || !$phoneCol) {
        return response()->json([]);
    }

    $rows = DB::table('client_companies')
        ->whereRaw("LOWER($nameCol) = ?", [mb_strtolower($company)])
        ->orWhere($nameCol, 'LIKE', $company.'%')
        ->pluck($phoneCol)
        ->map(fn($v) => trim((string) $v))
        ->filter()
        ->unique(fn($v) => mb_strtolower($v))
        ->values();

    return response()->json($rows);
}

public function getCompanyPICs(Request $request)
{
    $company = trim((string) $request->query('company', ''));
    $nameCol = $this->pickColumn('client_companies', ['name','company','company_name']);
    $picCols = collect(['pic','person_in_charge','contact_person','contact_name','pic_name'])
        ->filter(fn($c) => Schema::hasColumn('client_companies', $c));

    if (!$company || !$nameCol || $picCols->isEmpty()) {
        return response()->json([]);
    }

    $names = collect();
    foreach ($picCols as $col) {
        $names = $names->merge(
            DB::table('client_companies')
                ->whereRaw("LOWER($nameCol) = ?", [mb_strtolower($company)])
                ->orWhere($nameCol, 'LIKE', $company.'%')
                ->pluck($col)
        );
    }

    $names = $names
        ->map(fn($v) => trim((string) $v))
        ->filter()
        ->unique(fn($v) => mb_strtolower($v))
        ->values();

    return response()->json($names);
}

public function getCompanyEmails(Request $request)
{
    $company = trim((string) $request->query('company', ''));
    $nameCol = $this->pickColumn('client_companies', ['name','company','company_name']);
    $emailCols = collect(['email','pic_email','contact_email','email_address'])
        ->filter(fn($c) => Schema::hasColumn('client_companies', $c));

    if (!$company || !$nameCol || $emailCols->isEmpty()) {
        return response()->json([]);
    }

    $emails = collect();
    foreach ($emailCols as $col) {
        $emails = $emails->merge(
            DB::table('client_companies')
                ->whereRaw("LOWER($nameCol) = ?", [mb_strtolower($company)])
                ->orWhere($nameCol, 'LIKE', $company.'%')
                ->pluck($col)
        );
    }

    $emails = $emails
        ->map(fn($v) => trim((string) $v))
        ->filter()
        ->unique(fn($v) => mb_strtolower($v))
        ->values();

    return response()->json($emails);
}


public function getOutdoorStates(Request $request)
{
    $q = trim((string)$request->query('q', ''));

    $rows = \DB::table('states')
        ->select('id', 'name')
        ->when($q !== '', function ($w) use ($q) {
            $w->where('name', 'LIKE', "%{$q}%");
        })
        ->orderBy('name')
        ->get();

    $stateAbbr = [
        'Kuala Lumpur' => 'KL',
        'Selangor' => 'SEL',
        'Negeri Sembilan' => 'N9',
        'Melaka' => 'MLK',
        'Johor' => 'JHR',
        'Perak' => 'PRK',
        'Pahang' => 'PHG',
        'Terengganu' => 'TRG',
        'Kelantan' => 'KTN',
        'Perlis' => 'PLS',
        'Kedah' => 'KDH',
        'Penang' => 'PNG',
        'Pulau Pinang' => 'PNG',
        'Sarawak' => 'SWK',
        'Sabah' => 'SBH',
        'Labuan' => 'LBN',
        'Putrajaya' => 'PJY',
    ];

    $items = $rows->map(function ($r) use ($stateAbbr) {
        $abbr = $stateAbbr[$r->name] ?? strtoupper(substr($r->name, 0, 3));
        return [
            'value' => (string)$r->id,
            'label' => $abbr . ' - ' . $r->name,
        ];
    })->values();

    return response()->json($items);
}


public function getOutdoorDistricts(Request $request)
{
    $q = trim((string)$request->query('q', ''));
    $stateId = $request->query('state_id'); // optional filter

    $rows = \DB::table('districts')
        ->select('districts.id', 'districts.name', 'districts.state_id')
        ->when($stateId !== null && $stateId !== '', function ($w) use ($stateId) {
            $w->where('districts.state_id', (int)$stateId);
        })
        ->when($q !== '', function ($w) use ($q) {
            $w->where('districts.name', 'LIKE', "%{$q}%");
        })
        ->orderBy('districts.name')
        ->get();

    $items = $rows->map(function ($r) {
        return [
            'value' => (string)$r->id,
            'label' => $r->name,
            'state_id' => (string)$r->state_id,
        ];
    })->values();

    return response()->json($items);
}

public function getOutdoorLocations(Request $request)
{
    $q = trim((string)$request->query('q', ''));
    $districtId = $request->query('district_id');

    // CRITICAL: If no district_id, return nothing (force user to select district first)
    if (!$districtId) {
        return response()->json([
            ['label' => 'Please select a district first', 'value' => '', 'disabled' => true]
        ]);
    }

    $rows = \DB::table('locations')
        ->select('locations.id', 'locations.name', 'locations.district_id')
        ->where('locations.district_id', (int)$districtId)
        ->when($q !== '', function ($w) use ($q) {
            $w->where('locations.name', 'LIKE', "%{$q}%");
        })
        ->orderBy('locations.name')
        ->get();

    if ($rows->isEmpty()) {
        return response()->json([
            ['label' => 'No locations found for this district', 'value' => '', 'disabled' => true]
        ]);
    }

    $items = $rows->map(function ($r) {
        return [
            'value' => (string)$r->id,
            'label' => $r->name,
            'district_id' => (string)$r->district_id,
        ];
    })->values();

    return response()->json($items);
}

public function getOutdoorSites(Request $request)
{
    $q = Billboard::query()
        ->select(
            'billboards.id',
            'billboards.site_number',
            'billboards.size',
            'billboards.gps_latitude',
            'billboards.gps_longitude',
            'locations.id as location_id',
            'locations.name as location_name',
            'districts.id as district_id',
            'states.id as state_id'
        )
        ->leftJoin('locations', 'billboards.location_id', '=', 'locations.id')
        ->leftJoin('districts', 'locations.district_id', '=', 'districts.id')
        ->leftJoin('states', 'districts.state_id', '=', 'states.id');

    // ---- Filter by area_key "stateId|districtId" (utama)
    if ($request->filled('area_key')) {
        [$sid, $did] = array_pad(explode('|', $request->get('area_key'), 2), 2, null);
        if ($sid && $did) {
            $q->where('states.id', $sid)->where('districts.id', $did);
        }
    }

    // ---- Optional cascade filters (fallback / kombinasi)
    if ($request->filled('state_id')) {
        $q->where('states.id', $request->integer('state_id'));
    }
    if ($request->filled('district_id')) {
        $q->where('districts.id', $request->integer('district_id'));
    }
    if ($request->filled('location_id')) {
        $q->where('locations.id', $request->integer('location_id'));
    }

    // ---- Pencarian bebas (site number / location name)
    if ($request->filled('search')) {
        $term = trim($request->get('search'));
        $q->where(function($w) use ($term) {
            $w->where('billboards.site_number', 'like', "%{$term}%")
              ->orWhere('locations.name', 'like', "%{$term}%");
        });
    }

    // ---- Build "STATE_ABBR - District" as area label
    $abbr = "
        CASE
            WHEN states.name = 'Kuala Lumpur'    THEN 'KL'
            WHEN states.name = 'Selangor'        THEN 'SEL'
            WHEN states.name = 'Negeri Sembilan' THEN 'N9'
            WHEN states.name = 'Melaka'          THEN 'MLK'
            WHEN states.name = 'Johor'           THEN 'JHR'
            WHEN states.name = 'Perak'           THEN 'PRK'
            WHEN states.name = 'Pahang'          THEN 'PHG'
            WHEN states.name = 'Terengganu'      THEN 'TRG'
            WHEN states.name = 'Kelantan'        THEN 'KTN'
            WHEN states.name = 'Perlis'          THEN 'PLS'
            WHEN states.name = 'Kedah'           THEN 'KDH'
            WHEN states.name = 'Penang'          THEN 'PNG'
            WHEN states.name = 'Sarawak'         THEN 'SWK'
            WHEN states.name = 'Sabah'           THEN 'SBH'
            WHEN states.name = 'Labuan'          THEN 'LBN'
            WHEN states.name = 'Putrajaya'       THEN 'PJY'
            ELSE states.name
        END
    ";

    $q->addSelect(DB::raw("CONCAT($abbr, ' - ', districts.name) as area"));
    $q->addSelect(DB::raw("CONCAT(states.id, '|', districts.id) as area_key"));
    $q->addSelect(DB::raw("CONCAT(billboards.gps_latitude, ', ', billboards.gps_longitude) as coords"));

    $rows = $q->orderBy('billboards.site_number')->limit(50)->get();

    $data = $rows->map(function ($r) {
        return [
            'value'          => $r->id,
            'label'          => "{$r->site_number} — {$r->location_name}", // clean
            'site_number'    => $r->site_number,
            'location_name'  => $r->location_name,
            'size'           => $r->size,
            'coords'         => $r->coords,
            'area'           => $r->area,       // "KL - Bukit Jalil"
            'area_key'       => $r->area_key,   // "stateId|districtId"
            'state_id'       => $r->state_id,
            'district_id'    => $r->district_id,
        ];
    });

    return response()->json($data);
}


/**
 * @deprecated Use getOutdoorDistricts() + getOutdoorLocations() + getOutdoorSites() instead
 */
public function getOutdoorAreas(Request $request)
{
    $search = trim((string) $request->query('search', ''));

    $abbr = "
        CASE
            WHEN states.name = 'Kuala Lumpur'    THEN 'KL'
            WHEN states.name = 'Selangor'        THEN 'SEL'
            WHEN states.name = 'Negeri Sembilan' THEN 'N9'
            WHEN states.name = 'Melaka'          THEN 'MLK'
            WHEN states.name = 'Johor'           THEN 'JHR'
            WHEN states.name = 'Perak'           THEN 'PRK'
            WHEN states.name = 'Pahang'          THEN 'PHG'
            WHEN states.name = 'Terengganu'      THEN 'TRG'
            WHEN states.name = 'Kelantan'        THEN 'KTN'
            WHEN states.name = 'Perlis'          THEN 'PLS'
            WHEN states.name = 'Kedah'           THEN 'KDH'
            WHEN states.name = 'Penang'          THEN 'PNG'
            WHEN states.name = 'Sarawak'         THEN 'SWK'
            WHEN states.name = 'Sabah'           THEN 'SBH'
            WHEN states.name = 'Labuan'          THEN 'LBN'
            WHEN states.name = 'Putrajaya'       THEN 'PJY'
            ELSE states.name
        END
    ";

    $base = DB::table('districts')
        ->join('states', 'districts.state_id', '=', 'states.id')
        ->select(
            'states.id as state_id',
            'districts.id as district_id',
            DB::raw("CONCAT($abbr, ' - ', districts.name) as area"),
            DB::raw("CONCAT(states.id, '|', districts.id) as area_key")
        );

    if ($search !== '') {
        $base->where(function($w) use ($search, $abbr) {
            $w->where('districts.name', 'like', "%{$search}%")
              ->orWhere(DB::raw($abbr), 'like', "%{$search}%");
        });
    }

    $rows = $base
        ->orderBy('area')
        ->limit(100)
        ->get()
        ->unique('area_key')
        ->values();

    $data = $rows->map(function($r){
        return [
            'value'      => $r->area_key, // "stateId|districtId"
            'label'      => $r->area,     // "KL - Bukit Jalil"
            'state_id'   => $r->state_id,
            'district_id'=> $r->district_id,
        ];
    });

    return response()->json($data);
}


/**
 * @deprecated Use getOutdoorSites() which returns coords directly
 */
public function getOutdoorCoords(Request $request)
{
    $q = trim((string)$request->query('q', ''));

    $rows = \DB::table('billboards')
        ->select(\DB::raw("DISTINCT CONCAT(gps_latitude, ',', gps_longitude) as coords"))
        ->when($q !== '', function ($w) use ($q) {
            $w->where(\DB::raw("CONCAT(gps_latitude, ',', gps_longitude)"), 'LIKE', "%{$q}%");
        })
        ->limit(50)
        ->get();

    return response()->json(
        $rows->pluck('coords')
             ->filter()
             ->unique()
             ->values()
             ->map(fn($v) => ['label' => $v, 'value' => $v])
    );
}

protected function firstExistingColumn(string $table, array $candidates): ?string
{
    foreach ($candidates as $col) {
        if (Schema::hasColumn($table, $col)) return $col;
    }
    return null;
}

    // 🔧 FIXED: Single store method with AUTO-SEED KLTG DISABLED
   public function store(Request $request)
{
    // ---- 0) Normalise "company" & "client" (accept id / new: / plain text) BEFORE validate ----
    // Read raw inputs (support either *_id or plain fields)
    $companyRaw = $request->input('company_id', $request->input('company'));
    $clientRaw  = $request->input('client_id',  $request->input('client'));

    logger('request: ' . $request);

    // Helpers to fetch display column safely
    $getCompanyName = function ($companyModel) {
        return $companyModel->company
            ?? $companyModel->company_name
            ?? $companyModel->name
            ?? (string) $companyModel->id;
    };

    // Resolve / create Company
    $companyId   = null;
    $companyName = null;
    if (is_string($companyRaw) && str_starts_with($companyRaw, 'new:')) {
        $companyName = trim(substr($companyRaw, 4));
        $companyModel = \App\Models\ClientCompany::create([
            'company' => $companyName,
        ]);
        $companyId   = $companyModel->id;
        $companyName = $getCompanyName($companyModel);
    } elseif (is_string($companyRaw) && ctype_digit($companyRaw)) {
        $companyModel = \App\Models\ClientCompany::find($companyRaw);
        if ($companyModel) {
            $companyId   = $companyModel->id;
            $companyName = $getCompanyName($companyModel);
        } else {
            $companyName = $companyRaw;
        }
    } else {
        $typed = trim((string)$companyRaw);
        if ($typed !== '') {
            $companyModel = \App\Models\ClientCompany::where('company', $typed)
                ->orWhere('company_name', $typed)
                ->orWhere('name', $typed)
                ->first();
            if (!$companyModel) {
                $companyModel = \App\Models\ClientCompany::create([
                    'company' => $typed,
                ]);
            }
            $companyId   = $companyModel->id;
            $companyName = $getCompanyName($companyModel);
        }
    }

    // Resolve / create Client (PIC) attached to companyId when possible
    $clientId   = null;
    $clientName = null;
    $clientModel = null;
    if (is_string($clientRaw) && str_starts_with($clientRaw, 'new:')) {
        $clientName = trim(substr($clientRaw, 4));
        // Get contact/email from request first (in case user typed them)
        $autoContact = $request->input('contact_number');
        $autoEmail   = $request->input('email');

        $clientModel = \App\Models\Client::create([
            'name'       => $clientName,
            'company_id' => $companyId,
            'phone'      => $autoContact,   // ✅ save if provided
            'email'      => $autoEmail,     // ✅ save if provided
        ]);
        $clientId   = $clientModel->id;
        $clientName = $clientModel->name;
    } elseif (is_string($clientRaw) && ctype_digit($clientRaw)) {
        $clientModel = \App\Models\Client::find($clientRaw);
        if ($clientModel) {
            $clientId   = $clientModel->id;
            $clientName = $clientModel->name;

            // 🔴 IMPORTANT: inherit company from client if not already set
            if (!$companyId && $clientModel->company_id) {
                $companyId = $clientModel->company_id;
                if ($companyId) {
                    $cc = \App\Models\ClientCompany::find($companyId);
                    if ($cc) {
                        $companyName = $getCompanyName($cc);
                    }
                }
            }
        }
    } else {
        $typed = trim((string)$clientRaw);
        if ($typed !== '') {
            $q = \App\Models\Client::query()->where('name', $typed);
            if ($companyId) $q->where('company_id', $companyId);
            $clientModel = $q->first();

            if (!$clientModel) {
                // Get contact/email from request for new client
                $autoContact = $request->input('contact_number');
                $autoEmail   = $request->input('email');

                $clientModel = \App\Models\Client::create([
                    'name'       => $typed,
                    'company_id' => $companyId,
                    'phone'      => $autoContact,   // ✅ save if provided
                    'email'      => $autoEmail,     // ✅ save if provided
                ]);
            }
            $clientId   = $clientModel->id;
            $clientName = $clientModel->name;

            // Inherit company from existing client
            if (!$companyId && $clientModel->company_id) {
                $companyId = $clientModel->company_id;
                if ($companyId) {
                    $cc = \App\Models\ClientCompany::find($companyId);
                    if ($cc) {
                        $companyName = $getCompanyName($cc);
                    }
                }
            }
        }
    }

    // Auto-fill contact & email from client model if not provided in request
    $autoContact = $request->filled('contact_number')
        ? $request->input('contact_number')
        : optional($clientModel)->phone;     // ✅ safe even if $clientModel is null

    $autoEmail   = $request->filled('email')
        ? $request->input('email')
        : optional($clientModel)->email;     // ✅ safe even if $clientModel is null

    // Merge resolved IDs and names back to request
    $request->merge([
        'company_id'     => $companyId,      // ✅ NEW: save company ID
        'client_id'      => $clientId,       // ✅ NEW: save client ID
        'company'        => $companyName ?? (string)$companyRaw,
        'client'         => $clientName  ?? (string)$clientRaw,
        'contact_number' => $autoContact,
        'email'          => $autoEmail,
    ]);

    // ---- 1) VALIDASI ----
    $data = $request->validate([
        'month' => ['required', 'string', 'max:255'],
        'date' => ['required', 'date'],
        'company' => ['required', 'string', 'max:255'],
        'company_id' => ['nullable', 'integer', 'exists:client_companies,id'],  // ✅ NEW
        'client_id' => ['nullable', 'integer', 'exists:clients,id'],            // ✅ NEW
        'product' => ['required', 'string', 'max:255'],
        'product_category' => ['nullable', 'string', 'max:255'],
        'location' => ['nullable', 'string', 'max:255'],
        'traffic' => ['required', 'string', 'max:255'],
        'duration' => ['nullable', 'string', 'max:255'],
        'amount' => ['nullable', 'numeric', 'between:0,999999999.99'],
        'status' => ['required', 'string', 'max:255'],
        'remarks' => ['nullable', 'string'],
        'client' => ['required', 'string', 'max:255'],
        'sales_person' => ['nullable', 'string', 'max:255'],
        'date_finish' => ['nullable', 'date'],
        'job_number' => ['nullable', 'string', 'max:255'],
        'artwork' => ['nullable', 'string', 'max:255'],
        'invoice_date' => ['nullable', 'date'],
        'invoice_number' => ['nullable', 'string', 'max:255'],
        'contact_number' => ['nullable', 'string', 'max:255'],
        'email' => ['nullable', 'email', 'max:255'],

        'kltg_industry' => ['nullable', 'string', 'max:255'],
        'kltg_x' => ['nullable', 'string', 'max:255'],
        'kltg_edition' => ['nullable', 'string', 'max:255'],
        'kltg_material_cbp' => ['nullable', 'string', 'max:255'],
        'kltg_print' => ['nullable', 'string', 'max:255'],
        'kltg_article' => ['nullable', 'string', 'max:255'],
        'kltg_video' => ['nullable', 'string', 'max:255'],
        'kltg_leaderboard' => ['nullable', 'string', 'max:255'],
        'kltg_qr_code' => ['nullable', 'string', 'max:255'],
        'kltg_blog' => ['nullable', 'string', 'max:255'],
        'kltg_em' => ['nullable', 'string', 'max:255'],
        'kltg_remarks' => ['nullable', 'string', 'max:255'],

        'outdoor_size' => ['nullable', 'string', 'max:255'],
        'outdoor_district_council' => ['nullable', 'string', 'max:255'],
        'outdoor_coordinates' => ['nullable', 'string', 'max:255'],
        'outdoor_status' => ['nullable', 'string', 'max:255'],

        'bulk_placements' => ['nullable', 'string'],
    ]);

    // ---- 2) TRANSACTION + OUTDOOR LOGIC ----
    DB::transaction(function () use ($request, $data) {
        /** @var \App\Models\MasterFile $masterFile */
        $masterFile = MasterFile::create($data);

        $isOutdoor = ($data['product_category'] ?? '') === 'Outdoor';

        // ===== REPEATER MODE with Billboard Integration =====
        if ($isOutdoor && $request->has('locations')) {
            $locations = $request->input('locations', []);

            foreach ($locations as $loc) {
                $billboardId = isset($loc['billboard_id']) && is_numeric($loc['billboard_id'])
                    ? (int) $loc['billboard_id'] : null;

                $typedSite = trim($loc['site'] ?? '');

                // Skip empty rows
                if (!$billboardId && $typedSite === '') {
                    continue;
                }

                // Map UI keys -> DB columns
                $subProduct = $loc['sub_product'] ?? ($data['product'] ?? 'Outdoor');
                $size       = $loc['size'] ?? null;
                $area       = $loc['council'] ?? null;   // UI 'council' -> DB 'district_council'
                $coords     = $loc['coords'] ?? null;    // UI 'coords'  -> DB 'coordinates'
                $remarks    = $loc['remarks'] ?? null;
                $startDate  = $loc['start_date'] ?? null;
                $endDate    = $loc['end_date'] ?? null;
                $outdoorStatus    = $loc['outdoor_status'] ?? null;
                $siteLabel  = $typedSite ?: null;

                // Hydrate from billboard if ID is present
                if ($billboardId) {
                    $bb = \DB::table('billboards as b')
                        ->leftJoin('locations as l', 'l.id', '=', 'b.location_id')
                        ->where('b.id', $billboardId)
                        ->first(['b.site_number', 'b.size', 'b.gps_latitude', 'b.gps_longitude', 'l.name as area_name']);

                    if ($bb) {
                        $siteLabel = $siteLabel ?: ($bb->site_number ?? null);
                        $size      = $size      ?: ($bb->size ?? null);
                        $area      = $area      ?: ($bb->area_name ?? null);
                        if (!$coords && $bb->gps_latitude !== null && $bb->gps_longitude !== null) {
                            $coords = $bb->gps_latitude . ',' . $bb->gps_longitude;
                        }
                    }
                }

                // Insert outdoor item
                $masterFile->outdoorItems()->create([
                    'sub_product'      => $subProduct,
                    'qty'              => 1, // or ($loc['qty'] ?? 1)
                    'site'             => $siteLabel,
                    'size'             => $size,
                    'district_council' => $area,
                    'coordinates'      => $coords,
                    'remarks'          => $remarks,
                    'start_date'       => $startDate ?: null,
                    'end_date'         => $endDate   ?: null,
                    'status'           => $outdoorStatus,
                    'billboard_id'     => $billboardId,
                ]);
            }

            return; // Done with repeater mode
        }

        // ===== FALLBACK: Old textarea mode (bulk_placements) =====
        $raw = trim((string)$request->input('bulk_placements', ''));
        if ($isOutdoor && $raw !== '') {
            $lines = preg_split("/\r\n|\n|\r/", $raw);
            $defaultSub = $data['product'] ?? 'Outdoor';

            $items = [];
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '') continue;

                $sep   = str_contains($line, '|') ? '|' : ',';
                $parts = array_map('trim', str_getcsv($line, $sep));

                $site        = $parts[0] ?? null;
                $size        = $parts[1] ?? null;
                $council     = $parts[2] ?? null;
                $coordinates = $parts[3] ?? null;
                $remarks     = $parts[4] ?? null;

                $sub = $defaultSub;

                if ($site && preg_match('/^(BB|TB|Bunting|Flyers|Star|Signages|Newspaper)\s*:\s*(.+)$/i', $site, $m)) {
                    $sub  = $m[1];
                    $site = $m[2];
                }

                if (
                    $coordinates && $remarks
                    && preg_match('/^-?\d+(\.\d+)?$/', $coordinates)
                    && preg_match('/^-?\d+(\.\d+)?$/', $remarks)
                ) {
                    $coordinates = $coordinates . ',' . $remarks;
                    $remarks = null;
                }

                $items[] = [
                    'sub_product'      => $sub,
                    'qty'              => 1,
                    'site'             => $site,
                    'size'             => $size ?: null,
                    'district_council' => $council ?: null,
                    'coordinates'      => $coordinates ?: null,
                    'remarks'          => $remarks ?: null,
                ];
            }

            if (!empty($items)) {
                $masterFile->outdoorItems()->createMany($items);
            }
        }
    });

    return redirect()->route('dashboard')->with('success', 'Saved.');
}


    private function guessCategoryFromProduct(string $product): string
    {
        $p = strtolower($product);
        if (str_contains($p, 'kltg')) return 'KLTG';
        if (str_contains($p, 'fb') || str_contains($p, 'ig') || str_contains($p, 'ad') || str_contains($p, 'media')) {
            return 'Media';
        }
        return 'Outdoor';
    }
    public function upsertKltgMonthly(Request $request, $id)
    {
        $data = $request->validate([
            'month'  => 'required|in:jan,feb,mar,apr,may,jun,jul,aug,sep,oct,nov,dec',
            'type'   => 'required|in:kltg,video,article,lb,em',
            'status' => 'nullable|string'   // <-- ini penting, sesuai kolom
        ]);

        KltgMonthlyDetail::updateOrCreate(
            ['master_file_id' => $id, 'month' => $data['month'], 'type' => $data['type']],
            ['status' => $data['status']]
        );

        return response()->json(['success' => true]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xls,xlsx,csv']
        ]);

        $stats = MasterFileImport::run($request->file('file'));

        return back()->with('status', "Import done. Created: {$stats['created']}, Updated: {$stats['updated']}, Skipped: {$stats['skipped']}.");
    }





    public function printAuto(MasterFile $file)
    {
        $type = request('type') ?: $this->guessProductType($file);

        // Ambil items outdoor dengan kolom yang dibutuhkan (termasuk tanggal)
        if ($type === 'outdoor') {
            $file->load(['outdoorItems' => function ($q) {
                $q->orderBy('id')
                    ->select([
                        'id',
                        'master_file_id',
                        'site',
                        'size',
                        'start_date',
                        'end_date',   // <-- penting untuk IN CHARGE DATE
                        // optional kalau dipakai di PDF:
                        // 'district_council','coordinates','remarks','qty'
                    ]);
            }]);
            $items = $file->outdoorItems; // sudah berupa collection of models (tanggal = Carbon)
        } else {
            $items = collect();
        }

        $data = [
            'file'         => $file,
            'items'        => $items,
            'date'         => $file->date ? Carbon::parse($file->date)->format('d/m/Y') : '',
            'date_finish'  => $file->date_finish ? Carbon::parse($file->date_finish)->format('d/m/Y') : '',
            'invoice_date' => $file->invoice_date ? Carbon::parse($file->invoice_date)->format('d/m/Y') : '',
        ];

        $views = [
            'kltg'    => 'prints.kltg_job_order',
            'outdoor' => 'prints.outdoor_job_order',
            'media'   => 'prints.media_job_order',
        ];

        return Pdf::loadView($views[$type] ?? $views['kltg'], $data)
            ->setPaper('a4', 'portrait')
            ->download(strtoupper($type) . '_JobOrder_' . ($file->company ?? 'NA') . '.pdf');
    }


    private function guessProductType(MasterFile $file): string
    {
        // Gather all possible fields you might be using
        $candidates = [
            (string)($file->product_category ?? ''), // e.g. BB, TB, Bunting, KLTG listing
            (string)($file->category_product ?? ''),
            (string)($file->category ?? ''),
            (string)($file->product ?? ''),          // e.g. BB (see your screenshot)
        ];

        $hay = strtolower(trim(implode(' ', $candidates)));

        // --- HARD EQUALITY MAPS (most reliable) ---
        $equalsOutdoor = ['bb', 'tb', 'np', 'bunting', 'flyers', 'star', 'signages', 'signage', 'newspaper'];
        if (
            in_array(strtolower(trim($file->product ?? '')), $equalsOutdoor, true) ||
            in_array(strtolower(trim($file->product_category ?? '')), $equalsOutdoor, true)
        ) {
            return 'outdoor';
        }

        $equalsKltg = ['kltg', 'kltg listing', 'kltg quarter page'];
        if (
            in_array(strtolower(trim($file->product ?? '')), $equalsKltg, true) ||
            in_array(strtolower(trim($file->product_category ?? '')), $equalsKltg, true)
        ) {
            return 'kltg';
        }

        // --- CONTAINS-BASED (fallbacks) ---
        $outdoorKeys = [
            'outdoor',
            'tempboard',
            'tb',
            'billboard',
            'bb',
            'newspaper',
            'bunting',
            'flyers',
            'star',
            'signages',
            'signage'
        ];
        foreach ($outdoorKeys as $k) {
            if (str_contains($hay, $k)) return 'outdoor';
        }

        $mediaKeys = [
            'social media',
            'tiktok',
            'youtube',
            'fb',
            'ig',
            'facebook',
            'instagram',
            'giveaways',
            'contest',
            'xiaohongshu',
            'ads',
            'ad',
            'management',
            'boost'
        ];
        foreach ($mediaKeys as $k) {
            if (str_contains($hay, $k)) return 'media';
        }

        $kltgKeys = ['kltg', 'kl the guide', 'guide', 'listing', 'quarter page'];
        foreach ($kltgKeys as $k) {
            if (str_contains($hay, $k)) return 'kltg';
        }

        return 'kltg'; // final fallback
    }


    public function exportXlsx(Request $request): StreamedResponse
    {
        // ----- Build select list safely -----
        $select = [
            'created_at',
            // add sales_person (safe fallback if missing)
        ];
        if (Schema::hasColumn('master_files', 'sales_person')) {
            $select[] = 'sales_person';
        } else {
            $select[] = DB::raw('NULL as sales_person');
        }

        $select = array_merge($select, [
            'company',
            'client',                 // Person In Charge
            'product',
            'month',
            'date as start_date',
            'date_finish as end_date',
            'duration',
            'status',
            'job_number',
            'artwork',
            'traffic',
            'invoice_date',
            'invoice_number',
            'product_category',
        ]);

        // Add email/amount/contact_number safely
        if (Schema::hasColumn('master_files', 'email')) {
            $select[] = 'email';
        } else {
            $select[] = DB::raw('NULL as email');
        }
        if (Schema::hasColumn('master_files', 'amount')) {
            $select[] = 'amount';
        } else {
            $select[] = DB::raw('NULL as amount');
        }
        if (Schema::hasColumn('master_files', 'contact_number')) {
            $select[] = 'contact_number';
        } else {
            $select[] = DB::raw('NULL as contact_number');
        }

        $q = MasterFile::query()->select($select);

        // ... (filters remain the same) ...

        $rows = $q->orderByDesc('created_at')->cursor();

        // ----- Spreadsheet -----
        $ss = new Spreadsheet();
        $sheet = $ss->getActiveSheet();

        $headings = [
            'Date Created',
            'Sales Person',
            'Company Name',
            'Person In Charge',
            'Email',
            'Amount',
            'Contact Number',
            'Product',
            'Month',
            'Start Date',
            'End Date',
            'Remarks',
            'Status',
            'Job',
            'Artwork',
            'Traffic',
            'Invoice Date',
            'Invoice Number',
        ];
        $lastColLetter = Coordinate::stringFromColumnIndex(count($headings));

        // Title row
        $title = 'MASTER_PROPOSAL_CONFIRMATION_' . now()->format('Y-m-d');
        $sheet->setCellValue('A1', $title);
        $sheet->mergeCells("A1:{$lastColLetter}1");
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'FFFF00'],
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(24);

        // Headings (row 2)
        $col = 1;
        foreach ($headings as $h) {
            $letter = Coordinate::stringFromColumnIndex($col++);
            $sheet->setCellValue($letter . '2', $h);
        }
        $sheet->getStyle("A2:{$lastColLetter}2")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'EEEEEE'],
            ],
        ]);

        // Data rows
        $r = 3;
        foreach ($rows as $row) {
            $c = 1;
            $put = function ($value) use (&$c, $r, $sheet) {
                $letter = Coordinate::stringFromColumnIndex($c++);
                $sheet->setCellValue($letter . $r, $value);
            };
            $fmtDate = function ($v) {
                if (!$v) return null;
                if ($v instanceof \DateTimeInterface) return $v->format('Y-m-d');
                try {
                    return Carbon::parse($v)->format('Y-m-d');
                } catch (\Throwable $e) {
                    return (string)$v;
                }
            };

            // Order matches $headings
            $put((string) $row->created_at);
            $put($row->sales_person ?? '');          // Sales Person
            $put($row->company);
            $put($row->client);
            $put($row->email ?? '');
            $put($row->amount ?? '');
            $put($row->contact_number ?? '');
            $put($row->product);
            $put($row->month);
            $put($fmtDate($row->start_date));
            $put($fmtDate($row->end_date));
            $put($row->duration);
            $put($row->status);
            $put($row->job_number);
            $put($row->artwork);
            $put($row->traffic);
            $put($fmtDate($row->invoice_date));
            $put($row->invoice_number);
            $r++;
        }

        // Borders + autosize + freeze
        $lastDataRow = $r - 1;
        $sheet->getStyle("A2:{$lastColLetter}{$lastDataRow}")
            ->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ]);
        foreach (range(1, count($headings)) as $colIndex) {
            $letter = Coordinate::stringFromColumnIndex($colIndex);
            $sheet->getColumnDimension($letter)->setAutoSize(true);
        }
        $sheet->freezePane('A3');

        $filename = 'MASTER_PROPOSAL CONFIRMATION_' . now()->format('Ymd_His') . '.xlsx';
        $writer = new Xlsx($ss);

        return response()->streamDownload(function () use ($writer, $ss) {
            $writer->save('php://output');
            $ss->disconnectWorksheets();
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }


    public function exportKltgXlsx(Request $request): StreamedResponse
    {
        // ===== Helpers =====
        $cols = Schema::getColumnListing('master_files');
        $has  = fn($c) => in_array($c, $cols, true);

        $normalizeMonth = function ($raw) {
            if ($raw === null || $raw === '') return [];
            $m = trim((string)$raw);
            $map = [
                'jan' => 1,
                'january' => 1,
                'feb' => 2,
                'february' => 2,
                'mar' => 3,
                'march' => 3,
                'apr' => 4,
                'april' => 4,
                'may' => 5,
                'jun' => 6,
                'june' => 6,
                'jul' => 7,
                'july' => 7,
                'aug' => 8,
                'august' => 8,
                'sep' => 9,
                'september' => 9,
                'oct' => 10,
                'october' => 10,
                'nov' => 11,
                'november' => 11,
                'dec' => 12,
                'december' => 12,
            ];
            if (ctype_digit($m)) {
                $n = max(1, min(12, (int)$m));
            } else {
                $key = strtolower($m);
                if (!isset($map[$key])) return [];
                $n = $map[$key];
            }
            return [(string)$n, str_pad((string)$n, 2, '0', STR_PAD_LEFT), $m];
        };

        // ===== Column spec (ONLY these columns, in this exact order) =====
        // ===== Column spec (ONLY these columns, in this exact order) =====
        $colKeys = [
            // 1..20 (strict order)
            'created_at',        // Date Created
            'month',             // Month
            'company',           // Company Name
            'date',              // Start Date
            'date_finish',       // End Date
            'barter',            // Barter
            'product',           // Product
            'product_category',  // Category
            'kltg_industry',     // Industry
            'kltg_x',            // KLTG X
            'kltg_edition',      // Edition
            'kltg_material_cbp', // Material C/BP
            'kltg_print',        // Print
            'kltg_article',      // Article
            'kltg_video',        // Video
            'kltg_leaderboard',  // Leaderboard
            'kltg_qr_code',      // QR Code
            'kltg_blog',         // Blog
            'kltg_em',           // EM
            'kltg_remarks',      // Remarks
        ];

        // ===== Headings shown in the sheet (must match your request exactly) =====
        $labels = [
            'created_at'        => 'Date Created',
            'month'             => 'Month',
            'company'           => 'Company Name',
            'date'              => 'Start Date',
            'date_finish'       => 'End Date',
            'barter'            => 'Barter',
            'product'           => 'Product',
            'product_category'  => 'Category',
            'kltg_industry'     => 'Industry',
            'kltg_x'            => 'KLTG X',
            'kltg_edition'      => 'Edition',
            'kltg_material_cbp' => 'Material C/BP',
            'kltg_print'        => 'Print',
            'kltg_article'      => 'Article',
            'kltg_video'        => 'Video',
            'kltg_leaderboard'  => 'Leaderboard',
            'kltg_qr_code'      => 'QR Code',
            'kltg_blog'         => 'Blog',
            'kltg_em'           => 'EM',
            'kltg_remarks'      => 'Remarks',
        ];


        // ===== Build SELECT strictly for the chosen columns =====
        $select = [];
        foreach ($colKeys as $k) {
            $select[] = $has($k) ? $k : DB::raw("NULL as {$k}");
        }

        // ===== Base Query (scope KLTG/The Guide + has any KLTG fields) =====
        $q = MasterFile::query()->select($select)
            ->where(function ($w) {
                $w->whereRaw('LOWER(product_category) LIKE ?', ['%kltg%'])
                    ->orWhereRaw('LOWER(product_category) LIKE ?', ['%the guide%'])
                    ->orWhereRaw('LOWER(product) LIKE ?', ['%kltg%'])
                    ->orWhereRaw('LOWER(product) LIKE ?', ['%the guide%'])
                    ->orWhereNotNull('kltg_industry')
                    ->orWhereNotNull('kltg_x')
                    ->orWhereNotNull('kltg_edition')
                    ->orWhereNotNull('kltg_material_cbp')
                    ->orWhereNotNull('kltg_print')
                    ->orWhereNotNull('kltg_article')
                    ->orWhereNotNull('kltg_video')
                    ->orWhereNotNull('kltg_leaderboard')
                    ->orWhereNotNull('kltg_qr_code')
                    ->orWhereNotNull('kltg_blog')
                    ->orWhereNotNull('kltg_em')
                    ->orWhereNotNull('kltg_remarks');
            });

        // ===== Filters (compatible with your current ones) =====
        if ($term = trim((string)$request->get('search', ''))) {
            $q->where(function ($w) use ($term) {
                $w->where('company', 'like', "%{$term}%")
                    ->orWhere('client', 'like', "%{$term}%")
                    ->orWhere('product', 'like', "%{$term}%")
                    ->orWhere('status', 'like', "%{$term}%")
                    ->orWhere('traffic', 'like', "%{$term}%")
                    ->orWhere('invoice_number', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('contact_number', 'like', "%{$term}%");
            });
        }
        if (($status = $request->get('status')) !== null && $status !== '') {
            $q->where('status', $status);
        }
        if (($pc = $request->get('product_category')) !== null && $pc !== '') {
            $q->whereRaw('LOWER(product_category) = ?', [strtolower(trim($pc))]);
        }
        $monthOptions = $normalizeMonth($request->get('month'));
        if (!empty($monthOptions)) {
            $q->whereIn('month', $monthOptions);
        }
        $allowedDateFields = ['created_at', 'updated_at', 'date', 'date_finish', 'invoice_date'];
        $field = $request->get('date_field', 'created_at');
        if (!in_array($field, $allowedDateFields, true)) $field = 'created_at';
        if (($from = $request->get('date_from')) !== null && $from !== '') $q->whereDate($field, '>=', $from);
        if (($to   = $request->get('date_to'))   !== null && $to   !== '') $q->whereDate($field, '<=', $to);

        $rows = $q->orderByDesc('created_at')->cursor();

        // ===== Spreadsheet =====
        $ss = new Spreadsheet();
        $sheet = $ss->getActiveSheet();

        // Title row 1
        $headings = array_map(fn($k) => $labels[$k] ?? ucfirst(str_replace('_', ' ', $k)), $colKeys);
        $lastColLetter = Coordinate::stringFromColumnIndex(count($headings));
        $sheet->setCellValue('A1', 'KLTG_MASTER_CLIENTELE_' . now()->format('Y-m-d'));
        $sheet->mergeCells("A1:{$lastColLetter}1");
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFF00']],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);
        $sheet->getRowDimension(1)->setRowHeight(24);

        // Headings row 2
        foreach ($headings as $i => $h) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($i + 1) . '2', $h);
        }
        $sheet->getStyle("A2:{$lastColLetter}2")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'EEEEEE']],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Data rows from row 3
        $r = 3;

        // Date formatter => n/j/y (e.g., 9/8/25)
        $fmtDate = function ($v) {
            if ($v === null || $v === '') return '';
            if ($v instanceof \DateTimeInterface) return $v->format('n/j/y');
            try {
                return Carbon::parse($v)->format('n/j/y');
            } catch (\Throwable $e) {
                return (string)$v;
            }
        };

        $dataStartRow = $r;
        foreach ($rows as $row) {
            $out = [];
            foreach ($colKeys as $k) {
                $val = $row->{$k} ?? null;
                if (in_array($k, ['created_at', 'date', 'date_finish'], true)) {
                    $val = $fmtDate($val);
                }
                $out[] = $val;
            }
            foreach ($out as $i => $val) {
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($i + 1) . $r, $val);
            }
            $r++;
        }

        // Apply borders to all data cells if there are data rows
        if ($r > $dataStartRow) {
            $dataEndRow = $r - 1;
            $sheet->getStyle("A{$dataStartRow}:{$lastColLetter}{$dataEndRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ]);
        }

        // Autosize + freeze
        for ($i = 1; $i <= count($headings); $i++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
        }
        $sheet->freezePane('A3');

        $writer = new Xlsx($ss);
        $filename = 'KLTG_MASTER_CLIENTELE' . now()->format('Ymd_His') . '.xlsx';
        return response()->streamDownload(function () use ($writer, $ss) {
            $writer->save('php://output');
            $ss->disconnectWorksheets();
        }, $filename, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }


    public function exportOutdoorXlsx(Request $request): StreamedResponse
    {
        // === Query: master_files JOIN outdoor_items (1 baris = 1 site) ===
        $q = DB::table('master_files as mf')
            ->join('outdoor_items as oi', 'oi.master_file_id', '=', 'mf.id') // pakai leftJoin kalau mau tetap include yang tanpa item
            ->select([
                'mf.created_at',
                'mf.month',
                'mf.company',
                'mf.product',
                DB::raw('oi.site as location'),
                DB::raw('oi.district_council as outdoor_district_council'),
                'mf.duration',
                'mf.date',
                'mf.date_finish',
                DB::raw('oi.size as outdoor_size'),
                DB::raw('oi.coordinates as outdoor_coordinates'),
                'mf.remarks', // ★ NEW
            ])
            ->where(function ($w) {
                $w->whereRaw('LOWER(mf.product_category) LIKE ?', ['%outdoor%'])
                    ->orWhereRaw('LOWER(mf.product) LIKE ?', ['%outdoor%'])
                    ->orWhereRaw('LOWER(mf.product) LIKE ?', ['%billboard%']);
            });

        // --- (opsional) samakan filter dengan halaman ---
        if ($term = trim((string)$request->get('search', ''))) {
            $q->where(function ($w) use ($term) {
                $w->where('mf.company', 'like', "%{$term}%")
                    ->orWhere('mf.product', 'like', "%{$term}%")
                    ->orWhere('oi.site', 'like', "%{$term}%");
            });
        }
        if ($m = $request->get('month')) $q->where('mf.month', $m);
        if ($from = $request->get('date_from')) $q->whereDate('mf.created_at', '>=', $from);
        if ($to   = $request->get('date_to'))   $q->whereDate('mf.created_at', '<=', $to);

        $rows = $q->orderByDesc('mf.created_at')->cursor();

        // === Kolom & heading (remarks di paling ujung) ===
        $colKeys = [
            'created_at',
            'month',
            'company',
            'product',
            'location',
            'outdoor_district_council',
            'duration',
            'date',
            'date_finish',
            'outdoor_size',
            'outdoor_coordinates',
            'remarks', // ★ NEW
        ];

        $labels = [
            'created_at'               => 'CREATED AT',
            'month'                    => 'MONTH',
            'company'                  => 'COMPANY',
            'product'                  => 'PRODUCT',
            'location'                 => 'LOCATION',
            'outdoor_district_council' => 'AREA',
            'duration'                 => 'DURATION',
            'date'                     => 'DATE',
            'date_finish'              => 'DATE FINISH',
            'outdoor_size'             => 'OUTDOOR SIZE',
            'outdoor_coordinates'      => 'OUTDOOR COORDINATES',
            'remarks'                  => 'REMARKS', // ★ NEW
        ];

        // === Spreadsheet ===
        $ss = new Spreadsheet();
        $sheet = $ss->getActiveSheet();

        $headings = array_map(fn($k) => $labels[$k], $colKeys);
        $lastCol = Coordinate::stringFromColumnIndex(count($headings));

        // Title
        $sheet->setCellValue('A1', 'OUTDOOR_EXPORT_' . now()->format('Y-m-d'));
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFF00']],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Headings
        foreach ($headings as $i => $h) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($i + 1) . '2', $h);
        }
        $sheet->getStyle("A2:{$lastCol}2")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'EEEEEE']],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Data
        $fmt = function ($v) {
            if ($v === null || $v === '') return '';
            if ($v instanceof \DateTimeInterface) return $v->format('n/j/y');
            try {
                return Carbon::parse($v)->format('n/j/y');
            } catch (\Throwable $e) {
                return (string)$v;
            }
        };

        $r = 3;
        $dataStartRow = $r;

        foreach ($rows as $row) {
            $data = [
                $fmt($row->created_at),
                $row->month,
                $row->company,
                $row->product,
                $row->location,
                $row->outdoor_district_council,
                $row->duration,
                $fmt($row->date),
                $fmt($row->date_finish),
                $row->outdoor_size,
                $row->outdoor_coordinates,
                $row->remarks ?? '', // ★ NEW
            ];

            foreach ($data as $i => $val) {
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($i + 1) . $r, $val);
            }
            $r++;
        }

        // Borders
        if ($r > $dataStartRow) {
            $dataEndRow = $r - 1;
            $sheet->getStyle("A{$dataStartRow}:{$lastCol}{$dataEndRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ]);
        }

        // Autosize + freeze; wrap text for remarks
        for ($i = 1; $i <= count($headings); $i++) {
            $col = Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        // make remarks wrap (last column)
        $sheet->getStyle($lastCol . '3:' . $lastCol . $r)->getAlignment()->setWrapText(true);

        $sheet->freezePane('A3');

        $writer = new Xlsx($ss);
        $filename = 'OUTDOOR_MASTER_CLIENTELE' . now()->format('Ymd_His') . '.xlsx';
        return response()->streamDownload(function () use ($writer, $ss) {
            $writer->save('php://output');
            $ss->disconnectWorksheets();
        }, $filename, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }



    // 🔧 UPDATED: Export method for Monthly Ongoing Job section with product_category fallback
    public function downloadTemplate()
    {
        $csvFileName = 'master_file_import_template.csv';
        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'month',
                'date',
                'company',
                'product',
                'traffic',
                'duration',
                'status',
                'client',
                'date_finish',
                'job_number',
                'artwork',
                'invoice_date',
                'invoice_number'
            ]);
            fputcsv($handle, [
                'January',
                '2024-01-15',
                'Sample Company Ltd',
                'HM',
                '1000',
                '30',
                'completed',
                'Sample Client',
                '2024-01-20',
                'JOB001',
                'BGOC',
                '2024-01-25',
                'INV001'
            ]);
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$csvFileName}\"",
            'Cache-Control' => 'no-store, no-cache',
            'Pragma' => 'no-cache'
        ]);
    }

    // Put this near the bottom of the controller (or wherever you keep helpers)
    private function normalizeMonthCandidates($raw): array
    {
        if ($raw === null || $raw === '') return [];

        $m = trim((string)$raw);
        $map = [
            'jan' => 1,
            'january' => 1,
            'feb' => 2,
            'february' => 2,
            'mar' => 3,
            'march' => 3,
            'apr' => 4,
            'april' => 4,
            'may' => 5,
            'jun' => 6,
            'june' => 6,
            'jul' => 7,
            'july' => 7,
            'aug' => 8,
            'august' => 8,
            'sep' => 9,
            'september' => 9,
            'oct' => 10,
            'october' => 10,
            'nov' => 11,
            'november' => 11,
            'dec' => 12,
            'december' => 12,
        ];

        if (ctype_digit($m)) {
            $n = max(1, min(12, (int)$m));
        } else {
            $key = strtolower($m);
            if (!isset($map[$key])) return [];
            $n = $map[$key];
        }

        // DB holds month as varchar; cover common forms:
        return [
            (string)$n,              // "6"
            str_pad((string)$n, 2, '0', STR_PAD_LEFT), // "06"
            // Keep original (just in case your UI already passes "6" or "06")
            $m,
        ];
    }

    public function inlineUpdate(Request $request)
    {
        // Validate core shape
        $data = $request->validate([
            'id'              => ['required', 'integer'],
            'column'          => ['required', 'string', 'max:255'],
            'value'           => ['nullable', 'string'],
            'scope'           => ['nullable', 'in:kltg,outdoor,master'],
            'outdoor_item_id' => ['nullable', 'integer'],
        ]);

        $scope = $data['scope'] ?? 'master';
        $id    = (int) $data['id'];
        $col   = $data['column'];
        $val   = $data['value'] ?? null;

        try {
            if ($scope === 'outdoor' && $request->filled('outdoor_item_id')) {
                // Map "outdoor_*" view columns → DB columns
                $columnMap = [
                    'outdoor_size'             => 'size',
                    'outdoor_district_council' => 'district_council',
                    'outdoor_coordinates'      => 'coordinates',
                ];
                $dbCol = $columnMap[$col] ?? null;
                if (!$dbCol) {
                    return response()->json(['ok' => false, 'message' => "Unknown outdoor column: {$col}"], 422);
                }

                // Update the outdoor_items row that belongs to this master file
                $changed = DB::table('outdoor_items')
                    ->where('master_file_id', $id)
                    ->where('id', (int)$request->outdoor_item_id)
                    ->update([$dbCol => $val, 'updated_at' => now()]);

                return response()->json([
                    'ok'      => true,
                    'changed' => $changed,
                    'message' => $changed ? 'Saved' : 'No row changed',
                ], 200);
            }

            // Default: update master_files (used by KLTG and general fields)
            // Whitelist columns you actually allow inline
            $allowed = [
                // add safe master_files columns here
                'company',
                'product',
                'category',
                'location',
                'status',
                'date',
                'date_finish',
                'start_date',
                'end_date',
                'invoice_date',
                'outdoor_coordinates', // if you actually stored them on master_files
                // …extend as needed
            ];

            if (!in_array($col, $allowed, true)) {
                return response()->json(['ok' => false, 'message' => "Column not allowed: {$col}"], 422);
            }

            // Autocast dates (your Blade already formats YYYY-MM-DD, but keep this safe)
            $dateLike = ['date', 'date_finish', 'start_date', 'end_date', 'invoice_date'];
            if (in_array($col, $dateLike, true) && $val !== null && $val !== '') {
                try {
                    $val = Carbon::parse($val)->format('Y-m-d');
                } catch (\Throwable $e) { /* ignore; store raw */
                }
            }

            $changed = DB::table('master_files')
                ->where('id', $id)
                ->update([$col => $val, 'updated_at' => now()]);

            return response()->json([
                'ok'      => true,
                'changed' => $changed,
                'message' => $changed ? 'Saved' : 'No row changed',
            ], 200);
        } catch (\Throwable $e) {
            Log::error('inlineUpdate error', ['err' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['ok' => false, 'message' => 'Server error'], 500);
        }
    }





    public function confirmationLink()
    {
        $files = MasterFile::orderBy('date', 'desc')->get();
        $grouped = $files->groupBy(fn($item) => Carbon::parse($item->date)->format('Y'));
        return view('confirmation_links.index', ['grouped' => $grouped, 'years' => $grouped->keys()]);
    }

    public function updateRemarksAndLocation(Request $request, $id)
    {
        $request->validate([
            'remarks' => 'nullable|string',
            'location' => 'nullable|string',
        ]);

        $masterFile = MasterFile::findOrFail($id);

        // Update remarks and location
        $masterFile->remarks = $request->remarks;
        $masterFile->location = $request->location;

        // Handle monthly checkboxes including the new KLTG column
        $months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
        $types = ['kltg', 'video', 'article', 'lb', 'em']; // 🔧 NEW: Added 'kltg' type

        foreach ($months as $month) {
            foreach ($types as $type) {
                $checkboxName = "check_{$month}_{$type}";
                // If checkbox is checked, set to 1, otherwise set to 0
                $masterFile->{$checkboxName} = $request->has($checkboxName) ? 1 : 0;
            }
        }

        $masterFile->save();

        return redirect()->route('masterfile.show', $id)->with('success', 'Information updated successfully!');
    }

    public function getMediaOngoingJobs()
    {
        $mediaOngoingJobs = MasterFile::where('category', 'Media')->get(); // Adjust 'category' if needed

        return view('dashboard', compact('mediaOngoingJobs'));
    }

    // 🔧 ADDED from file 2: Monthly Job methods
    public function monthlyJob()
    {
        $files = MasterFile::orderBy('date')->get();
        return view('monthly_jobs.index', compact('files'));
    }

    public function updateMonthlyJob(Request $request, $id)
    {
        $months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'Dec'];
        $validated = $request->only(array_map(fn($m) => "check_$m", $months));
        $validated['remarks'] = $request->remarks;
        MasterFile::where('id', $id)->update($validated);
        return back()->with('success', 'Updated successfully.');
    }

    // 🔧 ADDED from file 2: Stats method
    public function getStats()
    {
        try {
            $stats = [
                'total' => MasterFile::count(),
                'completed' => MasterFile::where('status', 'completed')->count(),
                'ongoing' => MasterFile::where('status', 'ongoing')->count(),
                'pending' => MasterFile::where('status', 'pending')->count(),
                'recent' => MasterFile::orderBy('created_at', 'desc')->limit(5)->get()
            ];
            return response()->json($stats);
        } catch (Exception $e) {
            Log::error('Stats error: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to fetch stats'], 500);
        }
    }

    // 🔧 ADDED from file 2: Timeline update method
    public function updateTimeline(Request $request, $id)
    {
        Log::info('🚀 updateTimeline triggered', ['id' => $id]);
        logger('disini');

        $file = MasterFile::findOrFail($id);

        $data = [];

        foreach (
            [
                'product',
                'site',
                'client',
                'payment',
                'material_received',
                'artwork',
                'approval',
                'sent_to_printer',
                'installation',
                'dismantle'
            ] as $field
        ) {
            $existing = optional($file->timeline)->$field;

            if ($request->has($field) && !$existing) {
                $data[$field] = now();
            } else {
                $data[$field] = $existing;
            }
        }

        $data['remarks'] = $request->remarks;
        $data['next_follow_up'] = $request->next_follow_up;

        $file->timeline()->updateOrCreate([], $data);

        return back()->with('success', 'Timeline updated.');
    }

    // 🔧 ADDED from file 2: KLTG Matrix methods
    public function showKltgMatrix($id)
    {
        $file = MasterFile::with('kltgMatrix')->findOrFail($id);
        $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $types = ['KLTG', 'VIDEO', 'ARTICLE', 'LB', 'EM'];

        $grouped = $file->kltgMatrix->groupBy(fn($item) => $item->month);

        return view('kltg.matrix', compact('file', 'grouped', 'months', 'types'));
    }

    public function editKltgMatrix($id)
    {
        $file = MasterFile::with('kltgMatrix')->findOrFail($id);
        $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $types = ['KLTG', 'VIDEO', 'ARTICLE', 'LB', 'EM'];

        return view('kltg.matrix_edit', compact('file', 'months', 'types'));
    }

    public function updateKltgMatrix(Request $request, $id)
    {
        try {
            Log::info('🟡 updateKltgMatrix triggered', ['id' => $id, 'payload' => $request->all()]);

            $file = MasterFile::findOrFail($id);
            $months = $request->input('months', []);

            // Convert short month labels to full month names
            $monthLabels = [
                'jan' => 'January',
                'feb' => 'February',
                'mar' => 'March',
                'apr' => 'April',
                'may' => 'May',
                'jun' => 'June',
                'jul' => 'July',
                'aug' => 'August',
                'sep' => 'September',
                'oct' => 'October',
                'nov' => 'November',
                'dec' => 'December'
            ];

            foreach ($months as $short => $types) {
                $monthName = $monthLabels[$short] ?? $short;

                foreach ($types as $type => $status) {
                    Log::info('⏳ Saving:', [
                        'master_file_id' => $file->id,
                        'month' => $monthName,
                        'type' => strtoupper($type),
                        'status' => $status,
                    ]);

                    KltgMonthlyDetail::updateOrCreate(
                        [
                            'master_file_id' => $file->id,
                            'month' => $monthName,
                            'type' => strtoupper($type),
                        ],
                        [
                            'client' => $file->company,
                            'status' => $status,
                        ]
                    );
                }
            }

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            Log::error('🔥 KLTG Matrix Save Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Server error'], 500);
        }
    }



    public function destroy($id)
    {
        $file = MasterFile::findOrFail($id);
        $file->delete();

        return redirect()->route('masterfile.index')
            ->with('success', 'Record deleted successfully.');
    }
}
