<?php

use Illuminate\Foundation\Application;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CheckFeesMiddleware;
use App\Http\Middleware\CheckInvoiceStatus;
use App\Http\Middleware\CheckPendingInvoiceMiddleware;
use App\Http\Middleware\ParentMiddleware;
use App\Http\Middleware\PermissionMiddleware;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\StudentMiddleware;
use App\Http\Middleware\TeacherMiddleware;
use App\Http\Middleware\VerifyReceiptAccess;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

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

        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
