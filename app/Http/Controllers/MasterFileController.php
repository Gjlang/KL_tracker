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



class MasterFileController extends Controller
{


    private function selectExistingColumns(array $wanted): array
{
    $tableCols = Schema::getColumnListing((new MasterFile)->getTable());
    return array_values(array_unique(array_filter(
        array_merge(['id'], $wanted),
        fn ($c) => in_array($c, $tableCols, true)
    )));
}

       public function kltg(Request $request)
{
    // Kolom yang diminta (sesuai list kamu)
    $columns = [
        'month','date','company','product','product_category','location','traffic','duration','amount',
        'status','remarks','client','date_finish','job_number','artwork','invoice_date','invoice_number',
        'created_at','updated_at','contact_number','email',
        'kltg_industry','kltg_x','kltg_edition','kltg_material_cbp','kltg_print','kltg_article',
        'kltg_video','kltg_leaderboard','kltg_qr_code','kltg_blog','kltg_em','kltg_remarks',
    ];

    // Variasi nama produk untuk KLTG (lowercase semua)
    $kltgSet = array_map('strtolower', [
        'kltg', 'kltg listing', 'kltg quarter page',
    ]);

    $select = $this->selectExistingColumns($columns);

    $rows = MasterFile::query()
        ->select($select)
        ->where(function ($q) use ($kltgSet) {
            // LOWER(product) in set  OR  LOWER(product_category) in set
            $q->whereIn(DB::raw('LOWER(product)'), $kltgSet)
              ->orWhereIn(DB::raw('LOWER(product_category)'), $kltgSet);
        })
        ->latest('created_at')
        ->paginate(25)
        ->appends($request->query());

    return view('dashboard.master.kltg', [
        'rows'    => $rows,
        'columns' => $columns,
    ]);
}

    /** GET /dashboard/master/outdoor */
    public function outdoor(Request $request)
{
    // Kolom yang diminta (sesuai list kamu)
    $columns = [
        'id','month','date','company','product','product_category','location','traffic','duration','amount',
        'status','remarks','client','date_finish','job_number','artwork','invoice_date','invoice_number',
        'created_at','updated_at','contact_number','email',
        'outdoor_size','outdoor_district_council','outdoor_coordinates',
    ];

    // Variasi nama produk untuk Outdoor (lowercase semua) + 'outdoor'
    $outdoorSet = array_map('strtolower', [
        'outdoor', // jika ada yang tersimpan begitu
        'hm','tb','ttm','bb','star','flyers','bunting','signages',
    ]);

    $select = $this->selectExistingColumns($columns);

    $rows = MasterFile::query()
        ->select($select)
        ->where(function ($q) use ($outdoorSet) {
            // LOWER(product) in set  OR  LOWER(product_category) in set
            $q->whereIn(DB::raw('LOWER(product)'), $outdoorSet)
              ->orWhereIn(DB::raw('LOWER(product_category)'), $outdoorSet);
        })
        ->latest('created_at')
        ->paginate(25)
        ->appends($request->query());

    return view('dashboard.master.outdoor', [
        'rows'    => $rows,
        'columns' => $columns,
    ]);
}

