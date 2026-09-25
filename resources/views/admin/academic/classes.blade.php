@extends('admin.app')
@section('page-title', 'শ্রেণি ব্যবস্থাপনা')

@section('content')
<div class="row">
    <div class="col-md-5">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">নতুন শ্রেণি</h3></div>
            <form action="{{ route('admin.academic.classes.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group"><label>নাম (English) *</label><input type="text" name="name" class="form-control" required></div>
                    <div class="form-group"><label>নাম (বাংলা)</label><input type="text" name="name_bn" class="form-control"></div>
                    <div class="form-group"><label>ক্রমিক নম্বর *</label><input type="number" name="numeric_value" class="form-control" required></div>
                    <div class="form-group">
                        <label>শিক্ষার স্তর *</label>
                        <select name="education_level" class="form-control" required>
                            <option value="primary">প্রাথমিক</option>
                            <option value="secondary">মাধ্যমিক</option>
                            <option value="higher_secondary">উচ্চমাধ্যমিক</option>
                            <option value="madrasha">মাদ্রাসা</option>
                            <option value="other">অন্যান্য</option>
                        </select>
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
                    <thead><tr><th>ক্রম</th><th>নাম</th><th>স্তর</th><th></th></tr></thead>
                    <tbody>
                        @foreach($classes as $c)
                        <tr>
                            <td>{{ $c->numeric_value }}</td>
                            <td><b>{{ $c->name }}</b><br><small class="text-muted">{{ $c->name_bn }}</small></td>
                            <td><span class="badge badge-info">{{ $c->education_level }}</span></td>
                            <td>
                                <form action="{{ route('admin.academic.classes.destroy', $c) }}" method="POST" onsubmit="return confirm('নিশ্চিত?')">
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
