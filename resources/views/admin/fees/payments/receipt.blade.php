<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>মানি রিসিট — {{ $payment->receipt_no }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'SolaimanLipi', 'Kalpurush', Arial, sans-serif;
            background: #f4f6f9;
            padding: 20px;
        }
        .receipt-wrap {
            max-width: 800px;
            margin: auto;
        }
        .receipt {
            background: #fff;
            border: 2px solid #333;
            padding: 25px;
            border-radius: 6px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        }
        .receipt-header {
            text-align: center;
            border-bottom: 2px dashed #333;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .receipt-header h2 {
            margin: 0;
            color: #2c3e50;
            font-weight: 700;
        }
        .receipt-header p {
            margin: 3px 0;
            color: #666;
            font-size: 13px;
        }
        .receipt-header h4 {
            margin-top: 10px;
            display: inline-block;
            border: 1px solid #333;
            padding: 4px 20px;
            border-radius: 20px;
            background: #f8f9fa;
            font-size: 15px;
        }
        .info-table td {
            padding: 5px 8px;
            font-size: 14px;
            vertical-align: top;
        }
        .info-table .label {
            color: #666;
            font-weight: 600;
        }
        .items-table th,
        .items-table td {
            font-size: 13px;
            vertical-align: middle;
            padding: 8px 10px;
        }
        .items-table th {
            background: #f1f3f5;
            font-weight: 600;
        }
        .items-table td.amount,
        .items-table th.amount {
            text-align: right;
            font-family: 'Courier New', monospace;
        }
        .summary-row {
            font-size: 14px;
        }
        .total-paid {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 12px 15px;
            border-radius: 5px;
            margin-top: 12px;
        }
        .total-paid .amount {
            font-size: 20px;
            font-weight: 700;
            color: #155724;
        }
        .meta-info {
            background: #f8f9fa;
            border-left: 4px solid #17a2b8;
            padding: 10px 15px;
            margin-top: 15px;
            font-size: 13px;
        }
        .signature-area {
            margin-top: 55px;
            font-size: 13px;
        }
        .signature-area .line {
            border-top: 1px solid #333;
            padding-top: 6px;
            text-align: center;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-25deg);
            font-size: 60px;
            color: rgba(40, 167, 69, 0.08);
            font-weight: 900;
            pointer-events: none;
            white-space: nowrap;
        }
        .footer-note {
            text-align: center;
            font-size: 11px;
            color: #888;
            margin-top: 20px;
            border-top: 1px dashed #ccc;
            padding-top: 10px;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .receipt {
                box-shadow: none;
                border: 2px solid #000;
                padding: 15px;
            }
            .watermark {
                display: block;
            }
        }
    </style>
</head>
<body>

{{-- Action buttons (print এ লুকাবে) --}}
<div class="receipt-wrap no-print mb-3 text-center">
    <a href="{{ route('admin.fees.invoices.show', $payment->fee_invoice_id) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> ফিরে যান
    </a>
    <a href="{{ route('admin.pdf.receipt', $payment) }}" class="btn btn-danger">
        <i class="fas fa-file-pdf"></i> PDF ডাউনলোড
    </a>
    <button onclick="window.print()" class="btn btn-primary">
        <i class="fas fa-print"></i> প্রিন্ট
    </button>
</div>

