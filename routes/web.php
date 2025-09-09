<?php

use Illuminate\Support\Facades\Route;
use App\Models\Job;
use App\Models\MasterFile;
use Illuminate\Support\Facades\Auth;

// Controllers
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\MediaOngoingJobController;
use App\Http\Controllers\OutdoorCoordinatorController;
use App\Http\Controllers\KltgCoordinatorController;
use App\Http\Controllers\MediaCoordinatorController;
use App\Http\Controllers\KltgMonthlyController;
use App\Http\Controllers\OutdoorOngoingJobController;
use App\Http\Controllers\MediaMonthlyDetailController;
use App\Http\Controllers\CoordinatorMediaController;
use App\Http\Controllers\SerialController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\MasterFileController;
use App\Http\Controllers\InformationBoothController;



// ===============================================
// ROOT & AUTHENTICATION ROUTES
// ===============================================

// Root route - redirect to dashboard for authenticated users
Route::get('/', fn () => redirect()->route('dashboard'))
    ->middleware('auth')
    ->name('home');

// Guest routes (login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

// Protected routes (authenticated users only)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
});

// ===============================================
// DASHBOARD ROUTES
// ===============================================

Route::get('/dashboard/kltg', [KltgMonthlyController::class, 'index'])->name('dashboard.kltg');
Route::get('/dashboard/media', [MediaMonthlyDetailController::class, 'index'])->name('dashboard.media');
Route::get('/dashboard/outdoor', [OutdoorOngoingJobController::class, 'index'])->name('dashboard.outdoor');

// Legacy dashboard redirects
Route::get('/media-jobs', fn () => redirect()->route('dashboard.media'))->name('dashboard.media.jobs');
Route::get('/outdoor-jobs', fn () => redirect()->route('dashboard.outdoor'))->name('dashboard.outdoor.legacy');

// Dashboard specific updates
Route::post('/dashboard/outdoor/update', [DashboardController::class, 'updateOutdoorField'])->name('dashboard.outdoor.update');

// ===============================================
// CALENDAR ROUTES
// ===============================================

Route::prefix('calendar')->name('calendar.')->group(function () {
    Route::get('/', [CalendarController::class, 'index'])->name('index');
    Route::get('/events', [CalendarController::class, 'events'])->name('events');
    Route::get('/debug', [CalendarController::class, 'debugData'])->name('debug');
});

// Calendar legacy route
Route::get('/calendar-view', [CalendarController::class, 'index'])->name('calendar');

// ===============================================
// MASTERFILE ROUTES
// ===============================================

Route::prefix('masterfile')->name('masterfile.')->group(function () {
    // Export routes (must be before dynamic {id} routes)
    Route::get('/export-xlsx', [MasterFileController::class, 'exportXlsx'])->name('exportXlsx');
    Route::get('/template', [MasterFileController::class, 'downloadTemplate'])->name('template');
    Route::get('/stats', [MasterFileController::class, 'getStats'])->name('stats');

    // Main CRUD routes
    Route::get('/', [MasterFileController::class, 'index'])->name('index');
    Route::get('/create', [MasterFileController::class, 'create'])->name('create');
    Route::post('/store', [MasterFileController::class, 'store'])->name('store');
    Route::get('/monthly', [MasterFileController::class, 'monthlyJob'])->name('monthly');
    Route::post('/import', [MasterFileController::class, 'import'])->name('import');

    // Dynamic ID routes (constrained to numeric IDs)
    Route::get('/{id}', [MasterFileController::class, 'show'])->whereNumber('id')->name('show');
    Route::put('/{id}', [MasterFileController::class, 'update'])->whereNumber('id')->name('update');
    Route::post('/{id}/update', [MasterFileController::class, 'updateRemarksAndLocation'])->name('update.partial');
    Route::post('/{id}/timeline', [MasterFileController::class, 'updateTimeline'])->name('timeline.update');

    // Matrix routes
    Route::get('/{id}/matrix', [MasterFileController::class, 'showMatrix'])->name('matrix.show');
    Route::get('/{id}/kltg-matrix', [MasterFileController::class, 'showKltgMatrix'])->name('kltg.matrix');
    Route::get('/{id}/kltg-matrix/edit', [MasterFileController::class, 'editKltgMatrix'])->name('kltg.matrix.edit');
    Route::post('/{id}/kltg-matrix/update', [MasterFileController::class, 'updateKltgMatrix'])->name('kltg.matrix.update');
    Route::post('/{id}/kltg-monthly', [MasterFileController::class, 'upsertKltgMonthly'])->name('kltg.monthly.upsert');

    // Print routes
    Route::get('/{file}/print', [MasterFileController::class, 'printAuto'])->whereNumber('file')->name('print');

});

