@extends('admin.app')
@section('page-title', 'SMS সেটিংস')

@section('content')
<form action="{{ route('admin.sms.settings.update') }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-cog"></i> API কনফিগারেশন</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>প্রোভাইডার *</label>
                        <select name="provider" class="form-control" required>
                            <option value="bulksmsbd" @selected($setting->provider=='bulksmsbd')>Bulk SMS BD (bulksmsbd.net)</option>
                            <option value="alphasms" @selected($setting->provider=='alphasms')>Alpha SMS (alphasms.biz)</option>
                            <option value="custom" @selected($setting->provider=='custom')>Custom API</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>API Key / User</label>
                        <input type="text" name="api_key" value="{{ $setting->api_key }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Sender ID / Password</label>
                        <input type="text" name="sender_id" value="{{ $setting->sender_id }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>API URL (ঐচ্ছিক)</label>
                        <input type="url" name="api_url" value="{{ $setting->api_url }}" class="form-control" placeholder="ডিফল্ট URL ব্যবহার হবে">
                    </div>
                    <div class="custom-control custom-switch mb-2">
                        <input type="checkbox" name="is_active" value="1" class="custom-control-input" id="active" @checked($setting->is_active)>
                        <label class="custom-control-label" for="active"><b>SMS সিস্টেম চালু</b></label>
                    </div>
                    <hr>
                    <h5>স্বয়ংক্রিয় নোটিফিকেশন</h5>
                    <div class="custom-control custom-switch mb-2">
                        <input type="checkbox" name="notify_attendance" value="1" class="custom-control-input" id="na" @checked($setting->notify_attendance)>
                        <label class="custom-control-label" for="na">হাজিরা SMS</label>
                    </div>
                    <div class="custom-control custom-switch mb-2">
                        <input type="checkbox" name="notify_result" value="1" class="custom-control-input" id="nr" @checked($setting->notify_result)>
                        <label class="custom-control-label" for="nr">ফলাফল SMS</label>
                    </div>
                    <div class="custom-control custom-switch mb-2">
                        <input type="checkbox" name="notify_due" value="1" class="custom-control-input" id="nd" @checked($setting->notify_due)>
                        <label class="custom-control-label" for="nd">বকেয়া SMS</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-info">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-edit"></i> SMS টেমপ্লেট</h3></div>
                <div class="card-body">
                    <div class="alert alert-info small">
                        <b>ভেরিয়েবল:</b> {student_name}, {status}, {date}, {class}, {roll}, {due_amount}, {exam_name}, {gpa}, {grade}, {institute}
                    </div>
                    <div class="form-group">
                        <label>হাজিরা টেমপ্লেট</label>
                        <textarea name="attendance_template" class="form-control" rows="3">{{ $setting->attendance_template }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>ফলাফল টেমপ্লেট</label>
                        <textarea name="result_template" class="form-control" rows="3">{{ $setting->result_template }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>বকেয়া টেমপ্লেট</label>
                        <textarea name="due_template" class="form-control" rows="3">{{ $setting->due_template }}</textarea>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary"><i class="fas fa-save"></i> সংরক্ষণ</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
