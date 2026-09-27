<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Group;
use App\Models\InstituteSetting;
use App\Models\Notice;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FrontendController extends Controller
{
    public function home()
    {
        $settings = InstituteSetting::first();
        $notices = Notice::published()->whereIn('audience', ['all', 'students'])->latest('publish_date')->limit(5)->get();
        $stats = [
            'students' => Student::where('status', 'active')->count(),
            'teachers' => \App\Models\Teacher::where('status', 'active')->count(),
            'classes' => SchoolClass::where('is_active', true)->count(),
        ];
        return view('frontend.partials.home', compact('settings', 'notices', 'stats'));
    }

    public function about()
    {
        $settings = InstituteSetting::first();
        return view('frontend.partials.about', compact('settings'));
    }

    public function notices()
    {
        $settings = InstituteSetting::first();
        $notices = Notice::published()->whereIn('audience', ['all', 'students'])->latest('publish_date')->paginate(10);
        return view('frontend.partials.notices', compact('settings', 'notices'));
    }

    public function noticeShow(Notice $notice)
    {
        abort_unless($notice->is_published, 404);
        $settings = InstituteSetting::first();
        return view('frontend.partials.notice-show', compact('settings', 'notice'));
    }

    public function contact()
    {
        $settings = InstituteSetting::first();
        return view('frontend.partials.contact', compact('settings'));
    }

    public function contactSubmit(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:200',
            'message' => 'required|string|max:2000',
        ]);

        // চাইলে DB তে সেভ করুন অথবা ইমেইল পাঠান
        \Log::info('Contact form', $data);

        return back()->with('success', 'আপনার বার্তা পাঠানো হয়েছে। আমরা শীঘ্রই যোগাযোগ করব।');
    }

    public function admission()
    {
        $settings = InstituteSetting::first();
        $classes = SchoolClass::where('is_active', true)->orderBy('numeric_value')->get();
        $groups = Group::where('is_active', true)->get();
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        return view('frontend.partials.admission', compact('settings', 'classes', 'groups', 'academicYears'));
    }

    public function admissionSubmit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'name_bn' => 'nullable|string|max:150',
            'father_name' => 'required|string|max:150',
            'mother_name' => 'required|string|max:150',
            'father_phone' => 'required|string|max:20',
            'mother_phone' => 'nullable|string|max:20',
            'guardian_name' => 'nullable|string|max:150',
            'guardian_phone' => 'nullable|string|max:20',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'religion' => 'nullable|string|max:50',
            'blood_group' => 'nullable|string|max:10',
            'class_id' => 'required|exists:classes,id',
            'group_id' => 'nullable|exists:groups,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'present_address' => 'required|string',
            'email' => 'nullable|email',
            'photo' => 'nullable|image|max:2048',
            'birth_certificate' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        DB::beginTransaction();
        try {
            if ($request->hasFile('photo')) {
                $validated['photo'] = $request->file('photo')->store('students', 'public');
            }

            $validated['admission_date'] = now()->toDateString();
            $validated['admission_type'] = 'online';
            $validated['status'] = 'inactive'; // Admin approve করলে active হবে
            $validated['nid_or_birth_certificate'] = $validated['birth_certificate'] ?? null;

            $student = Student::create($validated);

            DB::commit();

            return redirect()->route('frontend.admission.success', $student->student_id)
                ->with('success', 'আবেদন জমা হয়েছে।');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function admissionSuccess(string $studentId)
    {
        $student = Student::where('student_id', $studentId)->firstOrFail();
        $settings = InstituteSetting::first();
        return view('frontend.partials.admission-success', compact('student', 'settings'));
    }
}
