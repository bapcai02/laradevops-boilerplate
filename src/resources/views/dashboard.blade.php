<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-100 text-slate-900" x-data="{ tab: 'deploy' }">
<header class="bg-white border-b shadow-sm">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded bg-emerald-600"></div>
            <h1 class="text-xl font-semibold">Admin Dashboard</h1>
            <span class="text-xs ml-2 px-2 py-0.5 rounded bg-slate-200 text-slate-700">{{ strtoupper(config('app.env')) }}</span>
        </div>
        <div class="text-sm text-slate-600">{{ now()->toDayDateTimeString() }}</div>
    </div>
</header>

<main class="max-w-7xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow">
        <nav class="border-b px-4">
            <ul class="flex items-center gap-4 overflow-x-auto">
                <li>
                    <button @click="tab='deploy'" :class="tab==='deploy' ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-slate-600 hover:text-slate-800'" class="py-3 border-b-2 font-medium">Deploy</button>
                </li>
                <li>
                    <button @click="tab='logs'" :class="tab==='logs' ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-slate-600 hover:text-slate-800'" class="py-3 border-b-2 font-medium">Logs</button>
                </li>
                <li>
                    <button @click="tab='backups'" :class="tab==='backups' ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-slate-600 hover:text-slate-800'" class="py-3 border-b-2 font-medium">Backups</button>
                </li>
                <li>
                    <button @click="tab='horizon'" :class="tab==='horizon' ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-slate-600 hover:text-slate-800'" class="py-3 border-b-2 font-medium">Horizon</button>
                </li>
            </ul>
        </nav>
        <div class="p-4">
            <div x-show="tab==='deploy'">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <div class="lg:col-span-2">
                        <div class="rounded border shadow-sm bg-white">
                            <div class="p-4 flex items-center justify-between border-b">
                                <h2 class="font-semibold">Deployment</h2>
                                <div class="space-x-2">
                                    <form method="POST" action="{{ url('/dashboard/deploy') }}" class="inline">
                                        @csrf
                                        <button class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded">Deploy now</button>
                                    </form>
                                    <form method="POST" action="{{ url('/dashboard/rollback') }}" class="inline">
                                        @csrf
                                        <button class="px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white rounded">Rollback latest</button>
                                    </form>
                                </div>
                            </div>
                            <div class="p-4 text-sm text-slate-600">
                                Use Deploy to run composer, cache, migrate; Rollback restores the latest DB backup.
                            </div>
                        </div>
                    </div>
                    <aside class="space-y-4">
                        <div class="rounded border shadow-sm bg-white">
                            <div class="p-4 border-b font-semibold">App status</div>
                            <div class="p-4 text-sm space-y-1">
                                <div><span class="font-mono">PHP mem:</span> {{ number_format(memory_get_usage(true)/1024/1024, 1) }} MB</div>
                                <div><span class="font-mono">PHP peak:</span> {{ number_format(memory_get_peak_usage(true)/1024/1024, 1) }} MB</div>
                                <div><span class="font-mono">Time:</span> {{ now()->toDateTimeString() }}</div>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
            <div x-show="tab==='logs'" x-cloak>
                <div class="rounded border shadow-sm bg-white">
                    <div class="p-4 flex items-center justify-between border-b">
                        <h2 class="font-semibold">Deployment Logs</h2>
                        <form method="POST" action="{{ url('/dashboard/deploy') }}">
                            @csrf
                            <button class="px-3 py-1.5 bg-slate-200 hover:bg-slate-300 rounded text-sm">Refresh by deploying</button>
                        </form>
                    </div>
                    <pre class="bg-slate-900 text-slate-100 p-4 h-[60vh] overflow-auto text-xs">{{ $logs ?? '' }}</pre>
                </div>
            </div>
            <div x-show="tab==='backups'" x-cloak>
                <div class="rounded border shadow-sm bg-white">
                    <div class="p-4 flex items-center justify-between border-b">
                        <h2 class="font-semibold">Database Backups</h2>
                        <div class="text-sm text-slate-500">Latest first</div>
                    </div>
                    <div class="p-4">
                        <ul class="divide-y text-sm">
                            @forelse(($backups ?? []) as $b)
                                <li class="py-2 flex items-center justify-between">
                                    <span class="font-mono truncate mr-3">{{ $b }}</span>
                                    <form method="POST" action="{{ url('/dashboard/restore') }}">
                                        @csrf
                                        <input type="hidden" name="file" value="{{ $b }}" />
                                        <button class="px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded">Restore</button>
                                    </form>
                                </li>
                            @empty
                                <li class="py-6 text-slate-500 text-center">No backups found.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
            <div x-show="tab==='horizon'" x-cloak>
                <div class="rounded border shadow-sm bg-white">
                    <div class="p-4 flex items-center justify-between border-b">
                        <h2 class="font-semibold">Horizon</h2>
                        <a href="{{ url('/horizon') }}" target="_blank" class="text-sm text-emerald-700 underline">Open full page</a>
                    </div>
                    <div class="h-[75vh]">
                        <iframe src="{{ url('/horizon') }}" class="w-full h-full" style="border:0;" title="Horizon"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<footer class="max-w-7xl mx-auto px-6 py-6 text-center text-xs text-slate-500">
    Laravel DevOps Boilerplate
    </footer>

</body>
</html>


