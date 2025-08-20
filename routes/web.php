<?php

use Illuminate\Support\Facades\Route;
use App\Models\Job;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\MasterFileController;
use App\Http\Controllers\MediaOngoingJobController;
use App\Http\Controllers\OutdoorCoordinatorController;
use App\Http\Controllers\KltgCoordinatorController;
use App\Http\Controllers\MediaCoordinatorController;
use App\Http\Controllers\KltgMonthlyController;
use App\Http\Controllers\OutdoorOngoingJobController;
use App\Http\Controllers\MediaMonthlyDetailController;
use App\Http\Controllers\CoordinatorMediaController;

use App\Models\MasterFile;

// ===============================================
// ROOT & AUTHENTICATION ROUTES
// ===============================================
Route::get('/', function () {
    return redirect('/dashboard');
});

Route::get('/login', function () {
    return redirect('/'); // or redirect to your main page
})->name('login');

// ===============================================
// DASHBOARD ROUTES
// ===============================================
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
// routes/web.php
Route::get('/dashboard/kltg',   [KltgMonthlyController::class, 'index'])->name('dashboard.kltg');
Route::get('/coordinator/kltg', [KltgCoordinatorController::class, 'index'])->name('coordinator.kltg.index');
Route::get('/media-jobs', fn () => redirect()->route('dashboard.media'))
    ->name('dashboard.media.jobs');
Route::post('coordinator/kltg', [KltgCoordinatorController::class, 'store'])->name('coordinator.kltg.store');
Route::get('/outdoor-jobs', [DashboardController::class, 'outdoor'])->name('dashboard.outdoor');
Route::post('/dashboard/outdoor/update', [DashboardController::class, 'updateOutdoorField'])->name('dashboard.outdoor.update');

// ===============================================
// CALENDAR ROUTES
// ===============================================
Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
Route::get('/calendar/events', [CalendarController::class, 'events'])->name('calendar.events');
Route::get('/calendar/debug', [CalendarController::class, 'debugData'])->name('calendar.debug');
Route::get('/calendar-view', [CalendarController::class, 'index'])->name('calendar');

// ===============================================
// MASTERFILE ROUTES
// ===============================================
Route::prefix('masterfile')->name('masterfile.')->group(function () {
    Route::get('/', [MasterFileController::class, 'index'])->name('index');
    Route::get('/create', [MasterFileController::class, 'create'])->name('create');
    Route::post('/store', [MasterFileController::class, 'store'])->name('store');
    Route::get('/monthly', [MasterFileController::class, 'monthlyJob'])->name('monthly');
    Route::get('/template', [MasterFileController::class, 'downloadTemplate'])->name('template');
    Route::get('/stats', [MasterFileController::class, 'getStats'])->name('stats');
    Route::post('/import', [MasterFileController::class, 'import'])->name('import');
    Route::get('/{id}', [MasterFileController::class, 'show'])->name('show');
    Route::put('/{id}', [MasterFileController::class, 'update'])->name('update');
    Route::post('/{id}/update', [MasterFileController::class, 'updateRemarksAndLocation'])->name('update.partial');
    Route::post('/{id}/timeline', [MasterFileController::class, 'updateTimeline'])->name('timeline.update');
    Route::get('/export-csv', [MasterFileController::class, 'export'])->name('exportCsv');

    // Matrix routes
    Route::get('/{id}/matrix', [MasterFileController::class, 'showMatrix'])->name('matrix.show');
    Route::get('/{id}/kltg-matrix', [MasterFileController::class, 'showKltgMatrix'])->name('kltg.matrix');
    Route::get('/{id}/kltg-matrix/edit', [MasterFileController::class, 'editKltgMatrix'])->name('kltg.matrix.edit');
    Route::post('/{id}/kltg-matrix/update', [MasterFileController::class, 'updateKltgMatrix'])->name('kltg.matrix.update');
    Route::post('/{id}/kltg-monthly', [MasterFileController::class, 'upsertKltgMonthly'])->name('kltg.monthly.upsert');
});

