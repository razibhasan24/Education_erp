<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>প্রবেশপত্র</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; padding: 10px; }
        .card {
            border: 2px solid #000;
            padding: 15px;
            max-width: 100%;
        }
        .header { text-align: center; border-bottom: 2px dashed #333; padding-bottom: 8px; margin-bottom: 10px; }
        .header h2 { margin: 0; font-size: 16px; }
        .header h4 { margin: 3px 0; color: #444; }
        .info-row { display: flex; margin-bottom: 5px; }
        .info-row .label { width: 100px; font-weight: bold; }
        .photo-box { float: right; width: 80px; height: 95px; border: 1px solid #333; text-align: center; padding-top: 30px; font-size: 9px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        table th, table td { border: 1px solid #333; padding: 4px; font-size: 9px; }
        table th { background: #eee; }
        .signature { margin-top: 35px; }
        .signature td { text-align: center; padding-top: 25px; border-top: 1px solid #333; border: none; border-top: 1px solid #333; font-size: 9px; }
    </style>
</head>
<body>
<div class="card">
    <div class="header">
        <h2>{{ $settings->institute_name ?? 'Universal School' }}</h2>
        <p style="margin:2px 0; font-size:9px;">{{ $settings->address ?? '' }}</p>
        <h4>প্রবেশপত্র / Admit Card</h4>
        <p style="margin:2px 0;"><b>{{ $exam->name }}</b> — {{ $exam->academicYear->name ?? '' }}</p>
    </div>

    <div class="photo-box">ছবি</div>

    <div class="info-row"><div class="label">নাম:</div><div>{{ $student->name }}</div></div>
    <div class="info-row"><div class="label">স্টুডেন্ট ID:</div><div><b>{{ $student->student_id }}</b></div></div>
    <div class="info-row"><div class="label">শ্রেণি:</div><div>{{ $class->name ?? '-' }} @if($section) ({{ $section->name }}) @endif</div></div>
    <div class="info-row"><div class="label">রোল:</div><div><b>{{ $student->roll_number ?? '-' }}</b></div></div>
    <div class="info-row"><div class="label">পিতা:</div><div>{{ $student->father_name }}</div></div>
    <div class="info-row"><div class="label">মাতা:</div><div>{{ $student->mother_name }}</div></div>
    <div class="info-row"><div class="label">মোবাইল:</div><div>{{ $student->father_phone ?? '-' }}</div></div>

    @if($routines->count())
    <table>
        <thead>
            <tr>
                <th width="20">#</th>
                <th>তারিখ</th>
                <th>বিষয়</th>
                <th>সময়</th>
                <th width="50">রুম</th>
            </tr>
        </thead>
        <tbody>
            @foreach($routines as $i => $r)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $r->exam_date->format('d-m-Y') }}</td>
                <td>{{ $r->subject->name ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($r->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($r->end_time)->format('h:i A') }}</td>
                <td>{{ $r->room_no ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <p style="margin-top:10px; font-size:8px;">
        <b>নির্দেশনা:</b> পরীক্ষার ৩০ মিনিট আগে হলে উপস্থিত থাকতে হবে। প্রবেশপত্র ছাড়া পরীক্ষায় অংশ নেওয়া যাবে না।
    </p>

    <table class="signature">
        <tr>
            <td>শ্রেণি শিক্ষক</td>
            <td>পরীক্ষা নিয়ন্ত্রক</td>
            <td>প্রধান শিক্ষক</td>
        </tr>
    </table>
</div>
</body>
</html>