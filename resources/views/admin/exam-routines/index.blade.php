@extends('admin.app')
@section('page-title', $exam->name . ' — পরীক্ষার রুটিন')

@section('page-actions')
    <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary btn-sm">ফিরে যান</a>
    @if($selectedClass && $routines->count())
        <a href="{{ route('admin.exams.routine.pdf', [$exam, 'class_id' => $selectedClass, 'section_id' => request('section_id')]) }}" 
           class="btn btn-danger btn-sm" target="_blank">
            <i class="fas fa-file-pdf"></i> PDF ডাউনলোড
        </a>
    @endif
@endsection

@section('content')
<div class="card card-primary">
    <form method="GET" class="card-body form-inline">
        <label class="mr-1">শ্রেণি *</label>
        <select name="class_id" class="form-control form-control-sm mr-2" required onchange="this.form.submit()">
            <option value="">নির্বাচন</option>
            @foreach($classes as $c)
                <option value="{{ $c->id }}" @selected($selectedClass==$c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
        <button class="btn btn-sm btn-info"><i class="fas fa-search"></i> দেখান</button>
    </form>
</div>

@if($selectedClass)
<div class="row">
    <div class="col-md-4">
        <div class="card card-success">
            <div class="card-header"><h3 class="card-title">নতুন রুটিন এন্ট্রি</h3></div>
            <form action="{{ route('admin.exams.routine.store', $exam) }}" method="POST">
                @csrf
                <input type="hidden" name="class_id" value="{{ $selectedClass }}">
                <div class="card-body">
                    <div class="form-group">
                        <label>বিষয় *</label>
                        <select name="subject_id" class="form-control select2" required>
                            <option value="">নির্বাচন</option>
                            @foreach($subjects as $es)
                                <option value="{{ $es->subject_id }}" data-es="{{ $es->id }}">
                                    {{ $es->subject->name ?? '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group"><label>তারিখ *</label><input type="date" name="exam_date" value="{{ date('Y-m-d') }}" class="form-control" required></div>
                    <div class="row">
                        <div class="col-6 form-group"><label>শুরু *</label><input type="time" name="start_time" value="09:00" class="form-control" required></div>
                        <div class="col-6 form-group"><label>শেষ *</label><input type="time" name="end_time" value="12:00" class="form-control" required></div>
                    </div>
                    <div class="form-group"><label>রুম</label><input type="text" name="room_no" class="form-control"></div>
                    <div class="form-group"><label>পরিদর্শক</label><input type="text" name="invigilator" class="form-control"></div>
                    <div class="form-group"><label>নোট</label><textarea name="note" class="form-control" rows="2"></textarea></div>
                </div>
                <div class="card-footer"><button class="btn btn-success btn-block"><i class="fas fa-plus"></i> যোগ</button></div>
            </form>
        </div>

        <div class="card card-info">
            <div class="card-header"><h3 class="card-title">বাল্ক রুটিন (সব বিষয় অটো)</h3></div>
            <form action="{{ route('admin.exams.routine.bulk', $exam) }}" method="POST">
                @csrf
                <input type="hidden" name="class_id" value="{{ $selectedClass }}">
                <div class="card-body">
                    <div class="form-group"><label>শুরুর তারিখ *</label><input type="date" name="start_date" value="{{ date('Y-m-d') }}" class="form-control" required></div>
                    <div class="row">
                        <div class="col-6 form-group"><label>শুরু *</label><input type="time" name="start_time" value="09:00" class="form-control" required></div>
                        <div class="col-6 form-group"><label>শেষ *</label><input type="time" name="end_time" value="12:00" class="form-control" required></div>
                    </div>
                    <div class="form-group"><label>প্রতি বিষয়ে দিন *</label><input type="number" name="duration_minutes_per_day" value="60" class="form-control" required></div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-info btn-block" onclick="return confirm('প্রতিটি বিষয়ের জন্য ১টি করে দিনে রুটিন তৈরি হবে। চালিয়ে যাবেন?')">
                        <i class="fas fa-magic"></i> বাল্ক তৈরি
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-calendar-alt"></i> রুটিন ({{ $routines->count() }}টি বিষয়)</h3></div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>তারিখ</th>
                            <th>বিষয়</th>
                            <th>সময়</th>
                            <th>স্থিতিকাল</th>
                            <th>রুম</th>
                            <th>পরিদর্শক</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($routines as $r)
                        <tr>
                            <td><b>{{ $r->exam_date->format('d M, Y') }}</b><br><small class="text-muted">{{ $r->exam_date->format('l') }}</small></td>
                            <td>{{ $r->subject->name ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($r->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($r->end_time)->format('h:i A') }}</td>
                            <td>{{ $r->duration }}</td>
                            <td>{{ $r->room_no ?? '-' }}</td>
                            <td>{{ $r->invigilator ?? '-' }}</td>
                            <td>
                                <form action="{{ route('admin.exams.routine.destroy', $r) }}" method="POST" onsubmit="return confirm('নিশ্চিত?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-info-circle"></i> এখনো কোনো রুটিন যোগ করা হয়নি
                        </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif
@endsection