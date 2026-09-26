@extends('student.app')
@section('page-title', 'অভিভাবক ড্যাশবোর্ড')

@section('content')
<div class="alert alert-info">
    <i class="fas fa-user-friends"></i> স্বাগতম, <b>{{ $guardian->name }}</b>! আপনার সন্তানের তথ্য নিচে দেখুন।
</div>

<div class="row">
    @foreach($children as $child)
    <div class="col-md-6">
        <div class="card card-primary card-outline">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <img src="{{ $child->photo ? asset('storage/'.$child->photo) : 'https://ui-avatars.com/api/?name='.urlencode($child->name).'&size=120' }}"
                             class="rounded-circle" style="width:100px;height:100px;">
                    </div>
                    <div class="col-md-8">
                        <h4>{{ $child->name }}</h4>
                        <p class="text-muted mb-1">{{ $child->student_id }}</p>
                        <p><b>শ্রেণি:</b> {{ $child->schoolClass->name ?? '-' }} @if($child->section) ({{ $child->section->name }}) @endif</p>
                        <p><b>রোল:</b> {{ $child->roll_number ?? '-' }}</p>
                        <a href="{{ route('guardian.child', $child->id) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> বিস্তারিত দেখুন
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
