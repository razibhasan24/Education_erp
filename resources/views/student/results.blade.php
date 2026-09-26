@extends('student.app')
@section('page-title', 'আমার ফলাফল')

@section('content')
@forelse($examResults as $r)
<div class="card card-success">
    <div class="card-header">
        <h3 class="card-title">
            {{ $r['exam']->name }}
            <span class="badge badge-info">GPA: {{ number_format($r['overall']['gpa'], 2) }}</span>
            <span class="badge badge-{{ $r['overall']['failed'] ? 'danger' : 'success' }}">
                {{ $r['overall']['failed'] ? 'ফেল' : 'পাস' }}
            </span>
        </h3>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered">
            <thead class="thead-light"><tr><th>#</th><th>বিষয়</th><th class="text-center">পূর্ণ</th><th class="text-center">প্রাপ্ত</th><th class="text-center">গ্রেড</th><th class="text-center">GPA</th></tr></thead>
            <tbody>
                @foreach($r['subjects'] as $i => $s)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $s['subject'] }}</td>
                    <td class="text-center">{{ $s['full_marks'] }}</td>
                    <td class="text-center"><b>{{ $s['marks'] }}</b></td>
                    <td class="text-center">{{ $s['grade'] }}</td>
                    <td class="text-center">{{ number_format($s['gpa'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@empty
<div class="alert alert-warning">এখনো কোনো প্রকাশিত ফলাফল নেই।</div>
@endforelse
@endsection
