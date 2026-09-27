@extends('admin.app')
@section('page-title', 'বই তালিকা')

@section('content')
<div class="card card-primary">
    <div class="card-header"><h3 class="card-title">নতুন বই</h3></div>
    <form action="{{ route('admin.library.books.store') }}" method="POST" class="card-body">
        @csrf
        <div class="row">
            <div class="col-md-4 form-group"><label>শিরোনাম *</label><input type="text" name="title" class="form-control" required></div>
            <div class="col-md-4 form-group"><label>শিরোনাম (বাংলা)</label><input type="text" name="title_bn" class="form-control"></div>
            <div class="col-md-4 form-group"><label>লেখক</label><input type="text" name="author" class="form-control"></div>
            <div class="col-md-3 form-group"><label>প্রকাশক</label><input type="text" name="publisher" class="form-control"></div>
            <div class="col-md-3 form-group"><label>ISBN</label><input type="text" name="isbn" class="form-control"></div>
            <div class="col-md-3 form-group">
                <label>ক্যাটাগরি</label>
                <select name="book_category_id" class="form-control select2">
                    <option value="">নির্বাচন</option>
                    @foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-3 form-group"><label>মোট কপি *</label><input type="number" name="total_copies" value="1" class="form-control" required></div>
            <div class="col-md-3 form-group"><label>শেলফ নং</label><input type="text" name="shelf_no" class="form-control"></div>
            <div class="col-md-3 form-group"><label>দাম (৳)</label><input type="number" step="0.01" name="price" class="form-control"></div>
            <div class="col-md-6 d-flex align-items-end">
                <button class="btn btn-primary"><i class="fas fa-plus"></i> বই যোগ</button>
            </div>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" class="form-inline">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="শিরোনাম/লেখক/ISBN" class="form-control form-control-sm mr-2">
            <select name="category_id" class="form-control form-control-sm mr-2">
                <option value="">সব ক্যাটাগরি</option>
                @foreach($categories as $c)<option value="{{ $c->id }}" @selected(request('category_id')==$c->id)>{{ $c->name }}</option>@endforeach
            </select>
            <button class="btn btn-sm btn-info"><i class="fas fa-search"></i> খুঁজুন</button>
        </form>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-hover datatable">
            <thead><tr><th>শিরোনাম</th><th>লেখক</th><th>ক্যাটাগরি</th><th>মোট/উপলব্ধ</th><th>শেলফ</th><th></th></tr></thead>
            <tbody>
                @foreach($books as $b)
                <tr>
                    <td><b>{{ $b->title }}</b>@if($b->title_bn)<br><small class="text-muted">{{ $b->title_bn }}</small>@endif</td>
                    <td>{{ $b->author ?? '-' }}</td>
                    <td>{{ $b->category->name ?? '-' }}</td>
                    <td><span class="badge badge-{{ $b->available_copies>0 ? 'success':'danger' }}">{{ $b->available_copies }}/{{ $b->total_copies }}</span></td>
                    <td>{{ $b->shelf_no ?? '-' }}</td>
                    <td>
                        <form action="{{ route('admin.library.books.destroy', $b) }}" method="POST" class="d-inline" onsubmit="return confirm('নিশ্চিত?')">
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
@endsection
