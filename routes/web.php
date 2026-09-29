<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
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

use App\Http\Controllers\Admin\LibraryController;
use App\Http\Controllers\Admin\TransportController;
use App\Http\Controllers\Admin\HostelController;
use App\Http\Controllers\Admin\PayrollController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Teacher\TeacherPortalController;
use App\Http\Controllers\Admin\AccountingController;
use App\Http\Controllers\Admin\BankReconciliationController;
use App\Http\Controllers\Admin\ScholarshipController;
use App\Http\Controllers\Admin\InstallmentController;
use App\Http\Controllers\Admin\RefundController;
use App\Http\Controllers\Admin\BudgetController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\ExamRoutineController;
use App\Http\Controllers\Admin\ClassRoutineController;
use App\Http\Controllers\Admin\AdmitCardController;
use App\Http\Controllers\Admin\TeacherEvaluationController;
use App\Http\Controllers\Admin\AdmissionTestController;
use App\Http\Controllers\Frontend\AdmissionResultController;


use App\Http\Controllers\FrontendController;

Route::name('frontend.')->group(function () {
    Route::get('/', [FrontendController::class, 'home'])->name('home');
    Route::get('/about', [FrontendController::class, 'about'])->name('about');
    Route::get('/notices', [FrontendController::class, 'notices'])->name('notices');
    Route::get('/notices/{notice}', [FrontendController::class, 'noticeShow'])->name('notice.show');
    Route::get('/contact', [FrontendController::class, 'contact'])->name('contact');
    Route::post('/contact', [FrontendController::class, 'contactSubmit'])->name('contact.submit');
    Route::get('/online-admission', [FrontendController::class, 'admission'])->name('admission');
    Route::post('/online-admission', [FrontendController::class, 'admissionSubmit'])->name('admission.submit');
    Route::get('/admission-success/{studentId}', [FrontendController::class, 'admissionSuccess'])->name('admission.success');
    // Public (Frontend)
    Route::get('/admission-result', [AdmissionResultController::class, 'index'])->name('frontend.admission-result');
    Route::post('/admission-result/check', [AdmissionResultController::class, 'check'])->name('frontend.admission-result.check');

});


//api route

