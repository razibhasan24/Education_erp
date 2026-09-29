@extends('admin.app')
@section('page-title', 'বৃত্তি ব্যবস্থাপনা')

@section('page-actions')
    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addScholarshipModal">
        <i class="fas fa-plus"></i> নতুন বৃত্তি
    </button>
    <a href="{{ route('admin.scholarships.applications') }}" class="btn btn-info btn-sm">
        <i class="fas fa-file-alt"></i> আবেদন তালিকা
    </a>
@endsection

@section('content')
<div class="row">
    @foreach($scholarships as $s)
    <div class="col-md-6 col-lg-4">
        <div class="card card-{{ $s->is_active ? 'primary' : 'secondary' }} card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-award"></i>
                    <b>{{ $s->name }}</b>
                    <br><small class="text-muted">Code: {{ $s->code }}</small>
                </h3>
                <div class="card-tools">
                    @if($s->is_active)
                        <span class="badge badge-success">সক্রিয়</span>
                    @else
                        <span class="badge badge-secondary">নিষ্ক্রিয়</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <span class="badge badge-info">{{ \App\Models\Scholarship::criteriaLabels()[$s->criteria_type] ?? $s->criteria_type }}</span>
                    @if($s->applicable_to === 'tuition')
                        <span class="badge badge-warning">শুধু বেতন</span>
                    @elseif($s->applicable_to === 'all_fees')
                        <span class="badge badge-success">সব ফি</span>
                    @endif
                </div>

                <div class="callout callout-{{ $s->type === 'percentage' ? 'info' : 'success' }} py-2 mb-2">
                    <h4 class="mb-0">
                        <b>
                            @if($s->type === 'percentage')
                                {{ $s->value }}% ছাড়
                            @else
                                ৳{{ number_format($s->value, 2) }} ছাড়
                            @endif
                        </b>
                    </h4>
                </div>

                <ul class="list-unstyled small mb-2">
                    @if($s->min_gpa)<li><i class="fas fa-check text-success"></i> সর্বনিম্ন GPA: {{ $s->min_gpa }}</li>@endif
                    @if($s->max_income)<li><i class="fas fa-check text-success"></i> সর্বোচ্চ পারিবারিক আয়: ৳{{ number_format($s->max_income, 2) }}</li>@endif
                    @if($s->max_recipients)<li><i class="fas fa-users"></i> সর্বোচ্চ {{ $s->max_recipients }} জন</li>@endif
                    <li><i class="fas fa-file-alt text-info"></i> মোট আবেদন: <b>{{ $s->applications_count }}</b></li>
                    @if($s->start_date && $s->end_date)
                        <li><i class="fas fa-calendar"></i> {{ $s->start_date->format('d M, Y') }} — {{ $s->end_date->format('d M, Y') }}</li>
                    @endif
                </ul>

                @if($s->description)
                    <p class="text-muted small mb-0">{{ Str::limit($s->description, 100) }}</p>
                @endif
            </div>
            <div class="card-footer">
                <button class="btn btn-xs btn-warning" data-toggle="modal" data-target="#editModal{{ $s->id }}">
                    <i class="fas fa-edit"></i> এডিট
                </button>
                <form action="{{ route('admin.scholarships.destroy', $s) }}" method="POST" class="d-inline" onsubmit="return confirm('নিশ্চিত?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div class="modal fade" id="editModal{{ $s->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.scholarships.update', $s) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">বৃত্তি এডিট — {{ $s->name }}</h5>
                        <button class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group"><label>নাম *</label><input type="text" name="name" value="{{ $s->name }}" class="form-control" required></div>
                        <div class="form-group"><label>Value *</label><input type="number" step="0.01" name="value" value="{{ $s->value }}" class="form-control" required></div>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="is_active" value="1" class="custom-control-input" id="act{{ $s->id }}" @checked($s->is_active)>
                            <label class="custom-control-label" for="act{{ $s->id }}">সক্রিয়</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary"><i class="fas fa-save"></i> সংরক্ষণ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach

    @if($scholarships->isEmpty())
        <div class="col-12">
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> এখনো কোনো বৃত্তি যোগ করা হয়নি।
            </div>
        </div>
    @endif
</div>

{{-- Add Modal --}}
<div class="modal fade" id="addScholarshipModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.scholarships.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-award"></i> নতুন বৃত্তি / ছাড়</h5>
                    <button class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group"><label>নাম *</label><input type="text" name="name" class="form-control" required></div>
                        <div class="col-md-6 form-group"><label>কোড *</label><input type="text" name="code" class="form-control" required placeholder="MERIT-2025"></div>
                        <div class="col-md-4 form-group">
                            <label>ধরন *</label>
                            <select name="type" class="form-control" required>
                                <option value="percentage">শতকরা (%)</option>
                                <option value="fixed">নির্দিষ্ট পরিমাণ (৳)</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group"><label>Value *</label><input type="number" step="0.01" name="value" class="form-control" required placeholder="50 বা 500"></div>
                        <div class="col-md-4 form-group">
                            <label>প্রযোজ্য *</label>
                            <select name="applicable_to" class="form-control" required>
                                <option value="tuition">শুধু বেতন ফি</option>
                                <option value="all_fees">সব ফি</option>
                                <option value="specific_category">নির্দিষ্ট ক্যাটাগরি</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>ফি ক্যাটাগরি (নির্দিষ্ট হলে)</label>
                            <select name="fee_category_id" class="form-control select2">
                                <option value="">-- নির্বাচন --</option>
                                @foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>মানদণ্ড *</label>
                            <select name="criteria_type" class="form-control" required>
                                @foreach(\App\Models\Scholarship::criteriaLabels() as $k=>$v)
                                    <option value="{{ $k }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 form-group"><label>সর্বনিম্ন GPA</label><input type="number" step="0.01" min="0" max="5" name="min_gpa" class="form-control"></div>
                        <div class="col-md-4 form-group"><label>সর্বোচ্চ পারিবারিক আয় (৳)</label><input type="number" step="0.01" name="max_income" class="form-control"></div>
                        <div class="col-md-4 form-group"><label>সর্বোচ্চ প্রাপক</label><input type="number" name="max_recipients" class="form-control"></div>
                        <div class="col-md-6 form-group"><label>শুরুর তারিখ</label><input type="date" name="start_date" class="form-control"></div>
                        <div class="col-md-6 form-group"><label>শেষ তারিখ</label><input type="date" name="end_date" class="form-control"></div>
                        <div class="col-md-12 form-group"><label>বিবরণ</label><textarea name="description" class="form-control" rows="2"></textarea></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary"><i class="fas fa-save"></i> সংরক্ষণ</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection