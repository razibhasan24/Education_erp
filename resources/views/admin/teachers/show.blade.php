@extends('admin.app')
@section('page-title', $teacher->name)

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card card-primary card-outline">
            <div class="card-body box-profile text-center">
                <img class="profile-user-img img-fluid img-circle"
                     src="{{ $teacher->photo ? asset('storage/'.$teacher->photo) : 'https://ui-avatars.com/api/?name='.urlencode($teacher->name).'&size=150' }}"
                     style="width:120px;height:120px;">
                <h3 class="profile-username mt-2">{{ $teacher->name }}</h3>
                <p class="text-muted">{{ $teacher->teacher_id }} | {{ $teacher->designation }}</p>
                <span class="badge badge-{{ $teacher->status=='active'?'success':'secondary' }}">{{ $teacher->status }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h3 class="card-title">বিস্তারিত</h3></div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr><th width="30%">ইমেইল</th><td>{{ $teacher->email }}</td></tr>
                    <tr><th>মোবাইল</th><td>{{ $teacher->phone }}</td></tr>
                    <tr><th>যোগ্যতা</th><td>{{ $teacher->qualification ?? '-' }}</td></tr>
                    <tr><th>যোগদান</th><td>{{ $teacher->joining_date?->format('d M, Y') }}</td></tr>
                    <tr><th>বেতন</th><td>{{ $teacher->basic_salary ?? '-' }}</td></tr>
                    <tr><th>চাকরির ধরন</th><td>{{ $teacher->employment_type }}</td></tr>
                    <tr><th>ঠিকানা</th><td>{{ $teacher->present_address ?? '-' }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
