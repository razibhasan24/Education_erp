<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\AcademicController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\StudentAttendanceController;
use App\Http\Controllers\Admin\TeacherAttendanceController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\MarkController;
use App\Http\Controllers\Admin\ResultController;
use App\Http\Controllers\Admin\FeeStructureController;
use App\Http\Controllers\Admin\FeeInvoiceController;
use App\Http\Controllers\Admin\FeePaymentController;
use App\Http\Controllers\Admin\FeeReportController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\SmsController;
use App\Http\Controllers\Admin\PdfController;
use App\Http\Controllers\Admin\AnalyticsController;
// api controller
use App\Models\Section;
use App\Models\Student;
use Illuminate\Http\Request;


Route::get('/', fn() => view('welcome'));


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    Route::prefix('admin')->name('admin.')->middleware('role:Super Admin|Admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Students
        Route::resource('students', StudentController::class);

        // Teachers
        Route::resource('teachers', TeacherController::class);

        // Academic Setup
        Route::prefix('academic')->name('academic.')->group(function () {
            Route::get('/years', [AcademicController::class, 'academicYears'])->name('years');
            Route::post('/years', [AcademicController::class, 'storeAcademicYear'])->name('years.store');
            Route::delete('/years/{year}', [AcademicController::class, 'deleteAcademicYear'])->name('years.destroy');

            Route::get('/classes', [AcademicController::class, 'classes'])->name('classes');
            Route::post('/classes', [AcademicController::class, 'storeClass'])->name('classes.store');
            Route::delete('/classes/{class}', [AcademicController::class, 'deleteClass'])->name('classes.destroy');

            Route::get('/sections', [AcademicController::class, 'sections'])->name('sections');
            Route::post('/sections', [AcademicController::class, 'storeSection'])->name('sections.store');
            Route::delete('/sections/{section}', [AcademicController::class, 'deleteSection'])->name('sections.destroy');

            Route::get('/groups', [AcademicController::class, 'groups'])->name('groups');
            Route::post('/groups', [AcademicController::class, 'storeGroup'])->name('groups.store');
            Route::delete('/groups/{group}', [AcademicController::class, 'deleteGroup'])->name('groups.destroy');

            Route::get('/subjects', [AcademicController::class, 'subjects'])->name('subjects');
            Route::post('/subjects', [AcademicController::class, 'storeSubject'])->name('subjects.store');
            Route::delete('/subjects/{subject}', [AcademicController::class, 'deleteSubject'])->name('subjects.destroy');
        });
    });


    Route::prefix('teacher')->name('teacher.')->middleware('role:Teacher')->group(function () {
        Route::get('/dashboard', fn() => view('teacher.dashboard'))->name('dashboard');
    });

    Route::prefix('student')->name('student.')->middleware('role:Student')->group(function () {
        Route::get('/dashboard', fn() => view('student.dashboard'))->name('dashboard');
    });


});
Route::prefix('admin')
    ->name('admin.')
    ->middleware('role:Super Admin|Admin')
    ->group(function () {

        // =========================
        // Exam Management
        // =========================
        Route::resource('exams', ExamController::class);

        // =========================
        // Subject Management
        // =========================
        Route::get('/exams/{exam}/subjects', [ExamController::class, 'subjects'])
            ->name('exams.subjects');

        Route::post('/exams/{exam}/subjects', [ExamController::class, 'storeSubject'])
            ->name('exams.subjects.store');

        Route::post('/exams/{exam}/subjects/bulk', [ExamController::class, 'bulkImportSubjects'])
            ->name('exams.subjects.bulk');

        Route::delete('/exams/subjects/{examSubject}', [ExamController::class, 'deleteSubject'])
            ->name('exams.subjects.destroy');

        // =========================
        // Mark Entry
        // =========================
        Route::get('/exams/{exam}/marks', [MarkController::class, 'index'])
            ->name('exams.marks');

        Route::post('/exams/{exam}/marks', [MarkController::class, 'store'])
            ->name('exams.marks.store');

        // =========================
        // Result
        // =========================
        Route::get('/exams/{exam}/result', [ResultController::class, 'index'])
            ->name('exams.result');

        Route::get('/exams/{exam}/marksheet/{student}', [ResultController::class, 'marksheet'])
            ->name('exams.marksheet');
    });


