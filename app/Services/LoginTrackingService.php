<?php

namespace App\Services;

use Stevebauman\Location\Facades\Location;
use Jenssegers\Agent\Agent;
use App\Models\LoginActivity;
use Illuminate\Support\Facades\Mail;
use App\Mail\SuspiciousLoginDetected;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\RateLimiter;



class LoginTrackingService
{
    protected $agent;

    public function __construct()
    {
        $this->agent = new Agent();
    }

    /**
     * Check if too many login attempts
     */
    public function tooManyAttempts(string $ip): bool
    {
        $key = $this->getLoginAttemptKey($ip);
        $maxAttempts = Config::get('login-security.max_attempts', 5);

        return RateLimiter::tooManyAttempts($key, $maxAttempts);
    }


    /**
     * Get remaining seconds before next attempt
     */
    public function getTimeUntilNextAttempt(string $ip): int
    {
        $key = $this->getLoginAttemptKey($ip);
        return RateLimiter::availableIn($key);
    }

    /**
     * Record failed attempt
     */
    public function recordFailedAttempt(string $ip): void
    {
        $key = $this->getLoginAttemptKey($ip);
        RateLimiter::hit($key, Config::get('login-security.decay_minutes', 1) * 60);
    }

    /**
     * Clear failed attempts
     */
    public function clearAttempts(string $ip): void
    {
        $key = $this->getLoginAttemptKey($ip);
        RateLimiter::clear($key);
    }




    /**
     * Handle new device detection and notification
     */
    public function handleNewDeviceLogin($user, LoginActivity $activity): void
    {
        if (!Config::get('login-security.notify_on_new_device', true)) {
            return;
        }

        if ($activity->isNewDevice()) {
            Mail::to($user->email)
                ->queue(new SuspiciousLoginDetected($activity));
        }
    }


    /**
     * Check for suspicious activity
     */
    public function isSuspiciousActivity($user): bool
    {
        $windowMinutes = Config::get('login-security.suspicious_activity.window_minutes', 60);
        $maxFailedAttempts = Config::get('login-security.suspicious_activity.max_failed_attempts', 3);

        $recentFailedAttempts = LoginActivity::where('user_id', $user->id)
            ->where('is_suspicious', true)
            ->where('created_at', '>=', now()->subMinutes($windowMinutes))
            ->count();

        return $recentFailedAttempts >= $maxFailedAttempts;
    }

    private function getLoginAttemptKey(string $ip): string
    {
        return 'login_attempt:' . $ip;
    }





    public function track($user, bool $isSuspicious = false)
    {
        $deviceInfo = $this->getDeviceInfo();

        // Create login activity record using the model
        $activity = LoginActivity::create([
            'user_id' => $user->id,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'device_type' => $deviceInfo['device'],
            'browser' => $deviceInfo['browser'],
            'operating_system' => $deviceInfo['platform'],
            'location' => $this->getLocation(Request::ip()),
            'is_suspicious' => $isSuspicious
        ]);

        // Update user's last login info
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => Request::ip()
        ]);

        // Check if this is a new device
        if ($this->isNewDevice($user, $deviceInfo)) {
            $this->notifyNewDeviceLogin($user, $activity);
        }

        return $activity;
    }

    protected function getDeviceInfo(): array
    {
        return [
            'device' => $this->agent->device(),
            'browser' => $this->agent->browser(),
            'platform' => $this->agent->platform(),
            'fingerprint' => hash('sha256', implode('|', [
                Request::userAgent(),
                $this->agent->browser(),
                $this->agent->platform()
            ]))
        ];
    }

    protected function isNewDevice($user, array $deviceInfo): bool
    {
        $knownDevices = LoginActivity::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subMonths(3))
            ->pluck('user_agent')
            ->toArray();

        return !in_array($deviceInfo['fingerprint'], array_map(function ($userAgent) {
            return hash('sha256', $userAgent);
        }, $knownDevices));
    }

    protected function getLocation($ip): string
    {
        if ($position = Location::get($ip)) {
            return sprintf(
                '%s, %s, %s',
                $position->cityName ?? 'Unknown City',
                $position->regionName ?? 'Unknown Region',
                $position->countryName ?? 'Unknown Country'
            );
        }

        return 'Location Not Found';
    }

    protected function notifyNewDeviceLogin($user, $activity)
    {
        Mail::to($user->email)->send(new SuspiciousLoginDetected($activity));
    }
}
