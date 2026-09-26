<?php

namespace App\Services;

use App\Models\InstituteSetting;
use App\Models\SmsLog;
use App\Models\SmsSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * একটি SMS পাঠান
     */
    public function send(string $phone, string $message, ?int $studentId = null, string $type = 'custom'): array
    {
        $phone = $this->normalizePhone($phone);

        $log = SmsLog::create([
            'phone' => $phone,
            'message' => $message,
            'type' => $type,
            'student_id' => $studentId,
            'status' => 'pending',
            'sent_by' => auth()->id(),
        ]);

        $setting = SmsSetting::current();

        if (!$setting->is_active || empty($setting->api_key)) {
            $log->update(['status' => 'failed', 'response' => 'SMS সেটিংস নিষ্ক্রিয় বা API কী নেই।']);
            return ['success' => false, 'message' => 'SMS সেটিংস কনফিগার করা হয়নি।'];
        }

        try {
            $response = match ($setting->provider) {
                'bulksmsbd' => $this->sendBulkSmsBd($setting, $phone, $message),
                'alphasms'  => $this->sendAlphaSms($setting, $phone, $message),
                'custom'    => $this->sendCustom($setting, $phone, $message),
                default     => ['success' => false, 'response' => 'Unknown provider'],
            };

            $log->update([
                'status' => $response['success'] ? 'sent' : 'failed',
                'response' => $response['response'] ?? null,
            ]);

            return $response;
        } catch (\Exception $e) {
            $log->update(['status' => 'failed', 'response' => $e->getMessage()]);
            Log::error('SMS Error: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Bulk SMS BD (https://bulksmsbd.net)
     */
    private function sendBulkSmsBd(SmsSetting $setting, string $phone, string $message): array
    {
        $url = $setting->api_url ?: 'http://bulksmsbd.net/api/smsapi';

        $response = Http::get($url, [
            'api_key' => $setting->api_key,
            'type' => 'text',
            'number' => $phone,
            'senderid' => $setting->sender_id,
            'message' => $message,
        ]);

        return [
            'success' => $response->successful(),
            'response' => $response->body(),
        ];
    }

    /**
     * Alpha SMS (https://alphasms.biz)
     */
    private function sendAlphaSms(SmsSetting $setting, string $phone, string $message): array
    {
        $url = $setting->api_url ?: 'http://alphasms.biz/index.php';

        $response = Http::get($url, [
            'app' => 'ws',
            'op' => 'pv',
            'cmd' => 'sendsms',
            'user' => $setting->api_key,
            'pwd' => $setting->sender_id,
            'sender' => $setting->sender_id,
            'text' => $message,
            'to' => $phone,
        ]);

        return [
            'success' => $response->successful(),
            'response' => $response->body(),
        ];
    }

    private function sendCustom(SmsSetting $setting, string $phone, string $message): array
    {
        $response = Http::post($setting->api_url, [
            'api_key' => $setting->api_key,
            'sender' => $setting->sender_id,
            'to' => $phone,
            'message' => $message,
        ]);

        return [
            'success' => $response->successful(),
            'response' => $response->body(),
        ];
    }

    /**
     * টেমপ্লেট ভেরিয়েবল প্রতিস্থাপন
     */
    public function parseTemplate(string $template, array $data): string
    {
        $institute = InstituteSetting::first();
        $data['institute'] = $institute->institute_name ?? 'School';

        foreach ($data as $key => $value) {
            $template = str_replace('{' . $key . '}', $value, $template);
        }
        return $template;
    }

    /**
     * হাজিরা SMS পাঠান
     */
    public function sendAttendanceSms($student, string $status, string $date): array
    {
        $setting = SmsSetting::current();
        if (!$setting->notify_attendance) {
            return ['success' => false, 'message' => 'হাজিরা SMS নোটিফিকেশন নিষ্ক্রিয়।'];
        }

        $phone = $student->father_phone ?? $student->guardian_phone;
        if (!$phone) {
            return ['success' => false, 'message' => 'মোবাইল নম্বর নেই।'];
        }

        $statusLabels = [
            'present' => 'উপস্থিত',
            'absent' => 'অনুপস্থিত',
            'late' => 'বিলম্ব',
            'leave' => 'ছুটি',
            'holiday' => 'ছুটি',
        ];

        $message = $this->parseTemplate($setting->attendance_template, [
            'student_name' => $student->name,
            'status' => $statusLabels[$status] ?? $status,
            'date' => $date,
            'class' => $student->schoolClass->name ?? '',
            'roll' => $student->roll_number ?? '',
        ]);

        return $this->send($phone, $message, $student->id, 'attendance');
    }

    /**
     * বকেয়া SMS
     */
    public function sendDueSms($student, float $dueAmount): array
    {
        $setting = SmsSetting::current();
        if (!$setting->notify_due) {
            return ['success' => false, 'message' => 'বকেয়া SMS নোটিফিকেশন নিষ্ক্রিয়।'];
        }

        $phone = $student->father_phone ?? $student->guardian_phone;
        if (!$phone) {
            return ['success' => false, 'message' => 'মোবাইল নম্বর নেই।'];
        }

        $message = $this->parseTemplate($setting->due_template, [
            'student_name' => $student->name,
            'due_amount' => number_format($dueAmount, 2),
            'class' => $student->schoolClass->name ?? '',
            'roll' => $student->roll_number ?? '',
        ]);

        return $this->send($phone, $message, $student->id, 'due');
    }

    /**
     * মোবাইল নম্বর ফরম্যাট (01XXXXXXXXX → 880XXXXXXXXX)
     */
    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '880')) {
            return $phone;
        }
        if (str_starts_with($phone, '0')) {
            return '88' . $phone;
        }
        return '880' . $phone;
    }
}