@extends('admin.app')
@section('page-title', 'বার্ষিক বাজেট')

@section('page-actions')
    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addBudgetModal">
        <i class="fas fa-plus"></i> নতুন বাজেট
    </button>
@endsection

@section('content')
<div class="card card-primary">
    <form method="GET" class="card-body form-inline">
        <label class="mr-1">বছর:</label>
        <input type="number" name="year" value="{{ $year }}" class="form-control form-control-sm mr-2" style="width:100px">
        <button class="btn btn-sm btn-info"><i class="fas fa-search"></i> দেখান</button>
    </form>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>৳{{ number_format($summary['income_budget'], 0) }}</h3>
                <p>আয় বাজেট</p>
            </div>
            <div class="icon"><i class="fas fa-arrow-up"></i></div>
            <div class="small-box-footer">
                প্রকৃত: ৳{{ number_format($summary['income_actual'], 0) }}
                @if($summary['income_budget'] > 0)
                    ({{ round(($summary['income_actual']/$summary['income_budget'])*100, 1) }}%)
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>৳{{ number_format($summary['expense_budget'], 0) }}</h3>
                <p>ব্যয় বাজেট</p>
            </div>
            <div class="icon"><i class="fas fa-arrow-down"></i></div>
            <div class="small-box-footer">
                প্রকৃত: ৳{{ number_format($summary['expense_actual'], 0) }}
                @if($summary['expense_budget'] > 0)
                    ({{ round(($summary['expense_actual']/$summary['expense_budget'])*100, 1) }}%)
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>৳{{ number_format($summary['income_budget'] - $summary['expense_budget'], 0) }}</h3>
                <p>প্রত্যাশিত সঞ্চয়</p>
            </div>
            <div class="icon"><i class="fas fa-piggy-bank"></i></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>৳{{ number_format($summary['income_actual'] - $summary['expense_actual'], 0) }}</h3>
                <p>প্রকৃত সঞ্চয়</p>
            </div>
            <div class="icon"><i class="fas fa-balance-scale"></i></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-chart-pie"></i> বাজেট vs প্রকৃত ({{ $year }})</h3>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-hover datatable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>বাজেট</th>
                    <th>ধরন</th>
                    <th>অ্যাকাউন্ট</th>
                    <th class="text-right">বাজেট (৳)</th>
                    <th class="text-right">প্রকৃত (৳)</th>
                    <th class="text-right">ভ্যারিয়েন্স</th>
                    <th>ব্যবহার (%)</th>
                    <th>অ্যাকশন</th>
                </tr>
            </thead>
            <tbody>
                @foreach($budgets as $b)
                @php
                    $util = $b->utilization_percent;
                    $color = $util > 100 ? 'danger' : ($util > 80 ? 'warning' : 'success');
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td><b>{{ $b->name }}</b></td>
                    <td>
                        @if($b->type === 'income') <span class="badge badge-success">আয়</span>
                        @else <span class="badge badge-danger">ব্যয়</span> @endif
                    </td>
                    <td>{{ $b->account->name ?? '-' }}</td>
                    <td class="text-right">৳{{ number_format($b->budgeted_amount, 2) }}</td>
                    <td class="text-right"><b>৳{{ number_format($b->actual_amount, 2) }}</b></td>
                    <td class="text-right text-{{ $b->variance >= 0 ? 'success' : 'danger' }}">
                        ৳{{ number_format($b->variance, 2) }}
                    </td>
                    <td width="180">
                        <div class="progress" style="height:18px;">
                            <div class="progress-bar bg-{{ $color }}" style="width: {{ min($util, 100) }}%">
                                {{ $util }}%
                            </div>
                        </div>
                    </td>
                    <td>
                        <button class="btn btn-xs btn-warning" data-toggle="modal" data-target="#editBudget{{ $b->id }}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('admin.accounting.budgets.destroy', $b) }}" method="POST" class="d-inline" onsubmit="return confirm('নিশ্চিত?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>

                {{-- Edit Modal --}}
                <div class="modal fade" id="editBudget{{ $b->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('admin.accounting.budgets.update', $b) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="modal-header"><h5 class="modal-title">বাজেট এডিট</h5><button class="close" data-dismiss="modal">&times;</button></div>
                                <div class="modal-body">
                                    <div class="form-group"><label>নাম *</label><input type="text" name="name" value="{{ $b->name }}" class="form-control" required></div>
                                    <div class="form-group"><label>পরিমাণ (৳) *</label><input type="number" step="0.01" name="budgeted_amount" value="{{ $b->budgeted_amount }}" class="form-control" required></div>
                                    <div class="form-group"><label>নোট</label><textarea name="notes" class="form-control" rows="2">{{ $b->notes }}</textarea></div>
                                </div>
                                <div class="modal-footer"><button class="btn btn-primary"><i class="fas fa-save"></i> সংরক্ষণ</button></div>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach

                @if($budgets->isEmpty())
                    <tr><td colspan="9" class="text-center text-muted py-4">
                        <i class="fas fa-info-circle"></i> এই বছরের জন্য কোনো বাজেট তৈরি করা হয়নি।
                    </td></tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

{{-- Add Modal --}}
<div class="modal fade" id="addBudgetModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.accounting.budgets.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-chart-pie"></i> নতুন বাজেট</h5>
                    <button class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>বাজেটের নাম *</label>
                            <input type="text" name="name" class="form-control" required placeholder="যেমন: শিক্ষক বেতন বাজেট">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>বছর *</label>
                            <input type="number" name="year" value="{{ date('Y') }}" class="form-control" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>ধরন *</label>
                            <select name="type" class="form-control" required>
                                <option value="expense">ব্যয়</option>
                                <option value="income">আয়</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>শিক্ষাবর্ষ</label>
                            <select name="academic_year_id" class="form-control select2">
                                <option value="">-- নির্বাচন --</option>
                                @foreach($academicYears as $y)<option value="{{ $y->id }}" @selected($y->is_current)>{{ $y->name }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>অ্যাকাউন্ট (লিংক)</label>
                            <select name="account_id" class="form-control select2">
                                <option value="">-- লিংক করবেন না --</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">লিংক করলে প্রকৃত খরচ অটো আপডেট হবে।</small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>বাজেট পরিমাণ (৳) *</label>
                            <input type="number" step="0.01" min="0" name="budgeted_amount" class="form-control" required>
                        </div>
                        <div class="col-md-12 form-group">
                            <label>নোট</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
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