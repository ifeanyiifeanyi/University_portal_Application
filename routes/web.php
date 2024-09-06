<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Parent\ParentController;
use App\Http\Controllers\Parent\ChildrenController;
use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\Teacher\TeacherController;
use App\Http\Controllers\Student\StudentFeesController;
use App\Http\Controllers\Student\OnlineClassesController;
use App\Http\Controllers\Student\StudentResultController;
use App\Http\Controllers\Teacher\TeacherCoursesController;
use App\Http\Controllers\Student\StudentAcceptanceController;
use App\Http\Controllers\Teacher\TeacherDepartmentController;
use App\Http\Controllers\Admin\AdminAccountsManagersController;
use App\Http\Controllers\Admin\AdminDepartmentCreditController;
use App\Http\Controllers\Admin\AdminTeacherAssignmentController;
use App\Http\Controllers\Admin\AdminAssignStudentCourseController;
use App\Http\Controllers\Student\StudentCourseRegistrationController;
use App\Http\Controllers\Teacher\TeacherAttendanceController;

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
    Route::prefix('courses')->group(function () {
    Route::controller(TeacherCoursesController::class)->group(function () {
        Route::get('/', 'courses')->name('teacher.view.courses');
        Route::get('/students/{id}', 'students')->name('teacher.view.courses.students');
        Route::get('/get-grade/{total}','getGrade')->name('getGrade');
        Route::post('/uploadresult/{courseid}', 'uploadresult')->name('teacher.upload.result');
        Route::get('/export/{id}','exportassessment')->name('exportassessment');
        Route::post('/importassessment', 'ImportAssessmentCsv')->name('importassessment.csv');
        

    });
});

Route::prefix('attendance')->group(function () {
    Route::controller(TeacherAttendanceController::class)->group(function () {
        Route::get('/', 'attendance')->name('teacher.view.attendance');
        Route::get('/create', 'create')->name('teacher.view.create.attendance');
        Route::get('/createattendance/{sessionid}/{semesterid}/{departmentid}/{courseid}', 'createattendance')->name('teacher.create.attendance');
        Route::get('/view/{sessionid}/{semesterid}/{departmentid}/{courseid}', 'view')->name('teacher.view.attendees');
        Route::post('/create-attendance', 'createstudentAttendance')->name('attendance.create');
        Route::post('/update-attendance', 'updateAttendance')->name('teacher.attendance.update');
    });
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
            Route::get('/register/{semester_regid}/{session_id}/{semester_id}', 'registercourse')->name('student.view.registercourse');
            Route::get('departments/{department}/levels', 'levels');

            Route::post('/check-credit-load','checkCreditLoad')->name('check.credit.load');

            Route::post('/proceedsession', 'proceedsession')->name('student.proceed.session');
            Route::post('/courseregister', 'courseregister')->name('student.proceed.courseregister');
        });
    });

    Route::controller(StudentResultController::class)->group(function () {
        Route::prefix('/result')->group(function () {
            Route::get('/select', 'index')->name('student.view.result.select');
            Route::get('/view/{session}/{semester}/{teacherid}', 'view')->name('student.view.result');
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


    Route::controller(OnlineClassesController::class)->group(function () {
        Route::get('onlineclasses', 'index')->name('student.view.onlineclasses');  
    });
});


Route::prefix('parent')->middleware('parent')->group(function () {
    Route::controller(ParentController::class)->group(function () {
        Route::get('dashboard', 'index')->name('parent.view.dashboard');
        Route::get('profile', 'profile')->name('parent.view.profile');
    });
    Route::controller(ChildrenController::class)->group(function () {
        Route::prefix('/children')->group(function () {
            Route::get('/', 'index')->name('parent.view.childrens');
            Route::get('/view/{id}', 'view')->name('parent.view.child');
            Route::get('/result/{session}/{semester}/{teacherid}/{studentid}', 'result')->name('parent.view.child.result');
        });
    });
});
