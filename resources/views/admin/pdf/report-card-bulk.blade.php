<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>রিপোর্ট কার্ড - বাল্ক</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; padding: 10px; }
        .page-break { page-break-after: always; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 6px; margin-bottom: 8px; }
        .header h2 { margin: 0; font-size: 15px; }
        .header p { margin: 2px 0; font-size: 9px; }
        table.info { width: 100%; margin-bottom: 6px; }
        table.info td { padding: 2px 5px; font-size: 9px; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.data th, table.data td { border: 1px solid #333; padding: 4px; font-size: 9px; text-align: center; }
        table.data th { background: #eee; }
        table.data td.subject { text-align: left; }
        .summary { border: 2px solid #333; padding: 6px; margin-top: 8px; background: #f9f9f9; font-size: 10px; }
        .signature { margin-top: 30px; }
        .signature td { text-align: center; padding-top: 25px; border-top: 1px solid #333; font-size: 8px; }
    </style>
</head>
<body>
    @foreach($studentsData as $idx => $data)
    <div class="{{ $idx < count($studentsData) - 1 ? 'page-break' : '' }}">
        <div class="header">
            <h2>ইউনিভার্সাল স্কুল ম্যানেজমেন্ট সিস্টেম</h2>
            <p>প্রতিষ্ঠানের নাম, ঠিকানা, ফোন</p>
            <h4>{{ $exam->name }} — প্রগ্রেস রিপোর্ট</h4>
        </div>

        <table class="info">
            <tr>
                <td width="50%"><b>নাম:</b> {{ $data['student']->name }}</td>
                <td><b>স্টুডেন্ট আইডি:</b> {{ $data['student']->student_id }}</td>
            </tr>
            <tr>
                <td><b>শ্রেণি:</b> {{ $class->name ?? '' }} @if($section) ({{ $section->name }}) @endif</td>
                <td><b>রোল:</b> {{ $data['student']->roll_number ?? '-' }}</td>
            </tr>
            <tr>
                <td><b>পিতা:</b> {{ $data['student']->father_name }}</td>
                <td><b>মাতা:</b> {{ $data['student']->mother_name }}</td>
            </tr>
        </table>

        <table class="data">
            <thead>
                <tr>
                    <th width="25">#</th>
                    <th>বিষয়</th>
                    <th width="50">পূর্ণ</th>
                    <th width="50">প্রাপ্ত</th>
                    <th width="45">গ্রেড</th>
                    <th width="45">GPA</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['subjects'] as $i => $r)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td class="subject">{{ $r['subject'] }}</td>
                    <td>{{ $r['full_marks'] }}</td>
                    <td><b>{{ $r['marks'] }}</b></td>
                    <td>{{ $r['grade'] }}</td>
                    <td>{{ number_format($r['gpa'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:#eee;">
                    <th colspan="2">মোট</th>
                    <th>{{ $data['overall']['total_full_marks'] }}</th>
                    <th>{{ $data['overall']['total_marks'] }}</th>
                    <th>{{ $data['overall']['grade'] }}</th>
                    <th>{{ number_format($data['overall']['gpa'], 2) }}</th>
                </tr>
            </tfoot>
        </table>

        <table class="summary" width="100%">
            <tr>
                <td><b>ফলাফল:</b> {{ $data['overall']['failed'] ? 'ফেল' : 'উত্তীর্ণ' }}</td>
                <td><b>GPA:</b> {{ number_format($data['overall']['gpa'], 2) }}</td>
                <td><b>গ্রেড:</b> {{ $data['overall']['grade'] }}</td>
                <td><b>পজিশন:</b> {{ $data['position'] ?? '—' }}</td>
            </tr>
        </table>

        <table class="signature" width="100%">
            <tr>
                <td>শ্রেণি শিক্ষক</td>
                <td>পরীক্ষা নিয়ন্ত্রক</td>
                <td>প্রধান শিক্ষক</td>
            </tr>
        </table>
    </div>
    @endforeach
</body>
</html>
