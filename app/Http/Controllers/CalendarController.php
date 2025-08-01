<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        // Get filter parameters
        $section = $request->get('section');
        $myTasksOnly = $request->get('my_tasks_only', false);

        // Build query based on filters
        $query = Job::query();

        if ($section) {
            $query->where('section', $section);
        }

        if ($myTasksOnly) {
            $query->where('assigned_user_id', Auth::id());
        }

        $jobs = $query->get();

        // Get upcoming tasks for sidebar (next 7 days)
        $upcomingTasks = Job::where('start_date', '>', now())
            ->where('start_date', '<=', now()->addDays(7))
            ->orderBy('start_date')
            ->take(5)
            ->get();

        // Get sections for filter dropdown
        $sections = Job::distinct()->pluck('section')->filter();

        return view('calendar', compact('jobs', 'upcomingTasks', 'sections'));
    }

    public function getEvents(Request $request)
    {
        // Retrieve filter parameters
        $start = $request->get('start');
        $end = $request->get('end');
        $section = $request->get('section');
        $myTasksOnly = $request->get('my_tasks_only', false);
        $status = $request->get('status');

        // Query jobs based on date range filter
        $query = Job::query();

        // Apply date range filter
        $query->where(function($q) use ($start, $end) {
            $q->whereBetween('start_date', [$start, $end])
            ->orWhereBetween('end_date', [$start, $end])
            ->orWhere(function($subQ) use ($start, $end) {
                $subQ->where('start_date', '<=', $start)
                    ->where('end_date', '>=', $end);
            });
        });

        // Apply other filters
        if ($section) {
            $query->where('section', $section);
        }

        if ($myTasksOnly) {
            $query->where('assigned_user_id', Auth::id());
        }

        if ($status) {
            $query->where('status', $status);
        }

        $jobs = $query->with('assignedUser')->get();

        // Map jobs to events format
        $events = $jobs->map(function ($job) {
            return [
                'id' => $job->id,
                'title' => $job->company_name . ' - ' . $job->product,
                'start' => $job->start_date->toISOString(),  // Ensure the date is properly formatted
                'end' => $job->end_date->toISOString(),      // Ensure the date is properly formatted
                'backgroundColor' => $this->getStatusColor($job->status),
                'borderColor' => $this->getStatusColor($job->status),
                'extendedProps' => [
                    'company' => $job->company_name,
                    'product' => $job->product,
                    'status' => $job->status,
                    'remarks' => $job->remarks,
                    'section' => $job->section,
                    'progress' => $job->progress ?? 0,
                    'file_path' => $job->file_path,
                    'assigned_user' => $job->assignedUser->name ?? 'Unassigned'
                ]
            ];
        });

        // Return events as JSON response
        return response()->json($events);
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'company_name' => 'required|string|max:255',
            'product' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:pending,ongoing,completed',
            'section' => 'required|string|max:255',
            'remarks' => 'nullable|string',
            'progress' => 'nullable|integer|min:0|max:100',
            'file' => 'nullable|file|mimes:pdf,jpg,png,jpeg,docx,xlsx|max:10240', // 10MB max
            'assigned_user_id' => 'nullable|exists:users,id'
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('job_files', $fileName, 'public');
            $validatedData['file_path'] = $filePath;
        }

        // Set assigned user to current user if not specified
        if (!isset($validatedData['assigned_user_id'])) {
            $validatedData['assigned_user_id'] = Auth::id();
        }

        $job = Job::create($validatedData);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'job' => $job->load('assignedUser'),
                'message' => 'Job created successfully!'
            ]);
        }

        return redirect()->back()->with('success', 'Job created successfully!');
    }

    public function update(Request $request, Job $job)
    {
        $validatedData = $request->validate([
            'company_name' => 'required|string|max:255',
            'product' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:pending,ongoing,completed',
            'section' => 'required|string|max:255',
            'remarks' => 'nullable|string',
            'progress' => 'nullable|integer|min:0|max:100',
            'file' => 'nullable|file|mimes:pdf,jpg,png,jpeg,docx,xlsx|max:10240',
            'assigned_user_id' => 'nullable|exists:users,id'
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($job->file_path && Storage::disk('public')->exists($job->file_path)) {
                Storage::disk('public')->delete($job->file_path);
            }

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('job_files', $fileName, 'public');
            $validatedData['file_path'] = $filePath;
        }

        $job->update($validatedData);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'job' => $job->load('assignedUser'),
                'message' => 'Job updated successfully!'
            ]);
        }

        return redirect()->back()->with('success', 'Job updated successfully!');
    }

    public function destroy(Job $job)
    {
        // Delete associated file if exists
        if ($job->file_path && Storage::disk('public')->exists($job->file_path)) {
            Storage::disk('public')->delete($job->file_path);
        }

        // This will hard delete the job (permanently remove)
        $job->delete();

        return response()->json([
            'success' => true,
            'message' => 'Job deleted successfully!'
        ]);
    }

    private function getStatusColor($status)
    {
        switch ($status) {
            case 'completed':
                return '#10b981'; // Green
            case 'ongoing':
                return '#f59e0b'; // Yellow/Orange
            case 'pending':
            default:
                return '#ef4444'; // Red
        }
    }
}
