@extends('admin.app')
@section('page-title', 'শাখা ব্যবস্থাপনা')

@section('content')
<div class="row">
    <div class="col-md-5">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">নতুন শাখা</h3></div>
            <form action="{{ route('admin.academic.sections.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label>শ্রেণি *</label>
                        <select name="class_id" class="form-control select2" required>
                            <option value="">নির্বাচন করুন</option>
                            @foreach($classes as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="form-group"><label>শাখার নাম *</label><input type="text" name="name" class="form-control" placeholder="A" required></div>
                    <div class="form-group"><label>ধারণক্ষমতা</label><input type="number" name="capacity" class="form-control" value="50"></div>
                </div>
                <div class="card-footer"><button class="btn btn-primary"><i class="fas fa-plus"></i> যোগ করুন</button></div>
            </form>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-striped datatable">
                    <thead><tr><th>শ্রেণি</th><th>শাখা</th><th>ধারণক্ষমতা</th><th></th></tr></thead>
                    <tbody>
                        @foreach($sections as $s)
                        <tr>
                            <td>{{ $s->schoolClass->name ?? '-' }}</td>
                            <td><b>{{ $s->name }}</b></td>
                            <td>{{ $s->capacity }}</td>
                            <td>
                                <form action="{{ route('admin.academic.sections.destroy', $s) }}" method="POST" onsubmit="return confirm('নিশ্চিত?')">
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
