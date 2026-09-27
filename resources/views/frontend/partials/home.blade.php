@extends('frontend.partials.layout')
@section('title', $settings->institute_name ?? 'হোম')

@section('content')
<div class="hero">
    <div class="container">
        <h1>{{ $settings->institute_name ?? 'Universal School' }}</h1>
        <p class="lead">{{ $settings->institute_name_bn ?? 'আধুনিক শিক্ষা, উজ্জ্বল ভবিষ্যৎ' }}</p>
        <a href="{{ route('frontend.admission') }}" class="btn btn-primary btn-lg mt-3">
            <i class="fas fa-user-plus"></i> অনলাইনে ভর্তি হোন
        </a>
    </div>
</div>

<div class="container my-5">
    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="stat-box">
                <i class="fas fa-user-graduate"></i>
                <h3>{{ $stats['students'] }}+</h3>
                <p class="text-muted mb-0">শিক্ষার্থী</p>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stat-box">
                <i class="fas fa-chalkboard-teacher"></i>
                <h3>{{ $stats['teachers'] }}+</h3>
                <p class="text-muted mb-0">দক্ষ শিক্ষক</p>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stat-box">
                <i class="fas fa-layer-group"></i>
                <h3>{{ $stats['classes'] }}</h3>
                <p class="text-muted mb-0">শ্রেণি</p>
            </div>
        </div>
    </div>
</div>

<div class="container my-5">
    <div class="row">
        <div class="col-md-8">
            <h3 class="mb-4"><i class="fas fa-bullhorn text-primary"></i> সাম্প্রতিক নোটিশ</h3>
            @forelse($notices as $notice)
                <div class="notice-card">
                    <div class="d-flex justify-content-between">
                        <h5 class="mb-1"><a href="{{ route('frontend.notice.show', $notice) }}" class="text-decoration-none">{{ $notice->title }}</a></h5>
                        <small class="text-muted">{{ $notice->publish_date->format('d M, Y') }}</small>
                    </div>
                    <p class="mb-0 text-muted">{{ Str::limit(strip_tags($notice->content), 120) }}</p>
                </div>
            @empty
                <p class="text-muted">কোনো নোটিশ নেই</p>
            @endforelse
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-info-circle text-primary"></i> যোগাযোগ</h5>
                    <p><i class="fas fa-map-marker-alt text-danger"></i> {{ $settings->address ?? 'ঠিকানা যোগ করুন' }}</p>
                    @if($settings && $settings->phone)<p><i class="fas fa-phone text-success"></i> {{ $settings->phone }}</p>@endif
                    @if($settings && $settings->email)<p><i class="fas fa-envelope text-info"></i> {{ $settings->email }}</p>@endif
                    @if($settings && $settings->principal_name)<p><i class="fas fa-user-tie"></i> অধ্যক্ষ: {{ $settings->principal_name }}</p>@endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
