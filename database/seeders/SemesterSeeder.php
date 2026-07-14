<?php

namespace Database\Seeders;

use App\Models\Prodi;
use App\Models\Semester;
use Illuminate\Database\Seeder;

/**
 * Mengisi data Semester (1-4) untuk setiap Prodi (THP, TEP, TIP).
 * Dibuat berdasarkan data yang sudah ada di database lokal, supaya
 * konsisten dan otomatis ter-seed di database manapun (termasuk saat
 * deploy ke server/hosting baru seperti Railway).
 */
class SemesterSeeder extends Seeder
{
    public function run(): void
    {
        // Peta kode prodi (sesuai ProdiSeeder) ke prefix slug yang sudah
        // dipakai lebih dulu pada slug matakuliah (mis. 'tep-s1-kimia').
        // Prodi TPB (Teknik Pertanian) memakai prefix slug 'tep'.
        $prodiSlugPrefixMap = [
            'THP' => 'thp',
            'TPB' => 'tep',
            'TIP' => 'tip',
        ];

        foreach ($prodiSlugPrefixMap as $kodeProdi => $prefix) {
            $prodi = Prodi::where('kode', $kodeProdi)->first();

            if (! $prodi) {
                $this->command?->warn("Prodi dengan kode {$kodeProdi} tidak ditemukan, dilewati.");
                continue;
            }

            for ($nomor = 1; $nomor <= 4; $nomor++) {
                Semester::updateOrCreate(
                    ['slug' => "{$prefix}-semester-{$nomor}"],
                    [
                        'prodi_id' => $prodi->id,
                        'nomor' => $nomor,
                        'nama' => 'Semester '.$nomor,
                    ]
                );
            }
        }
    }
}
