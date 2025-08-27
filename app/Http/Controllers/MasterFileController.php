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
use App\Models\KltgMonthlyDetail;

class MasterFileController extends Controller
{
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
                    'Outdoor' => ['HM', 'TB', 'TTM', 'BB', 'Star', 'Flyers', 'Bunting', 'Signages'],
                    'Media' => ['FB IG Ad'],
                    'KLTG' => ['KLTG', 'KLTG listing', 'KLTG quarter page','NP'],
                    default => []
                });
            } else {
                // fallback: filter lewat product
                $cat = strtolower($request->product_category);
                if ($cat === 'outdoor') {
                    $query->where(function($q) {
                        $q->whereIn('product', ['HM','TB','TTM','BB','Star','Flyers','Bunting','Signages'])
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
        $mediaOngoingJobs = MasterFile::where('category', 'Media')->get(); // Adjust as needed

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
        $allowedProducts = [
            'HM','TB','TTM','BB','Star','KLTG','Flyers','Bunting',
            'KLTG listing','KLTG quarter page','Signages','FB IG Ad','NP',
            // ✅ New ones:
            'YouTube Management',
            'FB/IG Management',
            'TikTok Management Boost',
            'Giveaways/ Contest Management',
            'Xiaohongshu Management',
        ];

        $validated = $request->validate([
            'month'           => 'required|string',
            'date'            => 'required|date',
            'company'         => 'required|string',
            'product'         => 'required|in:' . implode(',', $allowedProducts),
            'traffic'         => 'required|string',
            'duration'        => 'required|string',
            'status'          => 'required|string',
            'client'          => 'required|string',
            'date_finish'     => 'nullable|date',
            'job_number'      => 'nullable|string',
            'artwork'         => 'nullable|in:BGOC,Client',
            'invoice_date'    => 'nullable|date',
            'invoice_number'  => 'nullable|string',
        ]);

        // Detect category for JO prefix + (optionally) persist it
        $detectedCategory = method_exists(MasterFile::class, 'detectCategory')
            ? MasterFile::detectCategory($validated['product'])
            : $this->guessCategoryFromProduct($validated['product']);

        if (Schema::hasColumn('master_files', 'product_category')) {
            $validated['product_category'] = $detectedCategory;
        }

        if (empty($validated['job_number'])) {
            // Generate job number using category/product + global monthly suffix
            $validated['job_number'] = app(\App\Services\JobNumberService::class)
                ->generate(
                    $detectedCategory,
                    $validated['product']
                );
        }

        // Create MasterFile
        $mf = MasterFile::create($validated);
        return redirect()->route('dashboard')
            ->with('success', 'Master File data added successfully!');
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
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // 10MB
        ]);

        try {
            Excel::import(new MasterFileImport, $request->file('file'));

            // ===== 🔥 AUTO SEED KLTG ON IMPORT DISABLED =====
            // ℹ️ If you had auto-seeding logic in MasterFileImport class,
            // make sure to disable it there as well and use upsertKltgMonthly() instead.

            return back()->with('success', 'Import completed successfully.');
        } catch (\Throwable $e) {
            return back()->withErrors(['file' => 'Import failed: '.$e->getMessage()]);
        }
    }

    public function exportXlsx(Request $request)
    {
        // Collect known filters from the UI (adjust keys to your inputs if needed)
        $filters = $request->only([
            'date_from', 'date_to', 'date_field',   // date_field optional (e.g. 'created_at' or 'date')
            'search', 'contains',                   // unified "contains" search
            'status'                                // string or array
        ]);

        $filename = 'export_masterfiles_' . now('Asia/Kuala_Lumpur')->format('Ymd') . '.xlsx';

        return Excel::download(new MasterFilesExport($filters), $filename);
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
}
