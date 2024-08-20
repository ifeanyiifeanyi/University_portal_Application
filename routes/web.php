<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Parent\ParentController;
use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\Teacher\TeacherController;
use App\Http\Controllers\Student\StudentFeesController;
use App\Http\Controllers\Student\StudentResultController;
use App\Http\Controllers\Teacher\TeacherCoursesController;
use App\Http\Controllers\Student\StudentAcceptanceController;
use App\Http\Controllers\Teacher\TeacherDepartmentController;
use App\Http\Controllers\Admin\AdminAccountsManagersController;
use App\Http\Controllers\Admin\AdminDepartmentCreditController;
use App\Http\Controllers\Admin\AdminTeacherAssignmentController;
use App\Http\Controllers\Admin\AdminAssignStudentCourseController;
use App\Http\Controllers\Student\StudentCourseRegistrationController;

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

    Route::controller(AdminTeacherAssignmentController::class)->group(function () {
        Route::get('teacher-assignment', 'index')->name('admin.teacher.assignment.view');
        Route::get('teacher-assignment/create/{teacher?}', 'create')->name('admin.teacher.assignment.create');
        Route::get('get-department-courses', 'getDepartmentCourses')->name('admin.get-department-courses');
        Route::get('get-assigned-lecturer-details/{teacherAssignment}', 'show')->name('admin.teacher.assignment.show');



        Route::post('teacher-assignment', 'store')->name('admin.teacher.assignment.store');
        Route::get('teacher-assignment/edit/{id}', 'edit')->name('admin.teacher.assignment.edit');
        Route::put('teacher-assignment/update/{id}', 'update')->name('admin.teacher.assignment.update');
        Route::delete('teacher-assignment/{id}', 'destroy')->name('admin.teacher.assignment.delete');
    });


    Route::controller(AdminAssignStudentCourseController::class)->group(function () {
        Route::get('assign-student-courses/{id}', 'showSemesterCourses')->name('admin.assign.courseForStudent');
        Route::post('assign-student-courses/{id}', 'registerCourses')->name('admin.students.register-courses.store');
        Route::get('students/{student}/course-registrations', 'showStudentCourseRegistrations')->name('admin.students.course-registrations');



        Route::get('students/{student}/remove-course/{enrollment}',  'removeCourse')->name('admin.students.remove-course');
        Route::post('students/{student}/approve-registration',  'approveRegistration')->name('admin.students.approve-registration');
    });







    Route::controller(AdminAccountsManagersController::class)->group(function () {
        Route::get('accounts-managers', 'index')->name('admin.accounts.managers.view');
        Route::get('accounts-managers/create', 'create')->name('admin.accounts.managers.create');
        Route::get('accounts-managers/edit/{admin}', 'edit')->name('admin.accounts.managers.edit');
        Route::put('accounts-managers/update/{admin}', 'update')->name('admin.accounts.managers.update');
        Route::post('accounts-managers', 'store')->name('admin.accounts.managers.store');
        Route::get('accounts-managers/details/{admin}', 'show')->name('admin.accounts.managers.details');
        Route::delete('accounts-managers/del/{admin}', 'destroy')->name('admin.accounts.managers.delete');
    });



    Route::controller(AdminDepartmentCreditController::class)->group(function(){
        Route::get('department-credit', 'index')->name('admin.department.credit.view');
        Route::get('department-credit/create', 'create')->name('admin.department.credit.create');
        Route::post('department-credit', 'store')->name('admin.department.credit.store');
        Route::get('department-credit/edit/{departmentCredit}', 'edit')->name('admin.department.credit.edit');
        Route::put('department-credit/update/{departmentCredit}', 'update')->name('admin.department.credit.update');
        Route::delete('department-credit/{departmentCredit}', 'destroy')->name('admin.department.credit.delete');

        Route::get('/departments/{department}/levels', 'levels');
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
        Route::get('profile', 'profile')->name('student.view.profile');

        
        // post requests
        Route::post('createprofile', 'createprofile')->name('student.create.profile');
        Route::post('updateprofile', 'updateprofile')->name('student.update.profile');

        
    });
    Route::controller(StudentCourseRegistrationController::class)->group(function () {
        Route::prefix('course_registration')->group(function () {
            Route::get('/', 'courseregistration')->name('student.view.courseregistration');
            Route::get('/view/{id}', 'viewregistered')->name('student.view.courseregistered');
            Route::get('/session', 'sessioncourse')->name('student.view.sessioncourse');
            Route::get('/register/{semester_regid}/{session_id}/{semester_id}/{level}', 'registercourse')->name('student.view.registercourse');
            Route::get('departments/{department}/levels', 'levels');

            Route::post('/check-credit-load','checkCreditLoad')->name('check.credit.load');

            Route::post('/proceedsession', 'proceedsession')->name('student.proceed.session');
            Route::post('/courseregister', 'courseregister')->name('student.proceed.courseregister');
        });
    });

    Route::controller(StudentResultController::class)->group(function () {
        Route::prefix('/result')->group(function () {
            Route::get('/select', 'index')->name('student.view.result.select');
            Route::get('/view', 'view')->name('student.view.result');
        });
    });
    Route::controller(StudentAcceptanceController::class)->group(function () {
        Route::prefix('/acceptance')->group(function () {
            Route::get('/', 'index')->name('student.view.acceptance.all');
            Route::get('/view', 'view')->name('student.view.acceptance');
        });
    });
    Route::controller(StudentFeesController::class)->group(function () {
        Route::prefix('/fees')->group(function () {
            Route::get('/', 'index')->name('student.view.fees.all');
            Route::get('/view', 'view')->name('student.view.fees');
        });
    });
});


Route::prefix('parent')->middleware('parent')->group(function () {
    Route::controller(ParentController::class)->group(function () {
        Route::get('dashboard', 'index')->name('parent.view.dashboard');
    });
});
