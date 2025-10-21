<?php

namespace App\Http\Controllers;

class MonitorController extends Controller
{
    public function metrics()
    {
        $metrics = [
            'php_memory_usage' => memory_get_usage(true),
            'php_memory_peak' => memory_get_peak_usage(true),
            'load_avg' => function_exists('sys_getloadavg') ? sys_getloadavg() : [],
            'time' => now()->toISOString(),
        ];

        // Attempt docker stats (optional)
        $docker = [
            'available' => false,
            'containers' => [],
        ];
        if (@shell_exec('command -v docker')) {
            $docker['available'] = true;
            $jsonLines = @shell_exec('docker stats --no-stream --format "{{json .}}" 2>/dev/null');
            if ($jsonLines) {
                $lines = preg_split("/\r?\n/", trim($jsonLines));
                foreach ($lines as $line) {
                    $decoded = json_decode($line, true);
                    if ($decoded) {
                        $docker['containers'][] = $decoded;
                    }
                }
            }
        }

        return response()->json([
            'metrics' => $metrics,
            'docker' => $docker,
        ]);
    }
}


