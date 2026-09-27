@extends('admin.app')
@section('page-title', 'ডেটাবেস ব্যাকআপ')

@section('page-actions')
    <form action="{{ route('admin.backup.create') }}" method="POST" class="d-inline">
        @csrf
        <button class="btn btn-primary btn-sm"><i class="fas fa-database"></i> নতুন ব্যাকআপ তৈরি</button>
    </form>
@endsection

@section('content')
<div class="alert alert-info">
    <i class="fas fa-info-circle"></i>
    ব্যাকআপ ফাইলগুলো <code>storage/app/backups/</code> ফোল্ডারে সংরক্ষিত হয়। নিয়মিত ব্যাকআপ নেওয়ার পরামর্শ দেওয়া হচ্ছে।
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover datatable">
            <thead><tr><th>ফাইল</th><th>সাইজ</th><th>তৈরি</th><th>অ্যাকশন</th></tr></thead>
            <tbody>
                @forelse($files as $f)
                <tr>
                    <td><i class="fas fa-file-archive text-info"></i> {{ $f['name'] }}</td>
                    <td>{{ $f['size'] }}</td>
                    <td>{{ $f['created'] }}</td>
                    <td>
                        <a href="{{ route('admin.backup.download', $f['name']) }}" class="btn btn-xs btn-info"><i class="fas fa-download"></i> ডাউনলোড</a>
                        <form action="{{ route('admin.backup.restore') }}" method="POST" class="d-inline" onsubmit="return confirm('⚠️ ডেটাবেস রিস্টোর করলে বর্তমান ডেটা মুছে যাবে! নিশ্চিত?')">
                            @csrf
                            <input type="hidden" name="filename" value="{{ $f['name'] }}">
                            <button class="btn btn-xs btn-warning"><i class="fas fa-undo"></i> রিস্টোর</button>
                        </form>
                        <form action="{{ route('admin.backup.destroy', $f['name']) }}" method="POST" class="d-inline" onsubmit="return confirm('মুছে ফেলবেন?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted">এখনো কোনো ব্যাকআপ নেই</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
