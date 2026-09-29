@extends('admin.app')
@section('page-title', 'Refund: ' . $refund->refund_no)

@section('page-actions')
    <a href="{{ route('admin.fees.refunds.index') }}" class="btn btn-secondary btn-sm">ফিরে যান</a>
    <button onclick="window.print()" class="btn btn-info btn-sm"><i class="fas fa-print"></i> প্রিন্ট</button>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Refund বিস্তারিত</h3>
                <div class="card-tools">
                    @if($refund->status === 'paid') <span class="badge badge-success">Paid</span>
                    @elseif($refund->status === 'approved') <span class="badge badge-info">Approved</span>
                    @elseif($refund->status === 'rejected') <span class="badge badge-danger">Rejected</span>
                    @else <span class="badge badge-warning">Pending</span> @endif
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr><th width="30%">Refund নম্বর</th><td><b>{{ $refund->refund_no }}</b></td></tr>
                    <tr><th>শিক্ষার্থী</th><td>{{ $refund->student->name ?? '-' }} ({{ $refund->student->student_id ?? '' }})</td></tr>
                    <tr><th>শ্রেণি</th><td>{{ $refund->student->schoolClass->name ?? '-' }} {{ $refund->student->section->name ?? '' }}</td></tr>
                    <tr><th>পরিমাণ</th><td><h4 class="text-danger mb-0">৳{{ number_format($refund->amount, 2) }}</h4></td></tr>
                    <tr><th>কারণ</th><td>{{ $refund->reason }}</td></tr>
                    <tr><th>Refund তারিখ</th><td>{{ $refund->refund_date->format('d F, Y') }}</td></tr>
                    <tr><th>মেথড</th><td><span class="badge badge-info">{{ $refund->refund_method }}</span></td></tr>
                    @if($refund->transaction_id)<tr><th>Trx ID</th><td>{{ $refund->transaction_id }}</td></tr>@endif
                    @if($refund->invoice)<tr><th>সংশ্লিষ্ট ইনভয়েস</th><td>{{ $refund->invoice->invoice_no }}</td></tr>@endif
                    @if($refund->payment)<tr><th>সংশ্লিষ্ট পেমেন্ট</th><td>{{ $refund->payment->receipt_no }} (৳{{ number_format($refund->payment->amount, 2) }})</td></tr>@endif
                    @if($refund->approvedBy)
                        <tr><th>অনুমোদনকারী</th><td>{{ $refund->approvedBy->name }} — {{ $refund->approved_at?->format('d M, Y H:i') }}</td></tr>
                    @endif
                    @if($refund->remarks)<tr><th>মন্তব্য</th><td>{{ $refund->remarks }}</td></tr>@endif
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-info">
            <div class="card-header"><h3 class="card-title">অ্যাকশন</h3></div>
            <div class="card-body">
                @if($refund->status === 'pending')
                    <form action="{{ route('admin.fees.refunds.approve', $refund) }}" method="POST" class="mb-2">
                        @csrf
                        <button class="btn btn-success btn-block"><i class="fas fa-check"></i> Approve</button>
                    </form>
                    <button class="btn btn-danger btn-block" data-toggle="modal" data-target="#rejectModal">
                        <i class="fas fa-times"></i> Reject
                    </button>
                @elseif($refund->status === 'approved')
                    <form action="{{ route('admin.fees.refunds.mark-paid', $refund) }}" method="POST">
                        @csrf
                        <button class="btn btn-primary btn-block"><i class="fas fa-money-bill"></i> Mark as Paid</button>
                    </form>
                @else
                    <p class="text-muted mb-0">এই Refund সম্পন্ন হয়েছে।</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.fees.refunds.reject', $refund) }}" method="POST">
                @csrf
                <div class="modal-header"><h5 class="modal-title">Reject কারণ</h5><button class="close" data-dismiss="modal">&times;</button></div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>কারণ *</label>
                        <textarea name="remarks" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger"><i class="fas fa-times"></i> Reject করুন</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection