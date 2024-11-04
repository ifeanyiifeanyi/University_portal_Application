<?php

namespace App\Services;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Contracts\Auth\StatefulGuard;

class AuthService
{
    protected $auth;

    public function __construct(StatefulGuard $auth)
    {
        $this->auth = $auth;
    }

    public function attempt(array $credentials, bool $remember = false): bool
    {
        $this->setNRegenerate();
        return $this->auth->attempt($credentials, $remember);
    }

    public function login($user, bool $remember = false): void
    {
        $this->setNRegenerate();
        $this->auth->login($user, $remember);

        // Set last activity timestamp
        Session::put('last_activity', time());
    }

    public function check(): bool
    {
        if ($this->auth->check()) {
            // Update last activity timestamp
            Session::put('last_activity', time());
            return true;
        }
        return false;
    }

    public function user()
    {
        if ($this->check()) {
            return $this->auth->user();
        }
        return null;
    }

    public function logout(): void
    {
        $this->flushNRegenerate();

        $this->auth->logout();
    }

    private function flushNRegenerate()
    {
        // Clear all session data
        Session::flush();

        // Regenerate session ID
        Session::regenerate(true);
    }

    private function setNRegenerate()
    {
        // Set a longer session lifetime for authenticated users
        Config::set('session.lifetime', 1440); // 24 hours in minutes

        // Prevent session fixation
        Session::regenerate();
    }
}
