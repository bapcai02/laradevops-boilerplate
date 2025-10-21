@extends('admin.layouts.app')

@section('content')
<div class="page-header">
    <h1 class="page-title">Dashboard</h1>
    <p class="page-subtitle">Welcome back! Here's what's happening with your DevOps system today.</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <h6 class="stat-title">PHP Version</h6>
            <div class="stat-icon primary">
                <i class="bi bi-code-slash"></i>
            </div>
        </div>
        <h3 class="stat-value">{{ $phpVersion }}</h3>
        <div class="stat-change positive">
            <i class="bi bi-arrow-up"></i>
            <span>Latest</span>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <h6 class="stat-title">Laravel Version</h6>
            <div class="stat-icon success">
                <i class="bi bi-laravel"></i>
            </div>
        </div>
        <h3 class="stat-value">{{ $laravelVersion }}</h3>
        <div class="stat-change positive">
            <i class="bi bi-arrow-up"></i>
            <span>Stable</span>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <h6 class="stat-title">Memory Usage</h6>
            <div class="stat-icon warning">
                <i class="bi bi-memory"></i>
            </div>
        </div>
        <h3 class="stat-value">{{ $memoryUsage }} MB</h3>
        <div class="stat-change positive">
            <i class="bi bi-arrow-up"></i>
            <span>+12% from last hour</span>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <h6 class="stat-title">System Uptime</h6>
            <div class="stat-icon danger">
                <i class="bi bi-clock"></i>
            </div>
        </div>
        <h3 class="stat-value">{{ $uptime ?: 'N/A' }}</h3>
        <div class="stat-change positive">
            <i class="bi bi-check-circle"></i>
            <span>Running</span>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h6>System Overview</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-muted">Current User</label>
                            <div class="p-3 bg-light rounded">{{ $whoami ?: 'N/A' }}</div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-muted">Server Time</label>
                            <div class="p-3 bg-light rounded">{{ now()->format('Y-m-d H:i:s T') }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-muted">Environment</label>
                            <div class="p-3 bg-light rounded">{{ app()->environment() }}</div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-muted">Debug Mode</label>
                            <div class="p-3 bg-light rounded">
                                <span class="badge {{ config('app.debug') ? 'bg-success' : 'bg-secondary' }}">
                                    {{ config('app.debug') ? 'Enabled' : 'Disabled' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6>Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.deploy') }}" class="btn btn-primary">
                        <i class="bi bi-rocket-takeoff me-2"></i>Deploy Now
                    </a>
                    <a href="{{ route('admin.logs') }}" class="btn btn-outline-primary">
                        <i class="bi bi-file-text me-2"></i>View Logs
                    </a>
                    <a href="{{ route('admin.cache') }}" class="btn btn-outline-primary">
                        <i class="bi bi-database me-2"></i>Manage Cache
                    </a>
                    <a href="{{ route('admin.system') }}" class="btn btn-outline-primary">
                        <i class="bi bi-cpu me-2"></i>System Info
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection