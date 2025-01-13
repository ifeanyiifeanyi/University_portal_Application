<?php

namespace App\Http\Requests;

use Exception;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|email',
            'password' => 'required',
            'cf-turnstile-response' => 'required'
        ];
    }

    public function validated($key = null, $default = null)
    {
        // Only return email and password for credentials
        return [
            'email' => $this->input('email'),
            'password' => $this->input('password'),
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$this->validateTurnstile()) {
                $validator->errors()->add('turnstile', 'Please complete the security check');
            }
        });
    }

    protected function validateTurnstile()
    {
        try {
            
            $response = Http::post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => config('services.turnstile.secret_key'),
                'response' => $this->input('cf-turnstile-response'),
                'remoteip' => $this->ip()
            ]);

            return $response->json('success', false);
        } catch (Exception $e) {
            Log::info(['captcha ' => $e->getMessage()]);
            return redirect()->back()->withErrors('Captcha failed to connect, try again later!');
        }
    }
}
