@extends('student.app')
@section('page-title', 'আমার প্রোফাইল')

@section('content')
<div class="card card-primary">
    <div class="card-body">
        <table class="table table-bordered">
            <tr><th width="30%">নাম</th><td>{{ $student->name }}</td></tr>
            <tr><th>স্টুডেন্ট আইডি</th><td>{{ $student->student_id }}</td></tr>
            <tr><th>শ্রেণি</th><td>{{ $student->schoolClass->name ?? '-' }} @if($student->section) ({{ $student->section->name }}) @endif</td></tr>
            <tr><th>রোল</th><td>{{ $student->roll_number ?? '-' }}</td></tr>
            <tr><th>পিতা</th><td>{{ $student->father_name }}</td></tr>
            <tr><th>মাতা</th><td>{{ $student->mother_name }}</td></tr>
            <tr><th>জন্ম তারিখ</th><td>{{ $student->date_of_birth?->format('d M, Y') }}</td></tr>
            <tr><th>লিঙ্গ</th><td>{{ $student->gender }}</td></tr>
            <tr><th>ধর্ম</th><td>{{ $student->religion ?? '-' }}</td></tr>
            <tr><th>রক্তের গ্রুপ</th><td>{{ $student->blood_group ?? '-' }}</td></tr>
            <tr><th>ঠিকানা</th><td>{{ $student->present_address ?? '-' }}</td></tr>
        </table>
    </div>
</div>
@endsection
