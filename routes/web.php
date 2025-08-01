<?php

use Illuminate\Support\Facades\Route;
use App\Models\Job;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\MasterFileController;

// Redirect root to dashboard
Route::get('/', function () {
    return redirect('/dashboard');
});

// Dashboard route
Route::get('/dashboard', [MasterFileController::class, 'index'])->name('dashboard');

// Calendar routes
Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
Route::get('/calendar/events', [CalendarController::class, 'getEvents'])->name('calendar.events');
Route::post('/calendar/jobs', [CalendarController::class, 'store'])->name('calendar.jobs.store');
Route::put('/calendar/jobs/{job}', [CalendarController::class, 'update'])->name('calendar.jobs.update');
Route::delete('/calendar/jobs/{job}', [CalendarController::class, 'destroy'])->name('calendar.jobs.destroy');

// Task routes
Route::resource('tasks', TaskController::class);

// MasterFile confirmation & monthly link routes
Route::get('/confirmation-links', [MasterFileController::class, 'confirmationLink'])->name('confirmation.links');
Route::post('/confirmation-links/{id}/update', [MasterFileController::class, 'updateRemarksAndLocation'])->name('confirmation.update');
Route::get('/monthly-jobs', [MasterFileController::class, 'monthlyJob'])->name('monthlyjob.index');
Route::post('/monthly-jobs/{id}/update', [MasterFileController::class, 'updateMonthlyJob'])->name('monthlyjob.update');

// 🔧 MasterFile routes - SPECIFIC routes FIRST, then dynamic {id}
Route::prefix('masterfile')->name('masterfile.')->group(function () {
    Route::get('/', [MasterFileController::class, 'index'])->name('index');
    Route::get('/create', [MasterFileController::class, 'create'])->name('create');
    Route::post('/store', [MasterFileController::class, 'store'])->name('store');
    Route::get('/monthly', [MasterFileController::class, 'monthlyJob'])->name('monthly');
    Route::get('/export', [MasterFileController::class, 'exportCsv'])->name('export');
    Route::get('/template', [MasterFileController::class, 'downloadTemplate'])->name('template');
    Route::get('/stats', [MasterFileController::class, 'getStats'])->name('stats');
    Route::post('/import', [MasterFileController::class, 'import'])->name('import');
    Route::get('/{id}', [MasterFileController::class, 'show'])->name('show');
    Route::put('/{id}', [MasterFileController::class, 'update'])->name('update');  // 🔧 ADDED: PUT method for full update
    Route::post('/{id}/update', [MasterFileController::class, 'updateRemarksAndLocation'])->name('update.partial'); // Keep old method for compatibility
    Route::post('/masterfile/{id}/timeline', [MasterFileController::class, 'updateTimeline'])->name('masterfile.timeline');
    Route::post('/{id}/timeline', [MasterFileController::class, 'updateTimeline'])->name('timeline.update');


});

// Jobs
Route::get('/jobs', function () {
    $jobs = Job::orderBy('created_at', 'desc')->get();
    return view('jobs.index', compact('jobs'));
})->name('jobs.index');

Route::get('/job/{id}', function ($id) {
    $job = Job::findOrFail($id);
    return view('jobs.show', compact('job'));
})->name('jobs.show');

Route::get('/monthly', function () {
    $jobs = Job::whereMonth('created_at', now()->month)
             ->whereYear('created_at', now()->year)
             ->orderBy('created_at', 'desc')
             ->get();
    return view('jobs.monthly', compact('jobs'));
})->name('jobs.monthly');

// Alternative calendar route alias
Route::get('/calendar-view', [CalendarController::class, 'index'])->name('calendar');
