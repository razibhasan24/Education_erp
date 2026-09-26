<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// ===== Controllers =====
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PaymentController;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\AcademicController;
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
use App\Http\Controllers\Admin\GuardianController;
use App\Http\Controllers\Admin\ReportCardController;

// Portal Controllers
use App\Http\Controllers\Student\StudentPortalController;
use App\Http\Controllers\Guardian\GuardianPortalController;

// Models (for inline API routes)
use App\Models\Section;
use App\Models\Student;


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => view('welcome'))->name('home');


/*
|--------------------------------------------------------------------------
| Payment Gateway Callback Routes (Public — SSLCommerz থেকে কল হয়)
|--------------------------------------------------------------------------
*/
Route::prefix('payment')->name('payment.')->group(function () {
    Route::match(['get', 'post'], '/success', [PaymentController::class, 'success'])->name('success');
    Route::post('/fail', [PaymentController::class, 'fail'])->name('fail');
    Route::post('/cancel', [PaymentController::class, 'cancel'])->name('cancel');
    Route::post('/ipn', [PaymentController::class, 'ipn'])->name('ipn');
});


/*
|--------------------------------------------------------------------------
| Authenticated Routes (সব লগইন করা ইউজারের জন্য)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {

    // Generic dashboard
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Payment Initiate (Student Only)
    |--------------------------------------------------------------------------
    */
    Route::post('/payment/invoice/{invoice}/initiate', [PaymentController::class, 'initiate'])
        ->name('payment.initiate');

    /*
    |--------------------------------------------------------------------------
    | ADMIN ROUTES
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->middleware('role:Super Admin|Admin')->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Analytics (JSON API)
        Route::get('/analytics/dashboard', [AnalyticsController::class, 'dashboardData'])->name('analytics.dashboard');

        // ============ Students ============
        Route::resource('students', StudentController::class);

        // ============ Teachers ============
        Route::resource('teachers', TeacherController::class);

        // ============ Guardians ============
        Route::resource('guardians', GuardianController::class);

        // ============ Academic Setup ============
        Route::prefix('academic')->name('academic.')->group(function () {
            // Academic Years
            Route::get('/years', [AcademicController::class, 'academicYears'])->name('years');
            Route::post('/years', [AcademicController::class, 'storeAcademicYear'])->name('years.store');
            Route::delete('/years/{year}', [AcademicController::class, 'deleteAcademicYear'])->name('years.destroy');

            // Classes
            Route::get('/classes', [AcademicController::class, 'classes'])->name('classes');
            Route::post('/classes', [AcademicController::class, 'storeClass'])->name('classes.store');
            Route::delete('/classes/{class}', [AcademicController::class, 'deleteClass'])->name('classes.destroy');

            // Sections
            Route::get('/sections', [AcademicController::class, 'sections'])->name('sections');
            Route::post('/sections', [AcademicController::class, 'storeSection'])->name('sections.store');
            Route::delete('/sections/{section}', [AcademicController::class, 'deleteSection'])->name('sections.destroy');

            // Groups
            Route::get('/groups', [AcademicController::class, 'groups'])->name('groups');
            Route::post('/groups', [AcademicController::class, 'storeGroup'])->name('groups.store');
            Route::delete('/groups/{group}', [AcademicController::class, 'deleteGroup'])->name('groups.destroy');

            // Subjects
            Route::get('/subjects', [AcademicController::class, 'subjects'])->name('subjects');
            Route::post('/subjects', [AcademicController::class, 'storeSubject'])->name('subjects.store');
            Route::delete('/subjects/{subject}', [AcademicController::class, 'deleteSubject'])->name('subjects.destroy');
        });

        // ============ Attendance ============
        Route::prefix('attendance')->name('attendance.')->group(function () {
            // Student Attendance
            Route::get('/students', [StudentAttendanceController::class, 'index'])->name('students.index');
            Route::post('/students', [StudentAttendanceController::class, 'store'])->name('students.store');
            Route::get('/students/report', [StudentAttendanceController::class, 'report'])->name('students.report');
            Route::get('/students/{student}/report', [StudentAttendanceController::class, 'studentReport'])->name('students.student-report');

            // Teacher Attendance
            Route::get('/teachers', [TeacherAttendanceController::class, 'index'])->name('teachers.index');
            Route::post('/teachers', [TeacherAttendanceController::class, 'store'])->name('teachers.store');
            Route::get('/teachers/report', [TeacherAttendanceController::class, 'report'])->name('teachers.report');
        });

        // ============ Exams ============
        Route::resource('exams', ExamController::class);

        // Exam Subjects
        Route::get('/exams/{exam}/subjects', [ExamController::class, 'subjects'])->name('exams.subjects');
        Route::post('/exams/{exam}/subjects', [ExamController::class, 'storeSubject'])->name('exams.subjects.store');
        Route::post('/exams/{exam}/subjects/bulk', [ExamController::class, 'bulkImportSubjects'])->name('exams.subjects.bulk');
        Route::delete('/exams/subjects/{examSubject}', [ExamController::class, 'deleteSubject'])->name('exams.subjects.destroy');

        // Marks Entry
        Route::get('/exams/{exam}/marks', [MarkController::class, 'index'])->name('exams.marks');
        Route::post('/exams/{exam}/marks', [MarkController::class, 'store'])->name('exams.marks.store');

        // Result
        Route::get('/exams/{exam}/result', [ResultController::class, 'index'])->name('exams.result');
        Route::get('/exams/{exam}/marksheet/{student}', [ResultController::class, 'marksheet'])->name('exams.marksheet');

        // ============ Fees ============
        Route::prefix('fees')->name('fees.')->group(function () {

            // Fee Categories
            Route::get('/categories', [FeeStructureController::class, 'categories'])->name('categories');
            Route::post('/categories', [FeeStructureController::class, 'storeCategory'])->name('categories.store');
            Route::delete('/categories/{category}', [FeeStructureController::class, 'deleteCategory'])->name('categories.destroy');

            // Fee Structures
            Route::get('/structures', [FeeStructureController::class, 'index'])->name('structures');
            Route::post('/structures', [FeeStructureController::class, 'store'])->name('structures.store');
            Route::delete('/structures/{structure}', [FeeStructureController::class, 'destroy'])->name('structures.destroy');

            // Invoices
            Route::get('/invoices', [FeeInvoiceController::class, 'index'])->name('invoices.index');
            Route::get('/invoices/create', [FeeInvoiceController::class, 'create'])->name('invoices.create');
            Route::post('/invoices', [FeeInvoiceController::class, 'store'])->name('invoices.store');
            Route::get('/invoices/bulk', [FeeInvoiceController::class, 'bulkCreate'])->name('invoices.bulk');
            Route::post('/invoices/bulk', [FeeInvoiceController::class, 'bulkStore'])->name('invoices.bulk.store');
            Route::get('/invoices/{invoice}', [FeeInvoiceController::class, 'show'])->name('invoices.show');
            Route::delete('/invoices/{invoice}', [FeeInvoiceController::class, 'destroy'])->name('invoices.destroy');

            // Payments
            Route::get('/payments', [FeePaymentController::class, 'index'])->name('payments.index');
            Route::get('/payments/invoice/{invoice}/create', [FeePaymentController::class, 'create'])->name('payments.create');
            Route::post('/payments/invoice/{invoice}', [FeePaymentController::class, 'store'])->name('payments.store');
            Route::get('/payments/{payment}/receipt', [FeePaymentController::class, 'receipt'])->name('payments.receipt');
            Route::delete('/payments/{payment}', [FeePaymentController::class, 'destroy'])->name('payments.destroy');

            // Reports
            Route::get('/reports/due', [FeeReportController::class, 'dueList'])->name('reports.due');
            Route::get('/reports/income-expense', [FeeReportController::class, 'incomeExpense'])->name('reports.income-expense');
            Route::get('/reports/student-ledger/{student}', [FeeReportController::class, 'studentLedger'])->name('reports.student-ledger');

            // Expenses
            Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
            Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
            Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
        });

        // ============ SMS ============
        Route::prefix('sms')->name('sms.')->group(function () {
            Route::get('/settings', [SmsController::class, 'settings'])->name('settings');
            Route::post('/settings', [SmsController::class, 'updateSettings'])->name('settings.update');
            Route::get('/compose', [SmsController::class, 'compose'])->name('compose');
            Route::post('/send', [SmsController::class, 'send'])->name('send');
            Route::get('/logs', [SmsController::class, 'logs'])->name('logs');
            Route::post('/due-reminders', [SmsController::class, 'sendDueReminders'])->name('due-reminders');
        });

        // ============ PDF ============
        Route::prefix('pdf')->name('pdf.')->group(function () {
            Route::get('/marksheet/{exam}/{student}', [PdfController::class, 'marksheet'])->name('marksheet');
            Route::get('/receipt/{payment}', [PdfController::class, 'receipt'])->name('receipt');
            Route::get('/result-sheet/{exam}', [PdfController::class, 'resultSheet'])->name('result-sheet');
            Route::get('/due-list', [PdfController::class, 'dueList'])->name('due-list');
            Route::get('/attendance-report', [PdfController::class, 'attendanceReport'])->name('attendance-report');
        });

        // ============ Report Cards ============
        Route::prefix('report-cards')->name('report-cards.')->group(function () {
            Route::get('/', [ReportCardController::class, 'index'])->name('index');
            Route::get('/{exam}/student/{student}', [ReportCardController::class, 'single'])->name('single');
            Route::post('/{exam}/bulk', [ReportCardController::class, 'bulk'])->name('bulk');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | TEACHER PORTAL
    |--------------------------------------------------------------------------
    */
    Route::prefix('teacher')->name('teacher.')->middleware('role:Teacher')->group(function () {
        Route::get('/dashboard', fn() => view('teacher.dashboard'))->name('dashboard');
    });

    /*
    |--------------------------------------------------------------------------
    | STUDENT PORTAL
    |--------------------------------------------------------------------------
    */
    Route::prefix('student-portal')->name('student.')->middleware('role:Student')->group(function () {
        Route::get('/dashboard', [StudentPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/attendance', [StudentPortalController::class, 'attendance'])->name('attendance');
        Route::get('/results', [StudentPortalController::class, 'results'])->name('results');
        Route::get('/fees', [StudentPortalController::class, 'fees'])->name('fees');
        Route::get('/profile', [StudentPortalController::class, 'profile'])->name('profile');
    });

    /*
    |--------------------------------------------------------------------------
    | GUARDIAN PORTAL
    |--------------------------------------------------------------------------
    */
    Route::prefix('guardian-portal')->name('guardian.')->middleware('role:Guardian')->group(function () {
        Route::get('/dashboard', [GuardianPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/child/{student}', [GuardianPortalController::class, 'childOverview'])->name('child');
    });

});


/*
|--------------------------------------------------------------------------
| API Routes (Dynamic data fetching — auth required)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('api')->name('api.')->group(function () {

    Route::get('/sections', function (Request $request) {
        return Section::query()
            ->where('class_id', $request->class_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);
    })->name('sections');

    Route::get('/students', function (Request $request) {
        return Student::query()
            ->where('class_id', $request->class_id)
            ->when(
                $request->filled('section_id'),
                fn ($query) => $query->where('section_id', $request->section_id)
            )
            ->where('status', 'active')
            ->orderBy('roll_number')
            ->get(['id', 'name', 'roll_number', 'student_id']);
    })->name('students');
});


require __DIR__ . '/auth.php';