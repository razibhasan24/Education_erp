@extends('student.app')
@section('page-title', 'আমার হাজিরা')

@section('content')
<div class="card card-primary">
    <form method="GET" class="card-body form-inline">
        <label class="mr-2">মাস:</label>
        <select name="month" class="form-control form-control-sm mr-2">
            @foreach($months as $k=>$v)<option value="{{ $k }}" @selected($month==$k)>{{ $v }}</option>@endforeach
        </select>
        <select name="year" class="form-control form-control-sm mr-2">
            @for($y=now()->year;$y>=now()->year-3;$y--)<option value="{{ $y }}" @selected($year==$y)>{{ $y }}</option>@endfor
        </select>
        <button class="btn btn-sm btn-info">দেখান</button>
    </form>
</div>

<div class="row">
    <div class="col-md-3"><div class="small-box bg-success"><div class="inner"><h3>{{ $summary['present'] }}</h3><p>উপস্থিত</p></div><div class="icon"><i class="fas fa-check"></i></div></div></div>
    <div class="col-md-3"><div class="small-box bg-danger"><div class="inner"><h3>{{ $summary['absent'] }}</h3><p>অনুপস্থিত</p></div><div class="icon"><i class="fas fa-times"></i></div></div></div>
    <div class="col-md-3"><div class="small-box bg-warning"><div class="inner"><h3>{{ $summary['late'] }}</h3><p>বিলম্ব</p></div><div class="icon"><i class="fas fa-clock"></i></div></div></div>
    <div class="col-md-3"><div class="small-box bg-info"><div class="inner"><h3>{{ $summary['leave'] }}</h3><p>ছুটি</p></div><div class="icon"><i class="fas fa-plane"></i></div></div></div>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered table-sm">
            <thead class="thead-light"><tr><th>তারিখ</th><th>দিন</th><th>স্ট্যাটাস</th><th>মন্তব্য</th></tr></thead>
            <tbody>
                @forelse($attendances as $a)
                <tr>
                    <td>{{ $a->attendance_date->format('d M, Y') }}</td>
                    <td>{{ $a->attendance_date->format('l') }}</td>
                    <td>@php $st = \App\Models\StudentAttendance::statusLabels()[$a->status]; @endphp<span class="badge badge-{{ $st['color'] }}">{{ $st['label'] }}</span></td>
                    <td>{{ $a->remarks ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted">এই মাসে কোনো রেকর্ড নেই</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
