<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientServiceController;
use App\Http\Controllers\ClientTypeController;
use App\Http\Controllers\CompaniesHouseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RenewalController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ImpersonateController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('clients', ClientController::class);
    Route::post('clients/{client}/services', [ClientServiceController::class, 'store'])->name('clients.services.store');
    Route::delete('clients/{client}/services/{service}', [ClientServiceController::class, 'destroy'])->name('clients.services.destroy');
    Route::resource('projects', ProjectController::class);
    Route::resource('tasks', TaskController::class);
    Route::post('tasks/{task}/urgent', [TaskController::class, 'toggleUrgent'])->name('tasks.urgent');
    Route::resource('services', ServiceController::class);
    Route::resource('products', ProductController::class);
    Route::resource('renewals', RenewalController::class);

    Route::resource('jobs', JobController::class);
    Route::post('jobs/{job}/complete', [JobController::class, 'complete'])->name('jobs.complete');

    Route::get('api/companies-house/search', [CompaniesHouseController::class, 'search'])->name('companies-house.search');
    Route::get('api/companies-house/{number}', [CompaniesHouseController::class, 'profile'])->name('companies-house.profile');

    Route::post('impersonate/stop', [ImpersonateController::class, 'stop'])->name('impersonate.stop');
    Route::post('impersonate/{user}', [ImpersonateController::class, 'start'])->name('impersonate.start');

    Route::middleware('manager')->group(function () {
        Route::resource('users', UserController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
        Route::resource('client-types', ClientTypeController::class)->only(['index', 'show', 'store', 'update', 'destroy']);

        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/fixed-prices', [ReportController::class, 'fixedPrices'])->name('reports.fixed-prices');
        Route::get('reports/fixed-prices/csv', [ReportController::class, 'fixedPricesCsv'])->name('reports.fixed-prices.csv');
        Route::get('reports/fixed-prices/pdf/{orientation}', [ReportController::class, 'fixedPricesPdf'])
            ->name('reports.fixed-prices.pdf')
            ->where('orientation', 'portrait|landscape');

        Route::get('reports/upcoming-jobs', [ReportController::class, 'upcomingJobs'])->name('reports.upcoming-jobs');
        Route::get('reports/upcoming-jobs/csv', [ReportController::class, 'upcomingJobsCsv'])->name('reports.upcoming-jobs.csv');
        Route::get('reports/upcoming-jobs/pdf/{orientation}', [ReportController::class, 'upcomingJobsPdf'])
            ->name('reports.upcoming-jobs.pdf')
            ->where('orientation', 'portrait|landscape');
    });
});