    /**
     * Utility: ensure we only select columns that truly exist,
     * always include the primary key so Blade links can work.
     */
    private function buildQueryBits(array $wanted, string $kind): array
    {
        $tableCols = Schema::getColumnListing((new MasterFile)->getTable());

        $select = array_values(array_unique(array_filter(array_merge(['id'], $wanted), function ($c) use ($tableCols) {
            return in_array($c, $tableCols, true);
        })));

        // if a project sometimes stores type in product, sometimes in category
        $filters = [
            'by_product'  => in_array('product', $tableCols, true),
            'by_category' => in_array('product_category', $tableCols, true),
        ];

        return [$select, $filters];
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
                    'KLTG' => ['KLTG', 'KLTG listing', 'KLTG quarter page','NP'],
                    default => []
                });
            } else {
                // fallback: filter lewat product
                $cat = strtolower($request->product_category);
                if ($cat === 'outdoor') {
                    $query->where(function($q) {
                        $q->whereIn('product', ['HM','TB','TTM','BB','Star','Flyers','Bunting','Signages','Newspaper'])
                          ->orWhereRaw('LOWER(product) LIKE ?', ['%outdoor%']);
                    });
                } elseif ($cat === 'kltg') {
                    $query->whereRaw('LOWER(product) LIKE ?', ['%kltg%']);
                } elseif ($cat === 'media') {
                    $query->where(function($q) {
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
                            ->groupBy(function($item) {
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
        'company'          => ['sometimes','string','max:255'],
        'client'           => ['sometimes','nullable','string','max:255'],
        'product'          => ['sometimes','nullable','string','max:255'],
        'product_category' => ['sometimes','nullable','string','max:50'],
        'month'            => ['sometimes','nullable','string','max:20'], // if stored as text; use integer if tinyint
        'date'             => ['sometimes','nullable','date'],
        'date_finish'      => ['sometimes','nullable','date'],
        'contact_number'   => 'nullable|string|max:20',
        'email'            => 'nullable|email|max:255',
        'duration'         => ['sometimes','nullable','string','max:255'],
        'status'           => ['sometimes','nullable','string','max:255'],
        'traffic'          => ['sometimes','nullable','string','max:255'],
        'job_number'       => ['sometimes','nullable','string','max:255'],
        'artwork'          => ['sometimes','nullable','string','max:255'],
        'invoice_date'     => ['sometimes','nullable','date'],
        'invoice_number'   => ['sometimes','nullable','string','max:255'],
        'location'         => ['sometimes','nullable','string','max:255'],
        'remarks'          => ['sometimes','nullable','string'],

        // KLTG-only (all optional)
        'kltg_industry'      => ['nullable','string','max:255'],
        'kltg_x'             => ['nullable','string','max:255'],
        'kltg_edition'       => ['nullable','string','max:255'],
        'kltg_material_cbp'  => ['nullable','string','max:255'],
        'kltg_print'         => ['nullable','string','max:255'],
        'kltg_article'       => ['nullable','string','max:255'],
        'kltg_video'         => ['nullable','string','max:255'],
        'kltg_leaderboard'   => ['nullable','string','max:255'],
        'kltg_qr_code'       => ['nullable','string','max:255'],
        'kltg_blog'          => ['nullable','string','max:255'],
        'kltg_em'            => ['nullable','string','max:255'],
        'kltg_remarks'       => ['nullable','string','max:255'],

        // Outdoor-only
        'outdoor_size'             => ['nullable','string','max:255'],
        'outdoor_district_council' => ['nullable','string','max:255'],
        'outdoor_coordinates'      => ['nullable','string','max:255'],
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
        'month' => ['required','string','max:255'],
        'date' => ['required','date'],
        'company' => ['required','string','max:255'],
        'product' => ['required','string','max:255'],
        'product_category' => ['nullable','string','max:255'],
        'location' => ['nullable','string','max:255'],
        'traffic' => ['required','string','max:255'],
        'duration' => ['nullable','string','max:255'],
        'amount' => ['nullable','numeric','between:0,999999999.99'],
        'status' => ['required','string','max:255'],
        'remarks' => ['nullable','string'],
        'client' => ['required','string','max:255'],
        'sales_person' => ['nullable','string','max:255'],
        'date_finish' => ['nullable','date'],
        'job_number' => ['nullable','string','max:255'],
        'artwork' => ['nullable','string','max:255'],
        'invoice_date' => ['nullable','date'],
        'invoice_number' => ['nullable','string','max:255'],
        'contact_number' => ['nullable','string','max:255'],
        'email' => ['nullable','email','max:255'],

        // === field khusus KLTG / Outdoor (kalau kamu pakai) ===
        'kltg_industry' => ['nullable','string','max:255'],
        'kltg_x' => ['nullable','string','max:255'],
        'kltg_edition' => ['nullable','string','max:255'],
        'kltg_material_cbp' => ['nullable','string','max:255'],
        'kltg_print' => ['nullable','string','max:255'],
        'kltg_article' => ['nullable','string','max:255'],
        'kltg_video' => ['nullable','string','max:255'],
        'kltg_leaderboard' => ['nullable','string','max:255'],
        'kltg_qr_code' => ['nullable','string','max:255'],
        'kltg_blog' => ['nullable','string','max:255'],
        'kltg_em' => ['nullable','string','max:255'],
        'kltg_remarks' => ['nullable','string','max:255'],

        'outdoor_size' => ['nullable','string','max:255'],
        'outdoor_district_council' => ['nullable','string','max:255'],
        'outdoor_coordinates' => ['nullable','string','max:255'],

        // === textarea bulk ===
        'bulk_placements' => ['nullable','string'],
    ]);

    DB::transaction(function() use ($request, $data) {
        // 2) SIMPAN HEADER
        /** @var \App\Models\MasterFile $masterFile */
        $masterFile = \App\Models\MasterFile::create($data);

        // 3) JIKA OUTDOOR & textarea diisi -> parse jadi banyak child
        $isOutdoor = ($data['product_category'] ?? '') === 'Outdoor';
        $raw = trim((string) $request->input('bulk_placements', ''));

        if ($isOutdoor && $raw !== '') {
            $lines = preg_split("/\r\n|\n|\r/", $raw);
            $defaultSub = $data['product'] ?? null; // contoh: BB, TB, Bunting, dll

            $items = [];
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '') continue;

                // pakai | atau koma sebagai separator
                $sep = str_contains($line, '|') ? '|' : ',';          // utamakan '|'
                $parts = array_map('trim', str_getcsv($line, $sep));   // CSV-aware (boleh quoted)

                // mapping dasar
                $site        = $parts[0] ?? null;
                $size        = $parts[1] ?? null;
                $council     = $parts[2] ?? null;
                $coordinates = $parts[3] ?? null;
                $remarks     = $parts[4] ?? null;

                // prefix opsional: "BB: Site name"
                if ($site && preg_match('/^(BB|TB|Bunting|Flyers|Star|Signages|Newspaper)\s*:\s*(.+)$/i', $site, $m)) {
                    $sub  = $m[1];
                    $site = $m[2];
                }

                // AUTO-MERGE kalau lat & long kepotong jadi dua angka berurutan
                if ($coordinates && $remarks
                    && preg_match('/^-?\d+(\.\d+)?$/', $coordinates)
                    && preg_match('/^-?\d+(\.\d+)?$/', $remarks)) {
                    $coordinates = $coordinates . ',' . $remarks;  // gabungkan jadi "lat,long"
                    $remarks = null;
                }

                $items[] = [
                'sub_product'      => $sub ?? ($data['product'] ?? 'Outdoor'),
                'qty'              => 1,
                'site'             => $site,
                'size'             => $size,
                'district_council' => $council,
                'coordinates'      => $coordinates,
                'remarks'          => $remarks,
                ];
            }

            if (!empty($items)) {
                $masterFile->outdoorItems()->createMany($items);
            }
        }
    });

    return redirect()->route('dashboard')->with('success','Saved.');
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
            'file' => ['required','file','mimes:xls,xlsx,csv']
        ]);

        $stats = MasterFileImport::run($request->file('file'));

        return back()->with('status', "Import done. Created: {$stats['created']}, Updated: {$stats['updated']}, Skipped: {$stats['skipped']}.");
    }



