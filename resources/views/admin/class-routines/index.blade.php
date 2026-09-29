@extends('admin.app')
@section('page-title', 'ক্লাস রুটিন (সাপ্তাহিক)')

@section('page-actions')
    @if($classId)
        <a href="{{ route('admin.class-routines.pdf', ['class_id' => $classId, 'section_id' => $sectionId]) }}" 
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
            @foreach($classes as $c)<option value="{{ $c->id }}" @selected($classId==$c->id)>{{ $c->name }}</option>@endforeach
        </select>
        <label class="mr-1">শাখা</label>
        <select name="section_id" class="form-control form-control-sm mr-2">
            <option value="">সব</option>
            @foreach($sections as $s)<option value="{{ $s->id }}" @selected($sectionId==$s->id)>{{ $s->name }}</option>@endforeach
        </select>
        <button class="btn btn-sm btn-info"><i class="fas fa-search"></i> দেখান</button>
    </form>
</div>

@if($classId)
<div class="row">
    <div class="col-md-4">
        <div class="card card-success">
            <div class="card-header"><h3 class="card-title">নতুন পিরিয়ড যোগ</h3></div>
            <form action="{{ route('admin.class-routines.store') }}" method="POST">
                @csrf
                <input type="hidden" name="class_id" value="{{ $classId }}">
                <div class="card-body">
                    <div class="form-group">
                        <label>শাখা</label>
                        <select name="section_id" class="form-control">
                            <option value="">--</option>
                            @foreach($sections as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>দিন *</label>
                        <select name="day" class="form-control" required>
                            @foreach(\App\Models\ClassRoutine::days() as $k=>$v)
                                <option value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group"><label>শুরু *</label><input type="time" name="start_time" value="09:00" class="form-control" required></div>
                        <div class="col-6 form-group"><label>শেষ *</label><input type="time" name="end_time" value="09:45" class="form-control" required></div>
                    </div>
                    <div class="form-group">
                        <label>বিষয়</label>
                        <select name="subject_id" class="form-control select2">
                            <option value="">-- নির্বাচন --</option>
                            @foreach($subjects as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>শিক্ষক</label>
                        <select name="teacher_id" class="form-control select2">
                            <option value="">-- নির্বাচন --</option>
                            @foreach($teachers as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group"><label>রুম</label><input type="text" name="room_no" class="form-control"></div>
                        <div class="col-6 form-group"><label>পিরিয়ড #</label><input type="text" name="period_no" class="form-control" placeholder="1st"></div>
                    </div>
                </div>
                <div class="card-footer"><button class="btn btn-success btn-block"><i class="fas fa-plus"></i> যোগ</button></div>
            </form>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-calendar-week"></i> সাপ্তাহিক রুটিন</h3></div>
            <div class="card-body">
                @foreach(\App\Models\ClassRoutine::days() as $dayKey => $dayLabel)
                    @if(isset($routines[$dayKey]) && $routines[$dayKey]->count() > 0)
                        <h5 class="mt-3 mb-2 text-primary">
                            <i class="fas fa-calendar-day"></i> {{ $dayLabel }}
                        </h5>
                        <table class="table table-sm table-bordered mb-3">
                            <thead class="thead-light">
                                <tr>
                                    <th width="90">সময়</th>
                                    <th>বিষয়</th>
                                    <th>শিক্ষক</th>
                                    <th width="60">রুম</th>
                                    <th width="60"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($routines[$dayKey] as $r)
                                <tr>
                                    <td><small>{{ \Carbon\Carbon::parse($r->start_time)->format('h:i A') }}<br>-{{ \Carbon\Carbon::parse($r->end_time)->format('h:i A') }}</small></td>
                                    <td>
                                        {{ $r->subject->name ?? '—' }}
                                        @if($r->section)<br><small class="text-muted">শাখা: {{ $r->section->name }}</small>@endif
                                    </td>
                                    <td>{{ $r->teacher->name ?? '—' }}</td>
                                    <td>{{ $r->room_no ?? '—' }}</td>
                                    <td>
                                        <form action="{{ route('admin.class-routines.destroy', $r) }}" method="POST" onsubmit="return confirm('নিশ্চিত?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-xs btn-danger"><i class="fas fa-times"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif
@endsection