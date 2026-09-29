@extends('admin.app')
@section('page-title', 'ব্যাংক অ্যাকাউন্ট ও Reconciliation')

@section('page-actions')
    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addBankModal">
        <i class="fas fa-plus"></i> নতুন ব্যাংক অ্যাকাউন্ট
    </button>
@endsection

@section('content')
<div class="row">
    @foreach($bankAccounts as $ba)
    <div class="col-md-4">
        <div class="card card-{{ $ba->type === 'bank' ? 'primary' : 'info' }} card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-{{ $ba->type === 'bank' ? 'university' : 'mobile-alt' }}"></i>
                    <b>{{ $ba->name }}</b>
                </h3>
                <div class="card-tools">
                    <span class="badge badge-{{ \App\Models\BankAccount::typeLabels()[$ba->type] ? 'info' : 'secondary' }}">
                        {{ \App\Models\BankAccount::typeLabels()[$ba->type] }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <p class="mb-1"><b>Account No:</b> {{ $ba->account_no }}</p>
                @if($ba->bank_name)<p class="mb-1"><b>Bank:</b> {{ $ba->bank_name }} @if($ba->branch_name) ({{ $ba->branch_name }}) @endif</p>@endif
                @if($ba->account_holder)<p class="mb-1"><b>Holder:</b> {{ $ba->account_holder }}</p>@endif
                <hr>
                <div class="row text-center">
                    <div class="col-6">
                        <small class="text-muted">Opening</small>
                        <h5>৳{{ number_format($ba->opening_balance, 2) }}</h5>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Current</small>
                        <h5 class="text-{{ $ba->current_balance >= 0 ? 'success' : 'danger' }}">
                            ৳{{ number_format($ba->current_balance, 2) }}
                        </h5>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.accounting.banks.show', $ba) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-list"></i> Statement দেখুন
                </a>
                <form action="{{ route('admin.accounting.banks.destroy', $ba) }}" method="POST" class="d-inline float-right" onsubmit="return confirm('নিশ্চিত?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Add Modal --}}
<div class="modal fade" id="addBankModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.accounting.banks.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">নতুন ব্যাংক / মোবাইল ব্যাংকিং অ্যাকাউন্ট</h5>
                    <button class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group"><label>নাম *</label><input type="text" name="name" class="form-control" required placeholder="Sonali Bank - Main"></div>
                        <div class="col-md-6 form-group"><label>Account No *</label><input type="text" name="account_no" class="form-control" required></div>
                        <div class="col-md-6 form-group">
                            <label>ধরন *</label>
                            <select name="type" class="form-control" required>
                                @foreach(\App\Models\BankAccount::typeLabels() as $k=>$v)
                                    <option value="{{ $k }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 form-group"><label>Opening Balance *</label><input type="number" step="0.01" name="opening_balance" value="0" class="form-control" required></div>
                        <div class="col-md-6 form-group"><label>Bank Name</label><input type="text" name="bank_name" class="form-control"></div>
                        <div class="col-md-6 form-group"><label>Branch</label><input type="text" name="branch_name" class="form-control"></div>
                        <div class="col-md-6 form-group"><label>Account Holder</label><input type="text" name="account_holder" class="form-control"></div>
                        <div class="col-md-6 form-group">
                            <label>Chart of Accounts লিংক</label>
                            <select name="account_id" class="form-control select2">
                                <option value="">-- লিংক করবেন না --</option>
                                @foreach(\App\Models\Account::whereIn('sub_type', ['bank','cash'])->get() as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                @endforeach
                            </select>
                        </div>
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