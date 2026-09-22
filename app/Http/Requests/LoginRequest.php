<?php

namespace App\Http\Requests;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Boleh diakses siapa saja yang belum login (guest).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Rules validasi input.
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Pesan error custom (opsional, kalau mau bahasa Indonesia).
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ];
    }

    /**
     * Coba autentikasi user berdasarkan kredensial yang sudah divalidasi.
     * Dipanggil dari controller setelah $request->validated() lolos.
     *
     * @param  array  $extraConditions  Kondisi tambahan untuk Auth::attempt(),
     *                                  mis. ['status' => 'active'] supaya user
     *                                  nonaktif tetap ditolak login.
     */
    public function authenticate(array $extraConditions = []): void
    {
        $this->ensureIsNotRateLimited();

        $credentials = array_merge($this->only('email', 'password'), $extraConditions);

        if (! Auth::attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => 'Email, password salah, atau akun tidak aktif.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Pastikan request ini belum kena rate limit (brute-force protection).
     * Dibatasi 5 percobaan gagal per kombinasi email+IP (lihat throttleKey()).
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());
        $minutes = (int) ceil($seconds / 60);

        throw ValidationException::withMessages([
            'email' => "Terlalu banyak percobaan login. Coba lagi dalam {$minutes} menit.",
        ]);
    }

    /**
     * Key unik untuk rate limiter, dibedakan per email + IP.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}