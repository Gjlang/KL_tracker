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
use App\Http\Controllers\ClienteleController;
use App\Http\Controllers\OutdoorInlineController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Report\SummaryReportController;


// ===============================================
// ROOT & AUTHENTICATION ROUTES
// ===============================================

// Root route - redirect to dashboard for authenticated users
Route::get('/', fn () => redirect()->route('dashboard'))
    ->middleware(['web', 'auth', 'permission:dashboard.view'])
    ->name('home');

// Guest routes (login)
Route::middleware(['web', 'guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');

    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
});

// Protected routes (authenticated users only) - FIXED MIDDLEWARE ORDER
Route::middleware(['web', 'auth', 'permission:dashboard.view'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['web', 'auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
});

// Debug route (temporary - remove after fixing)
Route::get('/whoami', function () {
    $u = Auth::user();
    if (!$u) return 'not logged in';
    return [
        'id'    => $u->id,
        'email' => $u->email,
        'roles' => $u->getRoleNames(),
        'perms' => $u->getAllPermissions()->pluck('name'),
    ];
})->middleware(['web', 'auth']);

// ===============================================
// DASHBOARD ROUTES
// ===============================================

Route::middleware(['web', 'auth', 'permission:dashboard.view'])->group(function () {
    Route::get('/dashboard/kltg', [KltgMonthlyController::class, 'index'])->name('dashboard.kltg');
    Route::get('/dashboard/media', [MediaMonthlyDetailController::class, 'index'])->name('dashboard.media');
    Route::get('/dashboard/outdoor', [OutdoorOngoingJobController::class, 'index'])->name('dashboard.outdoor');

    // Legacy dashboard redirects
    Route::get('/media-jobs', fn () => redirect()->route('dashboard.media'))->name('dashboard.media.jobs');
    Route::get('/outdoor-jobs', fn () => redirect()->route('dashboard.outdoor'))->name('dashboard.outdoor.legacy');
});

// Dashboard specific updates
Route::post('/dashboard/outdoor/update', [DashboardController::class, 'updateOutdoorField'])
    ->middleware(['web', 'auth', 'role_or_permission:admin|outdoor.edit'])
    ->name('dashboard.outdoor.update');

// ===============================================
// CALENDAR ROUTES
// ===============================================

Route::prefix('calendar')->middleware(['web', 'auth', 'permission:dashboard.view'])->name('calendar.')->group(function () {
    Route::get('/', [CalendarController::class, 'index'])->name('index');
    Route::get('/events', [CalendarController::class, 'events'])->name('events');
    Route::get('/debug', [CalendarController::class, 'debugData'])->name('debug');
});

// Calendar legacy route
Route::get('/calendar-view', [CalendarController::class, 'index'])
    ->middleware(['web', 'auth', 'permission:dashboard.view'])
    ->name('calendar');

// ===============================================
// MASTERFILE ROUTES
// ===============================================

Route::prefix('masterfile')->middleware(['web', 'auth'])->name('masterfile.')->group(function () {
    // Export routes (must be before dynamic {id} routes)
    Route::get('/export-xlsx', [MasterFileController::class, 'exportXlsx'])
        ->middleware('permission:export.run')
        ->name('exportXlsx');

    Route::get('/template', [MasterFileController::class, 'downloadTemplate'])
        ->middleware('permission:export.run')
        ->name('template');

    Route::get('/stats', [MasterFileController::class, 'getStats'])
        ->middleware('permission:export.run')
        ->name('stats');

    // Main CRUD routes
    Route::get('/', [MasterFileController::class, 'index'])
        ->middleware('permission:masterfile.view')
        ->name('index');

    Route::get('/create', [MasterFileController::class, 'create'])
        ->middleware('permission:masterfile.create')
        ->name('create');

    Route::post('/store', [MasterFileController::class, 'store'])
        ->middleware('permission:masterfile.create')
        ->name('store');

    Route::get('/monthly', [MasterFileController::class, 'monthlyJob'])
        ->middleware('permission:masterfile.monthly')
        ->name('monthly');

    Route::post('/import', [MasterFileController::class, 'import'])
        ->middleware('permission:masterfile.import')
        ->name('import');

    // Dynamic ID routes (constrained to numeric IDs)
    Route::get('/{id}', [MasterFileController::class, 'show'])
        ->whereNumber('id')
        ->middleware('permission:masterfile.show')
        ->name('show');

    Route::put('/{id}', [MasterFileController::class, 'update'])
        ->whereNumber('id')
        ->middleware('role_or_permission:admin|kltg.edit|outdoor.edit|media.edit')
        ->name('update');

    Route::post('/{id}/update', [MasterFileController::class, 'updateRemarksAndLocation'])
        ->middleware('role_or_permission:admin|kltg.edit|outdoor.edit|media.edit')
        ->name('update.partial');

    Route::post('/{id}/timeline', [MasterFileController::class, 'updateTimeline'])
        ->middleware('role_or_permission:admin|kltg.edit|outdoor.edit|media.edit')
        ->name('timeline.update');

    // Matrix routes
    Route::get('/{id}/matrix', [MasterFileController::class, 'showMatrix'])
        ->middleware('permission:masterfile.show')
        ->name('matrix.show');

    Route::get('/{id}/kltg-matrix', [MasterFileController::class, 'showKltgMatrix'])
        ->middleware('permission:masterfile.show')
        ->name('kltg.matrix');

    Route::get('/{id}/kltg-matrix/edit', [MasterFileController::class, 'editKltgMatrix'])
        ->middleware('permission:kltg.edit')
        ->name('kltg.matrix.edit');

    Route::post('/{id}/kltg-matrix/update', [MasterFileController::class, 'updateKltgMatrix'])
        ->middleware('permission:kltg.edit')
        ->name('kltg.matrix.update');

    Route::post('/{id}/kltg-monthly', [MasterFileController::class, 'upsertKltgMonthly'])
        ->middleware('permission:kltg.edit')
        ->name('kltg.monthly.upsert');

    // Print routes
    Route::get('/{file}/print', [MasterFileController::class, 'printAuto'])
        ->whereNumber('file')
        ->middleware('permission:export.run')
        ->name('print');
});

// Dashboard routes (including export) under the dashboard group
Route::prefix('dashboard/master')->middleware(['auth', 'permission:dashboard.view'])->name('dashboard.master.')->group(function () {
    Route::get('/kltg', [MasterFileController::class, 'kltg'])->name('kltg');
    Route::get('/outdoor', [MasterFileController::class, 'outdoor'])->name('outdoor');

    Route::get('/export/kltg', [MasterFileController::class, 'exportKltgXlsx'])
        ->middleware('permission:export.run')
        ->name('export.kltg');

    Route::get('/export/outdoor', [MasterFileController::class, 'exportOutdoorXlsx'])
        ->middleware('permission:export.run')
        ->name('export.outdoor');
});

// MasterFile backward compatibility routes
Route::get('masterfiles/{id}', [MasterFileController::class, 'show'])
    ->middleware(['auth', 'permission:masterfile.show'])
    ->name('masterfiles.show');

// MasterFile additional routes
Route::get('/confirmation-links', [MasterFileController::class, 'confirmationLink'])
    ->middleware(['auth', 'permission:masterfile.view'])
    ->name('confirmation.links');

Route::post('/confirmation-links/{id}/update', [MasterFileController::class, 'updateRemarksAndLocation'])
    ->middleware(['auth', 'role_or_permission:admin|kltg.edit|outdoor.edit|media.edit'])
    ->name('confirmation.update');

Route::get('/monthly-jobs', [MasterFileController::class, 'monthlyJob'])
    ->middleware(['auth', 'permission:masterfile.monthly'])
    ->name('monthlyjob.index');

Route::post('/monthly-jobs/{id}/update', [MasterFileController::class, 'updateMonthlyJob'])
    ->middleware(['auth', 'role_or_permission:admin|kltg.edit|outdoor.edit|media.edit'])
    ->name('monthlyjob.update');

// Export routes
Route::get('/export-monthly-ongoing', [MasterFileController::class, 'exportMonthlyOngoing'])
    ->middleware(['auth', 'permission:export.run'])
    ->name('export.monthly.ongoing');

Route::get('/serials/preview', [MasterFileController::class, 'previewSerials'])
    ->middleware(['auth', 'permission:export.run'])
    ->name('serials.preview');

Route::delete('/masterfile/{id}', [MasterFileController::class, 'destroy'])
    ->middleware(['auth', 'permission:masterfile.delete'])
    ->name('masterfile.destroy');

// ===============================================
// COORDINATOR ROUTES
// ===============================================

// OUTDOOR COORDINATOR
Route::prefix('coordinator/outdoor')->middleware(['auth', 'permission:coordinator.view'])->name('coordinator.outdoor.')->group(function () {
    // Main CRUD routes
    Route::get('/', [OutdoorCoordinatorController::class, 'index'])->name('index');

    Route::get('/create', [OutdoorCoordinatorController::class, 'create'])
        ->middleware('permission:outdoor.create')
        ->name('create');

    Route::post('/', [OutdoorCoordinatorController::class, 'store'])
        ->middleware('permission:outdoor.create')
        ->name('store');

    Route::get('/{id}', [OutdoorCoordinatorController::class, 'show'])
        ->whereNumber('id')
        ->name('show');

    Route::get('/{id}/edit', [OutdoorCoordinatorController::class, 'edit'])
        ->whereNumber('id')
        ->middleware('permission:outdoor.edit')
        ->name('edit');

    Route::patch('/{id}', [OutdoorCoordinatorController::class, 'update'])
        ->whereNumber('id')
        ->middleware('permission:outdoor.edit')
        ->name('update');

    Route::delete('/{id}', [OutdoorCoordinatorController::class, 'destroy'])
        ->whereNumber('id')
        ->middleware('permission:outdoor.delete')
        ->name('destroy');

    // Field update routes
    Route::post('/update-field', [OutdoorCoordinatorController::class, 'updateField'])
        ->middleware('permission:outdoor.edit')
        ->name('updateField');

    Route::post('/update-inline', [OutdoorCoordinatorController::class, 'updateInline'])
        ->middleware('permission:outdoor.edit')
        ->name('updateInline');

    Route::post('/upsert', [OutdoorCoordinatorController::class, 'upsert'])
        ->middleware('permission:outdoor.edit')
        ->name('upsert');

    // Data management routes
    Route::post('/sync', [OutdoorCoordinatorController::class, 'syncWithMasterFiles'])
        ->middleware('permission:outdoor.sync')
        ->name('sync');

    Route::get('/seed', [OutdoorCoordinatorController::class, 'seedFromMasterFiles'])
        ->middleware('permission:outdoor.sync')
        ->name('seed');

    // Export routes
    Route::get('/export', [OutdoorCoordinatorController::class, 'export'])
        ->middleware('permission:export.run')
        ->name('export');

    Route::get('/export-matrix', [OutdoorOngoingJobController::class, 'exportMatrix'])
        ->middleware('permission:export.run')
        ->name('exportMatrix');

    Route::get('/coordinator/kltg/export-xlsx', [KltgCoordinatorController::class, 'exportXlsx'])
        ->middleware('permission:export.run')
        ->name('coordinator.kltg.export.xlsx');
});

// KLTG COORDINATOR
Route::prefix('coordinator/kltg')->middleware(['auth', 'permission:coordinator.view'])->name('coordinator.kltg.')->group(function () {
    Route::get('/', [KltgCoordinatorController::class, 'index'])->name('index');

    Route::post('/', [KltgCoordinatorController::class, 'store'])
        ->middleware('permission:kltg.edit')
        ->name('store');

    Route::post('/upsert', [KltgCoordinatorController::class, 'upsert'])
        ->middleware('permission:kltg.edit')
        ->name('upsert');

    Route::patch('/{masterFile}/upsert', [KltgCoordinatorController::class, 'upsert'])
        ->middleware('permission:kltg.edit')
        ->name('upsert.patch');

    Route::put('/{id}', [MasterFileController::class, 'update'])
        ->whereNumber('id')
        ->middleware('permission:kltg.edit')
        ->name('update');

    Route::get('/eligible', [KltgCoordinatorController::class, 'getEligibleMasterFiles'])
        ->name('eligible');

    Route::get('/export', [KltgCoordinatorController::class, 'export'])
        ->middleware('permission:export.run')
        ->name('export');
});

// MEDIA COORDINATOR
Route::prefix('coordinator')->middleware(['auth', 'permission:coordinator.view'])->name('coordinator.')->group(function () {
    Route::get('/media', [MediaCoordinatorController::class, 'index'])->name('media.index');

    Route::post('/media/upsert', [MediaCoordinatorController::class, 'upsert'])
        ->middleware('permission:media.edit')
        ->name('media.upsert');

    Route::get('/media/export', [MediaCoordinatorController::class, 'export'])
        ->middleware('permission:export.run')
        ->name('media.export');

    Route::get('/export', [KltgCoordinatorController::class, 'export'])
        ->middleware('permission:export.run')
        ->name('export');
});

// ===============================================
// OUTDOOR ONGOING JOB ROUTES
// ===============================================

// Outdoor monthly details
Route::post('/outdoor/monthly/upsert', [OutdoorOngoingJobController::class, 'upsert'])
    ->middleware(['auth', 'permission:outdoor.edit'])
    ->name('outdoor.monthly.upsert');

Route::post('/coordinator/outdoor/details/upsert', [OutdoorOngoingJobController::class, 'upsertMonthlyDetail'])
    ->middleware(['auth', 'permission:outdoor.edit'])
    ->name('coordinator.outdoor.details.upsert');

// Legacy outdoor routes
Route::get('/outdoor/ongoing-jobs', [OutdoorOngoingJobController::class, 'index'])
    ->middleware(['auth', 'permission:coordinator.view'])
    ->name('outdoor.ongoing.index');

Route::post('/coordinator/outdoor/clone-year', [OutdoorOngoingJobController::class, 'cloneYear'])
    ->middleware(['auth', 'permission:outdoor.sync'])
    ->name('coordinator.outdoor.cloneYear');

// ===============================================
// KLTG MONTHLY ROUTES
// ===============================================

Route::get('/kltg', [KltgMonthlyController::class, 'index'])
    ->middleware(['auth', 'permission:coordinator.view'])
    ->name('kltg.index');

Route::post('/kltg/detail/update', [KltgMonthlyController::class, 'update'])
    ->middleware(['auth', 'permission:kltg.edit'])
    ->name('kltg.detail.update');

Route::prefix('kltg')->middleware(['auth', 'permission:coordinator.view'])->name('kltg.')->group(function () {
    Route::get('/grid', [KltgMonthlyController::class, 'grid'])->name('grid');

    Route::post('/details/upsert', [KltgMonthlyController::class, 'upsert'])
        ->middleware('permission:kltg.edit')
        ->name('details.upsert');

    Route::get('/export-matrix', [KltgMonthlyController::class, 'exportMatrix'])
        ->middleware('permission:export.run')
        ->name('exportMatrix');

    Route::get('/export/print', [KltgMonthlyController::class, 'exportPrint'])
        ->middleware('permission:export.run')
        ->name('export.print');

    Route::post('/clone-year', [KltgMonthlyController::class, 'cloneYear'])
        ->middleware('permission:kltg.sync')
        ->name('cloneYear');
});

// ===============================================
// MEDIA ONGOING JOB ROUTES
// ===============================================

Route::prefix('media-ongoing')->middleware(['auth', 'permission:coordinator.view'])->name('media.ongoing.')->group(function () {
    // Main CRUD routes
    Route::get('/', [MediaOngoingJobController::class, 'index'])->name('index');

    Route::get('/create', [MediaOngoingJobController::class, 'create'])
        ->middleware('permission:media.create')
        ->name('create');

    Route::post('/store', [MediaOngoingJobController::class, 'store'])
        ->middleware('permission:media.create')
        ->name('store');

    // Specific routes before generic {id} routes
    Route::get('/{id}/edit', [MediaOngoingJobController::class, 'edit'])
        ->whereNumber('id')
        ->middleware('permission:media.edit')
        ->name('edit');

    Route::get('/{id}', [MediaOngoingJobController::class, 'show'])
        ->whereNumber('id')
        ->name('show');

    Route::put('/{id}', [MediaOngoingJobController::class, 'update'])
        ->whereNumber('id')
        ->middleware('permission:media.edit')
        ->name('update');

    Route::delete('/{id}', [MediaOngoingJobController::class, 'destroy'])
        ->whereNumber('id')
        ->middleware('permission:media.delete')
        ->name('destroy');

    // Update routes
    Route::post('/update/{id}', [MediaOngoingJobController::class, 'updateField'])
        ->whereNumber('id')
        ->middleware('permission:media.edit')
        ->name('update.field');

    Route::post('/monthly-job/update', [MediaOngoingJobController::class, 'inlineUpdate'])
        ->middleware('permission:media.edit')
        ->name('monthly.job.update');

    Route::post('/details/upsert', [MediaOngoingJobController::class, 'upsert'])
        ->middleware('permission:media.edit')
        ->name('details.upsert');
});

// ===============================================
// MEDIA MONTHLY ROUTES
// ===============================================

Route::post('/media/monthly/upsert', [MediaMonthlyDetailController::class, 'upsert'])
    ->middleware(['auth', 'permission:media.edit'])
    ->name('media.monthly.upsert');

Route::get('/serials/preview', [SerialController::class, 'preview'])
    ->middleware(['auth', 'permission:export.run'])
    ->name('serials.preview');

// ===============================================
// JOB ROUTES
// ===============================================

Route::prefix('jobs')->middleware(['auth', 'permission:dashboard.view'])->name('jobs.')->group(function () {
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
})->middleware(['auth', 'permission:dashboard.view'])->name('jobs.monthly');

// ===============================================
// INFORMATION BOOTH ROUTES
// ===============================================

Route::prefix('information-booth')->middleware(['auth', 'permission:information.booth.view'])->name('information.booth.')->group(function () {
    // Main pages
    Route::get('/', [InformationBoothController::class, 'index'])->name('index');

    Route::get('/create', [InformationBoothController::class, 'create'])
        ->middleware('permission:information.booth.create')
        ->name('create');

    Route::post('/store', [InformationBoothController::class, 'store'])
        ->middleware('permission:information.booth.create')
        ->name('store');

    // Calendar + Events (put BEFORE param routes)
    Route::get('/calendar', [InformationBoothController::class, 'calendar'])->name('calendar');
    Route::get('/calendar/events', [InformationBoothController::class, 'events'])->name('calendar.events');

    Route::patch('/calendar/move/{feed}', [InformationBoothController::class, 'move'])
        ->middleware('permission:information.booth.edit')
        ->name('calendar.move');

    // Edit/Update/Delete (AFTER calendar routes to avoid conflicts)
    Route::get('/{feed}/edit', [InformationBoothController::class, 'edit'])
        ->middleware('permission:information.booth.edit')
        ->name('edit');

    Route::put('/{feed}', [InformationBoothController::class, 'update'])
        ->middleware('permission:information.booth.edit')
        ->name('update');

    Route::delete('/{feed}', [InformationBoothController::class, 'destroy'])
        ->middleware('permission:information.booth.delete')
        ->name('destroy');
});

// ===============================================
// INLINE UPDATE ROUTES
// ===============================================

Route::post('/clientele/inline-update', [ClienteleController::class, 'inlineUpdate'])
    ->middleware(['auth', 'permission:clientele.edit'])
    ->name('clientele.inline.update');

Route::post('/outdoor/inline-update', [OutdoorInlineController::class, 'update'])
    ->middleware(['auth', 'permission:outdoor.edit'])
    ->name('outdoor.inline.update');

Route::post('/clientele/bulk-inline-update', [ClienteleController::class, 'bulkInlineUpdate'])
    ->middleware(['auth', 'permission:clientele.edit'])
    ->name('clientele.bulk.inline.update');

Route::middleware(['auth','permission:report.summary.view'])
    ->get('/report/summary', [\App\Http\Controllers\Report\SummaryReportController::class, 'index'])
    ->name('report.summary');

Route::middleware(['auth','permission:report.summary.export'])
    ->get('/report/summary.pdf', [\App\Http\Controllers\Report\SummaryReportController::class, 'pdf'])
    ->name('report.summary.pdf');
