@extends('admin.app')
@section('page-title', 'SMS পাঠান')

@section('content')
<form action="{{ route('admin.sms.send') }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-7">
            <div class="card card-primary">
                <div class="card-header"><h3 class="card-title">প্রাপক নির্বাচন</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>প্রাপকের ধরন *</label>
                        <select name="recipient_type" id="recipient_type" class="form-control" required>
                            <option value="all">সব শিক্ষার্থী</option>
                            <option value="class">শ্রেণি অনুযায়ী</option>
                            <option value="section">শাখা অনুযায়ী</option>
                            <option value="individual">একক শিক্ষার্থী</option>
                            <option value="custom">কাস্টম নম্বর</option>
                        </select>
                    </div>

                    <div class="form-group rec-class d-none">
                        <label>শ্রেণি</label>
                        <select name="class_id" class="form-control select2">
                            <option value="">নির্বাচন</option>
                            @foreach($classes as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="form-group rec-section d-none">
                        <label>শাখা</label>
                        <select name="section_id" id="section_id" class="form-control select2">
                            <option value="">নির্বাচন</option>
                        </select>
                    </div>
                    <div class="form-group rec-individual d-none">
                        <label>শিক্ষার্থী</label>
                        <select name="student_id" id="student_id" class="form-control select2">
                            <option value="">নির্বাচন</option>
                        </select>
                    </div>
                    <div class="form-group rec-custom d-none">
                        <label>কাস্টম নম্বর (একাধিক হলে কমা বা নতুন লাইনে)</label>
                        <textarea name="custom_phone" class="form-control" rows="3" placeholder="01711111111, 01722222222"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card card-success">
                <div class="card-header"><h3 class="card-title">বার্তা</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>বার্তা *</label>
                        <textarea name="message" id="message" class="form-control" rows="6" required maxlength="1000" placeholder="প্রিয় অভিভাবক, ..."></textarea>
                        <small class="text-muted"><span id="char-count">0</span>/1000 অক্ষর — প্রতিটি SMS এ ১৬০ অক্ষর</small>
                    </div>
                    <div class="callout callout-info small">
                        <b>টিপস:</b> {student_name} দিলে প্রতিটি শিক্ষার্থীর নাম বসে যাবে।
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-success btn-block"><i class="fas fa-paper-plane"></i> SMS পাঠান</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
const typeSelect = document.getElementById('recipient_type');
function toggleFields() {
    ['rec-class','rec-section','rec-individual','rec-custom'].forEach(c =>
        document.querySelector('.' + c).classList.add('d-none'));
    if (typeSelect.value === 'class') document.querySelector('.rec-class').classList.remove('d-none');
    if (typeSelect.value === 'section') {
        document.querySelector('.rec-class').classList.remove('d-none');
        document.querySelector('.rec-section').classList.remove('d-none');
    }
    if (typeSelect.value === 'individual') {
        document.querySelector('.rec-class').classList.remove('d-none');
        document.querySelector('.rec-section').classList.remove('d-none');
        document.querySelector('.rec-individual').classList.remove('d-none');
    }
    if (typeSelect.value === 'custom') document.querySelector('.rec-custom').classList.remove('d-none');
}
typeSelect.addEventListener('change', toggleFields);
toggleFields();

document.getElementById('message').addEventListener('input', function() {
    document.getElementById('char-count').innerText = this.value.length;
});

// ক্লাস পরিবর্তন হলে সেকশন লোড
document.querySelector('select[name="class_id"]').addEventListener('change', function() {
    const cid = this.value;
    if (!cid) return;
    fetch('{{ url("admin/api/sections") }}?class_id=' + cid)
        .then(r => r.json())
        .then(data => {
            const sel = document.getElementById('section_id');
            sel.innerHTML = '<option value="">নির্বাচন</option>';
            data.forEach(s => sel.innerHTML += `<option value="${s.id}">${s.name}</option>`);
            const sst = document.getElementById('student_id');
            sst.innerHTML = '<option value="">নির্বাচন</option>';
        });
});
</script>
@endpush
