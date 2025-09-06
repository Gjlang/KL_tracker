<?php

// Add these routes to your routes/web.php file

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CalendarController;

Route::middleware(['auth'])->group(function () {
    // Calendar routes
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/events', [CalendarController::class, 'getEvents'])->name('calendar.events');
    
    // Job management routes
    Route::post('/calendar/jobs', [CalendarController::class, 'store'])->name('calendar.jobs.store');
    Route::put('/calendar/jobs/{job}', [CalendarController::class, 'update'])->name('calendar.jobs.update');
    Route::delete('/calendar/jobs/{job}', [CalendarController::class, 'destroy'])->name('calendar.jobs.destroy');
});