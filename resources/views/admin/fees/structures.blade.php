@extends('admin.app')
@section('page-title', 'ফি স্ট্রাকচার')

@section('content')
<div class="row">
    <div class="col-md-5">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">নতুন ফি স্ট্রাকচার</h3></div>
            <form action="{{ route('admin.fees.structures.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label>শ্রেণি *</label>
                        <select name="class_id" class="form-control select2" required>
                            <option value="">নির্বাচন</option>
                            @foreach($classes as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>ফি ক্যাটাগরি *</label>
                        <select name="fee_category_id" class="form-control select2" required>
                            <option value="">নির্বাচন</option>
                            @foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>শিক্ষাবর্ষ</label>
                        <select name="academic_year_id" class="form-control select2">
                            <option value="">সব</option>
                            @foreach($academicYears as $y)<option value="{{ $y->id }}">{{ $y->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="form-group"><label>পরিমাণ (৳) *</label><input type="number" step="0.01" name="amount" class="form-control" required></div>
                    <div class="form-group">
                        <label>প্রযোজ্য</label>
                        <select name="applicable_for" class="form-control">
                            <option value="all">সবাই</option>
                            <option value="new">নতুন ভর্তি</option>
                            <option value="old">পুরাতন</option>
                        </select>
                    </div>
                    <div class="form-group"><label>কার্যকর তারিখ</label><input type="date" name="effective_from" class="form-control"></div>
                </div>
                <div class="card-footer"><button class="btn btn-primary"><i class="fas fa-plus"></i> যোগ করুন</button></div>
            </form>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <form method="GET" class="form-inline">
                    <label class="mr-2">শ্রেণি:</label>
                    <select name="class_id" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                        <option value="">সব</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" @selected(request('class_id')==$c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-striped datatable">
                    <thead><tr><th>শ্রেণি</th><th>ক্যাটাগরি</th><th>পরিমাণ</th><th>শিক্ষাবর্ষ</th><th></th></tr></thead>
                    <tbody>
                        @foreach($structures as $s)
                        <tr>
                            <td>{{ $s->schoolClass->name ?? '-' }}</td>
                            <td><b>{{ $s->category->name ?? '-' }}</b></td>
                            <td class="text-right"><b>৳ {{ number_format($s->amount, 2) }}</b></td>
                            <td>{{ $s->academicYear->name ?? 'সব' }}</td>
                            <td>
                                <form action="{{ route('admin.fees.structures.destroy', $s) }}" method="POST" onsubmit="return confirm('নিশ্চিত?')">
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
@endsection
