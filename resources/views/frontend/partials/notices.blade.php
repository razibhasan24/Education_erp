@extends('frontend.partials.layout')
@section('title', 'নোটিশ বোর্ড')

@section('content')
<div class="bg-primary text-white py-5">
    <div class="container"><h1><i class="fas fa-bullhorn"></i> নোটিশ বোর্ড</h1></div>
</div>
<div class="container my-5">
    @forelse($notices as $notice)
        <div class="notice-card">
            <div class="d-flex justify-content-between">
                <h5><a href="{{ route('frontend.notice.show', $notice) }}" class="text-decoration-none">{{ $notice->title }}</a></h5>
                <span class="badge bg-{{ \App\Models\Notice::priorityLabels()[$notice->priority]['color'] }}">
                    {{ \App\Models\Notice::priorityLabels()[$notice->priority]['label'] }}
                </span>
            </div>
            <p class="text-muted small mb-1"><i class="fas fa-calendar"></i> {{ $notice->publish_date->format('d M, Y') }}</p>
            <p class="mb-0">{{ Str::limit(strip_tags($notice->content), 200) }}</p>
            <a href="{{ route('frontend.notice.show', $notice) }}" class="btn btn-sm btn-outline-primary mt-2">বিস্তারিত পড়ুন</a>
        </div>
    @empty
        <p class="text-center text-muted py-5">কোনো নোটিশ নেই।</p>
    @endforelse
    <div class="mt-4">{{ $notices->links() }}</div>
</div>
@endsection
