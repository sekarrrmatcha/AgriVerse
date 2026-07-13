<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materis', function (Blueprint $table) {
            $table->foreignId('matakuliah_id')->nullable()->after('prodi_id')->constrained()->nullOnDelete();
        });

        Schema::table('praktikums', function (Blueprint $table) {
            $table->foreignId('matakuliah_id')->nullable()->after('prodi_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('materis', function (Blueprint $table) {
            $table->dropConstrainedForeignId('matakuliah_id');
        });

        Schema::table('praktikums', function (Blueprint $table) {
            $table->dropConstrainedForeignId('matakuliah_id');
        });
    }
};
