@extends('admin.app')
@section('page-title', 'অভিভাবক তালিকা')

@section('page-actions')
    <a href="{{ route('admin.guardians.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> নতুন অভিভাবক</a>
@endsection

@section('content')
<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover datatable">
            <thead>
                <tr><th>ছবি</th><th>আইডি</th><th>নাম</th><th>সম্পর্ক</th><th>মোবাইল</th><th>সন্তান</th><th>স্ট্যাটাস</th><th>অ্যাকশন</th></tr>
            </thead>
            <tbody>
                @foreach($guardians as $g)
                <tr>
                    <td><img src="{{ $g->photo ? asset('storage/'.$g->photo) : 'https://ui-avatars.com/api/?name='.urlencode($g->name) }}" width="40" class="rounded-circle"></td>
                    <td>{{ $g->guardian_id }}</td>
                    <td>{{ $g->name }}</td>
                    <td>{{ $g->relation ?? '-' }}</td>
                    <td>{{ $g->phone }}</td>
                    <td><span class="badge badge-info">{{ $g->students->count() }} জন</span></td>
                    <td><span class="badge badge-{{ $g->status=='active'?'success':'secondary' }}">{{ $g->status }}</span></td>
                    <td>
                        <a href="{{ route('admin.guardians.show', $g) }}" class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('admin.guardians.edit', $g) }}" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.guardians.destroy', $g) }}" method="POST" class="d-inline" onsubmit="return confirm('নিশ্চিত?')">
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
