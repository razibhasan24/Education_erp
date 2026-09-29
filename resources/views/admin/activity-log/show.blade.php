@extends('admin.app')
@section('page-title', 'Log বিস্তারিত')

@section('page-actions')
    <a href="{{ route('admin.activity-log.index') }}" class="btn btn-secondary btn-sm">ফিরে যান</a>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Activity তথ্য</h3></div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr><th>সময়</th><td>{{ $activity->created_at->format('d M, Y H:i:s') }}</td></tr>
                    <tr><th>Log Name</th><td><span class="badge badge-info">{{ $activity->log_name }}</span></td></tr>
                    <tr><th>Event</th><td><span class="badge badge-warning">{{ $activity->event }}</span></td></tr>
                    <tr><th>ইউজার</th><td>{{ $activity->causer->name ?? 'System' }}</td></tr>
                    <tr><th>Description</th><td>{{ $activity->description }}</td></tr>
                    @if($activity->subject_type)
                        <tr><th>Subject Type</th><td>{{ $activity->subject_type }}</td></tr>
                        <tr><th>Subject ID</th><td>{{ $activity->subject_id }}</td></tr>
                    @endif
                    <tr><th>IP</th><td>{{ $activity->properties['ip'] ?? '-' }}</td></tr>
                    <tr><th>User Agent</th><td><small>{{ Str::limit($activity->properties['user_agent'] ?? '-', 80) }}</small></td></tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        @if($activity->properties && ($activity->properties['attributes'] ?? null))
        <div class="card card-warning">
            <div class="card-header"><h3 class="card-title">পরিবর্তনের বিস্তারিত</h3></div>
            <div class="card-body table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th width="30%">Field</th>
                            <th>পুরোনো মান</th>
                            <th>নতুন মান</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $old = $activity->properties['old'] ?? [];
                            $new = $activity->properties['attributes'] ?? [];
                        @endphp
                        @foreach($new as $key => $newVal)
                            @if(!in_array($key, ['updated_at', 'created_at']))
                            <tr>
                                <td><b>{{ $key }}</b></td>
                                <td class="text-danger">
                                    @if(is_array($old[$key] ?? null))
                                        <pre>{{ json_encode($old[$key], JSON_PRETTY_PRINT) }}</pre>
                                    @else
                                        {{ $old[$key] ?? '—' }}
                                    @endif
                                </td>
                                <td class="text-success">
                                    @if(is_array($newVal))
                                        <pre>{{ json_encode($newVal, JSON_PRETTY_PRINT) }}</pre>
                                    @else
                                        {{ $newVal }}
                                    @endif
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> কোনো পরিবর্তনের বিস্তারিত নেই (সম্ভবত নতুন তৈরি বা মুছে ফেলা)।
        </div>
        @endif
    </div>
</div>
@endsection