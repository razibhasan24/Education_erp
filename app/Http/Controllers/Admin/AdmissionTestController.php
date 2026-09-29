<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AdmissionTestResult;
use App\Models\SchoolClass;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdmissionTestController extends Controller
{
    public function index(Request $request)
    {
        $classes = SchoolClass::where('is_active', true)->orderBy('numeric_value')->get();

        $query = AdmissionTestResult::with(['schoolClass', 'academicYear']);
        if ($request->filled('class_id')) $query->where('class_id', $request->class_id);
        if ($request->filled('status')) $query->where('status', $request->status);

        $results = $query->orderBy('merit_position')->paginate(50);
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();

        return view('admin.admission-test.index', compact('results', 'classes', 'academicYears'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_name' => 'required|string|max:150',
            'father_name' => 'required|string|max:150',
            'mother_name' => 'nullable|string|max:150',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'class_id' => 'required|exists:classes,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'bangla' => 'required|numeric|min:0',
            'english' => 'required|numeric|min:0',
            'math' => 'required|numeric|min:0',
            'general_knowledge' => 'required|numeric|min:0',
        ]);

        $data['roll_no'] = AdmissionTestResult::generateRollNo($data['class_id']);
        $data['total_marks'] = $data['bangla'] + $data['english'] + $data['math'] + $data['general_knowledge'];

        DB::beginTransaction();
        try {
            $result = AdmissionTestResult::create($data);
            AdmissionTestResult::recalculateMeritPositions($data['class_id']);
            DB::commit();

            return back()->with('success', "রেজাল্ট যোগ হয়েছে — রোল: {$result->roll_no}");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, AdmissionTestResult $result)
    {
        $data = $request->validate([
            'bangla' => 'required|numeric|min:0',
            'english' => 'required|numeric|min:0',
            'math' => 'required|numeric|min:0',
            'general_knowledge' => 'required|numeric|min:0',
            'status' => 'required|in:pending,passed,failed,waiting',
            'remarks' => 'nullable|string',
        ]);

        $data['total_marks'] = $data['bangla'] + $data['english'] + $data['math'] + $data['general_knowledge'];
        $result->update($data);

        AdmissionTestResult::recalculateMeritPositions($result->class_id);

        return back()->with('success', 'আপডেট হয়েছে।');
    }

    public function destroy(AdmissionTestResult $result)
    {
        $classId = $result->class_id;
        $result->delete();
        AdmissionTestResult::recalculateMeritPositions($classId);
        return back()->with('success', 'মুছে ফেলা হয়েছে।');
    }

    public function publish(Request $request)
    {
        $data = $request->validate([
            'class_id' => 'required|exists:classes,id',
        ]);

        AdmissionTestResult::where('class_id', $data['class_id'])->update(['is_published' => true]);

        return back()->with('success', 'রেজাল্ট প্রকাশিত হয়েছে।');
    }

    public function pdf(Request $request)
    {
        $data = $request->validate([
            'class_id' => 'required|exists:classes,id',
        ]);

        $class = SchoolClass::findOrFail($data['class_id']);
        $results = AdmissionTestResult::where('class_id', $data['class_id'])
            ->orderBy('merit_position')->get();
        $settings = \App\Models\InstituteSetting::first();

        $pdf = Pdf::loadView('admin.pdf.admission-results', compact('class', 'results', 'settings'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download("admission-results-{$class->id}.pdf");
    }
}