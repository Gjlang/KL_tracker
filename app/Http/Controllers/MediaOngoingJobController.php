<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MediaOngoingJob;
use App\Models\MasterFile;
use Illuminate\Support\Facades\Log;
use App\Models\OutdoorCoordinatorTracking;
use App\Models\MediaMonthlyDetail;
use Carbon\Carbon;

class MediaOngoingJobController extends Controller
{
    public function index(Request $request)
{
    $year  = (int)($request->input('year') ?: now()->year);
    $month = $request->filled('month') ? (int)$request->input('month') : null;

    // Ambil master files yang relevan Media
    $masters = MasterFile::query()
        ->where(function ($q) {
            $q->whereRaw('LOWER(product_category) like ?', ['%media%'])
              ->orWhereRaw('LOWER(product) like ?', ['%fb%'])
              ->orWhereRaw('LOWER(product) like ?', ['%ig%']);
        })
        ->orderByRaw('COALESCE(`date`, `created_at`) DESC')
        ->get();

    // Helper: ambil rows section tertentu dan jadikan map [master_id => [field=>value]]
    $buildMap = function (string $section, array $fields) use ($masters, $year, $month) {
        $model = \App\Models\MediaCoordinatorTracking::forSection($section);

        $q = $model::query()->whereIn('master_file_id', $masters->pluck('id'));
        // Scope filter: default Month+Year (sesuai UI kamu)
        $q->where('year', $year);
        if ($month) {
            $q->where('month', $month);
        }

        $rows = $q->get();
        $map  = [];
        foreach ($rows as $r) {
            $arr = [];
            foreach ($fields as $f) {
                $arr[$f] = $r->{$f};
            }
            $map[$r->master_file_id] = $arr;
        }
        return $map;
    };

    // Bangun map untuk tiap tab (field harus cocok dengan tabelnya)
    $contentMap = $buildMap('content',  ['total_artwork','pending','draft_wa','approved']);
    $editingMap = $buildMap('editing',  ['total_artwork','pending','draft_wa','approved']);
    $scheduleMap= $buildMap('schedule', ['total_artwork','crm','meta_mgr','tiktok_ig_draft']);
    $reportMap  = $buildMap('report',   ['pending','completed']);
    $valueMap   = $buildMap('valueadd', ['quota','completed']);

    // Label periode
    $periodLabel = $month
        ? Carbon::create()->month($month)->format('F') . " $year"
        : "All months $year";

    // Kirim ke view yang benar (file kamu ada di coordinators/media.blade.php)
    return view('coordinators.media', [
        'year'        => $year,
        'month'       => $month,
        'masters'     => $masters,
        'contentMap'  => $contentMap,
        'editingMap'  => $editingMap,
        'scheduleMap' => $scheduleMap,
        'reportMap'   => $reportMap,
        'valueMap'    => $valueMap,
        'activeTab'   => $request->input('tab', 'content'),
        'scope'       => $request->input('scope', 'month_year'),
        'periodLabel' => $periodLabel,
    ]);
}


    public function create()
    {
        return view('media_ongoing.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'master_file_id' => 'nullable|exists:master_files,id',
            'date'          => 'nullable|date',
            'company'       => 'required|string|max:255',
            'product'       => 'required|string|max:255',
            'category'      => 'nullable|string|max:100',
            'location'      => 'nullable|string|max:255',
            'start_date'    => 'nullable|date',
            'end_date'      => 'nullable|date',
            'jan' => 'nullable|string|max:50',
            'feb' => 'nullable|string|max:50',
            'mar' => 'nullable|string|max:50',
            'apr' => 'nullable|string|max:50',
            'may' => 'nullable|string|max:50',
            'jun' => 'nullable|string|max:50',
            'jul' => 'nullable|string|max:50',
            'aug' => 'nullable|string|max:50',
            'sep' => 'nullable|string|max:50',
            'oct' => 'nullable|string|max:50',
            'nov' => 'nullable|string|max:50',
            'dec' => 'nullable|string|max:50',
            'remarks'       => 'nullable|string'
        ]);

        MediaOngoingJob::create($validated);

