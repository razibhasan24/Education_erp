@extends('admin.app')

@section('title', $student->name)
@section('page-title', 'শিক্ষার্থীর বিস্তারিত তথ্য')

@section('page-actions')
    <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> এডিট</a>
    <a href="{{ route('admin.students.index') }}" class="btn btn-secondary btn-sm">ফিরে যান</a>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    <img class="profile-user-img img-fluid img-circle"
                         src="{{ $student->photo ? asset('storage/'.$student->photo) : 'https://ui-avatars.com/api/?name='.urlencode($student->name).'&size=150' }}"
                         style="width:120px;height:120px;">
                </div>
                <h3 class="profile-username text-center mt-2">{{ $student->name }}</h3>
                <p class="text-muted text-center">{{ $student->student_id }}</p>
                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item"><b>শ্রেণি</b> <span class="float-right">{{ $student->schoolClass->name ?? '-' }}</span></li>
                    <li class="list-group-item"><b>শাখা</b> <span class="float-right">{{ $student->section->name ?? '-' }}</span></li>
                    <li class="list-group-item"><b>বিভাগ</b> <span class="float-right">{{ $student->group->name ?? '-' }}</span></li>
                    <li class="list-group-item"><b>রোল</b> <span class="float-right">{{ $student->roll_number ?? '-' }}</span></li>
                    <li class="list-group-item"><b>শিক্ষাবর্ষ</b> <span class="float-right">{{ $student->academicYear->name ?? '-' }}</span></li>
                    <li class="list-group-item"><b>স্ট্যাটাস</b> <span class="float-right badge badge-success">{{ $student->status }}</span></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h3 class="card-title">বিস্তারিত তথ্য</h3></div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr><th width="30%">পিতার নাম</th><td>{{ $student->father_name }}</td></tr>
                    <tr><th>মাতার নাম</th><td>{{ $student->mother_name }}</td></tr>
                    <tr><th>পিতার মোবাইল</th><td>{{ $student->father_phone ?? '-' }}</td></tr>
                    <tr><th>জন্ম তারিখ</th><td>{{ $student->date_of_birth?->format('d M, Y') }}</td></tr>
                    <tr><th>লিঙ্গ</th><td>{{ $student->gender }}</td></tr>
                    <tr><th>ধর্ম</th><td>{{ $student->religion ?? '-' }}</td></tr>
                    <tr><th>রক্তের গ্রুপ</th><td>{{ $student->blood_group ?? '-' }}</td></tr>
                    <tr><th>NID/জন্ম নিবন্ধন</th><td>{{ $student->nid_or_birth_certificate ?? '-' }}</td></tr>
                    <tr><th>বর্তমান ঠিকানা</th><td>{{ $student->present_address ?? '-' }}</td></tr>
                    <tr><th>স্থায়ী ঠিকানা</th><td>{{ $student->permanent_address ?? '-' }}</td></tr>
                    <tr><th>ভর্তির তারিখ</th><td>{{ $student->admission_date?->format('d M, Y') }}</td></tr>
                    <tr><th>ভর্তির ধরন</th><td><span class="badge badge-info">{{ $student->admission_type }}</span></td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
