@extends('admin.layouts.app')

@section('title', 'Redirect Logs')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header border-bottom">
                <h4 class="card-title">SEO Auto-Redirect Logs</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Requested URL (404/Bare Slug)</th>
                                <th>Redirected To</th>
                                <th>Hits</th>
                                <th>Last Hit At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                            <tr>
                                <td><span class="text-danger">{{ $log->from_url }}</span></td>
                                <td><a href="{{ $log->to_url }}" target="_blank" class="text-success">{{ $log->to_url }}</a></td>
                                <td><span class="badge badge-primary">{{ $log->hits }}</span></td>
                                <td>{{ $log->last_hit_at ? \Carbon\Carbon::parse($log->last_hit_at)->diffForHumans() : '-' }}</td>
                                <td>
                                    <form action="{{ route('admin.redirect-logs.destroy', $log) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this log entry?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm bg-danger-light"><i class="feather-trash-2"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No redirects logged yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $logs->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
