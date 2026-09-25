@extends('admin.app')

@section('title', $student->name . ' — প্রোফাইল')
@section('page-title', 'শিক্ষার্থীর প্রোফাইল')

@section('page-actions')
    <a href="{{ route('admin.attendance.students.student-report', $student) }}" class="btn btn-info btn-sm">
        <i class="fas fa-clipboard-check"></i> হাজিরা
    </a>
    <a href="{{ route('admin.fees.reports.student-ledger', $student) }}" class="btn btn-success btn-sm">
        <i class="fas fa-money-bill"></i> ফি লেজার
    </a>
    <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-warning btn-sm">
        <i class="fas fa-edit"></i> এডিট
    </a>
    <a href="{{ route('admin.students.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left"></i> ফিরে যান
    </a>
@endsection

@php
    // হাজিরা সামারি (এই মাসের)
    $currentMonth = now()->month;
    $currentYear = now()->year;
    $attendanceQuery = \App\Models\StudentAttendance::where('student_id', $student->id)
        ->whereMonth('attendance_date', $currentMonth)
        ->whereYear('attendance_date', $currentYear);
    $attendanceSummary = [
        'present' => (clone $attendanceQuery)->where('status', 'present')->count(),
        'absent'  => (clone $attendanceQuery)->where('status', 'absent')->count(),
        'late'    => (clone $attendanceQuery)->where('status', 'late')->count(),
        'leave'   => (clone $attendanceQuery)->where('status', 'leave')->count(),
    ];
    $totalDays = array_sum($attendanceSummary);
    $attendanceRate = $totalDays > 0 ? round(($attendanceSummary['present'] / $totalDays) * 100, 1) : 0;

    // ফি সামারি
    $feeInvoices = \App\Models\FeeInvoice::where('student_id', $student->id)->get();
    $feeSummary = [
        'billed' => $feeInvoices->sum('total_amount'),
        'paid'   => $feeInvoices->sum('paid_amount'),
        'due'    => $feeInvoices->sum('due_amount'),
        'count'  => $feeInvoices->count(),
    ];

    // শেষ পরীক্ষার রেজাল্ট (সর্বশেষ published exam)
    $lastExam = \App\Models\Exam::where('is_published', true)
        ->whereHas('marks', fn($q) => $q->where('student_id', $student->id))
        ->latest()
        ->first();

    $lastExamResult = null;
    if ($lastExam) {
        $examSubjects = \App\Models\ExamSubject::with('subject')
            ->where('exam_id', $lastExam->id)
            ->where('class_id', $student->class_id)
            ->get();

        $studentMarks = \App\Models\Mark::where('exam_id', $lastExam->id)
            ->where('student_id', $student->id)
            ->get()
            ->keyBy('exam_subject_id');

        $subjects = [];
        foreach ($examSubjects as $es) {
            $m = $studentMarks->get($es->id);
            $subjects[] = [
                'subject' => $es->subject->name ?? '-',
                'marks' => $m?->total_marks ?? 0,
                'full_marks' => $es->full_marks,
                'grade' => $m?->grade ?? '-',
                'gpa' => $m?->gpa ?? 0,
            ];
        }

        $lastExamResult = [
            'exam' => $lastExam,
            'subjects' => $subjects,
            'overall' => \App\Helpers\GradeHelper::calculateGPA(array_map(fn($r) => [
                'marks' => $r['marks'],
                'full_marks' => $r['full_marks'],
                'gpa' => $r['gpa'],
            ], $subjects)),
        ];
    }
@endphp

@section('content')

