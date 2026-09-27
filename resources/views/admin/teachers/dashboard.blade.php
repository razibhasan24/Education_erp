@extends('teacher.app')
@section('page-title', 'শিক্ষক ড্যাশবোর্ড')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card card-primary card-outline">
            <div class="card-body box-profile text-center">
                <img class="profile-user-img img-fluid img-circle"
                     src="{{ $teacher->photo ? asset('storage/'.$teacher->photo) : 'https://ui-avatars.com/api/?name='.urlencode($teacher->name).'&size=120' }}"
                     style="width:110px;height:110px;">
                <h4 class="mt-2">{{ $teacher->name }}</h4>
                <p class="text-muted">{{ $teacher->teacher_id }} | {{ $teacher->designation ?? '' }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="row">
            <div class="col-md-4"><div class="small-box bg-info"><div class="inner"><h3>{{ $stats['assigned_classes'] }}</h3><p>আমার ক্লাস</p></div><div class="icon"><i class="fas fa-layer-group"></i></div></div></div>
            <div class="col-md-4"><div class="small-box bg-success"><div class="inner"><h3>{{ $stats['assigned_subjects'] }}</h3><p>আমার বিষয়</p></div><div class="icon"><i class="fas fa-book"></i></div></div></div>
            <div class="col-md-4"><div class="small-box bg-warning"><div class="inner"><h3>{{ $stats['today_attendance'] }}</h3><p>আজকের হাজিরা</p></div><div class="icon"><i class="fas fa-user-check"></i></div></div></div>
        </div>

        <div class="card card-success">
            <div class="card-header"><h3 class="card-title">আমার ক্লাস ও বিষয়</h3></div>
            <div class="card-body table-responsive">
                <table class="table table-sm">
                    <thead><tr><th>ক্লাস</th><th>শাখা</th><th>বিষয়</th></tr></thead>
                    <tbody>
                        @forelse($assignments as $a)
                        <tr>
                            <td>{{ $a->schoolClass->name ?? '-' }}</td>
                            <td>{{ $a->section->name ?? 'সব' }}</td>
                            <td>{{ $a->subject->name ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted">কোনো অ্যাসাইনমেন্ট নেই</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <a href="{{ route('teacher.attendance') }}" class="btn btn-info btn-block btn-lg">
                    <i class="fas fa-clipboard-check"></i> হাজিরা নিন
                </a>
            </div>
            <div class="col-md-6">
                <a href="{{ route('teacher.marks') }}" class="btn btn-success btn-block btn-lg">
                    <i class="fas fa-pen"></i> মার্ক এন্ট্রি করুন
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
