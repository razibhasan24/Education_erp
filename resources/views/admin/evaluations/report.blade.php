@extends('admin.app')
@section('page-title', 'শিক্ষক মূল্যায়ন রিপোর্ট')

@section('page-actions')
    <button onclick="window.print()" class="btn btn-info btn-sm"><i class="fas fa-print"></i> প্রিন্ট</button>
@endsection

@section('content')
<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="thead-light">
                <tr>
                    <th>র‍্যাংক</th>
                    <th>শিক্ষক</th>
                    <th>পদবি</th>
                    <th>মোট মূল্যায়ন</th>
                    <th class="text-center">Overall</th>
                    @foreach(\App\Models\TeacherEvaluation::criteria() as $label)
                        <th class="text-center">{{ $label }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($data as $i => $row)
                <tr>
                    <td><b>{{ $i + 1 }}</b></td>
                    <td>{{ $row['teacher']->name }}</td>
                    <td>{{ $row['teacher']->designation ?? '-' }}</td>
                    <td>{{ $row['count'] }}</td>
                    <td class="text-center">
                        <h5 class="text-warning mb-0">{{ number_format($row['overall'], 2) }}</h5>
                    </td>
                    @foreach($row['breakdown'] as $avg)
                        <td class="text-center">{{ number_format($avg, 1) }}</td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection