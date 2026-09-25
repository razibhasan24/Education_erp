@extends('admin.app')
@section('page-title', 'ফি ইনভয়েস')

@section('page-actions')
    <a href="{{ route('admin.fees.invoices.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> নতুন ইনভয়েস</a>
    <a href="{{ route('admin.fees.invoices.bulk') }}" class="btn btn-info btn-sm"><i class="fas fa-copy"></i> বাল্ক ইনভয়েস</a>
@endsection

@section('content')
<div class="card card-primary">
    <form method="GET" class="card-body form-inline">
        <select name="class_id" class="form-control form-control-sm mr-2 select2">
            <option value="">সব শ্রেণি</option>
            @foreach($classes as $c)<option value="{{ $c->id }}" @selected(request('class_id')==$c->id)>{{ $c->name }}</option>@endforeach
        </select>
        <select name="status" class="form-control form-control-sm mr-2">
            <option value="">সব স্ট্যাটাস</option>
            @foreach(\App\Models\FeeInvoice::statusLabels() as $k=>$v)
                <option value="{{ $k }}" @selected(request('status')==$k)>{{ $v['label'] }}</option>
            @endforeach
        </select>
        <button class="btn btn-sm btn-info"><i class="fas fa-search"></i> ফিল্টার</button>
        <a href="{{ route('admin.fees.invoices.index') }}" class="btn btn-sm btn-secondary ml-1">রিসেট</a>
    </form>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover datatable">
            <thead>
                <tr>
                    <th>ইনভয়েস #</th>
                    <th>শিক্ষার্থী</th>
                    <th>শ্রেণি</th>
                    <th>মাস/বছর</th>
                    <th>মোট</th>
                    <th>পরিশোধ</th>
                    <th>বকেয়া</th>
                    <th>স্ট্যাটাস</th>
                    <th>অ্যাকশন</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $inv)
                <tr>
                    <td><b>{{ $inv->invoice_no }}</b></td>
                    <td>{{ $inv->student->name ?? '-' }}<br><small class="text-muted">{{ $inv->student->student_id ?? '' }}</small></td>
                    <td>{{ $inv->schoolClass->name ?? '-' }} {{ $inv->section->name ?? '' }}</td>
                    <td>{{ $inv->month ? str_pad($inv->month,2,'0',STR_PAD_LEFT).'/'.$inv->year : '-' }}</td>
                    <td class="text-right">৳{{ number_format($inv->total_amount, 2) }}</td>
                    <td class="text-right text-success">৳{{ number_format($inv->paid_amount, 2) }}</td>
                    <td class="text-right text-danger"><b>৳{{ number_format($inv->due_amount, 2) }}</b></td>
                    <td>
                        @php $st = \App\Models\FeeInvoice::statusLabels()[$inv->status]; @endphp
                        <span class="badge badge-{{ $st['color'] }}">{{ $st['label'] }}</span>
                    </td>
                    <td>
                        <a href="{{ route('admin.fees.invoices.show', $inv) }}" class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a>
                        @if($inv->due_amount > 0)
                            <a href="{{ route('admin.fees.payments.create', $inv) }}" class="btn btn-xs btn-success"><i class="fas fa-hand-holding-usd"></i></a>
                        @endif
                        <form action="{{ route('admin.fees.invoices.destroy', $inv) }}" method="POST" class="d-inline" onsubmit="return confirm('নিশ্চিত?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
