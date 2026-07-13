<?php

namespace Database\Seeders;

use App\Models\Prodi;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Jalankan seluruh seeder untuk AgriVerse.
     */
    public function run(): void
    {
        $this->call([
            ProdiSeeder::class,
            MateriSeeder::class,
            PraktikumSeeder::class,
        ]);

        // Akun demo agar langsung bisa login & tes Dashboard tanpa daftar dulu.
        User::updateOrCreate(
            ['email' => 'mahasiswa@agriverse.test'],
            [
                'name' => 'Mahasiswa Demo',
                'nim' => '3124521008',
                'prodi_id' => Prodi::where('kode', 'THP')->value('id'),
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
    }
}