// ✅ Put dashboard routes (including export) under the dashboard group
Route::prefix('dashboard/master')->name('dashboard.master.')->middleware('auth')->group(function () {
    Route::get('/kltg', [MasterFileController::class, 'kltg'])->name('kltg');
    Route::get('/outdoor', [MasterFileController::class, 'outdoor'])->name('outdoor');
     Route::get('/export/kltg', [MasterFileController::class, 'exportKltgXlsx'])->name('export.kltg');
    Route::get('/export/outdoor', [MasterFileController::class, 'exportOutdoorXlsx'])->name('export.outdoor');
});




// MasterFile backward compatibility routes
Route::get('masterfiles/{id}', [MasterFileController::class, 'show'])->name('masterfiles.show');

// MasterFile additional routes
Route::get('/confirmation-links', [MasterFileController::class, 'confirmationLink'])->name('confirmation.links');
Route::post('/confirmation-links/{id}/update', [MasterFileController::class, 'updateRemarksAndLocation'])->name('confirmation.update');
Route::get('/monthly-jobs', [MasterFileController::class, 'monthlyJob'])->name('monthlyjob.index');
Route::post('/monthly-jobs/{id}/update', [MasterFileController::class, 'updateMonthlyJob'])->name('monthlyjob.update');

// Export routes
Route::get('/export-monthly-ongoing', [MasterFileController::class, 'exportMonthlyOngoing'])->name('export.monthly.ongoing');
Route::get('/serials/preview', [MasterFileController::class, 'previewSerials'])->name('serials.preview');
Route::delete('/masterfile/{id}', [MasterFileController::class, 'destroy'])->name('masterfile.destroy');


// ===============================================
// INFORMATION BOOTH ROUTES
// ===============================================
Route::middleware(['auth'])->group(function () {
    Route::get('/information-booth', [InformationBoothController::class, 'index'])
        ->name('information.booth');

    Route::post('/information-booth/feeds', [InformationBoothController::class, 'store'])
        ->name('information.booth.feeds.store');

    Route::patch('/information-booth/feeds/{feed}', [InformationBoothController::class, 'update'])
        ->name('information.booth.feeds.update');

    Route::delete('/information-booth/feeds/{feed}', [InformationBoothController::class, 'destroy'])
        ->name('information.booth.feeds.destroy');
});


// ===============================================
// COORDINATOR ROUTES
// ===============================================

// OUTDOOR COORDINATOR
Route::prefix('coordinator/outdoor')->name('coordinator.outdoor.')->group(function () {
    // Main CRUD routes
    Route::get('/', [OutdoorCoordinatorController::class, 'index'])->name('index');
    Route::get('/create', [OutdoorCoordinatorController::class, 'create'])->name('create');
    Route::post('/', [OutdoorCoordinatorController::class, 'store'])->name('store');
    Route::get('/{id}', [OutdoorCoordinatorController::class, 'show'])->whereNumber('id')->name('show');
    Route::get('/{id}/edit', [OutdoorCoordinatorController::class, 'edit'])->whereNumber('id')->name('edit');
    Route::patch('/{id}', [OutdoorCoordinatorController::class, 'update'])->whereNumber('id')->name('update');
    Route::delete('/{id}', [OutdoorCoordinatorController::class, 'destroy'])->whereNumber('id')->name('destroy');

    // Field update routes
    Route::post('/update-field', [OutdoorCoordinatorController::class, 'updateField'])->name('updateField');
    Route::post('/update-inline', [OutdoorCoordinatorController::class, 'updateInline'])->name('updateInline');
    Route::post('/upsert', [OutdoorCoordinatorController::class, 'upsert'])->name('upsert');

    // Data management routes
    Route::post('/sync', [OutdoorCoordinatorController::class, 'syncWithMasterFiles'])->name('sync');
    Route::get('/seed', [OutdoorCoordinatorController::class, 'seedFromMasterFiles'])->name('seed');

    // Export routes
    Route::get('/export', [OutdoorCoordinatorController::class, 'export'])->name('export');
    Route::get('/export-matrix', [OutdoorOngoingJobController::class, 'exportMatrix'])->name('exportMatrix');
    Route::get('/coordinator/kltg/export-xlsx', [KltgCoordinatorController::class, 'exportXlsx'])
    ->name('coordinator.kltg.export.xlsx');

});