{{-- Receipt --}}
<div class="receipt-wrap">
    <div class="receipt position-relative">

        @php
            $invoice = $payment->invoice;
            $student = $payment->student;
        @endphp

        {{-- Watermark (শুধু প্রিন্টে দেখাবে) --}}
        @if($invoice && $invoice->status === 'paid')
            <div class="watermark">PAID</div>
        @endif

        {{-- ===== Header ===== --}}
        <div class="receipt-header">
            <h2>ইউনিভার্সাল স্কুল ম্যানেজমেন্ট সিস্টেম</h2>
            <p>প্রতিষ্ঠানের নাম, ঠিকানা, ফোন নম্বর</p>
            <h4><i class="fas fa-receipt"></i> মানি রিসিট / Money Receipt</h4>
        </div>

        {{-- ===== Invoice & Student Info ===== --}}
        <table class="info-table w-100">
            <tr>
                <td width="50%">
                    <span class="label">রিসিট নম্বর:</span>
                    <b>{{ $payment->receipt_no }}</b>
                </td>
                <td width="50%" class="text-right">
                    <span class="label">তারিখ:</span>
                    <b>{{ $payment->payment_date->format('d F, Y') }}</b>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="label">শিক্ষার্থী:</span>
                    <b>{{ $student->name ?? '-' }}</b>
                </td>
                <td class="text-right">
                    <span class="label">স্টুডেন্ট আইডি:</span>
                    <b>{{ $student->student_id ?? '-' }}</b>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="label">শ্রেণি:</span>
                    <b>{{ $student->schoolClass->name ?? '-' }}</b>
                    @if($student && $student->section)
                        <b>({{ $student->section->name }})</b>
                    @endif
                </td>
                <td class="text-right">
                    <span class="label">রোল:</span>
                    <b>{{ $student->roll_number ?? '-' }}</b>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="label">পিতা/অভিভাবক:</span>
                    {{ $student->father_name ?? ($student->guardian_name ?? '-') }}
                </td>
                <td class="text-right">
                    <span class="label">মোবাইল:</span>
                    {{ $student->father_phone ?? ($student->guardian_phone ?? '-') }}
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <span class="label">ইনভয়েস নম্বর:</span>
                    <b>{{ $invoice->invoice_no ?? '-' }}</b>
                    @if($invoice && $invoice->month)
                        &nbsp;|&nbsp;
                        <span class="label">মাস/বছর:</span>
                        <b>{{ str_pad($invoice->month, 2, '0', STR_PAD_LEFT) }}/{{ $invoice->year }}</b>
                    @endif
                </td>
            </tr>
        </table>

        {{-- ===== Items Table ===== --}}
        <table class="table table-bordered items-table mt-3 mb-0">
            <thead>
                <tr>
                    <th width="50">#</th>
                    <th>বিবরণ / Description</th>
                    <th width="150" class="amount">পরিমাণ (৳)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoice->items ?? [] as $i => $item)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $item->category->name ?? '-' }}</td>
                        <td class="amount">{{ number_format($item->amount, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted">কোনো আইটেম নেই</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="summary-row">
                    <th colspan="2" class="text-right">সাব-টোটাল</th>
                    <th class="amount">{{ number_format($invoice->subtotal ?? 0, 2) }}</th>
                </tr>
                @if(($invoice->discount ?? 0) > 0)
                    <tr class="summary-row">
                        <th colspan="2" class="text-right text-info">ডিসকাউন্ট</th>
                        <th class="amount text-info">- {{ number_format($invoice->discount, 2) }}</th>
                    </tr>
                @endif
                @if(($invoice->fine ?? 0) > 0)
                    <tr class="summary-row">
                        <th colspan="2" class="text-right text-danger">জরিমানা</th>
                        <th class="amount text-danger">+ {{ number_format($invoice->fine, 2) }}</th>
                    </tr>
                @endif
                <tr class="summary-row" style="background:#f8f9fa;">
                    <th colspan="2" class="text-right">মোট বিল</th>
                    <th class="amount"><b>{{ number_format($invoice->total_amount ?? 0, 2) }}</b></th>
                </tr>
                <tr class="summary-row">
                    <th colspan="2" class="text-right text-success">মোট পরিশোধিত (এখন পর্যন্ত)</th>
                    <th class="amount text-success">
                        <b>{{ number_format($invoice->paid_amount ?? 0, 2) }}</b>
                    </th>
                </tr>
                @if(($invoice->due_amount ?? 0) > 0)
                    <tr class="summary-row">
                        <th colspan="2" class="text-right text-danger">বকেয়া</th>
                        <th class="amount text-danger">
                            <b>{{ number_format($invoice->due_amount, 2) }}</b>
                        </th>
                    </tr>
                @endif
            </tfoot>
        </table>

        {{-- ===== Amount in this Receipt (Highlighted) ===== --}}
        <div class="total-paid">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <i class="fas fa-check-circle text-success"></i>
                    <b>এই রিসিটে পরিশোধিত:</b>
                </div>
                <div class="amount">
                    ৳ {{ number_format($payment->amount, 2) }}
                </div>
            </div>
            @if($payment->discount > 0 || $payment->fine > 0)
                <div class="mt-1 small">
                    @if($payment->discount > 0)
                        <span class="text-info">ডিসকাউন্ট: ৳ {{ number_format($payment->discount, 2) }}</span>
                    @endif
                    @if($payment->fine > 0)
                        <span class="text-danger ml-3">জরিমানা: ৳ {{ number_format($payment->fine, 2) }}</span>
                    @endif
                </div>
            @endif
        </div>

        {{-- ===== Payment Meta Info ===== --}}
        <div class="meta-info">
            <div class="row">
                <div class="col-md-4 col-6">
                    <b>পেমেন্ট মেথড:</b><br>
                    <span class="badge badge-info">{{ strtoupper($payment->payment_method) }}</span>
                </div>
                @if($payment->transaction_id)
                    <div class="col-md-4 col-6">
                        <b>ট্রানজেকশন আইডি:</b><br>
                        <code>{{ $payment->transaction_id }}</code>
                    </div>
                @endif
                <div class="col-md-4 col-12">
                    <b>আদায়কারী:</b><br>
                    {{ $payment->receiver->name ?? 'সিস্টেম' }}
                </div>
            </div>
            @if($payment->remarks)
                <div class="mt-2">
                    <b>মন্তব্য:</b> {{ $payment->remarks }}
                </div>
            @endif
        </div>

        {{-- ===== Signatures ===== --}}
        <div class="signature-area">
            <div class="row">
                <div class="col-6">
                    <div class="line">আদায়কারীর স্বাক্ষর</div>
                </div>
                <div class="col-6">
                    <div class="line">অনুমোদনকারীর স্বাক্ষর</div>
                </div>
            </div>
        </div>

        <div class="footer-note">
            <i class="fas fa-info-circle"></i>
            এই রিসিট কম্পিউটার জেনারেটেড। যেকোনো প্রশ্নে প্রতিষ্ঠানের অফিসে যোগাযোগ করুন।<br>
            Generated on {{ now()->format('d M, Y \\a\\t h:i A') }}
        </div>

    </div>
</div>

</body>
</html>
