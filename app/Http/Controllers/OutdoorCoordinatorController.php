<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterFile;
use App\Models\OutdoorCoordinatorTracking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse; // add at top
use Illuminate\Foundation\Configuration\Exceptions;

class OutdoorCoordinatorController  extends Controller
{

public function index(Request $request)
{
    $debug     = $request->boolean('debug');
    $nofilter  = $request->boolean('nofilter');
    $forceId   = $request->input('force_id'); // e.g. 37

    $month = (int) ($request->input('month') ?: now()->month);
    $year  = (int) ($request->input('year')  ?: now()->year);

    // 1) Selected IDs for (year, month)
    $selectedMasterIds = DB::table('outdoor_monthly_details')
        ->where('year',  $year)
        ->where('month', $month)
        ->distinct()
        ->pluck('master_file_id')
        ->map(fn ($v) => (int) $v)
        ->unique()
        ->values();

    // Allow force override for sanity check
    if ($forceId) {
        $selectedMasterIds = collect([(int)$forceId]);
    }

    // 2) Base query
    $base = MasterFile::query()
        ->from('master_files as mf')
        ->leftJoin('outdoor_coordinator_trackings as t', 't.master_file_id', '=', 'mf.id');

    // 3) Outdoor filter (skip when nofilter=1)
    if (!$nofilter) {
        $outdoorCodes  = ['TB','BB','NEWSPAPER','BUNTING','FLYERS','STAR','SIGNAGES'];
        $outdoorLabels = ['TB - Tempboard','BB - Billboard','Newspaper','Bunting','Flyers','Star','Signages'];

        $base->where(function ($q) use ($outdoorCodes, $outdoorLabels) {
            $q->whereRaw('LOWER(mf.product) = ?', ['outdoor'])
              ->orWhereRaw('LOWER(mf.product) LIKE ?', ['%outdoor%'])
              ->orWhereRaw('LOWER(mf.product_category) LIKE ?', ['%outdoor%'])
              ->orWhereIn('mf.product_category', $outdoorCodes)
              ->orWhereIn('mf.product_category', $outdoorLabels)
              ->orWhereRaw('LEFT(mf.product_category, 2) IN ("TB","BB")');
        });
    }

    // 4) Selected-only
    if ($selectedMasterIds->isNotEmpty()) {
        $base->whereIn('mf.id', $selectedMasterIds->all());
    } else {
        $base->whereRaw('1=0');
    }

    // 5) Select fields
    $records = $base->select([
            'mf.id as master_file_id',
            'mf.company','mf.client','mf.product','mf.product_category','mf.location',
            't.id as tracking_id','t.site','t.payment','t.material','t.artwork',
            't.received_approval','t.sent_to_printer','t.collection_printer','t.installation',
            't.dismantle','t.remarks','t.next_follow_up','t.status',
            't.created_at as tracking_created_at',
        ])
        ->orderByRaw('LOWER(mf.company) asc')
        ->paginate(20)
        ->appends($request->query());

    if ($debug) {
        Log::info('Outdoor@index DEBUG', [
            'year' => $year,
            'month' => $month,
            'nofilter' => $nofilter,
            'force_id' => $forceId,
            'selected_ids' => $selectedMasterIds->all(),
            'page_rows' => $records->pluck('master_file_id')->all(),
        ]);
    }

    // months for dropdown
    $months = [];
    for ($i = 1; $i <= 12; $i++) {
        $months[] = ['value' => $i, 'label' => Carbon::create()->month($i)->format('F')];
    }

    return view('coordinators.outdoor', [
        'rows'          => $records,
        'months'        => $months,
        'month'         => $month,
        'year'          => $year,
        'selectedCount' => $selectedMasterIds->count(),
        'debug'         => $debug,
        'selectedIds'   => $selectedMasterIds,
        'nofilter'      => $nofilter,
        'forceId'       => $forceId,
    ]);
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
     * 🔥 UPDATED: AJAX Update Field for Inline Editing - Enhanced version
     */
    public function updateField(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:outdoor_coordinator_trackings,id',
                'field' => 'required|string',
                'value' => 'nullable|string'
            ]);

            $job = OutdoorCoordinatorTracking::with('masterFile')->findOrFail($validated['id']);
            $field = $validated['field'];
            $value = $validated['value'];

            // Handle month checkboxes that need to go to master_files table
            $masterFields = [
                'check_jan', 'check_feb', 'check_mar', 'check_apr',
                'check_may', 'check_jun', 'check_jul', 'check_aug',
                'check_sep', 'check_oct', 'check_nov', 'check_dec',
            ];

            if (in_array($field, $masterFields)) {
                if ($job->masterFile) {
                    // Convert value to boolean for checkboxes
                    $boolValue = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
                    $job->masterFile->{$field} = $boolValue;
                    $job->masterFile->save();
                    return response()->json(['success' => true, 'value' => $boolValue]);
                } else {
                    return response()->json(['error' => 'No master file found.'], 404);
                }
            }

