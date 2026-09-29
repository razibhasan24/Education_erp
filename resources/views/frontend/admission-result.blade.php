@extends('frontend.partials.layout')
@section('title', 'ভর্তি পরীক্ষার ফলাফল')

@section('content')
<div class="bg-primary text-white py-5">
    <div class="container text-center">
        <h1><i class="fas fa-search"></i> ভর্তি পরীক্ষার ফলাফল</h1>
        <p>রোল নম্বর এবং মোবাইল নম্বর দিয়ে ফলাফল দেখুন</p>
    </div>
</div>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-body">
                    <form action="{{ route('frontend.admission-result.check') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label><b>রোল নম্বর *</b></label>
                            <input type="text" name="roll_no" value="{{ old('roll_no') }}" class="form-control form-control-lg" required placeholder="ADM-2025-1-0001">
                        </div>
                        <div class="form-group">
                            <label><b>মোবাইল নম্বর *</b></label>
                            <input type="text" name="phone" value="{{ old('phone') }}" class="form-control form-control-lg" required placeholder="01712345678">
                        </div>
                        <button class="btn btn-primary btn-lg btn-block mt-3"><i class="fas fa-search"></i> ফলাফল দেখুন</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection