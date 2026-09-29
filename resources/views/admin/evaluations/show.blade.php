@extends('admin.app')
@section('page-title', $teacher->name . ' — মূল্যায়ন')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card card-primary card-outline">
            <div class="card-body text-center">
                <img src="{{ $teacher->photo ? asset('storage/'.$teacher->photo) : 'https://ui-avatars.com/api/?name='.urlencode($teacher->name).'&size=150' }}"
                     class="rounded-circle mb-2" width="120" height="120">
                <h3>{{ $teacher->name }}</h3>
                <p class="text-muted">{{ $teacher->teacher_id }} | {{ $teacher->designation }}</p>

                @if($summary['count'] > 0)
                    <div class="callout callout-warning">
                        <h1 class="mb-0 text-warning">{{ number_format($summary['overall'], 2) }}</h1>
                        <div class="text-warning">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= round($summary['overall']))
                                    <i class="fas fa-star"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                        </div>
                        <p class="text-muted mb-0">{{ $summary['count'] }}টি মূল্যায়ন</p>
                    </div>

                    <h5 class="mt-3 mb-2">বিভাগভিত্তিক রেটিং</h5>
                    @foreach($summary['breakdown'] as $key => $avg)
                        <div class="text-left mb-2">
                            <small><b>{{ \App\Models\TeacherEvaluation::criteria()[$key] }}</b></small>
                            <div class="progress" style="height:18px;">
                                <div class="progress-bar bg-warning" style="width: {{ ($avg/5)*100 }}%">
                                    {{ number_format($avg, 1) }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted">এখনো কোনো মূল্যায়ন নেই</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h3 class="card-title">সব মূল্যায়ন</h3></div>
            <div class="card-body">
                @forelse($evaluations as $e)
                <div class="callout callout-info">
                    <div class="d-flex justify-content-between">
                        <div>
                            <b>
                                @if($e->is_anonymous)
                                    <i class="fas fa-user-secret"></i> Anonymous Student
                                @else
                                    <i class="fas fa-user"></i> {{ $e->student->name ?? '' }}
                                @endif
                            </b>
                            <br><small class="text-muted">
                                {{ $e->schoolClass->name ?? '' }} 
                                @if($e->subject) | {{ $e->subject->name }} @endif
                                | {{ $e->created_at->diffForHumans() }}
                            </small>
                        </div>
                        <div class="text-right">
                            <h4 class="text-warning mb-0">{{ number_format($e->average_rating, 1) }} <i class="fas fa-star"></i></h4>
                        </div>
                    </div>

                    @if($e->comments)
                        <p class="mt-2 mb-0">{{ $e->comments }}</p>
                    @endif

                    <div class="mt-2">
                        @foreach(\App\Models\TeacherEvaluation::criteria() as $key => $label)
                            <span class="badge badge-light mr-1">
                                {{ $label }}: {{ $e->$key }}/5
                            </span>
                        @endforeach
                    </div>
                    <form action="{{ route('admin.evaluations.destroy', $e) }}" method="POST" class="d-inline float-right mt-2" onsubmit="return confirm('নিশ্চিত?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
                @empty
                <p class="text-muted text-center py-4">কোনো মূল্যায়ন নেই</p>
                @endforelse
            </div>
            <div class="card-footer">{{ $evaluations->links() }}</div>
        </div>
    </div>
</div>
@endsection