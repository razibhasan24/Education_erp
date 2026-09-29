@extends('admin.app')
@section('page-title', 'শিক্ষক মূল্যায়ন')

@section('page-actions')
    <a href="{{ route('admin.evaluations.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus"></i> নতুন মূল্যায়ন
    </a>
    <a href="{{ route('admin.evaluations.report') }}" class="btn btn-info btn-sm">
        <i class="fas fa-chart-bar"></i> সম্পূর্ণ রিপোর্ট
    </a>
@endsection

@section('content')
<div class="row">
    @foreach($teachers as $t)
    <div class="col-md-6 col-lg-4">
        <div class="card card-primary card-outline">
            <div class="card-body text-center">
                <img src="{{ $t->photo ? asset('storage/'.$t->photo) : 'https://ui-avatars.com/api/?name='.urlencode($t->name).'&size=100' }}"
                     class="rounded-circle mb-2" width="80" height="80">
                <h4>{{ $t->name }}</h4>
                <p class="text-muted small mb-2">{{ $t->designation ?? '' }}</p>

                @if($t->total_evaluations > 0)
                    <div class="mb-2">
                        <h2 class="text-warning mb-0">
                            {{ number_format($t->avg_rating, 1) }}
                            <small class="text-muted">/ 5</small>
                        </h2>
                        <div class="text-warning">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= round($t->avg_rating))
                                    <i class="fas fa-star"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                        </div>
                    </div>
                    <p class="text-muted small">{{ $t->total_evaluations }}টি মূল্যায়ন</p>
                @else
                    <p class="text-muted">এখনো কোনো মূল্যায়ন নেই</p>
                @endif

                <a href="{{ route('admin.evaluations.show', $t) }}" class="btn btn-sm btn-info">
                    <i class="fas fa-eye"></i> বিস্তারিত
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection