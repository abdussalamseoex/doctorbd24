@extends('admin.layouts.app')

@section('title', 'Redirect Logs')

@section('content')
<div class="row mb-3">
    <div class="col-sm-12 d-flex justify-content-between align-items-center">
        <h4 class="page-title mb-0">Redirect Logs</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRedirectModal">
            <i class="feather-plus mr-1"></i> Add New Redirect
        </button>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card shadow-sm border-0">
            <div class="card-header border-bottom bg-white py-3">
                <h5 class="card-title mb-0"><i class="feather-link text-primary mr-2"></i> SEO Redirect Management</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-center mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Requested URL (404/Bare Slug)</th>
                                <th><i class="feather-arrow-right text-muted"></i> Redirected To</th>
                                <th>Hits</th>
                                <th>Last Hit At</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                            <tr>
                                <td>
                                    @if($log->hits == 0)
                                        <span class="badge bg-secondary mb-1">Manual</span><br>
                                    @else
                                        <span class="badge bg-info mb-1">Auto</span><br>
                                    @endif
                                    <span class="text-danger font-weight-bold">{{ $log->from_url }}</span>
                                </td>
                                <td><a href="{{ $log->to_url }}" target="_blank" class="text-success">{{ $log->to_url }}</a></td>
                                <td><span class="badge bg-primary rounded-pill px-3">{{ $log->hits }}</span></td>
                                <td>{{ $log->last_hit_at ? \Carbon\Carbon::parse($log->last_hit_at)->diffForHumans() : '-' }}</td>
                                <td class="text-right">
                                    <form action="{{ route('admin.redirect-logs.destroy', $log) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this log entry?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm bg-danger-light"><i class="feather-trash-2"></i> Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center p-5">
                                    <div class="text-muted">
                                        <i class="feather-alert-circle" style="font-size: 2rem;"></i>
                                        <p class="mt-2 mb-0">No redirects logged yet.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white mt-1">
                {{ $logs->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<!-- Add Redirect Modal -->
<div class="modal fade" id="addRedirectModal" tabindex="-1" aria-labelledby="addRedirectModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <form action="{{ route('admin.redirect-logs.store') }}" method="POST">
          @csrf
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title text-white" id="addRedirectModalLabel">Add Manual Redirect</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="alert alert-info border-0 text-sm">
                <i class="feather-info"></i> Format: <code>doctor/slug</code> or <code>hospital/slug</code>. Do not include full domain in 'From URL'.
            </div>
            <div class="mb-3">
                <label class="form-label font-weight-bold">From URL (Relative Path)</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted">{{ url('/') }}/</span>
                    <input type="text" name="from_url" class="form-control" placeholder="e.g. hospital/popular-diagnostic-centre" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label font-weight-bold">To URL (Full Path)</label>
                <input type="url" name="to_url" class="form-control" placeholder="e.g. {{ url('/hospital/popular-diagnostic') }}" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Redirect</button>
          </div>
      </form>
    </div>
  </div>
</div>
@endsection
