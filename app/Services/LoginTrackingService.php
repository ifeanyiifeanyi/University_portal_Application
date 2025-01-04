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
use Illuminate\Support\Facades\Log;

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

    public function track($user, bool $isSuspicious = false)
    {
        try {
            $deviceInfo = $this->getDeviceInfo();

            // Create login activity record
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

            Log::info('Checking for new device', [
                'user_id' => $user->id,
                'device_info' => $deviceInfo
            ]);

            // Check if this is a new device
            if ($this->isNewDevice($user, $deviceInfo)) {
                Log::info('New device detected', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);

                $this->notifyNewDeviceLogin($user, $activity);
            }

            return $activity;
        } catch (\Exception $e) {
            Log::error('Failed to track login activity', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }



    protected function isNewDevice($user, array $deviceInfo): bool
    {
        $knownDevices = LoginActivity::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subMonths(3))
            ->get(['user_agent', 'device_type', 'browser', 'operating_system']);

        if ($knownDevices->isEmpty()) {
            Log::info('First login for user', ['user_id' => $user->id]);
            return true;
        }

        $currentFingerprint = $this->generateFingerprint($deviceInfo);

        foreach ($knownDevices as $device) {
            $deviceData = [
                'device' => $device->device_type,
                'browser' => $device->browser,
                'platform' => $device->operating_system,
                'user_agent' => $device->user_agent
            ];

            if ($this->generateFingerprint($deviceData) === $currentFingerprint) {
                Log::info('Found matching device', [
                    'user_id' => $user->id,
                    'device_type' => $device->device_type
                ]);
                return false;
            }
        }

        Log::info('No matching devices found', ['user_id' => $user->id]);
        return true;
    }




    protected function generateFingerprint(array $deviceInfo): string
    {
        return hash('sha256', implode('|', [
            $deviceInfo['user_agent'] ?? '',
            $deviceInfo['browser'] ?? '',
            $deviceInfo['platform'] ?? '',
            $deviceInfo['device'] ?? ''
        ]));
    }

    protected function getDeviceInfo(): array
    {
        return [
            'device' => $this->agent->device() ?: 'Unknown Device',
            'browser' => $this->agent->browser() ?: 'Unknown Browser',
            'platform' => $this->agent->platform() ?: 'Unknown Platform',
            'user_agent' => Request::userAgent()
        ];
    }

    protected function getLocation($ip): string
    {
        try {
            if ($position = Location::get($ip)) {
                return sprintf(
                    '%s, %s, %s',
                    $position->cityName ?? 'Unknown City',
                    $position->regionName ?? 'Unknown Region',
                    $position->countryName ?? 'Unknown Country'
                );
            }
        } catch (\Exception $e) {
            Log::error('Failed to get location', [
                'ip' => $ip,
                'error' => $e->getMessage()
            ]);
        }

        return 'Location Not Found';
    }

    // protected function notifyNewDeviceLogin($user, $activity)
    // {
    //     // Create a unique key for this notification
    //     $cacheKey = sprintf(
    //         'new_device_login:%s:%s',
    //         $user->id,
    //         $this->generateFingerprint($this->getDeviceInfo())
    //     );

    //     // Check if we've already sent a notification for this device/user combination
    //     if (!Cache::has($cacheKey)) {
    //         try {
    //             Mail::to($user->email)->send(new SuspiciousLoginDetected($activity));
    //             // Cache the notification for 24 hours to prevent duplicates
    //             Cache::put($cacheKey, true, now()->addHours(24));
    //         } catch (\Exception $e) {
    //             Log::error('Failed to send new device notification email', [
    //                 'user_id' => $user->id,
    //                 'error' => $e->getMessage()
    //             ]);
    //             // Don't throw the exception - just log it
    //         }
    //     }
    // }

    protected function notifyNewDeviceLogin($user, $activity)
    {
        Log::info('Attempting to send new device notification', [
            'user_id' => $user->id,
            'email' => $user->email,
            'activity_id' => $activity->id
        ]);

        // Create a unique key for this notification
        $cacheKey = "new_device_login:{$user->id}:{$activity->id}";

        // Check if we've already sent a notification
        if (!Cache::has($cacheKey)) {
            try {
                // Send immediately instead of queuing
                Mail::to($user->email)->send(new SuspiciousLoginDetected($activity));

                Log::info('New device notification sent successfully', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);

                // Cache the notification for 24 hours
                Cache::put($cacheKey, true, now()->addHours(24));
            } catch (\Exception $e) {
                Log::error('Failed to send new device notification email', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        } else {
            Log::info('Notification already sent recently', [
                'user_id' => $user->id,
                'cache_key' => $cacheKey
            ]);
        }
    }

    private function getLoginAttemptKey(string $ip): string
    {
        return 'login_attempt:' . $ip;
    }
}
