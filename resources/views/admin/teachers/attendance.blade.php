@extends('teacher.app')
@section('page-title', 'হাজিরা নিন')

@section('content')
<div class="card card-primary">
    <form method="GET" class="card-body">
        <div class="row">
            <div class="col-md-4 form-group">
                <label>ক্লাস *</label>
                <select name="class_id" class="form-control" required onchange="this.form.submit()">
                    <option value="">নির্বাচন</option>
                    @foreach($myClasses as $c)
                        <option value="{{ $c->id }}" @selected($classId==$c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label>শাখা</label>
                <select name="section_id" class="form-control">
                    <option value="">সব</option>
                    @foreach($sections as $s)<option value="{{ $s->id }}" @selected($sectionId==$s->id)>{{ $s->name }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label>তারিখ</label>
                <input type="date" name="attendance_date" value="{{ $date }}" class="form-control">
            </div>
        </div>
        <button class="btn btn-primary"><i class="fas fa-search"></i> দেখান</button>
    </form>
</div>

@if($students->count())
<form action="{{ route('teacher.attendance.store') }}" method="POST">
    @csrf
    <input type="hidden" name="class_id" value="{{ $classId }}">
    <input type="hidden" name="section_id" value="{{ $sectionId }}">
    <input type="hidden" name="attendance_date" value="{{ $date }}">

    <div class="card card-success">
        <div class="card-header">
            <h3 class="card-title">{{ $students->count() }} জন — {{ \Carbon\Carbon::parse($date)->format('d M, Y') }}</h3>
            <div class="card-tools">
                <button type="button" onclick="markAll('present')" class="btn btn-xs btn-light">সবাই উপস্থিত</button>
                <button type="button" onclick="markAll('absent')" class="btn btn-xs btn-light">সবাই অনুপস্থিত</button>
            </div>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-sm table-striped">
                <thead class="thead-light"><tr><th>রোল</th><th>নাম</th><th>স্ট্যাটাস</th></tr></thead>
                <tbody>
                    @foreach($students as $student)
                        @php $st = $existing->get($student->id)?->status ?? 'present'; @endphp
                        <tr>
                            <td><b>{{ $student->roll_number ?? '-' }}</b></td>
                            <td>{{ $student->name }}</td>
                            <td>
                                @foreach(['present'=>'উপস্থিত','absent'=>'অনুপস্থিত','late'=>'বিলম্ব','leave'=>'ছুটি'] as $k=>$v)
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="st_{{ $student->id }}_{{ $k }}"
                                               name="statuses[{{ $student->id }}]" value="{{ $k }}"
                                               class="custom-control-input" {{ $st == $k ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="st_{{ $student->id }}_{{ $k }}">{{ $v }}</label>
                                    </div>
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <button class="btn btn-success btn-lg"><i class="fas fa-save"></i> সংরক্ষণ করুন</button>
        </div>
    </div>
</form>
@endif
@endsection

@push('scripts')
<script>
function markAll(s) {
    document.querySelectorAll('input[type=radio][value="'+s+'"]').forEach(r => r.checked = true);
}
</script>
@endpush
