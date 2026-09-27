<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Notifications\BroadcastNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()->paginate(20);
        return view('admin.notifications.index', compact('notifications'));
    }

    public function create()
    {
        return view('admin.notifications.create');
    }

    public function send(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'message' => 'required|string',
            'audience' => 'required|in:all,students,teachers,guardians,admins,custom',
            'user_ids' => 'nullable|array',
            'send_sms' => 'nullable|boolean',
        ]);

        $userIds = match ($data['audience']) {
            'students' => Student::where('status', 'active')->pluck('user_id')->filter()->toArray(),
            'teachers' => Teacher::where('status', 'active')->pluck('user_id')->filter()->toArray(),
            'guardians' => Guardian::where('status', 'active')->pluck('user_id')->filter()->toArray(),
            'admins' => User::role(['Super Admin', 'Admin'])->pluck('id')->toArray(),
            'custom' => $data['user_ids'] ?? [],
            default => User::where('is_active', true)->pluck('id')->toArray(),
        };

        if (empty($userIds)) {
            return back()->with('error', 'কোনো প্রাপক পাওয়া যায়নি।');
        }

        $users = User::whereIn('id', $userIds)->get();
        $notification = new BroadcastNotification($data['title'], $data['message']);

        $sent = 0;
        foreach ($users as $user) {
            $user->notify($notification);
            $sent++;
        }

        // SMS পাঠানোর ইচ্ছা থাকলে
        if ($request->boolean('send_sms')) {
            $smsService = app(\App\Services\SmsService::class);
            foreach ($users as $user) {
                if ($user->phone) {
                    $smsService->send($user->phone, $data['title'] . ' - ' . $data['message'], null, 'notice');
                }
            }
        }

        return redirect()->route('admin.notifications.index')
            ->with('success', "{$sent} জনকে নোটিফিকেশন পাঠানো হয়েছে।");
    }

     public function markAsRead(string $id)
    {
        $notification = auth()->user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }
        return back();
    }

    public function markAllRead()
    {
        \App\Models\Notification::markAllReadFor(auth()->id());
        return back()->with('success', 'সব নোটিফিকেশন পড়া হিসেবে চিহ্নিত হয়েছে।');
    }
}