@extends('admin.app')
@section('page-title', 'Trial Balance')

@section('page-actions')
    <button onclick="window.print()" class="btn btn-info btn-sm"><i class="fas fa-print"></i> প্রিন্ট</button>
@endsection

@section('content')
<div class="card card-primary">
    <form method="GET" class="card-body form-inline">
        <label class="mr-1">থেকে:</label>
        <input type="date" name="from" value="{{ $tb['from'] }}" class="form-control form-control-sm mr-2">
        <label class="mr-1">পর্যন্ত:</label>
        <input type="date" name="to" value="{{ $tb['to'] }}" class="form-control form-control-sm mr-2">
        <button class="btn btn-sm btn-info"><i class="fas fa-search"></i> দেখান</button>
    </form>
</div>

<div class="alert alert-{{ $tb['balanced'] ? 'success' : 'danger' }}">
    <i class="fas fa-{{ $tb['balanced'] ? 'check-circle' : 'exclamation-triangle' }}"></i>
    @if($tb['balanced'])
        <b>✓ ব্যালেন্সড</b> — Debit = Credit
    @else
        <b>✗ ব্যালেন্সড নয়!</b> — Debit: ৳{{ number_format($tb['total_debit'], 2) }}, Credit: ৳{{ number_format($tb['total_credit'], 2) }}
    @endif
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Trial Balance ({{ \Carbon\Carbon::parse($tb['from'])->format('d M, Y') }} - {{ \Carbon\Carbon::parse($tb['to'])->format('d M, Y') }})</h3>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-sm">
            <thead class="thead-light">
                <tr>
                    <th>Code</th>
                    <th>Account</th>
                    <th class="text-right">Debit (৳)</th>
                    <th class="text-right">Credit (৳)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tb['rows'] as $r)
                <tr>
                    <td>{{ $r['code'] }}</td>
                    <td>{{ $r['name'] }} <small class="text-muted">({{ $r['name_bn'] }})</small></td>
                    <td class="text-right">{{ $r['debit'] > 0 ? number_format($r['debit'], 2) : '-' }}</td>
                    <td class="text-right">{{ $r['credit'] > 0 ? number_format($r['credit'], 2) : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-light">
                <tr>
                    <th colspan="2" class="text-right">মোট</th>
                    <th class="text-right">৳{{ number_format($tb['total_debit'], 2) }}</th>
                    <th class="text-right">৳{{ number_format($tb['total_credit'], 2) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
