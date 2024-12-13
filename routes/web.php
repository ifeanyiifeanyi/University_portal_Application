<?php

use App\Models\Receipt;
use App\Models\Student;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\FacultyController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Parent\ParentController;
use App\Http\Controllers\Parent\ChildrenController;
use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\Teacher\TeacherController;
use App\Http\Controllers\Admin\AdminGradeController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\LogActivityController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\AdminStudentController;
use App\Http\Controllers\Admin\AdminProgramsController;
use App\Http\Controllers\Admin\AdminSemesterController;
use App\Http\Controllers\Admin\AdminUserRoleController;
use App\Http\Controllers\Admin\BackupSettingController;
use App\Http\Controllers\Student\StudentFeesController;
use App\Http\Controllers\Admin\AdminTimeTableController;
use App\Http\Controllers\Admin\ProofOfPaymentController;
use App\Http\Controllers\Student\FeesPaymentsController;
use App\Http\Controllers\Admin\AcademicSessionController;
use App\Http\Controllers\Admin\AdminAttendanceController;
use App\Http\Controllers\Admin\AdminScoreAuditController;
use App\Http\Controllers\Student\OnlineClassesController;
use App\Http\Controllers\Student\StudentResultController;
use App\Http\Controllers\Admin\AdminPaymentTypeController;
use App\Http\Controllers\Admin\PasswordRecoveryController;
use App\Http\Controllers\Teacher\TeacherCoursesController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\AdminApprovedScoreController;
use App\Http\Controllers\Admin\AdminPaymentMethodController;
use App\Http\Controllers\Admin\AdminRejectedScoreController;
use App\Http\Controllers\Admin\AdminScoreApprovalController;
use App\Http\Controllers\Admin\AdminInvoiceManagerController;
use App\Http\Controllers\Student\StudentAcceptanceController;
use App\Http\Controllers\Teacher\TeacherAttendanceController;
use App\Http\Controllers\Teacher\TeacherDepartmentController;
use App\Http\Controllers\Admin\AdminAccountsManagersController;
use App\Http\Controllers\Admin\AdminCourseAssignmentController;
use App\Http\Controllers\Admin\AdminDepartmentCreditController;
use App\Http\Controllers\Admin\AdminTeacherAssignmentController;
use App\Http\Controllers\Admin\AdminAssignStudentCourseController;
use App\Http\Controllers\Student\StudentCourseRegistrationController;
use App\Http\Controllers\Admin\AdminStudentRegisteredCoursesController;
use App\Http\Controllers\Admin\AdminSupportTicketController;
use App\Http\Controllers\Admin\TeacherController as AdminTeacherController;
use App\Http\Controllers\Student\StudentSupportTicketController;

