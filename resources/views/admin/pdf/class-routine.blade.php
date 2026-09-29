<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>ক্লাস রুটিন</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; padding: 15px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 8px; margin-bottom: 15px; }
        .header h2 { margin: 0; }
        .day-title { background: #eee; padding: 5px; margin-top: 10px; font-weight: bold; border-left: 4px solid #007bff; }
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        table th, table td { border: 1px solid #333; padding: 5px; font-size: 10px; }
        table th { background: #f5f5f5; }
        .footer { text-align: center; margin-top: 15px; font-size: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $settings->institute_name ?? 'Universal School' }}</h2>
        <p>{{ $settings->address ?? '' }}</p>
        <h4>ক্লাস রুটিন — {{ $class->name }} @if($section) ({{ $section->name }}) @endif</h4>
    </div>

    @foreach($days as $key => $label)
        @if(isset($routines[$key]) && $routines[$key]->count() > 0)
        <div class="day-title">{{ $label }}</div>
        <table>
            <thead>
                <tr>
                    <th width="110">সময়</th>
                    <th>বিষয়</th>
                    <th width="130">শিক্ষক</th>
                    <th width="60">রুম</th>
                </tr>
            </thead>
            <tbody>
                @foreach($routines[$key] as $r)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($r->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($r->end_time)->format('h:i A') }}</td>
                    <td>{{ $r->subject->name ?? '—' }}</td>
                    <td>{{ $r->teacher->name ?? '—' }}</td>
                    <td>{{ $r->room_no ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    @endforeach

    <div class="footer">Generated on {{ now()->format('d M, Y H:i') }}</div>
</body>
</html>