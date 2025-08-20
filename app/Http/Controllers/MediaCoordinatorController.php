<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\MasterFile;
use App\Models\MediaCoordinatorTracking;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Collection;

class MediaCoordinatorController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month');
        $year  = $request->get('year') ?: now()->year;

        // Get Media masters - only use existing columns
        $masters = MasterFile::query()
            ->where(function ($q) {
                $q->where('product_category', 'Media')
                  ->orWhereRaw('LOWER(product_category) LIKE ?', ['%media%']);
            })
            ->orderByRaw('COALESCE(date, created_at) DESC')
            ->get();

        // Pull latest coordinator tracking per master
        $masterIds = $masters->pluck('id')->all();

        $latestTracking = MediaCoordinatorTracking::query()
            ->whereIn('master_file_id', $masterIds)
            ->when($year, function($q) use ($year) {
                $q->whereYear('created_at', $year);
            })
            ->when($month, function($q) use ($month) {
                $monthNum = array_search($month, ['January','February','March','April','May','June','July','August','September','October','November','December']) + 1;
                if ($monthNum > 0) {
                    $q->whereMonth('created_at', $monthNum);
                }
            })
            ->orderByDesc(DB::raw('COALESCE(updated_at, created_at)'))
            ->get()
            ->keyBy('master_file_id'); // Map by master file ID

        // Build the data structure your Blade expects
        $records = $masters->map(function ($m) use ($latestTracking) {
            $t = $latestTracking->get($m->id);

            return (object)[
                // Master file fields
                'id'               => $m->id,
                'company'          => $m->company ?? $m->company_name ?? $m->client ?? '',
                'client'           => $m->client ?? '',
                'product'          => $m->product ?? $m->name ?? '',
                'product_category' => $m->product_category ?? 'Media',
                'end_date'         => $m->end_date ? \Carbon\Carbon::parse($m->end_date) : null,
                'date'             => $m->date ? \Carbon\Carbon::parse($m->date) : null,

                // Video workflow tracking fields (from MediaCoordinatorTracking)
                'title'            => $t ? ($t->title ?? '') : '',
                'client_bp'        => $t ? ($t->client_bp ?? '') : '',
                'x'                => $t ? ($t->x ?? '') : '',
                'material_reminder' => $t ? ($t->material_reminder ?? '') : '',
                'material_received' => $t ? ($t->material_received ?? '') : '',
                'video_done'       => $t ? ($t->video_done ?? '') : '',
                'video_approval'   => $t ? ($t->video_approval ?? '') : '',
                'video_approved'   => $t ? ($t->video_approved ?? '') : '',
                'video_scheduled'  => $t ? ($t->video_scheduled ?? '') : '',
                'video_posted'     => ($t && $t->video_posted) ? \Carbon\Carbon::parse($t->video_posted) : null,
                'post_link'        => $t ? ($t->post_link ?? '') : '',

                // Basic status fields
                'status'           => $this->getVideoWorkflowStatus($t),
                'project_status'   => $this->getVideoWorkflowStatus($t),
                'platform'         => $m->platform ?? 'Social Media',
                'location'         => $m->location ?? 'Online',
                'date_finish'      => ($t && $t->video_posted) ? \Carbon\Carbon::parse($t->video_posted) : null,
                'remarks'          => $this->generateRemarksFromWorkflow($t),

                // Monthly check fields - empty since this table tracks video workflow
                'check_jan'        => '',
                'check_feb'        => '',
                'check_mar'        => '',
                'check_apr'        => '',
                'check_may'        => '',
                'check_jun'        => '',
                'check_jul'        => '',
                'check_aug'        => '',
                'check_sep'        => '',
                'check_oct'        => '',
                'check_nov'        => '',
                'check_dec'        => '',

                // For updates - include tracking ID if exists
                'tracking_id'      => $t->id ?? null,
                'master_file_id'   => $m->id,
            ];
        });

        // Build the structure your Blade template expects
        $monthlyByCategory = [
            'Media' => $records,
        ];

        $months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        $years  = range(now()->year, now()->year - 5);

        // Make sure this matches your actual view file location
        return view('coordinators.media', compact(
            'monthlyByCategory','months','month','years','year'
        ));
    }

    /**
     * Get video workflow status based on tracking data
     */
    private function getVideoWorkflowStatus($tracking)
    {
        if (!$tracking) return 'Not Started';

        if ($tracking->video_posted) return 'Completed';
        if ($tracking->video_scheduled) return 'Scheduled';
        if ($tracking->video_approved) return 'Approved';
        if ($tracking->video_approval) return 'Pending Approval';
        if ($tracking->video_done) return 'Video Complete';
        if ($tracking->material_received) return 'In Production';
        if ($tracking->material_reminder) return 'Waiting for Materials';
        if ($tracking->client_bp) return 'Planning';

        return 'In Progress';
    }

    /**
     * Generate remarks based on video workflow progress
     */
    private function generateRemarksFromWorkflow($tracking)
    {
        if (!$tracking) return '';

        $remarks = [];
        if ($tracking->client_bp) $remarks[] = "Client BP: {$tracking->client_bp}";
        if ($tracking->material_reminder) $remarks[] = "Material: {$tracking->material_reminder}";
        if ($tracking->video_done) $remarks[] = "Video: {$tracking->video_done}";
        if ($tracking->post_link) $remarks[] = "Posted: {$tracking->post_link}";

        return implode(' | ', $remarks);
    }

    // Keep your alternative method for reference
    public function indexFromMasterFiles(Request $request)
    {
        $month = $request->get('month');
        $year  = $request->get('year');

        // Get MasterFiles that are Media category
        $mediaJobs = MasterFile::where('product_category', 'Media')
            ->orWhereRaw('LOWER(product_category) LIKE ?', ['%media%'])
            ->with(['mediaCoordinatorTracking']) // if you have this relationship
            ->when($month, function($q) use ($month) {
                $monthNum = array_search($month, ['January','February','March','April','May','June','July','August','September','October','November','December']) + 1;
                if ($monthNum > 0) {
                    $q->whereMonth('date', $monthNum);
                }
            })
            ->when($year, function($q) use ($year) {
                $q->whereYear('date', $year);
            })
            ->orderByDesc('date')
            ->get()
            ->map(function($masterFile) {
                // Add video workflow tracking data if exists
                $tracking = $masterFile->mediaCoordinatorTracking;

                // Merge master file with tracking data
                $combined = (object) $masterFile->toArray();

                if ($tracking) {
                    // Add video workflow fields instead of monthly checks
                    $combined->title = $tracking->title ?? '';
                    $combined->client_bp = $tracking->client_bp ?? '';
                    $combined->x = $tracking->x ?? '';
                    $combined->material_reminder = $tracking->material_reminder ?? '';
                    $combined->material_received = $tracking->material_received ?? '';
                    $combined->video_done = $tracking->video_done ?? '';
                    $combined->video_approval = $tracking->video_approval ?? '';
                    $combined->video_approved = $tracking->video_approved ?? '';
                    $combined->video_scheduled = $tracking->video_scheduled ?? '';
                    $combined->video_posted = $tracking->video_posted ?? '';
                    $combined->post_link = $tracking->post_link ?? '';
                    $combined->remarks = $this->generateRemarksFromWorkflow($tracking);
                } else {
                    // Initialize empty video workflow fields
                    $combined->title = '';
                    $combined->client_bp = '';
                    $combined->x = '';
                    $combined->material_reminder = '';
                    $combined->material_received = '';
                    $combined->video_done = '';
                    $combined->video_approval = '';
                    $combined->video_approved = '';
                    $combined->video_scheduled = '';
                    $combined->video_posted = '';
                    $combined->post_link = '';
                    $combined->remarks = '';
                }

                // Initialize empty monthly check fields for compatibility
                foreach(['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'] as $m) {
                    $combined->{"check_{$m}"} = '';
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
                // Update tracking record (not master file)
                $tracking = MediaCoordinatorTracking::findOrFail($validated['id']);

                // Only allow updates to existing columns
                $allowedFields = ['title','client_bp','x','material_reminder','material_received',
                                'video_done','video_approval','video_approved','video_scheduled',
                                'video_posted','post_link'];

                if (in_array($validated['field'], $allowedFields)) {
                    $tracking->{$validated['field']} = $validated['value'];
                    $tracking->save();
                } else {
                    // For monthly check fields, just return success (they're not stored in this table)
                    if (strpos($validated['field'], 'check_') === 0 || $validated['field'] === 'remarks') {
                        return response()->json(['success' => true, 'message' => 'Monthly tracking not available for video workflow']);
                    }
                }
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('MediaCoordinatorController@updateField error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 422);
        }
    }
}
