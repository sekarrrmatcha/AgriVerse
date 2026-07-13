<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('praktikum_progresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('praktikum_id')->constrained()->cascadeOnDelete();
            $table->json('langkah_selesai')->nullable(); // array of completed step indexes
            $table->boolean('kuis_benar')->nullable();
            $table->timestamp('selesai_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'praktikum_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('praktikum_progresses');
    }
};
