<?php

use Illuminate\Support\Facades\Route;
use App\Admin\Controllers\DashboardController;
use App\Admin\Controllers\LogsController;
use App\Admin\Controllers\JobsController;
use App\Admin\Controllers\DeployController as AdminDeployController;
use App\Admin\Controllers\CacheController;
use App\Admin\Controllers\SystemController;

Route::middleware(['auth.basic'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/logs', [LogsController::class, 'index'])->name('logs');
    Route::post('/logs/clear', [LogsController::class, 'clear'])->name('logs.clear');
    Route::get('/logs/{id}', [LogsController::class, 'show'])->name('logs.show');

    Route::get('/jobs', [JobsController::class, 'index'])->name('jobs');
    Route::post('/jobs/retry/{id}', [JobsController::class, 'retry'])->name('jobs.retry');
    Route::post('/jobs/retry-all', [JobsController::class, 'retryAll'])->name('jobs.retry_all');
    Route::delete('/jobs/{id}', [JobsController::class, 'delete'])->name('jobs.delete');

    Route::get('/deploy', [AdminDeployController::class, 'index'])->name('deploy');
    Route::post('/deploy/run', [AdminDeployController::class, 'run'])->name('deploy.run');
    Route::get('/deploy/logs', [AdminDeployController::class, 'logs'])->name('deploy.logs');

    Route::get('/cache', [CacheController::class, 'index'])->name('cache');
    Route::post('/cache/clear-all', [CacheController::class, 'clearAll'])->name('cache.clear_all');
    Route::post('/cache/clear/{key}', [CacheController::class, 'clearKey'])->name('cache.clear');

    Route::get('/system', [SystemController::class, 'index'])->name('system');
});


