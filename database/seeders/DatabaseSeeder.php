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
            SemesterSeeder::class,
            MatakuliahSeeder::class,
            PraktikumSeeder::class,

            // Konten tambahan per-semester (Materi & Praktikum) per prodi.
            // Sebelumnya file-file ini sudah dibuat tapi belum didaftarkan
            // di sini, sehingga tidak ikut ter-seed di environment baru
            // (mis. Railway) walaupun sudah ada di database lokal.
            TepSemester1KontenSeeder::class,
            TepSemester2KontenSeeder::class,
            TepSemester3KontenSeeder::class,
            TepSemester4KontenSeeder::class,
            ThpSemester2KontenSeeder::class,
            ThpSemester3KontenSeeder::class,
            ThpSemester4KontenSeeder::class,
            TipSemester2KontenSeeder::class,

            // CATATAN: MatakuliahRegulerSeeder & MateriSeeder SENGAJA tidak
            // dipanggil lagi. Keduanya adalah dataset "reguler" versi lama
            // (mata kuliah kode THP101-301, TPB101-301, TIP101-301) yang
            // tidak lagi dipakai, dan sebelumnya membuat jumlah mata kuliah
            // & materi di setiap prodi jadi kelebihan +5 dari yang seharusnya.
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
