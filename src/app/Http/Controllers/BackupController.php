<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BackupController extends Controller
{
    private function backupDir(): string
    {
        return base_path('backups');
    }

    public function list()
    {
        $dir = $this->backupDir();
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        $files = glob($dir . '/*.sql*');
        usort($files, fn($a, $b) => strcmp($b, $a));
        return response()->json([
            'files' => array_map(fn($f) => basename($f), $files),
        ]);
    }

    public function restore(Request $request)
    {
        $file = $request->input('file');
        if (!$file) {
            return response()->json(['error' => 'file required'], 422);
        }
        $path = $this->backupDir() . '/' . basename($file);
        if (!file_exists($path)) {
            return response()->json(['error' => 'file not found'], 404);
        }

        $script = base_path('deploy/rollback.sh');
        $cmd = "bash '" . $script . "' '" . $path . "'";
        $output = shell_exec($cmd . ' 2>&1');
        return response()->json(['output' => $output]);
    }
}


