<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassRoutine;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Teacher;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ClassRoutineController extends Controller
{
    public function index(Request $request)
    {
        $classes = SchoolClass::where('is_active', true)->orderBy('numeric_value')->get();
        $sections = collect();
        $subjects = collect();
        $teachers = Teacher::where('status', 'active')->get();
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();

        $classId = $request->class_id;
        $sectionId = $request->section_id;

        if ($classId) {
            $sections = Section::where('class_id', $classId)->where('is_active', true)->get();
            $subjects = Subject::where('class_id', $classId)->where('is_active', true)->get();
        }

        // সাপ্তাহিক গ্রিড
        $routines = collect();
        if ($classId) {
            $routines = ClassRoutine::with(['subject', 'teacher'])
                ->where('class_id', $classId)
                ->when($sectionId, fn($q) => $q->where('section_id', $sectionId))
                ->orderByRaw("FIELD(day, 'saturday','sunday','monday','tuesday','wednesday','thursday','friday')")
                ->orderBy('start_time')
                ->get()
                ->groupBy('day');
        }

        return view('admin.class-routines.index', compact(
            'classes', 'sections', 'subjects', 'teachers', 'academicYears',
            'routines', 'classId', 'sectionId'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'day' => 'required|in:saturday,sunday,monday,tuesday,wednesday,thursday,friday',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'room_no' => 'nullable|string|max:30',
            'period_no' => 'nullable|string|max:20',
        ]);

        // Conflict check — একই ক্লাসে একই দিনে একই সময়ে অন্য কিছু
        $conflict = ClassRoutine::where('class_id', $data['class_id'])
            ->where('day', $data['day'])
            ->when($data['section_id'] ?? null, fn($q) => $q->where('section_id', $data['section_id']))
            ->where(function ($q) use ($data) {
                $q->whereBetween('start_time', [$data['start_time'], $data['end_time']])
                  ->orWhereBetween('end_time', [$data['start_time'], $data['end_time']])
                  ->orWhere(function ($q2) use ($data) {
                      $q2->where('start_time', '<=', $data['start_time'])
                         ->where('end_time', '>=', $data['end_time']);
                  });
            })->exists();

        if ($conflict) {
            return back()->with('error', 'এই সময়ে ইতিমধ্যে অন্য একটি পিরিয়ড আছে।')->withInput();
        }

        ClassRoutine::create($data);
        return back()->with('success', 'রুটিন যোগ হয়েছে।');
    }

    public function destroy(ClassRoutine $routine)
    {
        $routine->delete();
        return back()->with('success', 'মুছে ফেলা হয়েছে।');
    }

    public function pdf(Request $request)
    {
        $data = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
        ]);

        $class = SchoolClass::findOrFail($data['class_id']);
        $section = $data['section_id'] ? Section::find($data['section_id']) : null;

        $routines = ClassRoutine::with(['subject', 'teacher'])
            ->where('class_id', $data['class_id'])
            ->when($data['section_id'] ?? null, fn($q) => $q->where('section_id', $data['section_id']))
            ->get()
            ->groupBy('day');

        $settings = \App\Models\InstituteSetting::first();
        $days = ClassRoutine::days();

        $pdf = Pdf::loadView('admin.pdf.class-routine', compact('class', 'section', 'routines', 'settings', 'days'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download("class-routine-{$class->id}.pdf");
    }
}