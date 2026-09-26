@extends('admin.app')
@section('page-title', 'SMS লগ')

@section('page-actions')
    <form action="{{ route('admin.sms.due-reminders') }}" method="POST" class="d-inline" onsubmit="return confirm('সব বকেয়াধারীকে SMS পাঠানো হবে?')">
        @csrf
        <button class="btn btn-warning btn-sm"><i class="fas fa-bell"></i> বকেয়া রিমাইন্ডার</button>
    </form>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4"><div class="small-box bg-info"><div class="inner"><h3>{{ $summary['total'] }}</h3><p>মোট SMS</p></div><div class="icon"><i class="fas fa-envelope"></i></div></div></div>
    <div class="col-md-4"><div class="small-box bg-success"><div class="inner"><h3>{{ $summary['sent'] }}</h3><p>সফল</p></div><div class="icon"><i class="fas fa-check"></i></div></div></div>
    <div class="col-md-4"><div class="small-box bg-danger"><div class="inner"><h3>{{ $summary['failed'] }}</h3><p>ব্যর্থ</p></div><div class="icon"><i class="fas fa-times"></i></div></div></div>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover datatable">
            <thead>
                <tr>
                    <th>তারিখ</th>
                    <th>নম্বর</th>
                    <th>শিক্ষার্থী</th>
                    <th>বার্তা</th>
                    <th>ধরন</th>
                    <th>স্ট্যাটাস</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $l)
                <tr>
                    <td>{{ $l->created_at->format('d M, Y H:i') }}</td>
                    <td>{{ $l->phone }}</td>
                    <td>{{ $l->student->name ?? '-' }}</td>
                    <td><small>{{ Str::limit($l->message, 60) }}</small></td>
                    <td><span class="badge badge-info">{{ $l->type }}</span></td>
                    <td>
                        @if($l->status == 'sent') <span class="badge badge-success">পাঠানো</span>
                        @elseif($l->status == 'failed') <span class="badge badge-danger">ব্যর্থ</span>
                        @else <span class="badge badge-warning">অপেক্ষমাণ</span> @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
