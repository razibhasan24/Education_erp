@extends('admin.app')
@section('page-title', $bankAccount->name . ' — Statement')

@section('page-actions')
    <a href="{{ route('admin.accounting.banks') }}" class="btn btn-secondary btn-sm">ফিরে যান</a>
    <form action="{{ route('admin.accounting.banks.auto-match', $bankAccount) }}" method="POST" class="d-inline">
        @csrf
        <button class="btn btn-warning btn-sm"><i class="fas fa-magic"></i> Auto Match</button>
    </form>
    <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#importModal">
        <i class="fas fa-file-upload"></i> CSV Import
    </button>
    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addStatementModal">
        <i class="fas fa-plus"></i> Manual Entry
    </button>
@endsection

@section('content')
<div class="row">
    <div class="col-md-3"><div class="small-box bg-success"><div class="inner"><h3>৳{{ number_format($summary['total_credit'], 0) }}</h3><p>মোট Credit (জমা)</p></div><div class="icon"><i class="fas fa-arrow-down"></i></div></div></div>
    <div class="col-md-3"><div class="small-box bg-danger"><div class="inner"><h3>৳{{ number_format($summary['total_debit'], 0) }}</h3><p>মোট Debit (উত্তোলন)</p></div><div class="icon"><i class="fas fa-arrow-up"></i></div></div></div>
    <div class="col-md-3"><div class="small-box bg-warning"><div class="inner"><h3>{{ $summary['unmatched'] }}</h3><p>Unmatched</p></div><div class="icon"><i class="fas fa-question-circle"></i></div></div></div>
    <div class="col-md-3"><div class="small-box bg-info"><div class="inner"><h3>{{ $summary['matched'] }}</h3><p>Matched</p></div><div class="icon"><i class="fas fa-check-circle"></i></div></div></div>
</div>

<div class="card card-primary">
    <form method="GET" class="card-body form-inline">
        <label class="mr-1">Status:</label>
        <select name="status" class="form-control form-control-sm mr-2">
            <option value="">সব</option>
            <option value="unmatched" @selected(request('status')==='unmatched')>Unmatched</option>
            <option value="matched" @selected(request('status')==='matched')>Matched</option>
            <option value="ignored" @selected(request('status')==='ignored')>Ignored</option>
        </select>
        <input type="date" name="from" value="{{ request('from') }}" class="form-control form-control-sm mr-2">
        <input type="date" name="to" value="{{ request('to') }}" class="form-control form-control-sm mr-2">
        <button class="btn btn-sm btn-info"><i class="fas fa-search"></i> ফিল্টার</button>
    </form>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover datatable">
            <thead>
                <tr>
                    <th>তারিখ</th>
                    <th>বিবরণ</th>
                    <th>Reference</th>
                    <th>TrxID</th>
                    <th class="text-right">Debit</th>
                    <th class="text-right">Credit</th>
                    <th class="text-right">Balance</th>
                    <th>স্ট্যাটাস</th>
                    <th>Matched With</th>
                    <th>অ্যাকশন</th>
                </tr>
            </thead>
            <tbody>
                @foreach($statements as $s)
                <tr class="{{ $s->status === 'matched' ? 'table-success' : ($s->status === 'unmatched' ? 'table-warning' : '') }}">
                    <td>{{ $s->transaction_date->format('d M, Y') }}</td>
                    <td>{{ $s->description }}</td>
                    <td>{{ $s->reference ?? '-' }}</td>
                    <td>{{ $s->transaction_id ?? '-' }}</td>
                    <td class="text-right text-danger">{{ $s->debit > 0 ? number_format($s->debit, 2) : '-' }}</td>
                    <td class="text-right text-success">{{ $s->credit > 0 ? number_format($s->credit, 2) : '-' }}</td>
                    <td class="text-right"><b>{{ number_format($s->balance, 2) }}</b></td>
                    <td>
                        @if($s->status === 'matched') <span class="badge badge-success">Matched</span>
                        @elseif($s->status === 'ignored') <span class="badge badge-secondary">Ignored</span>
                        @else <span class="badge badge-warning">Unmatched</span> @endif
                    </td>
                    <td>
                        @if($s->matchedPayment)
                            <small>
                                <b>{{ $s->matchedPayment->receipt_no }}</b><br>
                                {{ $s->matchedPayment->student->name ?? '' }}
                            </small>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($s->status === 'unmatched')
                            <button class="btn btn-xs btn-success" onclick="openMatchModal({{ $s->id }})">
                                <i class="fas fa-link"></i> Match
                            </button>
                            <form action="{{ route('admin.accounting.banks.ignore', $s) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-xs btn-secondary"><i class="fas fa-ban"></i></button>
                            </form>
                        @elseif($s->status === 'matched')
                            <form action="{{ route('admin.accounting.banks.unmatch', $s) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-xs btn-warning"><i class="fas fa-unlink"></i></button>
                            </form>
                        @endif
                        <form action="{{ route('admin.accounting.banks.statements.destroy', $s) }}" method="POST" class="d-inline" onsubmit="return confirm('নিশ্চিত?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-2">{{ $statements->links() }}</div>
    </div>
