<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordRecoveryEmail;
use Illuminate\Support\Str;

class PasswordRecoveryService
{
    public function sendRecoveryLink(User $user)
    {
        $token = $this->generateUniqueToken();

        $user->recovery_link = $token;
        $user->recovery_link_expires_at = now()->addHours(24);
        $user->save();

        $recoveryLink = $this->buildRecoveryLink($token, $user->email);

        try {
            Mail::to($user->email)->send(new PasswordRecoveryEmail($recoveryLink));
        } catch (\Exception $e) {
            Log::error('Failed to send password recovery email', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            // Reset the token since email failed
            $user->recovery_link = null;
            $user->recovery_link_expires_at = null;
            $user->save();

            throw $e;
        }
    }

    public function verifyRecoveryLink(User $user, $token)
    {
        if (!$user->recovery_link || !$user->recovery_link_expires_at) {
            return false;
        }

        if (now()->isAfter($user->recovery_link_expires_at)) {
            // Clear expired token
            $user->recovery_link = null;
            $user->recovery_link_expires_at = null;
            $user->save();
            return false;
        }

        return hash_equals($user->recovery_link, $token);
    }

    private function generateUniqueToken()
    {
        return Str::random(64);
    }

    private function buildRecoveryLink($token, $email)
    {
        return route('password.reset.form', [
            'token' => $token,
            'email' => $email,
        ]);
    }
}
