@extends('admin.app')
@section('page-title', $guardian->name)

@section('page-actions')
    <a href="{{ route('admin.guardians.edit', $guardian) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> এডিট</a>
    <a href="{{ route('admin.guardians.index') }}" class="btn btn-secondary btn-sm">ফিরে যান</a>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card card-primary card-outline">
            <div class="card-body box-profile text-center">
                <img class="profile-user-img img-fluid img-circle"
                     src="{{ $guardian->photo ? asset('storage/'.$guardian->photo) : 'https://ui-avatars.com/api/?name='.urlencode($guardian->name).'&size=150' }}"
                     style="width:120px;height:120px;">
                <h3 class="profile-username mt-2">{{ $guardian->name }}</h3>
                <p class="text-muted">{{ $guardian->guardian_id }}</p>
                <p><span class="badge badge-info">{{ $guardian->relation ?? 'Guardian' }}</span></p>
                <ul class="list-group list-group-unbordered mt-3">
                    <li class="list-group-item"><b>ফোন</b><span class="float-right">{{ $guardian->phone }}</span></li>
                    <li class="list-group-item"><b>ইমেইল</b><span class="float-right">{{ $guardian->user->email ?? '-' }}</span></li>
                    <li class="list-group-item"><b>পেশা</b><span class="float-right">{{ $guardian->occupation ?? '-' }}</span></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card card-info">
            <div class="card-header"><h3 class="card-title">সন্তানগণ ({{ $guardian->students->count() }})</h3></div>
            <div class="card-body table-responsive">
                <table class="table table-sm">
                    <thead><tr><th>ছবি</th><th>নাম</th><th>স্টুডেন্ট আইডি</th><th>শ্রেণি</th><th>রোল</th><th></th></tr></thead>
                    <tbody>
                        @foreach($guardian->students as $s)
                        <tr>
                            <td><img src="{{ $s->photo ? asset('storage/'.$s->photo) : 'https://ui-avatars.com/api/?name='.urlencode($s->name) }}" width="35" class="rounded-circle"></td>
                            <td>{{ $s->name }}</td>
                            <td>{{ $s->student_id }}</td>
                            <td>{{ $s->schoolClass->name ?? '-' }}</td>
                            <td>{{ $s->roll_number ?? '-' }}</td>
                            <td><a href="{{ route('admin.students.show', $s) }}" class="btn btn-xs btn-info">প্রোফাইল</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
