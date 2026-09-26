<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>বকেয়া তালিকা</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; padding: 15px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 6px; margin-bottom: 10px; }
        .header h2 { margin: 0; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th, table.data td { border: 1px solid #333; padding: 5px; font-size: 10px; }
        table.data th { background: #eee; text-align: left; }
        table.data td.amount { text-align: right; }
        tfoot th { background: #f5f5f5; }
    </style>
</head>
<body>
    <div class="header">
        <h2>ইউনিভার্সাল স্কুল ম্যানেজমেন্ট সিস্টেম</h2>
        <h4>বকেয়া তালিকা — {{ now()->format('d M, Y') }}</h4>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th width="25">#</th>
                <th>ইনভয়েস #</th>
                <th>শিক্ষার্থী</th>
                <th>শ্রেণি</th>
                <th>মাস/বছর</th>
                <th>মোট</th>
                <th>পরিশোধ</th>
                <th>বকেয়া</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $i => $inv)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $inv->invoice_no }}</td>
                <td>{{ $inv->student->name ?? '-' }} ({{ $inv->student->student_id ?? '' }})</td>
                <td>{{ $inv->schoolClass->name ?? '-' }} {{ $inv->section->name ?? '' }}</td>
                <td>{{ $inv->month ? str_pad($inv->month,2,'0',STR_PAD_LEFT).'/'.$inv->year : '-' }}</td>
                <td class="amount">৳{{ number_format($inv->total_amount, 2) }}</td>
                <td class="amount">৳{{ number_format($inv->paid_amount, 2) }}</td>
                <td class="amount"><b>৳{{ number_format($inv->due_amount, 2) }}</b></td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="7" align="right">মোট বকেয়া:</th>
                <th class="amount">৳{{ number_format($totalDue, 2) }}</th>
            </tr>
        </tfoot>
    </table>

    <p style="text-align:center; margin-top:20px; font-size:9px;">Generated on {{ now()->format('d M, Y H:i') }}</p>
</body>
</html>