        return redirect()->route('dashboard')->with('success', 'Media ongoing job created.');
}


    public function show($id)
    {
        $job = MediaOngoingJob::findOrFail($id);
        return view('media_ongoing.show', compact('job'));
    }

    public function edit($id)
    {
        $job = MediaOngoingJob::findOrFail($id);
        return view('media_ongoing.edit', compact('job'));
    }

    // app/Http/Controllers/MediaOngoingJobController.php

// In MediaOngoingJobController.php, replace your upsert method with this:

public function upsert(Request $request)
{
    try {
        Log::info('Upsert request received', $request->all());

        $data = $request->validate([
            'section'        => 'required|string|in:content,editing,schedule,report,valueadd',
            'master_file_id' => 'required|exists:master_files,id',
            'year'           => 'required|integer|min:2000|max:2100',
            'month'          => 'nullable|integer|min:1|max:12',
            'field'          => 'required|string',
            'value'          => 'nullable',
        ]);

        // Get model class
        $modelClass = \App\Models\MediaCoordinatorTracking::forSection($data['section']);

        // Validate field is allowed for this section
        $allowedFields = [
            'content'  => ['total_artwork', 'pending', 'draft_wa', 'approved'],
            'editing'  => ['total_artwork', 'pending', 'draft_wa', 'approved'],
            'schedule' => ['total_artwork', 'crm', 'meta_mgr', 'tiktok_ig_draft'],
            'report'   => ['pending', 'completed'],
            'valueadd' => ['quota', 'completed'],
        ];

        if (!in_array($data['field'], $allowedFields[$data['section']], true)) {
            return response()->json(['ok' => false, 'error' => 'Field not allowed'], 422);
        }

        // CRITICAL: Map UI field names to database column names
        $fieldMapping = [
            'meta_mgr' => 'meta_manager', // UI sends 'meta_mgr', DB expects 'meta_manager'
        ];
        $dbField = $fieldMapping[$data['field']] ?? $data['field'];

        // Build unique keys
        $keys = [
            'master_file_id' => (int)$data['master_file_id'],
            'year' => (int)$data['year'],
        ];

        if (!empty($data['month'])) {
            $keys['month'] = (int)$data['month'];
        }

        Log::info('Looking for record', ['model' => $modelClass, 'keys' => $keys, 'field' => $dbField]);

        // Find or create record
        $record = $modelClass::firstOrNew($keys);

        // Process value based on field type
        $value = $data['value'];

        // Boolean fields
        if (in_array($data['field'], ['draft_wa', 'approved', 'tiktok_ig_draft'], true)) {
            $value = (bool)$value ? 1 : 0;
        }
        // Integer fields
        elseif (in_array($data['field'], ['total_artwork', 'pending', 'crm', 'meta_mgr', 'quota'], true)) {
            $value = ($value === '' || $value === null) ? null : (int)$value;
        }
        // Special case: valueadd completed can be integer
        elseif ($data['section'] === 'valueadd' && $data['field'] === 'completed') {
            $value = is_numeric($value) ? (int)$value : ((bool)$value ? 1 : 0);
        }
        // Report completed is boolean
        elseif ($data['field'] === 'completed') {
            $value = (bool)$value ? 1 : 0;
        }

        // Set the field value using the mapped database field name
        $record->{$dbField} = $value;

        Log::info('Saving record', [
            'dbField' => $dbField,
            'value' => $value,
            'record_exists' => $record->exists
        ]);

        $saved = $record->save();

        if ($saved) {
            Log::info('Record saved successfully', ['id' => $record->id]);
            return response()->json([
                'ok' => true,
                'id' => $record->id,
                'field' => $dbField,
                'value' => $value
            ]);
        } else {
            Log::error('Failed to save record');
            return response()->json(['ok' => false, 'error' => 'Failed to save'], 500);
        }

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::warning('Validation error', ['errors' => $e->errors()]);
        return response()->json(['ok' => false, 'errors' => $e->errors()], 422);
    } catch (\Exception $e) {
        Log::error('Upsert failed: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'request' => $request->all()
        ]);
        return response()->json([
            'ok' => false,
            'error' => 'Server error: ' . $e->getMessage()
        ], 500);
    }
}


    public function update(Request $request, $id)
    {
        $job = MediaOngoingJob::findOrFail($id);

        $validated = $request->validate([
            'date' => 'required|date',
            'company' => 'required|string|max:255',
            'platform' => 'required|string|max:100',
            'content_type' => 'nullable|string|max:100',
            'campaign' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'nullable|string|in:active,paused,completed,cancelled',
            'remarks' => 'nullable|string'
        ]);

        $job->update($validated);

        return redirect()->route('dashboard')->with('success', 'Media social job updated successfully!');
    }

    public function destroy($id)
    {
        try {
            $job = MediaOngoingJob::findOrFail($id);
            $job->delete();

            return response()->json([
                'success' => true,
                'message' => 'Media social job deleted successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting media social job: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to delete job'
            ], 500);
        }
    }

    // ✅ UPDATED: Enhanced inline update method for all table types
    public function inlineUpdate(Request $request)
    {
        try {
            Log::info('🔥 Inline Update Request', $request->all());

            $type = $request->input('type', 'media'); // Default to media
            $id = $request->input('id');
            $field = $request->input('field');
            $value = $request->input('value');

            // Determine which model to update based on type
            switch ($type) {
                case 'media':
                    $job = MediaOngoingJob::findOrFail($id);
                    break;
                case 'master':
                case 'masterfile':
                    $job = MasterFile::findOrFail($id);
                    break;
                case 'outdoor':
                    $job = OutdoorCoordinatorTracking::findOrFail($id);
                    break;
                default:
                    // Try to find in MasterFile first, then MediaOngoingJob
                    $job = MasterFile::find($id) ?? MediaOngoingJob::findOrFail($id);
                    break;
            }

            // Validate field is allowed to be updated
            $allowedFields = [
                'remarks', 'status', 'company', 'platform', 'content_type', 'campaign',
                'jan', 'feb', 'mar', 'apr', 'may', 'jun',
                'jul', 'aug', 'sep', 'oct', 'nov', 'dec',
                'check_jan', 'check_feb', 'check_mar', 'check_apr', 'check_may', 'check_jun',
                'check_jul', 'check_aug', 'check_sep', 'check_oct', 'check_nov', 'check_dec',
                'lb', 'em', 'location', 'product', 'traffic', 'duration', 'client'
            ];

            if (!in_array($field, $allowedFields)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Field not allowed to be updated'
                ], 400);
            }

            // Update the field
            $job->$field = $value;
            $job->save();

            Log::info('✅ Field updated successfully', [
                'type' => $type,
                'id' => $id,
                'field' => $field,
                'value' => $value
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Field updated successfully',
                'data' => [
                    'id' => $job->id,
                    'field' => $field,
                    'value' => $value,
                    'updated_at' => $job->updated_at->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Inline Update failed', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    // 🔥 CRITICAL: AJAX Update method for inline editing
    public function updateField(Request $request, $id)
    {
        try {
            Log::info('🔥 AJAX Update triggered', [
                'id' => $id,
                'field' => $request->field,
                'value' => $request->value
            ]);

            $job = MediaOngoingJob::findOrFail($id);
            $field = $request->field;
            $value = $request->value;

            // Validate the field is allowed to be updated
            $allowedFields = [
                'jan', 'feb', 'mar', 'apr', 'may', 'jun',
                'jul', 'aug', 'sep', 'oct', 'nov', 'dec',
                'remarks', 'status', 'company', 'platform',
                'content_type', 'campaign', 'lb', 'em',
            ];

            if (!in_array($field, $allowedFields)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Field not allowed to be updated'
                ], 400);
            }

            // Update the field
            $job->$field = $value;
            $job->save();

            Log::info('✅ Field updated successfully', [
                'id' => $id,
                'field' => $field,
                'new_value' => $value
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Field updated successfully',
                'data' => [
                    'id' => $job->id,
                    'field' => $field,
                    'value' => $value,
                    'updated_at' => $job->updated_at->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('❌ AJAX Update failed', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Update failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
