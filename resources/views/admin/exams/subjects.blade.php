@extends('admin.app')
@section('title', 'পরীক্ষার বিষয়')
@section('page-title', $exam->name . ' — বিষয় ও নম্বর')

@section('page-actions')
    <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary btn-sm">ফিরে যান</a>
    <a href="{{ route('admin.exams.marks', $exam) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> মার্ক এন্ট্রি</a>
    <a href="{{ route('admin.exams.result', $exam) }}" class="btn btn-info btn-sm"><i class="fas fa-chart-bar"></i> রেজাল্ট</a>
@endsection

@section('content')
<div class="row">
    <div class="col-md-5">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">নতুন বিষয় যোগ</h3></div>
            <form action="{{ route('admin.exams.subjects.store', $exam) }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label>শ্রেণি *</label>
                        <select name="class_id" class="form-control select2" required>
                            <option value="">নির্বাচন করুন</option>
                            @foreach($classes as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>বিষয় *</label>
                        <select name="subject_id" class="form-control select2" required>
                            <option value="">নির্বাচন করুন</option>
                            @foreach($subjects as $s)<option value="{{ $s->id }}">{{ $s->name }} ({{ $s->schoolClass->name ?? 'সব' }})</option>@endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>বিভাগ</label>
                        <select name="group_id" class="form-control select2">
                            <option value="">সব বিভাগ</option>
                            @foreach($groups as $g)<option value="{{ $g->id }}">{{ $g->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group"><label>পূর্ণ নম্বর *</label><input type="number" name="full_marks" value="100" class="form-control" required></div>
                        <div class="col-6 form-group"><label>পাস নম্বর *</label><input type="number" name="pass_marks" value="33" class="form-control" required></div>
                    </div>
                    <div class="row">
                        <div class="col-4 form-group"><label>লিখিত</label><input type="number" name="written_marks" value="0" class="form-control"></div>
                        <div class="col-4 form-group"><label>MCQ</label><input type="number" name="mcq_marks" value="0" class="form-control"></div>
                        <div class="col-4 form-group"><label>ব্যবহারিক</label><input type="number" name="practical_marks" value="0" class="form-control"></div>
                    </div>
                    <div class="form-group"><label>পরীক্ষার তারিখ</label><input type="date" name="exam_date" class="form-control"></div>
                </div>
                <div class="card-footer"><button class="btn btn-primary"><i class="fas fa-plus"></i> যোগ করুন</button></div>
            </form>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card card-info">
            <div class="card-header"><h3 class="card-title">অটো-ইমপোর্ট (ক্লাসের সব বিষয়)</h3></div>
            <form action="{{ route('admin.exams.subjects.bulk', $exam) }}" method="POST" class="card-body">
                @csrf
                <div class="row">
                    <div class="col-md-8">
                        <select name="class_id" class="form-control select2" required>
                            <option value="">শ্রেণি নির্বাচন করুন</option>
                            @foreach($classes as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-md-4"><button class="btn btn-info btn-block"><i class="fas fa-magic"></i> ইমপোর্ট</button></div>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="card-header"><h3 class="card-title">যোগ করা বিষয় ({{ $exam->examSubjects->count() }})</h3></div>
            <div class="card-body table-responsive">
                <table class="table table-striped datatable">
                    <thead><tr><th>শ্রেণি</th><th>বিষয়</th><th>বিভাগ</th><th>পূর্ণ/পাস</th><th>তারিখ</th><th></th></tr></thead>
                    <tbody>
                        @foreach($exam->examSubjects as $es)
                        <tr>
                            <td>{{ $es->schoolClass->name ?? '-' }}</td>
                            <td><b>{{ $es->subject->name ?? '-' }}</b></td>
                            <td>{{ $es->group->name ?? 'সব' }}</td>
                            <td>{{ $es->full_marks }}/{{ $es->pass_marks }}</td>
                            <td>{{ $es->exam_date?->format('d M, Y') ?? '-' }}</td>
                            <td>
                                <form action="{{ route('admin.exams.subjects.destroy', $es) }}" method="POST" onsubmit="return confirm('নিশ্চিত?')">
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
