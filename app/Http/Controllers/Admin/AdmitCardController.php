<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamRoutine;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class AdmitCardController extends Controller
{
    public function index()
    {
        $exams = Exam::where('is_active', true)->latest()->get();
        $classes = SchoolClass::where('is_active', true)->orderBy('numeric_value')->get();
        return view('admin.admit-cards.index', compact('exams', 'classes'));
    }

    public function generate(Request $request)
    {
        $data = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'student_id' => 'nullable|exists:students,id',
        ]);

        $exam = Exam::with('academicYear')->findOrFail($data['exam_id']);
        $class = SchoolClass::findOrFail($data['class_id']);
        $section = $data['section_id'] ? Section::find($data['section_id']) : null;

        $students = Student::where('class_id', $data['class_id'])
            ->when($data['section_id'] ?? null, fn($q) => $q->where('section_id', $data['section_id']))
            ->when($data['student_id'] ?? null, fn($q) => $q->where('id', $data['student_id']))
            ->where('status', 'active')
            ->orderBy('roll_number')
            ->get();

        if ($students->isEmpty()) {
            return back()->with('error', 'কোনো শিক্ষার্থী পাওয়া যায়নি।');
        }

        $routines = ExamRoutine::with('subject')
            ->where('exam_id', $exam->id)
            ->where('class_id', $class->id)
            ->orderBy('exam_date')
            ->get();

        $settings = \App\Models\InstituteSetting::first();

        $pdf = Pdf::loadView('admin.pdf.admit-cards-bulk', compact(
            'exam', 'class', 'section', 'students', 'routines', 'settings'
        ));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download("admit-cards-{$class->name}-{$exam->id}.pdf");
    }

    public function single(Exam $exam, Student $student)
    {
        $exam->load('academicYear');
        $class = $student->schoolClass;
        $section = $student->section;

        $routines = ExamRoutine::with('subject')
            ->where('exam_id', $exam->id)
            ->where('class_id', $student->class_id)
            ->orderBy('exam_date')
            ->get();

        $settings = \App\Models\InstituteSetting::first();

        $pdf = Pdf::loadView('admin.pdf.admit-card-single', compact(
            'exam', 'student', 'class', 'section', 'routines', 'settings'
        ));
        $pdf->setPaper('A5', 'portrait');

        return $pdf->download("admit-card-{$student->student_id}.pdf");
    }
}