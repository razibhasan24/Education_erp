@extends('admin.app')
@section('title', 'রেজাল্ট শীট')
@section('page-title', $exam->name . ' — রেজাল্ট শীট')

@section('page-actions')
    <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary btn-sm">ফিরে যান</a>
    <button onclick="window.print()" class="btn btn-info btn-sm"><i class="fas fa-print"></i> প্রিন্ট</button>
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
            <div class="col-md-3 form-group d-flex align-items-end">
                <button class="btn btn-primary btn-block"><i class="fas fa-search"></i> রেজাল্ট দেখান</button>
            </div>
        </div>
    </form>
</div>

@if($resultData->count())
<div class="card card-success">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-list-ol"></i> রেজাল্ট শীট ({{ $resultData->count() }} জন)</h3>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-hover datatable">
            <thead class="thead-light">
                <tr>
                    <th>মেধাক্রম</th>
                    <th>রোল</th>
                    <th>নাম</th>
                    @foreach($examSubjects as $es)
                        <th class="text-center">{{ $es->subject->name ?? '' }}<br><small>({{ $es->full_marks }})</small></th>
                    @endforeach
                    <th class="text-center">মোট</th>
                    <th class="text-center">GPA</th>
                    <th class="text-center">গ্রেড</th>
                    <th class="text-center">ফলাফল</th>
                    <th class="text-center">মার্কশিট</th>
                </tr>
            </thead>
            <tbody>
                @foreach($resultData as $row)
                <tr>
                    <td class="text-center"><b>{{ $row['position'] }}</b></td>
                    <td>{{ $row['student']->roll_number ?? '-' }}</td>
                    <td><b>{{ $row['student']->name }}</b></td>
                    @foreach($row['subjects'] as $sr)
                        <td class="text-center">
                            {{ $sr['marks'] }}
                            <br>
                            <small class="badge badge-{{ \App\Helpers\GradeHelper::getGrade($sr['marks'], $sr['full_marks'])['color'] }}">
                                {{ $sr['grade'] }}
                            </small>
                        </td>
                    @endforeach
                    <td class="text-center"><b>{{ $row['overall']['total_marks'] }}</b></td>
                    <td class="text-center"><b>{{ number_format($row['overall']['gpa'], 2) }}</b></td>
                    <td class="text-center"><span class="badge badge-info">{{ $row['overall']['grade'] }}</span></td>
                    <td class="text-center">
                        @if($row['overall']['failed'])
                            <span class="badge badge-danger">ফেল</span>
                        @else
                            <span class="badge badge-success">পাস</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <a href="{{ route('admin.exams.marksheet', [$exam, $row['student']]) }}" class="btn btn-xs btn-info">
                            <i class="fas fa-file-alt"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@elseif($classId)
    <div class="alert alert-warning">এই শ্রেণিতে কোনো রেজাল্ট নেই। প্রথমে মার্ক এন্ট্রি করুন।</div>
@endif
@endsection
