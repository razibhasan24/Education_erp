<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>হাজিরা রিপোর্ট</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; padding: 15px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 6px; margin-bottom: 10px; }
        .header h2 { margin: 0; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th, table.data td { border: 1px solid #333; padding: 5px; font-size: 10px; text-align: center; }
        table.data th { background: #eee; }
        table.data td.name { text-align: left; }
    </style>
</head>
<body>
    <div class="header">
        <h2>ইউনিভার্সাল স্কুল ম্যানেজমেন্ট সিস্টেম</h2>
        <h4>মাসিক হাজিরা রিপোর্ট — {{ $month }}/{{ $year }}</h4>
        <p>শ্রেণি: <b>{{ $class->name ?? '-' }}</b> @if($section) | শাখা: <b>{{ $section->name }}</b> @endif</p>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th>রোল</th>
                <th>নাম</th>
                <th>উপস্থিত</th>
                <th>অনুপস্থিত</th>
                <th>বিলম্ব</th>
                <th>ছুটি</th>
                <th>মোট</th>
                <th>উপস্থিতির হার</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData as $row)
            @php $rate = $row['total'] > 0 ? round(($row['present'] / $row['total']) * 100, 1) : 0; @endphp
            <tr>
                <td>{{ $row['student']->roll_number ?? '-' }}</td>
                <td class="name">{{ $row['student']->name }}</td>
                <td>{{ $row['present'] }}</td>
                <td>{{ $row['absent'] }}</td>
                <td>{{ $row['late'] }}</td>
                <td>{{ $row['leave'] }}</td>
                <td>{{ $row['total'] }}</td>
                <td>{{ $rate }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p style="text-align:center; margin-top:20px; font-size:9px;">Generated on {{ now()->format('d M, Y H:i') }}</p>
</body>
</html>
