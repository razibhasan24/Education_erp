@extends('admin.app')
@section('page-title', 'বৃত্তির আবেদন')

@section('page-actions')
    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#newApplicationModal">
        <i class="fas fa-plus"></i> নতুন আবেদন
    </button>
    <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#bulkModal">
        <i class="fas fa-copy"></i> বাল্ক আবেদন
    </button>
@endsection

@section('content')
<div class="card card-primary">
    <form method="GET" class="card-body form-inline">
        <label class="mr-1">বৃত্তি:</label>
        <select name="scholarship_id" class="form-control form-control-sm mr-2">
            <option value="">সব</option>
            @foreach($scholarships as $s)<option value="{{ $s->id }}" @selected(request('scholarship_id')==$s->id)>{{ $s->name }}</option>@endforeach
        </select>
        <label class="mr-1">স্ট্যাটাস:</label>
        <select name="status" class="form-control form-control-sm mr-2">
            <option value="">সব</option>
            <option value="pending" @selected(request('status')=='pending')>অপেক্ষমাণ</option>
            <option value="approved" @selected(request('status')=='approved')>অনুমোদিত</option>
            <option value="rejected" @selected(request('status')=='rejected')>বাতিল</option>
        </select>
        <button class="btn btn-sm btn-info"><i class="fas fa-search"></i> ফিল্টার</button>
    </form>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover datatable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>শিক্ষার্থী</th>
                    <th>শ্রেণি</th>
                    <th>বৃত্তি</th>
                    <th>শিক্ষাবর্ষ</th>
                    <th class="text-right">চাওয়া (৳)</th>
                    <th class="text-right">অনুমোদিত (৳)</th>
                    <th>স্ট্যাটাস</th>
                    <th>অ্যাকশন</th>
                </tr>
            </thead>
            <tbody>
                @foreach($applications as $a)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <b>{{ $a->student->name ?? '-' }}</b><br>
                        <small class="text-muted">{{ $a->student->student_id ?? '' }}</small>
                    </td>
                    <td>{{ $a->student->schoolClass->name ?? '-' }} {{ $a->student->section->name ?? '' }}</td>
                    <td>{{ $a->scholarship->name ?? '-' }}</td>
                    <td>{{ $a->academicYear->name ?? '-' }}</td>
                    <td class="text-right">{{ number_format($a->requested_amount, 2) }}</td>
                    <td class="text-right"><b class="text-success">{{ number_format($a->approved_amount, 2) }}</b></td>
                    <td>
                        @if($a->status === 'approved') <span class="badge badge-success">অনুমোদিত</span>
                        @elseif($a->status === 'rejected') <span class="badge badge-danger">বাতিল</span>
                        @elseif($a->status === 'cancelled') <span class="badge badge-secondary">বাতিল</span>
                        @else <span class="badge badge-warning">অপেক্ষমাণ</span> @endif
                    </td>
                    <td>
                        @if($a->status === 'pending')
                            <button class="btn btn-xs btn-success" data-toggle="modal" data-target="#approveModal{{ $a->id }}">
                                <i class="fas fa-check"></i> অনুমোদন
                            </button>
                            <form action="{{ route('admin.scholarships.applications.reject', $a) }}" method="POST" class="d-inline" onsubmit="return confirm('নিশ্চিত?')">
                                @csrf
                                <button class="btn btn-xs btn-danger"><i class="fas fa-times"></i></button>
                            </form>
                        @elseif($a->status === 'approved' && $a->approvedBy)
                            <small class="text-muted">
                                অনুমোদন: {{ $a->approvedBy->name }}<br>
                                {{ $a->approved_at?->format('d M, Y') }}
                            </small>
                        @endif
                    </td>
                </tr>

                {{-- Approve Modal --}}
                @if($a->status === 'pending')
                <div class="modal fade" id="approveModal{{ $a->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('admin.scholarships.applications.approve', $a) }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title">বৃত্তি অনুমোদন — {{ $a->student->name }}</h5>
                                    <button class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <p><b>বৃত্তি:</b> {{ $a->scholarship->name }}</p>
                                    <p><b>প্রস্তাবিত ছাড়:</b> {{ $a->scholarship->type === 'percentage' ? $a->scholarship->value . '%' : '৳' . number_format($a->scholarship->value, 2) }}</p>
                                    <div class="form-group">
                                        <label>অনুমোদিত পরিমাণ (৳) *</label>
                                        <input type="number" step="0.01" name="approved_amount" class="form-control" required value="{{ $a->scholarship->value }}">
                                    </div>
                                    <div class="form-group">
                                        <label>মন্তব্য</label>
                                        <textarea name="remarks" class="form-control" rows="2"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-success"><i class="fas fa-check"></i> অনুমোদন করুন</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
            </tbody>
        </table>
        <div class="mt-2">{{ $applications->links() }}</div>
    </div>