// MasterFile backward compatibility routes
Route::get('masterfile/{id}/matrix', [MasterFileController::class, 'showMatrix'])->name('matrix.show');
Route::get('masterfiles/{id}', [MasterFileController::class, 'show'])->name('masterfiles.show');
Route::put('/masterfile/{id}', [MasterFileController::class, 'update'])->name('masterfile.update');

// MasterFile confirmation & monthly link routes
Route::get('/confirmation-links', [MasterFileController::class, 'confirmationLink'])->name('confirmation.links');
Route::post('/confirmation-links/{id}/update', [MasterFileController::class, 'updateRemarksAndLocation'])->name('confirmation.update');
Route::get('/monthly-jobs', [MasterFileController::class, 'monthlyJob'])->name('monthlyjob.index');
Route::post('/monthly-jobs/{id}/update', [MasterFileController::class, 'updateMonthlyJob'])->name('monthlyjob.update');

// Export routes
Route::get('/export-monthly-ongoing', [MasterFileController::class, 'exportMonthlyOngoing'])->name('export.monthly.ongoing');
Route::get('/template', [MasterFileController::class, 'downloadTemplate'])->name('template');
Route::get('/masterfile/export', [MasterFileController::class, 'exportCsv'])->name('masterfile.export');
Route::get('/masterfile/export-csv', [MasterFileController::class, 'exportCsv'])->name('masterfile.exportCsv');

// Serials preview
Route::get('/serials/preview', [MasterFileController::class, 'previewSerials'])->name('serials.preview');

// ===============================================
// OUTDOOR COORDINATOR ROUTES
// ===============================================
Route::prefix('coordinator/outdoor')->name('coordinator.outdoor.')->group(function () {
    Route::post('/update-field', [OutdoorCoordinatorController::class, 'updateField'])->name('updateField');
    Route::post('/update-inline', [OutdoorCoordinatorController::class, 'updateInline'])->name('updateInline');
    Route::post('/sync', [OutdoorCoordinatorController::class, 'syncWithMasterFiles'])->name('sync');
    Route::get('/seed', [OutdoorCoordinatorController::class, 'seedFromMasterFiles'])->name('seed');
    Route::get('/export', [OutdoorCoordinatorController::class, 'export'])->name('export');

    Route::get('/', [OutdoorCoordinatorController::class, 'index'])->name('index');
    Route::get('/create', [OutdoorCoordinatorController::class, 'create'])->name('create');
    Route::post('/', [OutdoorCoordinatorController::class, 'store'])->name('store');
    Route::post('/outdoor/details/upsert', action: [OutdoorOngoingJobController::class, 'upsert'])->name('outdoor.details.upsert');

    Route::get('/{id}', [OutdoorCoordinatorController::class, 'show'])->whereNumber('id')->name('show');
    Route::get('/{id}/edit', [OutdoorCoordinatorController::class, 'edit'])->whereNumber('id')->name('edit');
    Route::patch('/{id}', [OutdoorCoordinatorController::class, 'update'])->whereNumber('id')->name('update');
    Route::delete('/{id}', [OutdoorCoordinatorController::class, 'destroy'])->whereNumber('id')->name('destroy');
});
Route::get('/coordinator/outdoor', [OutdoorCoordinatorController::class, 'index'])->name('coordinator.outdoor.index');
Route::get('/outdoor/ongoing-jobs', [OutdoorOngoingJobController::class, 'index'])->name('outdoor.ongoing.index');


Route::post('/outdoor-coordinator/upsert', [OutdoorCoordinatorController::class, 'upsert'])->name('coordinator.outdoor.upsert');
Route::post('coordinator/outdoor/update-field', [OutdoorCoordinatorController::class, 'updateField'])->name('coordinator.outdoor.updateField');

// Outdoor monthly and move routes
Route::post('/outdoor/monthly/upsert', [OutdoorOngoingJobController::class, 'upsertMonthlyDetail'])->name('outdoor.monthly.upsert');
Route::post('/outdoor/move/{master_file_id}', [OutdoorOngoingJobController::class, 'moveToOngoing'])->name('outdoor.move');

