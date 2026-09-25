<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\FeeCategory;
use App\Models\FeeInvoice;
use App\Models\FeeInvoiceItem;
use App\Models\FeeStructure;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeeInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = FeeInvoice::with(['student', 'schoolClass', 'section']);

        if ($request->filled('class_id')) $query->where('class_id', $request->class_id);
        if ($request->filled('section_id')) $query->where('section_id', $request->section_id);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('month')) $query->where('month', $request->month);
        if ($request->filled('year')) $query->where('year', $request->year);

        $invoices = $query->latest()->get();
        $classes = SchoolClass::where('is_active', true)->orderBy('numeric_value')->get();
        $sections = Section::where('is_active', true)->get();

        return view('admin.fees.invoices.index', compact('invoices', 'classes', 'sections'));
    }

    /**
     * একক শিক্ষার্থীর ইনভয়েস তৈরি
     */
    public function create(Request $request)
    {
        $classes = SchoolClass::where('is_active', true)->orderBy('numeric_value')->get();
        $sections = collect();
        $students = collect();
        $feeStructures = collect();

        $classId = $request->class_id;
        $sectionId = $request->section_id;

        if ($classId) {
            $sections = Section::where('class_id', $classId)->where('is_active', true)->get();
            $students = Student::where('class_id', $classId)
                ->when($sectionId, fn($q) => $q->where('section_id', $sectionId))
                ->where('status', 'active')
                ->orderBy('roll_number')->get();

            $feeStructures = FeeStructure::with('category')
                ->where('class_id', $classId)
                ->where('is_active', true)
                ->get();
        }

        $categories = FeeCategory::where('is_active', true)->get();

        return view('admin.fees.invoices.create', compact(
            'classes', 'sections', 'students', 'feeStructures', 'categories',
            'classId', 'sectionId'
        ));
    }

    /**
     * একক ইনভয়েস তৈরি (স্টুডেন্টের মাসিক ফি)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:invoice_date',
            'month' => 'nullable|string|max:2',
            'year' => 'nullable|integer',
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:fee_categories,id',
            'amounts' => 'required|array',
            'amounts.*' => 'numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'fine' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);

        $student = Student::findOrFail($validated['student_id']);

        // আগে থেকে একই মাসের ইনভয়েস আছে কিনা চেক
        if ($validated['month'] && $validated['year']) {
            $exists = FeeInvoice::where('student_id', $student->id)
                ->where('month', $validated['month'])
                ->where('year', $validated['year'])
                ->where('status', '!=', 'cancelled')
                ->exists();
            if ($exists) {
                return back()->with('error', 'এই মাসের ইনভয়েস ইতিমধ্যে তৈরি হয়েছে।')->withInput();
            }
        }

        DB::beginTransaction();
        try {
            $subtotal = 0;
            $items = [];
            foreach ($validated['category_ids'] as $catId) {
                $amount = (float)($validated['amounts'][$catId] ?? 0);
                if ($amount <= 0) continue;
                $items[] = ['fee_category_id' => $catId, 'amount' => $amount];
                $subtotal += $amount;
            }

            if (empty($items)) {
                return back()->with('error', 'অন্তত একটি ফি আইটেম যোগ করুন।')->withInput();
            }

            $discount = (float)($validated['discount'] ?? 0);
            $fine = (float)($validated['fine'] ?? 0);
            $total = $subtotal - $discount + $fine;

            $invoice = FeeInvoice::create([
                'invoice_no' => FeeInvoice::generateInvoiceNo(),
                'student_id' => $student->id,
                'class_id' => $student->class_id,
                'section_id' => $student->section_id,
                'academic_year_id' => $student->academic_year_id,
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'] ?? null,
                'month' => $validated['month'] ?? null,
                'year' => $validated['year'] ?? null,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'fine' => $fine,
                'total_amount' => $total,
                'paid_amount' => 0,
                'due_amount' => $total,
                'status' => 'unpaid',
                'remarks' => $validated['remarks'] ?? null,
                'created_by' => auth()->id(),
            ]);

            foreach ($items as $item) {
                $invoice->items()->create($item);
            }

            DB::commit();
            return redirect()->route('admin.fees.invoices.show', $invoice)
                ->with('success', 'ইনভয়েস তৈরি হয়েছে।');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(FeeInvoice $invoice)
    {
        $invoice->load(['student.schoolClass', 'student.section', 'items.category', 'payments.receiver', 'section']);
        return view('admin.fees.invoices.show', compact('invoice'));
    }

    /**
     * Bulk ইনভয়েস তৈরি — পুরো ক্লাসের জন্য মাসিক ফি
     */
    public function bulkCreate(Request $request)
    {
        $classes = SchoolClass::where('is_active', true)->orderBy('numeric_value')->get();
        $sections = collect();
        $feeStructures = collect();

        $classId = $request->class_id;
        if ($classId) {
            $sections = Section::where('class_id', $classId)->where('is_active', true)->get();
            $feeStructures = FeeStructure::with('category')
                ->where('class_id', $classId)->where('is_active', true)->get();
        }

        $months = [
            1 => 'জানুয়ারি', 2 => 'ফেব্রুয়ারি', 3 => 'মার্চ', 4 => 'এপ্রিল',
            5 => 'মে', 6 => 'জুন', 7 => 'জুলাই', 8 => 'আগস্ট',
            9 => 'সেপ্টেম্বর', 10 => 'অক্টোবর', 11 => 'নভেম্বর', 12 => 'ডিসেম্বর',
        ];

        return view('admin.fees.invoices.bulk', compact('classes', 'sections', 'feeStructures', 'classId', 'months'));
    }

    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'month' => 'required|string|max:2',
            'year' => 'required|integer',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date',
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:fee_categories,id',
        ]);

        $students = Student::where('class_id', $validated['class_id'])
            ->when($validated['section_id'], fn($q) => $q->where('section_id', $validated['section_id']))
            ->where('status', 'active')
            ->get();

        // ক্লাসের ফি স্ট্রাকচার থেকে অ্যামাউন্ট নেওয়া
        $structures = FeeStructure::where('class_id', $validated['class_id'])
            ->whereIn('fee_category_id', $validated['category_ids'])
            ->get()
            ->keyBy('fee_category_id');

        DB::beginTransaction();
        try {
            $created = 0;
            $skipped = 0;

            foreach ($students as $student) {
                // আগের ইনভয়েস আছে কিনা
                $exists = FeeInvoice::where('student_id', $student->id)
                    ->where('month', $validated['month'])
                    ->where('year', $validated['year'])
                    ->where('status', '!=', 'cancelled')
                    ->exists();

                if ($exists) { $skipped++; continue; }

                $subtotal = 0;
                $items = [];
                foreach ($validated['category_ids'] as $catId) {
                    $structure = $structures->get($catId);
                    if (!$structure) continue;
                    $items[] = ['fee_category_id' => $catId, 'amount' => $structure->amount];
                    $subtotal += $structure->amount;
                }

                if (empty($items)) { $skipped++; continue; }

                $invoice = FeeInvoice::create([
                    'invoice_no' => FeeInvoice::generateInvoiceNo(),
                    'student_id' => $student->id,
                    'class_id' => $student->class_id,
                    'section_id' => $student->section_id,
                    'academic_year_id' => $student->academic_year_id,
                    'invoice_date' => $validated['invoice_date'],
                    'due_date' => $validated['due_date'] ?? null,
                    'month' => $validated['month'],
                    'year' => $validated['year'],
                    'subtotal' => $subtotal,
                    'total_amount' => $subtotal,
                    'due_amount' => $subtotal,
                    'status' => 'unpaid',
                    'created_by' => auth()->id(),
                ]);

                foreach ($items as $item) {
                    $invoice->items()->create($item);
                }
                $created++;
            }

            DB::commit();
            return redirect()->route('admin.fees.invoices.index')
                ->with('success', "ইনভয়েস তৈরি: {$created}টি, স্কিপ: {$skipped}টি");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(FeeInvoice $invoice)
    {
        if ($invoice->payments()->count() > 0) {
            return back()->with('error', 'পেমেন্ট করা ইনভয়েস মুছে ফেলা যাবে না।');
        }
        $invoice->delete();
        return back()->with('success', 'ইনভয়েস মুছে ফেলা হয়েছে।');
    }
}
