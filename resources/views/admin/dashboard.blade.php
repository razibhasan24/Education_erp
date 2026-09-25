@extends('admin.app')

@section('title', 'অ্যাডমিন ড্যাশবোর্ড')
@section('page-title', 'অ্যাডমিন ড্যাশবোর্ড')

@section('content')
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ \App\Models\Student::count() }}</h3>
                <p>মোট শিক্ষার্থী</p>
            </div>
            <div class="icon"><i class="fas fa-user-graduate"></i></div>
            <a href="{{ route('admin.students.index') }}" class="small-box-footer">
                বিস্তারিত <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ \App\Models\Teacher::count() }}</h3>
                <p>মোট শিক্ষক</p>
            </div>
            <div class="icon"><i class="fas fa-chalkboard-teacher"></i></div>
            <a href="{{ route('admin.teachers.index') }}" class="small-box-footer">
                বিস্তারিত <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ \App\Models\SchoolClass::count() }}</h3>
                <p>মোট শ্রেণি</p>
            </div>
            <div class="icon"><i class="fas fa-layer-group"></i></div>
            <a href="{{ route('admin.academic.classes') }}" class="small-box-footer">
                বিস্তারিত <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ \App\Models\Subject::count() }}</h3>
                <p>মোট বিষয়</p>
            </div>
            <div class="icon"><i class="fas fa-book"></i></div>
            <a href="{{ route('admin.academic.subjects') }}" class="small-box-footer">
                বিস্তারিত <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-school"></i> প্রতিষ্ঠানের তথ্য</h3>
            </div>
            <div class="card-body">
                <p>ইউনিভার্সাল স্কুল ম্যানেজমেন্ট সিস্টেমে স্বাগতম। বাম দিকের সাইডবার থেকে ম্যানেজমেন্ট শুরু করুন।</p>
            </div>
        </div>
    </div>
</div>
@endsection
