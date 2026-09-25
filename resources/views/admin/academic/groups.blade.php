@extends('admin.app')
@section('page-title', 'বিভাগ (Group) ব্যবস্থাপনা')

@section('content')
<div class="row">
    <div class="col-md-5">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">নতুন বিভাগ</h3></div>
            <form action="{{ route('admin.academic.groups.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group"><label>নাম (English) *</label><input type="text" name="name" class="form-control" placeholder="Science" required></div>
                    <div class="form-group"><label>নাম (বাংলা)</label><input type="text" name="name_bn" class="form-control" placeholder="বিজ্ঞান"></div>
                </div>
                <div class="card-footer"><button class="btn btn-primary"><i class="fas fa-plus"></i> যোগ করুন</button></div>
            </form>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-striped datatable">
                    <thead><tr><th>নাম</th><th>বাংলা</th><th></th></tr></thead>
                    <tbody>
                        @foreach($groups as $g)
                        <tr>
                            <td><b>{{ $g->name }}</b></td>
                            <td>{{ $g->name_bn }}</td>
                            <td>
                                <form action="{{ route('admin.academic.groups.destroy', $g) }}" method="POST" onsubmit="return confirm('নিশ্চিত?')">
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
