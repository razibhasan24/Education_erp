@extends('admin.app')
@section('page-title', 'নতুন অভিভাবক')

@section('content')
<form action="{{ route('admin.guardians.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header"><h3 class="card-title">অভিভাবকের তথ্য</h3></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 form-group"><label>নাম (English) *</label><input type="text" name="name" class="form-control" required></div>
                        <div class="col-md-6 form-group"><label>নাম (বাংলা)</label><input type="text" name="name_bn" class="form-control"></div>
                        <div class="col-md-4 form-group">
                            <label>সম্পর্ক</label>
                            <select name="relation" class="form-control">
                                <option value="Father">পিতা</option>
                                <option value="Mother">মাতা</option>
                                <option value="Guardian">অভিভাবক</option>
                                <option value="Uncle">চাচা</option>
                                <option value="Aunt">খালা/ফুফু</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group"><label>মোবাইল *</label><input type="text" name="phone" class="form-control" required></div>
                        <div class="col-md-4 form-group"><label>পেশা</label><input type="text" name="occupation" class="form-control"></div>
                        <div class="col-md-6 form-group"><label>NID</label><input type="text" name="nid" class="form-control"></div>
                        <div class="col-md-6 form-group"><label>বর্তমান ঠিকানা</label><input type="text" name="present_address" class="form-control"></div>
                        <div class="col-md-12 form-group"><label>স্থায়ী ঠিকানা</label><input type="text" name="permanent_address" class="form-control"></div>
                    </div>

                    <hr>
                    <h5>সন্তান নির্বাচন করুন (একাধিক সিলেক্ট করা যাবে)</h5>
                    <div class="form-group">
                        <select name="student_ids[]" class="form-control select2" multiple required>
                            @foreach($students as $s)
                                <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->student_id }}) — {{ $s->schoolClass->name ?? '' }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-success">
                <div class="card-header"><h3 class="card-title">ছবি ও লগইন</h3></div>
                <div class="card-body">
                    <div class="form-group text-center">
                        <img id="preview" src="https://ui-avatars.com/api/?name=Guardian&size=150" class="img-thumbnail mb-2" width="150">
                        <input type="file" name="photo" accept="image/*" onchange="document.getElementById('preview').src=window.URL.createObjectURL(this.files[0])" class="form-control-file">
                    </div>
                    <div class="form-group"><label>ইমেইল *</label><input type="email" name="email" class="form-control" required></div>
                    <small class="text-muted">ডিফল্ট পাসওয়ার্ড: <b>password</b></small>
                </div>
                <div class="card-footer">
                    <button class="btn btn-success btn-block"><i class="fas fa-save"></i> সংরক্ষণ</button>
                    <a href="{{ route('admin.guardians.index') }}" class="btn btn-secondary btn-block">বাতিল</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
