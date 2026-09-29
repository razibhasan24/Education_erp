@extends('admin.app')
@section('title', $bankAccount->name)
@section('page-title', $bankAccount->name . ' — লেনদেন')

@php
    $colorMap = [
        'bkash' => 'danger',
        'nagad' => 'warning',
        'rocket' => 'primary',
        'upay' => 'info',
    ];
    $iconMap = [
        'bkash' => 'fa-wallet',
        'nagad' => 'fa-money-bill-wave',
        'rocket' => 'fa-rocket',
        'upay' => 'fa-mobile-alt',
    ];
    $color = $colorMap[$bankAccount->type] ?? 'secondary';
    $icon = $iconMap[$bankAccount->type] ?? 'fa-mobile-alt';
@endphp

@section('page-actions')
    <a href="{{ route('admin.accounting.mobile.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left"></i> ফিরে যান
    </a>
    <form action="{{ route('admin.accounting.mobile.auto-match', $bankAccount) }}" method="POST" class="d-inline">
        @csrf
        <button class="btn btn-warning btn-sm" onclick="return confirm('স্বয়ংক্রিয়ভাবে ম্যাচ করা হবে?')">
            <i class="fas fa-magic"></i> Auto Match
        </button>
    </form>
    <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#importModal">
        <i class="fas fa-file-upload"></i> CSV Import
    </button>
    <button class="btn btn-{{ $color }} btn-sm" data-toggle="modal" data-target="#addStatementModal">
        <i class="fas fa-plus"></i> ম্যানুয়াল এন্ট্রি
    </button>
@endsection

