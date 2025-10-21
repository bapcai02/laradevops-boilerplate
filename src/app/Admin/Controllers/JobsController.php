<?php

namespace App\Admin\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;

class JobsController
{
    public function index()
    {
        $failed = DB::table('failed_jobs')->orderByDesc('failed_at')->limit(200)->get();
        return view('admin.jobs', compact('failed'));
    }

    public function retry(string $id): RedirectResponse
    {
        // Reinsert the payload into jobs table using artisan command
        \Artisan::call('queue:retry', ['id' => $id]);
        return redirect()->route('admin.jobs');
    }

    public function retryAll(): RedirectResponse
    {
        \Artisan::call('queue:retry', ['id' => 'all']);
        return redirect()->route('admin.jobs');
    }

    public function delete(string $id): RedirectResponse
    {
        DB::table('failed_jobs')->where('id', $id)->delete();
        return redirect()->route('admin.jobs');
    }
}


