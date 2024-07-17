<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class AuthController extends Controller
{

    protected $authService;

    /**
     * CLASS
     * instance of our auth service class
     */
    public function __construct(AuthService $authService){

        $this->authService = $authService;
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
        $credentials = $request->validated();
        $remember = $request->filled('remember');

        if ($this->authService->attempt($credentials, $remember)) {
            return $this->redirectBasedOnUserType($this->authService->user());
        }
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


    public function logout() {

        $this->authService->logout();
        return redirect()->route('login.view');
    }
}
