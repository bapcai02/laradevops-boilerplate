<?php

namespace App\Admin\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\RedirectResponse;

class CacheController
{
    public function index()
    {
        $keys = [];
        $store = Cache::getStore();
        if (method_exists($store, 'getRedis')) {
            try {
                $keys = $store->getRedis()->keys('*');
            } catch (\Throwable $e) {
                $keys = [];
            }
        }
        return view('admin.cache', compact('keys'));
    }

    public function clearAll(): RedirectResponse
    {
        Cache::flush();
        return redirect()->route('admin.cache');
    }

    public function clearKey(string $key): RedirectResponse
    {
        Cache::forget($key);
        return redirect()->route('admin.cache');
    }
}


