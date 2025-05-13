<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Support\Facades\Log;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    // public function register(): void
    // {
    //     $this->reportable(function (Throwable $e) {
    //         // Log the exception with additional context
    //         Log::error('Application Exception: ' . $e->getMessage(), [
    //             'exception' => get_class($e),
    //             'file' => $e->getFile(),
    //             'line' => $e->getLine(),
    //             'trace' => $e->getTraceAsString(),
    //             'url' => request()->fullUrl(),
    //             'user_id' => auth()->id() ?? 'guest',
    //             'ip' => request()->ip(),
    //             'user_agent' => request()->userAgent(),
    //         ]);
    //     });
    // }
    public function register(): void
    {
        // Register specific exception handlers
        $this->renderable(function (\InvalidArgumentException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Invalid argument provided',
                    'message' => app()->environment('production') ? 'Bad Request' : $e->getMessage()
                ], 400);
            }
        });

        // Your existing reportable method
        $this->reportable(function (Throwable $e) {
            Log::error('Application Exception: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'url' => request()->fullUrl(),
                'user_id' => auth()->id() ?? 'guest',
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        // If the request expects JSON, return JSON response
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'An error occurred while processing your request.',
                'message' => app()->environment('production') ? 'Server Error' : $e->getMessage()
            ], 500);
        }

        // Use our custom generic exception view for all unhandled exceptions
        if (app()->environment('production') && !$this->isHttpException($e)) {
            return response()->view('errors.generic-exception', ['exception' => $e], 500);
        }

        return parent::render($request, $e);
    }
}
