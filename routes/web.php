<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\FacultyController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Parent\ParentController;
use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\AcademicSessionController;
use App\Http\Controllers\Admin\AdminStudentController;

// Route::get('/', function () {
//     return view('auth.login');
// });





Route::controller(AuthController::class)->group(function () {

    Route::get('/', 'login')->name('login.view');
    Route::post('/login', 'postLogin')->name('login.post');

    Route::post('logout', 'logout')->name('logout');
});


Route::prefix('admin')->middleware('admin')->group(function () {

    Route::controller(AdminController::class)->group(function () {
        Route::get('dashboard', 'index')->name('admin.view.dashboard');
    });

    Route::controller(ProfileController::class)->group(function(){
        Route::get('profile', 'index')->name('admin.view.profile');
        Route::patch('update-profile/{user::slug}', 'update')->name('admin.update.profile');
        Route::patch('update-password/{user::slug}', 'updatePassword')->name('admin.update.password');
    });

    Route::controller(AcademicSessionController::class)->group(function(){
        Route::get('academic-session-manager', 'index')->name('admin.academic.session');
        Route::post('academic-session-manager', 'store')->name('admin.academic.store');

        Route::get('academic-session-manager/edit/{id}', 'edit')->name('admin.academic.edit');
        Route::put('academic-session-manager/update/{id}', 'update')->name('admin.academic.update');
        Route::get('academic-session-manager/delete/{id}', 'destroy')->name('admin.academic.delete');

    });

    Route::controller(CourseController::class)->group(function(){
        Route::get('course-managers', 'index')->name('admin.courses.view');
        Route::post('courses/store', 'store')->name('admin.courses.store');
        Route::post('courses/update/{id}','update')->name('admin.courses.update');
        Route::get('courses/delete/{id}','destroy')->name('admin.courses.delete');

    });


    Route::resource('faculty-manager', FacultyController::class);


    Route::controller(DepartmentController::class)->group(function(){
        Route::get('manage-department', 'index')->name('admin.department.view');
        Route::post('manage-department', 'store')->name('admin.department.store');
        Route::get('manage-department/edit/{id}', 'edit')->name('admin.department.edit');
        Route::get('manage-department/show/{id}', 'show')->name('admin.department.show');
        Route::put('manage-department/update/{id}', 'update')->name('admin.department.update');
        Route::delete('manage-department/del/{id}', 'destroy')->name('admin.department.delete');
    });

    Route::controller(TeacherController::class)->group(function(){
        Route::get('manage-lecturers', 'index')->name('admin.teacher.view');
        Route::post('manage-lecturers/store', 'store')->name('admin.teacher.store');
        Route::get('manage-lecturers/create', 'create')->name('admin.teacher.create');
        Route::get('manage-lecturers/{teacher}/show', 'show')->name('admin.teacher.show');
        Route::get('manage-lecturers/{teacher}/edit', 'edit')->name('admin.teacher.edit');
        Route::put('manage-lecturers/{teacher}/update', 'update')->name('admin.teachers.update');
        Route::delete('manage-lecturers/{teacher}/delete', 'destroy')->name('admin.teachers.delete');
    });

    Route::controller(AdminStudentController::class)->group(function(){
        Route::get('student-manager', 'index')->name('admin.student.view');
        Route::get('student-manager/create', 'create')->name('admin.student.create');
        Route::get('student-manager/edit/{student}', 'edit')->name('admin.student.edit');
        Route::put('student-manager/update/{student}', 'update')->name('admin.student.update');
        Route::post('student-manager/store', 'store')->name('admin.student.store');
        Route::get('student-manager/details/{student}', 'show')->name('admin.student.details');
        Route::delete('student-manager/del/{student}', 'destroy')->name('admin.student.delete');
    });

});














// Route::prefix('teacher')->middleware('teacher')->group(function () {
//     Route::controller(TeacherController::class)->group(function () {
//         Route::get('dashboard', 'index')->name('teacher.view.dashboard');
//     });
// });



Route::prefix('student')->middleware('student')->group(function () {
    Route::controller(StudentController::class)->group(function () {
        Route::get('dashboard', 'index')->name('student.view.dashboard');
    });
});


Route::prefix('parent')->middleware('parent')->group(function(){
    Route::controller(ParentController::class)->group(function(){
        Route::get('dashboard', 'index')->name('parent.view.dashboard');
    });
});
