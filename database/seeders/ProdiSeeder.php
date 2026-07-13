<?php

namespace Database\Seeders;

use App\Models\Prodi;
use Illuminate\Database\Seeder;

class ProdiSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'kode' => 'THP',
                'nama' => 'Teknologi Hasil Pertanian',
                'slug' => 'teknologi-hasil-pertanian',
                'plot_label' => 'Blok A',
                'accent_color' => '#9c5f3c',
                'deskripsi' => 'Pengolahan, pengawasan mutu, dan keamanan produk pangan hasil pertanian.',
            ],
            [
                'kode' => 'TPB',
                'nama' => 'Teknik Pertanian',
                'slug' => 'teknik-pertanian',
                'plot_label' => 'Blok B',
                'accent_color' => '#4c6b76',
                'deskripsi' => 'Merancang dan mengoperasikan alat, mesin, dan sistem otomasi untuk mendukung produksi pertanian.',
            ],
            [
                'kode' => 'TIP',
                'nama' => 'Teknologi Industri Pertanian',
                'slug' => 'teknologi-industri-pertanian',
                'plot_label' => 'Blok C',
                'accent_color' => '#dd9c33',
                'deskripsi' => 'Mengelola sistem produksi, mutu, dan rantai pasok agroindustri secara efisien dan berkelanjutan.',
            ],
        ];

        foreach ($data as $row) {
            Prodi::updateOrCreate(['kode' => $row['kode']], $row);
        }
    }
}
