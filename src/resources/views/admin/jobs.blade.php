@extends('admin.layouts.app')

@section('content')
<div class="page-header">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 class="page-title">Failed Jobs</h1>
            <p class="page-subtitle">Manage and retry failed background jobs.</p>
        </div>
        <div>
            <form method="POST" action="{{ route('admin.jobs.retry_all') }}" class="d-inline">
                @csrf
                <button class="btn btn-primary">
                    <i class="bi bi-arrow-clockwise me-2"></i>Retry All Failed Jobs
                </button>
            </form>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="text-danger mb-1">{{ count($failed) }}</h3>
                <p class="text-muted mb-0">Failed Jobs</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="text-warning mb-1">{{ collect($failed)->where('queue', 'default')->count() }}</h3>
                <p class="text-muted mb-0">Default Queue</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="text-info mb-1">{{ collect($failed)->where('queue', 'high')->count() }}</h3>
                <p class="text-muted mb-0">High Priority</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <h6 class="mb-0">Failed Jobs Queue</h6>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-primary active">All</button>
                    <button type="button" class="btn btn-sm btn-outline-primary">Default</button>
                    <button type="button" class="btn btn-sm btn-outline-primary">High</button>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise me-1"></i>Bulk Retry
                </button>
                <button class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-download me-1"></i>Export
                </button>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th style="width: 50px;">
                        <input type="checkbox" class="form-check-input" id="selectAll">
                    </th>
                    <th>Job ID <i class="bi bi-arrow-up-down ms-1"></i></th>
                    <th>Connection <i class="bi bi-arrow-up-down ms-1"></i></th>
                    <th>Queue <i class="bi bi-arrow-up-down ms-1"></i></th>
                    <th>Failed At <i class="bi bi-arrow-up-down ms-1"></i></th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($failed as $job)
                <tr>
                    <td>
                        <input type="checkbox" class="form-check-input row-checkbox" value="{{ $job->id }}">
                    </td>
                    <td class="font-monospace">#{{ $job->id }}</td>
                    <td>
                        <span class="badge bg-info">{{ $job->connection }}</span>
                    </td>
                    <td>
                        <span class="badge bg-secondary">{{ $job->queue }}</span>
                    </td>
                    <td class="text-nowrap">{{ $job->failed_at }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-1">
                            <form method="POST" action="{{ route('admin.jobs.retry', $job->id) }}" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.jobs.delete', $job->id) }}" onsubmit="return confirm('Delete failed job #{{ $job->id }}?');" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" onclick="viewJobDetails('{{ $job->id }}')">
                                        <i class="bi bi-eye me-2"></i>View Details
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="retryJob('{{ $job->id }}')">
                                        <i class="bi bi-arrow-clockwise me-2"></i>Retry Job
                                    </a></li>
                                </ul>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="bi bi-check-circle display-4 d-block mb-2 text-success"></i>
                        No failed jobs
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <div class="d-flex align-items-center justify-content-between">
            <div class="text-muted">
                Showing 1-{{ count($failed) }} of {{ count($failed) }} entries
            </div>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1">Previous</a>
                    </li>
                    <li class="page-item active">
                        <a class="page-link" href="#">1</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">2</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">3</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Floating Action Bar -->
<div id="floatingActionBar" class="floating-action-bar" style="display: none;">
    <div class="d-flex align-items-center gap-3">
        <span class="text-white" id="selectedCount">0 Selected</span>
        <button class="btn btn-sm btn-light" onclick="retrySelected()">
            <i class="bi bi-arrow-clockwise me-1"></i>Retry
        </button>
        <button class="btn btn-sm btn-danger" onclick="deleteSelected()">
            <i class="bi bi-trash me-1"></i>Delete
        </button>
        <button class="btn btn-sm btn-outline-light" onclick="closeFloatingBar()">
            <i class="bi bi-x"></i>
        </button>
    </div>
</div>

<style>
.floating-action-bar {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0, 0, 0, 0.9);
    color: white;
    padding: 12px 20px;
    border-radius: 25px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    z-index: 1000;
    backdrop-filter: blur(10px);
}
</style>

<script>
// Select All functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    updateFloatingBar();
});

// Individual checkbox change
document.querySelectorAll('.row-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateFloatingBar);
});

function updateFloatingBar() {
    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    const floatingBar = document.getElementById('floatingActionBar');
    const selectedCount = document.getElementById('selectedCount');
    
    if (checkedBoxes.length > 0) {
        floatingBar.style.display = 'block';
        selectedCount.textContent = `${checkedBoxes.length} Selected`;
    } else {
        floatingBar.style.display = 'none';
    }
}

function closeFloatingBar() {
    document.querySelectorAll('.row-checkbox:checked').forEach(checkbox => {
        checkbox.checked = false;
    });
    document.getElementById('selectAll').checked = false;
    updateFloatingBar();
}

function retrySelected() {
    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    if (confirm(`Retry ${checkedBoxes.length} selected jobs?`)) {
        console.log('Retrying:', Array.from(checkedBoxes).map(cb => cb.value));
        // Implement retry functionality
    }
}

function deleteSelected() {
    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    if (confirm(`Delete ${checkedBoxes.length} selected jobs?`)) {
        console.log('Deleting:', Array.from(checkedBoxes).map(cb => cb.value));
        // Implement delete functionality
    }
}

function viewJobDetails(id) {
    console.log('View job details:', id);
    // Implement view details functionality
}

function retryJob(id) {
    console.log('Retry job:', id);
    // Implement retry functionality
}
</script>
@endsection