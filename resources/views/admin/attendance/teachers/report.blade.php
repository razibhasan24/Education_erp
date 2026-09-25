@extends('admin.app')

@section('title', 'শিক্ষক হাজিরা রিপোর্ট')
@section('page-title', 'শিক্ষক মাসিক হাজিরা রিপোর্ট')

@section('content')
<div class="card card-primary">
    <form method="GET" class="form-inline p-3">
        <label class="mr-2">মাস:</label>
        <select name="month" class="form-control mr-2">
            @foreach($months as $k=>$v)<option value="{{ $k }}" @selected($month==$k)>{{ $v }}</option>@endforeach
        </select>
        <select name="year" class="form-control mr-2">
            @for($y = now()->year; $y >= now()->year - 5; $y--)
                <option value="{{ $y }}" @selected($year==$y)>{{ $y }}</option>
            @endfor
        </select>
        <button class="btn btn-primary"><i class="fas fa-search"></i> রিপোর্ট</button>
    </form>
</div>

<div class="card card-success">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-chart-line"></i> {{ $months[$month] }} {{ $year }}</h3>
        <div class="card-tools"><button onclick="window.print()" class="btn btn-xs btn-light"><i class="fas fa-print"></i> প্রিন্ট</button></div>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-hover datatable">
            <thead class="thead-light">
                <tr>
                    <th>ID</th>
                    <th>নাম</th>
                    <th>পদবি</th>
                    <th class="text-center bg-success">উপস্থিত</th>
                    <th class="text-center bg-danger">অনুপস্থিত</th>
                    <th class="text-center bg-warning">বিলম্ব</th>
                    <th class="text-center bg-info">ছুটি</th>
                    <th class="text-center">মোট</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportData as $row)
                <tr>
                    <td><b>{{ $row['teacher']->teacher_id }}</b></td>
                    <td>{{ $row['teacher']->name }}</td>
                    <td>{{ $row['teacher']->designation ?? '-' }}</td>
                    <td class="text-center text-success"><b>{{ $row['present'] }}</b></td>
                    <td class="text-center text-danger"><b>{{ $row['absent'] }}</b></td>
                    <td class="text-center text-warning"><b>{{ $row['late'] }}</b></td>
                    <td class="text-center text-info"><b>{{ $row['leave'] }}</b></td>
                    <td class="text-center">{{ $row['total'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
