<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prodi_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('pertemuan_ke');
            $table->string('judul');
            $table->string('slug')->unique();
            $table->json('capaian');        // array of learning outcomes
            $table->json('pokok_bahasan');  // array of topics
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materis');
    }
};
