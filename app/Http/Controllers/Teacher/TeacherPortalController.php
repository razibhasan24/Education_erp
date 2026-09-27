<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassSubject;
use App\Models\Exam;
use App\Models\ExamSubject;
use App\Models\Mark;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeacherPortalController extends Controller
{
    private function teacher()
    {
        return auth()->user()->teacher;
    }

    public function dashboard()
    {
        $teacher = $this->teacher();
        abort_unless($teacher, 403, 'শিক্ষক প্রোফাইল নেই।');

        $today = now()->toDateString();

        $stats = [
            'assigned_classes' => ClassSubject::where('teacher_id', $teacher->id)->distinct('class_id')->count('class_id'),
            'assigned_subjects' => ClassSubject::where('teacher_id', $teacher->id)->distinct('subject_id')->count('subject_id'),
            'today_attendance' => StudentAttendance::where('recorded_by', auth()->id())->where('attendance_date', $today)->count(),
        ];

        $assignments = ClassSubject::with(['schoolClass', 'section', 'subject'])
            ->where('teacher_id', $teacher->id)
            ->get();

        return view('teacher.dashboard', compact('teacher', 'stats', 'assignments'));
    }

    /**
     * শিক্ষক নিজে হাজিরা নিতে পারবে (শুধু তার ক্লাসের জন্য)
     */
    public function attendance(Request $request)
    {
        $teacher = $this->teacher();
        $myClasses = ClassSubject::with('schoolClass')
            ->where('teacher_id', $teacher->id)
            ->distinct('class_id')
            ->get()
            ->pluck('schoolClass')
            ->unique('id');

        $sections = collect();
        $students = collect();
        $existing = collect();

        $classId = $request->class_id;
        $sectionId = $request->section_id;
        $date = $request->attendance_date ?? now()->toDateString();

        if ($classId) {
            $sections = Section::where('class_id', $classId)->where('is_active', true)->get();
        }

        if ($classId) {
            $students = Student::where('class_id', $classId)
                ->when($sectionId, fn($q) => $q->where('section_id', $sectionId))
                ->where('status', 'active')
                ->orderBy('roll_number')
                ->get();

            $existing = StudentAttendance::where('attendance_date', $date)
                ->whereIn('student_id', $students->pluck('id'))
                ->get()
                ->keyBy('student_id');
        }

        return view('teacher.attendance', compact(
            'teacher', 'myClasses', 'sections', 'students', 'existing',
            'classId', 'sectionId', 'date'
        ));
    }

    public function storeAttendance(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'attendance_date' => 'required|date',
            'statuses' => 'required|array',
            'statuses.*' => 'in:present,absent,late,leave,holiday',
            'remarks' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['statuses'] as $studentId => $status) {
                StudentAttendance::updateOrCreate(
                    ['student_id' => $studentId, 'attendance_date' => $validated['attendance_date']],
                    [
                        'class_id' => $validated['class_id'],
                        'section_id' => $validated['section_id'] ?? null,
                        'status' => $status,
                        'remarks' => $validated['remarks'][$studentId] ?? null,
                        'recorded_by' => auth()->id(),
                    ]
                );
            }
            DB::commit();
            return back()->with('success', 'হাজিরা সংরক্ষণ হয়েছে।');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * মার্ক এন্ট্রি (শিক্ষক নিজের বিষয়ে)
     */
    public function marks(Request $request)
    {
        $teacher = $this->teacher();

        // শিক্ষক যেসব Exam Subject এ assigned
        $examSubjects = ExamSubject::with(['subject', 'schoolClass', 'exam'])
            ->whereHas('subject', function ($q) use ($teacher) {
                $q->whereHas('classSubjects', fn($sq) => $sq->where('teacher_id', $teacher->id));
            })
            ->orWhereHas('schoolClass.classSubjects', fn($sq) => $sq->where('teacher_id', $teacher->id))
            ->latest()
            ->get();

        $students = collect();
        $existing = collect();
        $selectedSubject = null;

        $examSubjectId = $request->exam_subject_id;
        $sectionId = $request->section_id;

        if ($examSubjectId) {
            $selectedSubject = ExamSubject::with(['subject', 'schoolClass'])->find($examSubjectId);
            if ($selectedSubject) {
                $students = Student::where('class_id', $selectedSubject->class_id)
                    ->when($sectionId, fn($q) => $q->where('section_id', $sectionId))
                    ->where('status', 'active')
                    ->orderBy('roll_number')
                    ->get();

                $existing = Mark::where('exam_subject_id', $examSubjectId)
                    ->whereIn('student_id', $students->pluck('id'))
                    ->get()
                    ->keyBy('student_id');
            }
        }

        return view('teacher.marks', compact(
            'teacher', 'examSubjects', 'students', 'existing',
            'selectedSubject', 'examSubjectId', 'sectionId'
        ));
    }

    public function storeMarks(Request $request)
    {
        $data = $request->validate([
            'exam_subject_id' => 'required|exists:exam_subjects,id',
            'written' => 'nullable|array',
            'mcq' => 'nullable|array',
            'practical' => 'nullable|array',
            'is_absent' => 'nullable|array',
        ]);

        $examSubject = ExamSubject::findOrFail($data['exam_subject_id']);

        DB::beginTransaction();
        try {
            $ids = array_unique(array_merge(
                array_keys($data['written'] ?? []),
                array_keys($data['mcq'] ?? []),
                array_keys($data['practical'] ?? []),
            ));

            foreach ($ids as $sid) {
                $written = (float)($data['written'][$sid] ?? 0);
                $mcq = (float)($data['mcq'][$sid] ?? 0);
                $practical = (float)($data['practical'][$sid] ?? 0);
                $isAbsent = !empty($data['is_absent'][$sid]);
                $total = $isAbsent ? 0 : ($written + $mcq + $practical);

                $grade = \App\Helpers\GradeHelper::getGrade($total, $examSubject->full_marks);

                Mark::updateOrCreate(
                    ['exam_subject_id' => $examSubject->id, 'student_id' => $sid],
                    [
                        'exam_id' => $examSubject->exam_id,
                        'class_id' => $examSubject->class_id,
                        'written_marks' => $written,
                        'mcq_marks' => $mcq,
                        'practical_marks' => $practical,
                        'total_marks' => $total,
                        'grade' => $isAbsent ? 'F' : $grade['grade'],
                        'gpa' => $isAbsent ? 0 : $grade['gpa'],
                        'is_absent' => $isAbsent,
                        'entered_by' => auth()->id(),
                    ]
                );
            }

            DB::commit();
            return back()->with('success', 'মার্ক সংরক্ষণ হয়েছে।');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}
