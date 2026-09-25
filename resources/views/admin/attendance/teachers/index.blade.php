@extends('admin.app')

@section('title', 'শিক্ষক হাজিরা')
@section('page-title', 'শিক্ষক হাজিরা')

@section('content')
<div class="card card-primary">
    <form method="GET" class="form-inline p-3">
        <label class="mr-2">তারিখ:</label>
        <input type="date" name="attendance_date" value="{{ $date }}" class="form-control mr-2" onchange="this.form.submit()">
        <button class="btn btn-primary"><i class="fas fa-search"></i> দেখান</button>
    </form>
</div>

<form action="{{ route('admin.attendance.teachers.store') }}" method="POST">
    @csrf
    <input type="hidden" name="attendance_date" value="{{ $date }}">

    <div class="card card-success">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-chalkboard-teacher"></i> {{ $teachers->count() }} জন শিক্ষক — {{ \Carbon\Carbon::parse($date)->format('d M, Y') }}</h3>
            <div class="card-tools">
                <button type="button" onclick="markAllT('present')" class="btn btn-xs btn-light"><i class="fas fa-check"></i> সবাই উপস্থিত</button>
                <button type="button" onclick="markAllT('absent')" class="btn btn-xs btn-light"><i class="fas fa-times"></i> সবাই অনুপস্থিত</button>
            </div>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped">
                <thead class="thead-light">
                    <tr>
                        <th width="60">ID</th>
                        <th>নাম</th>
                        <th>পদবি</th>
                        <th width="120">ইন টাইম</th>
                        <th width="120">আউট টাইম</th>
                        <th width="350">স্ট্যাটাস</th>
                        <th>মন্তব্য</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($teachers as $t)
                        @php $ex = $existing->get($t->id); @endphp
                        <tr>
                            <td><b>{{ $t->teacher_id }}</b></td>
                            <td>{{ $t->name }}</td>
                            <td>{{ $t->designation ?? '-' }}</td>
                            <td><input type="time" name="in_time[{{ $t->id }}]" value="{{ $ex?->in_time }}" class="form-control form-control-sm"></td>
                            <td><input type="time" name="out_time[{{ $t->id }}]" value="{{ $ex?->out_time }}" class="form-control form-control-sm"></td>
                            <td>
                                @foreach(['present'=>'উপস্থিত','absent'=>'অনুপস্থিত','late'=>'বিলম্ব','leave'=>'ছুটি'] as $key=>$label)
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="t_{{ $t->id }}_{{ $key }}"
                                               name="statuses[{{ $t->id }}]" value="{{ $key }}"
                                               class="custom-control-input status-radio-t"
                                               {{ ($ex?->status ?? 'present') == $key ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="t_{{ $t->id }}_{{ $key }}">{{ $label }}</label>
                                    </div>
                                @endforeach
                            </td>
                            <td><input type="text" name="remarks[{{ $t->id }}]" value="{{ $ex?->remarks }}" class="form-control form-control-sm"></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-save"></i> সংরক্ষণ করুন</button>
            <a href="{{ route('admin.attendance.teachers.report') }}" class="btn btn-info btn-lg"><i class="fas fa-chart-bar"></i> মাসিক রিপোর্ট</a>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
function markAllT(status) {
    document.querySelectorAll('.status-radio-t[value="'+status+'"]').forEach(r => r.checked = true);
}
</script>
@endpush
