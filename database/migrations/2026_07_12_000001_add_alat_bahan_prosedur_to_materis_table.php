<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materis', function (Blueprint $table) {
            $table->json('alat')->nullable()->after('pokok_bahasan');
            $table->json('bahan')->nullable()->after('alat');
            $table->json('prosedur')->nullable()->after('bahan');
        });
    }

    public function down(): void
    {
        Schema::table('materis', function (Blueprint $table) {
            $table->dropColumn(['alat', 'bahan', 'prosedur']);
        });
    }
};
