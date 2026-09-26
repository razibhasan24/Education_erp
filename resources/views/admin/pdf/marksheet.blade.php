<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>মার্কশিট</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; padding: 15px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 8px; margin-bottom: 12px; }
        .header h2 { margin: 0; }
        .header p { margin: 2px 0; font-size: 11px; }
        .info td { padding: 3px 8px; font-size: 11px; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data th, table.data td { border: 1px solid #333; padding: 5px; font-size: 11px; text-align: center; }
        table.data th { background: #eee; }
        table.data td.subject { text-align: left; }
        .grade-box { border: 1px solid #333; padding: 6px; margin-top: 10px; font-size: 11px; }
        .signature { margin-top: 50px; font-size: 10px; }
        .signature td { text-align: center; padding-top: 30px; border-top: 1px solid #333; }
    </style>
</head>
<body>
    <div class="header">
        <h2>ইউনিভার্সাল স্কুল ম্যানেজমেন্ট সিস্টেম</h2>
        <p>প্রতিষ্ঠানের নাম, ঠিকানা, ফোন</p>
        <h4>{{ $exam->name }} — একাডেমিক ট্রান্সক্রিপ্ট</h4>
    </div>

    <table class="info" width="100%">
        <tr>
            <td><b>নাম:</b> {{ $student->name }}</td>
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
    </table>

    <table class="data">
        <thead>
            <tr>
                <th width="30">#</th>
                <th>বিষয়</th>
                <th width="60">পূর্ণ</th>
                <th width="60">পাস</th>
                <th width="60">প্রাপ্ত</th>
                <th width="50">গ্রেড</th>
                <th width="50">GPA</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subjectResults as $i => $r)
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

    <table class="info" width="100%" style="margin-top:10px;">
        <tr>
            <td>
                <div class="grade-box">
                    <b>ফলাফল:</b> {{ $overall['failed'] ? 'ফেল' : 'উত্তীর্ণ' }}<br>
                    <b>GPA:</b> {{ number_format($overall['gpa'], 2) }}<br>
                    <b>গ্রেড:</b> {{ $overall['grade'] }}<br>
                    <b>গড়:</b> {{ $overall['average_percent'] }}%
                </div>
            </td>
        </tr>
    </table>

    <table class="signature" width="100%">
        <tr>
            <td>শ্রেণি শিক্ষক</td>
            <td>পরীক্ষা নিয়ন্ত্রক</td>
            <td>প্রধান শিক্ষক</td>
        </tr>
    </table>

    <p style="text-align:center; font-size:9px; margin-top:20px;">Generated on {{ now()->format('d M, Y H:i') }}</p>
</body>
</html>