Route::get('/migrate-and-seed', function () {
    try {
        // Run database migrations
        Artisan::call('migrate');
        $migrationOutput = Artisan::output();

        // Run storage link seeder
        Artisan::call('storage:link');
        $storageLinkOutput = Artisan::output();

        // Run database seeders
        Artisan::call('db:seed', [
            '--class' => 'DatabaseSeeder'
        ]);
        $seedingOutput = Artisan::output();

        return response()->json([
            'migration_output' => $migrationOutput,
            'storage_link_output' => $storageLinkOutput,
            'seeding_output' => $seedingOutput
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }
});

// Route::middleware(['guest', 'security.headers'])->controller(AuthController::class)->group(function () {

Route::middleware(['guest'])->controller(AuthController::class)->group(function () {

    Route::get('/', 'login')->name('login.view');
    Route::post('/', 'postLogin')->name('login.post');

    Route::get('logout', 'logout')->name('logout');
});


Route::get('receipt-details/{receipt}', function (Receipt $receipt) {
    return view('admin.show_receipt', compact('receipt'));
})->name('receipts.show');

Route::get('student-info/{student}', function (Student $student) {
    return view('admin.student.show_student_idcard', compact('student'));
})->name('student.show');



Route::middleware('admin')->group(function () {
    Route::post('/timetables/bulk-approve', [AdminTimeTableController::class, 'bulkApprove'])->name('admin.timetables.bulk-approve');
    Route::get('/timetables/approver-dashboard', [AdminTimeTableController::class, 'approverDashboard'])->name('admin.timetables.approver-dashboard');
    Route::get('/timetables/{id}/version-history', [AdminTimeTableController::class, 'versionHistory'])->name('admin.timetables.version-history');
    Route::get('/timetables/clone', [AdminTimeTableController::class, 'cloneTimetable'])->name('admin.timetables.clone');
    Route::get('/timetables/{id}/export-to-google-calendar', [AdminTimeTableController::class, 'exportToGoogleCalendar'])->name('admin.timetables.export-to-google-calendar');
    Route::get('/timetables/{id}/export', [AdminTimeTableController::class, 'export'])->name('admin.timetables.export');
});

Route::get('/public-timetable', [AdminTimeTableController::class, 'publicView'])->name('public.timetable');

Route::controller(PasswordRecoveryController::class)->middleware(['guest', 'security.headers'])->group(function () {
    Route::get('password/recovery',  'showRecoveryForm')->name('password.recovery.form');
    Route::post('password/recovery/send',  'sendRecoveryCode')->name('password.recovery.send');
    Route::get('password/reset',  'showResetForm')->name('password.reset.form');
    Route::post('password/reset',  'reset')->name('password.reset');
});

Route::prefix('admin')->middleware('admin')->group(function () {

    Route::controller(LogActivityController::class)->group(function () {
        Route::get('/activities',  'index')->name('activities.index');
        Route::delete('/activities/{id}',  'destroy')->name('activities.destroy');
    });

    Route::controller(BackupSettingController::class)->group(function () {
        Route::get('backups', 'index')->name('admin.backups.index');

        Route::get('backups/create', 'create')->name('admin.backups.create');
        Route::get('backups/download/{file_name}', 'download')->name('admin.backups.download');
        Route::get('backups/restore/{file_name}', 'restore')->name('admin.backups.restore');
        Route::delete('backups/delete/{file_name}', 'delete')->name('admin.backups.delete');

        Route::get('backups/files', 'createFilesOnly')->name('admin.backups.files');
        Route::get('backups/database', 'createDatabaseOnly')->name('admin.backups.database');

    });

    Route::controller(AdminProgramsController::class)->group(function(){
        Route::get('programs', 'index')->name('admin.programs.index');
        Route::get('programs/create', 'create')->name('admin.programs.create');
        Route::post('programs', 'store')->name('admin.programs.store');
        Route::get('programs/{program}/edit', 'edit')->name('admin.programs.edit');
        Route::put('programs/{program}', 'update')->name('admin.programs.update');
        Route::delete('programs/{program}', 'destroy')->name('admin.programs.destroy');
    });

    Route::controller(AdminSupportTicketController::class)->group(function () {
        Route::get('support-tickets', 'index')->name('admin.support_tickets.index');
        Route::get('support-tickets/{ticket}/show', 'show')->name('admin.support_tickets.show');
        Route::post('admin-tickets/{ticket}/respond', 'respond')->name('admin.support_tickets.respond');


        Route::patch('support-tickets/{ticket}/update-status', 'updateStatus')->name('admin.support_tickets.update_status');
        Route::patch('support-tickets/{ticket}/update-priority', 'updatePriority')->name('admin.support_tickets.update_priority');

        Route::get('ticket-history/{ticket}', 'ticketHistory')->name('admin.support_tickets.history');

        Route::delete('support-tickets/{ticket}', 'destroy')->name('admin.support_tickets.destroy');
    });


    // Dashboard Routes
    Route::middleware('permission:view dashboard')->group(function () {
        Route::controller(AdminController::class)->group(function () {
            Route::get('dashboard', 'index')->name('admin.view.dashboard');
            Route::post('logout', 'logout')->name('admin.logout');
        });
    });

    // admin profile manager
    Route::controller(ProfileController::class)->group(function () {
        Route::get('profile/view', 'index')->name('admin.view.profile');
        Route::patch('update-profile/{user::slug}', 'update')->name('admin.update.profile');
        Route::patch('update-password/{user::slug}', 'updatePassword')->name('admin.update.password');
    });


    // Academic Session Management
    Route::middleware('permission:manage academic sessions')->group(function () {

        Route::controller(AcademicSessionController::class)->group(function () {
            Route::get('academic-session-manager', 'index')->name('admin.academic.session');
            Route::post('academic-session-manager', 'store')->name('admin.academic.store');

            Route::get('academic-session-manager/edit/{id}', 'edit')->name('admin.academic.edit');
            Route::put('academic-session-manager/update/{id}', 'update')->name('admin.academic.update');
            Route::get('academic-session-manager/delete/{id}', 'destroy')->name('admin.academic.delete');
        });
    });


    // Semester Management
    Route::middleware('permission:manage semester')->group(function () {
        Route::get('/semester-manager/search', [AdminSemesterController::class, 'show'])->name('semester.manager.search');
        Route::patch('semester-managers/bulk-action', [AdminSemesterController::class, 'bulkAction'])->name('semester.manager.bulk-action');
        Route::patch('semester-managers/{semester_manager}/toggle-current', [AdminSemesterController::class, 'toggleCurrent'])->name('semester-manager.toggle-current');

        Route::resource('semester-manager', AdminSemesterController::class);
    });

    // Course Assignment Management
    Route::middleware('permission:manage courses')->group(function () {

        Route::resource('course-assignments', AdminCourseAssignmentController::class);
    });

    // Faculty Management
    Route::middleware('permission:manage faculties')->group(function () {
        Route::resource('faculty-manager', FacultyController::class);
    });

    // Course Management
    Route::middleware('permission:manage courses')->group(function () {
        Route::controller(CourseController::class)->group(function () {
            Route::get('course-managers', 'index')->name('admin.courses.view');
            Route::post('courses/store', 'store')->name('admin.courses.store');
            Route::post('courses/update/{id}', 'update')->name('admin.courses.update');
            Route::get('courses/delete/{id}', 'destroy')->name('admin.courses.delete');
        });
    });



    // Department Management
    Route::middleware('permission:manage departments')->group(function () {
        Route::controller(DepartmentController::class)->group(function () {
            Route::get('manage-department', 'index')->name('admin.department.view');
            Route::post('manage-department', 'store')->name('admin.department.store');
            Route::get('manage-department/edit/{id}', 'edit')->name('admin.department.edit');
            Route::get('manage-department/show/{department}', 'show')->name('admin.department.show');

            Route::get('manage-department/courses/{id}', 'teacherCourses')->name('admin.department.teacherCourses');
            Route::get('manage-department/students/{id}', 'departmentStudent')->name('admin.department.departmentStudent');
            Route::get('admin/departments/{id}/export-csv', 'exportCsv')->name('admin.department.export-csv');

            Route::get('admin/students/{id}/export-students-csv', 'exportStudentsForDepartment')->name('admin.department.exportCsv');



            Route::put('manage-department/update/{department}', 'update')->name('admin.department.update');

            Route::delete('department/delete/{department}', 'destroy')->name('admin.department.delete');

            // unique route that helps separate the levels of study for a department
            Route::get('departments/{department}/levels', 'levels');
        });
    });



    // Lecturers Management
    Route::middleware('permission:manage lecturers')->group(function () {
        Route::controller(AdminTeacherController::class)->group(function () {
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
    });

    // Grade Management
    Route::middleware('permission:view grades')->group(function () {
        // for controlling the grade types
        Route::controller(AdminGradeController::class)->group(function () {
            Route::get('/get-grade/{score}', 'getGrade');
        });
    });

    // manage student scores
    Route::middleware('permission:manage student scores')->group(function () {
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
    });

    // reject submitted score
    Route::middleware('permission:view rejected scores')->group(function () {

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

    Route::middleware('permission:audit student scores')->group(function () {
        Route::controller(AdminScoreAuditController::class)->group(function () {
            Route::get('score-audit', 'index')->name('admin.score.audit.view');
            Route::get('score-audit/export', 'export')->name('admin.score.audit.export');
        });
    });


    // Student Management
    Route::middleware('permission:manage students')->group(function () {
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

            // TODO: student registration through excel format
            // Route::get('students/import',  'showImportForm')->name('admin.students.import');
            // Route::post('students/import',  'importStudents')->name('admin.students.import.process');

            Route::get('students/template/download/{format?}', 'downloadTemplate')->name('admin.students.template.download');
            Route::post('students/import/verify', 'verifyImportData')->name('admin.students.import.verify');

            // verify import
            Route::post('students-import/verify', 'importVerify')->name('admin.students.import_verify');

            // now attempt import
            Route::post('students/import/process', 'importProcess')->name('admin.students.import.process');



            Route::get('upload/status-process', 'importStatus')->name('admin.student.import-status');

            // * view student idcard
            Route::get('student/{student}/idcard', 'generateIdCard')->name('admin.student.idcard');
        });
    });

    //assign and manage the teacher content/courses
    Route::middleware('permission:assign department courses to lecturers')->group(function () {
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
    });

    Route::middleware('permission:manage course levels')->group(function () {
        // this route was used or creating courses for student via students view table
        Route::controller(AdminAssignStudentCourseController::class)->group(function () {
            Route::get('assign-student-courses/{student}', 'showSemesterCourses')->name('admin.assign.courseForStudent');

            Route::post('assign-student-courses/{id}', 'registerCourses')->name('admin.students.register-courses.store');

            Route::get('students/{student}/course-registrations', 'showStudentCourseRegistrations')->name('admin.students.course-registrations');



            Route::delete('students/{student}/remove-course/{enrollment}',  'removeCourse')->name('admin.students.remove-course');
            Route::post('students/{student}/approve-registration',  'approveRegistration')->name('admin.students.approve-registration');
            Route::patch('students/{student}/courses/{enrollment}/status', 'updateCourseStatus')->name('admin.students.update-course-status');

            Route::get('course-registration/student-list/{student_id?}', 'index')->name('admin.course_registration.student_list');
        });
    });

    Route::middleware('permission:manage student course registrations')->group(function () {
        Route::controller(AdminStudentRegisteredCoursesController::class)->group(function () {
            Route::get('student-registered-courses', 'index')->name('admin.students.all-course-registrations');

            Route::get('/student-course-registrations/export',  'export')->name('admin.course-registrations.export');
            Route::get('/student-course-registrations/{registration}',  'show')->name('admin.course-registrations.show');
            Route::patch('/student-course-registrations/{registration}/approve',  'approve')->name('admin.course-registrations.approve');
            Route::patch('/student-course-registrations/{registration}/reject',  'reject')->name('admin.course-registrations.reject');
        });
    });

    Route::middleware('permission:view administrators')->group(function () {
        Route::controller(AdminAccountsManagersController::class)->group(function () {
            Route::get('accounts-managers', 'index')->name('admin.accounts.managers.view');
            Route::get('accounts-managers/create', 'create')->name('admin.accounts.managers.create');
            Route::get('accounts-managers/edit/{admin}', 'edit')->name('admin.accounts.managers.edit');
            Route::put('accounts-managers/update/{admin}', 'update')->name('admin.accounts.managers.update');
            Route::post('accounts-managers', 'store')->name('admin.accounts.managers.store');
            Route::get('accounts-managers/details/{admin}', 'show')->name('admin.accounts.managers.details');
            Route::delete('accounts-managers/del/{admin}', 'destroy')->name('admin.accounts.managers.delete');
        });
    });

    Route::middleware('permission:assign semester courses to department')->group(function () {
        Route::controller(AdminDepartmentCreditController::class)->group(function () {
            Route::get('department-credit', 'index')->name('admin.department.credit.view');
            Route::get('department-credit/create', 'create')->name('admin.department.credit.create');
            Route::post('department-credit', 'store')->name('admin.department.credit.store');
            Route::get('department-credit/edit/{departmentCredit}', 'edit')->name('admin.department.credit.edit');
            Route::put('department-credit/update/{departmentCredit}', 'update')->name('admin.department.credit.update');
            Route::delete('department-credit/{departmentCredit}', 'destroy')->name('admin.department.credit.delete');

            Route::get('/departments/{department}/levels', 'levels');
        });
    });


    // Attendance Management
    Route::middleware('permission:manage attendance')->group(function () {
        Route::controller(AdminAttendanceController::class)->group(function () {
            Route::get('create-attendance', 'createAttendance')->name('admin.attendance.create');
            Route::post('create-attendance/create', 'storeAttendance')->name('admin.attendance.store');

            // API route for fetching students based on course
            Route::get('courses/{course}/students', 'getStudentsByCourse');
        });
    });


    // Timetable Management
    Route::middleware('permission:view timetable')->group(function () {

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
    });

    // Payment Management
    Route::middleware('permission:manage payment types')->group(function () {
        Route::controller(AdminPaymentTypeController::class)->group(function () {
            Route::get('payment-types', 'index')->name('admin.payment_type.index');
            Route::get('payment-types/create', 'create')->name('admin.payment_type.create');
            Route::post('payment-types/', 'store')->name('admin.payment_type.store');
            Route::put('payment-types/{paymentType}', 'update')->name('admin.payment_type.update');

            Route::get('payment-types/{paymentType}/edit', 'edit')->name('admin.payment_type.edit');
            Route::get('payment-types/{paymentType}/show', 'show')->name('admin.payment_type.show');
            Route::get('payment-types/{paymentType}', 'destroy')->name('admin.payment_type.destroy');
        });
    });

    Route::middleware('permission:manage payment methods')->group(function () {
        Route::controller(AdminPaymentMethodController::class)->group(function () {
            Route::get('payment-method', 'index')->name('admin.payment_method.index');
            Route::get('payment-method/create', 'create')->name('admin.payment_method.create');
            Route::post('payment-method', 'store')->name('admin.payment_method.store');
            Route::put('payment-method/{paymentMethod}/update', 'update')->name('admin.payment_method.update');
            Route::get('payment-method/{paymentMethod}/edit', 'edit')->name('admin.payment_method.edit');
            Route::get('payment-method/{paymentMethod}/details', 'show')->name('admin.payment_method.show');
            Route::delete('payment-method/{paymentMethod}/del', 'destroy')->name('admin.payment_method.destroy');
        });
    });


    // Payment Management
    Route::middleware('permission:pay fees')->group(function () {
        Route::controller(AdminPaymentController::class)->group(function () {
            Route::get('make-payments', 'index')->name('admin.payment.pay');
            Route::get('make-payments/transfer/{invoice}', 'payTransfer')->name('admin.payment.pay_manual');

            Route::get('payments', 'payments')->name('admin.payments.show');

            Route::get('/payments/get-departments-and-levels',  'getDepartmentsAndLevels')->name('payments.getDepartmentsAndLevels');
            Route::get('/payment-types/get-amount', [AdminPaymentController::class, 'getAmount'])->name('payment-types.getAmount');
            Route::get('/payments/get-students',  'getStudents')->name('payments.getStudents');

            Route::post('/payments/submit', 'submitPaymentForm')->name('admin.payments.submit');
            Route::post('/payments/process', 'processPayment')->name('admin.payments.processPayment');

            Route::get('payments/verify/{gateway}', 'verifyPayment')->name('payment.verify');
            Route::get('payments/{payment}/receipt', 'generateReceipt')->name('payments.receipt');
            // Route::get('receipts/{receipt}', 'showReceipt')->name('receipts.show');

            Route::get('receipts/{receipt}', 'showReceipt')
                ->name('admin.payments.showReceipt')
                ->middleware('verify.receipt');


            Route::post('/payments/change-method',  'changePaymentMethod')->name('admin.payments.changePaymentMethod');

            Route::get('/payments/invoice-details/{invoiceId?}', 'showConfirmation')->name('admin.payments.showConfirmation');
            // ->middleware(['check.pending.invoice']);


            Route::get('/payments/invoice', 'generateTicket')->name('admin.payments.generateTicket');
            Route::get('/payments/active', 'ProcessedPayments')->name('admin.payments.ProcessedPayments');
        });
    });

    Route::controller(ProofOfPaymentController::class)->group(function () {
        Route::get('cancel-invoice/{invoice}', 'destroy')->name('admin.invoice.cancel');

        Route::post('process-invoice-manual', 'processManualPayment')->name('admin.payments.process-manual');

        Route::get('/payments/prove/{paymentId?}', 'showConfirmationProve')->name('admin.payments.showConfirmation_prove');

        Route::put('/payments/{payment}/verify', 'verifyPayment')->name('admin.payments.verify');
        Route::put('/payments/{payment}/reject', 'rejectPayment')->name('admin.payments.reject');
    });

    // Notifications Management
    Route::middleware('permission:manage notifications')->group(function () {
        Route::controller(AdminNotificationController::class)->group(function () {
            Route::get('notifications', 'index')->name('admin.notification.view');

            // Route::get('/notifications', 'index')->name('admin.notifications.index');
            Route::post('/notifications/{id}/mark-as-read', 'markAsRead')->name('admin.notifications.markAsRead');
            Route::post('/notifications/mark-all-as-read', 'markAllAsRead')->name('admin.notifications.markAllAsRead');
            Route::delete('/notifications/{id}', 'destroy')->name('admin.notifications.destroy');
            Route::get('/notifications/latest', 'getLatestNotifications')->name('admin.notifications.latest');
            Route::get('/notifications/view/{id}', 'viewNotification')->name('admin.notifications.view');
        });
    });

    // Invoice
    Route::middleware('permission:view invoice manager')->group(function () {
        Route::controller(AdminInvoiceManagerController::class)->group(function () {
            Route::get('invoice-manager', 'index')->name('admin.invoice.view');
            Route::get('invoice-manager/{invoice}/details', 'show')->name('admin.invoice.show');
            Route::get('invoice-manager/download/{invoice}', 'download')->name('admin.invoice.download');
        });
    });









    // // Role and Permission Management
    // Route::middleware('permission:view roles')->group(function () {

    Route::controller(RoleController::class)->group(function () {
        Route::get('roles', 'index')->name('admin.roles.index');
        Route::get('/roles/create',  'create')->name('admin.roles.create');
        Route::post('/roles',  'store')->name('admin.roles.store');
        Route::get('/roles/{role}/edit',  'edit')->name('admin.roles.edit');
        Route::put('/roles/{role}',  'update')->name('admin.roles.update');
        Route::delete('/roles/{role}',  'destroy')->name('admin.roles.destroy');
    });

    Route::controller(PermissionController::class)->group(function () {
        Route::get('permissions', 'index')->name('admin.permissions.index');
        Route::get('/permissions/create',  'create')->name('admin.permissions.create');
        Route::post('/permissions',  'store')->name('admin.permissions.store');
        Route::get('/permissions/{permission}/edit',  'edit')->name('admin.permissions.edit');
        Route::put('/permissions/{permission}',  'update')->name('admin.permissions.update');
        Route::delete('/permissions/{permission}',  'destroy')->name('admin.permissions.destroy');
    });

    Route::controller(AdminUserRoleController::class)->group(function () {
        Route::get('admin-roles', 'index')->name('admin.admin-users.roles');
        Route::post('/admin-users/assign-roles', 'assignRoles')->name('admin.admin-users.assign-roles');
        Route::delete('/admin/users/revoke-role', 'revokeRole')->name('admin.admin-users.revoke-role');
    });
    // });






});



//! student routes
Route::prefix('student')->middleware('student')->group(function () {
    Route::controller(StudentController::class)->group(function () {
        Route::get('dashboard', 'index')->name('student.view.dashboard');
        Route::get('profile', 'profile')->name('student.view.profile');
        Route::get('virtualid', 'virtualid')->name('student.view.virtualid');


        // post requests
        Route::post('createprofile', 'createprofile')->name('student.create.profile');
        Route::post('updateprofile', 'updateprofile')->name('student.update.profile');
    });
    Route::controller(StudentCourseRegistrationController::class)->middleware('checkforfees')->group(function () {
        Route::prefix('course_registration')->group(function () {
            Route::get('/', 'courseregistration')->name('student.view.courseregistration');
            Route::get('/view/{id}', 'viewregistered')->name('student.view.courseregistered');
            Route::get('/session', 'sessioncourse')->name('student.view.sessioncourse');
            Route::get('/register/{semester_regid}/{session_id}/{semester_id}/{level}', 'registercourse')->name('student.view.registercourse');
            Route::get('departments/{department}/levels', 'levels');

            Route::post('/check-credit-load', 'checkCreditLoad')->name('check.credit.load');

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
            Route::get('/pay', 'pay')->name('student.view.fees.pay');
            Route::post('/process', 'process')->name('student.view.fees.process');
            Route::get('/invoice/{id}', 'invoice')->name('student.view.fees.invoice');
            Route::get('departments/{department}/levels', 'levels');

            Route::post('/payments/process', 'processPayment')->name('student.view.fees.processPayment');

            Route::get('payments/verify/{gateway}', 'verifyPayment')->name('student.fees.payment.verify');

            Route::get('receipts/{receipt}', 'showReceipt')->name('student.fees.payments.showReceipt');


            // Route::get('/payments/invoice-details/{invoiceId?}', 'showConfirmation')->name('student.fees.payments.showConfirmation');

        });
    });
    Route::controller(FeesPaymentsController::class)->group(function () {
        Route::prefix('/payments')->group(function () {
            Route::get('/', 'index')->name('student.view.payments');
        });
    });
    Route::controller(OnlineClassesController::class)->group(function () {
        Route::get('onlineclasses', 'index')->name('student.view.onlineclasses');
    });


    //: SUPPORT TICKET SECTION added to student section by ifeanyi
    Route::controller(StudentSupportTicketController::class)->group(function () {
        Route::get('support-tickets', 'index')->name('student.view.support-tickets');
        Route::get('support-tickets/{ticket}', 'show')->name('student.support-tickets.show');
        Route::get('tickets/create', 'create')->name('student.support-tickets.create');
        Route::post('support-tickets', 'store')->name('student.tickets.store');
        Route::post('tickets/{ticket}/reply', 'reply')->name('student.tickets.reply');
    });
});

//! Student parent routes
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
            Route::get('receipts/{receipt}', 'showReceipt')->name('parent.fees.payments.showReceipt');
        });
    });
});

//! the Lecturer routes
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
            Route::get('/get-grade/{total}', 'getGrade')->name('getGrade');
            Route::post('/uploadresult/{courseid}', 'uploadresult')->name('teacher.upload.result');
            Route::get('/export/{id}', 'exportassessment')->name('exportassessment');
            Route::post('/importassessment', 'ImportAssessmentCsv')->name('importassessment.csv');
        });
    });

    Route::prefix('attendance')->group(function () {
        Route::controller(TeacherAttendanceController::class)->group(function () {
            Route::get('/', 'attendance')->name('teacher.view.attendance');
            Route::get('/create', 'create')->name('teacher.view.create.attendance');
            Route::get('/createattendance/{sessionid}/{semesterid}/{departmentid}/{courseid}', 'createattendance')->name('teacher.create.attendance');
            Route::get('/view/{attendanceid}/{departmentid}/{courseid}', 'view')->name('teacher.view.attendees');
            Route::post('/create-attendance', 'createstudentAttendance')->name('attendance.create');
            Route::post('/update-attendance', 'updateAttendance')->name('teacher.attendance.update');
        });
    });
});
