<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterFile;
use App\Models\OutdoorCoordinatorTracking;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class OutdoorCoordinatorController  extends Controller
{

    public function index()
    {
        Log::info("HIT INDEX - Starting debug");

        try {
            // Step 1: Test autoCreateTrackingRecords
            Log::info("About to call autoCreateTrackingRecords");
            $this->autoCreateTrackingRecords();
            Log::info("autoCreateTrackingRecords completed successfully");

        } catch (\Exception $e) {
            Log::error("autoCreateTrackingRecords failed", [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            // Don't return here, continue to test other parts
        }

        try {
            // Step 2: Test the main query
            Log::info("About to run main outdoor jobs query");

            $outdoorJobs = OutdoorCoordinatorTracking::with('masterFile')
                ->whereHas('masterFile', function ($query) {
                    $query->whereIn('product_category', ['HM', 'TB', 'TTM', 'BB', 'Star', 'Flyers', 'Bunting', 'Signages'])
                        ->orWhere('product_category', 'LIKE', '%outdoor%')
                        ->orWhere('product_category', 'Outdoor');
                })
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            Log::info("Main query completed", ['count' => $outdoorJobs->count()]);

        } catch (\Exception $e) {
            Log::error("Main query failed", [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            // Return simple response to test if this is the issue
            return response("Main query failed: " . $e->getMessage(), 500);
        }

        try {
            // Step 3: Test statistics queries one by one
            Log::info("About to calculate totalOutdoor");

            $totalOutdoor = OutdoorCoordinatorTracking::whereHas('masterFile', function ($query) {
                $query->whereIn('product_category', ['HM', 'TB', 'TTM', 'BB', 'Star', 'Flyers', 'Bunting', 'Signages'])
                    ->orWhere('product_category', 'LIKE', '%outdoor%')
                    ->orWhere('product_category', 'Outdoor');
            })->count();

            Log::info("totalOutdoor calculated", ['total' => $totalOutdoor]);

            Log::info("About to calculate completedOutdoor");

            $completedOutdoor = OutdoorCoordinatorTracking::where('status', 'completed')
                ->whereHas('masterFile', function ($query) {
                    $query->whereIn('product_category', ['HM', 'TB', 'TTM', 'BB', 'Star', 'Flyers', 'Bunting', 'Signages'])
                        ->orWhere('product_category', 'LIKE', '%outdoor%')
                        ->orWhere('product_category', 'Outdoor');
                })->count();

            Log::info("completedOutdoor calculated", ['completed' => $completedOutdoor]);

            $ongoingOutdoor = OutdoorCoordinatorTracking::where('status', 'ongoing')
                ->whereHas('masterFile', function ($query) {
                    $query->whereIn('product_category', ['HM', 'TB', 'TTM', 'BB', 'Star', 'Flyers', 'Bunting', 'Signages'])
                        ->orWhere('product_category', 'LIKE', '%outdoor%')
                        ->orWhere('product_category', 'Outdoor');
                })->count();

            $pendingOutdoor = OutdoorCoordinatorTracking::where('status', 'pending')
                ->whereHas('masterFile', function ($query) {
                    $query->whereIn('product_category', ['HM', 'TB', 'TTM', 'BB', 'Star', 'Flyers', 'Bunting', 'Signages'])
                        ->orWhere('product_category', 'LIKE', '%outdoor%')
                        ->orWhere('product_category', 'Outdoor');
                })->count();

            Log::info("All statistics calculated successfully");

        } catch (\Exception $e) {
            Log::error("Statistics calculation failed", [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response("Statistics calculation failed: " . $e->getMessage(), 500);
        }

        try {
            // Step 4: Test view rendering
            Log::info("About to render view");

            // Check if view exists first
            if (!view()->exists('coordinators.outdoor')) {
                Log::error("View coordinators.outdoor does not exist");
                return response("View coordinators.outdoor not found!", 500);
            }

            $viewData = compact(
                'outdoorJobs',
                'totalOutdoor',
                'completedOutdoor',
                'ongoingOutdoor',
                'pendingOutdoor'
            );

            Log::info("About to call view() with data", ['data_keys' => array_keys($viewData)]);

            return view('coordinators.outdoor', $viewData);

        } catch (\Exception $e) {
            Log::error("View rendering failed", [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response("View rendering failed: " . $e->getMessage(), 500);
        }
    }

    // Alternative: Even simpler test to isolate the issue
    public function index_minimal_test()
    {
        Log::info("Minimal test starting");

        try {
            // Test 1: Just return simple data
            return response()->json([
                'status' => 'success',
                'message' => 'Controller reached successfully',
                'timestamp' => now()
            ]);

        } catch (\Exception $e) {
            Log::error("Even minimal test failed", ['error' => $e->getMessage()]);
            return response("Critical error: " . $e->getMessage(), 500);
        }
    }

    /**
     * 🔥 NEW: AJAX Update Field for Inline Editing
     */
    public function updateField(Request $request)
    {
        $job = OutdoorCoordinatorTracking::with('masterFile')->findOrFail($request->id);

        $field = $request->field;
        $value = $request->value;

        // Daftar field yang ada di master_files
        $masterFields = [
            'check_jan', 'check_feb', 'check_mar', 'check_apr',
            'check_may', 'check_jun', 'check_jul', 'check_aug',
            'check_sep', 'check_oct', 'check_nov', 'check_dec',
        ];

        if (in_array($field, $masterFields)) {
            if ($job->masterFile) {
                $job->masterFile->{$field} = $value;
                $job->masterFile->save();
                return response()->json(['success' => true]);
            } else {
                return response()->json(['error' => 'No master file found.'], 404);
            }
        }

        // Kalau field bukan bulan, update normal
        $job->{$field} = $value;
        $job->save();

        return response()->json(['success' => true]);
    }


    public function syncWithMasterFiles()
    {
        // Ambil semua master file dengan kategori Outdoor
        $masterFiles = MasterFile::where('product_category', 'Outdoor')->get();

        $synced = 0;

        foreach ($masterFiles as $mf) {
            // Cek kalau belum ada tracking-nya
            $exists = OutdoorCoordinatorTracking::where('master_file_id', $mf->id)->exists();
            if (!$exists) {
                OutdoorCoordinatorTracking::create([
                    'master_file_id' => $mf->id,
                    // kolom lainnya bisa default null
                ]);
                $synced++;
            }
        }

        return redirect()->back()->with('success', "$synced outdoor data synced successfully.");
    }

    /**
     * 🔥 NEW: Get Dynamic Years for Filter Dropdown
     */
    public function getAvailableYears()
    {
        $years = OutdoorCoordinatorTracking::selectRaw('DISTINCT YEAR(created_at) as year')
            ->whereNotNull('created_at')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        // Add current year if not in list
        $currentYear = now()->year;
        if (!in_array($currentYear, $years)) {
            $years[] = $currentYear;
            rsort($years); // Sort descending
        }

        return $years;
    }

    /**
     * Automatically create tracking records for outdoor master files that don't have them yet
     */
    private function autoCreateTrackingRecords()
    {
        // Get outdoor master files that don't have tracking records yet
        $outdoorMasterFiles = MasterFile::where(function($query) {
            $query->whereIn('product_category', ['HM', 'TB', 'TTM', 'BB', 'Star', 'Flyers', 'Bunting', 'Signages'])
                  ->orWhere('product_category', 'LIKE', '%outdoor%')
                  ->orWhere('product_category', 'Outdoor');
        })
        ->whereNotExists(function($query) {
            $query->select(DB::raw(1))
                  ->from('outdoor_coordinator_trackings')
                  ->whereRaw('outdoor_coordinator_trackings.master_file_id = master_files.id');
        })
        ->get();

        // Create tracking records for each outdoor master file
        foreach ($outdoorMasterFiles as $masterFile) {
            OutdoorCoordinatorTracking::create([
                'master_file_id' => $masterFile->id,
                'status' => 'pending',
                'site' => $masterFile->location ?? null,
                'payment' => null,
                'material' => null,
                'artwork' => null,
                'received_approval' => null,
                'sent_to_printer' => null,
                'collection_printer' => null,
                'installation' => null,
                'dismantle' => null,
                'remarks' => null,
                'next_follow_up' => null,
            ]);
        }
    }

    public function syncFromMasterFile()
    {
        $outdoor = MasterFile::where(function($query) {
            $query->whereIn('product_category', ['HM', 'TB', 'TTM', 'BB', 'Star', 'Flyers', 'Bunting', 'Signages'])
                  ->orWhere('product_category', 'LIKE', '%outdoor%')
                  ->orWhere('product_category', 'Outdoor');
        })->get();

        $createdCount = 0;
        $updatedCount = 0;

        foreach ($outdoor as $item) {
            $tracking = OutdoorCoordinatorTracking::updateOrCreate(
                ['master_file_id' => $item->id],
                [
                    'site' => $item->location,
                    'status' => 'pending',
                ]
            );

            if ($tracking->wasRecentlyCreated) {
                $createdCount++;
            } else {
                $updatedCount++;
            }
        }

        $message = "Sync completed! Created: {$createdCount}, Updated: {$updatedCount} records.";
        return redirect()->route('coordinator.outdoor.index')->with('success', $message);
    }

    public function create()
    {
        // Get only outdoor-related master files that don't have tracking records yet
        $masterFiles = MasterFile::where(function($query) {
            $query->whereIn('product_category', ['HM', 'TB', 'TTM', 'BB', 'Star', 'Flyers', 'Bunting', 'Signages'])
                  ->orWhere('product_category', 'LIKE', '%outdoor%')
                  ->orWhere('product_category', 'Outdoor');
        })
        ->whereNotExists(function($query) {
            $query->select(DB::raw(1))
                  ->from('outdoor_coordinator_trackings')
                  ->whereRaw('outdoor_coordinator_trackings.master_file_id = master_files.id');
        })
        ->orderBy('client')
        ->get();

        return view('coordinator.outdoor.create', compact('masterFiles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'master_file_id' => 'required|exists:master_files,id',
            'site' => 'nullable|string|max:255',
            'payment' => 'nullable|string|max:100',
            'material' => 'nullable|string|max:100',
            'artwork' => 'nullable|string|max:100',
            'received_approval' => 'nullable|date',
            'sent_to_printer' => 'nullable|date',
            'collection_printer' => 'nullable|date',
            'installation' => 'nullable|date',
            'dismantle' => 'nullable|date',
            'remarks' => 'nullable|string',
            'next_follow_up' => 'nullable|date',
            'status' => 'nullable|in:pending,ongoing,completed'
        ]);

        // Set default status if not provided
        if (!isset($validated['status'])) {
            if (!empty($validated['dismantle'])) {
                $validated['status'] = 'completed';
            } elseif (!empty($validated['installation'])) {
                $validated['status'] = 'ongoing';
            } else {
                $validated['status'] = 'pending';
            }
        }

        // Check if tracking record already exists for this master file
        $existingTracking = OutdoorCoordinatorTracking::where('master_file_id', $validated['master_file_id'])->first();

        if ($existingTracking) {
            return redirect()->route('coordinator.outdoor.index')
                           ->with('error', 'Tracking record already exists for this master file!');
        }

        OutdoorCoordinatorTracking::create($validated);

        return redirect()->route('coordinator.outdoor.index')
                        ->with('success', 'Outdoor tracking record created successfully!');
    }

    public function show($id)
    {
        $tracking = OutdoorCoordinatorTracking::with('masterFile')->findOrFail($id);
        return view('coordinator.outdoor.show', compact('tracking'));
    }

    public function edit($id)
    {
        $tracking = OutdoorCoordinatorTracking::with('masterFile')->findOrFail($id);

        // Get all outdoor master files for the dropdown
        $masterFiles = MasterFile::where(function($query) {
            $query->whereIn('product_category', ['HM', 'TB', 'TTM', 'BB', 'Star', 'Flyers', 'Bunting', 'Signages'])
                  ->orWhere('product_category', 'LIKE', '%outdoor%')
                  ->orWhere('product_category', 'Outdoor');
        })
        ->orderBy('client')
        ->get();

        return view('coordinator.outdoor.edit', compact('tracking', 'masterFiles'));
    }

    public function update(Request $request, $id)
    {
        $tracking = OutdoorCoordinatorTracking::findOrFail($id);

        $validated = $request->validate([
            'master_file_id' => 'required|exists:master_files,id',
            'site' => 'nullable|string|max:255',
            'payment' => 'nullable|string|max:100',
            'material' => 'nullable|string|max:100',
            'artwork' => 'nullable|string|max:100',
            'received_approval' => 'nullable|date',
            'sent_to_printer' => 'nullable|date',
            'collection_printer' => 'nullable|date',
            'installation' => 'nullable|date',
            'dismantle' => 'nullable|date',
            'remarks' => 'nullable|string',
            'next_follow_up' => 'nullable|date',
            'status' => 'nullable|in:pending,ongoing,completed'
        ]);

        // Auto-update status based on progress
        if (!isset($validated['status'])) {
            if (!empty($validated['dismantle'])) {
                $validated['status'] = 'completed';
            } elseif (!empty($validated['installation'])) {
                $validated['status'] = 'ongoing';
            } else {
                $validated['status'] = 'pending';
            }
        }

        $tracking->update($validated);

        return redirect()->route('coordinator.outdoor.index')
                        ->with('success', 'Outdoor tracking record updated successfully!');
    }

    public function updateInline(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:outdoor_coordinator_trackings,id',
            'field' => 'required|string',
            'value' => 'nullable|string',
        ]);

        $job = OutdoorCoordinatorTracking::find($validated['id']);

        // Only allow these fields for inline edit
        $allowed = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec','remarks','status','site','start_date','end_date'];
        if (!in_array($validated['field'], $allowed)) {
            return response()->json(['success' => false, 'error' => 'Invalid field']);
        }

        $job->{$validated['field']} = $validated['value'];
        $job->save();

        return response()->json(['success' => true]);
    }


    public function destroy($id)
    {
        $tracking = OutdoorCoordinatorTracking::findOrFail($id);
        $tracking->delete();

        return redirect()->route('coordinator.outdoor.index')
                        ->with('success', 'Outdoor tracking record deleted successfully!');
    }

    /**
     * Manually seed/sync tracking records from master files
     */
    public function seedFromMasterFiles()
    {
        $createdCount = 0;

        // Get outdoor master files that don't have tracking records yet
        $outdoorFiles = MasterFile::where(function($query) {
            $query->whereIn('product_category', ['HM', 'TB', 'TTM', 'BB', 'Star', 'Flyers', 'Bunting', 'Signages'])
                  ->orWhere('product_category', 'LIKE', '%outdoor%')
                  ->orWhere('product_category', 'Outdoor');
        })
        ->whereNotExists(function($query) {
            $query->select(DB::raw(1))
                  ->from('outdoor_coordinator_trackings')
                  ->whereRaw('outdoor_coordinator_trackings.master_file_id = master_files.id');
        })
        ->get();

        foreach ($outdoorFiles as $file) {
            OutdoorCoordinatorTracking::create([
                'master_file_id' => $file->id,
                'status' => 'pending',
                'site' => $file->location ?? null,
                'payment' => null,
                'material' => null,
                'artwork' => null,
                'received_approval' => null,
                'sent_to_printer' => null,
                'collection_printer' => null,
                'installation' => null,
                'dismantle' => null,
                'remarks' => null,
                'next_follow_up' => null,
            ]);
            $createdCount++;
        }

        if ($createdCount > 0) {
            return redirect()->route('coordinator.outdoor.index')
                           ->with('success', "Created {$createdCount} tracking records from Master Files!");
        }

        return redirect()->route('coordinator.outdoor.index')
                       ->with('info', 'No new outdoor master files found to create tracking records.');
    }

    public function upsert(Request $req)
    {
        $data = $req->validate([
            'master_file_id' => 'required|integer|exists:master_files,id',
            'year'           => 'required|integer|min:2000|max:2100',
            'month'          => 'required|integer|min:1|max:12',

            // include OUTDOOR here (and any others you actually support)
            'category'       => 'required|string|in:OUTDOOR,MEDIA,PUBLICATION,KLTG',

            // ✨ allow the new keys you want to store
            'field_key'      => 'required|string|in:STATUS,INSTALLED_ON,REMARK',

            'field_type'     => 'required|string|in:text,date',
            'value'          => 'nullable|string',
        ]);
        // normalize boolean checkboxes for months
        $boolFields = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec',
                       'payment','material','artwork','received_approval','sent_to_printer','collection_printer'];

        if (in_array($data['field'], $boolFields, true)) {
            $data['value'] = filter_var($data['value'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        }

        $row = OutdoorCoordinatorTracking::updateOrCreate(
            [
                'master_file_id' => $data['master_file_id'],
                'year'           => $data['year'],
            ],
            [
                $data['field'] => $data['value'],
            ]
        );

        return response()->json(['ok' => true, 'id' => $row->id]);
    }

    public function export()
    {

        Log::info('HIT OUTDOOR EXPORT');
        $data = OutdoorCoordinatorTracking::with('masterFile')
            ->whereHas('masterFile', function ($query) {
                $query->whereIn('product_category', ['HM', 'TB', 'TTM', 'BB', 'Star', 'Flyers', 'Bunting', 'Signages'])
                      ->orWhere('product_category', 'LIKE', '%outdoor%')
                      ->orWhere('product_category', 'Outdoor');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $csv = "ID,Client,Product,Site,Payment,Material,Artwork,Approval Date,Sent Date,Collection Date,Install Date,Dismantle Date,Status,Remarks,Next Follow Up\n";

        foreach ($data as $item) {
            $csv .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s\n",
                $item->id,
                $this->escapeCsv(optional($item->masterFile)->client ?? ''),
                $this->escapeCsv(optional($item->masterFile)->product ?? ''),
                $this->escapeCsv($item->site ?? ''),
                $this->escapeCsv($item->payment ?? ''),
                $this->escapeCsv($item->material ?? ''),
                $this->escapeCsv($item->artwork ?? ''),
                $item->received_approval ? $item->received_approval->format('Y-m-d') : '',
                $item->sent_to_printer ? $item->sent_to_printer->format('Y-m-d') : '',
                $item->collection_printer ? $item->collection_printer->format('Y-m-d') : '',
                $item->installation ? $item->installation->format('Y-m-d') : '',
                $item->dismantle ? $item->dismantle->format('Y-m-d') : '',
                ucfirst($item->status ?? 'pending'),
                $this->escapeCsv($item->remarks ?? ''),
                $item->next_follow_up ? $item->next_follow_up->format('Y-m-d') : ''
            );
        }

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="outdoor_tracking_' . date('Y-m-d') . '.csv"',
        ]);
    }

    private function escapeCsv($value)
    {
        if (strpos($value, ',') !== false || strpos($value, '"') !== false || strpos($value, "\n") !== false) {
            return '"' . str_replace('"', '""', $value) . '"';
        }
        return $value;
    }

    /**
     * Quick create tracking from master file (AJAX)
     */
    public function quickCreateFromMasterFile($masterFileId)
    {
        try {
            $masterFile = MasterFile::findOrFail($masterFileId);

            // Check if tracking already exists
            $existing = OutdoorCoordinatorTracking::where('master_file_id', $masterFileId)->first();
            if ($existing) {
                return response()->json(['error' => 'Tracking already exists for this master file'], 400);
            }

            $tracking = OutdoorCoordinatorTracking::create([
                'master_file_id' => $masterFileId,
                'site' => $masterFile->location,
                'status' => 'pending'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tracking created successfully',
                'tracking_id' => $tracking->id
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get available master files for API calls
     */
    public function getAvailableMasterFiles()
    {
        $masterFiles = MasterFile::where(function($query) {
            $query->whereIn('product_category', ['HM', 'TB', 'TTM', 'BB', 'Star', 'Flyers', 'Bunting', 'Signages'])
                  ->orWhere('product_category', 'LIKE', '%outdoor%')
                  ->orWhere('product_category', 'Outdoor');
        })
        ->whereNotExists(function($query) {
            $query->select(DB::raw(1))
                  ->from('outdoor_coordinator_trackings')
                  ->whereRaw('outdoor_coordinator_trackings.master_file_id = master_files.id');
        })
        ->select('id', 'client', 'product', 'product_category', 'location')
        ->orderBy('client')
        ->get();

        return response()->json($masterFiles);
    }
}
