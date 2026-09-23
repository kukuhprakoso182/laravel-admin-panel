<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel standar Laravel untuk Password broker (Password::sendResetLink(),
 * Password::reset() dipakai di AuthController). Sebelumnya belum ada sama
 * sekali di project ini, padahal rute forgot-password/reset-password sudah
 * terdaftar dan AuthController sudah memanggilnya - tanpa tabel ini,
 * request forgot-password akan langsung gagal dengan
 * "SQLSTATE[42S02]: Table 'password_reset_tokens' doesn't exist".
 *
 * Primary key-nya "email" (bukan id), sesuai konvensi bawaan Laravel -
 * jadi tidak masalah walau users.id di project ini pakai UUID.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
    }
};
