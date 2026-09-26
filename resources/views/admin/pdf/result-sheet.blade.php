<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>রেজাল্ট শীট</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; padding: 10px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 6px; margin-bottom: 10px; }
        .header h2 { margin: 0; font-size: 16px; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th, table.data td { border: 1px solid #333; padding: 4px; text-align: center; }
        table.data th { background: #eee; }
        table.data td.name { text-align: left; }
        .footer { margin-top: 20px; font-size: 9px; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>ইউনিভার্সাল স্কুল ম্যানেজমেন্ট সিস্টেম</h2>
        <h4>{{ $exam->name }} — রেজাল্ট শীট</h4>
        <p>
            শ্রেণি: <b>{{ $class->name ?? '-' }}</b>
            @if($section) | শাখা: <b>{{ $section->name }}</b> @endif
            | শিক্ষাবর্ষ: <b>{{ $exam->academicYear->name ?? '-' }}</b>
        </p>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th width="30">মেধা</th>
                <th width="40">রোল</th>
                <th>নাম</th>
                @foreach($examSubjects as $es)
                    <th>{{ $es->subject->name ?? '' }}<br>({{ $es->full_marks }})</th>
                @endforeach
                <th>মোট</th>
                <th>GPA</th>
                <th>গ্রেড</th>
                <th>ফলাফল</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $i => $r)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $r['student']->roll_number ?? '-' }}</td>
                <td class="name">{{ $r['student']->name }}</td>
                @foreach($r['subjects'] as $s)
                    <td>{{ $s['marks'] }}<br><small>{{ $s['grade'] }}</small></td>
                @endforeach
                <td><b>{{ $r['overall']['total_marks'] }}</b></td>
                <td><b>{{ number_format($r['overall']['gpa'], 2) }}</b></td>
                <td>{{ $r['overall']['grade'] }}</td>
                <td>{{ $r['overall']['failed'] ? 'ফেল' : 'পাস' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generated on {{ now()->format('d M, Y H:i') }}
    </div>
</body>
</html>
