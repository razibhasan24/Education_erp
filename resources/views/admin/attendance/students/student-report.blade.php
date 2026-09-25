@extends('admin.app')

@section('title', 'হাজিরা বিস্তারিত')
@section('page-title', $student->name . ' — হাজিরার বিস্তারিত')

@section('page-actions')
    <a href="{{ route('admin.attendance.students.report', ['class_id' => $student->class_id]) }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left"></i> ফিরে যান
    </a>
    <button onclick="window.print()" class="btn btn-info btn-sm"><i class="fas fa-print"></i> প্রিন্ট</button>
@endsection

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="card card-primary card-outline">
            <div class="card-body text-center">
                <img src="{{ $student->photo ? asset('storage/'.$student->photo) : 'https://ui-avatars.com/api/?name='.urlencode($student->name).'&size=150' }}"
                     class="rounded-circle" style="width:100px;height:100px;">
                <h5 class="mt-2">{{ $student->name }}</h5>
                <p class="text-muted">{{ $student->student_id }}</p>
                <hr>
                <form method="GET" class="form-inline justify-content-center">
                    <select name="month" class="form-control form-control-sm mr-1">
                        @foreach($months as $k=>$v)
                            <option value="{{ $k }}" @selected($month==$k)>{{ $v }}</option>
                        @endforeach
                    </select>
                    <select name="year" class="form-control form-control-sm mr-1">
                        @for($y = now()->year; $y >= now()->year - 5; $y--)
                            <option value="{{ $y }}" @selected($year==$y)>{{ $y }}</option>
                        @endfor
                    </select>
                    <button class="btn btn-sm btn-primary">দেখান</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <div class="row">
            <div class="col-md-3"><div class="small-box bg-success"><div class="inner"><h3>{{ $summary['present'] }}</h3><p>উপস্থিত</p></div><div class="icon"><i class="fas fa-check"></i></div></div></div>
            <div class="col-md-3"><div class="small-box bg-danger"><div class="inner"><h3>{{ $summary['absent'] }}</h3><p>অনুপস্থিত</p></div><div class="icon"><i class="fas fa-times"></i></div></div></div>
            <div class="col-md-3"><div class="small-box bg-warning"><div class="inner"><h3>{{ $summary['late'] }}</h3><p>বিলম্ব</p></div><div class="icon"><i class="fas fa-clock"></i></div></div></div>
            <div class="col-md-3"><div class="small-box bg-info"><div class="inner"><h3>{{ $summary['leave'] }}</h3><p>ছুটি</p></div><div class="icon"><i class="fas fa-plane"></i></div></div></div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ $months[$month] }} {{ $year }} — প্রতিদিনের হাজিরা</h3>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th>তারিখ</th>
                            <th>দিন</th>
                            <th>স্ট্যাটাস</th>
                            <th>মন্তব্য</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $a)
                        <tr>
                            <td>{{ $a->attendance_date->format('d M, Y') }}</td>
                            <td>{{ $a->attendance_date->format('l') }}</td>
                            <td>
                                @php $st = \App\Models\StudentAttendance::statusLabels()[$a->status]; @endphp
                                <span class="badge badge-{{ $st['color'] }}">{{ $st['label'] }}</span>
                            </td>
                            <td>{{ $a->remarks ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted">এই মাসে কোনো রেকর্ড নেই</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
