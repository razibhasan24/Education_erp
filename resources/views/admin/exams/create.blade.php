@extends('admin.app')
@section('title', 'নতুন পরীক্ষা')
@section('page-title', 'নতুন পরীক্ষা তৈরি')

@section('content')
<form action="{{ route('admin.exams.store') }}" method="POST">
    @csrf
    <div class="card card-primary">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 form-group">
                    <label>নাম (English) *</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
                </div>
                <div class="col-md-6 form-group">
                    <label>নাম (বাংলা)</label>
                    <input type="text" name="name_bn" value="{{ old('name_bn') }}" class="form-control">
                </div>
                <div class="col-md-4 form-group">
                    <label>পরীক্ষার ধরন *</label>
                    <select name="exam_type" class="form-control" required>
                        @foreach(\App\Models\Exam::typeLabels() as $k => $v)
                            <option value="{{ $k }}" @selected(old('exam_type')==$k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 form-group">
                    <label>শিক্ষাবর্ষ *</label>
                    <select name="academic_year_id" class="form-control" required>
                        @foreach($academicYears as $y)
                            <option value="{{ $y->id }}" @selected($y->is_current)>{{ $y->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 form-group">
                    <label>শুরু</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" class="form-control">
                </div>
                <div class="col-md-2 form-group">
                    <label>শেষ</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" class="form-control">
                </div>
                <div class="col-md-12 form-group">
                    <label>বিবরণ</label>
                    <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button class="btn btn-primary"><i class="fas fa-save"></i> সংরক্ষণ করে বিষয় যোগ করুন</button>
            <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary">বাতিল</a>
        </div>
    </div>
</form>
@endsection
