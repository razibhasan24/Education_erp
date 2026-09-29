<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherEvaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeacherEvaluationController extends Controller
{
    public function index(Request $request)
    {
        $teachers = Teacher::where('status', 'active')->withCount([
            'evaluations as total_evaluations' => fn($q) => $q->where('status', 'approved')
        ])->get();

        // প্রতিটির avg rating যোগ
        foreach ($teachers as $t) {
            $t->avg_rating = round(TeacherEvaluation::where('teacher_id', $t->id)
                ->where('status', 'approved')->avg('average_rating'), 2) ?? 0;
        }

        return view('admin.evaluations.index', compact('teachers'));
    }

    public function create(Request $request)
    {
        $teachers = Teacher::where('status', 'active')->orderBy('name')->get();
        $students = Student::where('status', 'active')->get();
        $classes = SchoolClass::where('is_active', true)->orderBy('numeric_value')->get();
        $subjects = Subject::where('is_active', true)->get();

        return view('admin.evaluations.create', compact('teachers', 'students', 'classes', 'subjects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'student_id' => 'required|exists:students,id',
            'class_id' => 'nullable|exists:classes,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'teaching_quality' => 'required|integer|min:1|max:5',
            'communication' => 'required|integer|min:1|max:5',
            'behavior' => 'required|integer|min:1|max:5',
            'punctuality' => 'required|integer|min:1|max:5',
            'knowledge' => 'required|integer|min:1|max:5',
            'comments' => 'nullable|string|max:1000',
            'is_anonymous' => 'nullable|boolean',
        ]);

        $avg = round((
            $data['teaching_quality'] +
            $data['communication'] +
            $data['behavior'] +
            $data['punctuality'] +
            $data['knowledge']
        ) / 5, 2);

        $data['average_rating'] = $avg;
        $data['is_anonymous'] = $request->boolean('is_anonymous', true);
        $data['status'] = 'approved';

        TeacherEvaluation::updateOrCreate(
            [
                'teacher_id' => $data['teacher_id'],
                'student_id' => $data['student_id'],
                'class_id' => $data['class_id'] ?? null,
                'subject_id' => $data['subject_id'] ?? null,
            ],
            $data
        );

        return redirect()->route('admin.evaluations.index')->with('success', 'মূল্যায়ন সংরক্ষণ হয়েছে।');
    }

    public function show(Teacher $teacher)
    {
        $evaluations = TeacherEvaluation::with(['student', 'subject', 'schoolClass'])
            ->where('teacher_id', $teacher->id)
            ->where('status', 'approved')
            ->latest()
            ->paginate(20);

        $summary = TeacherEvaluation::teacherAverage($teacher->id);

        return view('admin.evaluations.show', compact('teacher', 'evaluations', 'summary'));
    }

    public function destroy(TeacherEvaluation $evaluation)
    {
        $evaluation->delete();
        return back()->with('success', 'মুছে ফেলা হয়েছে।');
    }

    public function report(Request $request)
    {
        $teachers = Teacher::where('status', 'active')->get();
        $data = $teachers->map(function ($t) {
            $s = TeacherEvaluation::teacherAverage($t->id);
            return [
                'teacher' => $t,
                'overall' => $s['overall'],
                'count' => $s['count'],
                'breakdown' => $s['breakdown'],
            ];
        })->sortByDesc('overall')->values();

        return view('admin.evaluations.report', compact('data'));
    }
}