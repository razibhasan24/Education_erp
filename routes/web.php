<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\AcademicController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\StudentAttendanceController;
use App\Http\Controllers\Admin\TeacherAttendanceController;


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


// ... (আগের রাউটের নিচে)

Route::prefix('attendance')->name('attendance.')->group(function () {
    // Students
    Route::get('/students', [StudentAttendanceController::class, 'index'])->name('students.index');
    Route::post('/students', [StudentAttendanceController::class, 'store'])->name('students.store');
    Route::get('/students/report', [StudentAttendanceController::class, 'report'])->name('students.report');
    Route::get('/students/{student}/report', [StudentAttendanceController::class, 'studentReport'])->name('students.student-report');

    // Teachers
    Route::get('/teachers', [TeacherAttendanceController::class, 'index'])->name('teachers.index');
    Route::post('/teachers', [TeacherAttendanceController::class, 'store'])->name('teachers.store');
    Route::get('/teachers/report', [TeacherAttendanceController::class, 'report'])->name('teachers.report');
});
});

require __DIR__ . '/auth.php';
