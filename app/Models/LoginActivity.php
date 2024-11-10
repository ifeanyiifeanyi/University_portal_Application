<?php

namespace App\Models;

use Jenssegers\Agent\Agent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Stevebauman\Location\Facades\Location;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LoginActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'device_type',
        'browser',
        'operating_system',
        'location',
        'is_suspicious',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }





    /**
     * Get device fingerprint
     */
    public function getDeviceFingerprint(): string
    {
        return hash('sha256', implode('|', [
            $this->user_agent,
            $this->browser,
            $this->operating_system,
            $this->device_type
        ]));
    }

  /**
     * Get location from IP address using stevebauman/location package
     */
    protected static function getLocation(string $ip): string
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

    /**
     * Get recent login activities for user
     */
    public static function getRecentForUser(User $user, int $days = 30)
    {
        return self::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays($days))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Check for suspicious login patterns
     */
    public static function isSuspiciousActivity(User $user): bool
    {
        $recentFailedAttempts = self::where('user_id', $user->id)
            ->where('is_suspicious', true)
            ->where('created_at', '>=', now()->subHours(24))
            ->count();

        $suspiciousThreshold = config('login-security.suspicious_activity.max_failed_attempts', 3);

        return $recentFailedAttempts >= $suspiciousThreshold;
    }
}
