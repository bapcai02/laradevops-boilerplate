<?php

namespace App\Admin\Controllers;

use Illuminate\Http\Request;

class DashboardController
{
    public function index()
    {
        $phpVersion = PHP_VERSION;
        $laravelVersion = app()->version();
        $memoryUsage = round(memory_get_usage(true) / 1024 / 1024, 2);
        $uptime = function_exists('shell_exec') ? trim((string) @shell_exec('uptime -p')) : null;
        $whoami = function_exists('shell_exec') ? trim((string) @shell_exec('whoami')) : null;

        return view('admin.dashboard', compact('phpVersion', 'laravelVersion', 'memoryUsage', 'uptime', 'whoami'));
    }
}


