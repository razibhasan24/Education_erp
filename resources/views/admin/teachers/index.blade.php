@extends('admin.app')

@section('title', 'শিক্ষক তালিকা')
@section('page-title', 'শিক্ষক তালিকা')

@section('page-actions')
    <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> নতুন শিক্ষক</a>
@endsection

@section('content')
<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover datatable">
            <thead>
                <tr>
                    <th>ছবি</th>
                    <th>টিচার আইডি</th>
                    <th>নাম</th>
                    <th>পদবি</th>
                    <th>মোবাইল</th>
                    <th>ইমেইল</th>
                    <th>স্ট্যাটাস</th>
                    <th>অ্যাকশন</th>
                </tr>
            </thead>
            <tbody>
                @foreach($teachers as $t)
                <tr>
                    <td><img src="{{ $t->photo ? asset('storage/'.$t->photo) : 'https://ui-avatars.com/api/?name='.urlencode($t->name) }}" width="40" class="rounded-circle"></td>
                    <td><b>{{ $t->teacher_id }}</b></td>
                    <td>{{ $t->name }}</td>
                    <td>{{ $t->designation ?? '-' }}</td>
                    <td>{{ $t->phone }}</td>
                    <td>{{ $t->email }}</td>
                    <td><span class="badge badge-{{ $t->status=='active'?'success':'secondary' }}">{{ $t->status }}</span></td>
                    <td>
                        <a href="{{ route('admin.teachers.show', $t) }}" class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('admin.teachers.edit', $t) }}" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.teachers.destroy', $t) }}" method="POST" class="d-inline" onsubmit="return confirm('নিশ্চিত?')">
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
