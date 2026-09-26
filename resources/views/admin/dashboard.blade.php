@extends('admin.app')

@section('title', 'অ্যাডমিন ড্যাশবোর্ড')
@section('page-title', 'অ্যাডমিন ড্যাশবোর্ড')

@section('content')

    {{-- ===== Stat Cards ===== --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['total_students'] }}</h3>
                    <p>মোট শিক্ষার্থী</p>
                </div>
                <div class="icon"><i class="fas fa-user-graduate"></i></div>
                <a href="{{ route('admin.students.index') }}" class="small-box-footer">বিস্তারিত <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['total_teachers'] }}</h3>
                    <p>মোট শিক্ষক</p>
                </div>
                <div class="icon"><i class="fas fa-chalkboard-teacher"></i></div>
                <a href="{{ route('admin.teachers.index') }}" class="small-box-footer">বিস্তারিত <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['total_classes'] }}</h3>
                    <p>মোট শ্রেণি</p>
                </div>
                <div class="icon"><i class="fas fa-layer-group"></i></div>
                <a href="{{ route('admin.academic.classes') }}" class="small-box-footer">বিস্তারিত <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $stats['total_subjects'] }}</h3>
                    <p>মোট বিষয়</p>
                </div>
                <div class="icon"><i class="fas fa-book"></i></div>
                <a href="{{ route('admin.academic.subjects') }}" class="small-box-footer">বিস্তারিত <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    {{-- ===== Financial + Attendance Cards ===== --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-money-bill-wave"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">এই মাসের আদায়</span>
                    <span class="info-box-number">৳ {{ number_format($stats['fee_collected_month'], 0) }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="info-box">
                <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-triangle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">মোট বকেয়া</span>
                    <span class="info-box-number">৳ {{ number_format($stats['fee_due_total'], 0) }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-user-check"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">আজ উপস্থিত</span>
                    <span class="info-box-number">{{ $stats['present_today'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-user-times"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">আজ অনুপস্থিত</span>
                    <span class="info-box-number">{{ $stats['absent_today'] }}</span>
                </div>
            </div>
        </div>
    </div>

    @if (!$todayAttendanceTaken)
        <div class="alert alert-warning">
            <i class="fas fa-bell"></i> <b>আজকের হাজিরা এখনো নেওয়া হয়নি!</b>
            <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-warning float-right">
                <i class="fas fa-arrow-right"></i> হাজিরা নিন
            </a>
        </div>
    @endif

    {{-- ===== Charts ===== --}}
    <div class="row">
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-line"></i> শেষ ১৪ দিনের হাজিরা</h3>
                </div>
                <div class="card-body">
                    <canvas id="attendanceChart" height="90"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-pie"></i> গ্রেড বিতরণ</h3>
                </div>
                <div class="card-body">
                    <canvas id="gradeChart" height="180"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-bar"></i> শ্রেণিভিত্তিক শিক্ষার্থী</h3>
                </div>
                <div class="card-body"><canvas id="classChart" height="130"></canvas></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-area"></i> মাসিক ফি আদায়</h3>
                </div>
                <div class="card-body"><canvas id="feeChart" height="130"></canvas></div>
            </div>
        </div>
    </div>

    {{-- ===== Recent Activities ===== --}}
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user-plus"></i> সাম্প্রতিক ভর্তি</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-striped mb-0">
                        <tbody>
                            @forelse($recentStudents as $s)
                                <tr>
                                    <td><img src="{{ $s->photo ? asset('storage/' . $s->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($s->name) }}"
                                            width="30" class="rounded-circle"></td>
                                    <td><a href="{{ route('admin.students.show', $s) }}">{{ $s->name }}</a></td>
                                    <td>{{ $s->schoolClass->name ?? '-' }}</td>
                                    <td class="text-right text-muted"><small>{{ $s->created_at->diffForHumans() }}</small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">কোনো ডেটা নেই</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-money-bill"></i> সাম্প্রতিক পেমেন্ট</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-striped mb-0">
                        <tbody>
                            @forelse($recentPayments as $p)
                                <tr>
                                    <td>{{ $p->student->name ?? '-' }}</td>
                                    <td><small>{{ $p->receipt_no }}</small></td>
                                    <td class="text-right"><b>৳ {{ number_format($p->amount, 2) }}</b></td>
                                    <td class="text-right text-muted"><small>{{ $p->created_at->diffForHumans() }}</small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">কোনো ডেটা নেই</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        fetch('{{ route('admin.analytics.dashboard') }}')
            .then(r => r.json())
            .then(data => {
                // হাজিরা লাইন চার্ট
                new Chart(document.getElementById('attendanceChart'), {
                    type: 'line',
                    data: {
                        labels: data.attendance_trend.labels,
                        datasets: [{
                                label: 'উপস্থিত',
                                data: data.attendance_trend.present,
                                borderColor: '#28a745',
                                backgroundColor: 'rgba(40,167,69,0.1)',
                                fill: true,
                                tension: 0.3
                            },
                            {
                                label: 'অনুপস্থিত',
                                data: data.attendance_trend.absent,
                                borderColor: '#dc3545',
                                backgroundColor: 'rgba(220,53,69,0.1)',
                                fill: true,
                                tension: 0.3
                            },
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top'
                            }
                        }
                    }
                });

                // গ্রেড ডো nut
                new Chart(document.getElementById('gradeChart'), {
                    type: 'doughnut',
                    data: {
                        labels: data.grade_distribution.labels,
                        datasets: [{
                            data: data.grade_distribution.counts,
                            backgroundColor: ['#28a745', '#20c997', '#17a2b8', '#007bff', '#ffc107',
                                '#fd7e14', '#dc3545'
                            ],
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right'
                            }
                        }
                    }
                });

                // ক্লাস বার চার্ট
                new Chart(document.getElementById('classChart'), {
                    type: 'bar',
                    data: {
                        labels: data.students_by_class.map(i => i.label),
                        datasets: [{
                            label: 'শিক্ষার্থী',
                            data: data.students_by_class.map(i => i.count),
                            backgroundColor: '#17a2b8',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });

                // ফি এরিয়া চার্ট
                new Chart(document.getElementById('feeChart'), {
                    type: 'line',
                    data: {
                        labels: data.fee_collection.labels,
                        datasets: [{
                            label: 'আদায় (৳)',
                            data: data.fee_collection.amounts,
                            borderColor: '#ffc107',
                            backgroundColor: 'rgba(255,193,7,0.2)',
                            fill: true,
                            tension: 0.3,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            })
            .catch(err => console.error('Analytics loading failed:', err));
    </script>
@endpush