public function printAuto(MasterFile $file)
{
    $data = [
        'file'         => $file,
        'date'         => $file->date ? Carbon::parse($file->date)->format('d/m/Y') : '',
        'date_finish'  => $file->date_finish ? Carbon::parse($file->date_finish)->format('d/m/Y') : '',
        'invoice_date' => $file->invoice_date ? Carbon::parse($file->invoice_date)->format('d/m/Y') : '',
    ];

    $type = request('type') ?: $this->guessProductType($file); // fungsi helpermu
    $views = [
        'kltg'    => 'prints.kltg_job_order',
        'outdoor' => 'prints.outdoor_job_order',
        'media'   => 'prints.media_job_order',
    ];

    return Pdf::loadView($views[$type] ?? $views['kltg'], $data)
        ->setPaper('a4', 'portrait')
        ->download(strtoupper($type).'_JobOrder_'.($file->company ?? 'NA').'.pdf');
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
    $equalsOutdoor = ['bb','tb','np','bunting','flyers','star','signages','signage', 'newspaper'];
    if (in_array(strtolower(trim($file->product ?? '')), $equalsOutdoor, true) ||
        in_array(strtolower(trim($file->product_category ?? '')), $equalsOutdoor, true)) {
        return 'outdoor';
    }

    $equalsKltg = ['kltg','kltg listing','kltg quarter page'];
    if (in_array(strtolower(trim($file->product ?? '')), $equalsKltg, true) ||
        in_array(strtolower(trim($file->product_category ?? '')), $equalsKltg, true)) {
        return 'kltg';
    }

    // --- CONTAINS-BASED (fallbacks) ---
    $outdoorKeys = [
        'outdoor','tempboard','tb','billboard','bb','newspaper','bunting','flyers','star','signages','signage'
    ];
    foreach ($outdoorKeys as $k) {
        if (str_contains($hay, $k)) return 'outdoor';
    }

    $mediaKeys = [
        'social media','tiktok','youtube','fb','ig','facebook','instagram',
        'giveaways','contest','xiaohongshu','ads','ad','management','boost'
    ];
    foreach ($mediaKeys as $k) {
        if (str_contains($hay, $k)) return 'media';
    }

    $kltgKeys = ['kltg','kl the guide','guide','listing','quarter page'];
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
        'company',
        'client',                 // we'll DISPLAY this as "Person In Charge"
        'product',
        'month',
        'date as start_date',
        'date_finish as end_date',
        'duration',
        'status',
        'traffic',
        'job_number',
        'artwork',
        'invoice_date',
        'invoice_number',
        'product_category',
    ];

    // Add email/contact_number if columns exist; otherwise select NULL AS ...
    if (Schema::hasColumn('master_files', 'email')) {
        $select[] = 'email';
    } else {
        $select[] = DB::raw('NULL as email');
    }
    if (Schema::hasColumn('master_files', 'contact_number')) {
        $select[] = 'contact_number';
    } else {
        $select[] = DB::raw('NULL as contact_number');
    }

    // ----- Build query with the same filters you use on the dashboard -----
    $q = MasterFile::query()->select($select);

    if ($scope = strtolower((string) $request->query('scope', ''))) {
    $q->where(function ($w) use ($scope) {
        if ($scope === 'kltg' || $scope === 'theguide') {
            $w->whereRaw('LOWER(product_category) LIKE ?', ['%kltg%'])
              ->orWhereRaw('LOWER(product_category) LIKE ?', ['%the guide%']); // jika kamu rename
        } elseif ($scope === 'outdoor') {
            $w->whereRaw('LOWER(product_category) LIKE ?', ['%outdoor%']);
        }
    });
}

    if ($term = trim((string) $request->get('search', ''))) {
        $q->where(function ($w) use ($term) {
            $w->where('company', 'like', "%{$term}%")
              ->orWhere('client', 'like', "%{$term}%")
              ->orWhere('product', 'like', "%{$term}%")
              ->orWhere('status', 'like', "%{$term}%")
              ->orWhere('traffic', 'like', "%{$term}%")
              ->orWhere('invoice_number', 'like', "%{$term}%")
              // also allow search by email/contact if present
              ->orWhere('email', 'like', "%{$term}%")
              ->orWhere('contact_number', 'like', "%{$term}%");
        });
    }
    if ($status = $request->get('status')) {
        $q->where('status', $status);
    }
    if ($pc = $request->get('product_category')) {
        $q->where('product_category', $pc);
    }
    if ($month = $request->get('month')) {
        $q->where('month', $month);
    }
    $field = $request->get('date_field', 'created_at');
    if ($from = $request->get('date_from')) { $q->whereDate($field, '>=', $from); }
    if ($to   = $request->get('date_to'))   { $q->whereDate($field, '<=', $to);   }

     $rows = $q->orderByDesc('created_at')->cursor();

    // ----- Build spreadsheet -----
    $ss = new Spreadsheet();
    $sheet = $ss->getActiveSheet();

    // === NEW: Title row (A1:Q1) ===
    $lastColIndex = 17; // You have 17 headings below
    $lastColLetter = Coordinate::stringFromColumnIndex($lastColIndex);
    $title = 'MASTER_FILES_' . now()->format('Y-m-d');

    $sheet->setCellValue('A1', $title);
    $sheet->mergeCells("A1:{$lastColLetter}1");
    $sheet->getStyle('A1')->applyFromArray([
        'font' => ['bold' => true, 'size' => 14],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'color' => ['rgb' => 'FFFF00'], // Yellow background
        ],
    ]);
    $sheet->getRowDimension(1)->setRowHeight(24); // a bit taller

    // Headings now start at ROW 2 (previously row 1)
    $headings = [
        'Date Created',
        'Company Name',
        'Person In Charge',   // <-- shows "client" value
        'Email',
        'Contact Number',
        'Product',
        'Month',
        'Start Date',
        'End Date',
        'Duration (days)',
        'Status',
        'Traffic',
        'Job Number',
        'Artwork',
        'Invoice Date',
        'Invoice Number',
        'Product Category',
    ];

    // (Optional) style headings light gray and bold
    $col = 1;
    foreach ($headings as $h) {
        $letter = Coordinate::stringFromColumnIndex($col++);
        $sheet->setCellValue($letter.'2', $h);   // << row 2 now
    }
    $sheet->getStyle("A2:{$lastColLetter}2")->applyFromArray([
        'font' => ['bold' => true],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'color' => ['rgb' => 'EEEEEE'],
        ],
    ]);

    // Data now starts at ROW 3 (previously row 2)
    $r = 3;
    foreach ($rows as $row) {
        $c = 1;
        $put = function ($value) use (&$c, $r, $sheet) {
            $letter = Coordinate::stringFromColumnIndex($c++);
            $sheet->setCellValue($letter.$r, $value);
        };
        $fmtDate = function ($v) {
            if (!$v) return null;
            if ($v instanceof \DateTimeInterface) return $v->format('Y-m-d');
            try { return Carbon::parse($v)->format('Y-m-d'); } catch (\Throwable $e) { return (string)$v; }
        };

        $put((string) $row->created_at);
        $put($row->company);
        $put($row->client);
        $put($row->email ?? '');
        $put($row->contact_number ?? '');
        $put($row->product);
        $put($row->month);
        $put($fmtDate($row->start_date));
        $put($fmtDate($row->end_date));
        $put($row->duration);
        $put($row->status);
        $put($row->traffic);
        $put($row->job_number);
        $put($row->artwork);
        $put($fmtDate($row->invoice_date));
        $put($row->invoice_number);
        $put($row->product_category);
        $r++;
    }

    foreach (range(1, count($headings)) as $colIndex) {
        $letter = Coordinate::stringFromColumnIndex($colIndex);
        $sheet->getColumnDimension($letter)->setAutoSize(true);
    }

    // Optional: freeze header (so title + headings stay visible while scrolling)
    $sheet->freezePane('A3');

    // Optional: uppercase file name prefix
    $filename = 'MASTER_FILES_'.now()->format('Ymd_His').'.xlsx';
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
    // Helpers
    $cols = Schema::getColumnListing('master_files');
    $has  = fn($c) => in_array($c, $cols, true);
    $normalizeMonth = function($raw) {
        if ($raw === null || $raw === '') return [];
        $m = trim((string)$raw);
        $map = [
            'jan'=>1,'january'=>1,'feb'=>2,'february'=>2,'mar'=>3,'march'=>3,
            'apr'=>4,'april'=>4,'may'=>5,'jun'=>6,'june'=>6,'jul'=>7,'july'=>7,
            'aug'=>8,'august'=>8,'sep'=>9,'september'=>9,'oct'=>10,'october'=>10,
            'nov'=>11,'november'=>11,'dec'=>12,'december'=>12,
        ];
        if (ctype_digit($m)) { $n = max(1, min(12, (int)$m)); }
        else { $key = strtolower($m); if (!isset($map[$key])) return []; $n = $map[$key]; }
        return [(string)$n, str_pad((string)$n, 2, '0', STR_PAD_LEFT), $m];
    };

    // Select (urutan sesuai heading)
    $select = [
        'month',
        'date as date',
        'company',
        'product',
        'product_category',
        'location',
        'traffic',
        'duration',
        $has('amount') ? 'amount' : DB::raw('NULL as amount'),
        'status',
        'remarks',
        'client',
        'date_finish',
        'job_number',
        'artwork',
        'invoice_date',
        'invoice_number',
        'created_at',
        'updated_at',
        $has('contact_number') ? 'contact_number' : DB::raw('NULL as contact_number'),
        $has('email') ? 'email' : DB::raw('NULL as email'),
        $has('kltg_industry')     ? 'kltg_industry'     : DB::raw('NULL as kltg_industry'),
        $has('kltg_x')            ? 'kltg_x'            : DB::raw('NULL as kltg_x'),
        $has('kltg_edition')      ? 'kltg_edition'      : DB::raw('NULL as kltg_edition'),
        $has('kltg_material_cbp') ? 'kltg_material_cbp' : DB::raw('NULL as kltg_material_cbp'),
        $has('kltg_print')        ? 'kltg_print'        : DB::raw('NULL as kltg_print'),
        $has('kltg_article')      ? 'kltg_article'      : DB::raw('NULL as kltg_article'),
        $has('kltg_video')        ? 'kltg_video'        : DB::raw('NULL as kltg_video'),
        $has('kltg_leaderboard')  ? 'kltg_leaderboard'  : DB::raw('NULL as kltg_leaderboard'),
        $has('kltg_qr_code')      ? 'kltg_qr_code'      : DB::raw('NULL as kltg_qr_code'),
        $has('kltg_blog')         ? 'kltg_blog'         : DB::raw('NULL as kltg_blog'),
        $has('kltg_em')           ? 'kltg_em'           : DB::raw('NULL as kltg_em'),
        $has('kltg_remarks')      ? 'kltg_remarks'      : DB::raw('NULL as kltg_remarks'),
    ];

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

    // Filters
    $hasEmail = $has('email');
    $hasPhone = $has('contact_number');

    if ($term = trim((string)$request->get('search', ''))) {
        $q->where(function ($w) use ($term, $hasEmail, $hasPhone) {
            $w->where('company', 'like', "%{$term}%")
              ->orWhere('client', 'like', "%{$term}%")
              ->orWhere('product', 'like', "%{$term}%")
              ->orWhere('status', 'like', "%{$term}%")
              ->orWhere('traffic', 'like', "%{$term}%")
              ->orWhere('invoice_number', 'like', "%{$term}%");
            if ($hasEmail) $w->orWhere('email', 'like', "%{$term}%");
            if ($hasPhone) $w->orWhere('contact_number', 'like', "%{$term}%");
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
    $allowedDateFields = ['created_at','updated_at','date','date_finish','invoice_date'];
    $field = $request->get('date_field', 'created_at');
    if (!in_array($field, $allowedDateFields, true)) $field = 'created_at';
    if (($from = $request->get('date_from')) !== null && $from !== '') $q->whereDate($field, '>=', $from);
    if (($to   = $request->get('date_to'))   !== null && $to   !== '') $q->whereDate($field, '<=', $to);

    $rows = $q->orderByDesc('created_at')->cursor();

    // Spreadsheet
    $ss = new Spreadsheet();
    $sheet = $ss->getActiveSheet();

    $headings = [
        'Month','Date','Company','Product','Product Category','Location','Traffic','Duration','Amount','Status',
        'Remarks','Client','Date Finish','Job Number','Artwork','Invoice Date','Invoice Number',
        'Created At','Updated At','Contact Number','Email',
        'Kltg Industry','Kltg X','Kltg Edition','Kltg Material Cbp','Kltg Print','Kltg Article',
        'Kltg Video','Kltg Leaderboard','Kltg Qr Code','Kltg Blog','Kltg Em','Kltg Remarks',
    ];

    $lastColLetter = Coordinate::stringFromColumnIndex(count($headings));
    $sheet->setCellValue('A1', 'KLTG_EXPORT_'.now()->format('Y-m-d'));
    $sheet->mergeCells("A1:{$lastColLetter}1");
    $sheet->getStyle('A1')->applyFromArray([
        'font' => ['bold' => true, 'size' => 14],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFF00']],
    ]);
    $sheet->getRowDimension(1)->setRowHeight(24);

    // Headings row 2
    foreach ($headings as $i => $h) {
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($i + 1).'2', $h);
    }
    $sheet->getStyle("A2:{$lastColLetter}2")->applyFromArray([
        'font' => ['bold' => true],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'EEEEEE']],
    ]);

    // Data rows from row 3
    $r = 3;
    $fmt = function($v) {
        if (empty($v)) return '';
        if ($v instanceof \DateTimeInterface) return $v->format('Y-m-d');
        try { return Carbon::parse($v)->format('Y-m-d'); } catch (\Throwable $e) { return (string)$v; }
    };

    foreach ($rows as $row) {
        $values = [
            $row->month,
            $fmt($row->date ?? null),
            $row->company,
            $row->product,
            $row->product_category,
            $row->location,
            $row->traffic,
            $row->duration,
            $row->amount,
            $row->status,
            $row->remarks,
            $row->client,
            $fmt($row->date_finish ?? null),
            $row->job_number,
            $row->artwork,
            $fmt($row->invoice_date ?? null),
            $row->invoice_number,
            (string)$row->created_at,
            (string)$row->updated_at,
            $row->contact_number,
            $row->email,
            $row->kltg_industry,
            $row->kltg_x,
            $row->kltg_edition,
            $row->kltg_material_cbp,
            $row->kltg_print,
            $row->kltg_article,
            $row->kltg_video,
            $row->kltg_leaderboard,
            $row->kltg_qr_code,
            $row->kltg_blog,
            $row->kltg_em,
            $row->kltg_remarks,
        ];

        foreach ($values as $i => $val) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($i + 1).$r, $val);
        }
        $r++;
    }

    // Autosize + freeze
    for ($i=1; $i<=count($headings); $i++) {
        $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
    }
    $sheet->freezePane('A3');

    $writer = new Xlsx($ss);
    $filename = 'KLTG_EXPORT_'.now()->format('Ymd_His').'.xlsx';
    return response()->streamDownload(function () use ($writer, $ss) {
        $writer->save('php://output');
        $ss->disconnectWorksheets();
    }, $filename, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
}


