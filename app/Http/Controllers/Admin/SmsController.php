<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\SmsLog;
use App\Models\SmsSetting;
use App\Models\Student;
use App\Services\SmsService;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    public function settings()
    {
        $setting = SmsSetting::current();
        return view('admin.sms.settings', compact('setting'));
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'provider' => 'required|in:bulksmsbd,alphasms,custom',
            'api_key' => 'nullable|string',
            'sender_id' => 'nullable|string',
            'api_url' => 'nullable|url',
            'is_active' => 'nullable|boolean',
            'notify_attendance' => 'nullable|boolean',
            'notify_result' => 'nullable|boolean',
            'notify_due' => 'nullable|boolean',
            'attendance_template' => 'nullable|string',
            'result_template' => 'nullable|string',
            'due_template' => 'nullable|string',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['notify_attendance'] = $request->boolean('notify_attendance');
        $data['notify_result'] = $request->boolean('notify_result');
        $data['notify_due'] = $request->boolean('notify_due');

        SmsSetting::current()->update($data);

        return back()->with('success', 'SMS সেটিংস আপডেট হয়েছে।');
    }

    public function compose()
    {
        $classes = SchoolClass::where('is_active', true)->orderBy('numeric_value')->get();
        return view('admin.sms.compose', compact('classes'));
    }

    public function send(Request $request, SmsService $smsService)
    {
        $validated = $request->validate([
            'recipient_type' => 'required|in:all,class,section,individual,custom',
            'class_id' => 'nullable|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'student_id' => 'nullable|exists:students,id',
            'custom_phone' => 'nullable|string',
            'message' => 'required|string|max:1000',
        ]);

        $students = collect();

        if ($validated['recipient_type'] === 'all') {
            $students = Student::where('status', 'active')->get();
        } elseif ($validated['recipient_type'] === 'class' && $validated['class_id']) {
            $students = Student::where('class_id', $validated['class_id'])
                ->where('status', 'active')->get();
        } elseif ($validated['recipient_type'] === 'section' && $validated['section_id']) {
            $students = Student::where('section_id', $validated['section_id'])
                ->where('status', 'active')->get();
        } elseif ($validated['recipient_type'] === 'individual' && $validated['student_id']) {
            $students = Student::where('id', $validated['student_id'])->get();
        } elseif ($validated['recipient_type'] === 'custom' && $validated['custom_phone']) {
            // কাস্টম নম্বর
            $phones = preg_split('/[\s,]+/', $validated['custom_phone']);
            $sent = 0;
            foreach ($phones as $phone) {
                if (empty(trim($phone))) continue;
                $result = $smsService->send(trim($phone), $validated['message']);
                if ($result['success']) $sent++;
            }
            return back()->with('success', "কাস্টম নম্বরে {$sent}টি SMS পাঠানো হয়েছে।");
        }

        if ($students->isEmpty()) {
            return back()->with('error', 'কোনো প্রাপক পাওয়া যায়নি।');
        }

        $sent = 0;
        $failed = 0;
        foreach ($students as $student) {
            $phone = $student->father_phone ?? $student->guardian_phone;
            if (!$phone) {
                $failed++;
                continue;
            }
            $message = str_replace('{student_name}', $student->name, $validated['message']);
            $result = $smsService->send($phone, $message, $student->id, 'custom');
            $result['success'] ? $sent++ : $failed++;
        }

        return back()->with('success', "SMS পাঠানো: {$sent}টি, ব্যর্থ: {$failed}টি");
    }

    public function logs(Request $request)
    {
        $query = SmsLog::with(['student']);

        if ($request->filled('type')) $query->where('type', $request->type);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('from')) $query->whereDate('created_at', '>=', $request->from);
        if ($request->filled('to')) $query->whereDate('created_at', '<=', $request->to);

        $logs = $query->latest()->limit(500)->get();

        $summary = [
            'total' => $logs->count(),
            'sent' => $logs->where('status', 'sent')->count(),
            'failed' => $logs->where('status', 'failed')->count(),
        ];

        return view('admin.sms.logs', compact('logs', 'summary'));
    }

    public function sendDueReminders(SmsService $smsService)
    {
        $students = Student::where('status', 'active')->get();
        $sent = 0;

        foreach ($students as $student) {
            $due = \App\Models\FeeInvoice::where('student_id', $student->id)->sum('due_amount');
            if ($due > 0) {
                $result = $smsService->sendDueSms($student, $due);
                if ($result['success']) $sent++;
            }
        }

        return back()->with('success', "বকেয়া SMS পাঠানো হয়েছে {$sent} জনকে।");
    }
}
