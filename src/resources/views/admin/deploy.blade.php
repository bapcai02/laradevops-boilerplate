@extends('admin.layouts.app')

@section('content')
<div class="page-header">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 class="page-title">Deploy</h1>
            <p class="page-subtitle">Deploy your application and manage deployment history.</p>
        </div>
        <div>
            <form method="POST" action="{{ route('admin.deploy.run') }}" id="deployForm" class="d-inline">
                @csrf
                <button class="btn btn-primary" id="deployBtn">
                    <i class="bi bi-plus me-2"></i>+ Add Deploy
                </button>
            </form>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="text-primary mb-1">{{ count($history) }}</h3>
                <p class="text-muted mb-0">Total Deploys</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="text-warning mb-1">{{ collect($history)->where('status', 'started')->count() }}</h3>
                <p class="text-muted mb-0">Pending</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="text-success mb-1">{{ collect($history)->where('status', 'success')->count() }}</h3>
                <p class="text-muted mb-0">Success</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="text-danger mb-1">{{ collect($history)->where('status', 'fail')->count() }}</h3>
                <p class="text-muted mb-0">Failed</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <h6 class="mb-0">Deploy History</h6>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-primary active">All</button>
                    <button type="button" class="btn btn-sm btn-outline-primary">Pending</button>
                    <button type="button" class="btn btn-sm btn-outline-primary">Success</button>
                    <button type="button" class="btn btn-sm btn-outline-primary">Failed</button>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise me-1"></i>Bulk Update
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
                    <th>Deploy ID <i class="bi bi-arrow-up-down ms-1"></i></th>
                    <th>Environment <i class="bi bi-arrow-up-down ms-1"></i></th>
                    <th>Status <i class="bi bi-arrow-up-down ms-1"></i></th>
                    <th>Started At <i class="bi bi-arrow-up-down ms-1"></i></th>
                    <th>Duration <i class="bi bi-arrow-up-down ms-1"></i></th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($history as $index => $h)
                <tr>
                    <td>
                        <input type="checkbox" class="form-check-input row-checkbox" value="{{ $h['id'] ?? '' }}">
                    </td>
                    <td class="font-monospace">#{{ $h['id'] ?? '' }}</td>
                    <td>
                        <span class="badge bg-info">{{ $h['environment'] ?? 'production' }}</span>
                    </td>
                    <td>
                        <span class="badge {{ ($h['status'] ?? '')==='success' ? 'bg-success' : ((($h['status'] ?? '')==='fail') ? 'bg-danger' : (($h['status'] ?? '')==='started' ? 'bg-warning' : 'bg-secondary')) }}">
                            {{ strtoupper($h['status'] ?? 'unknown') }}
                        </span>
                    </td>
                    <td class="text-nowrap">{{ $h['started_at'] ?? '-' }}</td>
                    <td>{{ $h['duration_sec'] ?? '-' }}s</td>
                    <td>
                        <div class="d-flex align-items-center gap-1">
                            <button class="btn btn-sm btn-outline-primary" onclick="editDeploy('{{ $h['id'] ?? '' }}')">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteDeploy('{{ $h['id'] ?? '' }}')">
                                <i class="bi bi-trash"></i>
                            </button>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('admin.deploy.logs') }}?version={{ $h['id'] }}&html=1" target="_blank">
                                        <i class="bi bi-eye me-2"></i>View Log
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="retryDeploy('{{ $h['id'] ?? '' }}')">
                                        <i class="bi bi-arrow-clockwise me-2"></i>Retry
                                    </a></li>
                                </ul>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="bi bi-inbox display-4 d-block mb-2"></i>
                        No deployments found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <div class="d-flex align-items-center justify-content-between">
            <div class="text-muted">
                Showing 1-{{ count($history) }} of {{ count($history) }} entries
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
        <button class="btn btn-sm btn-light" onclick="duplicateSelected()">
            <i class="bi bi-files me-1"></i>Duplicate
        </button>
        <button class="btn btn-sm btn-light" onclick="printSelected()">
            <i class="bi bi-printer me-1"></i>Print
        </button>
        <button class="btn btn-sm btn-danger" onclick="deleteSelected()">
            <i class="bi bi-trash me-1"></i>Delete
        </button>
        <button class="btn btn-sm btn-outline-light" onclick="closeFloatingBar()">
            <i class="bi bi-x"></i>
        </button>
    </div>
