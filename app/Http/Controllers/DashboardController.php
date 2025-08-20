<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterFile;
use App\Models\MediaOngoingJob;
use App\Models\OutdoorCoordinatorTracking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('outdoor_year', now()->year);
        $outdoorCategories = ['HM','TB','TTM','BB','Star','Flyers','Bunting','Signages','Outdoor'];
        $hasPC = Schema::hasColumn('master_files', 'product_category');

        $outdoorQuery = MasterFile::query()
            ->where(function($q) use ($outdoorCategories, $hasPC) {
                if ($hasPC) {
                    $q->whereIn('product_category', $outdoorCategories)
                    ->orWhereRaw('LOWER(product_category) LIKE ?', ['%outdoor%']);
                } else {
                    // Fallback based on product names/codes
                    $q->whereIn('product', $outdoorCategories)
                    ->orWhereRaw('LOWER(product) LIKE ?', ['%outdoor%']);
                }
            })
            ->where(function($q) use ($year) {
                $q->whereYear('date', $year)
                ->orWhereYear('date_finish', $year)
                ->orWhereYear('created_at', $year);
            });

        $outdoorClients = MasterFile::query()
            ->where(function($q) use ($outdoorCategories, $hasPC){
                if ($hasPC) {
                    $q->whereIn('product_category', $outdoorCategories)
                    ->orWhereRaw('LOWER(product_category) LIKE ?', ['%outdoor%']);
                } else {
                    $q->whereIn('product', $outdoorCategories)
                    ->orWhereRaw('LOWER(product) LIKE ?', ['%outdoor%']);
                }
            })
            ->whereNotNull('client')
            ->distinct()->pluck('client')->sort();

        $outdoorStates = MasterFile::query()
            ->whereNotNull('location')
            ->where(function($q) use ($outdoorCategories, $hasPC){
                if ($hasPC) {
                    $q->whereIn('product_category', $outdoorCategories)
                    ->orWhereRaw('LOWER(product_category) LIKE ?', ['%outdoor%']);
                } else {
                    $q->whereIn('product', $outdoorCategories)
                    ->orWhereRaw('LOWER(product) LIKE ?', ['%outdoor%']);
                }
            })
            ->distinct()->pluck('location')->filter()->sort();

        $availableYears = MasterFile::query()
            ->selectRaw('YEAR(COALESCE(date, created_at)) as year')
            ->where(function($q) use ($outdoorCategories, $hasPC){
                if ($hasPC) {
                    $q->whereIn('product_category', $outdoorCategories)
                    ->orWhereRaw('LOWER(product_category) LIKE ?', ['%outdoor%']);
                } else {
                    $q->whereIn('product', $outdoorCategories)
                    ->orWhereRaw('LOWER(product) LIKE ?', ['%outdoor%']);
                }
            })
            ->distinct()->orderBy('year','desc')->pluck('year')
            ->when(fn($c) => !$c->contains(now()->year), fn($c) => $c->prepend(now()->year));


        // 🔧 FIX: Get all MasterFiles with proper filtering
        $masterFilesQuery = MasterFile::query();

        // Apply filters if they exist
        if ($request->filled('search')) {
            $search = $request->search;
            $masterFilesQuery->where(function($q) use ($search) {
                $q->where('company', 'like', "%{$search}%")
                  ->orWhere('product', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhere('client', 'like', "%{$search}%")
                  ->orWhere('month', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $masterFilesQuery->where('status', $request->status);
        }

        if ($request->filled('month')) {
            $masterFilesQuery->where('month', $request->month);
        }

        if ($request->filled('product_category')) {
            $masterFilesQuery->where('product_category', $request->product_category);
        }

        // Get paginated results
        $masterFiles = $masterFilesQuery->orderBy('created_at', 'desc')->paginate(50);

        // Calculate job statistics
        $totalJobs = MasterFile::count();
        $completedJobs = MasterFile::where('status', 'completed')->count();
        $ongoingJobs = MasterFile::where('status', 'ongoing')->count();
        $pendingJobs = MasterFile::where('status', 'pending')->count();

        // Monthly category data (adjust based on your needs)
        $monthlyByCategory = MasterFile::selectRaw('
                product_category,
                MONTH(COALESCE(date, created_at)) as month,
                COUNT(*) as count
            ')
            ->whereYear(DB::raw('COALESCE(date, created_at)'), now()->year)
            ->groupBy('product_category', DB::raw('MONTH(COALESCE(date, created_at))'))
            ->get()
            ->groupBy('product_category');

        // 🚀 FIX: Replace the "Fix for monthly by category" section with proper record queries

        // Get KLTG records with optional month filtering
        $kltgQuery = MasterFile::query()
            ->where(function ($q) {
                $q->where('product_category', 'KLTG')
                  ->orWhereRaw('LOWER(product_category) LIKE ?', ['%kltg%'])
                  ->orWhere('product', 'like', '%KLTG%');
            });

        if ($request->filled('month')) {
            $kltgQuery->where('month', $request->month);
        }

        $kltgRecords = $kltgQuery
            ->orderByRaw('COALESCE(date, created_at) DESC')
            ->get();

        // Get Media records with optional month filtering
        // Get Media records with optional filters
        $mediaQuery = MasterFile::query()
            ->where(function ($q) {
                $q->where('product_category', 'Media')
                ->orWhereRaw('LOWER(product_category) LIKE ?', ['%media%'])
                ->orWhere('product', 'like', '%FB%')
                ->orWhere('product', 'like', '%IG%');
            });

        // Year filter (optional if you want to match Outdoor logic)
        $year = $request->input('media_year', now()->year);
        $mediaQuery->where(function($q) use ($year) {
            $q->whereYear('date', $year)
            ->orWhereYear('date_finish', $year)
            ->orWhereYear('created_at', $year);
        });

        // Apply filters
        if ($request->filled('media_client')) {
            $mediaQuery->where('client', $request->media_client);
        }

        if ($request->filled('media_state')) {
            $mediaQuery->where('location', 'like', '%'.$request->media_state.'%');
        }

        if ($request->filled('media_status')) {
            $mediaQuery->where('status', $request->media_status);
        }

        if ($request->filled('month')) {
            $mediaQuery->where('month', $request->month);
        }

        $mediaRecords = $mediaQuery
            ->orderByRaw('COALESCE(date, created_at) DESC')
            ->get();


        // === Sync master_files -> media_ongoing_jobs (idempotent) ===
        $mediaUpserts = $mediaRecords->map(function ($mf) {
            return [
                'master_file_id' => $mf->id,
                'date' => $mf->date,
                'company' => $mf->company,
                'product' => $mf->product,
                'category' => $mf->product_category,
                'location' => $mf->location ?? $mf->site ?? null,
                'start_date' => $mf->start_date ?? null,
                'end_date' => $mf->end_date ?? null,
                // NOTE: map your month fields – adjust if your target table uses different names
                'jan' => $mf->check_jan,
                'feb' => $mf->check_feb,
                'mar' => $mf->check_mar,
                'apr' => $mf->check_apr,
                'may' => $mf->check_may,
                'jun' => $mf->check_jun,
                'jul' => $mf->check_jul,
                'aug' => $mf->check_aug,
                'sep' => $mf->check_sep,
                'oct' => $mf->check_oct,
                'nov' => $mf->check_nov,
                'dec' => $mf->check_dec,
                'remarks' => $mf->remarks,
                'updated_at' => now(),
                'created_at' => now(), // ignored on update
            ];
        });

        if ($mediaUpserts->isNotEmpty()) {
            MediaOngoingJob::upsert(
                $mediaUpserts->all(),
                ['master_file_id'], // unique key
                [
                    'date', 'company', 'product', 'category', 'location',
                    'start_date', 'end_date',
                    'jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec',
                    'remarks', 'updated_at'
                ]
            );
        }

        // Ensure the Blade can do @foreach($monthlyByCategory['Media']) as records
        // Convert to Collection if it isn't already
        $monthlyByCategory = $monthlyByCategory instanceof \Illuminate\Support\Collection
            ? $monthlyByCategory
            : collect($monthlyByCategory);

        // Override with actual records instead of counts
        $monthlyByCategory->put('KLTG', $kltgRecords);
        $monthlyByCategory->put('Media', $mediaRecords);

        // Keep the legacy monthlyByCategoryRecords for backward compatibility
        $monthlyByCategoryRecords = [];
        $monthlyByCategoryRecords['KLTG'] = $kltgRecords;
        $monthlyByCategoryRecords['Media'] = $mediaRecords;

        // After syncing, just read from media_ongoing_jobs (if you use this dataset elsewhere on the page)
        $mediaSocialJobs = MediaOngoingJob::orderBy('date', 'desc')->get();

        // Get recent jobs
        $recentJobs = MasterFile::whereNotNull('date')
                               ->orderBy('date', 'desc')
                               ->limit(10)
                               ->get();

        $grouped = MasterFile::whereNotNull('date')
                            ->orderBy('date', 'desc')
                            ->get()
                            ->groupBy(function($item) {
                                return Carbon::parse($item->date)->format('Y');
                            });

        return view('dashboard', compact(
            'masterFiles',
            'totalJobs',
            'completedJobs',
            'ongoingJobs',
            'pendingJobs',
            'monthlyByCategory',
            'monthlyByCategoryRecords', // Add this new variable
            'mediaSocialJobs',
            'recentJobs',
            'grouped',
            // Outdoor variables
            'outdoorClients',
            'outdoorStates',
            'availableYears'
        ));
    }

    // 🚀 NEW: Update Outdoor Tracking inline (AJAX)
    /**
 * AJAX update for outdoor monthly tracking fields
 */
    public function updateOutdoorField(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:master_files,id',
                'field' => 'required|string',
                'value' => 'nullable|string',
            ]);

            $masterFile = MasterFile::findOrFail($validated['id']);

            // Allow these fields for inline editing
            $allowedFields = [
                'site', 'location', 'status', 'remarks',
                'check_jan', 'check_feb', 'check_mar', 'check_apr',
                'check_may', 'check_jun', 'check_jul', 'check_aug',
                'check_sep', 'check_oct', 'check_nov', 'check_dec'
            ];

            if (!in_array($validated['field'], $allowedFields)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Field not allowed for editing'
                ], 400);
            }

            // Update the field
            $masterFile->{$validated['field']} = $validated['value'];
            $masterFile->save();

            return response()->json([
                'success' => true,
                'message' => 'Field updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Outdoor field update failed', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    // Existing coordinator methods...
    public function coordinatorOutdoor()
    {
        $outdoorJobs = MasterFile::whereIn('product', ['HM', 'TB', 'TTM', 'BB', 'Star', 'Flyers', 'Bunting', 'Signages'])
            ->orderBy('date', 'desc')
            ->paginate(20);

        $totalOutdoor = MasterFile::whereIn('product', ['HM', 'TB', 'TTM', 'BB', 'Star', 'Flyers', 'Bunting', 'Signages'])->count();
        $completedOutdoor = MasterFile::whereIn('product', ['HM', 'TB', 'TTM', 'BB', 'Star', 'Flyers', 'Bunting', 'Signages'])
            ->where('status', 'completed')->count();
        $ongoingOutdoor = MasterFile::whereIn('product', ['HM', 'TB', 'TTM', 'BB', 'Star', 'Flyers', 'Bunting', 'Signages'])
            ->where('status', 'ongoing')->count();
        $pendingOutdoor = MasterFile::whereIn('product', ['HM', 'TB', 'TTM', 'BB', 'Star', 'Flyers', 'Bunting', 'Signages'])
            ->where('status', 'pending')->count();

        return view('coordinators.outdoor', compact(
            'outdoorJobs',
            'totalOutdoor',
            'completedOutdoor',
            'ongoingOutdoor',
            'pendingOutdoor'
        ));
    }

    public function coordinatorKltg()
    {
        $kltgJobs = MasterFile::whereIn('product', ['KLTG', 'KLTG listing', 'KLTG quarter page'])
            ->orderBy('date', 'desc')
            ->paginate(20);

        $totalKltg = MasterFile::whereIn('product', ['KLTG', 'KLTG listing', 'KLTG quarter page'])->count();
        $completedKltg = MasterFile::whereIn('product', ['KLTG', 'KLTG listing', 'KLTG quarter page'])
            ->where('status', 'completed')->count();
        $ongoingKltg = MasterFile::whereIn('product', ['KLTG', 'KLTG listing', 'KLTG quarter page'])
            ->where('status', 'ongoing')->count();
        $pendingKltg = MasterFile::whereIn('product', ['KLTG', 'KLTG listing', 'KLTG quarter page'])
            ->where('status', 'pending')->count();

        return view('coordinators.kltg', compact(
            'kltgJobs',
            'totalKltg',
            'completedKltg',
            'ongoingKltg',
            'pendingKltg'
        ));
    }

    public function kltg()
    {
        return redirect()->route('coordinator.kltg.index');
    }

    // app/Http/Controllers/DashboardController.php

    public function media()
{
    $year = now()->year;

    $rows = MasterFile::query()
        ->where(function ($q) {
            $q->whereRaw('LOWER(product_category) LIKE ?', ['%media%'])
              ->orWhereRaw('LOWER(product) LIKE ?', ['%fb%'])
              ->orWhereRaw('LOWER(product) LIKE ?', ['%ig%']);
        })
        ->orderByRaw('COALESCE(`date`, `created_at`) DESC')
        ->get();

    // kalau belum pakai detailsMap, kirim array kosong
    return view('dashboard.media', [
        'year'       => $year,
        'rows'       => $rows,
        'detailsMap' => [],
    ]);
}


    public function outdoor()
{
    // Base query for outdoor master files
    $base = MasterFile::query()->where(function ($q) {
        $q->where('product_category', 'Outdoor')
          ->orWhereRaw('LOWER(product_category) LIKE ?', ['%outdoor%'])
          ->orWhereRaw('LOWER(product) LIKE ?', ['%outdoor%'])
          // Add the same categories used in OutdoorCoordinatorTracking
          ->orWhereIn('product_category', ['HM', 'TB', 'TTM', 'BB', 'Star', 'Flyers', 'Bunting', 'Signages']);
    });

    // Apply filters from request
    if (request('outdoor_year')) {
        $base->whereRaw("YEAR(COALESCE(
            STR_TO_DATE(`date`, '%Y-%m-%d'),
            STR_TO_DATE(`date`, '%d/%m/%Y'),
            STR_TO_DATE(`date`, '%Y/%m/%d'),
            `created_at`
        )) = ?", [request('outdoor_year')]);
    }

    if (request('outdoor_client')) {
        $base->where('client', request('outdoor_client'));
    }

    if (request('outdoor_state')) {
        // Check if 'state' column exists, otherwise use 'location'
        if (Schema::hasColumn('master_files', 'state')) {
            $base->where('state', request('outdoor_state'));
        } else {
            $base->where('location', 'LIKE', '%' . request('outdoor_state') . '%');
        }
    }

    if (request('outdoor_status')) {
        $base->where('status', request('outdoor_status'));
    }

    // Get available years for filter dropdown
    $yearExpr = "YEAR(COALESCE(
        STR_TO_DATE(`date`, '%Y-%m-%d'),
        STR_TO_DATE(`date`, '%d/%m/%Y'),
        STR_TO_DATE(`date`, '%Y/%m/%d'),
        `created_at`
    )) as year";

    $availableYears = MasterFile::query()
        ->where(function ($q) {
            $q->where('product_category', 'Outdoor')
              ->orWhereRaw('LOWER(product_category) LIKE ?', ['%outdoor%'])
              ->orWhereRaw('LOWER(product) LIKE ?', ['%outdoor%'])
              ->orWhereIn('product_category', ['HM', 'TB', 'TTM', 'BB', 'Star', 'Flyers', 'Bunting', 'Signages']);
        })
        ->selectRaw("DISTINCT {$yearExpr}")
        ->orderByDesc('year')
        ->pluck('year')
        ->filter()
        ->values();

    // Get the filtered records for the table
    $outdoorJobs = (clone $base)
        ->orderByRaw("COALESCE(
            STR_TO_DATE(`date`, '%Y-%m-%d'),
            STR_TO_DATE(`date`, '%d/%m/%Y'),
            STR_TO_DATE(`date`, '%Y/%m/%d'),
            `created_at`
        ) DESC")
        ->get();

    // Get unfiltered data for filter dropdown options
    $baseForFilters = MasterFile::query()->where(function ($q) {
        $q->where('product_category', 'Outdoor')
          ->orWhereRaw('LOWER(product_category) LIKE ?', ['%outdoor%'])
          ->orWhereRaw('LOWER(product) LIKE ?', ['%outdoor%'])
          ->orWhereIn('product_category', ['HM', 'TB', 'TTM', 'BB', 'Star', 'Flyers', 'Bunting', 'Signages']);
    });

    $outdoorClients = (clone $baseForFilters)
        ->whereNotNull('client')
        ->distinct()
        ->pluck('client')
        ->filter()
        ->values();

    $outdoorLocations = (clone $baseForFilters)
        ->whereNotNull('location')
        ->distinct()
        ->pluck('location')
        ->filter()
        ->values();

    // Get states - use 'state' column if it exists, otherwise derive from location
    if (Schema::hasColumn('master_files', 'state')) {
        $outdoorStates = (clone $baseForFilters)
            ->whereNotNull('state')
            ->distinct()
            ->pluck('state')
            ->filter()
            ->values();
    } else {
        $outdoorStates = $outdoorLocations->map(function ($loc) {
            $parts = array_map('trim', explode(',', (string) $loc));
            return count($parts) ? end($parts) : null;
        })->filter()->unique()->values();
    }

    $outdoorStatuses = (clone $baseForFilters)
        ->whereNotNull('status')
        ->distinct()
        ->pluck('status')
        ->filter()
        ->values();

    $outdoorProducts = (clone $baseForFilters)
        ->whereNotNull('product')
        ->distinct()
        ->pluck('product')
        ->filter()
        ->values();

    $monthlyByCategory = ['Outdoor' => $outdoorJobs];

    return view('dashboard.outdoor', compact(
        'availableYears',
        'outdoorJobs',  // This is what the Blade template expects
        'monthlyByCategory',
        'outdoorClients',
        'outdoorLocations',
        'outdoorStates',
        'outdoorStatuses',
        'outdoorProducts'
    ));
}

    public function coordinatorMedia()
    {
        $mediaJobs = MasterFile::whereIn('product', ['FB IG Ad'])
            ->orderBy('date', 'desc')
            ->paginate(20);

        $mediaOngoingJobs = MediaOngoingJob::orderBy('date', 'desc')->get();

        $totalMedia = MasterFile::whereIn('product', ['FB IG Ad'])->count();
        $completedMedia = MasterFile::whereIn('product', ['FB IG Ad'])
            ->where('status', 'completed')->count();
        $ongoingMedia = MasterFile::whereIn('product', ['FB IG Ad'])
            ->where('status', 'ongoing')->count();
        $pendingMedia = MasterFile::whereIn('product', ['FB IG Ad'])
            ->where('status', 'pending')->count();

        return view('coordinators.media', compact(
            'mediaJobs',
            'mediaOngoingJobs',
            'totalMedia',
            'completedMedia',
            'ongoingMedia',
            'pendingMedia'
        ));
    }
}
