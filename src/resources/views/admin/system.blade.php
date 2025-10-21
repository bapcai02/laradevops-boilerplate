@extends('admin.layouts.app')

@section('content')
<div class="page-header">
    <h1 class="page-title">System Information</h1>
    <p class="page-subtitle">Monitor your system resources and performance.</p>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6>Disk Usage</h6>
            </div>
            <div class="card-body">
                <pre class="pre-scrollable mb-0">{{ $df }}</pre>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6>Memory Usage</h6>
            </div>
            <div class="card-body">
                <pre class="pre-scrollable mb-0">{{ $free }}</pre>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6>System Uptime</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="bi bi-stopwatch text-primary me-3" style="font-size: 2rem;"></i>
                    <div>
                        <h5 class="mb-1">{{ $uptime ?: 'N/A' }}</h5>
                        <small class="text-muted">System uptime</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6>Current User</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="bi bi-person-circle text-success me-3" style="font-size: 2rem;"></i>
                    <div>
                        <h5 class="mb-1">{{ $user ?: 'N/A' }}</h5>
                        <small class="text-muted">Running as user</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection