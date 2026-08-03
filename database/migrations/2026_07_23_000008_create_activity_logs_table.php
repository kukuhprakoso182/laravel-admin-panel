<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            // Siapa yang melakukan aksi. Nullable karena bisa berasal dari
            // sistem/cron, atau percobaan login gagal (user belum dikenal).
            $table->foreignUuid('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('event'); // login, logout, login_failed, created, updated, deleted
            $table->string('subject_type')->nullable(); // contoh: App\Models\User
            $table->string('subject_id')->nullable();
            $table->string('description')->nullable();
            $table->json('properties')->nullable(); // {"old": {...}, "new": {...}}
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();

            // Hanya created_at — log bersifat immutable, tidak pernah di-update
            $table->timestamp('created_at')->nullable();

            $table->index(['subject_type', 'subject_id']);
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
