@extends('admin.app')
@section('page-title', 'মোবাইল ব্যাংকিং')

@section('page-actions')
    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addMobileAccountModal">
        <i class="fas fa-plus"></i> নতুন মোবাইল অ্যাকাউন্ট
    </button>
    <a href="{{ route('admin.accounting.banks') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-university"></i> ব্যাংক অ্যাকাউন্ট
    </a>
@endsection

@section('content')

{{-- ========== Top Stats Cards ========== --}}
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $typeStats['bkash']['count'] }}</h3>
                <p>বিকাশ অ্যাকাউন্ট</p>
            </div>
            <div class="icon"><i class="fas fa-wallet"></i></div>
            <div class="small-box-footer">
                Balance: ৳{{ number_format($typeStats['bkash']['balance'], 2) }}
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $typeStats['nagad']['count'] }}</h3>
                <p>নগদ অ্যাকাউন্ট</p>
            </div>
            <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
            <div class="small-box-footer">
                Balance: ৳{{ number_format($typeStats['nagad']['balance'], 2) }}
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-purple" style="background-color: #6f42c1 !important;">
            <div class="inner">
                <h3>{{ $typeStats['rocket']['count'] }}</h3>
                <p>রকেট অ্যাকাউন্ট</p>
            </div>
            <div class="icon"><i class="fas fa-rocket"></i></div>
            <div class="small-box-footer">
                Balance: ৳{{ number_format($typeStats['rocket']['balance'], 2) }}
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $typeStats['upay']['count'] }}</h3>
                <p>উপায় অ্যাকাউন্ট</p>
            </div>
            <div class="icon"><i class="fas fa-mobile-alt"></i></div>
            <div class="small-box-footer">
                Balance: ৳{{ number_format($typeStats['upay']['balance'], 2) }}
            </div>
        </div>
    </div>
</div>

