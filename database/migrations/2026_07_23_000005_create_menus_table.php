<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('menus')->nullOnDelete();
            $table->foreignId('icon_id')->nullable()->constrained('icons')->nullOnDelete();
            $table->string('name', 100);
            $table->string('link', 255)->nullable();
            $table->string('link_alias', 100)->nullable(); // dipakai untuk cek menu aktif & permission (nama route)
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['parent_id', 'order']);
            $table->index('link_alias');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
