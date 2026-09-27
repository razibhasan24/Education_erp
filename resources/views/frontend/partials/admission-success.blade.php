@extends('frontend.partials.layout')
@section('title', 'আবেদন সফল')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-lg border-success">
                <div class="card-body text-center p-5">
                    <i class="fas fa-check-circle text-success" style="font-size:80px;"></i>
                    <h2 class="mt-3">আবেদন সফলভাবে জমা হয়েছে!</h2>
                    <p class="lead text-muted">আপনার আবেদন আইডি</p>
                    <h3 class="text-primary">{{ $student->student_id }}</h3>
                    <hr>
                    <p>অফিস কর্তৃক যাচাই শেষে যোগাযোগ করা হবে। এই নম্বরটি সংরক্ষণ করুন।</p>
                    <a href="{{ route('frontend.home') }}" class="btn btn-primary mt-3"><i class="fas fa-home"></i> হোমে ফিরে যান</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
