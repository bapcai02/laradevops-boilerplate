<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeployController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\MonitorController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Dashboard APIs
Route::post('/deploy', [DeployController::class, 'deploy']);
Route::post('/rollback', [DeployController::class, 'rollback']);
Route::get('/logs', [DeployController::class, 'logs']);
Route::get('/metrics', [MonitorController::class, 'metrics']);
Route::get('/backups', [BackupController::class, 'list']);
Route::post('/restore', [BackupController::class, 'restore']);
