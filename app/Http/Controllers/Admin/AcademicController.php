<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Group;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use Illuminate\Http\Request;

class AcademicController extends Controller
{
    // ----- Academic Year -----
    public function academicYears()
    {
        $years = AcademicYear::latest()->get();
        return view('admin.academic.years', compact('years'));
    }

    public function storeAcademicYear(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_current' => 'nullable|boolean',
        ]);

        if (!empty($data['is_current'])) {
            AcademicYear::query()->update(['is_current' => false]);
        }
        AcademicYear::create($data);
        return back()->with('success', 'শিক্ষাবর্ষ যোগ হয়েছে।');
    }

    public function deleteAcademicYear(AcademicYear $year)
    {
        $year->delete();
        return back()->with('success', 'শিক্ষাবর্ষ মুছে ফেলা হয়েছে।');
    }

    // ----- Class -----
    public function classes()
    {
        $classes = SchoolClass::orderBy('numeric_value')->get();
        return view('admin.academic.classes', compact('classes'));
    }

    public function storeClass(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'name_bn' => 'nullable|string|max:100',
            'numeric_value' => 'required|integer',
            'education_level' => 'required|in:primary,secondary,higher_secondary,madrasha,other',
        ]);
        SchoolClass::create($data);
        return back()->with('success', 'শ্রেণি যোগ হয়েছে।');
    }

    public function deleteClass(SchoolClass $class)
    {
        $class->delete();
        return back()->with('success', 'শ্রেণি মুছে ফেলা হয়েছে।');
    }

    // ----- Section -----
    public function sections()
    {
        $sections = Section::with('schoolClass')->get();
        $classes = SchoolClass::orderBy('numeric_value')->get();
        return view('admin.academic.sections', compact('sections', 'classes'));
    }

    public function storeSection(Request $request)
    {
        $data = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'name' => 'required|string|max:20',
            'capacity' => 'nullable|integer',
        ]);
        Section::create($data);
        return back()->with('success', 'শাখা যোগ হয়েছে।');
    }

    public function deleteSection(Section $section)
    {
        $section->delete();
        return back()->with('success', 'শাখা মুছে ফেলা হয়েছে।');
    }

    // ----- Group -----
    public function groups()
    {
        $groups = Group::all();
        return view('admin.academic.groups', compact('groups'));
    }

    public function storeGroup(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'name_bn' => 'nullable|string|max:100',
        ]);
        Group::create($data);
        return back()->with('success', 'বিভাগ যোগ হয়েছে।');
    }

    public function deleteGroup(Group $group)
    {
        $group->delete();
        return back()->with('success', 'বিভাগ মুছে ফেলা হয়েছে।');
    }

    // ----- Subject -----
    public function subjects()
    {
        $subjects = Subject::with(['schoolClass', 'group'])->get();
        $classes = SchoolClass::orderBy('numeric_value')->get();
        $groups = Group::all();
        return view('admin.academic.subjects', compact('subjects', 'classes', 'groups'));
    }

    public function storeSubject(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'name_bn' => 'nullable|string|max:100',
            'code' => 'nullable|string|max:20',
            'class_id' => 'nullable|exists:classes,id',
            'group_id' => 'nullable|exists:groups,id',
            'full_marks' => 'required|integer',
            'pass_marks' => 'required|integer',
        ]);
        Subject::create($data);
        return back()->with('success', 'বিষয় যোগ হয়েছে।');
    }

    public function deleteSubject(Subject $subject)
    {
        $subject->delete();
        return back()->with('success', 'বিষয় মুছে ফেলা হয়েছে।');
    }
}
