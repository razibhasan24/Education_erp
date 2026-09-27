@extends('admin.app')
@section('page-title', 'বই ইস্যু / রিটার্ন')

@section('content')
<div class="card card-primary">
    <div class="card-header"><h3 class="card-title">নতুন ইস্যু</h3></div>
    <form action="{{ route('admin.library.issues.store') }}" method="POST" class="card-body">
        @csrf
        <div class="row">
            <div class="col-md-3 form-group">
                <label>বই *</label>
                <select name="book_id" class="form-control select2" required>
                    <option value="">নির্বাচন</option>
                    @foreach($books as $b)<option value="{{ $b->id }}">{{ $b->title }} ({{ $b->available_copies }} available)</option>@endforeach
                </select>
            </div>
            <div class="col-md-2 form-group">
                <label>সদস্যের ধরন *</label>
                <select name="member_type" class="form-control" required>
                    <option value="student">শিক্ষার্থী</option>
                    <option value="teacher">শিক্ষক</option>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label>সদস্য *</label>
                <select name="member_id" class="form-control select2" required>
                    <option value="">নির্বাচন</option>
                    @foreach($students as $s)<option value="{{ $s->id }}">{{ $s->name }} ({{ $s->student_id }})</option>@endforeach
                    @foreach($teachers as $t)<option value="{{ $t->id }}">{{ $t->name }} - {{ $t->teacher_id }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-2 form-group"><label>ইস্যু তারিখ *</label><input type="date" name="issue_date" value="{{ date('Y-m-d') }}" class="form-control" required></div>
            <div class="col-md-2 form-group"><label>ফেরতের শেষ তারিখ *</label><input type="date" name="due_date" value="{{ date('Y-m-d', strtotime('+14 days')) }}" class="form-control" required></div>
        </div>
        <button class="btn btn-primary"><i class="fas fa-book"></i> ইস্যু করুন</button>
    </form>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover datatable">
            <thead><tr><th>Issue #</th><th>বই</th><th>সদস্য</th><th>তারিখ</th><th>Due</th><th>Return</th><th>স্ট্যাটাস</th><th>অ্যাকশন</th></tr></thead>
            <tbody>
                @foreach($issues as $i)
                <tr>
                    <td><b>{{ $i->issue_no }}</b></td>
                    <td>{{ $i->book->title ?? '-' }}</td>
                    <td>{{ $i->student->name ?? ($i->teacher->name ?? '-') }}</td>
                    <td>{{ $i->issue_date->format('d M, Y') }}</td>
                    <td>{{ $i->due_date->format('d M, Y') }}</td>
                    <td>{{ $i->return_date?->format('d M, Y') ?? '-' }}</td>
                    <td>
                        @if($i->status === 'issued')<span class="badge badge-warning">ইস্যুকৃত</span>
                        @elseif($i->status === 'returned')<span class="badge badge-success">ফেরত</span>
                        @else<span class="badge badge-danger">{{ $i->status }}</span>@endif
                    </td>
                    <td>
                        @if($i->status === 'issued')
                        <form action="{{ route('admin.library.issues.return', $i) }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="return_date" value="{{ date('Y-m-d') }}">
                            <input type="hidden" name="status" value="returned">
                            <button class="btn btn-xs btn-success" onclick="return confirm('ফেরত নিশ্চিত?')"><i class="fas fa-undo"></i> ফেরত</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
