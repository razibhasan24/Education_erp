<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NoticeController extends Controller
{
    public function index()
    {
        $notices = Notice::with('author')->latest()->get();
        return view('admin.notices.index', compact('notices'));
    }

    public function create()
    {
        return view('admin.notices.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'title_bn' => 'nullable|string|max:200',
            'content' => 'required|string',
            'content_bn' => 'nullable|string',
            'audience' => 'required|in:all,students,teachers,guardians,staff',
            'priority' => 'required|in:low,normal,high,urgent',
            'publish_date' => 'required|date',
            'expire_date' => 'nullable|date|after_or_equal:publish_date',
            'attachment' => 'nullable|file|max:5120',
            'is_published' => 'nullable|boolean',
            'send_sms' => 'nullable|boolean',
        ]);

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('notices', 'public');
        }
        $data['is_published'] = $request->boolean('is_published', true);
        $data['send_sms'] = $request->boolean('send_sms');
        $data['created_by'] = auth()->id();

        Notice::create($data);

        return redirect()->route('admin.notices.index')->with('success', 'নোটিশ প্রকাশিত হয়েছে।');
    }

    public function edit(Notice $notice)
    {
        return view('admin.notices.edit', compact('notice'));
    }

    public function update(Request $request, Notice $notice)
    {
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'title_bn' => 'nullable|string|max:200',
            'content' => 'required|string',
            'content_bn' => 'nullable|string',
            'audience' => 'required|in:all,students,teachers,guardians,staff',
            'priority' => 'required|in:low,normal,high,urgent',
            'publish_date' => 'required|date',
            'expire_date' => 'nullable|date|after_or_equal:publish_date',
            'attachment' => 'nullable|file|max:5120',
        ]);

        if ($request->hasFile('attachment')) {
            if ($notice->attachment) Storage::disk('public')->delete($notice->attachment);
            $data['attachment'] = $request->file('attachment')->store('notices', 'public');
        }
        $data['is_published'] = $request->boolean('is_published');
        $data['send_sms'] = $request->boolean('send_sms');

        $notice->update($data);

        return redirect()->route('admin.notices.index')->with('success', 'নোটিশ আপডেট হয়েছে।');
    }

    public function destroy(Notice $notice)
    {
        if ($notice->attachment) Storage::disk('public')->delete($notice->attachment);
        $notice->delete();
        return back()->with('success', 'নোটিশ মুছে ফেলা হয়েছে।');
    }
}