Route::prefix('admin')
    ->name('admin.')
    ->middleware('role:Super Admin|Admin')
    ->group(function () {

        Route::prefix('fees')->name('fees.')->group(function () {

            // =========================
            // Fee Categories
            // =========================
            Route::get('/categories', [FeeStructureController::class, 'categories'])
                ->name('categories');

            Route::post('/categories', [FeeStructureController::class, 'storeCategory'])
                ->name('categories.store');

            Route::delete('/categories/{category}', [FeeStructureController::class, 'deleteCategory'])
                ->name('categories.destroy');


            // =========================
            // Fee Structures
            // =========================
            Route::get('/structures', [FeeStructureController::class, 'index'])
                ->name('structures');

            Route::post('/structures', [FeeStructureController::class, 'store'])
                ->name('structures.store');

            Route::delete('/structures/{structure}', [FeeStructureController::class, 'destroy'])
                ->name('structures.destroy');


            // =========================
            // Invoices
            // =========================
            Route::get('/invoices', [FeeInvoiceController::class, 'index'])
                ->name('invoices.index');

            Route::get('/invoices/create', [FeeInvoiceController::class, 'create'])
                ->name('invoices.create');

            Route::post('/invoices', [FeeInvoiceController::class, 'store'])
                ->name('invoices.store');

            Route::get('/invoices/bulk', [FeeInvoiceController::class, 'bulkCreate'])
                ->name('invoices.bulk');

            Route::post('/invoices/bulk', [FeeInvoiceController::class, 'bulkStore'])
                ->name('invoices.bulk.store');

            Route::get('/invoices/{invoice}', [FeeInvoiceController::class, 'show'])
                ->name('invoices.show');

            Route::delete('/invoices/{invoice}', [FeeInvoiceController::class, 'destroy'])
                ->name('invoices.destroy');


            // =========================
            // Payments
            // =========================
            Route::get('/payments', [FeePaymentController::class, 'index'])
                ->name('payments.index');

            Route::get('/payments/invoice/{invoice}/create', [FeePaymentController::class, 'create'])
                ->name('payments.create');

            Route::post('/payments/invoice/{invoice}', [FeePaymentController::class, 'store'])
                ->name('payments.store');

            Route::get('/payments/{payment}/receipt', [FeePaymentController::class, 'receipt'])
                ->name('payments.receipt');

            Route::delete('/payments/{payment}', [FeePaymentController::class, 'destroy'])
                ->name('payments.destroy');


            // =========================
            // Reports
            // =========================
            Route::get('/reports/due', [FeeReportController::class, 'dueList'])
                ->name('reports.due');

            Route::get('/reports/income-expense', [FeeReportController::class, 'incomeExpense'])
                ->name('reports.income-expense');

            Route::get('/reports/student-ledger/{student}', [FeeReportController::class, 'studentLedger'])
                ->name('reports.student-ledger');


            // =========================
            // Expenses
            // =========================
            Route::get('/expenses', [ExpenseController::class, 'index'])
                ->name('expenses.index');

            Route::post('/expenses', [ExpenseController::class, 'store'])
                ->name('expenses.store');

            Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])
                ->name('expenses.destroy');
        });
    });



Route::prefix('admin')
    ->name('admin.')
    ->middleware('role:Super Admin|Admin')
    ->group(function () {

        // =========================
        // SMS MANAGEMENT
        // =========================
        Route::prefix('sms')->name('sms.')->group(function () {

            Route::get('/settings', [SmsController::class, 'settings'])
                ->name('settings');

            Route::post('/settings', [SmsController::class, 'updateSettings'])
                ->name('settings.update');

            Route::get('/compose', [SmsController::class, 'compose'])
                ->name('compose');

            Route::post('/send', [SmsController::class, 'send'])
                ->name('send');

            Route::get('/logs', [SmsController::class, 'logs'])
                ->name('logs');

            Route::post('/due-reminders', [SmsController::class, 'sendDueReminders'])
                ->name('due-reminders');
        });


        // =========================
        // PDF REPORTS
        // =========================
        Route::prefix('pdf')->name('pdf.')->group(function () {

            Route::get('/marksheet/{exam}/{student}', [PdfController::class, 'marksheet'])
                ->name('marksheet');

            Route::get('/receipt/{payment}', [PdfController::class, 'receipt'])
                ->name('receipt');

            Route::get('/result-sheet/{exam}', [PdfController::class, 'resultSheet'])
                ->name('result-sheet');

            Route::get('/due-list', [PdfController::class, 'dueList'])
                ->name('due-list');

            Route::get('/attendance-report', [PdfController::class, 'attendanceReport'])
                ->name('attendance-report');
        });




        Route::get('/analytics/dashboard', [AnalyticsController::class, 'dashboardData'])
            ->name('analytics.dashboard');






Route::prefix('admin')
    ->name('admin.')
    ->middleware('role:Super Admin|Admin')
    ->group(function () {

        Route::prefix('attendance')->name('attendance.')->group(function () {

            // Student Attendance
            Route::get('/students', [StudentAttendanceController::class, 'index'])
                ->name('students.index');

            Route::post('/students', [StudentAttendanceController::class, 'store'])
                ->name('students.store');

            Route::get('/students/report', [StudentAttendanceController::class, 'report'])
                ->name('students.report');

            Route::get('/students/{student}/report', [StudentAttendanceController::class, 'studentReport'])
                ->name('students.student-report');

            // Teacher Attendance
            Route::get('/teachers', [TeacherAttendanceController::class, 'index'])
                ->name('teachers.index');

            Route::post('/teachers', [TeacherAttendanceController::class, 'store'])
                ->name('teachers.store');

            Route::get('/teachers/report', [TeacherAttendanceController::class, 'report'])
                ->name('teachers.report');
        });
    });
});

// API Routes for dynamic data fetching

Route::get('/api/sections', function (Request $request) {
    return Section::query()
        ->where('class_id', $request->class_id)
        ->where('is_active', true)
        ->orderBy('name')
        ->get(['id', 'name']);
})->name('api.sections');


Route::get('/api/students', function (Request $request) {
    return Student::query()
        ->where('class_id', $request->class_id)
        ->when(
            $request->filled('section_id'),
            fn ($query) => $query->where('section_id', $request->section_id)
        )
        ->where('status', 'active')
        ->orderBy('roll_number')
        ->get([
            'id',
            'name',
            'roll_number',
            'student_id'
        ]);
})->name('api.students');

require __DIR__ . '/auth.php';
