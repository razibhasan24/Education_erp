@extends('admin.app')
@section('title', 'মার্ক এন্ট্রি')
@section('page-title', $exam->name . ' — মার্ক এন্ট্রি')

@section('page-actions')
    <a href="{{ route('admin.exams.subjects', $exam) }}" class="btn btn-secondary btn-sm">বিষয় দেখুন</a>
    <a href="{{ route('admin.exams.result', $exam) }}" class="btn btn-info btn-sm">রেজাল্ট</a>
@endsection

@section('content')
<div class="card card-primary">
    <form method="GET" class="card-body">
        <div class="row">
            <div class="col-md-3 form-group">
                <label>শ্রেণি *</label>
                <select name="class_id" class="form-control select2" required onchange="this.form.submit()">
                    <option value="">নির্বাচন করুন</option>
                    @foreach($classes as $c)<option value="{{ $c->id }}" @selected($classId==$c->id)>{{ $c->name }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label>শাখা</label>
                <select name="section_id" class="form-control select2">
                    <option value="">সব শাখা</option>
                    @foreach($sections as $s)<option value="{{ $s->id }}" @selected($sectionId==$s->id)>{{ $s->name }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label>বিষয় *</label>
                <select name="exam_subject_id" class="form-control select2" required>
                    <option value="">নির্বাচন করুন</option>
                    @foreach($examSubjects as $es)
                        <option value="{{ $es->id }}" @selected($examSubjectId==$es->id)>
                            {{ $es->subject->name ?? '' }} (পূর্ণ: {{ $es->full_marks }}, পাস: {{ $es->pass_marks }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 form-group d-flex align-items-end">
                <button class="btn btn-primary btn-block"><i class="fas fa-search"></i> দেখান</button>
            </div>
        </div>
    </form>
</div>

@if($students->count() && $examSubjectId)
    @php $examSubject = $examSubjects->firstWhere('id', $examSubjectId); @endphp
    <form action="{{ route('admin.exams.marks.store', $exam) }}" method="POST">
        @csrf
        <input type="hidden" name="exam_subject_id" value="{{ $examSubjectId }}">
        <input type="hidden" name="class_id" value="{{ $classId }}">
        <input type="hidden" name="section_id" value="{{ $sectionId }}">

        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-pen"></i> {{ $examSubject->subject->name }} —
                    পূর্ণ নম্বর: {{ $examSubject->full_marks }}, পাস: {{ $examSubject->pass_marks }}
                </h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-striped">
                    <thead class="thead-light">
                        <tr>
                            <th width="60">রোল</th>
                            <th>নাম</th>
                            <th width="110">লিখিত</th>
                            <th width="110">MCQ</th>
                            <th width="110">ব্যবহারিক</th>
                            <th width="100">মোট</th>
                            <th width="80">গ্রেড</th>
                            <th width="80">অনুপস্থিত</th>
                            <th>মন্তব্য</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                            @php $mark = $existingMarks->get($student->id); @endphp
                            <tr>
                                <td><b>{{ $student->roll_number ?? '-' }}</b></td>
                                <td>{{ $student->name }}</td>
                                <td><input type="number" step="0.01" min="0" max="{{ $examSubject->full_marks }}"
                                          name="written[{{ $student->id }}]"
                                          value="{{ $mark?->written_marks ?? 0 }}"
                                          class="form-control form-control-sm mark-input w-{{ $student->id }}"
                                          oninput="calcRow({{ $student->id }}, {{ $examSubject->full_marks }})"></td>
                                <td><input type="number" step="0.01" min="0" max="{{ $examSubject->full_marks }}"
                                          name="mcq[{{ $student->id }}]"
                                          value="{{ $mark?->mcq_marks ?? 0 }}"
                                          class="form-control form-control-sm mark-input m-{{ $student->id }}"
                                          oninput="calcRow({{ $student->id }}, {{ $examSubject->full_marks }})"></td>
                                <td><input type="number" step="0.01" min="0" max="{{ $examSubject->full_marks }}"
                                          name="practical[{{ $student->id }}]"
                                          value="{{ $mark?->practical_marks ?? 0 }}"
                                          class="form-control form-control-sm mark-input p-{{ $student->id }}"
                                          oninput="calcRow({{ $student->id }}, {{ $examSubject->full_marks }})"></td>
                                <td><b class="total-{{ $student->id }}">{{ $mark?->total_marks ?? 0 }}</b></td>
                                <td><span class="badge badge-secondary grade-{{ $student->id }}">{{ $mark?->grade ?? '-' }}</span></td>
                                <td class="text-center">
                                    <input type="checkbox" name="is_absent[{{ $student->id }}]" value="1"
                                           class="absent-{{ $student->id }}" @checked($mark?->is_absent)
                                           onchange="calcRow({{ $student->id }}, {{ $examSubject->full_marks }})">
                                </td>
                                <td><input type="text" name="remarks[{{ $student->id }}]" value="{{ $mark?->remarks }}" class="form-control form-control-sm"></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-save"></i> নম্বর সংরক্ষণ করুন</button>
            </div>
        </div>
    </form>
@elseif($classId && !$examSubjectId)
    <div class="alert alert-warning">অনুগ্রহ করে একটি বিষয় নির্বাচন করুন।</div>
@endif
@endsection

@push('scripts')
<script>
function getGrade(pct) {
    if (pct >= 80) return {g:'A+', c:'success'};
    if (pct >= 70) return {g:'A', c:'success'};
    if (pct >= 60) return {g:'A-', c:'info'};
    if (pct >= 50) return {g:'B', c:'info'};
    if (pct >= 40) return {g:'C', c:'warning'};
    if (pct >= 33) return {g:'D', c:'warning'};
    return {g:'F', c:'danger'};
}
function calcRow(sid, full) {
    const absent = document.querySelector('.absent-'+sid).checked;
    let total = 0;
    if (!absent) {
        total = parseFloat(document.querySelector('.w-'+sid).value || 0)
              + parseFloat(document.querySelector('.m-'+sid).value || 0)
              + parseFloat(document.querySelector('.p-'+sid).value || 0);
    }
    document.querySelector('.total-'+sid).innerText = total;
    const pct = (total / full) * 100;
    const g = getGrade(pct);
    const badge = document.querySelector('.grade-'+sid);
    badge.className = 'badge badge-' + g.c + ' grade-' + sid;
    badge.innerText = g.g;
}
</script>
@endpush
