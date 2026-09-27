@extends('admin.app')
@section('page-title', 'Journal Entry: ' . $entry->voucher_no)

@section('page-actions')
    <a href="{{ route('admin.accounting.journal-entries') }}" class="btn btn-secondary btn-sm">ফিরে যান</a>
@endsection

@section('content')
<div class="card card-primary">
    <div class="card-body">
        <div class="row">
            <div class="col-md-3"><b>তারিখ:</b> {{ $entry->entry_date->format('d M, Y') }}</div>
            <div class="col-md-3"><b>Voucher #:</b> {{ $entry->voucher_no }}</div>
            <div class="col-md-3"><b>Reference:</b> {{ $entry->reference ?? '-' }}</div>
            <div class="col-md-3"><b>ধরন:</b> <span class="badge badge-info">{{ $entry->type }}</span></div>
            <div class="col-md-12 mt-2"><b>Narration:</b> {{ $entry->narration }}</div>
            <div class="col-md-12 mt-1"><b>তৈরি করেছেন:</b> {{ $entry->creator->name ?? '-' }} ({{ $entry->created_at->format('d M, Y H:i') }})</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>Code</th>
                    <th>Account</th>
                    <th>Description</th>
                    <th class="text-right">Debit (৳)</th>
                    <th class="text-right">Credit (৳)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($entry->items as $item)
                <tr>
                    <td>{{ $item->account->code ?? '-' }}</td>
                    <td>{{ $item->account->name ?? '-' }}</td>
                    <td>{{ $item->description ?? '-' }}</td>
                    <td class="text-right">{{ $item->debit > 0 ? number_format($item->debit, 2) : '-' }}</td>
                    <td class="text-right">{{ $item->credit > 0 ? number_format($item->credit, 2) : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-light">
                <tr>
                    <th colspan="3" class="text-right">মোট</th>
                    <th class="text-right">৳{{ number_format($entry->total_debit, 2) }}</th>
                    <th class="text-right">৳{{ number_format($entry->total_credit, 2) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
