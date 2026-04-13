<?php

use App\Http\Controllers\WeeklyScheduleExportController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin/login');

Route::middleware('auth')->get('/exports/weekly-schedule', WeeklyScheduleExportController::class)
    ->name('weekly-schedule.export');
