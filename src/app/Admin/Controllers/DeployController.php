<?php

namespace App\Admin\Controllers;

use Illuminate\Http\Request;

class DeployController
{
    public function index()
    {
        $historyPath = storage_path('logs/deploy_history.jsonl');
        $history = [];
        if (file_exists($historyPath)) {
            foreach (file($historyPath, FILE_IGNORE_NEW_LINES) as $line) {
                $decoded = json_decode($line, true);
                if (is_array($decoded)) { $history[] = $decoded; }
            }
            $history = array_reverse($history); // newest first
        }
        return view('admin.deploy', compact('history'));
    }

    public function run(Request $request)
    {
        $script = base_path('deploy/deploy.sh');
        $logPath = storage_path('logs/deploy.log');
        if (!file_exists($logPath)) {
            @touch($logPath);
        }
        if (file_exists($script)) {
            $cmd = "nohup bash '" . $script . "' >> '" . $logPath . "' 2>&1 &";
            shell_exec($cmd);
            return redirect()->route('admin.deploy');
        }
        return redirect()->route('admin.deploy')->with('error', "Script not found: {$script}");
    }

    public function logs(Request $request)
    {
        $versionId = $request->get('version');
        $logPath = storage_path('logs/deploy.log');
        
        // If specific version requested, try to get that version's log
        if ($versionId) {
            $historyPath = storage_path('logs/deploy_history.jsonl');
            if (file_exists($historyPath)) {
                foreach (file($historyPath, FILE_IGNORE_NEW_LINES) as $line) {
                    $decoded = json_decode($line, true);
                    if (is_array($decoded) && $decoded['id'] === $versionId && isset($decoded['log_file'])) {
                        $versionLogPath = storage_path($decoded['log_file']);
                        if (file_exists($versionLogPath)) {
                            $logPath = $versionLogPath;
                            break;
                        }
                    }
                }
            }
        }
        
        $logs = file_exists($logPath) ? file_get_contents($logPath) : '';

        if (request()->boolean('html')) {
            $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta http-equiv="refresh" content="2">'
                . '<style>body{margin:0;background:#0b1220;color:#e5e7eb;font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, Liberation Mono, monospace;}'
                . 'pre{white-space:pre-wrap;word-break:break-word;padding:12px;margin:0;}</style></head><body>'
                . '<pre>' . htmlspecialchars($logs, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</pre>'
                . '</body></html>';
            return response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
        }

        return response($logs, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}


