@extends('admin.app')

@section('title', 'নতুন শিক্ষক')
@section('page-title', 'নতুন শিক্ষক যোগ করুন')

@section('content')
<form action="{{ route('admin.teachers.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header"><h3 class="card-title">শিক্ষকের তথ্য</h3></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 form-group"><label>নাম (English) *</label><input type="text" name="name" class="form-control" required></div>
                        <div class="col-md-6 form-group"><label>নাম (বাংলা)</label><input type="text" name="name_bn" class="form-control"></div>
                        <div class="col-md-6 form-group">
                            <label>পদবি</label>
                            <select name="designation" class="form-control">
                                <option>Head Teacher</option>
                                <option>Assistant Teacher</option>
                                <option>Senior Teacher</option>
                                <option>Lecturer</option>
                                <option>Instructor</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group"><label>বিভাগ/ডিপার্টমেন্ট</label><input type="text" name="department" class="form-control"></div>
                        <div class="col-md-6 form-group"><label>পিতার নাম</label><input type="text" name="father_name" class="form-control"></div>
                        <div class="col-md-6 form-group"><label>মাতার নাম</label><input type="text" name="mother_name" class="form-control"></div>
                        <div class="col-md-6 form-group"><label>জন্ম তারিখ</label><input type="date" name="date_of_birth" class="form-control"></div>
                        <div class="col-md-6 form-group">
                            <label>লিঙ্গ</label>
                            <select name="gender" class="form-control"><option value="male">পুরুষ</option><option value="female">মহিলা</option></select>
                        </div>
                        <div class="col-md-6 form-group"><label>ধর্ম</label><input type="text" name="religion" class="form-control"></div>
                        <div class="col-md-6 form-group"><label>রক্তের গ্রুপ</label><input type="text" name="blood_group" class="form-control"></div>
                        <div class="col-md-6 form-group"><label>NID</label><input type="text" name="nid" class="form-control"></div>
                        <div class="col-md-6 form-group"><label>শিক্ষাগত যোগ্যতা</label><input type="text" name="qualification" class="form-control" placeholder="M.A, B.Ed"></div>
                        <div class="col-md-6 form-group"><label>যোগদানের তারিখ</label><input type="date" name="joining_date" class="form-control"></div>
                        <div class="col-md-6 form-group"><label>বেসিক বেতন</label><input type="number" step="0.01" name="basic_salary" class="form-control"></div>
                        <div class="col-md-6 form-group">
                            <label>চাকরির ধরন</label>
                            <select name="employment_type" class="form-control" required>
                                <option value="permanent">স্থায়ী</option>
                                <option value="contractual">চুক্তিভিত্তিক</option>
                                <option value="part_time">খণ্ডকালীন</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group"><label>বর্তমান ঠিকানা</label><input type="text" name="present_address" class="form-control"></div>
                        <div class="col-md-6 form-group"><label>স্থায়ী ঠিকানা</label><input type="text" name="permanent_address" class="form-control"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-success">
                <div class="card-header"><h3 class="card-title">লগইন ও ছবি</h3></div>
                <div class="card-body">
                    <div class="form-group text-center">
                        <img id="preview" src="https://ui-avatars.com/api/?name=Teacher&size=150" class="img-thumbnail mb-2" width="150">
                        <input type="file" name="photo" class="form-control-file" accept="image/*" onchange="document.getElementById('preview').src=window.URL.createObjectURL(this.files[0])">
                    </div>
                    <div class="form-group"><label>ইমেইল *</label><input type="email" name="email" class="form-control" required></div>
                    <div class="form-group"><label>মোবাইল *</label><input type="text" name="phone" class="form-control" required></div>
                    <small class="text-muted">ডিফল্ট পাসওয়ার্ড: <b>password</b></small>
                </div>
                <div class="card-footer">
                    <button class="btn btn-success btn-block"><i class="fas fa-save"></i> সংরক্ষণ করুন</button>
                    <a href="{{ route('admin.teachers.index') }}" class="btn btn-secondary btn-block">বাতিল</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
