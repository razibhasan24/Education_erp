@extends('admin.app')

@section('title', 'শিক্ষার্থী তালিকা')
@section('page-title', 'শিক্ষার্থী তালিকা')

@section('page-actions')
    <a href="{{ route('admin.students.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus"></i> নতুন ভর্তি
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <form method="GET" class="form-inline">
            <select name="class_id" class="form-control form-control-sm mr-2 select2">
                <option value="">সব শ্রেণি</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}" @selected(request('class_id') == $class->id)>{{ $class->name }}</option>
                @endforeach
            </select>
            <select name="status" class="form-control form-control-sm mr-2">
                <option value="">সব স্ট্যাটাস</option>
                <option value="active" @selected(request('status')=='active')>সক্রিয়</option>
                <option value="inactive" @selected(request('status')=='inactive')>নিষ্ক্রিয়</option>
                <option value="passed" @selected(request('status')=='passed')>উত্তীর্ণ</option>
            </select>
            <button class="btn btn-sm btn-info"><i class="fas fa-search"></i> ফিল্টার</button>
            <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-secondary ml-1">রিসেট</a>
        </form>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-hover datatable">
            <thead>
                <tr>
                    <th>ছবি</th>
                    <th>স্টুডেন্ট আইডি</th>
                    <th>নাম</th>
                    <th>শ্রেণি / শাখা</th>
                    <th>পিতা</th>
                    <th>মোবাইল</th>
                    <th>স্ট্যাটাস</th>
                    <th>অ্যাকশন</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                <tr>
                    <td>
                        @if($student->photo)
                            <img src="{{ asset('storage/'.$student->photo) }}" width="40" height="40" class="rounded-circle">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}" width="40" class="rounded-circle">
                        @endif
                    </td>
                    <td><b>{{ $student->student_id }}</b></td>
                    <td>{{ $student->name }}<br><small class="text-muted">{{ $student->name_bn }}</small></td>
                    <td>{{ $student->schoolClass->name ?? '-' }} / {{ $student->section->name ?? '-' }}</td>
                    <td>{{ $student->father_name }}</td>
                    <td>{{ $student->father_phone }}</td>
                    <td>
                        @if($student->status == 'active')
                            <span class="badge badge-success">সক্রিয়</span>
                        @else
                            <span class="badge badge-secondary">{{ $student->status }}</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.students.show', $student) }}" class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.students.destroy', $student) }}" method="POST" class="d-inline" onsubmit="return confirm('নিশ্চিত?')">
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
