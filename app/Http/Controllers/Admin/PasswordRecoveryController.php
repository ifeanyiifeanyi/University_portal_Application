<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\PasswordRecoveryService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;

class PasswordRecoveryController extends Controller
{
    protected $passwordRecoveryService;

    public function __construct(PasswordRecoveryService $passwordRecoveryService)
    {
        $this->passwordRecoveryService = $passwordRecoveryService;
    }

    public function showRecoveryForm()
    {
        return view('auth.password-recovery');
    }

    public function sendRecoveryCode(Request $request)
    {
        $request->validate([
            'recovery_identifier' => 'required|email',
        ]);

        $key = 'password_recovery_attempts_' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            return redirect()->back()->withErrors([
                'recovery_identifier' => 'Too many password recovery attempts. Please try again later.'
            ]);
        }

        $user = User::where('email', $request->input('recovery_identifier'))->first();

        if (!$user) {
            RateLimiter::hit($key);
            return redirect()->back()->withErrors([
                'recovery_identifier' => 'We could not find a user with that email address.'
            ]);
        }

        try {
            $this->passwordRecoveryService->sendRecoveryLink($user);
            RateLimiter::hit($key);

            return redirect()->back()->with('status', 'Password recovery link has been sent to your email.');
        } catch (\Exception $e) {
            report($e);
            return redirect()->back()->withErrors([
                'recovery_identifier' => 'Unable to send recovery link. Please try again later.'
            ]);
        }
    }

    public function showResetForm(Request $request)
    {
        $token = $request->query('token');
        $email = $request->query('email');

        if (!$token || !$email) {
            return redirect()->route('password.recovery.form')
                ->withErrors(['error' => 'Invalid password reset link.']);
        }

        $user = User::where('email', $email)->first();

        if (!$user || !$this->passwordRecoveryService->verifyRecoveryLink($user, $token)) {
            return redirect()->route('password.recovery.form')
                ->withErrors(['error' => 'This password reset link is invalid or has expired.']);
        }

        return view('auth.password-reset', [
            'token' => $token,
            'email' => $email
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'new_password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !$this->passwordRecoveryService->verifyRecoveryLink($user, $request->token)) {
            return redirect()->route('password.recovery.form')
                ->withErrors(['error' => 'This password reset link is invalid or has expired.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->recovery_link = null;
        $user->recovery_link_expires_at = null;
        $user->save();

        return redirect()->route('login')
            ->with('status', 'Your password has been reset successfully. You can now log in with your new password.');
    }
}