{{-- ========== প্রোফাইল হেডার কার্ড ========== --}}
<div class="row">
    <div class="col-md-4">
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    <img class="profile-user-img img-fluid img-circle"
                         src="{{ $student->photo ? asset('storage/'.$student->photo) : 'https://ui-avatars.com/api/?name='.urlencode($student->name).'&size=200&background=007bff&color=fff' }}"
                         style="width:130px;height:130px;object-fit:cover;border:3px solid #007bff;">
                </div>

                <h3 class="profile-username text-center mt-3">{{ $student->name }}</h3>
                @if($student->name_bn)
                    <p class="text-center text-muted mb-1">{{ $student->name_bn }}</p>
                @endif
                <p class="text-center text-muted">
                    <i class="fas fa-id-card"></i> {{ $student->student_id }}
                </p>

                <p class="text-center">
                    @if($student->status == 'active')
                        <span class="badge badge-success px-3 py-2"><i class="fas fa-check-circle"></i> সক্রিয়</span>
                    @elseif($student->status == 'inactive')
                        <span class="badge badge-secondary px-3 py-2"><i class="fas fa-pause-circle"></i> নিষ্ক্রিয়</span>
                    @elseif($student->status == 'passed')
                        <span class="badge badge-info px-3 py-2"><i class="fas fa-graduation-cap"></i> উত্তীর্ণ</span>
                    @elseif($student->status == 'transferred')
                        <span class="badge badge-warning px-3 py-2"><i class="fas fa-exchange-alt"></i> স্থানান্তর</span>
                    @else
                        <span class="badge badge-danger px-3 py-2">{{ $student->status }}</span>
                    @endif

                    @if($student->admission_type == 'online')
                        <span class="badge badge-primary px-3 py-2"><i class="fas fa-globe"></i> অনলাইন</span>
                    @else
                        <span class="badge badge-dark px-3 py-2"><i class="fas fa-building"></i> অফলাইন</span>
                    @endif
                </p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <i class="fas fa-layer-group text-primary"></i> <b>শ্রেণি</b>
                        <span class="float-right">{{ $student->schoolClass->name ?? '-' }}</span>
                    </li>
                    <li class="list-group-item">
                        <i class="fas fa-columns text-info"></i> <b>শাখা</b>
                        <span class="float-right">{{ $student->section->name ?? '-' }}</span>
                    </li>
                    <li class="list-group-item">
                        <i class="fas fa-users text-warning"></i> <b>বিভাগ</b>
                        <span class="float-right">{{ $student->group->name ?? '-' }}</span>
                    </li>
                    <li class="list-group-item">
                        <i class="fas fa-sort-numeric-up text-success"></i> <b>রোল</b>
                        <span class="float-right">{{ $student->roll_number ?? '-' }}</span>
                    </li>
                    <li class="list-group-item">
                        <i class="fas fa-calendar-alt text-danger"></i> <b>শিক্ষাবর্ষ</b>
                        <span class="float-right">{{ $student->academicYear->name ?? '-' }}</span>
                    </li>
                </ul>

                <div class="row">
                    <div class="col-6">
                        <a href="{{ route('admin.attendance.students.student-report', $student) }}" class="btn btn-info btn-block btn-sm">
                            <i class="fas fa-clipboard-check"></i> হাজিরা
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('admin.fees.reports.student-ledger', $student) }}" class="btn btn-success btn-block btn-sm">
                            <i class="fas fa-money-bill"></i> ফি লেজার
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        {{-- ========== কুইক সামারি কার্ড ========== --}}
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $attendanceSummary['present'] }}</h3>
                        <p>উপস্থিত (এই মাস)</p>
                    </div>
                    <div class="icon"><i class="fas fa-user-check"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $attendanceSummary['absent'] }}</h3>
                        <p>অনুপস্থিত (এই মাস)</p>
                    </div>
                    <div class="icon"><i class="fas fa-user-times"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>৳{{ number_format($feeSummary['due'], 0) }}</h3>
                        <p>মোট বকেয়া</p>
                    </div>
                    <div class="icon"><i class="fas fa-exclamation-circle"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $attendanceRate }}%</h3>
                        <p>উপস্থিতির হার</p>
                    </div>
                    <div class="icon"><i class="fas fa-chart-line"></i></div>
                </div>
            </div>
        </div>

        {{-- ========== ট্যাব সেকশন ========== --}}
        <div class="card card-primary card-tabs">
            <div class="card-header p-0 pt-1">
                <ul class="nav nav-tabs" id="studentTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="tab-personal" data-toggle="pill" href="#personal" role="tab">
                            <i class="fas fa-user"></i> ব্যক্তিগত
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-academic" data-toggle="pill" href="#academic" role="tab">
                            <i class="fas fa-graduation-cap"></i> একাডেমিক
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-attendance" data-toggle="pill" href="#attendance" role="tab">
                            <i class="fas fa-clipboard-check"></i> হাজিরা
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-fee" data-toggle="pill" href="#fee" role="tab">
                            <i class="fas fa-money-bill"></i> ফি
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-result" data-toggle="pill" href="#result" role="tab">
                            <i class="fas fa-chart-bar"></i> ফলাফল
                        </a>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content">

                    {{-- ===== ব্যক্তিগত তথ্য ===== --}}
                    <div class="tab-pane fade show active" id="personal" role="tabpanel">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th width="30%"><i class="fas fa-user text-primary"></i> পূর্ণ নাম</th>
                                    <td>{{ $student->name }}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-language text-primary"></i> নাম (বাংলা)</th>
                                    <td>{{ $student->name_bn ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-male text-primary"></i> পিতার নাম</th>
                                    <td>{{ $student->father_name }}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-female text-primary"></i> মাতার নাম</th>
                                    <td>{{ $student->mother_name }}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-briefcase text-primary"></i> পিতার পেশা</th>
                                    <td>{{ $student->father_occupation ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-briefcase text-primary"></i> মাতার পেশা</th>
                                    <td>{{ $student->mother_occupation ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-phone text-primary"></i> পিতার মোবাইল</th>
                                    <td>{{ $student->father_phone ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-phone text-primary"></i> মাতার মোবাইল</th>
                                    <td>{{ $student->mother_phone ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-user-shield text-primary"></i> অভিভাবকের নাম</th>
                                    <td>{{ $student->guardian_name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-phone-square text-primary"></i> অভিভাবকের মোবাইল</th>
                                    <td>{{ $student->guardian_phone ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-calendar text-primary"></i> জন্ম তারিখ</th>
                                    <td>{{ $student->date_of_birth?->format('d F, Y') }} ({{ $student->date_of_birth?->age }} বছর)</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-venus-mars text-primary"></i> লিঙ্গ</th>
                                    <td>
                                        @if($student->gender == 'male') পুরুষ
                                        @elseif($student->gender == 'female') মহিলা
                                        @else অন্যান্য @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-pray text-primary"></i> ধর্ম</th>
                                    <td>{{ $student->religion ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-tint text-primary"></i> রক্তের গ্রুপ</th>
                                    <td>
                                        @if($student->blood_group)
                                            <span class="badge badge-danger">{{ $student->blood_group }}</span>
                                        @else - @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-flag text-primary"></i> জাতীয়তা</th>
                                    <td>{{ $student->nationality ?? 'Bangladeshi' }}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-id-card-alt text-primary"></i> NID / জন্ম নিবন্ধন</th>
                                    <td>{{ $student->nid_or_birth_certificate ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-map-marker-alt text-primary"></i> বর্তমান ঠিকানা</th>
                                    <td>{{ $student->present_address ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-home text-primary"></i> স্থায়ী ঠিকানা</th>
                                    <td>{{ $student->permanent_address ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- ===== একাডেমিক তথ্য ===== --}}
                    <div class="tab-pane fade" id="academic" role="tabpanel">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th width="30%"><i class="fas fa-id-badge text-info"></i> স্টুডেন্ট আইডি</th>
                                    <td><b>{{ $student->student_id }}</b></td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-layer-group text-info"></i> শ্রেণি</th>
                                    <td>{{ $student->schoolClass->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-columns text-info"></i> শাখা</th>
                                    <td>{{ $student->section->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-users text-info"></i> বিভাগ</th>
                                    <td>{{ $student->group->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-sort-numeric-up text-info"></i> রোল নম্বর</th>
                                    <td>{{ $student->roll_number ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-calendar-alt text-info"></i> শিক্ষাবর্ষ</th>
                                    <td>{{ $student->academicYear->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-sign-in-alt text-info"></i> ভর্তির তারিখ</th>
                                    <td>{{ $student->admission_date?->format('d F, Y') }}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-globe text-info"></i> ভর্তির ধরন</th>
                                    <td>
                                        @if($student->admission_type == 'online')
                                            <span class="badge badge-primary"><i class="fas fa-globe"></i> অনলাইন</span>
                                        @else
                                            <span class="badge badge-dark"><i class="fas fa-building"></i> অফলাইন</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-info-circle text-info"></i> বর্তমান স্ট্যাটাস</th>
                                    <td>
                                        @if($student->status == 'active')
                                            <span class="badge badge-success">সক্রিয়</span>
                                        @else
                                            <span class="badge badge-secondary">{{ $student->status }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @if($student->user)
                                <tr>
                                    <th><i class="fas fa-envelope text-info"></i> লগইন ইমেইল</th>
                                    <td>{{ $student->user->email }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    {{-- ===== হাজিরা ===== --}}
                    <div class="tab-pane fade" id="attendance" role="tabpanel">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <b>{{ now()->format('F Y') }}</b> মাসের হাজিরা সামারি
                        </div>

                        <div class="row text-center mb-3">
                            <div class="col-md-3 col-6 mb-2">
                                <div class="info-box bg-success">
                                    <span class="info-box-icon"><i class="fas fa-check"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">উপস্থিত</span>
                                        <span class="info-box-number">{{ $attendanceSummary['present'] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6 mb-2">
                                <div class="info-box bg-danger">
                                    <span class="info-box-icon"><i class="fas fa-times"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">অনুপস্থিত</span>
                                        <span class="info-box-number">{{ $attendanceSummary['absent'] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6 mb-2">
                                <div class="info-box bg-warning">
                                    <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">বিলম্ব</span>
                                        <span class="info-box-number">{{ $attendanceSummary['late'] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6 mb-2">
                                <div class="info-box bg-info">
                                    <span class="info-box-icon"><i class="fas fa-plane"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">ছুটি</span>
                                        <span class="info-box-number">{{ $attendanceSummary['leave'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="progress mb-3" style="height:25px;">
                            <div class="progress-bar bg-success" style="width: {{ $attendanceRate }}%">
                                <b>{{ $attendanceRate }}% উপস্থিত</b>
                            </div>
                        </div>

                        <a href="{{ route('admin.attendance.students.student-report', $student) }}"
                           class="btn btn-info btn-block">
                            <i class="fas fa-chart-line"></i> সম্পূর্ণ হাজিরা রিপোর্ট দেখুন
                        </a>
                    </div>

                    {{-- ===== ফি ===== --}}
                    <div class="tab-pane fade" id="fee" role="tabpanel">
                        <div class="row text-center mb-3">
                            <div class="col-md-3 col-6 mb-2">
                                <div class="info-box bg-info">
                                    <span class="info-box-icon"><i class="fas fa-file-invoice"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">মোট বিল</span>
                                        <span class="info-box-number">৳{{ number_format($feeSummary['billed'], 0) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6 mb-2">
                                <div class="info-box bg-success">
                                    <span class="info-box-icon"><i class="fas fa-check"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">পরিশোধিত</span>
                                        <span class="info-box-number">৳{{ number_format($feeSummary['paid'], 0) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6 mb-2">
                                <div class="info-box bg-danger">
                                    <span class="info-box-icon"><i class="fas fa-exclamation"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">বকেয়া</span>
                                        <span class="info-box-number">৳{{ number_format($feeSummary['due'], 0) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6 mb-2">
                                <div class="info-box bg-primary">
                                    <span class="info-box-icon"><i class="fas fa-list"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">ইনভয়েস</span>
                                        <span class="info-box-number">{{ $feeSummary['count'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('admin.fees.reports.student-ledger', $student) }}"
                           class="btn btn-success btn-block">
                            <i class="fas fa-money-bill-wave"></i> সম্পূর্ণ ফি লেজার দেখুন
                        </a>
                    </div>

                    {{-- ===== ফলাফল ===== --}}
                    <div class="tab-pane fade" id="result" role="tabpanel">
                        @if($lastExamResult)
                            <div class="alert alert-success">
                                <i class="fas fa-trophy"></i>
                                <b>{{ $lastExamResult['exam']->name }}</b> —
                                GPA: <b>{{ number_format($lastExamResult['overall']['gpa'], 2) }}</b>,
                                গ্রেড: <b>{{ $lastExamResult['overall']['grade'] }}</b>
                            </div>

                            <table class="table table-bordered table-striped">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>বিষয়</th>
                                        <th class="text-center">পূর্ণ</th>
                                        <th class="text-center">প্রাপ্ত</th>
                                        <th class="text-center">গ্রেড</th>
                                        <th class="text-center">GPA</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lastExamResult['subjects'] as $i => $s)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $s['subject'] }}</td>
                                        <td class="text-center">{{ $s['full_marks'] }}</td>
                                        <td class="text-center"><b>{{ $s['marks'] }}</b></td>
                                        <td class="text-center"><span class="badge badge-info">{{ $s['grade'] }}</span></td>
                                        <td class="text-center">{{ number_format($s['gpa'], 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-light">
                                    <tr>
                                        <th colspan="2" class="text-right">মোট</th>
                                        <th class="text-center">{{ $lastExamResult['overall']['total_full_marks'] }}</th>
                                        <th class="text-center">{{ $lastExamResult['overall']['total_marks'] }}</th>
                                        <th class="text-center">{{ $lastExamResult['overall']['grade'] }}</th>
                                        <th class="text-center">{{ number_format($lastExamResult['overall']['gpa'], 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>

                            <a href="{{ route('admin.exams.marksheet', [$lastExamResult['exam'], $student]) }}"
                               class="btn btn-info btn-block">
                                <i class="fas fa-file-alt"></i> সম্পূর্ণ মার্কশিট দেখুন
                            </a>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                এখনো কোনো পরীক্ষার ফলাফল প্রকাশিত হয়নি।
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection
