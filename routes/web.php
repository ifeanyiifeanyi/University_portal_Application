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
use App\Http\Controllers\Admin\AdminStudentController;
use App\Http\Controllers\Admin\AdminSemesterController;
use App\Http\Controllers\Admin\AcademicSessionController;
use App\Http\Controllers\Admin\AdminAccountsManagersController;
use App\Http\Controllers\Admin\AdminApprovedScoreController;
use App\Http\Controllers\Admin\AdminCourseAssignmentController;
use App\Http\Controllers\Admin\AdminDepartmentCreditController;
use App\Http\Controllers\Admin\AdminTeacherAssignmentController;
use App\Http\Controllers\Admin\AdminAssignStudentCourseController;
use App\Http\Controllers\Admin\AdminAttendanceController;
use App\Http\Controllers\Admin\AdminGradeController;
use App\Http\Controllers\Admin\AdminPaymentMethodController;
use App\Http\Controllers\Admin\AdminPaymentTypeController;
use App\Http\Controllers\Admin\AdminRejectedScoreController;
use App\Http\Controllers\Admin\AdminScoreApprovalController;
use App\Http\Controllers\Admin\AdminScoreAuditController;
use App\Http\Controllers\Admin\AdminStudentRegisteredCoursesController;
use App\Http\Controllers\Admin\AdminTimeTableController;

// Route::get('/', function () {
//     return view('auth.login');
// });





Route::controller(AuthController::class)->group(function () {

    Route::get('/', 'login')->name('login.view');
    Route::post('/login', 'postLogin')->name('login.post');

    Route::post('logout', 'logout')->name('logout');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::post('/timetables/bulk-approve', [AdminTimeTableController::class, 'bulkApprove'])->name('admin.timetables.bulk-approve');
    Route::get('/timetables/approver-dashboard', [AdminTimeTableController::class, 'approverDashboard'])->name('admin.timetables.approver-dashboard');
    Route::get('/timetables/{id}/version-history', [AdminTimeTableController::class, 'versionHistory'])->name('admin.timetables.version-history');
    Route::get('/timetables/clone', [AdminTimeTableController::class, 'cloneTimetable'])->name('admin.timetables.clone');
    Route::get('/timetables/{id}/export-to-google-calendar', [AdminTimeTableController::class, 'exportToGoogleCalendar'])->name('admin.timetables.export-to-google-calendar');
    Route::get('/timetables/{id}/export', [AdminTimeTableController::class, 'export'])->name('admin.timetables.export');
});

Route::get('/public-timetable', [AdminTimeTableController::class, 'publicView'])->name('public.timetable');
Route::prefix('admin')->middleware('admin')->group(function () {

    Route::controller(AdminController::class)->group(function () {
        Route::get('dashboard', 'index')->name('admin.view.dashboard');
        Route::post('logout', 'logout')->name('admin.logout');
    });

    Route::controller(ProfileController::class)->group(function () {
        Route::get('profile', 'index')->name('admin.view.profile');
        Route::patch('update-profile/{user::slug}', 'update')->name('admin.update.profile');
        Route::patch('update-password/{user::slug}', 'updatePassword')->name('admin.update.password');
    });

    Route::controller(AcademicSessionController::class)->group(function () {
        Route::get('academic-session-manager', 'index')->name('admin.academic.session');
        Route::post('academic-session-manager', 'store')->name('admin.academic.store');

        Route::get('academic-session-manager/edit/{id}', 'edit')->name('admin.academic.edit');
        Route::put('academic-session-manager/update/{id}', 'update')->name('admin.academic.update');
        Route::get('academic-session-manager/delete/{id}', 'destroy')->name('admin.academic.delete');
    });

    Route::get('/semester-manager/search', [AdminSemesterController::class, 'show'])->name('semester.manager.search');
    Route::patch('semester-managers/bulk-action', [AdminSemesterController::class, 'bulkAction'])->name('semester.manager.bulk-action');
    Route::patch('semester-managers/{semester_manager}/toggle-current', [AdminSemesterController::class, 'toggleCurrent'])->name('semester-manager.toggle-current');

    Route::resource('semester-manager', AdminSemesterController::class);


    Route::resource('course-assignments', AdminCourseAssignmentController::class);

    Route::resource('faculty-manager', FacultyController::class);


    Route::controller(CourseController::class)->group(function () {
        Route::get('course-managers', 'index')->name('admin.courses.view');
        Route::post('courses/store', 'store')->name('admin.courses.store');
        Route::post('courses/update/{id}', 'update')->name('admin.courses.update');
        Route::get('courses/delete/{id}', 'destroy')->name('admin.courses.delete');
    });

    Route::controller(DepartmentController::class)->group(function () {
        Route::get('manage-department', 'index')->name('admin.department.view');
        Route::post('manage-department', 'store')->name('admin.department.store');
        Route::get('manage-department/edit/{id}', 'edit')->name('admin.department.edit');
        Route::get('manage-department/show/{id}', 'show')->name('admin.department.show');
        Route::put('manage-department/update/{id}', 'update')->name('admin.department.update');
        Route::delete('manage-department/del/{id}', 'destroy')->name('admin.department.delete');

        // unique route that helps separate the levels of study for a department
        Route::get('departments/{department}/levels', 'levels');
    });

    Route::controller(TeacherController::class)->group(function () {
        Route::get('manage-lecturers', 'index')->name('admin.teacher.view');
        Route::post('manage-lecturers/store', 'store')->name('admin.teacher.store');
        Route::get('manage-lecturers/create', 'create')->name('admin.teacher.create');
        Route::get('manage-lecturers/{teacher}/show', 'show')->name('admin.teacher.show');
        Route::get('manage-lecturers/{teacher}/edit', 'edit')->name('admin.teacher.edit');
        Route::put('manage-lecturers/{teacher}/update', 'update')->name('admin.teachers.update');
        Route::delete('manage-lecturers/{teacher}/delete', 'destroy')->name('admin.teachers.delete');

        // view department and courses the teacher as assigned to
        Route::get('lecturer-courses/{courseId}', 'courseDetails')->name('admin.teacher.course.show');
        Route::get('department/{department}/teacher/{teacher}', 'departmentDetails')->name('admin.teacher.department.show');

        // the teacher views students registered to the course assigned to them
        Route::get('/teacher/{teacherId}/course/{courseId}/semester/{semesterId}/academic-session/{academicSessionId}/students', 'viewRegisteredStudents')->name('teacher.course.students');

        Route::post('submit-student-assessment{assignmentId}', 'storeScores')->name('admin.store.scores');


        // exporting the table for submitting students scores as csv
        Route::get('export-scores/{assignmentId}', 'exportScores')->name('admin.export.scores');
        Route::post('import-scores/{assignmentId}', 'importScores')->name('admin.import.scores');

        // view assessments audits
        Route::get('/teacher/{teacher}/audits',  'viewAudits')->name('admin.teacher.audits');
    });

    // for controlling the grade types
    Route::controller(AdminGradeController::class)->group(function () {
        Route::get('/get-grade/{score}', 'getGrade');
    });

    Route::controller(AdminApprovedScoreController::class)->group(function () {
        // view approved scores
        Route::get('/approved-scores', 'approvedScores')->name('admin.approved_scores.view');

        // revert score to pending -- single
        Route::get('/approved/{score}/revert', 'revertApproval')->name('admin.score.approval.approved.revert');

        // revert back approved scores in bulk
        Route::post('/approved/bulk-revert', 'bulkRevertApproval')->name('admin.score.approval.approved.bulk-revert');

        //export n import
        Route::get('/approved/export', 'exportApprovedScores')->name('admin.score.approval.approved.export');
        Route::post('/approved/import', 'importApprovedScores')->name('admin.score.approval.approved.import');
    });

    Route::controller(AdminRejectedScoreController::class)->group(function () {
        Route::get('/rejected', 'rejectedScores')->name('admin.score.rejected.view');

        // --single revert
        Route::get('/rejected/{score}/single-revert', 'revertRejection')->name('admin.score.approval.rejected.revert');


        // -- bulk revert
        Route::post('/rejected/bulk-revert', 'bulkRevertRejection')->name('admin.score.approval.rejected.bulk-revert');

        // -- accept/approve rejected scores
        Route::post('/rejected/bulk-accept', 'bulkAcceptRejection')->name('admin.score.approval.rejected.bulk-accept');


        Route::get('/rejected/export', 'exportRejectedScores')->name('admin.rejected.score.export');
        Route::post('/rejected/import', 'importRejectedScores')->name('admin.rejected.score.import');
    });

    Route::controller(AdminScoreApprovalController::class)->group(function () {
        Route::get('score-approval', 'index')->name('admin.score.approval.view');
        Route::post('score-approval/approve/bulk', 'approveScore')->name('admin.score.approval.approve');
        Route::post('score-approval/reject', 'rejectScore')->name('admin.score.approval.reject');

        // --single approve
        Route::get('/approve/{score}/single-approve', 'singleApproveScore')->name('admin.score.approval.single.approve');

        // --single reject
        Route::get('/approve/{score}/single-reject', 'singleRejectScore')->name('admin.score.approval.single.reject');

        // export and import the table records
        Route::get('/scores/export', 'export')->name('admin.score.export');
        Route::post('/scores/import', 'import')->name('admin.score.import');
    });

    Route::controller(AdminScoreAuditController::class)->group(function () {
        Route::get('score-audit', 'index')->name('admin.score.audit.view');
        Route::get('score-audit/export', 'export')->name('admin.score.audit.export');
    });

    Route::controller(AdminStudentController::class)->group(function () {
        Route::get('student-manager', 'index')->name('admin.student.view');
        Route::get('student-manager/create', 'create')->name('admin.student.create');
        Route::get('student-manager/edit/{student}', 'edit')->name('admin.student.edit');
        Route::put('student-manager/update/{student}', 'update')->name('admin.student.update');
        Route::post('student-manager/store', 'store')->name('admin.student.store');
        Route::get('student-manager/details/{student}', 'show')->name('admin.student.details');
        Route::delete('student-manager/del/{student}', 'destroy')->name('admin.student.delete');

        // fetching the levels for departments based on the department selected
        Route::get('/departments/{department}/levels', 'levels');


        Route::get('students/{student}/audit', 'viewAudits')->name('admin.student.audit');
        // view registered courses
        Route::get('students/{studentId}/registration-history', 'studentRegistrationHistory')->name('admin.students.registration-history');
        // view score history
        Route::get('/student/{student}/approved-score-history',  'viewApprovedScoreHistory')->name('admin.student.approved-score-history');

        //assessment score audit history
        Route::get('/student/{student}/audits', 'viewAudits')->name('admin.student.audits');
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

    // this route was used or creating courses for student via students view table
    Route::controller(AdminAssignStudentCourseController::class)->group(function () {
        Route::get('assign-student-courses/{id}', 'showSemesterCourses')->name('admin.assign.courseForStudent');
        Route::post('assign-student-courses/{id}', 'registerCourses')->name('admin.students.register-courses.store');
        Route::get('students/{student}/course-registrations', 'showStudentCourseRegistrations')->name('admin.students.course-registrations');



        Route::delete('students/{student}/remove-course/{enrollment}',  'removeCourse')->name('admin.students.remove-course');
        Route::post('students/{student}/approve-registration',  'approveRegistration')->name('admin.students.approve-registration');
        Route::patch('students/{student}/courses/{enrollment}/status', 'updateCourseStatus')->name('admin.students.update-course-status');
    });

    Route::controller(AdminStudentRegisteredCoursesController::class)->group(function () {
        Route::get('student-registered-courses', 'index')->name('admin.students.all-course-registrations');

        Route::get('/student-course-registrations/export',  'export')->name('admin.course-registrations.export');
        Route::get('/student-course-registrations/{registration}',  'show')->name('admin.course-registrations.show');
        Route::patch('/student-course-registrations/{registration}/approve',  'approve')->name('admin.course-registrations.approve');
        Route::patch('/student-course-registrations/{registration}/reject',  'reject')->name('admin.course-registrations.reject');
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

    Route::controller(AdminDepartmentCreditController::class)->group(function () {
        Route::get('department-credit', 'index')->name('admin.department.credit.view');
        Route::get('department-credit/create', 'create')->name('admin.department.credit.create');
        Route::post('department-credit', 'store')->name('admin.department.credit.store');
        Route::get('department-credit/edit/{departmentCredit}', 'edit')->name('admin.department.credit.edit');
        Route::put('department-credit/update/{departmentCredit}', 'update')->name('admin.department.credit.update');
        Route::delete('department-credit/{departmentCredit}', 'destroy')->name('admin.department.credit.delete');

        Route::get('/departments/{department}/levels', 'levels');
    });

    Route::controller(AdminAttendanceController::class)->group(function () {
        Route::get('create-attendance', 'createAttendance')->name('admin.attendance.create');
        Route::post('create-attendance/create', 'storeAttendance')->name('admin.attendance.store');

        // API route for fetching students based on course
        Route::get('courses/{course}/students', 'getStudentsByCourse');
    });

    Route::controller(AdminTimeTableController::class)->group(function () {
        Route::get('timetable', 'index')->name('admin.timetable.view');
        Route::get('timetable/create', 'create')->name('admin.timetable.create');
        Route::get('timetable/details/{timeTable}', 'show')->name('admin.timetable.show');
        Route::post('timetable', 'store')->name('admin.timetable.store');
        Route::get('timetable/edit/{timetable}', 'edit')->name('admin.timetable.edit');
        Route::put('timetable/update/{timetable}', 'update')->name('admin.timetable.update');
        Route::delete('timetable/{timetable}', 'destroy')->name('admin.timetable.delete');

        Route::get('/courses/{course}/timetables', 'getTimetablesByCourse');



        Route::get('/department/{department}/levels', 'getDepartmentLevels');
        Route::get('/courses', 'getCourses');
        Route::get('/course-assignment', 'getCourseAssignment');


        Route::post('timetable/{timetable}/submit-for-approval', 'submitForApproval')->name('admin.timetable.submit-for-approval');
        Route::post('timetable/{timetable}/approve', 'approve')->name('admin.timetable.approve');
        Route::post('timetable/{timetable}/archive', 'archive')->name('admin.timetable.archive');
        Route::get('timetable/export/{format}', 'export')->name('admin.timetable.export');
        Route::get('timetable/print', 'printView')->name('admin.timetable.print');


        Route::get('timetables/bulk-create',  'bulkCreate')->name('admin.timetables.bulk_create');
        Route::post('timetables/bulk-store',  'bulkStore')->name('admin.timetables.bulk_store');
        Route::get('timetables/check-conflicts', 'checkConflicts')->name('admin.timetables.check');

        Route::get('timetables-by-department', 'viewByDepartment')->name('admin.timetables.by_department');
        Route::get('timetables-by-teacher', 'viewByTeacher')->name('admin.timetables.by_teacher');


        Route::get('/admin/timetable/calendar-data',  'getCalendarData')->name('admin.timetable.calendar-data');


        Route::get('timetable/drafts', 'draftIndex')->name('admin.timetable.draftIndex');
        Route::post('/admin/timetable/{timetable}/submit-for-approval',  'submitForApproval')->name('admin.timetable.submitForApproval');
        Route::post('/admin/timetable/{timetable}/archive',  'archive')->name('admin.timetable.archive');
    });

    Route::controller(AdminPaymentTypeController::class)->group(function(){
        Route::get('payment-types', 'index')->name('admin.payment_type.index');
        Route::get('payment-types/create', 'create')->name('admin.payment_type.create');
        Route::post('payment-types/', 'store')->name('admin.payment_type.store');
        Route::put('payment-types/{paymentType}', 'update')->name('admin.payment_type.update');

        Route::get('payment-types/{paymentType}/edit', 'edit')->name('admin.payment_type.edit');
        Route::get('payment-types/{paymentType}/show', 'show')->name('admin.payment_type.show');
        Route::get('payment-types/{paymentType}', 'destroy')->name('admin.payment_type.destroy');
    });

    Route::controller(AdminPaymentMethodController::class)->group(function(){
        Route::get('payment-method', 'index')->name('admin.payment_method.index');
        Route::get('payment-method/create', 'create')->name('admin.payment_method.create');
        Route::post('payment-method', 'store')->name('admin.payment_method.store');
        Route::get('payment-method/{paymentMethod}/edit', 'edit')->name('admin.payment_method.edit');
        Route::get('payment-method/{paymentMethod}/details', 'show')->name('admin.payment_method.show');
        Route::delete('payment-method/{paymentMethod}/del', 'destroy')->name('admin.payment_method.destroy');
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


Route::prefix('parent')->middleware('parent')->group(function () {
    Route::controller(ParentController::class)->group(function () {
        Route::get('dashboard', 'index')->name('parent.view.dashboard');
    });
});
