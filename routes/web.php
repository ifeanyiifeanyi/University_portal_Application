<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Parent\ParentController;
use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\Teacher\TeacherController;
use App\Http\Controllers\Teacher\TeacherCoursesController;
use App\Http\Controllers\Teacher\TeacherDepartmentController;

// Route::get('/', function () {
//     return view('auth.login');
// });





Route::controller(AuthController::class)->group(function () {

    Route::get('/', 'login')->name('login.view');
    Route::post('/login', 'postLogin')->name('login.post');

    Route::get('logout', 'logout')->name('logout');
});


Route::prefix('admin')->middleware('admin')->group(function () {

    Route::controller(AdminController::class)->group(function () {
        Route::get('dashboard', 'index')->name('admin.view.dashboard');
    });


});

Route::prefix('teacher')->middleware('teacher')->group(function () {
    
    Route::controller(TeacherController::class)->group(function () {
        Route::get('dashboard', 'index')->name('teacher.view.dashboard');
        Route::get('profile', 'profile')->name('teacher.view.profile');
        // post requests
        Route::post('createprofile', 'createprofile')->name('teacher.create.profile');
        Route::post('updateprofile', 'updateprofile')->name('teacher.update.profile');
    });
    Route::controller(TeacherDepartmentController::class)->group(function () {
        Route::get('departments', 'departments')->name('teacher.view.departments');
    });
    Route::controller(TeacherCoursesController::class)->group(function () {
        Route::get('courses', 'courses')->name('teacher.view.courses');
    });
});



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
