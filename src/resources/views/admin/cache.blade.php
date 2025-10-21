@extends('admin.layouts.app')

@section('content')
<div class="page-header">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 class="page-title">Cache</h1>
            <p class="page-subtitle">Manage your Redis cache keys and clear cache.</p>
        </div>
        <div>
            <form method="POST" action="{{ route('admin.cache.clear_all') }}" class="d-inline">
                @csrf
                <button class="btn btn-warning" onclick="return confirm('Are you sure you want to clear all cache?')">
                    <i class="bi bi-trash me-2"></i>Clear All Cache
                </button>
            </form>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="d-flex align-items-center justify-content-between">
            <h6 class="mb-0">Redis Cache Keys</h6>
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" placeholder="Search cache keys..." style="width: 250px;">
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>KEY</th>
                    <th width="120">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($keys as $key)
                <tr>
                    <td class="font-monospace">{{ is_string($key) ? $key : json_encode($key) }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.cache.clear', is_string($key)? $key : base64_encode(json_encode($key))) }}" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Clear this cache key?')">
                                <i class="bi bi-trash me-1"></i>Clear
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" class="text-center text-muted py-4">
                        <i class="bi bi-database display-4 d-block mb-2"></i>
                        No cache keys found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection