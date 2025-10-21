@extends('admin.layouts.app')

@section('content')
<div class="page-header">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 class="page-title">Logs</h1>
            <p class="page-subtitle">View and manage your application logs.</p>
        </div>
        <div>
            <form method="POST" action="{{ route('admin.logs.clear') }}" class="d-inline">
                @csrf
                <button class="btn btn-danger" onclick="return confirm('Are you sure you want to clear all logs?')">
                    <i class="bi bi-trash me-2"></i>Clear Logs
                </button>
            </form>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="text-primary mb-1">{{ count($entries) }}</h3>
                <p class="text-muted mb-0">Total Logs</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="text-danger mb-1">{{ collect($entries)->where('level', 'ERROR')->count() }}</h3>
                <p class="text-muted mb-0">Errors</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="text-warning mb-1">{{ collect($entries)->where('level', 'WARNING')->count() }}</h3>
                <p class="text-muted mb-0">Warnings</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="text-info mb-1">{{ collect($entries)->where('level', 'INFO')->count() }}</h3>
                <p class="text-muted mb-0">Info</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <h6 class="mb-0">Log Entries</h6>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-primary active">All</button>
                    <button type="button" class="btn btn-sm btn-outline-primary">Errors</button>
                    <button type="button" class="btn btn-sm btn-outline-primary">Warnings</button>
                    <button type="button" class="btn btn-sm btn-outline-primary">Info</button>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
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
                    <th>Datetime <i class="bi bi-arrow-up-down ms-1"></i></th>
                    <th>Level <i class="bi bi-arrow-up-down ms-1"></i></th>
                    <th>Message <i class="bi bi-arrow-up-down ms-1"></i></th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($entries as $e)
                <tr>
                    <td>
                        <input type="checkbox" class="form-check-input row-checkbox" value="{{ $e['id'] }}">
                    </td>
                    <td class="text-nowrap font-monospace">{{ $e['datetime'] }}</td>
                    <td>
                        <span class="badge {{ $e['level']==='ERROR' ? 'bg-danger' : ($e['level']==='WARNING' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                            {{ $e['level'] }}
                        </span>
                    </td>
                    <td class="text-truncate" style="max-width: 600px;">{{ $e['message'] }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-1">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.logs.show',$e['id']) }}">
                                <i class="bi bi-eye"></i>
                            </a>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteLog('{{ $e['id'] }}')">
                                <i class="bi bi-trash"></i>
                            </button>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('admin.logs.show',$e['id']) }}">
                                        <i class="bi bi-eye me-2"></i>View Details
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="copyLog('{{ $e['message'] }}')">
                                        <i class="bi bi-copy me-2"></i>Copy Message
                                    </a></li>
                                </ul>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        <i class="bi bi-inbox display-4 d-block mb-2"></i>
                        No logs found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <div class="d-flex align-items-center justify-content-between">
            <div class="text-muted">
                Showing 1-{{ count($entries) }} of {{ count($entries) }} entries
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
        <button class="btn btn-sm btn-light" onclick="exportSelected()">
            <i class="bi bi-download me-1"></i>Export
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

function exportSelected() {
    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    console.log('Exporting:', Array.from(checkedBoxes).map(cb => cb.value));
    // Implement export functionality
}

function deleteSelected() {
    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    if (confirm(`Delete ${checkedBoxes.length} selected logs?`)) {
        console.log('Deleting:', Array.from(checkedBoxes).map(cb => cb.value));
        // Implement delete functionality
    }
}

function deleteLog(id) {
    if (confirm('Delete this log entry?')) {
        console.log('Delete log:', id);
        // Implement delete functionality
    }
}

function copyLog(message) {
    navigator.clipboard.writeText(message).then(function() {
        // Show success message
        alert('Log message copied to clipboard!');
    });
}
</script>
@endsection