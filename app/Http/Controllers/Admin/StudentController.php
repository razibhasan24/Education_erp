<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Group;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['schoolClass', 'section', 'group', 'academicYear']);

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $students = $query->latest()->get();
        $classes = SchoolClass::where('is_active', true)->orderBy('numeric_value')->get();

        return view('admin.students.index', compact('students', 'classes'));
    }

    public function create()
    {
        return view('admin.students.create', $this->formData());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'name_bn' => 'nullable|string|max:150',
            'father_name' => 'required|string|max:150',
            'mother_name' => 'required|string|max:150',
            'father_phone' => 'nullable|string|max:20',
            'mother_phone' => 'nullable|string|max:20',
            'guardian_name' => 'nullable|string|max:150',
            'guardian_phone' => 'nullable|string|max:20',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'religion' => 'nullable|string|max:50',
            'blood_group' => 'nullable|string|max:10',
            'nid_or_birth_certificate' => 'nullable|string|max:50',
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'group_id' => 'nullable|exists:groups,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'roll_number' => 'nullable|integer',
            'present_address' => 'nullable|string',
            'permanent_address' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'admission_date' => 'required|date',
            'admission_type' => 'required|in:online,offline',
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
        ]);

        DB::beginTransaction();
        try {
            // ইউজার অ্যাকাউন্ট তৈরি (লগইন করার জন্য)
            $user = null;
            if ($request->filled('email')) {
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'username' => 'stu' . time(),
                    'phone' => $validated['phone'] ?? null,
                    'password' => Hash::make('password'),
                    'user_type' => 'student',
                    'is_active' => true,
                ]);
                $user->assignRole('Student');
            }

            if ($request->hasFile('photo')) {
                $validated['photo'] = $request->file('photo')->store('students', 'public');
            }

            $validated['user_id'] = $user?->id;
            $validated['status'] = 'active';

            Student::create($validated);

            DB::commit();
            return redirect()->route('admin.students.index')
                ->with('success', 'শিক্ষার্থী সফলভাবে ভর্তি হয়েছে। ডিফল্ট পাসওয়ার্ড: password');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'সমস্যা হয়েছে: ' . $e->getMessage());
        }
    }

    public function show(Student $student)
    {
        $student->load(['schoolClass', 'section', 'group', 'academicYear', 'user']);
        return view('admin.students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        return view('admin.students.edit', array_merge(
            $this->formData(),
            compact('student')
        ));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'name_bn' => 'nullable|string|max:150',
            'father_name' => 'required|string|max:150',
            'mother_name' => 'required|string|max:150',
            'father_phone' => 'nullable|string|max:20',
            'mother_phone' => 'nullable|string|max:20',
            'guardian_name' => 'nullable|string|max:150',
            'guardian_phone' => 'nullable|string|max:20',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'religion' => 'nullable|string|max:50',
            'blood_group' => 'nullable|string|max:10',
            'nid_or_birth_certificate' => 'nullable|string|max:50',
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'group_id' => 'nullable|exists:groups,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'roll_number' => 'nullable|integer',
            'present_address' => 'nullable|string',
            'permanent_address' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'admission_date' => 'required|date',
            'status' => 'required|in:active,inactive,passed,dropped,transferred',
        ]);

        if ($request->hasFile('photo')) {
            if ($student->photo) Storage::disk('public')->delete($student->photo);
            $validated['photo'] = $request->file('photo')->store('students', 'public');
        }

        $student->update($validated);

        return redirect()->route('admin.students.index')
            ->with('success', 'শিক্ষার্থীর তথ্য আপডেট হয়েছে।');
    }

    public function destroy(Student $student)
    {
        if ($student->photo) Storage::disk('public')->delete($student->photo);
        $student->delete();
        return redirect()->route('admin.students.index')
            ->with('success', 'শিক্ষার্থী মুছে ফেলা হয়েছে।');
    }

    private function formData(): array
    {
        return [
            'classes' => SchoolClass::where('is_active', true)->orderBy('numeric_value')->get(),
            'sections' => Section::where('is_active', true)->get(),
            'groups' => Group::where('is_active', true)->get(),
            'academicYears' => AcademicYear::orderBy('name', 'desc')->get(),
        ];
    }
}
