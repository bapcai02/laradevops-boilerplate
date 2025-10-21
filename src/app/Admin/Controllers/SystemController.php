<?php

namespace App\Admin\Controllers;

class SystemController
{
    public function index()
    {
        $df = function_exists('shell_exec') ? trim((string) @shell_exec('df -h')) : '';
        $free = function_exists('shell_exec') ? trim((string) @shell_exec('free -m')) : '';
        $uptime = function_exists('shell_exec') ? trim((string) @shell_exec('uptime -p')) : '';
        $user = function_exists('shell_exec') ? trim((string) @shell_exec('whoami')) : '';
        return view('admin.system', compact('df', 'free', 'uptime', 'user'));
    }
}


