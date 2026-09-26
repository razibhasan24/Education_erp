<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>মানি রিসিট</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; padding: 15px; }
        .header { text-align: center; border-bottom: 2px dashed #333; padding-bottom: 8px; margin-bottom: 10px; }
        .header h2 { margin: 0; font-size: 18px; }
        .header h4 { margin: 5px 0 0 0; }
        table.info td { padding: 3px 6px; font-size: 11px; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 8px; }
        table.data th, table.data td { border: 1px solid #333; padding: 5px; font-size: 11px; }
        table.data th { background: #eee; text-align: left; }
        table.data td.amount { text-align: right; }
        .signature { margin-top: 40px; font-size: 10px; }
        .signature td { text-align: center; padding-top: 30px; border-top: 1px solid #333; }
    </style>
</head>
<body>
    <div class="header">
        <h2>ইউনিভার্সাল স্কুল ম্যানেজমেন্ট সিস্টেম</h2>
        <p>প্রতিষ্ঠানের নাম, ঠিকানা, ফোন</p>
        <h4>মানি রিসিট / Money Receipt</h4>
    </div>

    <table class="info" width="100%">
        <tr>
            <td><b>রিসিট #:</b> {{ $payment->receipt_no }}</td>
            <td align="right"><b>তারিখ:</b> {{ $payment->payment_date->format('d M, Y') }}</td>
        </tr>
        <tr>
            <td><b>শিক্ষার্থী:</b> {{ $payment->student->name ?? '-' }}</td>
            <td><b>স্টুডেন্ট আইডি:</b> {{ $payment->student->student_id ?? '-' }}</td>
        </tr>
        <tr>
            <td><b>শ্রেণি:</b> {{ $payment->student->schoolClass->name ?? '-' }} {{ $payment->student->section->name ?? '' }}</td>
            <td><b>ইনভয়েস #:</b> {{ $payment->invoice->invoice_no ?? '-' }}</td>
        </tr>
    </table>

    <table class="data">
        <thead><tr><th>বিবরণ</th><th width="100">পরিমাণ (৳)</th></tr></thead>
        <tbody>
            @foreach($payment->invoice->items ?? [] as $it)
            <tr>
                <td>{{ $it->category->name ?? '-' }}</td>
                <td class="amount">{{ number_format($it->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr><th align="right">সাব-টোটাল</th><td class="amount">{{ number_format($payment->invoice->subtotal ?? 0, 2) }}</td></tr>
            @if($payment->discount > 0)
                <tr><th align="right">ডিসকাউন্ট</th><td class="amount">- {{ number_format($payment->discount, 2) }}</td></tr>
            @endif
            @if($payment->fine > 0)
                <tr><th align="right">জরিমানা</th><td class="amount">+ {{ number_format($payment->fine, 2) }}</td></tr>
            @endif
            <tr style="background:#eee;">
                <th align="right">পরিশোধিত এই রিসিটে</th>
                <td class="amount"><b>{{ number_format($payment->amount, 2) }}</b></td>
            </tr>
            <tr><th align="right">মোট পরিশোধিত</th><td class="amount">{{ number_format($payment->invoice->paid_amount ?? 0, 2) }}</td></tr>
            <tr><th align="right">বকেয়া</th><td class="amount">{{ number_format($payment->invoice->due_amount ?? 0, 2) }}</td></tr>
        </tfoot>
    </table>

    <p style="margin-top:8px;">
        <b>মেথড:</b> {{ $payment->payment_method }}
        @if($payment->transaction_id) | <b>TrxID:</b> {{ $payment->transaction_id }} @endif
    </p>

    <table class="signature" width="100%">
        <tr><td>আদায়কারী</td><td>অনুমোদনকারী</td></tr>
    </table>

    <p style="text-align:center; font-size:9px; margin-top:15px;">এই রিসিট কম্পিউটার জেনারেটেড।</p>
</body>
</html>
