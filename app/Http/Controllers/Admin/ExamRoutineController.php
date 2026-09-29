<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamRoutine;
use App\Models\ExamSubject;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ExamRoutineController extends Controller
{
    public function index(Request $request, Exam $exam)
    {
        $classes = SchoolClass::where('is_active', true)->orderBy('numeric_value')->get();
        $selectedClass = $request->class_id;
        $routines = collect();
        $subjects = collect();

        if ($selectedClass) {
            $routines = ExamRoutine::with(['subject', 'section'])
                ->where('exam_id', $exam->id)
                ->where('class_id', $selectedClass)
                ->when($request->section_id, fn($q) => $q->where('section_id', $request->section_id))
                ->orderBy('exam_date')
                ->orderBy('start_time')
                ->get();

            $subjects = ExamSubject::with('subject')
                ->where('exam_id', $exam->id)
                ->where('class_id', $selectedClass)
                ->get();
        }

        return view('admin.exam-routines.index', compact(
            'exam', 'classes', 'routines', 'subjects', 'selectedClass'
        ));
    }

    public function store(Request $request, Exam $exam)
    {
        $data = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'exam_subject_id' => 'nullable|exists:exam_subjects,id',
            'subject_id' => 'required|exists:subjects,id',
            'exam_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'room_no' => 'nullable|string|max:30',
            'invigilator' => 'nullable|string|max:150',
            'note' => 'nullable|string',
        ]);

        $data['exam_id'] = $exam->id;
        ExamRoutine::create($data);

        return back()->with('success', 'রুটিন যোগ হয়েছে।');
    }

    public function destroy(ExamRoutine $routine)
    {
        $routine->delete();
        return back()->with('success', 'মুছে ফেলা হয়েছে।');
    }

    /**
     * বাল্ক রুটিন তৈরি — সব বিষয়ের জন্য একসাথে
     */
    public function bulkStore(Request $request, Exam $exam)
    {
        $data = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'start_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'duration_minutes_per_day' => 'required|integer|min:30|max:480',
        ]);

        $examSubjects = ExamSubject::with('subject')
            ->where('exam_id', $exam->id)
            ->where('class_id', $data['class_id'])
            ->get();

        if ($examSubjects->isEmpty()) {
            return back()->with('error', 'এই ক্লাসের জন্য কোনো বিষয় যোগ করা হয়নি।');
        }

        $created = 0;
        $examDate = \Carbon\Carbon::parse($data['start_date']);
        $gapDays = max(1, ceil($data['duration_minutes_per_day'] / 60)); // প্রতি বিষয়ে দিন

        foreach ($examSubjects as $es) {
            ExamRoutine::create([
                'exam_id' => $exam->id,
                'class_id' => $data['class_id'],
                'section_id' => $data['section_id'] ?? null,
                'exam_subject_id' => $es->id,
                'subject_id' => $es->subject_id,
                'exam_date' => $examDate->toDateString(),
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
            ]);
            $examDate->addDays(1);
            $created++;
        }

        return back()->with('success', "{$created}টি বিষয়ের রুটিন তৈরি হয়েছে।");
    }

    /**
     * PDF রুটিন
     */
    public function pdf(Request $request, Exam $exam)
    {
        $data = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
        ]);

        $class = SchoolClass::findOrFail($data['class_id']);
        $section = $data['section_id'] ? Section::find($data['section_id']) : null;

        $routines = ExamRoutine::with('subject')
            ->where('exam_id', $exam->id)
            ->where('class_id', $data['class_id'])
            ->when($data['section_id'] ?? null, fn($q) => $q->where('section_id', $data['section_id']))
            ->orderBy('exam_date')->orderBy('start_time')
            ->get();

        $exam->load('academicYear');
        $settings = \App\Models\InstituteSetting::first();

        $pdf = Pdf::loadView('admin.pdf.exam-routine', compact('exam', 'class', 'section', 'routines', 'settings'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download("exam-routine-{$exam->id}-class-{$class->id}.pdf");
    }
}