            // Fields that go to outdoor_coordinator_trackings table
            $allowedFields = [
                'site', 'payment', 'material', 'artwork', 'received_approval',
                'sent_to_printer', 'collection_printer', 'installation',
                'dismantle', 'remarks', 'next_follow_up', 'status'
            ];

            if (!in_array($field, $allowedFields)) {
                return response()->json(['error' => 'Field not allowed for editing.'], 400);
            }

            // Handle date fields
            $dateFields = [
                'received_approval', 'sent_to_printer', 'collection_printer',
                'installation', 'dismantle', 'next_follow_up'
            ];

            if (in_array($field, $dateFields) && !empty($value)) {
                // Validate date format
                $date = \DateTime::createFromFormat('Y-m-d', $value);
                if (!$date || $date->format('Y-m-d') !== $value) {
                    return response()->json(['error' => 'Invalid date format. Use YYYY-MM-DD.'], 400);
                }
            }

            // Handle status field validation
            if ($field === 'status' && !in_array($value, ['pending', 'ongoing', 'completed', null])) {
                return response()->json(['error' => 'Invalid status value.'], 400);
            }

            // Update the field
            $job->{$field} = $value;
            $job->save();

            // Auto-update status based on progress if we're not directly updating status
            if ($field !== 'status') {
                $newStatus = $this->calculateStatus($job);
                if ($newStatus !== $job->status) {
                    $job->status = $newStatus;
                    $job->save();
                }
            }

            return response()->json([
                'success' => true,
                'value' => $value,
                'status' => $job->status // Return updated status
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            Log::error('updateField error', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Server error occurred.'], 500);
        }
    }

    /**
     * Calculate status based on progress
     */
    private function calculateStatus($job)
    {
        if (!empty($job->dismantle)) {
            return 'completed';
        } elseif (!empty($job->installation)) {
            return 'ongoing';
        } else {
            return 'pending';
        }
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
                    'status'         => 'pending',
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
            $query->whereIn('product_category', ['HM', 'TB', 'TTM', 'BB', 'Star', 'Flyers', 'Bunting', 'Signages', 'Newspaper'])
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
            $query->whereIn('product_category', ['HM', 'TB', 'TTM', 'BB', 'Star', 'Flyers', 'Bunting', 'Signages', 'Newspaper'])
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
            $query->whereIn('product_category', ['HM', 'TB', 'TTM', 'BB', 'Star', 'Flyers', 'Bunting', 'Signages', 'Newspaper'])
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
            $query->whereIn('product_category', ['HM', 'TB', 'TTM', 'BB', 'Star', 'Flyers', 'Bunting', 'Signages', 'Newspaper'])
                  ->orWhere('product_category', 'LIKE', '%outdoor%')
                  ->orWhere('product_category', 'Outdoor');
        })
        ->orderBy('client')
        ->get();

