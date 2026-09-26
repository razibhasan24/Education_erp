@extends('admin.app')

@section('title', 'মাসিক হাজিরা রিপোর্ট')
@section('page-title', 'শিক্ষার্থী মাসিক হাজিরা রিপোর্ট')

@section('content')
<div class="card card-primary">
    <div class="card-header"><h3 class="card-title"><i class="fas fa-filter"></i> রিপোর্ট ফিল্টার</h3></div>
    <form method="GET">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 form-group">
                    <label>শ্রেণি *</label>
                    <select name="class_id" class="form-control select2" required onchange="this.form.submit()">
                        <option value="">নির্বাচন করুন</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" @selected($classId == $c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 form-group">
                    <label>শাখা</label>
                    <select name="section_id" class="form-control select2">
                        <option value="">সব শাখা</option>
                        @foreach($sections as $s)
                            <option value="{{ $s->id }}" @selected($sectionId == $s->id)>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 form-group">
                    <label>মাস</label>
                    <select name="month" class="form-control">
                        @foreach($months as $k=>$v)
                            <option value="{{ $k }}" @selected($month==$k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 form-group">
                    <label>বছর</label>
                    <select name="year" class="form-control">
                        @for($y = now()->year; $y >= now()->year - 5; $y--)
                            <option value="{{ $y }}" @selected($year==$y)>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2 form-group d-flex align-items-end">
                    <button class="btn btn-primary btn-block"><i class="fas fa-search"></i> রিপোর্ট</button>
                </div>
            </div>
        </div>
    </form>
</div>

@if($reportData->count())
<div class="card card-success">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-chart-bar"></i>
            {{ $months[$month] }} {{ $year }} — মোট {{ $reportData->count() }} জন শিক্ষার্থী
        </h3>
        <div class="card-tools">
            <button onclick="window.print()" class="btn btn-xs btn-light"><i class="fas fa-print"></i> প্রিন্ট</button>
        </div>
        <a href="{{ route('admin.pdf.attendance-report', request()->query()) }}" class="btn btn-danger btn-sm">
    <i class="fas fa-file-pdf"></i> PDF
</a>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-hover datatable">
            <thead class="thead-light">
                <tr>
                    <th>রোল</th>
                    <th>নাম</th>
                    <th class="text-center bg-success">উপস্থিত</th>
                    <th class="text-center bg-danger">অনুপস্থিত</th>
                    <th class="text-center bg-warning">বিলম্ব</th>
                    <th class="text-center bg-info">ছুটি</th>
                    <th class="text-center">মোট দিন</th>
                    <th class="text-center">উপস্থিতির হার</th>
                    <th class="text-center">অ্যাকশন</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportData as $row)
                <tr>
                    <td>{{ $row['student']->roll_number ?? '-' }}</td>
                    <td>
                        <b>{{ $row['student']->name }}</b>
                        <br><small class="text-muted">{{ $row['student']->student_id }}</small>
                    </td>
                    <td class="text-center text-success"><b>{{ $row['present'] }}</b></td>
                    <td class="text-center text-danger"><b>{{ $row['absent'] }}</b></td>
                    <td class="text-center text-warning"><b>{{ $row['late'] }}</b></td>
                    <td class="text-center text-info"><b>{{ $row['leave'] }}</b></td>
                    <td class="text-center">{{ $row['total'] }}</td>
                    <td class="text-center">
                        @php $rate = $row['total'] > 0 ? round(($row['present'] / $row['total']) * 100, 1) : 0; @endphp
                        <div class="progress" style="height:20px;">
                            <div class="progress-bar bg-{{ $rate >= 80 ? 'success' : ($rate >= 60 ? 'warning' : 'danger') }}"
                                 style="width: {{ $rate }}%">{{ $rate }}%</div>
                        </div>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('admin.attendance.students.student-report', ['student' => $row['student']->id, 'month' => $month, 'year' => $year]) }}"
                           class="btn btn-xs btn-info"><i class="fas fa-eye"></i> বিস্তারিত</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@elseif($classId)
    <div class="alert alert-warning">এই ক্লাসে কোনো রেকর্ড নেই।</div>
@endif
@endsection
