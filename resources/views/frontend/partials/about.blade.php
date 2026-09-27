@extends('frontend.partials.layout')
@section('title', 'আমাদের সম্পর্কে')

@section('content')
<div class="bg-primary text-white py-5">
    <div class="container"><h1>আমাদের সম্পর্কে</h1></div>
</div>
<div class="container my-5">
    <div class="row">
        <div class="col-md-8">
            <h3>{{ $settings->institute_name ?? 'আমাদের প্রতিষ্ঠান' }}</h3>
            <p class="lead">{{ $settings->institute_name_bn ?? '' }}</p>
            <p>আমাদের প্রতিষ্ঠান দীর্ঘদিন ধরে মানসম্মত শিক্ষা প্রদান করে আসছে। আমরা শিক্ষার্থীদের নৈতিক, বৌদ্ধিক ও শারীরিক বিকাশে গুরুত্ব দিই।</p>
            @if($settings && $settings->eiin)<p><b>EIIN:</b> {{ $settings->eiin }}</p>@endif
            @if($settings && $settings->principal_name)<p><b>প্রধান শিক্ষক:</b> {{ $settings->principal_name }}</p>@endif
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>ঠিকানা</h5>
                    <p>{{ $settings->address ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