        return view('coordinator.outdoor.edit', compact('tracking', 'masterFiles'));
    }

    /**
     * 🔥 FIXED: Update method now handles both full forms and AJAX partial updates
     */
    public function update(Request $request, $id)
    {
        $tracking = OutdoorCoordinatorTracking::findOrFail($id);

        // Use 'sometimes' validation rules to allow partial updates
        $rules = [
            'master_file_id'     => 'sometimes|required|exists:master_files,id',
            'site'               => 'sometimes|nullable|string|max:255',
            'payment'            => 'sometimes|nullable|string|max:100',
            'material'           => 'sometimes|nullable|string|max:100',
            'artwork'            => 'sometimes|nullable|string|max:100',
            'received_approval'  => 'sometimes|nullable|date',
            'sent_to_printer'    => 'sometimes|nullable|date',
            'collection_printer' => 'sometimes|nullable|date',
            'installation'       => 'sometimes|nullable|date',
            'dismantle'          => 'sometimes|nullable|date',
            'remarks'            => 'sometimes|nullable|string',
            'next_follow_up'     => 'sometimes|nullable|date',
            'status'             => 'sometimes|nullable|in:pending,ongoing,completed'
        ];

        $validated = $request->validate($rules);

        // Auto-update status based on progress if status not explicitly provided
        if (!isset($validated['status'])) {
            $tracking->fill($validated); // Fill with new data to check latest state

            if (!empty($tracking->dismantle)) {
                $validated['status'] = 'completed';
            } elseif (!empty($tracking->installation)) {
                $validated['status'] = 'ongoing';
            } else {
                $validated['status'] = 'pending';
            }
        }

        $tracking->update($validated);

        // Return appropriate response based on request type
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Record updated successfully',
                'data' => $tracking->load('masterFile')
            ]);
        }

        return redirect()->route('coordinator.outdoor.index')
                        ->with('success', 'Outdoor tracking record updated successfully!');
    }

    /**
     * 🔥 ENHANCED: Better inline update method
     */
    public function updateInline(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:outdoor_coordinator_trackings,id',
                'field' => 'required|string',
                'value' => 'nullable|string',
            ]);

            $job = OutdoorCoordinatorTracking::with('masterFile')->findOrFail($validated['id']);

            // Define allowed fields for inline editing
            $allowedFields = [
                'site', 'payment', 'material', 'artwork', 'remarks', 'status',
                'received_approval', 'sent_to_printer', 'collection_printer',
                'installation', 'dismantle', 'next_follow_up'
            ];

            if (!in_array($validated['field'], $allowedFields)) {
                return response()->json(['success' => false, 'error' => 'Invalid field']);
            }

            // Handle date validation
            $dateFields = [
                'received_approval', 'sent_to_printer', 'collection_printer',
                'installation', 'dismantle', 'next_follow_up'
            ];

            if (in_array($validated['field'], $dateFields) && !empty($validated['value'])) {
                $date = \DateTime::createFromFormat('Y-m-d', $validated['value']);
                if (!$date || $date->format('Y-m-d') !== $validated['value']) {
                    return response()->json(['success' => false, 'error' => 'Invalid date format']);
                }
            }

            // Handle status validation
            if ($validated['field'] === 'status' && !in_array($validated['value'], ['pending', 'ongoing', 'completed', null])) {
                return response()->json(['success' => false, 'error' => 'Invalid status']);
            }

            $job->{$validated['field']} = $validated['value'];
            $job->save();

            // Auto-update status if we modified a progress field
            if ($validated['field'] !== 'status') {
                $newStatus = $this->calculateStatus($job);
                if ($newStatus !== $job->status) {
                    $job->status = $newStatus;
                    $job->save();
                }
            }

            return response()->json([
                'success' => true,
                'status' => $job->status,
                'value' => $validated['value']
            ]);

        } catch (\Exception $e) {
            Log::error('updateInline error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => 'Server error']);
        }
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
            $query->whereIn('product_category', ['HM', 'TB', 'TTM', 'BB', 'Star', 'Flyers', 'Bunting', 'Signages', 'Newspaper'])
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

    public function upsert(Request $request)
{


    try {
        $data = $request->validate([
            'id'    => 'required|integer|exists:outdoor_coordinator_trackings,id',
            'field' => 'required|string',
            'value' => 'nullable|string'
        ]);

        $job = OutdoorCoordinatorTracking::findOrFail($data['id']);
        $field = $data['field'];
        $value = $data['value'];

        // Whitelist allowed fields sesuai DB schema
        $allowedFields = [
            'site', 'payment', 'material', 'artwork', 'received_approval',
            'sent_to_printer', 'collection_printer', 'installation',
            'dismantle', 'remarks', 'next_follow_up', 'status'
        ];

        if (!in_array($field, $allowedFields)) {
            return response()->json(['error' => 'Field not allowed'], 400);
        }

        // Normalize date fields
        $dateFields = [
            'received_approval', 'sent_to_printer', 'collection_printer',
            'installation', 'dismantle', 'next_follow_up'
        ];

        if (in_array($field, $dateFields) && !empty($value)) {
            try {
                $value = date('Y-m-d', strtotime($value));
            } catch (\Throwable $e) {
                return response()->json(['error' => 'Invalid date format'], 400);
            }
        }

        // Update field
        $job->{$field} = $value;
        $job->save();

        return response()->json(['success' => true, 'id' => $job->id]);

    } catch (\Throwable $e) {
        Log::error('Upsert error: ' . $e->getMessage());
        return response()->json(['error' => 'Server error'], 500);
    }
 }

    /**
     * Get available master files for API calls
     */
    public function getAvailableMasterFiles()
    {
        $masterFiles = MasterFile::where(function($q) {
        $q->where('product_category', 'Outdoor')
          ->orWhere('product_category', 'LIKE', '%outdoor%');
    })
    ->whereNotExists(function($query) {
        $query->select(DB::raw(1))
              ->from('outdoor_coordinator_trackings')
              ->whereRaw('outdoor_coordinator_trackings.master_file_id = master_files.id');
    })
    ->select('id','client','product','product_category','location')
    ->orderBy('client')
    ->get();
        return response()->json($masterFiles);
    }


    public function export(Request $request): StreamedResponse
{
    // ---- DEBUG: Log untuk melihat apa yang terjadi ----
    Log::info('Export started', [
        'month_requested' => $request->integer('month'),
        'all_params' => $request->all()
    ]);

    // ---- 1) Get month filter ----
    $month = $request->integer('month'); // 1-12

    // ---- DEBUG: Cek total data di tabel ----
    $totalRows = DB::table('outdoor_coordinator_trackings')->count();
    Log::info("Total rows in outdoor_coordinator_trackings: {$totalRows}");

    // ---- 2) Build query dengan JOIN ke master_files ----
    $q = DB::table('outdoor_coordinator_trackings as oct')
        ->join('master_files as mf', 'oct.master_file_id', '=', 'mf.id')
        ->select([
            'oct.id',
            'oct.master_file_id',
            'mf.client',           // ✅ Ambil dari master_files
            'mf.product',          // ✅ Ambil dari master_files
            'oct.site',
            'oct.payment',
            'oct.material',
            'oct.artwork',
            'oct.received_approval',
            'oct.sent_to_printer',
            'oct.collection_printer',
            'oct.installation',
            'oct.dismantle',
            'oct.remarks',
            'oct.next_follow_up',
            'oct.status',
        ]);

    // ---- 3) Month filtering ----
    if ($month) {
        Log::info("Filtering by month: {$month}");

        // Filter berdasarkan date fields yang ada di outdoor_coordinator_trackings
        $q->where(function ($query) use ($month) {
            $query->whereRaw("MONTH(oct.received_approval) = ?", [$month])
                  ->orWhereRaw("MONTH(oct.sent_to_printer) = ?", [$month])
                  ->orWhereRaw("MONTH(oct.collection_printer) = ?", [$month])
                  ->orWhereRaw("MONTH(oct.installation) = ?", [$month])
                  ->orWhereRaw("MONTH(oct.dismantle) = ?", [$month]);
        });

        // DEBUG: Cek berapa yang match filter
        $filteredCount = $q->count();
        Log::info("Rows matching month filter: {$filteredCount}");
    }

    $rows = $q->orderBy('oct.id')->get();

    // ---- DEBUG: Log hasil query ----
    Log::info("Final query returned rows: " . count($rows));
    if (count($rows) > 0) {
        Log::info("Sample first row: ", (array) $rows[0]);
    }

    // ---- 4) Jika tidak ada data dengan filter, export semua data ----
    if ($rows->isEmpty()) {
        Log::info("No filtered data found, exporting ALL data");
        $rows = DB::table('outdoor_coordinator_trackings as oct')
            ->join('master_files as mf', 'oct.master_file_id', '=', 'mf.id')
            ->select([
                'oct.id', 'oct.master_file_id', 'mf.client', 'mf.product', 'oct.site',
                'oct.payment', 'oct.material', 'oct.artwork', 'oct.received_approval',
                'oct.sent_to_printer', 'oct.collection_printer', 'oct.installation',
                'oct.dismantle', 'oct.remarks', 'oct.next_follow_up', 'oct.status'
            ])
            ->orderBy('oct.id')
            ->get();

        Log::info("All data export returned rows: " . count($rows));
    }

    // ---- 5) Generate CSV ----
    $monthName = $month ? "month-{$month}" : 'all';
    $filename = "outdoor-coordinator-{$monthName}.csv";

    $headers = [
        'Content-Type'        => 'text/csv; charset=UTF-8',
        'Content-Disposition' => 'attachment; filename="'.$filename.'"',
    ];

    $csvHeaders = [
        'ID', 'Master File ID', 'Client', 'Product', 'Site',
        'Payment', 'Material', 'Artwork', 'Received Approval',
        'Sent to Printer', 'Collection Printer', 'Installation',
        'Dismantle', 'Remarks', 'Next Follow Up', 'Status'
    ];

    $dbColumns = [
        'id', 'master_file_id', 'client', 'product', 'site',
        'payment', 'material', 'artwork', 'received_approval',
        'sent_to_printer', 'collection_printer', 'installation',
        'dismantle', 'remarks', 'next_follow_up', 'status'
    ];

    return response()->streamDownload(function () use ($rows, $csvHeaders, $dbColumns) {
        $out = fopen('php://output', 'w');

        // BOM for Excel
        fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));

        // Write headers
        fputcsv($out, $csvHeaders);

        // Write data
        foreach ($rows as $row) {
            $line = [];
            foreach ($dbColumns as $col) {
                $value = $row->$col ?? '';

                // Format dates
                if (in_array($col, ['received_approval', 'sent_to_printer', 'collection_printer', 'installation', 'dismantle', 'next_follow_up'])) {
                    if ($value && !in_array($value, ['0000-00-00', '0000-00-00 00:00:00', '', null])) {
                        try {
                            $value = date('Y-m-d', strtotime($value));
                        } catch (\Throwable $e) {
                            // Keep original
                        }
                    } else {
                        $value = '';
                    }
                }
                $line[] = $value;
            }
            fputcsv($out, $line);
        }

        fclose($out);
    }, $filename, $headers);
}

}
