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
    public function index()
    {
        $year = now()->year;

        // Ambil data master_files kategori MEDIA (longgar, biar gak 0 baris)
        $rows = MasterFile::query()
            ->where(function ($q) {
                $q->whereRaw('LOWER(product_category) like ?', ['%media%'])
                ->orWhereRaw('LOWER(product) like ?', ['%fb%'])
                ->orWhereRaw('LOWER(product) like ?', ['%ig%']);
            })
            ->orderByRaw('COALESCE(`date`, `created_at`) DESC')
            ->get();

        // Optional: detailsMap untuk isi sel yang sudah ada (kalau kamu render balik nilai)
        $details = MediaMonthlyDetail::whereIn('master_file_id', $rows->pluck('id'))
            ->get()
            ->groupBy(function($d){
                // key sederhana: "<master_file_id>|<year>|<subcategory>|<kind>"
                return implode('|', [
                    $d->master_file_id,
                    $d->year,
                    $d->subcategory,
                    $d->kind, // 'text' atau 'date'
                ]);
            });

        $detailsMap = [];
        foreach ($details as $key => $group) {
            // Ambil last value
            $last = $group->sortByDesc('updated_at')->first();
            $detailsMap[$key] = [
                'text' => $last->value_text,
                'date' => optional($last->value_date)->format('Y-m-d'),
            ];
        }

        return view('dashboard.media', [
            'year'       => $year,
            'rows'       => $rows,
            'detailsMap' => $detailsMap,
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

    public function upsertMonthlyDetail(Request $request)
{
    $data = $request->validate([
        'master_file_id' => 'required|exists:master_files,id',
        'year'           => 'required|integer|min:2000|max:2100',
        'month'          => 'required|integer|min:1|max:12',
        'kind'           => 'required|in:text,date',
        'value'          => 'nullable|string',
    ]);

    // If your table still has 'subcategory' NOT NULL, choose a default:
    $sub = 'General';

    $detail = \App\Models\MediaMonthlyDetail::firstOrNew([
        'master_file_id' => $data['master_file_id'],
        'year'           => $data['year'],
        'month'          => $data['month'],
        'subcategory'    => $sub,  // keep constant key
    ]);

    if ($data['kind'] === 'text') {
        $detail->value_text = $data['value'];
    } else { // date
        $detail->value_date = $data['value'] ? date('Y-m-d', strtotime($data['value'])) : null;
    }

    // ensure column exists if still NOT NULL
    $detail->subcategory = $detail->subcategory ?: $sub;

    $detail->save();

    return response()->json(['ok' => true]);
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
