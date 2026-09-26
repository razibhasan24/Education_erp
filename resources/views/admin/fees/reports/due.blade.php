@extends('admin.app')
@section('page-title', 'বকেয়া তালিকা')

@section('page-actions')
    <button onclick="window.print()" class="btn btn-info btn-sm"><i class="fas fa-print"></i> প্রিন্ট</button>
    <a href="{{ route('admin.pdf.due-list', request()->query()) }}" class="btn btn-danger btn-sm">
    <i class="fas fa-file-pdf"></i> PDF
</a>
@endsection

@section('content')
<div class="card card-primary">
    <form method="GET" class="card-body form-inline">
        <select name="class_id" class="form-control form-control-sm mr-2 select2" onchange="this.form.submit()">
            <option value="">সব শ্রেণি</option>
            @foreach($classes as $c)<option value="{{ $c->id }}" @selected($classId==$c->id)>{{ $c->name }}</option>@endforeach
        </select>
        <select name="section_id" class="form-control form-control-sm mr-2">
            <option value="">সব শাখা</option>
            @foreach($sections as $s)<option value="{{ $s->id }}" @selected($sectionId==$s->id)>{{ $s->name }}</option>@endforeach
        </select>
        <select name="month" class="form-control form-control-sm mr-2">
            <option value="">সব মাস</option>
            @foreach($months as $k=>$v)<option value="{{ str_pad($k,2,'0',STR_PAD_LEFT) }}" @selected(request('month')==str_pad($k,2,'0',STR_PAD_LEFT))>{{ $v }}</option>@endforeach
        </select>
        <input type="number" name="year" value="{{ request('year', date('Y')) }}" class="form-control form-control-sm mr-2" style="width:100px">
        <button class="btn btn-sm btn-info"><i class="fas fa-search"></i> ফিল্টার</button>
    </form>
</div>

<div class="callout callout-danger">
    <h5><i class="fas fa-exclamation-triangle"></i> মোট বকেয়া: ৳ {{ number_format($totalDue, 2) }}</h5>
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
                    <th>পরিশোধিত</th>
                    <th>বকেয়া</th>
                    <th>স্ট্যাটাস</th>
                    <th>অ্যাকশন</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $inv)
                <tr>
                    <td>{{ $inv->invoice_no }}</td>
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
                        <a href="{{ route('admin.fees.payments.create', $inv) }}" class="btn btn-xs btn-success">
                            <i class="fas fa-hand-holding-usd"></i> পেমেন্ট
                        </a>
                        <a href="{{ route('admin.fees.invoices.show', $inv) }}" class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
