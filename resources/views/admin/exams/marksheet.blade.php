<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>মার্কশিট — {{ $student->name }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'SolaimanLipi', 'Kalpurush', Arial, sans-serif; padding: 20px; background: #f4f4f4; }
        .marksheet { max-width: 900px; margin: auto; background: #fff; padding: 30px; border: 2px solid #333; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #2c3e50; }
        .header p { margin: 2px 0; color: #555; }
        .info-table td { padding: 4px 10px; }
        .marks-table th, .marks-table td { text-align: center; vertical-align: middle; }
        .signature { margin-top: 60px; }
        .grade-box { border: 1px solid #333; padding: 10px; background: #f9f9f9; }
        @media print {
            body { background: #fff; padding: 0; }
            .marksheet { border: 2px solid #000; box-shadow: none; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
<div class="no-print mb-3 text-center">
    <a href="{{ route('admin.exams.result', [$exam, 'class_id' => $student->class_id]) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> ফিরে যান
    </a>
    <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> প্রিন্ট</button>
    <a href="{{ route('admin.pdf.marksheet', [$exam, $student]) }}" class="btn btn-danger">
    <i class="fas fa-file-pdf"></i> PDF ডাউনলোড
</a>
</div>

<div class="marksheet">
    <div class="header">
        <h2>ইউনিভার্সাল স্কুল ম্যানেজমেন্ট সিস্টেম</h2>
        <p>প্রতিষ্ঠানের নাম ও ঠিকানা এখানে বসবে</p>
        <h4 style="margin-top:10px;">{{ $exam->name }} — একাডেমিক ট্রান্সক্রিপ্ট</h4>
    </div>

    <table class="info-table mb-3">
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
        <tr>
            <td><b>শিক্ষাবর্ষ:</b> {{ $exam->academicYear->name ?? '-' }}</td>
            <td><b>তারিখ:</b> {{ now()->format('d M, Y') }}</td>
        </tr>
    </table>

    <table class="table table-bordered marks-table">
        <thead class="thead-light">
            <tr>
                <th>#</th>
                <th>বিষয়</th>
                <th>পূর্ণ নম্বর</th>
                <th>পাস নম্বর</th>
                <th>প্রাপ্ত নম্বর</th>
                <th>গ্রেড</th>
                <th>GPA</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subjectResults as $i => $r)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td class="text-left">{{ $r['exam_subject']->subject->name ?? '-' }}</td>
                    <td>{{ $r['full_marks'] }}</td>
                    <td>{{ $r['pass_marks'] }}</td>
                    <td><b>{{ $r['marks'] }}</b></td>
                    <td>{{ $r['grade'] }}</td>
                    <td>{{ number_format($r['gpa'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="bg-light">
                <th colspan="2">মোট</th>
                <th>{{ $overall['total_full_marks'] }}</th>
                <th>—</th>
                <th>{{ $overall['total_marks'] }}</th>
                <th>{{ $overall['grade'] }}</th>
                <th>{{ number_format($overall['gpa'], 2) }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="row mt-3">
        <div class="col-md-6">
            <div class="grade-box">
                <b>ফলাফল:</b>
                @if($overall['failed'])
                    <span class="text-danger"><b>ফেল</b></span>
                @else
                    <span class="text-success"><b>উত্তীর্ণ</b></span>
                @endif
                <br>
                <b>প্রাপ্ত GPA:</b> {{ number_format($overall['gpa'], 2) }}
                <br>
                <b>গ্রেড:</b> {{ $overall['grade'] }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="grade-box">
                <b>গড় (%):</b> {{ $overall['average_percent'] }}%
                <br>
                <b>অবস্থান:</b> — (রেজাল্ট শীটে দেখুন)
            </div>
        </div>
    </div>

    <div class="row signature text-center">
        <div class="col-4">
            <div style="border-top:1px solid #333; padding-top:5px;">শ্রেণি শিক্ষক</div>
        </div>
        <div class="col-4">
            <div style="border-top:1px solid #333; padding-top:5px;">পরীক্ষা নিয়ন্ত্রক</div>
        </div>
        <div class="col-4">
            <div style="border-top:1px solid #333; padding-top:5px;">প্রধান শিক্ষক</div>
        </div>
    </div>
</div>
</body>
</html>
