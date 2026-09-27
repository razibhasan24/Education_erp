@extends('admin.app')
@section('page-title', 'Chart of Accounts')

@section('page-actions')
    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addAccountModal">
        <i class="fas fa-plus"></i> নতুন অ্যাকাউন্ট
    </button>
    <form action="{{ route('admin.accounting.sync-old-records') }}" method="POST" class="d-inline" onsubmit="return confirm('পুরোনো লেনদেন থেকে Journal Entry তৈরি হবে। নিশ্চিত?')">
        @csrf
        <button class="btn btn-info btn-sm"><i class="fas fa-sync"></i> Sync Old Records</button>
    </form>
@endsection

@section('content')
@foreach(['asset', 'liability', 'equity', 'income', 'expense'] as $type)
    @if(isset($grouped[$type]))
        @php $label = \App\Models\Account::typeLabels()[$type]; @endphp
        <div class="card card-{{ $label['color'] }} card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-folder"></i>
                    <b>{{ $label['label'] }}</b> ({{ $grouped[$type]->count() }})
                    <small class="text-muted ml-2">Normal Balance: {{ strtoupper($label['normal']) }}</small>
                </h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-sm table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th width="100">Code</th>
                            <th>Name</th>
                            <th>বাংলা নাম</th>
                            <th>Sub Type</th>
                            <th class="text-right">Balance</th>
                            <th width="80"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($grouped[$type] as $acc)
                        <tr>
                            <td><b>{{ $acc->code }}</b></td>
                            <td>{{ $acc->name }} @if($acc->is_system)<i class="fas fa-lock text-muted ml-1" title="System"></i>@endif</td>
                            <td>{{ $acc->name_bn }}</td>
                            <td><span class="badge badge-secondary">{{ $acc->sub_type ?? '-' }}</span></td>
                            <td class="text-right">
                                <b>৳ {{ number_format($acc->current_balance, 2) }}</b>
                            </td>
                            <td>
                                @if(!$acc->is_system)
                                <form action="{{ route('admin.accounting.accounts.destroy', $acc) }}" method="POST" class="d-inline" onsubmit="return confirm('নিশ্চিত?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endforeach

{{-- Add Account Modal --}}
<div class="modal fade" id="addAccountModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.accounting.accounts.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">নতুন অ্যাকাউন্ট</h5>
                    <button class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 form-group"><label>Code *</label><input type="text" name="code" class="form-control" required></div>
                        <div class="col-md-4 form-group"><label>Name *</label><input type="text" name="name" class="form-control" required></div>
                        <div class="col-md-4 form-group"><label>নাম (বাংলা)</label><input type="text" name="name_bn" class="form-control"></div>
                        <div class="col-md-6 form-group">
                            <label>Type *</label>
                            <select name="type" class="form-control" required>
                                @foreach(\App\Models\Account::typeLabels() as $k=>$v)
                                    <option value="{{ $k }}">{{ $v['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Sub Type</label>
                            <select name="sub_type" class="form-control">
                                <option value="">--</option>
                                @foreach(['current_asset','fixed_asset','bank','cash','receivable','current_liability','long_term_liability','payable','capital','retained_earnings','direct_income','indirect_income','direct_expense','indirect_expense'] as $st)
                                    <option value="{{ $st }}">{{ $st }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Opening Balance</label>
                            <input type="number" step="0.01" name="opening_balance" value="0" class="form-control">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Opening Type</label>
                            <select name="opening_type" class="form-control">
                                <option value="debit">Debit</option>
                                <option value="credit">Credit</option>
                            </select>
                        </div>
                        <div class="col-md-12 form-group"><label>Description</label><textarea name="description" class="form-control" rows="2"></textarea></div>
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
