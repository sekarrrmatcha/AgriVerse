<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nim', 20)->nullable();
            $table->foreignId('prodi_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['prodi_id']);
                $table->dropColumn(['prodi_id', 'nim']);
            });

            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('prodi_id');
            $table->dropColumn('nim');
        });
    }
};
