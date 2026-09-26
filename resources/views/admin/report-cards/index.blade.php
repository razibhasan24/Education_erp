@extends('admin.app')
@section('page-title', 'রিপোর্ট কার্ড জেনারেট')

@section('content')
<div class="row">
    <div class="col-md-5">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-file-pdf"></i> পুরো ক্লাসের রিপোর্ট কার্ড</h3></div>
            <form action="" method="POST" id="bulkForm" target="_blank">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label>পরীক্ষা *</label>
                        <select id="exam_id" class="form-control select2" required>
                            @foreach($exams as $e)
                                <option value="{{ $e->id }}">{{ $e->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>শ্রেণি *</label>
                        <select name="class_id" class="form-control select2" required>
                            @foreach($classes as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>শাখা</label>
                        <select name="section_id" class="form-control select2">
                            <option value="">সব</option>
                        </select>
                    </div>
                    <p class="text-muted small"><i class="fas fa-info-circle"></i> সব শিক্ষার্থীর রিপোর্ট কার্ড একটি PDF এ ডাউনলোড হবে (প্রতি পেজে একজন)।</p>
                </div>
                <div class="card-footer">
                    <button class="btn btn-danger btn-block"><i class="fas fa-download"></i> PDF ডাউনলোড</button>
                </div>
            </form>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card card-success">
            <div class="card-header"><h3 class="card-title">একক শিক্ষার্থীর রিপোর্ট কার্ড</h3></div>
            <div class="card-body">
                <p class="text-muted">শিক্ষার্থী প্রোফাইল পেজ থেকে "রিপোর্ট কার্ড" ডাউনলোড করতে পারবেন।</p>
                <p>অথবা প্রত্যেক শিক্ষার্থীর রেজাল্ট পেজ থেকে সরাসরি:</p>
                <a href="{{ route('admin.exams.index') }}" class="btn btn-info">
                    <i class="fas fa-list"></i> রেজাল্ট শীট এ যান
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('bulkForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const examId = document.getElementById('exam_id').value;
    this.action = '{{ url("admin/report-cards") }}/' + examId + '/bulk';
});
</script>
@endpush
