@extends('admin.app')
@section('page-title', 'Journal Entries')

@section('page-actions')
    <a href="{{ route('admin.accounting.journal-entries.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus"></i> নতুন Journal Entry
    </a>
@endsection

@section('content')
<div class="card card-primary">
    <form method="GET" class="card-body form-inline">
        <label class="mr-1">থেকে:</label>
        <input type="date" name="from" value="{{ request('from') }}" class="form-control form-control-sm mr-2">
        <label class="mr-1">পর্যন্ত:</label>
        <input type="date" name="to" value="{{ request('to') }}" class="form-control form-control-sm mr-2">
        <select name="type" class="form-control form-control-sm mr-2">
            <option value="">সব ধরন</option>
            @foreach(['manual','fee_payment','expense','salary','opening','adjustment','refund','scholarship'] as $t)
                <option value="{{ $t }}" @selected(request('type')==$t)>{{ $t }}</option>
            @endforeach
        </select>
        <button class="btn btn-sm btn-info"><i class="fas fa-search"></i> ফিল্টার</button>
    </form>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover datatable">
            <thead>
                <tr>
                    <th>তারিখ</th>
                    <th>Voucher #</th>
                    <th>Narration</th>
                    <th>ধরন</th>
                    <th class="text-right">Debit</th>
                    <th class="text-right">Credit</th>
                    <th>স্ট্যাটাস</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($entries as $e)
                <tr>
                    <td>{{ $e->entry_date->format('d M, Y') }}</td>
                    <td><b>{{ $e->voucher_no }}</b></td>
                    <td>{{ Str::limit($e->narration, 50) }}</td>
                    <td><span class="badge badge-info">{{ $e->type }}</span></td>
                    <td class="text-right">৳{{ number_format($e->total_debit, 2) }}</td>
                    <td class="text-right">৳{{ number_format($e->total_credit, 2) }}</td>
                    <td>
                        @if($e->status === 'posted') <span class="badge badge-success">Posted</span>
                        @elseif($e->status === 'cancelled') <span class="badge badge-danger">Cancelled</span>
                        @else <span class="badge badge-warning">Draft</span> @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.accounting.journal-entries.show', $e) }}" class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a>
                        @if($e->status === 'posted')
                        <form action="{{ route('admin.accounting.journal-entries.cancel', $e) }}" method="POST" class="d-inline" onsubmit="return confirm('বাতিল করবেন?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-xs btn-danger"><i class="fas fa-ban"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-2">{{ $entries->links() }}</div>
    </div>
</div>
@endsection
