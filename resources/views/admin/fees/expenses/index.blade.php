@extends('admin.app')
@section('page-title', 'খরচ ব্যবস্থাপনা')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">নতুন খরচ</h3></div>
            <form action="{{ route('admin.fees.expenses.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group"><label>শিরোনাম *</label><input type="text" name="title" class="form-control" required></div>
                    <div class="form-group">
                        <label>ক্যাটাগরি</label>
                        <input type="text" name="category" class="form-control" list="cat-list" placeholder="Salary, Utility, Rent">
                        <datalist id="cat-list">
                            <option value="Salary">
                            <option value="Utility">
                            <option value="Rent">
                            <option value="Maintenance">
                            <option value="Stationery">
                            <option value="Event">
                        </datalist>
                    </div>
                    <div class="form-group"><label>পরিমাণ (৳) *</label><input type="number" step="0.01" name="amount" class="form-control" required></div>
                    <div class="form-group"><label>তারিখ *</label><input type="date" name="expense_date" value="{{ date('Y-m-d') }}" class="form-control" required></div>
                    <div class="form-group">
                        <label>পেমেন্ট মেথড</label>
                        <select name="payment_method" class="form-control">
                            <option value="cash">ক্যাশ</option>
                            <option value="bank">ব্যাংক</option>
                            <option value="bkash">বিকাশ</option>
                            <option value="cheque">চেক</option>
                        </select>
                    </div>
                    <div class="form-group"><label>রেফারেন্স</label><input type="text" name="reference" class="form-control"></div>
                    <div class="form-group"><label>মন্তব্য</label><textarea name="note" class="form-control" rows="2"></textarea></div>
                </div>
                <div class="card-footer"><button class="btn btn-primary"><i class="fas fa-plus"></i> যোগ করুন</button></div>
            </form>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">ফিল্টার</h3>
            </div>
            <form method="GET" class="card-body form-inline">
                <input type="date" name="from" value="{{ request('from') }}" class="form-control form-control-sm mr-2">
                <input type="date" name="to" value="{{ request('to') }}" class="form-control form-control-sm mr-2">
                <select name="category" class="form-control form-control-sm mr-2">
                    <option value="">সব ক্যাটাগরি</option>
                    @foreach($categories as $cat)<option value="{{ $cat }}" @selected(request('category')==$cat)>{{ $cat }}</option>@endforeach
                </select>
                <button class="btn btn-sm btn-info">ফিল্টার</button>
                <a href="{{ route('admin.fees.expenses.index') }}" class="btn btn-sm btn-secondary ml-1">রিসেট</a>
            </form>
        </div>

        <div class="callout callout-danger">
            <h5><i class="fas fa-wallet"></i> মোট খরচ: ৳ {{ number_format($total, 2) }}</h5>
        </div>

        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-hover datatable">
                    <thead><tr><th>শিরোনাম</th><th>ক্যাটাগরি</th><th>তারিখ</th><th class="text-right">পরিমাণ</th><th>মেথড</th><th></th></tr></thead>
                    <tbody>
                        @foreach($expenses as $e)
                        <tr>
                            <td><b>{{ $e->title }}</b>@if($e->reference)<br><small class="text-muted">Ref: {{ $e->reference }}</small>@endif</td>
                            <td>{{ $e->category ?? '-' }}</td>
                            <td>{{ $e->expense_date->format('d M, Y') }}</td>
                            <td class="text-right"><b>৳ {{ number_format($e->amount, 2) }}</b></td>
                            <td>{{ $e->payment_method ?? '-' }}</td>
                            <td>
                                <form action="{{ route('admin.fees.expenses.destroy', $e) }}" method="POST" onsubmit="return confirm('নিশ্চিত?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
