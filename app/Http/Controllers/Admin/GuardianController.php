<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guardian;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class GuardianController extends Controller
{
    public function index()
    {
        $guardians = Guardian::with('students')->latest()->get();
        return view('admin.guardians.index', compact('guardians'));
    }

    public function create()
    {
        $students = Student::where('status', 'active')->orderBy('name')->get();
        return view('admin.guardians.create', compact('students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'name_bn' => 'nullable|string|max:150',
            'relation' => 'nullable|string|max:50',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email',
            'occupation' => 'nullable|string|max:100',
            'nid' => 'nullable|string|max:50',
            'present_address' => 'nullable|string',
            'permanent_address' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'username' => 'gdn' . time(),
                'phone' => $validated['phone'],
                'password' => Hash::make('password'),
                'user_type' => 'guardian',
                'is_active' => true,
            ]);
            $user->assignRole('Guardian');

            if ($request->hasFile('photo')) {
                $validated['photo'] = $request->file('photo')->store('guardians', 'public');
            }

            $validated['user_id'] = $user->id;
            $validated['status'] = 'active';

            $guardian = Guardian::create($validated);
            $guardian->students()->sync($validated['student_ids']);

            DB::commit();
            return redirect()->route('admin.guardians.index')
                ->with('success', 'অভিভাবক সফলভাবে যোগ হয়েছেন। ডিফল্ট পাসওয়ার্ড: password');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(Guardian $guardian)
    {
        $guardian->load('students.schoolClass', 'students.section', 'user');
        return view('admin.guardians.show', compact('guardian'));
    }

    public function edit(Guardian $guardian)
    {
        $students = Student::where('status', 'active')->orderBy('name')->get();
        return view('admin.guardians.edit', compact('guardian', 'students'));
    }

    public function update(Request $request, Guardian $guardian)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'name_bn' => 'nullable|string|max:150',
            'relation' => 'nullable|string|max:50',
            'phone' => 'required|string|max:20',
            'occupation' => 'nullable|string|max:100',
            'nid' => 'nullable|string|max:50',
            'present_address' => 'nullable|string',
            'permanent_address' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
        ]);

        if ($request->hasFile('photo')) {
            if ($guardian->photo) Storage::disk('public')->delete($guardian->photo);
            $validated['photo'] = $request->file('photo')->store('guardians', 'public');
        }

        $guardian->update($validated);
        $guardian->students()->sync($validated['student_ids']);

        return redirect()->route('admin.guardians.index')->with('success', 'আপডেট হয়েছে।');
    }

    public function destroy(Guardian $guardian)
    {
        if ($guardian->photo) Storage::disk('public')->delete($guardian->photo);
        $guardian->delete();
        return back()->with('success', 'অভিভাবক মুছে ফেলা হয়েছে।');
    }
}
