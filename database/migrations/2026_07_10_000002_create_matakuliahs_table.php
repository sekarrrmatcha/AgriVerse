<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matakuliahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('urutan')->default(1);
            $table->string('kode', 20)->nullable();
            $table->string('nama');
            $table->string('slug');
            $table->text('deskripsi')->nullable();
            $table->timestamps();

            $table->unique(['semester_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matakuliahs');
    }
};
