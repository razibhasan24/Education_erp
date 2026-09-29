<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>প্রবেশপত্র - বাল্ক</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; padding: 10px; }
        .page-break { page-break-after: always; }
        .card { border: 2px solid #000; padding: 12px; margin-bottom: 20px; }
        .header { text-align: center; border-bottom: 2px dashed #333; padding-bottom: 6px; margin-bottom: 8px; }
        .header h2 { margin: 0; font-size: 14px; }
        .header h4 { margin: 3px 0; color: #444; font-size: 12px; }
        .photo-box { float: right; width: 70px; height: 85px; border: 1px solid #333; text-align: center; padding-top: 28px; font-size: 8px; }
        .info-row { margin-bottom: 4px; }
        .info-row .label { display: inline-block; width: 90px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table th, table td { border: 1px solid #333; padding: 3px; font-size: 8px; }
        table th { background: #eee; }
        .signature { margin-top: 30px; }
        .signature td { text-align: center; padding-top: 22px; border: none; border-top: 1px solid #333; font-size: 8px; }
    </style>
</head>
<body>
    @foreach($students as $idx => $student)
    <div class="{{ $idx < count($students) - 1 ? 'page-break' : '' }}">
        <div class="card">
            <div class="header">
                <h2>{{ $settings->institute_name ?? 'Universal School' }}</h2>
                <p style="margin:2px 0; font-size:8px;">{{ $settings->address ?? '' }}</p>
                <h4>প্রবেশপত্র</h4>
                <p style="margin:2px 0;"><b>{{ $exam->name }}</b></p>
            </div>

            <div class="photo-box">ছবি</div>

            <div class="info-row"><span class="label">নাম:</span> <b>{{ $student->name }}</b></div>
            <div class="info-row"><span class="label">ID:</span> {{ $student->student_id }}</div>
            <div class="info-row"><span class="label">শ্রেণি:</span> {{ $class->name }} @if($section) ({{ $section->name }}) @endif</div>
            <div class="info-row"><span class="label">রোল:</span> <b>{{ $student->roll_number ?? '-' }}</b></div>
            <div class="info-row"><span class="label">পিতা:</span> {{ $student->father_name }}</div>
            <div class="info-row"><span class="label">মাতা:</span> {{ $student->mother_name }}</div>

            @if($routines->count())
            <table>
                <thead>
                    <tr><th>#</th><th>তারিখ</th><th>বিষয়</th><th>সময়</th><th>রুম</th></tr>
                </thead>
                <tbody>
                    @foreach($routines as $i => $r)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $r->exam_date->format('d-m') }}</td>
                        <td>{{ $r->subject->name ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($r->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($r->end_time)->format('h:i A') }}</td>
                        <td>{{ $r->room_no ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif

            <table class="signature">
                <tr>
                    <td>শ্রেণি শিক্ষক</td>
                    <td>পরীক্ষা নিয়ন্ত্রক</td>
                    <td>প্রধান শিক্ষক</td>
                </tr>
            </table>
        </div>
    </div>
    @endforeach
</body>
</html>