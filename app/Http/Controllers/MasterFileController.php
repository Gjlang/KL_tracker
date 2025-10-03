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
        return view('masterfile.create');
    }

    // 🔧 FIXED: Single store method with AUTO-SEED KLTG DISABLED
    public function store(Request $request)
    {
        // 1) VALIDASI biasa + bulk_placements
        $data = $request->validate([
            // === field existing kamu ===
            'month' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'company' => ['required', 'string', 'max:255'],
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

            // === field khusus KLTG / Outdoor (kalau kamu pakai) ===
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

            // === textarea bulk ===
            'bulk_placements' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($request, $data) {
            /** @var \App\Models\MasterFile $masterFile */
            $masterFile = MasterFile::create($data);

            $isOutdoor = ($data['product_category'] ?? '') === 'Outdoor';

            // ===== NEW: Repeater mode (locations[...]) =====
            if ($isOutdoor && $request->has('locations')) {
                $locs = collect($request->input('locations', []))
                    ->map(function ($r) use ($data) {
                        // coords: "lat,lng" OR empty
                        $coords = trim((string)($r['coords'] ?? ''));
                        // allow sub_product override per row; fall back to selected product
                        $sub = trim((string)($r['sub_product'] ?? ($data['product'] ?? 'Outdoor')));

                        return [
                            'sub_product'      => Arr::get($r, 'sub_product', $data['product'] ?? 'Outdoor'),
                            'qty'              => 1,
                            'site'             => Arr::get($r, 'site'),
                            'size'             => Arr::get($r, 'size'),
                            'district_council' => Arr::get($r, 'council'),
                            'coordinates'      => Arr::get($r, 'coords'),
                            'remarks'          => Arr::get($r, 'remarks'),
                            'start_date'       => Arr::get($r, 'start_date'),
                            'end_date'         => Arr::get($r, 'end_date'),
                        ];
                    })
                    // keep only rows that at least have a site
                    ->filter(fn($row) => $row['site'] !== '')
                    ->values()
                    ->all();

                if (!empty($locs)) {
                    $masterFile->outdoorItems()->createMany($locs);
                }

                return; // done
            }

            // ===== Old textarea mode (bulk_placements) =====
            $raw = trim((string)$request->input('bulk_placements', ''));
            if ($isOutdoor && $raw !== '') {
                $lines = preg_split("/\r\n|\n|\r/", $raw);
                $defaultSub = $data['product'] ?? 'Outdoor';

                $items = [];
                foreach ($lines as $line) {
                    $line = trim($line);
                    if ($line === '') continue;

                    // prefer "|" else ","
                    $sep   = str_contains($line, '|') ? '|' : ',';
                    $parts = array_map('trim', str_getcsv($line, $sep));

                    $site        = $parts[0] ?? null;
                    $size        = $parts[1] ?? null;
                    $council     = $parts[2] ?? null;
                    $coordinates = $parts[3] ?? null;
                    $remarks     = $parts[4] ?? null;

                    // FIX: reset $sub per line (avoid leaking previous value)
                    $sub = $defaultSub;

                    // Optional prefix: "BB: Site name"
                    if ($site && preg_match('/^(BB|TB|Bunting|Flyers|Star|Signages|Newspaper)\s*:\s*(.+)$/i', $site, $m)) {
                        $sub  = $m[1];
                        $site = $m[2];
                    }

                    // If coords split into two numeric parts (lat / lng) and remarks captured the second, merge them
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
            'date'         => $file->date ? \Carbon\Carbon::parse($file->date)->format('d/m/Y') : '',
            'date_finish'  => $file->date_finish ? \Carbon\Carbon::parse($file->date_finish)->format('d/m/Y') : '',
            'invoice_date' => $file->invoice_date ? \Carbon\Carbon::parse($file->invoice_date)->format('d/m/Y') : '',
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

    public function inlineUpdate(\Illuminate\Http\Request $request)
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