@section('content')

    {{-- ========== Account Summary Cards ========== --}}
    <div class="row">
        <div class="col-md-3">
            <div class="info-box bg-{{ $color }}">
                <span class="info-box-icon"><i class="fas {{ $icon }}"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ \App\Models\BankAccount::typeLabels()[$bankAccount->type] }}</span>
                    <span class="info-box-number">৳{{ number_format($bankAccount->current_balance, 2) }}</span>
                    <span class="progress-description">
                        {{ $bankAccount->account_no }}
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-success">
                <span class="info-box-icon"><i class="fas fa-arrow-down"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">মোট জমা (Credit)</span>
                    <span class="info-box-number">৳{{ number_format($summary['total_credit'], 2) }}</span>
                    <span class="progress-description">
                        এই মাস: ৳{{ number_format($summary['this_month_in'], 2) }}
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-danger">
                <span class="info-box-icon"><i class="fas fa-arrow-up"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">মোট উত্তোলন (Debit)</span>
                    <span class="info-box-number">৳{{ number_format($summary['total_debit'], 2) }}</span>
                    <span class="progress-description">
                        আজ: ৳{{ number_format($summary['today_out'], 2) }}
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-info">
                <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Matched / Unmatched</span>
                    <span class="info-box-number">
                        {{ $summary['matched'] }} / {{ $summary['unmatched'] }}
                    </span>
                    <span class="progress-description">
                        @if ($summary['unmatched'] > 0)
                            <span class="text-warning">
                                <i class="fas fa-exclamation-triangle"></i> {{ $summary['unmatched'] }}টি ম্যাচ করা বাকি
                            </span>
                        @else
                            <span class="text-success">
                                <i class="fas fa-check"></i> সব ম্যাচ হয়েছে
                            </span>
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ========== Today's Activity ========== --}}
    <div class="row">
        <div class="col-md-6">
            <div class="card card-success card-outline">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">আজকের জমা</h6>
                            <h3 class="text-success mb-0">৳{{ number_format($summary['today_in'], 2) }}</h3>
                        </div>
                        <div class="text-right">
                            <i class="fas fa-arrow-down fa-3x text-success opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-danger card-outline">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">আজকের উত্তোলন</h6>
                            <h3 class="text-danger mb-0">৳{{ number_format($summary['today_out'], 2) }}</h3>
                        </div>
                        <div class="text-right">
                            <i class="fas fa-arrow-up fa-3x text-danger opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========== Filter ========== --}}
    <div class="card card-primary">
        <form method="GET" class="card-body">
            <div class="row">
                <div class="col-md-3 form-group">
                    <label>স্ট্যাটাস</label>
                    <select name="status" class="form-control form-control-sm">
                        <option value="">সব</option>
                        <option value="unmatched" @selected(request('status') === 'unmatched')>Unmatched</option>
                        <option value="matched" @selected(request('status') === 'matched')>Matched</option>
                        <option value="ignored" @selected(request('status') === 'ignored')>Ignored</option>
                    </select>
                </div>
                <div class="col-md-3 form-group">
                    <label>থেকে</label>
                    <input type="date" name="from" value="{{ request('from') }}"
                        class="form-control form-control-sm">
                </div>
                <div class="col-md-3 form-group">
                    <label>পর্যন্ত</label>
                    <input type="date" name="to" value="{{ request('to') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-3 form-group">
                    <label>Transaction ID</label>
                    <input type="text" name="transaction_id" value="{{ request('transaction_id') }}"
                        class="form-control form-control-sm" placeholder="TrxID খুঁজুন">
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button class="btn btn-sm btn-info"><i class="fas fa-search"></i> ফিল্টার করুন</button>
                    <a href="{{ route('admin.accounting.mobile.show', $bankAccount) }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-times"></i> রিসেট
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- ========== Transactions Table ========== --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list"></i> লেনদেন তালিকা
                <span class="badge badge-secondary">{{ $statements->total() }}টি</span>
            </h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped mb-0">
                <thead class="thead-light">
                    <tr>
                        <th width="90">তারিখ</th>
                        <th>বিবরণ</th>
                        <th>TrxID</th>
                        <th class="text-right">জমা (৳)</th>
                        <th class="text-right">উত্তোলন (৳)</th>
                        <th class="text-right">ব্যালেন্স</th>
                        <th>স্ট্যাটাস</th>
                        <th>Matched</th>
                        <th width="130">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($statements as $s)
                        <tr
                            class="{{ $s->status === 'matched' ? 'table-success' : ($s->status === 'unmatched' ? 'table-warning' : '') }}">
                            <td>
                                <b>{{ $s->transaction_date->format('d M') }}</b>
                                <br><small class="text-muted">{{ $s->transaction_date->format('Y') }}</small>
                            </td>
                            <td>
                                {{ $s->description }}
                                @if ($s->reference)
                                    <br><small class="text-muted">Ref: {{ $s->reference }}</small>
                                @endif
                            </td>
                            <td>
                                @if ($s->transaction_id)
                                    <code class="text-primary">{{ $s->transaction_id }}</code>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-right text-success">
                                @if ($s->credit > 0)
                                    <b>{{ number_format($s->credit, 2) }}</b>
                                @endif
                            </td>
                            <td class="text-right text-danger">
                                @if ($s->debit > 0)
                                    <b>{{ number_format($s->debit, 2) }}</b>
                                @endif
                            </td>
                            <td class="text-right"><b>৳{{ number_format($s->balance, 2) }}</b></td>
                            <td>
                                @if ($s->status === 'matched')
                                    <span class="badge badge-success"><i class="fas fa-check"></i> Matched</span>
                                @elseif($s->status === 'ignored')
                                    <span class="badge badge-secondary"><i class="fas fa-ban"></i> Ignored</span>
                                @else
                                    <span class="badge badge-warning"><i class="fas fa-question"></i> Unmatched</span>
                                @endif
                            </td>
                            <td>
                                @if ($s->matchedPayment)
                                    <small>
                                        <i class="fas fa-user text-primary"></i>
                                        {{ $s->matchedPayment->student->name ?? '' }}<br>
                                        <code>{{ $s->matchedPayment->receipt_no }}</code>
                                    </small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if ($s->status === 'unmatched')
                                    <button class="btn btn-xs btn-success" onclick="openMatchModal({{ $s->id }})"
                                        title="Match">
                                        <i class="fas fa-link"></i> Match
                                    </button>
                                    <form action="{{ route('admin.accounting.mobile.ignore', $s) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        <button class="btn btn-xs btn-secondary" title="Ignore">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </form>
                                @elseif($s->status === 'matched')
                                    <form action="{{ route('admin.accounting.mobile.unmatch', $s) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        <button class="btn btn-xs btn-warning" title="Unmatch"
                                            onclick="return confirm('Match বাতিল করবেন?')">
                                            <i class="fas fa-unlink"></i> Unmatch
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p class="mb-0">কোনো লেনদেন নেই। CSV Import করুন অথবা ম্যানুয়ালি যোগ করুন।</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($statements->hasPages())
            <div class="card-footer">
                {{ $statements->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    {{-- ========== CSV Import Modal ========== --}}
    <div class="modal fade" id="importModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.accounting.mobile.import', $bankAccount) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header bg-info">
                        <h5 class="modal-title text-white"><i class="fas fa-file-upload"></i> CSV Import</h5>
                        <button class="close text-white" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <b>CSV ফরম্যাট:</b>
                            <br><code>Date, Description, Reference, TransactionID, Debit, Credit</code>
                            <hr class="my-2">
                            <b>উদাহরণ:</b>
                            <pre class="mb-0" style="font-size:11px;">2025-01-15,Fee Payment,INV-001,7AB3D9E1,0,5000
2025-01-16,Cash Out,CASH-01,8BC4E0F2,3000,0</pre>
                        </div>
                        <div class="form-group">
                            <label>CSV ফাইল নির্বাচন করুন *</label>
                            <input type="file" name="csv" class="form-control-file" accept=".csv,.txt" required>
                        </div>
                        <div class="callout callout-info small mb-0">
                            <i class="fas fa-lightbulb"></i>
                            বিকাশ/নগদ অ্যাপ থেকে "Statement Download" করে CSV ফাইল পাবেন, তবে কলাম মিলিয়ে নিন।
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-info"><i class="fas fa-upload"></i> Import করুন</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ========== Manual Statement Modal ========== --}}
    <div class="modal fade" id="addStatementModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.accounting.mobile.statements.store', $bankAccount) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-{{ $color }}">
                        <h5 class="modal-title text-white"><i class="fas fa-plus"></i> নতুন লেনদেন</h5>
                        <button class="close text-white" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>তারিখ *</label>
                            <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}"
                                class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>বিবরণ *</label>
                            <input type="text" name="description" class="form-control" required
                                placeholder="যেমন: Student Fee Payment">
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Reference</label>
                                <input type="text" name="reference" class="form-control"
                                    placeholder="Invoice/Payment ref">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Transaction ID</label>
                                <input type="text" name="transaction_id" class="form-control"
                                    placeholder="bKash TrxID">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>জমা (Credit) ৳</label>
                                <input type="number" step="0.01" min="0" name="credit" value="0"
                                    class="form-control">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>উত্তোলন (Debit) ৳</label>
                                <input type="number" step="0.01" min="0" name="debit" value="0"
                                    class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-{{ $color }}"><i class="fas fa-save"></i> সংরক্ষণ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ========== Match Modal ========== --}}
    <div class="modal fade" id="matchModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white"><i class="fas fa-link"></i> Payment Match করুন</h5>
                    <button class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="matchBody">
                    <div class="text-center py-4">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p class="mt-2">লোড হচ্ছে...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function openMatchModal(statementId) {
            const modal = $('#matchModal');
            modal.modal('show');
            document.getElementById('matchBody').innerHTML = `
        <div class="text-center py-4">
            <i class="fas fa-spinner fa-spin fa-2x"></i>
            <p class="mt-2">লোড হচ্ছে...</p>
        </div>
    `;

            fetch(`{{ url('admin/accounting/mobile-banking/statements') }}/${statementId}/suggest`)
                .then(r => {
                    if (!r.ok) throw new Error('Network error');
                    return r.json();
                })
                .then(data => {
                    if (data.length === 0) {
                        document.getElementById('matchBody').innerHTML = `
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-exclamation-triangle"></i>
                        <b>কোনো matching payment পাওয়া যায়নি।</b><br>
                        <small>একই তারিখের (±৩ দিন) একই amount এর কোনো fee payment নেই।</small>
                    </div>
                `;
                        return;
                    }

                    let html = `
                <p class="text-muted">নিচের payment গুলো এই লেনদেনের সাথে মিলতে পারে:</p>
                <table class="table table-sm table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Receipt</th>
                            <th>শিক্ষার্থী</th>
                            <th>শ্রেণি</th>
                            <th class="text-right">পরিমাণ</th>
                            <th>তারিখ</th>
                            <th>TrxID</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
            `;

                    data.forEach(p => {
                        html += `
                    <tr>
                        <td><b>${p.receipt_no}</b></td>
                        <td>${p.student?.name ?? '-'}<br><small>${p.student?.student_id ?? ''}</small></td>
                        <td>${p.student?.school_class ?? '-'}</td>
                        <td class="text-right"><b>৳${parseFloat(p.amount).toFixed(2)}</b></td>
                        <td>${p.payment_date}</td>
                        <td><code>${p.transaction_id ?? '-'}</code></td>
                        <td>
                            <form action="{{ url('admin/accounting/mobile-banking/statements') }}/${statementId}/match" method="POST" style="display:inline">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="payment_id" value="${p.id}">
                                <button class="btn btn-xs btn-success">
                                    <i class="fas fa-link"></i> Match
                                </button>
                            </form>
                        </td>
                    </tr>
                `;
                    });

                    html += '</tbody></table>';
                    document.getElementById('matchBody').innerHTML = html;
                })
                .catch(err => {
                    document.getElementById('matchBody').innerHTML = `
                <div class="alert alert-danger mb-0">
                    <i class="fas fa-times-circle"></i> Error: ${err.message}
                </div>
            `;
                });
        }
    </script>
@endpush
