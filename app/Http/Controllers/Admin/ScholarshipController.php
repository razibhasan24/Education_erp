<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\FeeCategory;
use App\Models\Scholarship;
use App\Models\ScholarshipApplication;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScholarshipController extends Controller
{
    public function index()
    {
        $scholarships = Scholarship::with('feeCategory')->withCount('applications')->latest()->get();
        $categories = FeeCategory::where('is_active', true)->get();
        return view('admin.scholarships.index', compact('scholarships', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'code' => 'required|string|max:30|unique:scholarships,code',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'applicable_to' => 'required|in:tuition,all_fees,specific_category',
            'fee_category_id' => 'nullable|exists:fee_categories,id',
            'criteria_type' => 'required|string',
            'min_gpa' => 'nullable|numeric|min:0|max:5',
            'max_income' => 'nullable|numeric|min:0',
            'max_recipients' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
        ]);
        Scholarship::create($data);
        return back()->with('success', 'বৃত্তি যোগ হয়েছে।');
    }

    public function update(Request $request, Scholarship $scholarship)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'value' => 'required|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $scholarship->update($data);
        return back()->with('success', 'আপডেট হয়েছে।');
    }

    public function destroy(Scholarship $scholarship)
    {
        $scholarship->delete();
        return back()->with('success', 'মুছে ফেলা হয়েছে।');
    }

    public function applications(Request $request)
    {
        $query = ScholarshipApplication::with(['scholarship', 'student.schoolClass', 'student.section', 'academicYear', 'approvedBy']);

        if ($request->filled('scholarship_id')) $query->where('scholarship_id', $request->scholarship_id);
        if ($request->filled('status')) $query->where('status', $request->status);

        $applications = $query->latest()->paginate(50);
        $scholarships = Scholarship::where('is_active', true)->get();
        $students = Student::where('status', 'active')->orderBy('name')->get();
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();

        return view('admin.scholarships.applications', compact('applications', 'scholarships', 'students', 'academicYears'));
    }

    public function storeApplication(Request $request)
    {
        $data = $request->validate([
            'scholarship_id' => 'required|exists:scholarships,id',
            'student_id' => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'reason' => 'nullable|string',
        ]);

        $scholarship = Scholarship::find($data['scholarship_id']);

        // Check eligibility
        $student = Student::find($data['student_id']);

        // Amount কত হবে সেটা requested_amount এ
        $data['requested_amount'] = $scholarship->type === 'percentage' ? $scholarship->value : $scholarship->value;
        $data['status'] = 'pending';

        ScholarshipApplication::create($data);
        return back()->with('success', 'আবেদন জমা হয়েছে।');
    }

    public function approve(Request $request, ScholarshipApplication $application)
    {
        $data = $request->validate([
            'approved_amount' => 'required|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);

        $application->update([
            'approved_amount' => $data['approved_amount'],
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'remarks' => $data['remarks'] ?? null,
        ]);

        return back()->with('success', 'অনুমোদন হয়েছে।');
    }

    public function reject(Request $request, ScholarshipApplication $application)
    {
        $application->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'remarks' => $request->remarks,
        ]);

        return back()->with('success', 'বাতিল করা হয়েছে।');
    }

    /**
     * একটি ক্লাসের সব শিক্ষার্থীর জন্য scholarship যোগ
     */
    public function bulkApply(Request $request)
    {
        $data = $request->validate([
            'scholarship_id' => 'required|exists:scholarships,id',
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        $students = Student::where('class_id', $data['class_id'])
            ->when($data['section_id'], fn($q) => $q->where('section_id', $data['section_id']))
            ->where('status', 'active')
            ->get();

        $scholarship = Scholarship::find($data['scholarship_id']);
        $created = 0;

        foreach ($students as $student) {
            $exists = ScholarshipApplication::where('scholarship_id', $scholarship->id)
                ->where('student_id', $student->id)
                ->where('academic_year_id', $data['academic_year_id'])
                ->exists();

            if ($exists) continue;

            ScholarshipApplication::create([
                'scholarship_id' => $scholarship->id,
                'student_id' => $student->id,
                'academic_year_id' => $data['academic_year_id'],
                'requested_amount' => $scholarship->value,
                'status' => 'pending',
            ]);
            $created++;
        }

        return back()->with('success', "{$created}টি আবেদন তৈরি হয়েছে।");
    }
}