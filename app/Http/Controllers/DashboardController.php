<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterFile; // Adjust the model name to match yours
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Your existing code for stats
        $totalJobs = MasterFile::count();
        $completedJobs = MasterFile::where('status', 'completed')->count();
        $ongoingJobs = MasterFile::where('status', 'ongoing')->count();
        $pendingJobs = MasterFile::where('status', 'pending')->count();

        // Get all master files for the main table
        $masterFiles = MasterFile::orderBy('created_at', 'desc')->get();

        // Get recent jobs (limit to 5 for the recent jobs section)
        $recentJobs = MasterFile::orderBy('created_at', 'desc')->limit(5)->get();

        // NEW: Group master files by year for the Master Confirmation Link
        $allFiles = MasterFile::orderBy('date', 'desc')->get();

        $grouped = $allFiles->groupBy(function($file) {
            return Carbon::parse($file->date)->year;
        })->sortKeysDesc(); // Sort years in descending order (2024, 2023, etc.)

        return view('dashboard', compact(
            'totalJobs',
            'completedJobs',
            'ongoingJobs',
            'pendingJobs',
            'masterFiles',
            'recentJobs',
            'grouped' // Add this line - this is what was missing!
        ));
    }
}
