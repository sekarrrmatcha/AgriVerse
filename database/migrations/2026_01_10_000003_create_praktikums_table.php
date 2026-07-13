<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('praktikums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prodi_id')->constrained()->cascadeOnDelete();
            $table->string('kode', 20);              // THP-P01
            $table->string('judul');
            $table->string('slug')->unique();
            $table->string('tingkat', 20)->default('Dasar');
            $table->string('durasi')->nullable();     // "60 menit"
            $table->text('tujuan');
            $table->json('alat_bahan');               // array of strings
            $table->json('langkah');                  // array of strings (ordered steps)
            $table->json('kuis')->nullable();          // {pertanyaan, opsi:[], jawaban:int, penjelasan}
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('praktikums');
    }
};
