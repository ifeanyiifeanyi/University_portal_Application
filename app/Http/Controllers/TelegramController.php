<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TelegramAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramController extends Controller
{
    /**
     * Handle Telegram webhook
     */
    /**
     * Handle Telegram webhook
     */
    public function webhook(Request $request)
    {
        // Disable CSRF protection for this endpoint

        Log::info('Telegram webhook request received', [
            'data' => $request->all(),
            'headers' => $request->headers->all()
        ]);


        try {
            $n = Telegram::commandsHandler(true);
            Log::info("Telegram webhook handled: $n");

            return response('OK', 200);
        } catch (\Exception $e) {
            Log::error("Telegram webhook error: " . $e->getMessage());
            return response('Error: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Set Telegram webhook
     */
    public function setWebhook()
    {
        $response = Telegram::setWebhook(['url' => config('telegram.bots.mybot.webhook_url')]);

        return response()->json([
            'success' => $response,
        ]);
    }

    /**
     * Handle authentication between Telegram and web
     */
    public function auth(Request $request)
    {
        // If user is not logged in, redirect to login
        if (!Auth::check()) {
            // Store token in session for after login
            $request->session()->put('telegram_token', $request->token);
            return redirect()->route('login.view')->with('message', 'Please login to connect your Telegram account');
        }

        $token = $request->token;
        $user = Auth::user();

        // Find the auth request by token
        $auth = TelegramAuth::where('token', $token)
            ->where('expires_at', '>', now())
            ->where('is_active', false)
            ->first();

        if (!$auth) {
            return redirect()->route('student.view.dashboard')->with('error', 'Invalid or expired Telegram connection link');
        }

        // Link user to telegram_id
        $auth->user_id = $user->id;
        $auth->is_active = true;
        $auth->save();

        // Send confirmation message to Telegram
        Telegram::sendMessage([
            'chat_id' => $auth->telegram_id,
            'text' => "✅ Successfully connected to your student account!\n\n" .
                "You can now use these commands:\n" .
                "/results - View your semester results\n" .
                "/materials - Access course materials\n" .
                "/timetable - Check your class schedule",
        ]);

        return redirect()->route('student.view.dashboard')->with('success', 'Telegram account successfully connected');
    }

    /**
     * Handle material downloads with token authentication
     */
    // public function downloadMaterial($id, Request $request)
    // {
    //     $token = $request->token;

    //     // Validate token
    //     $auth = TelegramAuth::where('token', $token)
    //         ->where('is_active', true)
    //         ->first();

    //     if (!$auth) {
    //         abort(401, 'Unauthorized');
    //     }

    //     $material = Material::findOrFail($id);

    //     // Check if student is enrolled in the course
    //     $isEnrolled = $auth->user->student->enrollments()
    //         ->where('course_id', $material->course_id)
    //         ->where('is_active', true)
    //         ->exists();

    //     if (!$isEnrolled || !$material->is_published) {
    //         abort(403, 'Forbidden');
    //     }

    //     // Generate download URL with short expiry
    //     $url = URL::temporarySignedRoute(
    //         'materials.download.file',
    //         now()->addMinutes(5),
    //         ['id' => $material->id]
    //     );

    //     return redirect($url);
    // }
}
