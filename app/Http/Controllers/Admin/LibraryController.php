<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\BookIssue;
use App\Models\Student;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LibraryController extends Controller
{
    // ===== Categories =====
    public function categories()
    {
        $categories = BookCategory::withCount('books')->latest()->get();
        return view('admin.library.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'name_bn' => 'nullable|string|max:100',
            'code' => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ]);
        BookCategory::create($data);
        return back()->with('success', 'ক্যাটাগরি যোগ হয়েছে।');
    }

    public function deleteCategory(BookCategory $category)
    {
        $category->delete();
        return back()->with('success', 'ক্যাটাগরি মুছে ফেলা হয়েছে।');
    }

    // ===== Books =====
    public function books(Request $request)
    {
        $query = Book::with('category');
        if ($request->filled('category_id')) $query->where('book_category_id', $request->category_id);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('title', 'like', "%$s%")
                ->orWhere('author', 'like', "%$s%")
                ->orWhere('isbn', 'like', "%$s%"));
        }
        $books = $query->latest()->get();
        $categories = BookCategory::where('is_active', true)->get();
        return view('admin.library.books', compact('books', 'categories'));
    }

    public function storeBook(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'title_bn' => 'nullable|string|max:200',
            'author' => 'nullable|string|max:150',
            'publisher' => 'nullable|string|max:150',
            'isbn' => 'nullable|string|max:50',
            'book_category_id' => 'nullable|exists:book_categories,id',
            'edition' => 'nullable|string|max:50',
            'published_year' => 'nullable|integer',
            'total_copies' => 'required|integer|min:1',
            'shelf_no' => 'nullable|string|max:30',
            'price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);
        $data['available_copies'] = $data['total_copies'];
        Book::create($data);
        return back()->with('success', 'বই যোগ হয়েছে।');
    }

    public function updateBook(Request $request, Book $book)
    {
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'author' => 'nullable|string|max:150',
            'total_copies' => 'required|integer|min:1',
            'price' => 'nullable|numeric|min:0',
        ]);
        // available_copies সমন্বয়
        $issued = $book->total_copies - $book->available_copies;
        $data['available_copies'] = max(0, $data['total_copies'] - $issued);
        $book->update($data);
        return back()->with('success', 'আপডেট হয়েছে।');
    }

    public function deleteBook(Book $book)
    {
        $book->delete();
        return back()->with('success', 'বই মুছে ফেলা হয়েছে।');
    }

    // ===== Issues =====
    public function issues(Request $request)
    {
        $query = BookIssue::with(['book', 'student', 'teacher']);
        if ($request->filled('status')) $query->where('status', $request->status);
        $issues = $query->latest()->get();
        $books = Book::where('is_active', true)->where('available_copies', '>', 0)->get();
        $students = Student::where('status', 'active')->get();
        $teachers = Teacher::where('status', 'active')->get();
        return view('admin.library.issues', compact('issues', 'books', 'students', 'teachers'));
    }

    public function storeIssue(Request $request)
    {
        $data = $request->validate([
            'book_id' => 'required|exists:books,id',
            'member_type' => 'required|in:student,teacher',
            'member_id' => 'required|integer',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
        ]);

        DB::beginTransaction();
        try {
            $book = Book::findOrFail($data['book_id']);
            if ($book->available_copies < 1) {
                return back()->with('error', 'বইটির কোনো কপি বর্তমানে নেই।');
            }

            $issue = BookIssue::create([
                'issue_no' => BookIssue::generateIssueNo(),
                'book_id' => $book->id,
                'student_id' => $data['member_type'] === 'student' ? $data['member_id'] : null,
                'teacher_id' => $data['member_type'] === 'teacher' ? $data['member_id'] : null,
                'issue_date' => $data['issue_date'],
                'due_date' => $data['due_date'],
                'status' => 'issued',
                'issued_by' => auth()->id(),
            ]);

            $book->decrement('available_copies');
            DB::commit();
            return back()->with('success', "বই ইস্যু হয়েছে — {$issue->issue_no}");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function returnBook(Request $request, BookIssue $issue)
    {
        $data = $request->validate([
            'return_date' => 'required|date',
            'fine_amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:returned,lost,damaged',
            'remarks' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $issue->update([
                'return_date' => $data['return_date'],
                'fine_amount' => $data['fine_amount'] ?? 0,
                'status' => $data['status'],
                'remarks' => $data['remarks'] ?? null,
                'returned_to' => auth()->id(),
            ]);

            if ($data['status'] === 'returned') {
                $issue->book->increment('available_copies');
            }

            DB::commit();
            return back()->with('success', 'বই ফেরত প্রক্রিয়া সম্পন্ন।');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}
