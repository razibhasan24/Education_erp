@extends('admin.app')
@section('page-title', 'Activity Log (Audit Trail)')

@section('page-actions')
    <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#cleanupModal">
        <i class="fas fa-broom"></i> পুরোনো লগ মুছুন
    </button>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4"><div class="small-box bg-info"><div class="inner"><h3>{{ $summary['total'] }}</h3><p>মোট লগ</p></div><div class="icon"><i class="fas fa-history"></i></div></div></div>
    <div class="col-md-4"><div class="small-box bg-success"><div class="inner"><h3>{{ $summary['today'] }}</h3><p>আজকের লগ</p></div><div class="icon"><i class="fas fa-calendar-day"></i></div></div></div>
    <div class="col-md-4"><div class="small-box bg-warning"><div class="inner"><h3>{{ $summary['this_week'] }}</h3><p>এই সপ্তাহের</p></div><div class="icon"><i class="fas fa-calendar-week"></i></div></div></div>
</div>

<div class="card card-primary">
    <form method="GET" class="card-body">
        <div class="row">
            <div class="col-md-2 form-group">
                <label>Log Name</label>
                <select name="log_name" class="form-control form-control-sm">
                    <option value="">সব</option>
                    @foreach($logNames as $ln)<option value="{{ $ln }}" @selected(request('log_name')==$ln)>{{ $ln }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-2 form-group">
                <label>Event</label>
                <select name="event" class="form-control form-control-sm">
                    <option value="">সব</option>
                    @foreach($events as $ev)<option value="{{ $ev }}" @selected(request('event')==$ev)>{{ $ev }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label>ইউজার</label>
                <select name="causer_id" class="form-control form-control-sm select2">
                    <option value="">সব</option>
                    @foreach($users as $u)<option value="{{ $u->id }}" @selected(request('causer_id')==$u->id)>{{ $u->name }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-2 form-group">
                <label>থেকে</label>
                <input type="date" name="from" value="{{ request('from') }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-2 form-group">
                <label>পর্যন্ত</label>
                <input type="date" name="to" value="{{ request('to') }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-1 form-group d-flex align-items-end">
                <button class="btn btn-sm btn-info btn-block"><i class="fas fa-search"></i></button>
            </div>
        </div>
        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm mt-2" placeholder="Description খুঁজুন...">
    </form>
</div>

<div class="card">
    <div class="card-body table-responsive p-0">
        <table class="table table-hover table-sm mb-0">
            <thead class="thead-light">
                <tr>
                    <th width="150">সময়</th>
                    <th>ইউজার</th>
                    <th width="80">Event</th>
                    <th>Subject</th>
                    <th>বিবরণ</th>
                    <th width="60"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td>
                        <small>
                            {{ $log->created_at->format('d M, Y') }}<br>
                            <b>{{ $log->created_at->format('H:i:s') }}</b><br>
                            <span class="text-muted">{{ $log->created_at->diffForHumans() }}</span>
                        </small>
                    </td>
                    <td>
                        @if($log->causer)
                            <i class="fas fa-user-circle text-primary"></i>
                            {{ $log->causer->name ?? 'System' }}<br>
                            <small class="text-muted">{{ $log->causer->email ?? '' }}</small>
                        @else
                            <span class="text-muted"><i class="fas fa-robot"></i> System</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $eventColors = [
                                'created' => 'success',
                                'updated' => 'warning',
                                'deleted' => 'danger',
                                'restored' => 'info',
                            ];
                            $color = $eventColors[$log->event] ?? 'secondary';
                        @endphp
                        <span class="badge badge-{{ $color }}">{{ $log->event }}</span>
                    </td>
                    <td>
                        @if($log->subject_type)
                            <small>
                                <b>{{ class_basename($log->subject_type) }}</b><br>
                                ID: {{ $log->subject_id }}
                            </small>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        {{ Str::limit($log->description, 60) }}
                        @if($log->properties && ($log->properties['old'] ?? null) && ($log->properties['attributes'] ?? null))
                            <br><small class="text-info">
                                <i class="fas fa-exchange-alt"></i>
                                {{ count($log->properties['attributes']) }}টি ফিল্ড পরিবর্তিত
                            </small>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.activity-log.show', $log) }}" class="btn btn-xs btn-info">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">
                    <i class="fas fa-inbox fa-2x mb-2"></i><br>
                    কোনো লগ নেই
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $logs->appends(request()->query())->links() }}</div>
</div>

{{-- Cleanup Modal --}}
<div class="modal fade" id="cleanupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.activity-log.cleanup') }}" method="POST">
                @csrf
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white"><i class="fas fa-broom"></i> পুরোনো লগ মুছুন</h5>
                    <button class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> এই কাজটি ফেরানো যাবে না!
                    </div>
                    <div class="form-group">
                        <label>কত দিনের পুরোনো লগ মুছবেন? *</label>
                        <input type="number" name="days" value="90" min="1" class="form-control" required>
                        <small class="text-muted">উদাহরণ: 90 দিলে ৯০ দিনের পুরোনো সব লগ মুছে যাবে</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" onclick="return confirm('নিশ্চিত?')"><i class="fas fa-trash"></i> মুছুন</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection