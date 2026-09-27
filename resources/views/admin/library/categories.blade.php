@extends('admin.app')
@section('page-title', 'বই ক্যাটাগরি')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">নতুন ক্যাটাগরি</h3></div>
            <form action="{{ route('admin.library.categories.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group"><label>নাম *</label><input type="text" name="name" class="form-control" required></div>
                    <div class="form-group"><label>নাম (বাংলা)</label><input type="text" name="name_bn" class="form-control"></div>
                    <div class="form-group"><label>কোড</label><input type="text" name="code" class="form-control"></div>
                </div>
                <div class="card-footer"><button class="btn btn-primary"><i class="fas fa-plus"></i> যোগ</button></div>
            </form>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-striped datatable">
                    <thead><tr><th>নাম</th><th>বাংলা</th><th>বই</th><th></th></tr></thead>
                    <tbody>
                        @foreach($categories as $c)
                        <tr>
                            <td><b>{{ $c->name }}</b></td>
                            <td>{{ $c->name_bn }}</td>
                            <td><span class="badge badge-info">{{ $c->books_count }}টি</span></td>
                            <td>
                                <form action="{{ route('admin.library.categories.destroy', $c) }}" method="POST" class="d-inline" onsubmit="return confirm('নিশ্চিত?')">
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
