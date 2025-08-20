<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\MasterFile;
use App\Models\MediaCoordinatorTracking;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

use Illuminate\Support\Facades\DB;

class MediaCoordinatorController extends Controller
{
    public function index(Request $request)
{
    $month = $request->get('month');
    $year  = $request->get('year');

    // Base query (paginate FIRST)
    $page = MediaCoordinatorTracking::with('masterFile')
        ->when($month, fn($q) => $q->where('month', $month))
        ->when($year,  fn($q) => $q->where('year',  $year))
        ->orderByDesc(DB::raw('COALESCE(updated_at, created_at)'))
        ->paginate(20)
        ->withQueryString();

    // Map the paged collection to merge master file fields
    $items = $page->getCollection()->map(function ($t) {
        $mf = $t->masterFile;
        return (object) array_merge(
            $t->toArray(),
            $mf ? $mf->only(['company','client','product','product_category','end_date']) : []
        );
    });

    // Re-wrap as paginator so Blade pagination still works
    $records = new LengthAwarePaginator(
        $items,
        $page->total(),
        $page->perPage(),
        $page->currentPage(),
        ['path' => request()->url(), 'query' => request()->query()]
    );

    $months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    $years  = range(now()->year, now()->year - 5);

    // NOTE: don't pass 'monthlyByCategory' unless your Blade needs it
    return view('coordinators.media', compact('records','months','month','years','year'));
}

    // Alternative method if you want to get data directly from MasterFiles
    public function indexFromMasterFiles(Request $request)
    {
        $month = $request->get('month');
        $year  = $request->get('year');

        // Get MasterFiles that are Media category
        $mediaJobs = MasterFile::where('product_category', 'Media')
            ->orWhere('category', 'Media')
            ->with(['mediaCoordinatorTracking']) // if you have this relationship
            ->when($month, function($q) use ($month) {
                // Filter by month if master file has date field
                $q->whereMonth('date', array_search($month, ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec']) + 1);
            })
            ->when($year, function($q) use ($year) {
                $q->whereYear('date', $year);
            })
            ->orderByDesc('date')
            ->get()
            ->map(function($masterFile) {
                // Add monthly tracking data if exists
                $tracking = $masterFile->mediaCoordinatorTracking;

                // Merge master file with tracking data
                $combined = (object) $masterFile->toArray();

                if ($tracking) {
                    foreach(['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'] as $m) {
                        $checkField = "check_{$m}";
                        $combined->$checkField = $tracking->$checkField ?? '';
                    }
                    $combined->remarks = $tracking->remarks ?? '';
                } else {
                    // Initialize empty tracking fields
                    foreach(['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'] as $m) {
                        $combined->{"check_{$m}"} = '';
                    }
                    $combined->remarks = '';
                }

                return $combined;
            });

        $monthlyByCategory = [
            'Media' => $mediaJobs
        ];

        // Debug
        Log::info('MEDIA INDEX FROM MASTER FILES', [
            'media_jobs_count' => $mediaJobs->count(),
            'first_job' => $mediaJobs->first() ? $mediaJobs->first() : 'No jobs found'
        ]);

        $months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        $years  = range(now()->year, now()->year - 5);

        return view('coordinators.media', compact('monthlyByCategory', 'months', 'month', 'years', 'year'));
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'master_file_id' => 'required|exists:master_files,id|unique:media_coordinator_trackings,master_file_id',
            ]);

            $mf = MasterFile::findOrFail($data['master_file_id']);

            MediaCoordinatorTracking::create([
                'master_file_id' => $mf->id,
                'date_in_snapshot' => $mf->date,
                'company_snapshot' => $mf->company,
            ]);

            return back()->with('success', 'Added to Media Coordinator List.');

        } catch (\Exception $e) {
            Log::error('MediaCoordinatorController@store error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to add to Media Coordinator List.']);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $row = MediaCoordinatorTracking::findOrFail($id);

            $validated = $request->validate([
                'title' => 'nullable|string|max:255',
                'client_bp' => 'nullable|string|max:255',
                'x' => 'nullable|string|max:255',
                'material_reminder' => 'nullable|string|max:255',
                'material_received' => 'nullable|string|max:255',
                'video_done' => 'nullable|string|max:255',
                'video_approval' => 'nullable|string|max:255',
                'video_approved' => 'nullable|string|max:255',
                'video_scheduled' => 'nullable|string|max:255',
                'video_posted' => 'nullable|date',
                'post_link' => 'nullable|url|max:255',
                // Add monthly check fields
                'check_jan' => 'nullable|string|max:255',
                'check_feb' => 'nullable|string|max:255',
                'check_mar' => 'nullable|string|max:255',
                'check_apr' => 'nullable|string|max:255',
                'check_may' => 'nullable|string|max:255',
                'check_jun' => 'nullable|string|max:255',
                'check_jul' => 'nullable|string|max:255',
                'check_aug' => 'nullable|string|max:255',
                'check_sep' => 'nullable|string|max:255',
                'check_oct' => 'nullable|string|max:255',
                'check_nov' => 'nullable|string|max:255',
                'check_dec' => 'nullable|string|max:255',
                'remarks' => 'nullable|string|max:1000',
            ]);

            $row->fill($validated)->save();

            return response()->json(['ok' => true]);

        } catch (\Exception $e) {
            Log::error('MediaCoordinatorController@update error: ' . $e->getMessage());
            return response()->json(['ok' => false, 'error' => 'Failed to update record.'], 422);
        }
    }

    // Add this method for AJAX updates from your template
    public function updateField(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'type' => 'required|in:master,tracking',
                'field' => 'required|string',
                'value' => 'nullable|string'
            ]);

            if ($validated['type'] === 'master') {
                // Update master file or tracking record
                $tracking = MediaCoordinatorTracking::findOrFail($validated['id']);
                $tracking->{$validated['field']} = $validated['value'];
                $tracking->save();
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('MediaCoordinatorController@updateField error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 422);
        }
    }
}
