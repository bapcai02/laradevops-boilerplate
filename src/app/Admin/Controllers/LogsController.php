<?php

namespace App\Admin\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LogsController
{
    public function index()
    {
        $path = storage_path('logs/laravel.log');
        $content = file_exists($path) ? file($path, FILE_IGNORE_NEW_LINES) : [];
        $entries = $this->parseEntries($content);
        return view('admin.logs', compact('entries'));
    }

    public function clear(Request $request): RedirectResponse
    {
        $path = storage_path('logs/laravel.log');
        if (file_exists($path)) {
            file_put_contents($path, '');
        }
        return redirect()->route('admin.logs');
    }

    public function show(string $id)
    {
        $path = storage_path('logs/laravel.log');
        $content = file_exists($path) ? file($path, FILE_IGNORE_NEW_LINES) : [];
        $entries = $this->parseEntries($content);
        $entry = collect($entries)->firstWhere('id', (int) $id);
        if (!$entry) {
            return redirect()->route('admin.logs');
        }
        return view('admin.log_show', compact('entry'));
    }

    private function parseEntries(array $lines): array
    {
        $entries = [];
        $current = null;
        $id = 1;
        foreach ($lines as $line) {
            if (preg_match('/^\[(.*?)\]\s+[^.]*\.(\w+):\s(.*)$/', $line, $m)) {
                // Start of a new log entry
                if ($current) {
                    $current['body'] = rtrim($current['body']);
                    $entries[] = $current;
                }
                $current = [
                    'id' => $id++,
                    'datetime' => $m[1] ?? '',
                    'level' => strtoupper($m[2] ?? ''),
                    'message' => $m[3] ?? '',
                    'body' => $line . "\n",
                ];
            } else {
                if ($current) {
                    $current['body'] .= $line . "\n";
                }
            }
        }
        if ($current) {
            $current['body'] = rtrim($current['body']);
            $entries[] = $current;
        }
        // Show newest first
        return array_reverse($entries);
    }
}


