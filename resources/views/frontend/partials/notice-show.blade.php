@extends('frontend.partials.layout')
@section('title', $notice->title)

@section('content')
<div class="bg-primary text-white py-5">
    <div class="container">
        <h1>{{ $notice->title }}</h1>
        <p class="mb-0">
            <i class="fas fa-calendar"></i> {{ $notice->publish_date->format('d F, Y') }}
            <span class="badge bg-{{ \App\Models\Notice::priorityLabels()[$notice->priority]['color'] }} ms-2">
                {{ \App\Models\Notice::priorityLabels()[$notice->priority]['label'] }}
            </span>
        </p>
    </div>
</div>
<div class="container my-5">
    <div class="row">
        <div class="col-md-9">
            <div class="card shadow-sm">
                <div class="card-body">
                    {!! nl2br(e($notice->content)) !!}
                    @if($notice->attachment)
                        <hr>
                        <a href="{{ asset('storage/'.$notice->attachment) }}" target="_blank" class="btn btn-primary">
                            <i class="fas fa-paperclip"></i> সংযুক্তি ডাউনলোড
                        </a>
                    @endif
                </div>
            </div>
            <a href="{{ route('frontend.notices') }}" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left"></i> ফিরে যান</a>
        </div>
    </div>
</div>
@endsection
