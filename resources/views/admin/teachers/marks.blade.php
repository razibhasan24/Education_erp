@extends('teacher.app')
@section('page-title', 'মার্ক এন্ট্রি')

@section('content')
<div class="card card-primary">
    <form method="GET" class="card-body">
        <div class="row">
            <div class="col-md-8 form-group">
                <label>পরীক্ষা ও বিষয় *</label>
                <select name="exam_subject_id" class="form-control" required>
                    <option value="">নির্বাচন</option>
                    @foreach($examSubjects as $es)
                        <option value="{{ $es->id }}" @selected($examSubjectId==$es->id)>
                            {{ $es->exam->name ?? '' }} — {{ $es->subject->name ?? '' }} ({{ $es->schoolClass->name ?? '' }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 form-group d-flex align-items-end">
                <button class="btn btn-primary btn-block"><i class="fas fa-search"></i> দেখান</button>
            </div>
        </div>
    </form>
</div>

@if($students->count() && $selectedSubject)
<form action="{{ route('teacher.marks.store') }}" method="POST">
    @csrf
    <input type="hidden" name="exam_subject_id" value="{{ $examSubjectId }}">

    <div class="card card-success">
        <div class="card-header">
            <h3 class="card-title">{{ $selectedSubject->subject->name }} — পূর্ণ: {{ $selectedSubject->full_marks }}</h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-sm">
                <thead class="thead-light">
                    <tr><th>রোল</th><th>নাম</th><th>লিখিত</th><th>MCQ</th><th>ব্যবহারিক</th><th>অনুপস্থিত</th></tr>
                </thead>
                <tbody>
                    @foreach($students as $s)
                        @php $m = $existing->get($s->id); @endphp
                        <tr>
                            <td>{{ $s->roll_number ?? '-' }}</td>
                            <td>{{ $s->name }}</td>
                            <td><input type="number" step="0.01" name="written[{{ $s->id }}]" value="{{ $m?->written_marks ?? 0 }}" class="form-control form-control-sm"></td>
                            <td><input type="number" step="0.01" name="mcq[{{ $s->id }}]" value="{{ $m?->mcq_marks ?? 0 }}" class="form-control form-control-sm"></td>
                            <td><input type="number" step="0.01" name="practical[{{ $s->id }}]" value="{{ $m?->practical_marks ?? 0 }}" class="form-control form-control-sm"></td>
                            <td class="text-center"><input type="checkbox" name="is_absent[{{ $s->id }}]" value="1" @checked($m?->is_absent)></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <button class="btn btn-success btn-lg"><i class="fas fa-save"></i> সংরক্ষণ</button>
        </div>
    </div>
</form>
@endif
@endsection
