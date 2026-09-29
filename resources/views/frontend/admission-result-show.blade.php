@extends('frontend.partials.layout')
@section('title', 'ফলাফল — ' . $result->student_name)

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-{{ $result->status === 'passed' ? 'success' : ($result->status === 'failed' ? 'danger' : 'warning') }} shadow-lg">
                <div class="card-header bg-{{ $result->status === 'passed' ? 'success' : ($result->status === 'failed' ? 'danger' : 'warning') }} text-white text-center py-4">
                    <i class="fas fa-{{ $result->status === 'passed' ? 'check-circle' : ($result->status === 'failed' ? 'times-circle' : 'clock') }} fa-4x"></i>
                    <h2 class="mt-2">
                        @if($result->status === 'passed') অভিনন্দন! আপনি উত্তীর্ণ হয়েছেন
                        @elseif($result->status === 'failed') দুঃখিত, আপনি অকৃতকার্য হয়েছেন
                        @else অপেক্ষমাণ তালিকায় আছেন
                        @endif
                    </h2>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr><th width="40%">রোল নম্বর</th><td><b>{{ $result->roll_no }}</b></td></tr>
                        <tr><th>নাম</th><td>{{ $result->student_name }}</td></tr>
                        <tr><th>পিতা</th><td>{{ $result->father_name }}</td></tr>
                        <tr><th>মাতা</th><td>{{ $result->mother_name ?? '-' }}</td></tr>
                        <tr><th>শ্রেণি</th><td>{{ $result->schoolClass->name ?? '-' }}</td></tr>
                        <tr><th>মেধাক্রম</th><td><h3 class="mb-0 text-primary">{{ $result->merit_position }}</h3></td></tr>
                    </table>

                    <h5 class="mt-4">বিষয়ভিত্তিক নম্বর</h5>
                    <table class="table table-bordered table-striped">
                        <tr><th>বাংলা</th><td class="text-right">{{ $result->bangla }}</td></tr>
                        <tr><th>ইংরেজি</th><td class="text-right">{{ $result->english }}</td></tr>
                        <tr><th>গণিত</th><td class="text-right">{{ $result->math }}</td></tr>
                        <tr><th>সাধারণ জ্ঞান</th><td class="text-right">{{ $result->general_knowledge }}</td></tr>
                        <tr class="bg-light"><th><b>মোট</b></th><th class="text-right text-primary"><h4 class="mb-0">{{ $result->total_marks }}</h4></th></tr>
                    </table>

                    @if($result->remarks)
                        <div class="alert alert-info mt-3">
                            <b>মন্তব্য:</b> {{ $result->remarks }}
                        </div>
                    @endif

                    <div class="text-center mt-3">
                        <button onclick="window.print()" class="btn btn-info"><i class="fas fa-print"></i> প্রিন্ট করুন</button>
                        <a href="{{ route('frontend.admission-result') }}" class="btn btn-secondary">আবার খুঁজুন</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection