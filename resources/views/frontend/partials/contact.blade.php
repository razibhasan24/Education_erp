@extends('frontend.partials.layout')
@section('title', 'যোগাযোগ')

@section('content')
<div class="bg-primary text-white py-5">
    <div class="container"><h1><i class="fas fa-envelope"></i> যোগাযোগ করুন</h1></div>
</div>
<div class="container my-5">
    <div class="row">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>{{ $settings->institute_name ?? '-' }}</h5>
                    <p><i class="fas fa-map-marker-alt text-danger"></i> {{ $settings->address ?? '-' }}</p>
                    @if($settings && $settings->phone)<p><i class="fas fa-phone text-success"></i> {{ $settings->phone }}</p>@endif
                    @if($settings && $settings->email)<p><i class="fas fa-envelope text-info"></i> {{ $settings->email }}</p>@endif
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <form action="{{ route('frontend.contact.submit') }}" method="POST" class="card shadow-sm">
                @csrf
                <div class="card-body">
                    <h5 class="card-title mb-3">বার্তা পাঠান</h5>
                    <div class="mb-3"><label>নাম *</label><input type="text" name="name" class="form-control" required></div>
                    <div class="mb-3"><label>ইমেইল *</label><input type="email" name="email" class="form-control" required></div>
                    <div class="mb-3"><label>মোবাইল</label><input type="text" name="phone" class="form-control"></div>
                    <div class="mb-3"><label>বিষয় *</label><input type="text" name="subject" class="form-control" required></div>
                    <div class="mb-3"><label>বার্তা *</label><textarea name="message" class="form-control" rows="5" required></textarea></div>
                    <button class="btn btn-primary"><i class="fas fa-paper-plane"></i> পাঠান</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
