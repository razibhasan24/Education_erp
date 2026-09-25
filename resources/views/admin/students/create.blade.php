@extends('admin.app')

@section('title', 'নতুন শিক্ষার্থী ভর্তি')
@section('page-title', 'নতুন শিক্ষার্থী ভর্তি (Online/Offline)')

@section('content')
<form action="{{ route('admin.students.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header"><h3 class="card-title">ব্যক্তিগত তথ্য</h3></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>নাম (English) *</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label>নাম (বাংলা)</label>
                            <input type="text" name="name_bn" value="{{ old('name_bn') }}" class="form-control">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>পিতার নাম *</label>
                            <input type="text" name="father_name" value="{{ old('father_name') }}" class="form-control" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>মাতার নাম *</label>
                            <input type="text" name="mother_name" value="{{ old('mother_name') }}" class="form-control" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>পিতার মোবাইল</label>
                            <input type="text" name="father_phone" value="{{ old('father_phone') }}" class="form-control">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>মাতার মোবাইল</label>
                            <input type="text" name="mother_phone" value="{{ old('mother_phone') }}" class="form-control">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>জন্ম তারিখ *</label>
                            <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" class="form-control" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>লিঙ্গ *</label>
                            <select name="gender" class="form-control" required>
                                <option value="male">পুরুষ</option>
                                <option value="female">মহিলা</option>
                                <option value="other">অন্যান্য</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>ধর্ম</label>
                            <input type="text" name="religion" value="{{ old('religion', 'Islam') }}" class="form-control">
                        </div>
                        <div class="col-md-4 form-group">
                            <label>রক্তের গ্রুপ</label>
                            <input type="text" name="blood_group" value="{{ old('blood_group') }}" class="form-control">
                        </div>
                        <div class="col-md-4 form-group">
                            <label>NID / জন্ম নিবন্ধন</label>
                            <input type="text" name="nid_or_birth_certificate" value="{{ old('nid_or_birth_certificate') }}" class="form-control">
                        </div>
                        <div class="col-md-12 form-group">
                            <label>বর্তমান ঠিকানা</label>
                            <textarea name="present_address" class="form-control" rows="2">{{ old('present_address') }}</textarea>
                        </div>
                        <div class="col-md-12 form-group">
                            <label>স্থায়ী ঠিকানা</label>
                            <textarea name="permanent_address" class="form-control" rows="2">{{ old('permanent_address') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-info">
                <div class="card-header"><h3 class="card-title">একাডেমিক তথ্য</h3></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>শ্রেণি *</label>
                            <select name="class_id" class="form-control select2" required>
                                <option value="">নির্বাচন করুন</option>
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}" @selected(old('class_id')==$c->id)>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>শাখা</label>
                            <select name="section_id" class="form-control select2">
                                <option value="">নির্বাচন করুন</option>
                                @foreach($sections as $s)
                                    <option value="{{ $s->id }}" @selected(old('section_id')==$s->id)>{{ $s->schoolClass->name ?? '' }} - {{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>বিভাগ</label>
                            <select name="group_id" class="form-control select2">
                                <option value="">নির্বাচন করুন</option>
                                @foreach($groups as $g)
                                    <option value="{{ $g->id }}" @selected(old('group_id')==$g->id)>{{ $g->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>শিক্ষাবর্ষ *</label>
                            <select name="academic_year_id" class="form-control select2" required>
                                @foreach($academicYears as $y)
                                    <option value="{{ $y->id }}" @selected($y->is_current)>{{ $y->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>রোল নম্বর</label>
                            <input type="number" name="roll_number" value="{{ old('roll_number') }}" class="form-control">
                        </div>
                        <div class="col-md-4 form-group">
                            <label>ভর্তির তারিখ *</label>
                            <input type="date" name="admission_date" value="{{ old('admission_date', date('Y-m-d')) }}" class="form-control" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>ভর্তির ধরন *</label>
                            <select name="admission_type" class="form-control" required>
                                <option value="offline" @selected(old('admission_type')=='offline')>অফলাইন</option>
                                <option value="online" @selected(old('admission_type')=='online')>অনলাইন</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-success">
                <div class="card-header"><h3 class="card-title">ছবি ও লগইন অ্যাকাউন্ট</h3></div>
                <div class="card-body">
                    <div class="form-group text-center">
                        <img id="preview" src="https://ui-avatars.com/api/?name=Student&size=150" class="img-thumbnail mb-2" width="150">
                        <input type="file" name="photo" id="photo" class="form-control-file" accept="image/*" onchange="previewImage(event)">
                    </div>
                    <hr>
                    <div class="form-group">
                        <label>লগইন ইমেইল (ঐচ্ছিক)</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror">
                        @error('email')<span class="text-danger small">{{ $message }}</span>@enderror
                        <small class="text-muted">দিলে স্টুডেন্ট ড্যাশবোর্ডে লগইন করতে পারবে। ডিফল্ট পাসওয়ার্ড: <b>password</b></small>
                    </div>
                    <div class="form-group">
                        <label>মোবাইল</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="form-control">
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success btn-block"><i class="fas fa-save"></i> ভর্তি সম্পন্ন করুন</button>
                    <a href="{{ route('admin.students.index') }}" class="btn btn-secondary btn-block">বাতিল</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
function previewImage(e) {
    const reader = new FileReader();
    reader.onload = () => document.getElementById('preview').src = reader.result;
    reader.readAsDataURL(e.target.files[0]);
}
</script>
@endpush
