<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

use Illuminate\Support\Facades\File;
use App\Http\Controllers\DeployController;
\Illuminate\Support\Facades\Route::middleware([])->group(function () {
    if (file_exists(base_path('app/Admin/routes/admin.php'))) {
        require base_path('app/Admin/routes/admin.php');
    }
});

Route::get('/dashboard', function () {
    $logPath = storage_path('logs/deploy.log');
    $logs = file_exists($logPath) ? File::get($logPath) : '';
    $backupDir = base_path('backups');
    $backups = glob($backupDir . '/*.sql*');
    $backups = array_map(fn($f) => basename($f), $backups ?: []);
    rsort($backups);
    return view('dashboard', compact('logs', 'backups'));
});

Route::post('/dashboard/deploy', [DeployController::class, 'deployBackground']);
Route::post('/dashboard/rollback', [DeployController::class, 'rollbackBackground']);
Route::post('/dashboard/restore', function (\Illuminate\Http\Request $request) {
    $file = basename($request->input('file'));
    if (!$file) { return redirect('/dashboard'); }
    $script = base_path('deploy/rollback.sh');
    $logPath = storage_path('logs/deploy.log');
    $cmd = "nohup bash '".$script."' '".$file."' >> '".$logPath."' 2>&1 &";
    shell_exec($cmd);
    return redirect('/dashboard');
});
