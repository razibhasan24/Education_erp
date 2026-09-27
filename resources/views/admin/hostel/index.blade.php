@extends('admin.app')
@section('page-title', 'হোস্টেল ব্যবস্থাপনা')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">নতুন হোস্টেল</h3></div>
            <form action="{{ route('admin.hostel.hostels.store') }}" method="POST" class="card-body">
                @csrf
                <div class="row">
                    <div class="col-md-6 form-group"><label>নাম *</label><input type="text" name="name" class="form-control" required></div>
                    <div class="col-md-6 form-group">
                        <label>ধরন *</label>
                        <select name="type" class="form-control" required>
                            <option value="boys">ছেলেদের</option>
                            <option value="girls">মেয়েদের</option>
                        </select>
                    </div>
                    <div class="col-md-6 form-group"><label>ওয়ার্ডেন</label><input type="text" name="warden_name" class="form-control"></div>
                    <div class="col-md-6 form-group"><label>ওয়ার্ডেন ফোন</label><input type="text" name="warden_phone" class="form-control"></div>
                    <div class="col-md-6 form-group"><label>মাসিক ফি</label><input type="number" step="0.01" name="monthly_fee" value="0" class="form-control"></div>
                    <div class="col-md-6 form-group"><label>ঠিকানা</label><input type="text" name="address" class="form-control"></div>
                </div>
                <button class="btn btn-primary"><i class="fas fa-plus"></i> যোগ</button>
            </form>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-info">
            <div class="card-header"><h3 class="card-title">হোস্টেল তালিকা</h3></div>
            <div class="card-body table-responsive">
                <table class="table table-sm">
                    <thead><tr><th>নাম</th><th>ধরন</th><th>রুম</th><th>ওয়ার্ডেন</th><th></th></tr></thead>
                    <tbody>
                        @foreach($hostels as $h)
                        <tr>
                            <td><b>{{ $h->name }}</b></td>
                            <td>{{ $h->type === 'boys' ? 'ছেলে' : 'মেয়ে' }}</td>
                            <td><span class="badge badge-info">{{ $h->rooms_count }}</span></td>
                            <td>{{ $h->warden_name ?? '-' }}</td>
                            <td>
                                <form action="{{ route('admin.hostel.hostels.destroy', $h) }}" method="POST" class="d-inline" onsubmit="return confirm('নিশ্চিত?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card card-success">
    <div class="card-header"><h3 class="card-title">শিক্ষার্থী বরাদ্দ</h3></div>
    <form action="{{ route('admin.hostel.allocate') }}" method="POST" class="card-body">
        @csrf
        <div class="row">
            <div class="col-md-3 form-group">
                <label>শিক্ষার্থী *</label>
                <select name="student_id" class="form-control select2" required>
                    <option value="">নির্বাচন</option>
                    @foreach($students as $s)<option value="{{ $s->id }}">{{ $s->name }} ({{ $s->student_id }})</option>@endforeach
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label>হোস্টেল *</label>
                <select name="hostel_id" id="hostel_id" class="form-control select2" required>
                    <option value="">নির্বাচন</option>
                    @foreach($hostels as $h)<option value="{{ $h->id }}">{{ $h->name }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label>রুম *</label>
                <select name="hostel_room_id" id="room_id" class="form-control select2" required>
                    <option value="">হোস্টেল নির্বাচন করুন</option>
                </select>
            </div>
            <div class="col-md-3 form-group"><label>শুরু *</label><input type="date" name="start_date" value="{{ date('Y-m-d') }}" class="form-control" required></div>
        </div>
        <button class="btn btn-success"><i class="fas fa-check"></i> বরাদ্দ</button>
    </form>
</div>

<div class="card">
    <div class="card-header"><h3 class="card-title">চলমান বরাদ্দ</h3></div>
    <div class="card-body table-responsive">
        <table class="table table-hover datatable">
            <thead><tr><th>শিক্ষার্থী</th><th>হোস্টেল</th><th>রুম</th><th>শুরু</th><th></th></tr></thead>
            <tbody>
                @foreach($allocations as $a)
                <tr>
                    <td>{{ $a->student->name ?? '-' }}</td>
                    <td>{{ $a->hostel->name ?? '-' }}</td>
                    <td>{{ $a->room->room_no ?? '-' }}</td>
                    <td>{{ $a->start_date->format('d M, Y') }}</td>
                    <td>
                        <form action="{{ route('admin.hostel.allocate.release', $a) }}" method="POST" class="d-inline" onsubmit="return confirm('খালি করবেন?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-xs btn-danger"><i class="fas fa-sign-out-alt"></i> রিলিজ</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
