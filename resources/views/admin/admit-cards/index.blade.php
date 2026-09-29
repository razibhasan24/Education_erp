@extends('admin.app')
@section('page-title', 'প্রবেশপত্র জেনারেট')

@section('content')
<div class="row">
    <div class="col-md-5">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-ticket-alt"></i> প্রবেশপত্র তৈরি</h3></div>
            <form action="{{ route('admin.admit-cards.generate') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label>পরীক্ষা *</label>
                        <select name="exam_id" class="form-control select2" required>
                            <option value="">-- নির্বাচন --</option>
                            @foreach($exams as $e)
                                <option value="{{ $e->id }}">{{ $e->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>শ্রেণি *</label>
                        <select name="class_id" id="class_id" class="form-control select2" required>
                            <option value="">-- নির্বাচন --</option>
                            @foreach($classes as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>শাখা</label>
                        <select name="section_id" id="section_id" class="form-control select2">
                            <option value="">সব শাখা</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>শিক্ষার্থী</label>
                        <select name="student_id" id="student_id" class="form-control select2">
                            <option value="">সব শিক্ষার্থী</option>
                        </select>
                    </div>
                    <div class="callout callout-info small">
                        <i class="fas fa-info-circle"></i>
                        সব শিক্ষার্থীর প্রবেশপত্র একসাথে একটি PDF এ ডাউনলোড হবে।
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary btn-block btn-lg"><i class="fas fa-download"></i> প্রবেশপত্র ডাউনলোড</button>
                </div>
            </form>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card card-info">
            <div class="card-header"><h3 class="card-title">নির্দেশনা</h3></div>
            <div class="card-body">
                <ul>
                    <li>পরীক্ষার জন্য আগে Exam Routine তৈরি করা থাকতে হবে।</li>
                    <li>Admit Card এ পরীক্ষার সময়সূচি স্বয়ংক্রিয়ভাবে যুক্ত হবে।</li>
                    <li>নির্দিষ্ট শিক্ষার্থীর জন্য "শিক্ষার্থী" সিলেক্ট করুন, নাহলে সবাই একসাথে হবে।</li>
                    <li>Student Profile থেকেও একক Admit Card ডাউনলোড করা যাবে।</li>
                </ul>
                <a href="{{ route('admin.exams.index') }}" class="btn btn-info">
                    <i class="fas fa-list"></i> Exams Management
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('class_id').addEventListener('change', function () {
    const cid = this.value;
    const secSel = document.getElementById('section_id');
    const stuSel = document.getElementById('student_id');
    secSel.innerHTML = '<option value="">সব শাখা</option>';
    stuSel.innerHTML = '<option value="">সব শিক্ষার্থী</option>';
    if (!cid) return;
    fetch(`{{ url('api/sections') }}?class_id=${cid}`)
        .then(r => r.json())
        .then(data => data.forEach(s => secSel.innerHTML += `<option value="${s.id}">${s.name}</option>`));
    fetch(`{{ url('api/students') }}?class_id=${cid}`)
        .then(r => r.json())
        .then(data => data.forEach(s => stuSel.innerHTML += `<option value="${s.id}">${s.roll_number || ''} - ${s.name}</option>`));
});

document.getElementById('section_id').addEventListener('change', function () {
    const cid = document.getElementById('class_id').value;
    const sid = this.value;
    const stuSel = document.getElementById('student_id');
    stuSel.innerHTML = '<option value="">সব শিক্ষার্থী</option>';
    if (!cid) return;
    fetch(`{{ url('api/students') }}?class_id=${cid}&section_id=${sid}`)
        .then(r => r.json())
        .then(data => data.forEach(s => stuSel.innerHTML += `<option value="${s.id}">${s.roll_number || ''} - ${s.name}</option>`));
});
</script>
@endpush