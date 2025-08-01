<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\MasterFile;
use App\Imports\MasterFileImport;
use Carbon\Carbon;

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

        return view('dashboard', compact(
            'masterFiles',
            'totalJobs',
            'completedJobs',
            'ongoingJobs',
            'pendingJobs',
            'grouped',
            'recentJobs'
        ));
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

    // 🔧 FIXED: Single store method (removed duplicate)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'month'        => 'required|string',
            'date'         => 'required|date',
            'company'      => 'required|string',
            'product'      => 'required|in:HM,TB,TTM,BB,Star,KLTG,Flyers,Bunting,KLTG listing,KLTG quarter page,Signages,FB IG Ad',
            'traffic'      => 'required|string',
            'duration'     => 'required|string',
            'status'       => 'required|string',
            'client'       => 'required|string',
            'date_finish'  => 'nullable|date',
            'job_number'   => 'nullable|string',
            'artwork'      => 'nullable|in:BGOC,Client',
            'invoice_date' => 'nullable|date',
            'invoice_number' => 'nullable|string'
        ]);

        MasterFile::create($validated);

        return redirect()->route('dashboard')->with('success', 'Master File data added successfully!');
    }



    // 🔧 ADD this method to your MasterFileController.php

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'company'      => 'required|string|max:255',
            'month'        => 'required|string|max:50',
            'date'         => 'required|date',
            'product'      => 'required|in:HM,TB,TTM,BB,Star,KLTG,Flyers,Bunting,KLTG listing,KLTG quarter page,Signages,FB IG Ad',
            'traffic'      => 'required|string|max:255',
            'duration'     => 'required|string|max:255',
            'status'       => 'required|in:pending,ongoing,completed',
            'client'       => 'required|string|max:255',
            'date_finish'  => 'nullable|date',
            'job_number'   => 'nullable|string|max:255',
            'artwork'      => 'nullable|in:BGOC,Client',
            'invoice_date' => 'nullable|date',
            'invoice_number' => 'nullable|string|max:255',
            'remarks'      => 'nullable|string',
            'location'     => 'nullable|string|max:255',
        ]);

        $masterFile = MasterFile::findOrFail($id);

        // Update all main fields
        $masterFile->update($validated);

        // Handle monthly checkboxes
        $months = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];

        foreach ($months as $month) {
            $checkboxName = "check_$month";
            // If checkbox is checked, set to 1, otherwise set to 0
            $masterFile->{$checkboxName} = $request->has($checkboxName) ? 1 : 0;
        }

        $masterFile->save();

        return redirect()->route('masterfile.show', $id)->with('success', 'All information updated successfully!');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls|max:2048',
        ]);

        try {
            $file = $request->file('file');
            Log::info('Import hit');
            Excel::import(new MasterFileImport, $file);
            return back()->with('success', 'Data imported successfully!');
        } catch (Exception $e) {
            Log::error('Import failed: ' . $e->getMessage());
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function exportCsv(Request $request)
    {
        try {
            $query = MasterFile::query();
            if ($request->has('status')) $query->where('status', $request->status);
            if ($request->has('product')) $query->where('product', $request->product);
            if ($request->has('month')) $query->where('month', $request->month);
            $masterFiles = $query->get();

            $timestamp = now()->format('Y_m_d_H_i_s');
            $csvFileName = "master_files_{$timestamp}.csv";

            return response()->stream(function() use ($masterFiles) {
                $handle = fopen('php://output', 'w');
                fwrite($handle, "\xEF\xBB\xBF");
                fputcsv($handle, [
                    'ID','Month','Date','Company','Product','Traffic','Duration','Status','Client','Date Finish','Job Number','Artwork','Invoice Date','Invoice Number','Created At','Updated At'
                ]);
                foreach ($masterFiles as $file) {
                    fputcsv($handle, [
                        $file->id, $file->month, $file->date, $file->company, $file->product, $file->traffic,
                        $file->duration, $file->status, $file->client, $file->date_finish, $file->job_number,
                        $file->artwork, $file->invoice_date, $file->invoice_number,
                        optional($file->created_at)->format('Y-m-d H:i:s'),
                        optional($file->updated_at)->format('Y-m-d H:i:s')
                    ]);
                }
                fclose($handle);
            }, 200, [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$csvFileName}\"",
                'Cache-Control' => 'no-store, no-cache',
                'Pragma' => 'no-cache'
            ]);

        } catch (Exception $e) {
            Log::error('Export failed: ' . $e->getMessage());
            return back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

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

        // Handle monthly checkboxes
        $months = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];

        foreach ($months as $month) {
            $checkboxName = "check_$month";
            // If checkbox is checked, set to 1, otherwise set to 0
            $masterFile->{$checkboxName} = $request->has($checkboxName) ? 1 : 0;
        }

        $masterFile->save();

        return redirect()->route('masterfile.show', $id)->with('success', 'Information updated successfully!');
    }

    public function monthlyJob()
    {
        $files = MasterFile::orderBy('date')->get();
        return view('monthly_jobs.index', compact('files'));
    }

    public function updateMonthlyJob(Request $request, $id)
    {
        $months = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];
        $validated = $request->only(array_map(fn($m) => "check_$m", $months));
        $validated['remarks'] = $request->remarks;
        MasterFile::where('id', $id)->update($validated);
        return back()->with('success', 'Updated successfully.');
    }

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



    public function updateTimeline(Request $request, $id)
    {
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

}