</div>

<div class="card mt-4" id="liveOutputCard" style="display: none;">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span>
            <i class="bi bi-terminal me-2"></i>Deploy Output (live)
        </span>
        <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.deploy.logs') }}" target="_blank">
            <i class="bi bi-box-arrow-up-right me-1"></i>Open raw log
        </a>
    </div>
    <div class="card-body p-0">
        <iframe id="liveOutput" src="" style="width:100%; height:70vh; border:0; background:#0b1220;"></iframe>
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

function duplicateSelected() {
    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    console.log('Duplicating:', Array.from(checkedBoxes).map(cb => cb.value));
    // Implement duplicate functionality
}

function printSelected() {
    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    console.log('Printing:', Array.from(checkedBoxes).map(cb => cb.value));
    // Implement print functionality
}

function deleteSelected() {
    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    if (confirm(`Delete ${checkedBoxes.length} selected items?`)) {
        console.log('Deleting:', Array.from(checkedBoxes).map(cb => cb.value));
        // Implement delete functionality
    }
}

function editDeploy(id) {
    console.log('Edit deploy:', id);
    // Implement edit functionality
}

function deleteDeploy(id) {
    if (confirm('Delete this deploy?')) {
        console.log('Delete deploy:', id);
        // Implement delete functionality
    }
}

function retryDeploy(id) {
    console.log('Retry deploy:', id);
    // Implement retry functionality
}

// Deploy form submission
document.getElementById('deployForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Show live output
    document.getElementById('liveOutputCard').style.display = 'block';
    document.getElementById('liveOutput').src = '{{ route("admin.deploy.logs") }}?html=1';
    document.getElementById('deployBtn').disabled = true;
    document.getElementById('deployBtn').innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Deploying...';
    
    // Submit form
    fetch('{{ route("admin.deploy.run") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: '{{ csrf_token() }}'
    }).then(() => {
        // Start polling for completion
        let pollCount = 0;
        const maxPolls = 60; // 2 minutes max
        
        const pollInterval = setInterval(() => {
            pollCount++;
            
            // Check if deploy is complete by looking at the latest history
            fetch('{{ route("admin.deploy") }}')
                .then(response => response.text())
                .then(html => {
                    // Simple check: if we can find a success badge in the latest row, deploy is done
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const latestRow = doc.querySelector('tbody tr:first-child');
                    
                    if (latestRow) {
                        const statusBadge = latestRow.querySelector('.badge');
                        if (statusBadge && (statusBadge.textContent.includes('SUCCESS') || statusBadge.textContent.includes('FAIL'))) {
                            clearInterval(pollInterval);
                            
                            // Hide live output after 3 seconds
                            setTimeout(() => {
                                document.getElementById('liveOutputCard').style.display = 'none';
                                document.getElementById('deployBtn').disabled = false;
                                document.getElementById('deployBtn').innerHTML = '<i class="bi bi-plus me-2"></i>+ Add Deploy';
                                
                                // Reload page to show updated history
                                window.location.reload();
                            }, 3000);
                        }
                    }
                })
                .catch(() => {
                    // If polling fails, stop after max attempts
                    if (pollCount >= maxPolls) {
                        clearInterval(pollInterval);
                        document.getElementById('deployBtn').disabled = false;
                        document.getElementById('deployBtn').innerHTML = '<i class="bi bi-plus me-2"></i>+ Add Deploy';
                    }
                });
        }, 2000); // Poll every 2 seconds
    });
});
</script>
@endsection