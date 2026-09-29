<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>ভর্তি পরীক্ষার ফলাফল</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; padding: 15px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 8px; margin-bottom: 15px; }
        .header h2 { margin: 0; }
        table { width: 100%; border-collapse: collapse; }
        table th, table td { border: 1px solid #333; padding: 5px; font-size: 9px; }
        table th { background: #eee; text-align: center; }
        .footer { text-align: center; margin-top: 15px; font-size: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $settings->institute_name ?? 'Universal School' }}</h2>
        <p>{{ $settings->address ?? '' }}</p>
        <h4>ভর্তি পরীক্ষার ফলাফল — {{ $class->name }}</h4>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30">মেধা</th>
                <th>রোল</th>
                <th>নাম</th>
                <th>পিতা</th>
                <th width="30">বাং</th>
                <th width="30">ইং</th>
                <th width="30">গণ</th>
                <th width="30">সা.জ্ঞ</th>
                <th width="40">মোট</th>
                <th width="50">ফলাফল</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $r)
            <tr>
                <td style="text-align:center;"><b>{{ $r->merit_position }}</b></td>
                <td>{{ $r->roll_no }}</td>
                <td>{{ $r->student_name }}</td>
                <td>{{ $r->father_name }}</td>
                <td style="text-align:center;">{{ $r->bangla }}</td>
                <td style="text-align:center;">{{ $r->english }}</td>
                <td style="text-align:center;">{{ $r->math }}</td>
                <td style="text-align:center;">{{ $r->general_knowledge }}</td>
                <td style="text-align:center;"><b>{{ $r->total_marks }}</b></td>
                <td style="text-align:center;">{{ $r->status === 'passed' ? 'উত্তীর্ণ' : ($r->status === 'failed' ? 'অকৃতকার্য' : 'অপেক্ষমাণ') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">Generated on {{ now()->format('d M, Y H:i') }}</div>
</body>
</html>