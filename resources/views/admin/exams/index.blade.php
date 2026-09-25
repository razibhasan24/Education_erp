@extends('admin.app')
@section('title', 'পরীক্ষা তালিকা')
@section('page-title', 'পরীক্ষা তালিকা')

@section('page-actions')
    <a href="{{ route('admin.exams.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> নতুন পরীক্ষা</a>
@endsection

@section('content')
<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover datatable">
            <thead>
                <tr>
                    <th>নাম</th>
                    <th>ধরন</th>
                    <th>শিক্ষাবর্ষ</th>
                    <th>শুরু</th>
                    <th>শেষ</th>
                    <th>প্রকাশিত</th>
                    <th>অ্যাকশন</th>
                </tr>
            </thead>
            <tbody>
                @foreach($exams as $exam)
                <tr>
                    <td><b>{{ $exam->name }}</b><br><small class="text-muted">{{ $exam->name_bn }}</small></td>
                    <td><span class="badge badge-info">{{ \App\Models\Exam::typeLabels()[$exam->exam_type] }}</span></td>
                    <td>{{ $exam->academicYear->name ?? '-' }}</td>
                    <td>{{ $exam->start_date?->format('d M, Y') ?? '-' }}</td>
                    <td>{{ $exam->end_date?->format('d M, Y') ?? '-' }}</td>
                    <td>@if($exam->is_published)<span class="badge badge-success">হ্যাঁ</span>@else<span class="badge badge-secondary">না</span>@endif</td>
                    <td>
                        <a href="{{ route('admin.exams.subjects', $exam) }}" class="btn btn-xs btn-primary" title="বিষয়"><i class="fas fa-book"></i></a>
                        <a href="{{ route('admin.exams.marks', $exam) }}" class="btn btn-xs btn-warning" title="মার্ক এন্ট্রি"><i class="fas fa-edit"></i></a>
                        <a href="{{ route('admin.exams.result', $exam) }}" class="btn btn-xs btn-info" title="রেজাল্ট"><i class="fas fa-chart-bar"></i></a>
                        <a href="{{ route('admin.exams.edit', $exam) }}" class="btn btn-xs btn-secondary"><i class="fas fa-cog"></i></a>
                        <form action="{{ route('admin.exams.destroy', $exam) }}" method="POST" class="d-inline" onsubmit="return confirm('নিশ্চিত?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
