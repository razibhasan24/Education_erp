@extends('admin.app')

@section('title', 'শিক্ষার্থী হাজিরা')
@section('page-title', 'শিক্ষার্থী হাজিরা')

@section('content')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-filter"></i> শ্রেণি ও তারিখ নির্বাচন</h3>
        </div>
        <form method="GET" action="{{ route('admin.attendance.students.index') }}">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 form-group">
                        <label>শ্রেণি *</label>
                        <select name="class_id" class="form-control select2" required onchange="this.form.submit()">
                            <option value="">নির্বাচন করুন</option>
                            @foreach ($classes as $c)
                                <option value="{{ $c->id }}" @selected($classId == $c->id)>{{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label>শাখা</label>
                        <select name="section_id" class="form-control select2">
                            <option value="">সব শাখা</option>
                            @foreach ($sections as $s)
                                <option value="{{ $s->id }}" @selected($sectionId == $s->id)>{{ $s->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label>তারিখ *</label>
                        <input type="date" name="attendance_date" value="{{ $date }}" class="form-control"
                            required>
                    </div>
                    <div class="col-md-3 form-group d-flex align-items-end">
                        <button class="btn btn-primary btn-block"><i class="fas fa-search"></i> দেখান</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @if ($students->count())
        <form action="{{ route('admin.attendance.students.store') }}" method="POST">
            @csrf
            <input type="hidden" name="class_id" value="{{ $classId }}">
            <input type="hidden" name="section_id" value="{{ $sectionId }}">
            <input type="hidden" name="attendance_date" value="{{ $date }}">

            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users"></i> {{ $students->count() }} জন শিক্ষার্থী —
                        {{ \Carbon\Carbon::parse($date)->format('d M, Y') }}
                    </h3>
                    <div class="card-tools">
                        <button type="button" onclick="markAll('present')" class="btn btn-xs btn-light">
                            <i class="fas fa-check"></i> সবাই উপস্থিত
                        </button>
                        <button type="button" onclick="markAll('absent')" class="btn btn-xs btn-light">
                            <i class="fas fa-times"></i> সবাই অনুপস্থিত
                        </button>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th width="60">রোল</th>
                                <th>নাম</th>
                                <th width="100">ছবি</th>
                                <th width="380">স্ট্যাটাস</th>
                                <th>মন্তব্য</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($students as $student)
                                @php $existingStatus = $existingAttendance->get($student->id)?->status ?? 'present'; @endphp
                                <tr>
                                    <td><b>{{ $student->roll_number ?? '-' }}</b></td>
                                    <td>
                                        {{ $student->name }}
                                        <br><small class="text-muted">{{ $student->student_id }}</small>
                                    </td>
                                    <td>
                                        <img src="{{ $student->photo ? asset('storage/' . $student->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($student->name) }}"
                                            width="40" class="rounded-circle">
                                    </td>
                                    <td>
                                        @foreach (['present' => 'উপস্থিত', 'absent' => 'অনুপস্থিত', 'late' => 'বিলম্ব', 'leave' => 'ছুটি', 'holiday' => 'বন্ধ'] as $key => $label)
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" id="st_{{ $student->id }}_{{ $key }}"
                                                    name="statuses[{{ $student->id }}]" value="{{ $key }}"
                                                    class="custom-control-input status-radio-{{ $student->id }}"
                                                    {{ $existingStatus == $key ? 'checked' : '' }}>
                                                <label class="custom-control-label"
                                                    for="st_{{ $student->id }}_{{ $key }}">{{ $label }}</label>
                                            </div>
                                        @endforeach
                                    </td>
                                    <td>
                                        <input type="text" name="remarks[{{ $student->id }}]"
                                            class="form-control form-control-sm"
                                            value="{{ $existingAttendance->get($student->id)?->remarks }}"
                                            placeholder="ঐচ্ছিক">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-save"></i> হাজিরা সংরক্ষণ করুন
                    </button>
                    <a href="{{ route('admin.attendance.students.report', ['class_id' => $classId, 'section_id' => $sectionId]) }}"
                        class="btn btn-info btn-lg">
                        <i class="fas fa-chart-bar"></i> মাসিক রিপোর্ট
                    </a>
                </div>
            </div>
        </form>
    @elseif($classId)
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> এই শ্রেণিতে কোনো সক্রিয় শিক্ষার্থী পাওয়া যায়নি।
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        function markAll(status) {
            document.querySelectorAll('input[type=radio][value="' + status + '"]').forEach(r => r.checked = true);
        }
    </script>
@endpush
