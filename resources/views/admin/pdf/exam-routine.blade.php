<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>পরীক্ষার রুটিন</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; padding: 15px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 8px; margin-bottom: 15px; }
        .header h2 { margin: 0; color: #2c3e50; }
        .header p { margin: 2px 0; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        table th, table td { border: 1px solid #333; padding: 6px; font-size: 11px; }
        table th { background: #eee; text-align: center; }
        table td.center { text-align: center; }
        .info { margin-bottom: 10px; }
        .signature { margin-top: 50px; }
        .signature td { text-align: center; border: none; padding-top: 30px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $settings->institute_name ?? 'Universal School' }}</h2>
        <p>{{ $settings->address ?? '' }} @if($settings && $settings->phone) | {{ $settings->phone }} @endif</p>
        <h4>{{ $exam->name }} — পরীক্ষার সময়সূচি</h4>
    </div>

    <table class="info">
        <tr>
            <td><b>শ্রেণি:</b> {{ $class->name }} @if($section) ({{ $section->name }}) @endif</td>
            <td><b>শিক্ষাবর্ষ:</b> {{ $exam->academicYear->name ?? '-' }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th width="30">#</th>
                <th>তারিখ</th>
                <th>বার</th>
                <th>বিষয়</th>
                <th>সময়</th>
                <th>রুম</th>
            </tr>
        </thead>
        <tbody>
            @foreach($routines as $i => $r)
            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td class="center">{{ $r->exam_date->format('d-m-Y') }}</td>
                <td class="center">{{ $r->exam_date->format('l') }}</td>
                <td>{{ $r->subject->name ?? '-' }}</td>
                <td class="center">
                    {{ \Carbon\Carbon::parse($r->start_time)->format('h:i A') }} - 
                    {{ \Carbon\Carbon::parse($r->end_time)->format('h:i A') }}
                </td>
                <td class="center">{{ $r->room_no ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="signature">
        <tr>
            <td>শ্রেণি শিক্ষক</td>
            <td>পরীক্ষা নিয়ন্ত্রক</td>
            <td>প্রধান শিক্ষক</td>
        </tr>
    </table>

    <p style="text-align:center; margin-top:20px; font-size:9px;">Generated on {{ now()->format('d M, Y H:i') }}</p>
</body>
</html>