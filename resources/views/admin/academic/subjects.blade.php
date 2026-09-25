@extends('admin.app')
@section('page-title', 'বিষয় ব্যবস্থাপনা')

@section('content')
<div class="row">
    <div class="col-md-5">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">নতুন বিষয়</h3></div>
            <form action="{{ route('admin.academic.subjects.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group"><label>নাম *</label><input type="text" name="name" class="form-control" required></div>
                    <div class="form-group"><label>নাম (বাংলা)</label><input type="text" name="name_bn" class="form-control"></div>
                    <div class="form-group"><label>কোড</label><input type="text" name="code" class="form-control"></div>
                    <div class="form-group">
                        <label>শ্রেণি</label>
                        <select name="class_id" class="form-control select2">
                            <option value="">সব শ্রেণির জন্য</option>
                            @foreach($classes as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>বিভাগ</label>
                        <select name="group_id" class="form-control select2">
                            <option value="">সব বিভাগ</option>
                            @foreach($groups as $g)<option value="{{ $g->id }}">{{ $g->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group"><label>পূর্ণ নম্বর *</label><input type="number" name="full_marks" class="form-control" value="100" required></div>
                        <div class="col-6 form-group"><label>পাস নম্বর *</label><input type="number" name="pass_marks" class="form-control" value="33" required></div>
                    </div>
                </div>
                <div class="card-footer"><button class="btn btn-primary"><i class="fas fa-plus"></i> যোগ করুন</button></div>
            </form>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-striped datatable">
                    <thead><tr><th>নাম</th><th>শ্রেণি</th><th>বিভাগ</th><th>মার্ক</th><th></th></tr></thead>
                    <tbody>
                        @foreach($subjects as $s)
                        <tr>
                            <td><b>{{ $s->name }}</b> @if($s->code)<small class="text-muted">({{ $s->code }})</small>@endif</td>
                            <td>{{ $s->schoolClass->name ?? 'সব' }}</td>
                            <td>{{ $s->group->name ?? 'সব' }}</td>
                            <td>{{ $s->pass_marks }}/{{ $s->full_marks }}</td>
                            <td>
                                <form action="{{ route('admin.academic.subjects.destroy', $s) }}" method="POST" onsubmit="return confirm('নিশ্চিত?')">
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