</div>

{{-- New Application Modal --}}
<div class="modal fade" id="newApplicationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.scholarships.applications.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">নতুন বৃত্তির আবেদন</h5>
                    <button class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>বৃত্তি *</label>
                            <select name="scholarship_id" class="form-control select2" required>
                                <option value="">-- নির্বাচন --</option>
                                @foreach($scholarships as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>শিক্ষার্থী *</label>
                            <select name="student_id" class="form-control select2" required>
                                <option value="">-- নির্বাচন --</option>
                                @foreach($students as $st)<option value="{{ $st->id }}">{{ $st->name }} ({{ $st->student_id }})</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>শিক্ষাবর্ষ *</label>
                            <select name="academic_year_id" class="form-control select2" required>
                                @foreach($academicYears as $y)<option value="{{ $y->id }}" @selected($y->is_current)>{{ $y->name }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-12 form-group">
                            <label>কারণ</label>
                            <textarea name="reason" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer"><button class="btn btn-primary"><i class="fas fa-save"></i> জমা দিন</button></div>
            </form>
        </div>
    </div>
</div>

{{-- Bulk Modal --}}
<div class="modal fade" id="bulkModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.scholarships.bulk-apply') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">বাল্ক আবেদন (একটি ক্লাসের সব শিক্ষার্থী)</h5>
                    <button class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>বৃত্তি *</label>
                        <select name="scholarship_id" class="form-control select2" required>
                            <option value="">-- নির্বাচন --</option>
                            @foreach($scholarships as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>শ্রেণি *</label>
                        <select name="class_id" id="bulk_class_id" class="form-control select2" required>
                            <option value="">-- নির্বাচন --</option>
                            @foreach(\App\Models\SchoolClass::where('is_active',true)->orderBy('numeric_value')->get() as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>শাখা</label>
                        <select name="section_id" id="bulk_section_id" class="form-control select2">
                            <option value="">সব</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>শিক্ষাবর্ষ *</label>
                        <select name="academic_year_id" class="form-control select2" required>
                            @foreach($academicYears as $y)<option value="{{ $y->id }}" @selected($y->is_current)>{{ $y->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="alert alert-info small mb-0">
                        <i class="fas fa-info-circle"></i> নির্বাচিত ক্লাসের সব শিক্ষার্থীকে "Pending" স্ট্যাটাসে আবেদন তৈরি হবে।
                    </div>
                </div>
                <div class="modal-footer"><button class="btn btn-info"><i class="fas fa-copy"></i> বাল্ক আবেদন</button></div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('bulk_class_id')?.addEventListener('change', function () {
    const cid = this.value;
    const sel = document.getElementById('bulk_section_id');
    sel.innerHTML = '<option value="">সব</option>';
    if (!cid) return;
    fetch(`{{ url('api/sections') }}?class_id=${cid}`)
        .then(r => r.json())
        .then(data => data.forEach(s => sel.innerHTML += `<option value="${s.id}">${s.name}</option>`));
});
</script>
@endpush