Route::get('/admin/api/student-payments', function (\Illuminate\Http\Request $request) {
    return \App\Models\FeePayment::where('student_id', $request->student_id)
        ->latest()
        ->get(['id', 'receipt_no', 'amount', 'payment_date'])
        ->map(fn($p) => [
            'id' => $p->id,
            'receipt_no' => $p->receipt_no,
            'amount' => (float) $p->amount,
            'payment_date' => $p->payment_date->format('d M, Y'),
        ]);
})->middleware(['auth', 'role:Super Admin|Admin']);



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




Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified', 'role:Super Admin|Admin'])
    ->group(function () {

        // =========================
        // Admission Test
        // =========================
        Route::prefix('admission-test')
            ->name('admission-test.')
            ->group(function () {

                Route::get('/', [AdmissionTestController::class, 'index'])
                    ->name('index');

                Route::post('/', [AdmissionTestController::class, 'store'])
                    ->name('store');

                Route::post('/publish', [AdmissionTestController::class, 'publish'])
                    ->name('publish');

                Route::get('/pdf', [AdmissionTestController::class, 'pdf'])
                    ->name('pdf');

                Route::put('/{result}', [AdmissionTestController::class, 'update'])
                    ->name('update');

                Route::delete('/{result}', [AdmissionTestController::class, 'destroy'])
                    ->name('destroy');
            });


        // =========================
        // Teacher Evaluations
        // =========================
        Route::prefix('evaluations')
            ->name('evaluations.')
            ->group(function () {

                Route::get('/', [TeacherEvaluationController::class, 'index'])
                    ->name('index');

                Route::get('/create', [TeacherEvaluationController::class, 'create'])
                    ->name('create');

                Route::post('/', [TeacherEvaluationController::class, 'store'])
                    ->name('store');

                Route::get('/report', [TeacherEvaluationController::class, 'report'])
                    ->name('report');

                Route::get('/teacher/{teacher}', [TeacherEvaluationController::class, 'show'])
                    ->name('show');

                Route::delete('/{evaluation}', [TeacherEvaluationController::class, 'destroy'])
                    ->name('destroy');
            });


        // =========================
        // Admit Cards
        // =========================
        Route::prefix('admit-cards')
            ->name('admit-cards.')
            ->group(function () {

                Route::get('/', [AdmitCardController::class, 'index'])
                    ->name('index');

                Route::post('/generate', [AdmitCardController::class, 'generate'])
                    ->name('generate');

                Route::get('/{exam}/student/{student}', [AdmitCardController::class, 'single'])
                    ->name('single');
            });


        // =========================
        // Class Routines
        // =========================
        Route::prefix('class-routines')
            ->name('class-routines.')
            ->group(function () {

                Route::get('/', [ClassRoutineController::class, 'index'])
                    ->name('index');

                Route::post('/', [ClassRoutineController::class, 'store'])
                    ->name('store');

                Route::get('/pdf', [ClassRoutineController::class, 'pdf'])
                    ->name('pdf');

                Route::delete('/{routine}', [ClassRoutineController::class, 'destroy'])
                    ->name('destroy');
            });


        // =========================
        // Exam Routines
        // =========================
        Route::prefix('exams/{exam}/routine')
            ->name('exams.routine.')
            ->group(function () {

                Route::get('/', [ExamRoutineController::class, 'index'])
                    ->name('index');

                Route::post('/', [ExamRoutineController::class, 'store'])
                    ->name('store');

                Route::post('/bulk', [ExamRoutineController::class, 'bulkStore'])
                    ->name('bulk');

                Route::get('/pdf', [ExamRoutineController::class, 'pdf'])
                    ->name('pdf');

                Route::delete('/{routine}', [ExamRoutineController::class, 'destroy'])
                    ->name('destroy');
            });


        // =========================
        // Activity Log
        // =========================
        Route::prefix('activity-log')
            ->name('activity-log.')
            ->group(function () {

                Route::get('/', [ActivityLogController::class, 'index'])
                    ->name('index');

                Route::post('/cleanup', [ActivityLogController::class, 'cleanup'])
                    ->name('cleanup');

                Route::get('/subject/{type}/{id}', [ActivityLogController::class, 'forSubject'])
                    ->name('for-subject');

                Route::get('/{activity}', [ActivityLogController::class, 'show'])
                    ->name('show');
            });
    });

    
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified', 'role:Super Admin|Admin'])
    ->group(function () {

        // =========================
        // Accounting Budgets
        // =========================
        Route::prefix('accounting/budgets')
            ->name('accounting.budgets.')
            ->group(function () {
                Route::get('/', [BudgetController::class, 'index'])->name('index');
                Route::post('/', [BudgetController::class, 'store'])->name('store');
                Route::put('/{budget}', [BudgetController::class, 'update'])->name('update');
                Route::delete('/{budget}', [BudgetController::class, 'destroy'])->name('destroy');
            });

        // =========================
        // Fee Refunds
        // =========================
        Route::prefix('fees/refunds')
            ->name('fees.refunds.')
            ->group(function () {
                Route::get('/', [RefundController::class, 'index'])->name('index');
                Route::get('/create', [RefundController::class, 'create'])->name('create');
                Route::post('/', [RefundController::class, 'store'])->name('store');
                Route::get('/{refund}', [RefundController::class, 'show'])->name('show');
                Route::post('/{refund}/approve', [RefundController::class, 'approve'])->name('approve');
                Route::post('/{refund}/reject', [RefundController::class, 'reject'])->name('reject');
                Route::post('/{refund}/mark-paid', [RefundController::class, 'markPaid'])->name('mark-paid');
                Route::delete('/{refund}', [RefundController::class, 'destroy'])->name('destroy');
            });

        // =========================
        // Fee Installments
        // =========================
        Route::prefix('fees/installments')
            ->name('fees.installments.')
            ->group(function () {
                Route::get('/invoice/{invoice}/create', [InstallmentController::class, 'create'])
                    ->name('create');

                Route::post('/invoice/{invoice}', [InstallmentController::class, 'store'])
                    ->name('store');

                Route::delete('/invoice/{invoice}', [InstallmentController::class, 'destroy'])
                    ->name('destroy');

                Route::post('/update-late-fees', [InstallmentController::class, 'updateLateFees'])
                    ->name('update-late-fees');
                    

                Route::post('/{installment}/pay', [InstallmentController::class, 'pay'])
                    ->name('pay');
            });

        // =========================
        // Scholarships
        // =========================
        Route::prefix('scholarships')
            ->name('scholarships.')
            ->group(function () {

                // Static routes আগে
                Route::get('/applications', [ScholarshipController::class, 'applications'])
                    ->name('applications');

                Route::post('/applications', [ScholarshipController::class, 'storeApplication'])
                    ->name('applications.store');

                Route::post('/applications/{application}/approve', [ScholarshipController::class, 'approve'])
                    ->name('applications.approve');

                Route::post('/applications/{application}/reject', [ScholarshipController::class, 'reject'])
                    ->name('applications.reject');

                Route::post('/bulk-apply', [ScholarshipController::class, 'bulkApply'])
                    ->name('bulk-apply');

                Route::get('/', [ScholarshipController::class, 'index'])
                    ->name('index');

                Route::post('/', [ScholarshipController::class, 'store'])
                    ->name('store');

                Route::put('/{scholarship}', [ScholarshipController::class, 'update'])
                    ->name('update');

                Route::delete('/{scholarship}', [ScholarshipController::class, 'destroy'])
                    ->name('destroy');
            });

        // =========================
        // Bank Reconciliation
        // =========================
        Route::prefix('accounting/banks')
            ->name('accounting.banks.')
            ->group(function () {

                Route::get('/', [BankReconciliationController::class, 'index'])
                    ->name('index');

                Route::post('/', [BankReconciliationController::class, 'store'])
                    ->name('store');

                // Static routes আগে
                Route::post('/{bankAccount}/import', [BankReconciliationController::class, 'import'])
                    ->name('import');

                Route::post('/{bankAccount}/statements', [BankReconciliationController::class, 'storeStatement'])
                    ->name('statements.store');

                Route::post('/{bankAccount}/auto-match', [BankReconciliationController::class, 'autoMatch'])
                    ->name('auto-match');

                Route::get('/statements/{statement}/suggest', [BankReconciliationController::class, 'matchSuggest'])
                    ->name('match.suggest');

                Route::post('/statements/{statement}/match', [BankReconciliationController::class, 'match'])
                    ->name('match');

                Route::post('/statements/{statement}/unmatch', [BankReconciliationController::class, 'unmatch'])
                    ->name('unmatch');

                Route::post('/statements/{statement}/ignore', [BankReconciliationController::class, 'ignore'])
                    ->name('ignore');

                Route::delete('/statements/{statement}', [BankReconciliationController::class, 'destroyStatement'])
                    ->name('statements.destroy');

                Route::get('/{bankAccount}', [BankReconciliationController::class, 'show'])
                    ->name('show');

                Route::delete('/{bankAccount}', [BankReconciliationController::class, 'destroy'])
                    ->name('destroy');
            });
    });


Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified', 'role:Super Admin|Admin'])
    ->group(function () {

        Route::prefix('accounting')
            ->name('accounting.')
            ->group(function () {

                // Chart of Accounts
                Route::get('/chart-of-accounts', [AccountingController::class, 'chartOfAccounts'])
                    ->name('chart-of-accounts');

                Route::post('/chart-of-accounts', [AccountingController::class, 'storeAccount'])
                    ->name('accounts.store');

                Route::put('/chart-of-accounts/{account}', [AccountingController::class, 'updateAccount'])
                    ->name('accounts.update');

                Route::delete('/chart-of-accounts/{account}', [AccountingController::class, 'deleteAccount'])
                    ->name('accounts.destroy');

                // Journal Entries
                Route::get('/journal-entries', [AccountingController::class, 'journalEntries'])
                    ->name('journal-entries');

                Route::get('/journal-entries/create', [AccountingController::class, 'createJournalEntry'])
                    ->name('journal-entries.create');

                Route::post('/journal-entries', [AccountingController::class, 'storeJournalEntry'])
                    ->name('journal-entries.store');

                Route::get('/journal-entries/{entry}', [AccountingController::class, 'showJournalEntry'])
                    ->name('journal-entries.show');

                Route::delete('/journal-entries/{entry}', [AccountingController::class, 'cancelJournalEntry'])
                    ->name('journal-entries.cancel');

                // Reports
                Route::get('/ledger', [AccountingController::class, 'ledger'])
                    ->name('ledger');

                Route::get('/trial-balance', [AccountingController::class, 'trialBalance'])
                    ->name('trial-balance');

                Route::get('/profit-loss', [AccountingController::class, 'profitAndLoss'])
                    ->name('profit-loss');

                Route::get('/balance-sheet', [AccountingController::class, 'balanceSheet'])
                    ->name('balance-sheet');

                // Sync
                Route::post('/sync-old-records', [AccountingController::class, 'syncOldRecords'])
                    ->name('sync-old-records');
            });
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

 Route::prefix('admin')
    ->name('admin.')
    ->middleware('role:Super Admin|Admin')
    ->group(function () {

        // Library
        Route::prefix('library')->name('library.')->group(function () {
            Route::get('/categories', [LibraryController::class, 'categories'])
                ->name('categories');

            Route::post('/categories', [LibraryController::class, 'storeCategory'])
                ->name('categories.store');

            Route::delete('/categories/{category}', [LibraryController::class, 'deleteCategory'])
                ->name('categories.destroy');

            Route::get('/books', [LibraryController::class, 'books'])
                ->name('books');

            Route::post('/books', [LibraryController::class, 'storeBook'])
                ->name('books.store');

            Route::put('/books/{book}', [LibraryController::class, 'updateBook'])
                ->name('books.update');

            Route::delete('/books/{book}', [LibraryController::class, 'deleteBook'])
                ->name('books.destroy');

            Route::get('/issues', [LibraryController::class, 'issues'])
                ->name('issues');

            Route::post('/issues', [LibraryController::class, 'storeIssue'])
                ->name('issues.store');

            Route::post('/issues/{issue}/return', [LibraryController::class, 'returnBook'])
                ->name('issues.return');
        });


        // Transport
        Route::prefix('transport')->name('transport.')->group(function () {
            Route::get('/', [TransportController::class, 'index'])->name('index');
            Route::post('/vehicles', [TransportController::class, 'storeVehicle'])->name('vehicles.store');
            Route::delete('/vehicles/{vehicle}', [TransportController::class, 'deleteVehicle'])->name('vehicles.destroy');
            Route::post('/routes', [TransportController::class, 'storeRoute'])->name('routes.store');
            Route::delete('/routes/{route}', [TransportController::class, 'deleteRoute'])->name('routes.destroy');
            Route::post('/routes/{route}/stops', [TransportController::class, 'storeStop'])->name('stops.store');
            Route::post('/assign', [TransportController::class, 'assignStudent'])->name('assign');
            Route::delete('/assign/{assignment}', [TransportController::class, 'removeAssignment'])->name('assign.remove');
        });


        // Hostel
        Route::prefix('hostel')->name('hostel.')->group(function () {
            Route::get('/', [HostelController::class, 'index'])->name('index');
            Route::post('/hostels', [HostelController::class, 'storeHostel'])->name('hostels.store');
            Route::delete('/hostels/{hostel}', [HostelController::class, 'deleteHostel'])->name('hostels.destroy');
            Route::post('/hostels/{hostel}/rooms', [HostelController::class, 'storeRoom'])->name('rooms.store');
            Route::delete('/rooms/{room}', [HostelController::class, 'deleteRoom'])->name('rooms.destroy');
            Route::post('/allocate', [HostelController::class, 'allocate'])->name('allocate');
            Route::delete('/allocate/{allocation}', [HostelController::class, 'release'])->name('allocate.release');
        });


        // Payroll
        Route::prefix('payroll')->name('payroll.')->group(function () {
            Route::get('/', [PayrollController::class, 'index'])->name('index');
            Route::post('/', [PayrollController::class, 'store'])->name('store');
            Route::post('/bulk', [PayrollController::class, 'bulkStore'])->name('bulk');
            Route::get('/{payment}/payslip', [PayrollController::class, 'payslip'])->name('payslip');
            Route::delete('/{payment}', [PayrollController::class, 'destroy'])->name('destroy');
        });


        // Backup
        Route::prefix('backup')->name('backup.')->group(function () {
            Route::get('/', [BackupController::class, 'index'])->name('index');
            Route::post('/create', [BackupController::class, 'create'])->name('create');
            Route::get('/download/{filename}', [BackupController::class, 'download'])->name('download');
            Route::delete('/{filename}', [BackupController::class, 'destroy'])->name('destroy');
            Route::post('/restore', [BackupController::class, 'restore'])->name('restore');
        });

    });



Route::prefix('teacher')
    ->name('teacher.')
    ->middleware(['auth', 'verified', 'role:Teacher'])
    ->group(function () {

        Route::get('/dashboard', [TeacherPortalController::class, 'dashboard'])
            ->name('dashboard');

        Route::get('/attendance', [TeacherPortalController::class, 'attendance'])
            ->name('attendance');

        Route::post('/attendance', [TeacherPortalController::class, 'storeAttendance'])
            ->name('attendance.store');

        Route::get('/marks', [TeacherPortalController::class, 'marks'])
            ->name('marks');

        Route::post('/marks', [TeacherPortalController::class, 'storeMarks'])
            ->name('marks.store');

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




/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
*/

Artisan::command('inspire', function () {
    $this->comment('আপনি অসাধারণ একটি সিস্টেম বানাচ্ছেন! 🚀');
})->purpose('Display an inspiring quote');

require __DIR__ . '/auth.php';