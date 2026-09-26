<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>রিপোর্ট কার্ড</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; padding: 15px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 8px; margin-bottom: 10px; }
        .header h2 { margin: 0; }
        .header p { margin: 2px 0; font-size: 10px; }
        table.info { width: 100%; margin-bottom: 8px; }
        table.info td { padding: 3px 6px; font-size: 10px; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 8px; }
        table.data th, table.data td { border: 1px solid #333; padding: 5px; font-size: 10px; text-align: center; }
        table.data th { background: #eee; }
        table.data td.subject { text-align: left; }
        .summary { border: 2px solid #333; padding: 8px; margin-top: 10px; background: #f9f9f9; font-size: 11px; }
        .signature { margin-top: 40px; }
        .signature td { text-align: center; padding-top: 30px; border-top: 1px solid #333; font-size: 9px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>ইউনিভার্সাল স্কুল ম্যানেজমেন্ট সিস্টেম</h2>
        <p>প্রতিষ্ঠানের নাম, ঠিকানা, ফোন</p>
        <h4>{{ $exam->name }} — প্রগ্রেস রিপোর্ট / Report Card</h4>
    </div>

    <table class="info">
        <tr>
            <td width="50%"><b>নাম:</b> {{ $student->name }}</td>
            <td><b>স্টুডেন্ট আইডি:</b> {{ $student->student_id }}</td>
        </tr>
        <tr>
            <td><b>শ্রেণি:</b> {{ $student->schoolClass->name ?? '-' }} @if($student->section) ({{ $student->section->name }}) @endif</td>
            <td><b>রোল:</b> {{ $student->roll_number ?? '-' }}</td>
        </tr>
        <tr>
            <td><b>পিতা:</b> {{ $student->father_name }}</td>
            <td><b>মাতা:</b> {{ $student->mother_name }}</td>
        </tr>
        <tr>
            <td><b>শিক্ষাবর্ষ:</b> {{ $exam->academicYear->name ?? '-' }}</td>
            <td><b>তারিখ:</b> {{ now()->format('d M, Y') }}</td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th width="25">#</th>
                <th>বিষয়</th>
                <th width="55">পূর্ণ</th>
                <th width="55">পাস</th>
                <th width="55">প্রাপ্ত</th>
                <th width="45">গ্রেড</th>
                <th width="45">GPA</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subjects as $i => $r)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td class="subject">{{ $r['subject'] }}</td>
                <td>{{ $r['full_marks'] }}</td>
                <td>{{ $r['pass_marks'] }}</td>
                <td><b>{{ $r['marks'] }}</b></td>
                <td>{{ $r['grade'] }}</td>
                <td>{{ number_format($r['gpa'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background:#eee;">
                <th colspan="2">মোট</th>
                <th>{{ $overall['total_full_marks'] }}</th>
                <th>—</th>
                <th>{{ $overall['total_marks'] }}</th>
                <th>{{ $overall['grade'] }}</th>
                <th>{{ number_format($overall['gpa'], 2) }}</th>
            </tr>
        </tfoot>
    </table>

    <table class="summary" width="100%">
        <tr>
            <td><b>ফলাফল:</b> {{ $overall['failed'] ? 'ফেল' : 'উত্তীর্ণ' }}</td>
            <td><b>GPA:</b> {{ number_format($overall['gpa'], 2) }}</td>
            <td><b>গ্রেড:</b> {{ $overall['grade'] }}</td>
            <td><b>গড়:</b> {{ $overall['average_percent'] }}%</td>
            <td><b>ক্লাস পজিশন:</b> {{ $position ?? '—' }}</td>
        </tr>
    </table>

    <table class="signature" width="100%">
        <tr>
            <td>শ্রেণি শিক্ষক</td>
            <td>পরীক্ষা নিয়ন্ত্রক</td>
            <td>প্রধান শিক্ষক</td>
        </tr>
    </table>

    <p style="text-align:center; font-size:9px; margin-top:15px;">Generated on {{ now()->format('d M, Y H:i') }}</p>
</body>
</html>