{{-- ========== Mobile Account Cards ========== --}}
<div class="row">
    @forelse($mobileAccounts as $acc)
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
            $color = $colorMap[$acc->type] ?? 'secondary';
            $icon = $iconMap[$acc->type] ?? 'fa-mobile-alt';
            $accSummary = $summary[$acc->id] ?? null;
        @endphp

        <div class="col-md-6 col-lg-4">
            <div class="card card-{{ $color }} card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas {{ $icon }}"></i>
                        <b>{{ $acc->name }}</b>
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-{{ $color }}">
                            {{ \App\Models\BankAccount::typeLabels()[$acc->type] }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <small class="text-muted">বর্তমান ব্যালেন্স</small>
                        <h2 class="text-{{ $acc->current_balance >= 0 ? 'success' : 'danger' }} mb-0">
                            ৳{{ number_format($acc->current_balance, 2) }}
                        </h2>
                    </div>

                    <div class="row text-center border-top pt-3">
                        <div class="col-6 border-right">
                            <small class="text-success"><i class="fas fa-arrow-down"></i> আজ জমা</small>
                            <h6 class="text-success mb-0">৳{{ number_format($accSummary['today_in'] ?? 0, 2) }}</h6>
                        </div>
                        <div class="col-6">
                            <small class="text-danger"><i class="fas fa-arrow-up"></i> আজ উত্তোলন</small>
                            <h6 class="text-danger mb-0">৳{{ number_format($accSummary['today_out'] ?? 0, 2) }}</h6>
                        </div>
                    </div>

                    <div class="row text-center mt-3">
                        <div class="col-6">
                            <span class="badge badge-warning">
                                {{ $accSummary['unmatched'] ?? 0 }} Unmatched
                            </span>
                        </div>
                        <div class="col-6">
                            <span class="badge badge-success">
                                {{ $accSummary['matched'] ?? 0 }} Matched
                            </span>
                        </div>
                    </div>

                    <hr>
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted">Account No:</td>
                            <td class="text-right"><b>{{ $acc->account_no }}</b></td>
                        </tr>
                        @if($acc->account_holder)
                        <tr>
                            <td class="text-muted">Holder:</td>
                            <td class="text-right">{{ $acc->account_holder }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="text-muted">Opening:</td>
                            <td class="text-right">৳{{ number_format($acc->opening_balance, 2) }}</td>
                        </tr>
                    </table>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.accounting.mobile.show', $acc) }}" class="btn btn-{{ $color }} btn-sm btn-block">
                        <i class="fas fa-list"></i> লেনদেন দেখুন
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info text-center py-4">
                <i class="fas fa-info-circle fa-2x mb-2"></i>
                <h5>এখনো কোনো মোবাইল ব্যাংকিং অ্যাকাউন্ট যোগ করা হয়নি</h5>
                <p class="mb-3">বিকাশ, নগদ, রকেট বা উপায় অ্যাকাউন্ট যোগ করে শুরু করুন।</p>
                <button class="btn btn-primary" data-toggle="modal" data-target="#addMobileAccountModal">
                    <i class="fas fa-plus"></i> প্রথম অ্যাকাউন্ট যোগ করুন
                </button>
            </div>
        </div>
    @endforelse
</div>

{{-- ========== Recent Transactions ========== --}}
@if($recentStatements->count() > 0)
<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-history"></i> সাম্প্রতিক মোবাইল লেনদেন (সর্বশেষ ২০টি)
        </h3>
        <div class="card-tools">
            <a href="{{ route('admin.accounting.mobile.index') }}" class="btn btn-xs btn-light">
                <i class="fas fa-sync"></i> রিফ্রেশ
            </a>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover table-sm mb-0">
            <thead class="thead-light">
                <tr>
                    <th>তারিখ</th>
                    <th>অ্যাকাউন্ট</th>
                    <th>বিবরণ</th>
                    <th>TrxID</th>
                    <th class="text-right">জমা</th>
                    <th class="text-right">উত্তোলন</th>
                    <th>স্ট্যাটাস</th>
                    <th>সংশ্লিষ্ট শিক্ষার্থী</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentStatements as $s)
                <tr>
                    <td>{{ $s->transaction_date->format('d M') }}</td>
                    <td>
                        <span class="badge badge-{{ 
                            $s->bankAccount->type === 'bkash' ? 'danger' :
                            ($s->bankAccount->type === 'nagad' ? 'warning' :
                            ($s->bankAccount->type === 'rocket' ? 'primary' : 'info'))
                        }}">
                            {{ $s->bankAccount->name }}
                        </span>
                    </td>
                    <td>{{ Str::limit($s->description, 30) }}</td>
                    <td><code>{{ $s->transaction_id ?? '-' }}</code></td>
                    <td class="text-right text-success">
                        {{ $s->credit > 0 ? '৳' . number_format($s->credit, 2) : '-' }}
                    </td>
                    <td class="text-right text-danger">
                        {{ $s->debit > 0 ? '৳' . number_format($s->debit, 2) : '-' }}
                    </td>
                    <td>
                        @if($s->status === 'matched') <span class="badge badge-success">Matched</span>
                        @elseif($s->status === 'ignored') <span class="badge badge-secondary">Ignored</span>
                        @else <span class="badge badge-warning">Unmatched</span> @endif
                    </td>
                    <td>
                        @if($s->matchedPayment)
                            <small>
                                <i class="fas fa-user"></i> {{ $s->matchedPayment->student->name ?? '' }}<br>
                                <code>{{ $s->matchedPayment->receipt_no }}</code>
                            </small>
                        @else
                            <small class="text-muted">—</small>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- ========== Add Mobile Account Modal ========== --}}
<div class="modal fade" id="addMobileAccountModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.accounting.banks.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white"><i class="fas fa-mobile-alt"></i> নতুন মোবাইল ব্যাংকিং অ্যাকাউন্ট</h5>
                    <button class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>নাম *</label>
                            <input type="text" name="name" class="form-control" required placeholder="যেমন: বিকাশ - অফিস">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>মোবাইল নম্বর (Account No) *</label>
                            <input type="text" name="account_no" class="form-control" required placeholder="01712345678">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>ধরন *</label>
                            <select name="type" class="form-control" required>
                                <option value="bkash">বিকাশ</option>
                                <option value="nagad">নগদ</option>
                                <option value="rocket">রকেট</option>
                                <option value="upay">উপায়</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Opening Balance (৳) *</label>
                            <input type="number" step="0.01" name="opening_balance" value="0" class="form-control" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Account Holder</label>
                            <input type="text" name="account_holder" class="form-control" placeholder="প্রতিষ্ঠানের নাম বা ব্যক্তির নাম">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Chart of Accounts লিংক</label>
                            <select name="account_id" class="form-control select2">
                                <option value="">-- লিংক করবেন না --</option>
                                @foreach(\App\Models\Account::whereIn('sub_type', ['bank', 'cash'])->get() as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="alert alert-info small mb-0">
                        <i class="fas fa-info-circle"></i>
                        অ্যাকাউন্ট যোগ করার পর CSV ফাইল আপলোড করে অথবা ম্যানুয়ালি লেনদেন যোগ করতে পারবেন।
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary"><i class="fas fa-save"></i> সংরক্ষণ</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection