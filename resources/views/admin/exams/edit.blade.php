@extends('admin.app')
@section('title', 'পরীক্ষা সম্পাদনা')
@section('page-title', 'পরীক্ষা সম্পাদনা')

@section('content')
<form action="{{ route('admin.exams.update', $exam) }}" method="POST">
    @csrf @method('PUT')
    <div class="card card-primary">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 form-group"><label>নাম (English) *</label><input type="text" name="name" value="{{ old('name', $exam->name) }}" class="form-control" required></div>
                <div class="col-md-6 form-group"><label>নাম (বাংলা)</label><input type="text" name="name_bn" value="{{ old('name_bn', $exam->name_bn) }}" class="form-control"></div>
                <div class="col-md-4 form-group">
                    <label>ধরন *</label>
                    <select name="exam_type" class="form-control" required>
                        @foreach(\App\Models\Exam::typeLabels() as $k => $v)
                            <option value="{{ $k }}" @selected($exam->exam_type==$k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 form-group">
                    <label>শিক্ষাবর্ষ *</label>
                    <select name="academic_year_id" class="form-control" required>
                        @foreach($academicYears as $y)
                            <option value="{{ $y->id }}" @selected($exam->academic_year_id==$y->id)>{{ $y->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 form-group"><label>শুরু</label><input type="date" name="start_date" value="{{ $exam->start_date?->format('Y-m-d') }}" class="form-control"></div>
                <div class="col-md-2 form-group"><label>শেষ</label><input type="date" name="end_date" value="{{ $exam->end_date?->format('Y-m-d') }}" class="form-control"></div>
                <div class="col-md-12 form-group"><label>বিবরণ</label><textarea name="description" class="form-control" rows="2">{{ $exam->description }}</textarea></div>
                <div class="col-md-12 form-check ml-2">
                    <input type="checkbox" name="is_published" value="1" class="form-check-input" id="pub" @checked($exam->is_published)>
                    <label class="form-check-label" for="pub">রেজাল্ট প্রকাশিত</label>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button class="btn btn-primary"><i class="fas fa-save"></i> আপডেট</button>
            <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary">বাতিল</a>
        </div>
    </div>
</form>
@endsection
