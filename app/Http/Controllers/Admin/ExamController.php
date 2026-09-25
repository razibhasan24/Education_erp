<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Exam;
use App\Models\ExamSubject;
use App\Models\Group;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index()
    {
        $exams = Exam::with('academicYear')->latest()->get();
        return view('admin.exams.index', compact('exams'));
    }

    public function create()
    {
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        return view('admin.exams.create', compact('academicYears'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'name_bn' => 'nullable|string|max:150',
            'exam_type' => 'required|in:class_test,monthly,half_yearly,annual,model_test,admission',
            'academic_year_id' => 'required|exists:academic_years,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
        ]);

        $exam = Exam::create($data);

        return redirect()->route('admin.exams.subjects', $exam)
            ->with('success', 'পরীক্ষা তৈরি হয়েছে। এখন বিষয় ও নম্বর যোগ করুন।');
    }

    public function show(Exam $exam)
    {
        $exam->load(['examSubjects.schoolClass', 'examSubjects.subject', 'examSubjects.group']);
        return view('admin.exams.show', compact('exam'));
    }

    public function edit(Exam $exam)
    {
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        return view('admin.exams.edit', compact('exam', 'academicYears'));
    }

    public function update(Request $request, Exam $exam)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'name_bn' => 'nullable|string|max:150',
            'exam_type' => 'required|in:class_test,monthly,half_yearly,annual,model_test,admission',
            'academic_year_id' => 'required|exists:academic_years,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'is_published' => 'nullable|boolean',
        ]);
        $data['is_published'] = $request->boolean('is_published');
        $exam->update($data);

        return redirect()->route('admin.exams.index')->with('success', 'পরীক্ষা আপডেট হয়েছে।');
    }

    public function destroy(Exam $exam)
    {
        $exam->delete();
        return back()->with('success', 'পরীক্ষা মুছে ফেলা হয়েছে।');
    }

    /**
     * পরীক্ষার বিষয় ব্যবস্থাপনা
     */
    public function subjects(Exam $exam)
    {
        $exam->load(['examSubjects.schoolClass', 'examSubjects.subject', 'examSubjects.group']);
        $classes = SchoolClass::where('is_active', true)->orderBy('numeric_value')->get();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        $groups = Group::where('is_active', true)->get();

        return view('admin.exams.subjects', compact('exam', 'classes', 'subjects', 'groups'));
    }

    public function storeSubject(Request $request, Exam $exam)
    {
        $data = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'group_id' => 'nullable|exists:groups,id',
            'full_marks' => 'required|integer|min:1',
            'pass_marks' => 'required|integer|min:0',
            'written_marks' => 'nullable|integer|min:0',
            'mcq_marks' => 'nullable|integer|min:0',
            'practical_marks' => 'nullable|integer|min:0',
            'exam_date' => 'nullable|date',
        ]);
        $data['exam_id'] = $exam->id;
        $data['written_marks'] = $data['written_marks'] ?? 0;
        $data['mcq_marks'] = $data['mcq_marks'] ?? 0;
        $data['practical_marks'] = $data['practical_marks'] ?? 0;

        ExamSubject::updateOrCreate(
            [
                'exam_id' => $exam->id,
                'class_id' => $data['class_id'],
                'subject_id' => $data['subject_id'],
                'group_id' => $data['group_id'] ?? null,
            ],
            $data
        );

        return back()->with('success', 'বিষয় যোগ হয়েছে।');
    }

    public function deleteSubject(ExamSubject $examSubject)
    {
        $examSubject->delete();
        return back()->with('success', 'বিষয় মুছে ফেলা হয়েছে।');
    }

    /**
     * ক্লাসের সব বিষয় অটো-ইমপোর্ট (bulk)
     */
    public function bulkImportSubjects(Request $request, Exam $exam)
    {
        $classId = $request->validate(['class_id' => 'required|exists:classes,id'])['class_id'];
        $subjects = Subject::where('class_id', $classId)->where('is_active', true)->get();

        if ($subjects->isEmpty()) {
            return back()->with('error', 'এই শ্রেণিতে কোনো বিষয় নেই। আগে বিষয় তৈরি করুন।');
        }

        foreach ($subjects as $sub) {
            ExamSubject::firstOrCreate(
                [
                    'exam_id' => $exam->id,
                    'class_id' => $classId,
                    'subject_id' => $sub->id,
                    'group_id' => $sub->group_id,
                ],
                [
                    'full_marks' => $sub->full_marks,
                    'pass_marks' => $sub->pass_marks,
                ]
            );
        }

        return back()->with('success', $subjects->count() . 'টি বিষয় অটো-ইমপোর্ট হয়েছে।');
    }
}
