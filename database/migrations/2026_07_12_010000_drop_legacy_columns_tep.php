<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materis', function (Blueprint $table) {
            $drop = array_filter(['alat', 'bahan', 'prosedur'], fn ($col) => Schema::hasColumn('materis', $col));
            if ($drop) {
                $table->dropColumn($drop);
            }
        });

        Schema::table('praktikums', function (Blueprint $table) {
            if (Schema::hasColumn('praktikums', 'alat_bahan')) {
                $table->dropColumn('alat_bahan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('materis', function (Blueprint $table) {
            $table->json('alat')->nullable();
            $table->json('bahan')->nullable();
            $table->json('prosedur')->nullable();
        });

        Schema::table('praktikums', function (Blueprint $table) {
            $table->json('alat_bahan')->nullable();
        });
    }
};
