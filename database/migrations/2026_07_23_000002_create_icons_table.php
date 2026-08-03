<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('icons', function (Blueprint $table) {
            $table->id();
            $table->string('value', 100); // contoh: ri-home-4-line
            $table->string('section', 100)->nullable(); // System, Business, Communication, dst
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['section', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('icons');
    }
};
