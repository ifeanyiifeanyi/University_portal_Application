<?php

use Illuminate\Foundation\Application;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Support\Facades\Schedule;
use App\Http\Middleware\ParentMiddleware;
use App\Http\Middleware\StudentMiddleware;
use App\Http\Middleware\TeacherMiddleware;
use App\Http\Middleware\CheckInvoiceStatus;
use App\Http\Middleware\CheckFeesMiddleware;
use App\Http\Middleware\VerifyReceiptAccess;
use App\Http\Middleware\PermissionMiddleware;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckPendingInvoiceMiddleware;
use App\Http\Middleware\CheckFeesInstallmentMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'teacher' => TeacherMiddleware::class,
            'student' => StudentMiddleware::class,
            'parent' => ParentMiddleware::class,
            'checkforfees' => CheckFeesMiddleware::class,
            'check.pending.invoice' => CheckPendingInvoiceMiddleware::class,
            'check.invoice.status' => CheckInvoiceStatus::class,
            'verify.receipt' => VerifyReceiptAccess::class,
            'permission' => PermissionMiddleware::class,
            'security.headers' => SecurityHeaders::class, //not used yet
            'check.installment.fees'=>CheckFeesInstallmentMiddleware::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withCommands([
        'app:archive-logs' => 'weekly',
    ])
    ->create();
