@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h5 mb-0">Log Details</h1>
    <a class="btn btn-sm btn-secondary" href="{{ route('admin.logs') }}">Back</a>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <div class="mb-2">
        <span class="badge {{ $entry['level']==='ERROR' ? 'bg-danger' : ($entry['level']==='WARNING' ? 'bg-warning text-dark' : 'bg-secondary') }}">{{ $entry['level'] }}</span>
        <span class="text-muted ms-2">{{ $entry['datetime'] }}</span>
      </div>
      <div class="mb-3 fw-semibold">{{ $entry['message'] }}</div>
      <pre class="bg-dark text-white p-3" style="white-space: pre-wrap;">{{ $entry['body'] }}</pre>
    </div>
  </div>
</div>
@endsection


