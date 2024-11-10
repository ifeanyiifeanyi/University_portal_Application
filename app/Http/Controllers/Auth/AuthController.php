<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\LoginActivity;
use App\Services\AuthService;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\SuspiciousLoginDetected;
use Illuminate\Http\RedirectResponse;
use App\Services\LoginTrackingService;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{

    protected $authService;
    protected $loginTrackingService;


    /**
     * CLASS
     * instance of our auth service class
     */
    public function __construct(AuthService $authService, LoginTrackingService $loginTrackingService)
    {
        $this->authService = $authService;
        $this->loginTrackingService = $loginTrackingService;
    }
    /**
     * GET
     * Display the login page
     */
    public function login()
    {
        // check if user is authenticated
        if ($this->authService->check()) {
            return $this->redirectBasedOnUserType($this->authService->user());
        }
        return view('auth.login');
    }

    /**
     * POST
     * validate and handle user login attempt
     */
    public function postLogin(LoginRequest $request)
    {

        // Check for too many attempts
        if ($this->loginTrackingService->tooManyAttempts($request->ip())) {
            $seconds = $this->loginTrackingService->getTimeUntilNextAttempt($request->ip());
            return redirect()->back()
                ->withErrors(['email' => "Too many login attempts. Please try again in {$seconds} seconds."]);
        }



        $credentials = $request->validated();
        $remember = $request->filled('remember');

        if ($this->authService->attempt($credentials, $remember)) {
            // Clear failed attempts
            $this->loginTrackingService->clearAttempts($request->ip());

            $user = $this->authService->user();

            // Track login using the service instead of the model directly
            $loginActivity = $this->loginTrackingService->track($user);

            return $this->redirectBasedOnUserType($user);
        }

        // Record failed attempt
        $this->loginTrackingService->recordFailedAttempt($request->ip());

        return redirect()->back()->withErrors(['email' => 'Invalid credentials']);
    }

    /**
     * GET
     * Dynamic redirect based on user type i
     */
    protected function redirectBasedOnUserType(User $user): RedirectResponse
    {
        $routes = [
            User::TYPE_ADMIN => 'admin.view.dashboard',
            User::TYPE_TEACHER => 'teacher.view.dashboard',
            User::TYPE_STUDENT => 'student.view.dashboard',
            User::TYPE_PARENT => 'parent.view.dashboard',
        ];

        return redirect()->route($routes[$user->user_type] ?? 'login.view');
    }


    public function logout()
    {

        $this->authService->logout();
        return redirect()->route('login.view');
    }
}
