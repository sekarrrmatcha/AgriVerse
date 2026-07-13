<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materis', function (Blueprint $table) {
            if (!Schema::hasColumn('materis', 'pendahuluan')) {
                $table->longText('pendahuluan')->nullable()->after('capaian');
            }
            if (!Schema::hasColumn('materis', 'tinjauan_pustaka')) {
                $table->json('tinjauan_pustaka')->nullable()->after('pendahuluan');
            }
        });

        Schema::table('praktikums', function (Blueprint $table) {
            if (!Schema::hasColumn('praktikums', 'alat')) {
                $table->json('alat')->nullable()->after('tujuan');
            }
            if (!Schema::hasColumn('praktikums', 'bahan')) {
                $table->json('bahan')->nullable()->after('alat');
            }
        });
    }

    public function down(): void
    {
        Schema::table('materis', function (Blueprint $table) {
            $table->dropColumn(['pendahuluan', 'tinjauan_pustaka']);
        });

        Schema::table('praktikums', function (Blueprint $table) {
            $table->dropColumn(['alat', 'bahan']);
        });
    }
};
