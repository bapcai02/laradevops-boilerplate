<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DeployController extends Controller
{
    public function deployBackground()
    {
        $logPath = storage_path('logs/deploy.log');
        if (!file_exists(dirname($logPath))) {
            @mkdir(dirname($logPath), 0755, true);
        }
        $script = base_path('deploy/deploy.sh');
        $cmd = "ENVIRONMENT=production nohup bash '" . $script . "' >> '" . $logPath . "' 2>&1 &";
        shell_exec($cmd);
        return redirect('/dashboard')->with('status', 'Deployment started');
    }

    public function rollbackBackground(Request $request)
    {
        $file = $request->input('file');
        $logPath = storage_path('logs/deploy.log');
        $script = base_path('deploy/rollback.sh');
        $cmd = $file
            ? "nohup bash '" . $script . "' '" . basename($file) . "' >> '" . $logPath . "' 2>&1 &"
            : "nohup bash '" . $script . "' >> '" . $logPath . "' 2>&1 &";
        shell_exec($cmd);
        return redirect('/dashboard')->with('status', 'Rollback triggered');
    }

    public function deploy(Request $request)
    {
        $logPath = storage_path('logs/deploy.log');
        if (!file_exists(dirname($logPath))) {
            @mkdir(dirname($logPath), 0755, true);
        }

        $script = base_path('deploy/deploy.sh');
        $process = Process::fromShellCommandline("ENVIRONMENT=production bash '$script'");
        $process->setTimeout(null);

        $response = new StreamedResponse(function () use ($process, $logPath) {
            $process->run(function ($type, $buffer) use ($logPath) {
                file_put_contents($logPath, $buffer, FILE_APPEND);
                echo $buffer;
                @ob_flush();
                flush();
            });
        });
        $response->headers->set('Content-Type', 'text/plain');
        return $response;
    }

    public function rollback(Request $request)
    {
        $file = $request->input('file');
        $logPath = storage_path('logs/deploy.log');
        $script = base_path('deploy/rollback.sh');
        $cmd = $file ? "bash '$script' '$file'" : "bash '$script'";

        $process = Process::fromShellCommandline($cmd);
        $process->setTimeout(null);

        $response = new StreamedResponse(function () use ($process, $logPath) {
            $process->run(function ($type, $buffer) use ($logPath) {
                file_put_contents($logPath, $buffer, FILE_APPEND);
                echo $buffer;
                @ob_flush();
                flush();
            });
        });
        $response->headers->set('Content-Type', 'text/plain');
        return $response;
    }

    public function logs()
    {
        $logPath = storage_path('logs/deploy.log');
        if (!file_exists($logPath)) {
            return response()->json(['data' => ''], 200);
        }
        return response()->json(['data' => file_get_contents($logPath)]);
    }
}


