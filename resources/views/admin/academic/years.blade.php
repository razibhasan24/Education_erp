@section('page-title', 'শিক্ষাবর্ষ ব্যবস্থাপনা')

@section('content')
<div class="row">
    <div class="col-md-5">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">নতুন শিক্ষাবর্ষ</h3></div>
            <form action="{{ route('admin.academic.years.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group"><label>নাম *</label><input type="text" name="name" class="form-control" placeholder="2025" required></div>
                    <div class="form-group"><label>শুরু *</label><input type="date" name="start_date" class="form-control" required></div>
                    <div class="form-group"><label>শেষ *</label><input type="date" name="end_date" class="form-control" required></div>
                    <div class="form-check"><input type="checkbox" name="is_current" value="1" class="form-check-input" id="cur"><label for="cur" class="form-check-label">বর্তমান শিক্ষাবর্ষ</label></div>
                </div>
                <div class="card-footer"><button class="btn btn-primary"><i class="fas fa-plus"></i> যোগ করুন</button></div>
            </form>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-striped datatable">
                    <thead><tr><th>নাম</th><th>শুরু</th><th>শেষ</th><th>বর্তমান</th><th></th></tr></thead>
                    <tbody>
                        @foreach($years as $y)
                        <tr>
                            <td>{{ $y->name }}</td>
                            <td>{{ $y->start_date->format('d M, Y') }}</td>
                            <td>{{ $y->end_date->format('d M, Y') }}</td>
                            <td>@if($y->is_current)<span class="badge badge-success">হ্যাঁ</span>@endif</td>
                            <td>
                                <form action="{{ route('admin.academic.years.destroy', $y) }}" method="POST" onsubmit="return confirm('নিশ্চিত?')">
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
