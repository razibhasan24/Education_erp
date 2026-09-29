@extends('admin.app')
@section('page-title', 'ফি ফেরত (Refund)')

@section('page-actions')
    <a href="{{ route('admin.fees.refunds.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus"></i> নতুন Refund
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>৳{{ number_format($summary['total'], 2) }}</h3>
                <p>মোট Paid Refund</p>
            </div>
            <div class="icon"><i class="fas fa-undo"></i></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $summary['pending'] }}</h3>
                <p>Pending Refund</p>
            </div>
            <div class="icon"><i class="fas fa-clock"></i></div>
        </div>
    </div>
</div>

<div class="card card-primary">
    <form method="GET" class="card-body form-inline">
        <label class="mr-1">স্ট্যাটাস:</label>
        <select name="status" class="form-control form-control-sm mr-2">
            <option value="">সব</option>
            <option value="pending" @selected(request('status')=='pending')>Pending</option>
            <option value="approved" @selected(request('status')=='approved')>Approved</option>
            <option value="paid" @selected(request('status')=='paid')>Paid</option>
            <option value="rejected" @selected(request('status')=='rejected')>Rejected</option>
        </select>
        <label class="mr-1">শিক্ষার্থী:</label>
        <select name="student_id" class="form-control form-control-sm mr-2 select2">
            <option value="">সব</option>
            @foreach($students as $s)<option value="{{ $s->id }}" @selected(request('student_id')==$s->id)>{{ $s->name }}</option>@endforeach
        </select>
        <button class="btn btn-sm btn-info"><i class="fas fa-search"></i> ফিল্টার</button>
    </form>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover datatable">
            <thead>
                <tr>
                    <th>Refund #</th>
                    <th>শিক্ষার্থী</th>
                    <th class="text-right">পরিমাণ</th>
                    <th>কারণ</th>
                    <th>তারিখ</th>
                    <th>মেথড</th>
                    <th>স্ট্যাটাস</th>
                    <th>অ্যাকশন</th>
                </tr>
            </thead>
            <tbody>
                @foreach($refunds as $r)
                <tr>
                    <td><b>{{ $r->refund_no }}</b></td>
                    <td>
                        {{ $r->student->name ?? '-' }}<br>
                        <small class="text-muted">{{ $r->student->student_id ?? '' }}</small>
                    </td>
                    <td class="text-right"><b>৳{{ number_format($r->amount, 2) }}</b></td>
                    <td>{{ Str::limit($r->reason, 40) }}</td>
                    <td>{{ $r->refund_date->format('d M, Y') }}</td>
                    <td><span class="badge badge-info">{{ $r->refund_method }}</span></td>
                    <td>
                        @if($r->status === 'paid') <span class="badge badge-success">Paid</span>
                        @elseif($r->status === 'approved') <span class="badge badge-info">Approved</span>
                        @elseif($r->status === 'rejected') <span class="badge badge-danger">Rejected</span>
                        @else <span class="badge badge-warning">Pending</span> @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.fees.refunds.show', $r) }}" class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a>
                        @if($r->status === 'pending')
                            <form action="{{ route('admin.fees.refunds.approve', $r) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-xs btn-success" title="Approve"><i class="fas fa-check"></i></button>
                            </form>
                        @endif
                        @if($r->status === 'approved')
                            <form action="{{ route('admin.fees.refunds.mark-paid', $r) }}" method="POST" class="d-inline" onsubmit="return confirm('Paid হিসেবে চিহ্নিত করবেন?')">
                                @csrf
                                <button class="btn btn-xs btn-primary" title="Mark Paid"><i class="fas fa-money-bill"></i></button>
                            </form>
                        @endif
                        @if($r->status !== 'paid')
                            <form action="{{ route('admin.fees.refunds.destroy', $r) }}" method="POST" class="d-inline" onsubmit="return confirm('নিশ্চিত?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-2">{{ $refunds->links() }}</div>
    </div>
</div>
@endsection