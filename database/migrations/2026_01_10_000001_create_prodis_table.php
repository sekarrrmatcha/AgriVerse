<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prodis', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique();      // THP, TPB, TIP
            $table->string('nama');                      // Teknologi Hasil Pertanian
            $table->string('slug')->unique();            // teknologi-hasil-pertanian
            $table->string('plot_label')->nullable();     // Blok A
            $table->string('accent_color', 7)->default('#7d9c5e');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prodis');
    }
};