public function exportOutdoorXlsx(Request $request): StreamedResponse
{
    $cols = Schema::getColumnListing('master_files');
    $has  = fn($c) => in_array($c, $cols, true);
    $normalizeMonth = function($raw) {
        if ($raw === null || $raw === '') return [];
        $m = trim((string)$raw);
        $map = [
            'jan'=>1,'january'=>1,'feb'=>2,'february'=>2,'mar'=>3,'march'=>3,
            'apr'=>4,'april'=>4,'may'=>5,'jun'=>6,'june'=>6,'jul'=>7,'july'=>7,
            'aug'=>8,'august'=>8,'sep'=>9,'september'=>9,'oct'=>10,'october'=>10,
            'nov'=>11,'november'=>11,'dec'=>12,'december'=>12,
        ];
        if (ctype_digit($m)) { $n = max(1, min(12, (int)$m)); }
        else { $key = strtolower($m); if (!isset($map[$key])) return []; $n = $map[$key]; }
        return [(string)$n, str_pad((string)$n, 2, '0', STR_PAD_LEFT), $m];
    };

    $select = [
        'id',
        'month',
        'date as date',
        'company',
        'product',
        'product_category',
        'location',
        'traffic',
        'duration',
        $has('amount') ? 'amount' : DB::raw('NULL as amount'),
        'status',
        'remarks',
        'client',
        'date_finish',
        'job_number',
        'artwork',
        'invoice_date',
        'invoice_number',
        'created_at',
        'updated_at',
        $has('contact_number') ? 'contact_number' : DB::raw('NULL as contact_number'),
        $has('email') ? 'email' : DB::raw('NULL as email'),
        $has('outdoor_size')             ? 'outdoor_size'             : DB::raw('NULL as outdoor_size'),
        $has('outdoor_district_council') ? 'outdoor_district_council' : DB::raw('NULL as outdoor_district_council'),
        $has('outdoor_coordinates')      ? 'outdoor_coordinates'      : DB::raw('NULL as outdoor_coordinates'),
    ];

    $q = MasterFile::query()->select($select)
        ->where(function ($w) {
            $w->whereRaw('LOWER(product_category) LIKE ?', ['%outdoor%'])
              ->orWhereRaw('LOWER(product) LIKE ?', ['%outdoor%'])
              ->orWhereRaw('LOWER(product) LIKE ?', ['%billboard%'])
              ->orWhereNotNull('outdoor_size')
              ->orWhereNotNull('outdoor_district_council')
              ->orWhereNotNull('outdoor_coordinates');
        });

    // Filters
    $hasEmail = $has('email');
    $hasPhone = $has('contact_number');

    if ($term = trim((string)$request->get('search', ''))) {
        $q->where(function ($w) use ($term, $hasEmail, $hasPhone) {
            $w->where('company', 'like', "%{$term}%")
              ->orWhere('client', 'like', "%{$term}%")
              ->orWhere('product', 'like', "%{$term}%")
              ->orWhere('status', 'like', "%{$term}%")
              ->orWhere('traffic', 'like', "%{$term}%")
              ->orWhere('invoice_number', 'like', "%{$term}%");
            if ($hasEmail) $w->orWhere('email', 'like', "%{$term}%");
            if ($hasPhone) $w->orWhere('contact_number', 'like', "%{$term}%");
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
    $allowedDateFields = ['created_at','updated_at','date','date_finish','invoice_date'];
    $field = $request->get('date_field', 'created_at');
    if (!in_array($field, $allowedDateFields, true)) $field = 'created_at';
    if (($from = $request->get('date_from')) !== null && $from !== '') $q->whereDate($field, '>=', $from);
    if (($to   = $request->get('date_to'))   !== null && $to   !== '') $q->whereDate($field, '<=', $to);

    $rows = $q->orderByDesc('created_at')->cursor();

    // Spreadsheet
    $ss = new Spreadsheet();
    $sheet = $ss->getActiveSheet();

    $headings = [
        'Id','Month','Date','Company','Product','Product Category','Location','Traffic','Duration','Amount',
        'Status','Remarks','Client','Date Finish','Job Number','Artwork','Invoice Date','Invoice Number',
        'Created At','Updated At','Contact Number','Email','Outdoor Size','Outdoor District Council','Outdoor Coordinates',
    ];

    $lastColLetter = Coordinate::stringFromColumnIndex(count($headings));
    $sheet->setCellValue('A1', 'OUTDOOR_EXPORT_'.now()->format('Y-m-d'));
    $sheet->mergeCells("A1:{$lastColLetter}1");
    $sheet->getStyle('A1')->applyFromArray([
        'font' => ['bold' => true, 'size' => 14],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFF00']],
    ]);
    $sheet->getRowDimension(1)->setRowHeight(24);

    // Headings row 2
    foreach ($headings as $i => $h) {
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($i + 1).'2', $h);
    }
    $sheet->getStyle("A2:{$lastColLetter}2")->applyFromArray([
        'font' => ['bold' => true],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'EEEEEE']],
    ]);

    // Data rows from row 3
    $r = 3;
    $fmt = function($v) {
        if (empty($v)) return '';
        if ($v instanceof \DateTimeInterface) return $v->format('Y-m-d');
        try { return Carbon::parse($v)->format('Y-m-d'); } catch (\Throwable $e) { return (string)$v; }
    };

    foreach ($rows as $row) {
        $values = [
            $row->id,
            $row->month,
            $fmt($row->date ?? null),
            $row->company,
            $row->product,
            $row->product_category,
            $row->location,
            $row->traffic,
            $row->duration,
            $row->amount,
            $row->status,
            $row->remarks,
            $row->client,
            $fmt($row->date_finish ?? null),
            $row->job_number,
            $row->artwork,
            $fmt($row->invoice_date ?? null),
            $row->invoice_number,
            (string)$row->created_at,
            (string)$row->updated_at,
            $row->contact_number,
            $row->email,
            $row->outdoor_size,
            $row->outdoor_district_council,
            $row->outdoor_coordinates,
        ];
        foreach ($values as $i => $val) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($i + 1).$r, $val);
        }
        $r++;
    }

    for ($i=1; $i<=count($headings); $i++) {
        $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
    }
    $sheet->freezePane('A3');

    $writer = new Xlsx($ss);
    $filename = 'OUTDOOR_EXPORT_'.now()->format('Ymd_His').'.xlsx';
    return response()->streamDownload(function () use ($writer, $ss) {
        $writer->save('php://output');
        $ss->disconnectWorksheets();
    }, $filename, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
}




    // 🔧 UPDATED: Export method for Monthly Ongoing Job section with product_category fallback
    public function downloadTemplate()
    {
        $csvFileName = 'master_file_import_template.csv';
        return response()->stream(function() {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'month','date','company','product','traffic','duration','status','client','date_finish','job_number','artwork','invoice_date','invoice_number'
            ]);
            fputcsv($handle, [
                'January','2024-01-15','Sample Company Ltd','HM','1000','30','completed','Sample Client','2024-01-20','JOB001','BGOC','2024-01-25','INV001'
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
        'jan'=>1,'january'=>1,'feb'=>2,'february'=>2,'mar'=>3,'march'=>3,
        'apr'=>4,'april'=>4,'may'=>5,'jun'=>6,'june'=>6,'jul'=>7,'july'=>7,
        'aug'=>8,'august'=>8,'sep'=>9,'september'=>9,'oct'=>10,'october'=>10,
        'nov'=>11,'november'=>11,'dec'=>12,'december'=>12,
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
        $months = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];
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
        $months = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','Dec'];
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

        foreach ([
            'product','site','client','payment',
            'material_received','artwork','approval',
            'sent_to_printer','installation','dismantle'
        ] as $field) {
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
        $months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
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
