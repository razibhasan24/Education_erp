@extends('admin.app')
@section('page-title', 'General Ledger')

@section('content')
<div class="card card-primary">
    <form method="GET" class="card-body form-inline">
        <label class="mr-1">Account:</label>
        <select name="account_id" class="form-control form-control-sm mr-2 select2" required>
            <option value="">নির্বাচন</option>
            @foreach($accounts as $a)
                <option value="{{ $a->id }}" @selected(request('account_id')==$a->id)>{{ $a->code }} - {{ $a->name }}</option>
            @endforeach
        </select>
        <label class="mr-1">থেকে:</label>
        <input type="date" name="from" value="{{ request('from') }}" class="form-control form-control-sm mr-2">
        <label class="mr-1">পর্যন্ত:</label>
        <input type="date" name="to" value="{{ request('to') }}" class="form-control form-control-sm mr-2">
        <button class="btn btn-sm btn-info"><i class="fas fa-search"></i> দেখান</button>
    </form>
</div>

@if($ledger)
<div class="card card-success">
    <div class="card-header">
        <h3 class="card-title">{{ $account->code }} - {{ $account->name }}</h3>
        <div class="card-tools">
            <button onclick="window.print()" class="btn btn-xs btn-light"><i class="fas fa-print"></i> প্রিন্ট</button>
        </div>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-sm">
            <thead class="thead-light">
                <tr>
                    <th>তারিখ</th>
                    <th>Voucher</th>
                    <th>Narration</th>
                    <th class="text-right">Debit</th>
                    <th class="text-right">Credit</th>
                    <th class="text-right">Balance</th>
                </tr>
            </thead>
            <tbody>
                <tr class="bg-light">
                    <td colspan="5"><b>Opening Balance</b></td>
                    <td class="text-right"><b>৳{{ number_format($ledger['opening'], 2) }}</b></td>
                </tr>
                @foreach($ledger['rows'] as $r)
                <tr>
                    <td>{{ $r['date']->format('d M, Y') }}</td>
                    <td>{{ $r['voucher_no'] }}</td>
                    <td>{{ $r['narration'] }}</td>
                    <td class="text-right">{{ $r['debit'] > 0 ? number_format($r['debit'], 2) : '-' }}</td>
                    <td class="text-right">{{ $r['credit'] > 0 ? number_format($r['credit'], 2) : '-' }}</td>
                    <td class="text-right"><b>{{ number_format($r['balance'], 2) }}</b></td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-light">
                <tr>
                    <th colspan="5" class="text-right">Closing Balance</th>
                    <th class="text-right">৳{{ number_format($ledger['closing'], 2) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endif
@endsection
