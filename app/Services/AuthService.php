<?php

namespace App\Services;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    protected $auth;

    public function __construct(StatefulGuard $auth)
    {
        $this->auth = $auth;
    }

    public function attempt(array $credentials, bool $remember = false): bool
    {
        return $this->auth->attempt($credentials, $remember);
    }

    public function login($user, bool $remember = false): void
    {
        $this->auth->login($user, $remember);
    }

    public function check(): bool
    {
        return $this->auth->check();
    }

    public function user()
    {
        return $this->auth->user();
    }

    public function logout(): void
    {
        $this->auth->logout();
    }
}
