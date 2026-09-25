@extends('admin.app')
@section('page-title', 'ফি ক্যাটাগরি')

@section('content')
<div class="row">
    <div class="col-md-5">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">নতুন ক্যাটাগরি</h3></div>
            <form action="{{ route('admin.fees.categories.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group"><label>নাম (English) *</label><input type="text" name="name" class="form-control" required></div>
                    <div class="form-group"><label>নাম (বাংলা)</label><input type="text" name="name_bn" class="form-control"></div>
                    <div class="form-group">
                        <label>ধরন *</label>
                        <select name="fee_type" class="form-control" required>
                            @foreach(\App\Models\FeeCategory::typeLabels() as $k=>$v)
                                <option value="{{ $k }}">{{ $v }}</option>
                            @endforeach
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
                    <thead><tr><th>নাম</th><th>ধরন</th><th>স্ট্যাটাস</th><th></th></tr></thead>
                    <tbody>
                        @foreach($categories as $c)
                        <tr>
                            <td><b>{{ $c->name }}</b><br><small>{{ $c->name_bn }}</small></td>
                            <td><span class="badge badge-info">{{ \App\Models\FeeCategory::typeLabels()[$c->fee_type] }}</span></td>
                            <td>@if($c->is_active)<span class="badge badge-success">সক্রিয়</span>@endif</td>
                            <td>
                                <form action="{{ route('admin.fees.categories.destroy', $c) }}" method="POST" onsubmit="return confirm('নিশ্চিত?')">
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
