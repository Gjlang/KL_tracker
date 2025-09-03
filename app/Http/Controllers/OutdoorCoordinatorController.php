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
    // ===== 1) Month & Year (supports 1..12 or "January") =====
    $rawMonth = $request->input('month'); // 1..12 or "January"
    $month = null;
    if ($rawMonth !== null && $rawMonth !== '') {
        if (ctype_digit((string)$rawMonth)) {
            $month = max(1, min(12, (int)$rawMonth));
        } else {
            try { $month = Carbon::parse('1 '.$rawMonth)->month; } catch (\Throwable $e) { $month = null; }
        }
    }
    $year = $request->integer('year') ?: now()->year;

    // ===== 2) Months dropdown =====
    $months = [];
    for ($i = 1; $i <= 12; $i++) {
        $months[] = ['value' => $i, 'label' => Carbon::create()->month($i)->format('F')];
    }

    // ===== 3) Resolve a monthly-details source for OUTDOOR only =====
    // Prefer a true outdoor table; only accept generic monthly_details if it has a 'category' we can filter.
    $mdTable = null;
    $mdHasCategory = false;

    if (Schema::hasTable('outdoor_monthly_details')) {
        $mdTable = 'outdoor_monthly_details';
    } elseif (Schema::hasTable('monthly_details')) {
        $mdTable = 'monthly_details';
        $mdHasCategory = Schema::hasColumn('monthly_details', 'category');
    }
    // IMPORTANT: Do NOT use kltg_monthly_details for Outdoor

    // ===== 4) Collect master_file_id for the selected month (Outdoor only) =====
    $monthlyIds = collect();

    if ($month) {
        if ($mdTable) {
            // Use monthly-details source
            $q = DB::table($mdTable)
                ->join('master_files', $mdTable.'.master_file_id', '=', 'master_files.id')
                ->where(function ($qq) {
                    $qq->where('master_files.product_category', 'Outdoor')
                       ->orWhere('master_files.product_category', 'like', '%outdoor%');
                })
                ->where($mdTable.'.month', $month)
                ->where($mdTable.'.year',  $year)
                ->where(function ($w) use ($mdTable) {
                    $w->whereNotNull($mdTable.'.value_date')
                      ->orWhereNotNull($mdTable.'.value_text')
                      ->orWhere($mdTable.'.value_text', '!=', '');
                });

            // If using generic monthly_details, require OUTDOOR category
            if ($mdTable === 'monthly_details' && $mdHasCategory) {
                $q->where($mdTable.'.category', 'OUTDOOR');
            }

            $monthlyIds = $q->pluck($mdTable.'.master_file_id')->unique()->values();
        } else {
            // Fallback: no outdoor-capable monthly table → use date overlap from master_files
            $start = Carbon::create($year, $month, 1)->startOfDay();
            $end   = (clone $start)->endOfMonth()->endOfDay();

            $monthlyIds = MasterFile::query()
                ->where(function ($qq) {
                    $qq->where('product_category', 'Outdoor')
                       ->orWhere('product_category', 'like', '%outdoor%');
                })
                // overlap: start <= monthEnd && (finish or start) >= monthStart
                ->where(function ($mm) use ($start, $end) {
                    $mm->whereDate('date', '<=', $end)
                       ->whereDate(DB::raw('COALESCE(date_finish, date)'), '>=', $start);
                })
                // optional fallback if you still keep text month in master_files.month
                ->orWhere(function ($mm) use ($month) {
                    $mm->where(function ($qq) {
                            $qq->where('product_category', 'Outdoor')
                               ->orWhere('product_category', 'like', '%outdoor%');
                        })
                       ->whereNotNull('month')
                       ->whereRaw('MONTH(STR_TO_DATE(TRIM(month), "%M")) = ?', [$month]);
                })
                ->pluck('id')
                ->unique()
                ->values();
        }
    }

    // ===== 5) AUTO-SYNC: ensure coordinator rows exist for those master_file_id =====
    if ($month && $monthlyIds->isNotEmpty()) {
        $existing = DB::table('outdoor_coordinator_trackings')
            ->whereIn('master_file_id', $monthlyIds)
            ->pluck('master_file_id')
            ->all();

        $missing = $monthlyIds->diff($existing)->values();

        if ($missing->isNotEmpty()) {
            $now = now();
            $payload = $missing->map(fn($id) => [
                'master_file_id' => $id,
                'status'         => 'pending',
                'created_at'     => $now,
                'updated_at'     => $now,
            ])->all();

            DB::table('outdoor_coordinator_trackings')->insert($payload);
        }
    }

    // ===== 6) Base query: Outdoor only; month filter by monthlyIds when set =====
    $base = OutdoorCoordinatorTracking::query()
        ->with('masterFile')
        ->whereHas('masterFile', function ($q) {
            $q->where(function ($qq) {
                $qq->where('product_category', 'Outdoor')
                   ->orWhere('product_category', 'like', '%outdoor%');
            });
        });

    if ($month) {
        if ($monthlyIds->isNotEmpty()) {
            $base->whereIn('master_file_id', $monthlyIds);
        } else {
            // If the month truly has no rows, show none (keeps it consistent with Monthly view)
            $base->whereRaw('1=0');
        }
    }

    // ===== 7) Order & paginate =====
    // Keep order by first-created so "siapa yang masuk duluan" stays on top.
    $rows = (clone $base)
        ->orderBy('outdoor_coordinator_trackings.created_at', 'asc')
        ->paginate(20)
        ->appends($request->query());

    return view('coordinators.outdoor', [
        'rows'    => $rows,
        'months'  => $months,
        'month'   => $month,
        'year'    => $year,
        'mdTable' => $mdTable ?: 'dates_fallback', // small debug badge if you want
    ]);
}



private function masterFilesForMonth(?int $month, ?int $year)
{
    $q = \App\Models\MasterFile::query()
        ->where(function ($qq) {
            $qq->where('product_category', 'Outdoor')
               ->orWhere('product_category', 'like', '%outdoor%');
        });

    if ($month && $year) {
        $start = Carbon::create($year, $month, 1)->startOfDay();
        $end   = (clone $start)->endOfMonth()->endOfDay();

        // Overlap logic (start <= end && finish >= start)
        $q->where(function ($mm) use ($start, $end) {
            $mm->whereDate('date', '<=', $end)
               ->whereDate(DB::raw('COALESCE(date_finish, date)'), '>=', $start);
        })
        // Fallback if you still use text month in master_files.month
        ->orWhere(function ($mm) use ($month) {
            $mm->whereNotNull('month')
               ->whereRaw('MONTH(STR_TO_DATE(TRIM(month), "%M")) = ?', [$month]);
        });
    }

    return $q->select('id'); // we only need IDs
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
