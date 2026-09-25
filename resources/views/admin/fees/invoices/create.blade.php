@extends('admin.app')
@section('page-title', 'নতুন ইনভয়েস')

@section('content')
<div class="card card-primary">
    <form method="GET" class="card-body">
        <div class="row">
            <div class="col-md-3 form-group">
                <label>শ্রেণি *</label>
                <select name="class_id" class="form-control select2" required onchange="this.form.submit()">
                    <option value="">নির্বাচন</option>
                    @foreach($classes as $c)<option value="{{ $c->id }}" @selected($classId==$c->id)>{{ $c->name }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label>শাখা</label>
                <select name="section_id" class="form-control select2">
                    <option value="">সব</option>
                    @foreach($sections as $s)<option value="{{ $s->id }}" @selected($sectionId==$s->id)>{{ $s->name }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-2 form-group d-flex align-items-end">
                <button class="btn btn-primary btn-block"><i class="fas fa-search"></i> দেখান</button>
            </div>
        </div>
    </form>
</div>

@if($students->count())
<form action="{{ route('admin.fees.invoices.store') }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-8">
            <div class="card card-success">
                <div class="card-header"><h3 class="card-title">ফি আইটেম</h3></div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered">
                        <thead><tr><th>ক্যাটাগরি</th><th>পরিমাণ (৳)</th></tr></thead>
                        <tbody>
                            @foreach($feeStructures as $fs)
                            <tr>
                                <td>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input cat-check"
                                               id="cat_{{ $fs->fee_category_id }}" value="{{ $fs->fee_category_id }}">
                                        <label class="custom-control-label" for="cat_{{ $fs->fee_category_id }}">
                                            <b>{{ $fs->category->name }}</b>
                                        </label>
                                    </div>
                                </td>
                                <td width="200">
                                    <input type="number" step="0.01"
                                           name="amounts[{{ $fs->fee_category_id }}]"
                                           id="amt_{{ $fs->fee_category_id }}"
                                           value="{{ $fs->amount }}"
                                           class="form-control amount-input" readonly>
                                    <input type="hidden" name="category_ids[]"
                                           id="hid_{{ $fs->fee_category_id }}" value="{{ $fs->fee_category_id }}" disabled>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-light">
                                <th>সাব-টোটাল</th>
                                <th class="text-right"><span id="subtotal">0.00</span></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-info">
                <div class="card-header"><h3 class="card-title">বিস্তারিত</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>শিক্ষার্থী *</label>
                        <select name="student_id" class="form-control select2" required>
                            <option value="">নির্বাচন</option>
                            @foreach($students as $s)
                                <option value="{{ $s->id }}" @selected(old('student_id')==$s->id)>
                                    {{ $s->roll_number }} - {{ $s->name }} ({{ $s->student_id }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group"><label>মাস</label>
                            <select name="month" class="form-control">
                                <option value="">--</option>
                                @for($m=1;$m<=12;$m++)
                                    <option value="{{ str_pad($m,2,'0',STR_PAD_LEFT) }}" @selected(old('month')==str_pad($m,2,'0',STR_PAD_LEFT))>{{ str_pad($m,2,'0',STR_PAD_LEFT) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-6 form-group"><label>বছর</label>
                            <input type="number" name="year" value="{{ old('year', date('Y')) }}" class="form-control">
                        </div>
                    </div>
                    <div class="form-group"><label>ইনভয়েস তারিখ *</label><input type="date" name="invoice_date" value="{{ old('invoice_date', date('Y-m-d')) }}" class="form-control" required></div>
                    <div class="form-group"><label>পরিশোধের শেষ তারিখ</label><input type="date" name="due_date" value="{{ old('due_date') }}" class="form-control"></div>
                    <div class="row">
                        <div class="col-6 form-group"><label>ডিসকাউন্ট</label><input type="number" step="0.01" name="discount" value="0" class="form-control discount-fine"></div>
                        <div class="col-6 form-group"><label>জরিমানা</label><input type="number" step="0.01" name="fine" value="0" class="form-control discount-fine"></div>
                    </div>
                    <div class="form-group"><label>মন্তব্য</label><textarea name="remarks" class="form-control" rows="2"></textarea></div>
                    <div class="callout callout-success">
                        <h5>মোট: ৳ <span id="grand-total">0.00</span></h5>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-success btn-block"><i class="fas fa-save"></i> ইনভয়েস তৈরি</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endif
@endsection

@push('scripts')
<script>
function recalc() {
    let sub = 0;
    document.querySelectorAll('.cat-check').forEach(c => {
        const id = c.value;
        const amt = document.getElementById('amt_'+id);
        const hid = document.getElementById('hid_'+id);
        if (c.checked) {
            amt.removeAttribute('readonly');
            hid.disabled = false;
            sub += parseFloat(amt.value || 0);
        } else {
            amt.setAttribute('readonly', true);
            hid.disabled = true;
        }
    });
    document.getElementById('subtotal').innerText = sub.toFixed(2);
    const d = parseFloat(document.querySelector('input[name=discount]').value || 0);
    const f = parseFloat(document.querySelector('input[name=fine]').value || 0);
    document.getElementById('grand-total').innerText = (sub - d + f).toFixed(2);
}
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('cat-check') || e.target.classList.contains('discount-fine') || e.target.classList.contains('amount-input')) {
        recalc();
    }
});
document.querySelectorAll('.amount-input').forEach(i => i.addEventListener('input', recalc));
</script>
@endpush
