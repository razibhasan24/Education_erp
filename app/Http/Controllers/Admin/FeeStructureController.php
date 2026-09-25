<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\FeeCategory;
use App\Models\FeeStructure;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class FeeStructureController extends Controller
{
    public function categories()
    {
        $categories = FeeCategory::latest()->get();
        return view('admin.fees.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'name_bn' => 'nullable|string|max:100',
            'fee_type' => 'required|in:monthly,one_time,yearly,session',
        ]);
        FeeCategory::create($data);
        return back()->with('success', 'ফি ক্যাটাগরি যোগ হয়েছে।');
    }

    public function deleteCategory(FeeCategory $category)
    {
        $category->delete();
        return back()->with('success', 'ক্যাটাগরি মুছে ফেলা হয়েছে।');
    }

    public function index(Request $request)
    {
        $classes = SchoolClass::where('is_active', true)->orderBy('numeric_value')->get();
        $categories = FeeCategory::where('is_active', true)->get();
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();

        $structures = FeeStructure::with(['schoolClass', 'category', 'academicYear'])
            ->when($request->class_id, fn($q) => $q->where('class_id', $request->class_id))
            ->latest()
            ->get();

        return view('admin.fees.structures', compact('structures', 'classes', 'categories', 'academicYears'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'fee_category_id' => 'required|exists:fee_categories,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'amount' => 'required|numeric|min:0',
            'applicable_for' => 'required|in:all,new,old',
            'effective_from' => 'nullable|date',
        ]);

        FeeStructure::create($data);
        return back()->with('success', 'ফি স্ট্রাকচার যোগ হয়েছে।');
    }

    public function destroy(FeeStructure $structure)
    {
        $structure->delete();
        return back()->with('success', 'স্ট্রাকচার মুছে ফেলা হয়েছে।');
    }
}