Route::get('/dashboard/outdoor', [OutdoorOngoingJobController::class, 'index'])
    ->name('dashboard.outdoor');
Route::get('/outdoor-jobs', fn () => redirect()->route('dashboard.outdoor'))
    ->name('dashboard.outdoor.legacy');
Route::post('/outdoor/monthly/upsert', [OutdoorOngoingJobController::class, 'upsertMonthlyDetail'])
    ->name('outdoor.monthly.upsert');
// ===============================================
// KLTG COORDINATOR ROUTES (CLEANED)
// ===============================================
Route::prefix('coordinator/kltg')->name('coordinator.kltg.')->group(function () {
    Route::get('/', [KltgCoordinatorController::class, 'index'])->name('index');
    Route::post('/upsert', [KltgCoordinatorController::class, 'upsert'])->name('upsert');
    Route::get('/eligible', [KltgCoordinatorController::class, 'getEligibleMasterFiles'])->name('eligible');
    Route::get('/export', [KltgCoordinatorController::class, 'export'])->name('export');

    // Optional: Keep PATCH variant with different name if needed
    Route::patch('/{masterFile}/upsert', [KltgCoordinatorController::class, 'upsert'])->name('upsert.patch');
});

// KLTG Monthly routes
Route::get('/kltg', [KltgMonthlyController::class, 'index'])->name('kltg.index');
Route::post('/kltg/detail/update', [KltgMonthlyController::class, 'update'])->name('kltg.detail.update');
Route::prefix('kltg')->name('kltg.')->group(function () {
    Route::get('/grid', [KltgMonthlyController::class, 'grid'])->name('grid');
    Route::post('/details/upsert', [KltgMonthlyController::class, 'upsert'])->name('details.upsert');
});


// ===============================================
// MEDIA COORDINATOR ROUTES
// ===============================================
Route::prefix('coordinator')->name('coordinator.')->group(function () {
    Route::get('/media', [MediaCoordinatorController::class, 'index'])->name('media.index');
});

Route::get('/coordinator/media', [MediaCoordinatorController::class, 'index'])->name('coordinator.media.index');
Route::post('/coordinator/media', [MediaCoordinatorController::class, 'store'])->name('coordinator.media.store');
Route::patch('/coordinator/media/{id}', [MediaCoordinatorController::class, 'update'])->name('coordinator.media.update');

// ===============================================
// MEDIA ONGOING JOB ROUTES
// ===============================================
Route::prefix('media-social-job')->group(function () {
    Route::get('/', [MediaOngoingJobController::class, 'index'])->name('media.social.index');
    Route::get('/create', [MediaOngoingJobController::class, 'create'])->name('media.social.create');
    Route::post('/store', [MediaOngoingJobController::class, 'store'])->name('media.social.store');
    Route::get('/{id}', [MediaOngoingJobController::class, 'show'])->name('media.social.show');
    Route::get('/{id}/edit', [MediaOngoingJobController::class, 'edit'])->name('media.social.edit');
    Route::put('/{id}', [MediaOngoingJobController::class, 'update'])->name('media.social.update');
    Route::delete('/{id}', [MediaOngoingJobController::class, 'destroy'])->name('media.social.destroy');
    Route::post('/update/{id}', [MediaOngoingJobController::class, 'updateField'])->name('media.social.update.field');
    Route::post('/monthly-job/update', [MediaOngoingJobController::class, 'inlineUpdate'])->name('monthly.job.update');
    Route::post('/media/details/upsert', [MediaOngoingJobController::class, 'upsert'])->name('media.details.upsert');
});
Route::get('/dashboard/media', [MediaMonthlyDetailController::class, 'index'])
    ->name('dashboard.media');
Route::post('/media/monthly/upsert', [MediaMonthlyDetailController::class, 'upsert'])
    ->name('media.monthly.upsert');


// ===============================================
// JOB ROUTES
// ===============================================
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
