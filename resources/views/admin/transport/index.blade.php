@extends('admin.app')
@section('page-title', 'ট্রান্সপোর্ট ব্যবস্থাপনা')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">নতুন যানবাহন</h3></div>
            <form action="{{ route('admin.transport.vehicles.store') }}" method="POST" class="card-body">
                @csrf
                <div class="row">
                    <div class="col-md-6 form-group"><label>নাম *</label><input type="text" name="name" class="form-control" required></div>
                    <div class="col-md-6 form-group"><label>রেজি. নং *</label><input type="text" name="registration_no" class="form-control" required></div>
                    <div class="col-md-3 form-group"><label>ধারণক্ষমতা *</label><input type="number" name="capacity" value="40" class="form-control" required></div>
                    <div class="col-md-3 form-group"><label>ড্রাইভার</label><input type="text" name="driver_name" class="form-control"></div>
                    <div class="col-md-3 form-group"><label>ড্রাইভার ফোন</label><input type="text" name="driver_phone" class="form-control"></div>
                    <div class="col-md-3 form-group"><label>হেল্পার</label><input type="text" name="helper_name" class="form-control"></div>
                </div>
                <button class="btn btn-primary"><i class="fas fa-plus"></i> যোগ</button>
            </form>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-info">
            <div class="card-header"><h3 class="card-title">যানবাহন তালিকা</h3></div>
            <div class="card-body table-responsive">
                <table class="table table-sm">
                    <thead><tr><th>নাম</th><th>রেজি. নং</th><th>ধারণক্ষমতা</th><th>ড্রাইভার</th><th></th></tr></thead>
                    <tbody>
                        @foreach($vehicles as $v)
                        <tr>
                            <td>{{ $v->name }}</td>
                            <td>{{ $v->registration_no }}</td>
                            <td>{{ $v->capacity }}</td>
                            <td>{{ $v->driver_name ?? '-' }}</td>
                            <td>
                                <form action="{{ route('admin.transport.vehicles.destroy', $v) }}" method="POST" class="d-inline" onsubmit="return confirm('নিশ্চিত?')">
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

<div class="row">
    <div class="col-md-6">
        <div class="card card-success">
            <div class="card-header"><h3 class="card-title">নতুন রুট</h3></div>
            <form action="{{ route('admin.transport.routes.store') }}" method="POST" class="card-body">
                @csrf
                <div class="row">
                    <div class="col-md-6 form-group"><label>নাম *</label><input type="text" name="name" class="form-control" required></div>
                    <div class="col-md-6 form-group"><label>মাসিক ফি *</label><input type="number" step="0.01" name="monthly_fee" value="0" class="form-control" required></div>
                    <div class="col-md-6 form-group"><label>শুরু</label><input type="text" name="start_point" class="form-control"></div>
                    <div class="col-md-6 form-group"><label>শেষ</label><input type="text" name="end_point" class="form-control"></div>
                    <div class="col-md-12 form-group">
                        <label>যানবাহন</label>
                        <select name="vehicle_id" class="form-control select2">
                            <option value="">নির্বাচন</option>
                            @foreach($vehicles as $v)<option value="{{ $v->id }}">{{ $v->name }} ({{ $v->registration_no }})</option>@endforeach
                        </select>
                    </div>
                </div>
                <button class="btn btn-success"><i class="fas fa-plus"></i> রুট যোগ</button>
            </form>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-warning">
            <div class="card-header"><h3 class="card-title">রুট তালিকা</h3></div>
            <div class="card-body table-responsive">
                <table class="table table-sm">
                    <thead><tr><th>নাম</th><th>যানবাহন</th><th>ফি</th><th>শিক্ষার্থী</th><th></th></tr></thead>
                    <tbody>
                        @foreach($routes as $r)
                        <tr>
                            <td><b>{{ $r->name }}</b></td>
                            <td>{{ $r->vehicle->name ?? '-' }}</td>
                            <td>৳{{ number_format($r->monthly_fee, 0) }}</td>
                            <td><span class="badge badge-info">{{ $r->student_transports_count }}</span></td>
                            <td>
                                <form action="{{ route('admin.transport.routes.destroy', $r) }}" method="POST" class="d-inline" onsubmit="return confirm('নিশ্চিত?')">
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
    <div class="card-header"><h3 class="card-title">শিক্ষার্থীকে ট্রান্সপোর্টে যোগ করুন</h3></div>
    <form action="{{ route('admin.transport.assign') }}" method="POST" class="card-body">
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
                <label>রুট *</label>
                <select name="transport_route_id" class="form-control select2" required>
                    <option value="">নির্বাচন</option>
                    @foreach($routes as $r)<option value="{{ $r->id }}">{{ $r->name }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-3 form-group"><label>স্টার্ট তারিখ *</label><input type="date" name="start_date" value="{{ date('Y-m-d') }}" class="form-control" required></div>
            <div class="col-md-3 d-flex align-items-end"><button class="btn btn-success btn-block"><i class="fas fa-check"></i> বরাদ্দ</button></div>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header"><h3 class="card-title">চলমান বরাদ্দ</h3></div>
    <div class="card-body table-responsive">
        <table class="table table-hover datatable">
            <thead><tr><th>শিক্ষার্থী</th><th>রুট</th><th>স্টার্ট</th><th>স্ট্যাটাস</th><th></th></tr></thead>
            <tbody>
                @foreach($assignments as $a)
                <tr>
                    <td>{{ $a->student->name ?? '-' }}</td>
                    <td>{{ $a->route->name ?? '-' }}</td>
                    <td>{{ $a->start_date->format('d M, Y') }}</td>
                    <td><span class="badge badge-success">সক্রিয়</span></td>
                    <td>
                        <form action="{{ route('admin.transport.assign.remove', $a) }}" method="POST" class="d-inline" onsubmit="return confirm('বন্ধ করবেন?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-xs btn-danger"><i class="fas fa-times"></i> বন্ধ</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
