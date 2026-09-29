@extends('admin.app')
@section('page-title', 'শিক্ষক মূল্যায়ন ফর্ম')

@section('content')
<form action="{{ route('admin.evaluations.store') }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header"><h3 class="card-title">মূল্যায়ন তথ্য</h3></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>শিক্ষক *</label>
                            <select name="teacher_id" class="form-control select2" required>
                                <option value="">-- নির্বাচন --</option>
                                @foreach($teachers as $t)<option value="{{ $t->id }}">{{ $t->name }} ({{ $t->teacher_id }})</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>শিক্ষার্থী *</label>
                            <select name="student_id" class="form-control select2" required>
                                <option value="">-- নির্বাচন --</option>
                                @foreach($students as $s)<option value="{{ $s->id }}">{{ $s->name }} ({{ $s->student_id }})</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>শ্রেণি</label>
                            <select name="class_id" class="form-control select2">
                                <option value="">-- নির্বাচন --</option>
                                @foreach($classes as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>বিষয়</label>
                            <select name="subject_id" class="form-control select2">
                                <option value="">-- নির্বাচন --</option>
                                @foreach($subjects as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
                            </select>
                        </div>
                    </div>

                    <hr>
                    <h5 class="mb-3">রেটিং (১ = খারাপ, ৫ = অসাধারণ)</h5>

                    @foreach(\App\Models\TeacherEvaluation::criteria() as $key => $label)
                    <div class="form-group">
                        <label>{{ $label }}</label>
                        <div class="rating-stars">
                            @for($i = 1; $i <= 5; $i++)
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="{{ $key }}_{{ $i }}" name="{{ $key }}" value="{{ $i }}"
                                           class="custom-control-input" {{ $i == 5 ? 'checked' : '' }} required>
                                    <label class="custom-control-label" for="{{ $key }}_{{ $i }}">
                                        @for($s = 1; $s <= $i; $s++) <i class="fas fa-star text-warning"></i>@endfor
                                    </label>
                                </div>
                            @endfor
                        </div>
                    </div>
                    @endforeach

                    <div class="form-group">
                        <label>মন্তব্য</label>
                        <textarea name="comments" class="form-control" rows="3" placeholder="শিক্ষক সম্পর্কে আপনার মতামত"></textarea>
                    </div>

                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="is_anonymous" value="1" class="custom-control-input" id="anon" checked>
                        <label class="custom-control-label" for="anon">নাম গোপন রাখুন</label>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary btn-lg"><i class="fas fa-save"></i> মূল্যায়ন জমা দিন</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection