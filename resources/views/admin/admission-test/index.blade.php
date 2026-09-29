@extends('admin.app')
@section('page-title', 'ভর্তি পরীক্ষার ফলাফল')

@section('page-actions')
    @if(request('class_id'))
        <form action="{{ route('admin.admission-test.publish') }}" method="POST" class="d-inline">
            @csrf
            <input type="hidden" name="class_id" value="{{ request('class_id') }}">
            <button class="btn btn-success btn-sm" onclick="return confirm('রেজাল্ট প্রকাশ করবেন?')">
                <i class="fas fa-check"></i> রেজাল্ট প্রকাশ করুন
            </button>
        </form>
        <a href="{{ route('admin.admission-test.pdf', ['class_id' => request('class_id')]) }}" class="btn btn-danger btn-sm" target="_blank">
            <i class="fas fa-file-pdf"></i> PDF
        </a>
    @endif
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">নতুন ফলাফল এন্ট্রি</h3></div>
            <form action="{{ route('admin.admission-test.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group"><label>শিক্ষার্থীর নাম *</label><input type="text" name="student_name" class="form-control" required></div>
                    <div class="form-group"><label>পিতার নাম *</label><input type="text" name="father_name" class="form-control" required></div>
                    <div class="form-group"><label>মাতার নাম</label><input type="text" name="mother_name" class="form-control"></div>
                    <div class="form-group"><label>মোবাইল *</label><input type="text" name="phone" class="form-control" required></div>
                    <div class="form-group"><label>ইমেইল</label><input type="email" name="email" class="form-control"></div>
                    <div class="form-group">
                        <label>শ্রেণি *</label>
                        <select name="class_id" class="form-control" required>
                            @foreach($classes as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>শিক্ষাবর্ষ</label>
                        <select name="academic_year_id" class="form-control">
                            @foreach($academicYears as $y)<option value="{{ $y->id }}" @selected($y->is_current)>{{ $y->name }}</option>@endforeach
                        </select>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-6 form-group"><label>বাংলা *</label><input type="number" step="0.01" name="bangla" value="0" class="form-control" required></div>
                        <div class="col-6 form-group"><label>ইংরেজি *</label><input type="number" step="0.01" name="english" value="0" class="form-control" required></div>
                        <div class="col-6 form-group"><label>গণিত *</label><input type="number" step="0.01" name="math" value="0" class="form-control" required></div>
                        <div class="col-6 form-group"><label>সাধারণ জ্ঞান *</label><input type="number" step="0.01" name="general_knowledge" value="0" class="form-control" required></div>
                    </div>
                </div>
                <div class="card-footer"><button class="btn btn-primary btn-block"><i class="fas fa-save"></i> সংরক্ষণ</button></div>
            </form>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card card-primary">
            <form method="GET" class="card-body form-inline">
                <label class="mr-1">শ্রেণি:</label>
                <select name="class_id" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                    <option value="">সব</option>
                    @foreach($classes as $c)<option value="{{ $c->id }}" @selected(request('class_id')==$c->id)>{{ $c->name }}</option>@endforeach
                </select>
                <label class="mr-1">স্ট্যাটাস:</label>
                <select name="status" class="form-control form-control-sm mr-2">
                    <option value="">সব</option>
                    <option value="pending" @selected(request('status')=='pending')>অপেক্ষমাণ</option>
                    <option value="passed" @selected(request('status')=='passed')>উত্তীর্ণ</option>
                    <option value="failed" @selected(request('status')=='failed')>অকৃতকার্য</option>
                    <option value="waiting" @selected(request('status')=='waiting')>অপেক্ষমাণ তালিকা</option>
                </select>
                <button class="btn btn-sm btn-info"><i class="fas fa-search"></i></button>
            </form>
        </div>

        <div class="card">
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-sm mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>মেধাক্রম</th>
                            <th>রোল</th>
                            <th>নাম</th>
                            <th>শ্রেণি</th>
                            <th class="text-center">বাং</th>
                            <th class="text-center">ইং</th>
                            <th class="text-center">গণ</th>
                            <th class="text-center">সা.জ্ঞ</th>
                            <th class="text-center">মোট</th>
                            <th>স্ট্যাটাস</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $r)
                        <tr>
                            <td class="text-center"><b>{{ $r->merit_position }}</b></td>
                            <td>{{ $r->roll_no }}</td>
                            <td>
                                {{ $r->student_name }}<br>
                                <small class="text-muted">{{ $r->phone }}</small>
                            </td>
                            <td>{{ $r->schoolClass->name ?? '-' }}</td>
                            <td class="text-center">{{ $r->bangla }}</td>
                            <td class="text-center">{{ $r->english }}</td>
                            <td class="text-center">{{ $r->math }}</td>
                            <td class="text-center">{{ $r->general_knowledge }}</td>
                            <td class="text-center"><b class="text-primary">{{ $r->total_marks }}</b></td>
                            <td>
                                @if($r->status === 'passed') <span class="badge badge-success">উত্তীর্ণ</span>
                                @elseif($r->status === 'failed') <span class="badge badge-danger">অকৃতকার্য</span>
                                @elseif($r->status === 'waiting') <span class="badge badge-warning">অপেক্ষমাণ</span>
                                @else <span class="badge badge-secondary">Pending</span> @endif
                                @if($r->is_published) <i class="fas fa-check-circle text-success ml-1" title="Published"></i>@endif
                            </td>
                            <td>
                                <button class="btn btn-xs btn-warning" data-toggle="modal" data-target="#editModal{{ $r->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('admin.admission-test.destroy', $r) }}" method="POST" class="d-inline" onsubmit="return confirm('নিশ্চিত?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>

                        {{-- Edit Modal --}}
                        <div class="modal fade" id="editModal{{ $r->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.admission-test.update', $r) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title">Edit — {{ $r->student_name }}</h5>
                                            <button class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-6 form-group"><label>বাংলা</label><input type="number" step="0.01" name="bangla" value="{{ $r->bangla }}" class="form-control" required></div>
                                                <div class="col-6 form-group"><label>ইংরেজি</label><input type="number" step="0.01" name="english" value="{{ $r->english }}" class="form-control" required></div>
                                                <div class="col-6 form-group"><label>গণিত</label><input type="number" step="0.01" name="math" value="{{ $r->math }}" class="form-control" required></div>
                                                <div class="col-6 form-group"><label>সাধারণ জ্ঞান</label><input type="number" step="0.01" name="general_knowledge" value="{{ $r->general_knowledge }}" class="form-control" required></div>
                                                <div class="col-12 form-group">
                                                    <label>স্ট্যাটাস *</label>
                                                    <select name="status" class="form-control" required>
                                                        <option value="pending" @selected($r->status=='pending')>অপেক্ষমাণ</option>
                                                        <option value="passed" @selected($r->status=='passed')>উত্তীর্ণ</option>
                                                        <option value="failed" @selected($r->status=='failed')>অকৃতকার্য</option>
                                                        <option value="waiting" @selected($r->status=='waiting')>অপেক্ষমাণ তালিকা</option>
                                                    </select>
                                                </div>
                                                <div class="col-12 form-group"><label>মন্তব্য</label><textarea name="remarks" class="form-control" rows="2">{{ $r->remarks }}</textarea></div>
                                            </div>
                                        </div>
                                        <div class="modal-footer"><button class="btn btn-warning"><i class="fas fa-save"></i> সংরক্ষণ</button></div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">{{ $results->links() }}</div>
        </div>
    </div>
</div>
@endsection