// KLTG COORDINATOR
Route::prefix('coordinator/kltg')->name('coordinator.kltg.')->group(function () {
    Route::get('/', [KltgCoordinatorController::class, 'index'])->name('index');
    Route::post('/', [KltgCoordinatorController::class, 'store'])->name('store');
    Route::post('/upsert', [KltgCoordinatorController::class, 'upsert'])->name('upsert');
    Route::patch('/{masterFile}/upsert', [KltgCoordinatorController::class, 'upsert'])->name('upsert.patch');
    Route::put('/{id}', [MasterFileController::class, 'update'])->whereNumber('id')->name('update');
    Route::get('/eligible', [KltgCoordinatorController::class, 'getEligibleMasterFiles'])->name('eligible');
    Route::get('/export', [KltgCoordinatorController::class, 'export'])->name('export');
});

// MEDIA COORDINATOR
Route::prefix('coordinator')->name('coordinator.')->group(function () {
    Route::get('/media', [MediaCoordinatorController::class, 'index'])->name('media.index');
    Route::post('/media/upsert', [MediaCoordinatorController::class, 'upsert'])->name('media.upsert');
    Route::get('/media/export', [MediaCoordinatorController::class, 'export'])->name('media.export');
    Route::get('/export', [KltgCoordinatorController::class, 'export'])->name('export');
});

// ===============================================
// OUTDOOR ONGOING JOB ROUTES
// ===============================================

// Outdoor monthly details
Route::post('/outdoor/monthly/upsert', [OutdoorOngoingJobController::class, 'upsert'])->name('outdoor.monthly.upsert');
Route::post('/coordinator/outdoor/details/upsert', [OutdoorOngoingJobController::class, 'upsertMonthlyDetail'])->name('coordinator.outdoor.details.upsert');

// Legacy outdoor routes
Route::get('/outdoor/ongoing-jobs', [OutdoorOngoingJobController::class, 'index'])->name('outdoor.ongoing.index');

// ===============================================
// KLTG MONTHLY ROUTES
// ===============================================

Route::get('/kltg', [KltgMonthlyController::class, 'index'])->name('kltg.index');
Route::post('/kltg/detail/update', [KltgMonthlyController::class, 'update'])->name('kltg.detail.update');

Route::prefix('kltg')->name('kltg.')->group(function () {
    Route::get('/grid', [KltgMonthlyController::class, 'grid'])->name('grid');
    Route::post('/details/upsert', [KltgMonthlyController::class, 'upsert'])->name('details.upsert');
    Route::get('/export-matrix', [KltgMonthlyController::class, 'exportMatrix'])->name('exportMatrix');
    Route::get('/export/print', [KltgMonthlyController::class, 'exportPrint'])->name('export.print');
});

// ===============================================
// MEDIA ONGOING JOB ROUTES
// ===============================================

Route::prefix('media-ongoing')->name('media.ongoing.')->group(function () {
    // Main CRUD routes
    Route::get('/', [MediaOngoingJobController::class, 'index'])->name('index');
    Route::get('/create', [MediaOngoingJobController::class, 'create'])->name('create');
    Route::post('/store', [MediaOngoingJobController::class, 'store'])->name('store');

    // Specific routes before generic {id} routes
    Route::get('/{id}/edit', [MediaOngoingJobController::class, 'edit'])->whereNumber('id')->name('edit');
    Route::get('/{id}', [MediaOngoingJobController::class, 'show'])->whereNumber('id')->name('show');
    Route::put('/{id}', [MediaOngoingJobController::class, 'update'])->whereNumber('id')->name('update');
    Route::delete('/{id}', [MediaOngoingJobController::class, 'destroy'])->whereNumber('id')->name('destroy');

    // Update routes
    Route::post('/update/{id}', [MediaOngoingJobController::class, 'updateField'])->whereNumber('id')->name('update.field');
    Route::post('/monthly-job/update', [MediaOngoingJobController::class, 'inlineUpdate'])->name('monthly.job.update');
    Route::post('/details/upsert', [MediaOngoingJobController::class, 'upsert'])->name('details.upsert');
});

// ===============================================
// MEDIA MONTHLY ROUTES
// ===============================================

Route::post('/media/monthly/upsert', [MediaMonthlyDetailController::class, 'upsert'])->name('media.monthly.upsert');


Route::get('/serials/preview', [SerialController::class, 'preview'])
    ->name('serials.preview');

// ===============================================
// JOB ROUTES
// ===============================================

Route::prefix('jobs')->name('jobs.')->group(function () {
    Route::get('/', function () {
        $jobs = Job::orderBy('created_at', 'desc')->get();
        return view('jobs.index', compact('jobs'));
    })->name('index');

    Route::get('/{id}', function ($id) {
        $job = Job::findOrFail($id);
        return view('jobs.show', compact('job'));
    })->name('show');
});

Route::get('/monthly', function () {
    $jobs = Job::whereMonth('created_at', now()->month)
             ->whereYear('created_at', now()->year)
             ->orderBy('created_at', 'desc')
             ->get();
    return view('jobs.monthly', compact('jobs'));
})->name('jobs.monthly');
