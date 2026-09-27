@extends('admin.app')
@section('page-title', 'পে-রোল ব্যবস্থাপনা')

@section('content')
<div class="row">
    <div class="col-md-5">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">একক বেতন প্রদান</h3></div>
            <form action="{{ route('admin.payroll.store') }}" method="POST" class="card-body">
                @csrf
                <div class="form-group">
                    <label>শিক্ষক *</label>
                    <select name="teacher_id" id="teacher_id" class="form-control select2" required>
                        <option value="">নির্বাচন</option>
                        @foreach($teachers as $t)<option value="{{ $t->id }}" data-salary="{{ $t->basic_salary ?? 0 }}">{{ $t->name }} ({{ $t->teacher_id }})</option>@endforeach
                    </select>
                </div>
                <div class="row">
                    <div class="col-6 form-group">
                        <label>মাস *</label>
                        <select name="month" class="form-control" required>
                            @foreach($months as $k=>$v)<option value="{{ str_pad($k,2,'0',STR_PAD_LEFT) }}">{{ $v }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-6 form-group"><label>বছর *</label><input type="number" name="year" value="{{ date('Y') }}" class="form-control" required></div>
                </div>
                <div class="form-group"><label>বেসিক বেতন *</label><input type="number" step="0.01" name="basic_salary" id="basic_salary" class="form-control" required></div>
                <div class="row">
                    <div class="col-6 form-group"><label>ভাতা</label><input type="number" step="0.01" name="allowance" value="0" class="form-control"></div>
                    <div class="col-6 form-group"><label>কর্তন</label><input type="number" step="0.01" name="deduction" value="0" class="form-control"></div>
                </div>
                <div class="form-group"><label>পেমেন্ট তারিখ *</label><input type="date" name="payment_date" value="{{ date('Y-m-d') }}" class="form-control" required></div>
                <div class="form-group">
                    <label>পেমেন্ট মেথড</label>
                    <select name="payment_method" class="form-control">
                        <option value="cash">ক্যাশ</option>
                        <option value="bank">ব্যাংক</option>
                        <option value="bkash">বিকাশ</option>
                    </select>
                </div>
                <div class="form-group"><label>মন্তব্য</label><textarea name="remarks" class="form-control" rows="2"></textarea></div>
                <button class="btn btn-primary btn-block"><i class="fas fa-money-bill"></i> পরিশোধ</button>
            </form>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card card-info">
            <div class="card-header"><h3 class="card-title">বাল্ক বেতন (সব শিক্ষক)</h3></div>
            <form action="{{ route('admin.payroll.bulk') }}" method="POST" class="card-body">
                @csrf
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>মাস *</label>
                        <select name="month" class="form-control" required>
                            @foreach($months as $k=>$v)<option value="{{ str_pad($k,2,'0',STR_PAD_LEFT) }}">{{ $v }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-md-4 form-group"><label>বছর *</label><input type="number" name="year" value="{{ date('Y') }}" class="form-control" required></div>
                    <div class="col-md-4 form-group"><label>পেমেন্ট তারিখ *</label><input type="date" name="payment_date" value="{{ date('Y-m-d') }}" class="form-control" required></div>
                </div>
                <div class="form-group">
                    <label>পেমেন্ট মেথড</label>
                    <select name="payment_method" class="form-control">
                        <option value="cash">ক্যাশ</option>
                        <option value="bank">ব্যাংক</option>
                    </select>
                </div>
                <button class="btn btn-info btn-block" onclick="return confirm('সব শিক্ষকের বেতন তৈরি হবে?')"><i class="fas fa-copy"></i> সব শিক্ষকের বেতন তৈরি</button>
            </form>
        </div>

        <div class="card">
            <div class="card-header"><h3 class="card-title">বেতন পরিশোধের তালিকা</h3></div>
            <div class="card-body table-responsive">
                <table class="table table-hover datatable">
                    <thead><tr><th>ভাউচার</th><th>শিক্ষক</th><th>মাস/বছর</th><th>নেট</th><th>তারিখ</th><th></th></tr></thead>
                    <tbody>
                        @foreach($payments as $p)
                        <tr>
                            <td><b>{{ $p->voucher_no }}</b></td>
                            <td>{{ $p->teacher->name ?? '-' }}</td>
                            <td>{{ str_pad($p->month,2,'0',STR_PAD_LEFT) }}/{{ $p->year }}</td>
                            <td>৳{{ number_format($p->net_salary, 2) }}</td>
                            <td>{{ $p->payment_date->format('d M, Y') }}</td>
                            <td>
                                <form action="{{ route('admin.payroll.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('নিশ্চিত?')">
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

@push('scripts')
<script>
document.getElementById('teacher_id').addEventListener('change', function() {
    const salary = this.options[this.selectedIndex].dataset.salary || 0;
    document.getElementById('basic_salary').value = salary;
});
</script>
@endpush
