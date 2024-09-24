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
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\AdminPaymentMethodController;
use App\Http\Controllers\Admin\AdminPaymentTypeController;
use App\Http\Controllers\Admin\AdminRejectedScoreController;
use App\Http\Controllers\Admin\AdminScoreApprovalController;
use App\Http\Controllers\Admin\AdminScoreAuditController;
use App\Http\Controllers\Admin\AdminStudentRegisteredCoursesController;
use App\Http\Controllers\Admin\AdminTimeTableController;


Route::prefix('admin')->middleware('admin')->group(function () {



    Route::controller(AdminPaymentController::class)->group(function(){
        Route::get('make-payments', 'index')->name('admin.payment.pay');
        Route::get('payments', 'payments')->name('admin.payments.show');

        Route::get('/payments/get-departments-and-levels',  'getDepartmentsAndLevels')->name('payments.getDepartmentsAndLevels');
        Route::get('/payment-types/get-amount', [AdminPaymentController::class, 'getAmount'])->name('payment-types.getAmount');
        Route::get('/payments/get-students',  'getStudents')->name('payments.getStudents');

        Route::post('/payments/submit', 'submitPaymentForm')->name('admin.payments.submit');
        Route::post('/payments/process', 'processPayment')->name('admin.payments.processPayment');

        Route::get('payments/verify/{gateway}', 'verifyPayment')->name('payment.verify');
        Route::get('payments/{payment}/receipt', 'generateReceipt')->name('payments.receipt');
        // Route::get('receipts/{receipt}', 'showReceipt')->name('receipts.show');

        Route::get('receipts/{receipt}', 'showReceipt')->name('admin.payments.showReceipt');




        Route::get('/payments/invoice-details/{invoiceId?}', 'showConfirmation')->name('admin.payments.showConfirmation');
        Route::get('/payments/invoice', 'generateTicket')->name('admin.payments.generateTicket');


    });
});














// Route::prefix('teacher')->middleware('teacher')->group(function () {
//     Route::controller(TeacherController::class)->group(function () {
//         Route::get('dashboard', 'index')->name('teacher.view.dashboard');
//     });
// });