</div>

{{-- Import Modal --}}
<div class="modal fade" id="importModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.accounting.banks.import', $bankAccount) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header"><h5 class="modal-title">CSV Import</h5><button class="close" data-dismiss="modal">&times;</button></div>
                <div class="modal-body">
                    <div class="alert alert-info small">
                        <b>CSV ফরম্যাট:</b><br>
                        <code>Date, Description, Reference, TransactionID, Debit, Credit</code>
                    </div>
                    <div class="form-group">
                        <label>CSV ফাইল *</label>
                        <input type="file" name="csv" class="form-control-file" accept=".csv,.txt" required>
                    </div>
                </div>
                <div class="modal-footer"><button class="btn btn-primary"><i class="fas fa-upload"></i> Import</button></div>
            </form>
        </div>
    </div>
</div>

{{-- Manual Statement Modal --}}
<div class="modal fade" id="addStatementModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.accounting.banks.statements.store', $bankAccount) }}" method="POST">
                @csrf
                <div class="modal-header"><h5 class="modal-title">Manual Entry</h5><button class="close" data-dismiss="modal">&times;</button></div>
                <div class="modal-body">
                    <div class="form-group"><label>তারিখ *</label><input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" class="form-control" required></div>
                    <div class="form-group"><label>বিবরণ *</label><input type="text" name="description" class="form-control" required></div>
                    <div class="form-group"><label>Reference</label><input type="text" name="reference" class="form-control"></div>
                    <div class="form-group"><label>Transaction ID</label><input type="text" name="transaction_id" class="form-control"></div>
                    <div class="row">
                        <div class="col-6 form-group"><label>Debit</label><input type="number" step="0.01" name="debit" value="0" class="form-control"></div>
                        <div class="col-6 form-group"><label>Credit</label><input type="number" step="0.01" name="credit" value="0" class="form-control"></div>
                    </div>
                </div>
                <div class="modal-footer"><button class="btn btn-primary"><i class="fas fa-save"></i> সংরক্ষণ</button></div>
            </form>
        </div>
    </div>
</div>

{{-- Match Modal --}}
<div class="modal fade" id="matchModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Payment Match করুন</h5><button class="close" data-dismiss="modal">&times;</button></div>
            <div class="modal-body" id="matchBody">লোড হচ্ছে...</div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openMatchModal(statementId) {
    const modal = $('#matchModal');
    modal.modal('show');
    document.getElementById('matchBody').innerHTML = 'লোড হচ্ছে...';

    fetch(`/admin/accounting/banks/statements/${statementId}/suggest`)
        .then(r => r.json())
        .then(data => {
            if (data.length === 0) {
                document.getElementById('matchBody').innerHTML = '<div class="alert alert-warning">কোনো matching payment পাওয়া যায়নি।</div>';
                return;
            }
            let html = '<table class="table table-sm"><thead><tr><th>Receipt</th><th>Student</th><th>Amount</th><th>Date</th><th>Action</th></tr></thead><tbody>';
            data.forEach(p => {
                html += `<tr>
                    <td>${p.receipt_no}</td>
                    <td>${p.student?.name ?? '-'}</td>
                    <td>৳${p.amount}</td>
                    <td>${p.payment_date}</td>
                    <td>
                        <form action="/admin/accounting/banks/statements/${statementId}/match" method="POST">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="payment_id" value="${p.id}">
                            <button class="btn btn-xs btn-success">Match</button>
                        </form>
                    </td>
                </tr>`;
            });
            html += '</tbody></table>';
            document.getElementById('matchBody').innerHTML = html;
        });
}
</script>
@endpush