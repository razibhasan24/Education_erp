<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = Teacher::latest()->get();
        return view('admin.teachers.index', compact('teachers'));
    }

    public function create()
    {
        return view('admin.teachers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'name_bn' => 'nullable|string|max:150',
            'designation' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'father_name' => 'nullable|string|max:150',
            'mother_name' => 'nullable|string|max:150',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'religion' => 'nullable|string|max:50',
            'blood_group' => 'nullable|string|max:10',
            'nid' => 'nullable|string|max:50',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email',
            'present_address' => 'nullable|string',
            'permanent_address' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'qualification' => 'nullable|string|max:200',
            'joining_date' => 'nullable|date',
            'basic_salary' => 'nullable|numeric',
            'employment_type' => 'required|in:permanent,contractual,part_time',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'username' => 'tch' . time(),
                'phone' => $validated['phone'],
                'password' => Hash::make('password'),
                'user_type' => 'teacher',
                'is_active' => true,
            ]);
            $user->assignRole('Teacher');

            if ($request->hasFile('photo')) {
                $validated['photo'] = $request->file('photo')->store('teachers', 'public');
            }

            $validated['user_id'] = $user->id;
            $validated['status'] = 'active';

            Teacher::create($validated);

            DB::commit();
            return redirect()->route('admin.teachers.index')
                ->with('success', 'শিক্ষক সফলভাবে যোগ হয়েছে। ডিফল্ট পাসওয়ার্ড: password');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(Teacher $teacher)
    {
        return view('admin.teachers.show', compact('teacher'));
    }

    public function edit(Teacher $teacher)
    {
        return view('admin.teachers.edit', compact('teacher'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'designation' => 'nullable|string|max:100',
            'phone' => 'required|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'qualification' => 'nullable|string|max:200',
            'joining_date' => 'nullable|date',
            'basic_salary' => 'nullable|numeric',
            'employment_type' => 'required|in:permanent,contractual,part_time',
            'status' => 'required|in:active,inactive,resigned,retired',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($teacher->photo) Storage::disk('public')->delete($teacher->photo);
            $validated['photo'] = $request->file('photo')->store('teachers', 'public');
        }

        $teacher->update($validated);

        return redirect()->route('admin.teachers.index')
            ->with('success', 'শিক্ষকের তথ্য আপডেট হয়েছে।');
    }

    public function destroy(Teacher $teacher)
    {
        if ($teacher->photo) Storage::disk('public')->delete($teacher->photo);
        $teacher->delete();
        return redirect()->route('admin.teachers.index')
            ->with('success', 'শিক্ষক মুছে ফেলা হয়েছে।');
    }
}
