@extends('frontend.partials.layout')
@section('title', 'অনলাইন ভর্তি')

@section('content')
<div class="bg-primary text-white py-5">
    <div class="container">
        <h1><i class="fas fa-user-plus"></i> অনলাইন ভর্তি ফর্ম</h1>
        <p class="mb-0">নিচের ফর্মটি সঠিকভাবে পূরণ করুন</p>
    </div>
</div>
<div class="container my-5">
    <form action="{{ route('frontend.admission.submit') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light"><h5 class="mb-0">শিক্ষার্থীর তথ্য</h5></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3"><label>নাম (English) *</label><input type="text" name="name" class="form-control" required></div>
                            <div class="col-md-6 mb-3"><label>নাম (বাংলা)</label><input type="text" name="name_bn" class="form-control"></div>
                            <div class="col-md-6 mb-3"><label>পিতার নাম *</label><input type="text" name="father_name" class="form-control" required></div>
                            <div class="col-md-6 mb-3"><label>মাতার নাম *</label><input type="text" name="mother_name" class="form-control" required></div>
                            <div class="col-md-6 mb-3"><label>পিতার মোবাইল *</label><input type="text" name="father_phone" class="form-control" required></div>
                            <div class="col-md-6 mb-3"><label>মাতার মোবাইল</label><input type="text" name="mother_phone" class="form-control"></div>
                            <div class="col-md-4 mb-3"><label>জন্ম তারিখ *</label><input type="date" name="date_of_birth" class="form-control" required></div>
                            <div class="col-md-4 mb-3">
                                <label>লিঙ্গ *</label>
                                <select name="gender" class="form-control" required>
                                    <option value="male">পুরুষ</option>
                                    <option value="female">মহিলা</option>
                                    <option value="other">অন্যান্য</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3"><label>ধর্ম</label><input type="text" name="religion" class="form-control"></div>
                            <div class="col-md-6 mb-3"><label>রক্তের গ্রুপ</label><input type="text" name="blood_group" class="form-control"></div>
                            <div class="col-md-6 mb-3"><label>ইমেইল</label><input type="email" name="email" class="form-control"></div>
                            <div class="col-md-12 mb-3"><label>বর্তমান ঠিকানা *</label><textarea name="present_address" class="form-control" rows="2" required></textarea></div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light"><h5 class="mb-0">একাডেমিক তথ্য</h5></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label>শ্রেণি *</label>
                                <select name="class_id" class="form-control" required>
                                    <option value="">নির্বাচন</option>
                                    @foreach($classes as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>বিভাগ</label>
                                <select name="group_id" class="form-control">
                                    <option value="">নির্বাচন</option>
                                    @foreach($groups as $g)<option value="{{ $g->id }}">{{ $g->name }}</option>@endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>শিক্ষাবর্ষ *</label>
                                <select name="academic_year_id" class="form-control" required>
                                    @foreach($academicYears as $y)<option value="{{ $y->id }}" @selected($y->is_current)>{{ $y->name }}</option>@endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-light"><h5 class="mb-0">ছবি ও ডকুমেন্ট</h5></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label>শিক্ষার্থীর ছবি</label>
                            <input type="file" name="photo" class="form-control" accept="image/*">
                            <small class="text-muted">jpg, jpeg, png (max 2MB)</small>
                        </div>
                        <div class="mb-3">
                            <label>জন্ম নিবন্ধন (ঐচ্ছিক)</label>
                            <input type="file" name="birth_certificate" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                        </div>
                        <div class="alert alert-info small">
                            <i class="fas fa-info-circle"></i> জমা দেওয়ার পর অফিস থেকে যাচাই করে আপনার সন্তানকে চূড়ান্ত ভর্তি দেওয়া হবে।
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-primary btn-block btn-lg"><i class="fas fa-paper-plane"></i> আবেদন জমা দিন</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
