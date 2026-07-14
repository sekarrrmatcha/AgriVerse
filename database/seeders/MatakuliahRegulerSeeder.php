<?php

namespace Database\Seeders;

use App\Models\Matakuliah;
use App\Models\Semester;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Mengisi data Matakuliah "reguler" (bukan praktikum) yang dibutuhkan oleh
 * MateriSeeder — misalnya THP101 "Kimia dan Komposisi Bahan Pangan", dst.
 * Matakuliah ini sebelumnya belum ada di database manapun (baik lokal
 * maupun server), makanya MateriSeeder gagal dengan ModelNotFoundException.
 *
 * Penempatan semester ditentukan dari pola kode: 1xx -> semester 1,
 * 2xx -> semester 2, 3xx -> semester 3. Silakan sesuaikan 'sks' di bawah
 * kalau beda dengan kurikulum aslinya.
 */
class MatakuliahRegulerSeeder extends Seeder
{
    public function run(): void
    {
        // Prodi TPB (Teknik Pertanian) memakai prefix slug 'tep' pada
        // Semester, mengikuti konvensi yang sudah dipakai di SemesterSeeder.
        $prefixByProdiKode = [
            'THP' => 'thp',
            'TPB' => 'tep',
            'TIP' => 'tip',
        ];

        foreach ($this->matakuliahList() as $kodeProdi => $matakuliahs) {
            $prefix = $prefixByProdiKode[$kodeProdi] ?? null;

            if (! $prefix) {
                $this->command?->warn("Prefix slug untuk prodi {$kodeProdi} tidak dikenali, dilewati.");
                continue;
            }

            $urutanPerSemester = [];

            foreach ($matakuliahs as $kode => $data) {
                $nomorSemester = (int) substr($kode, strlen($kodeProdi), 1);
                $semesterSlug = "{$prefix}-semester-{$nomorSemester}";
                $semester = Semester::where('slug', $semesterSlug)->first();

                if (! $semester) {
                    $this->command?->warn("Semester dengan slug {$semesterSlug} tidak ditemukan, dilewati ({$kode}).");
                    continue;
                }

                $urutanPerSemester[$nomorSemester] = ($urutanPerSemester[$nomorSemester] ?? 0) + 1;

                Matakuliah::updateOrCreate(
                    ['kode' => $kode],
                    [
                        'semester_id' => $semester->id,
                        'urutan' => $urutanPerSemester[$nomorSemester],
                        'nama' => $data['judul'],
                        'sks' => $data['sks'] ?? 3,
                        'slug' => Str::slug($kode.'-'.$data['judul']),
                    ]
                );
            }
        }
    }

    protected function matakuliahList(): array
    {
        return [
            'THP' => [
                'THP101' => ['judul' => 'Kimia dan Komposisi Bahan Pangan'],
                'THP102' => ['judul' => 'Mikrobiologi Pangan Dasar'],
                'THP201' => ['judul' => 'Teknologi Pengolahan Pangan'],
                'THP202' => ['judul' => 'Fermentasi dan Bioproses Pangan'],
                'THP301' => ['judul' => 'Keamanan Pangan dan Sanitasi'],
            ],
            'TPB' => [
                'TPB101' => ['judul' => 'Mekanisasi dan Alat Mesin Pertanian'],
                'TPB102' => ['judul' => 'Teknik Irigasi dan Drainase'],
                'TPB201' => ['judul' => 'Instrumentasi dan Kontrol Otomatis'],
                'TPB202' => ['judul' => 'Energi Terbarukan untuk Pertanian'],
                'TPB301' => ['judul' => 'Teknik Tanah dan Konservasi Air'],
            ],
            'TIP' => [
                'TIP101' => ['judul' => 'Pengantar Manajemen Agroindustri'],
                'TIP102' => ['judul' => 'Perencanaan dan Pengendalian Produksi'],
                'TIP201' => ['judul' => 'Teknik Tata Cara Kerja'],
                'TIP202' => ['judul' => 'Sistem Manajemen Mutu Agroindustri'],
                'TIP301' => ['judul' => 'Manajemen Rantai Pasok dan Limbah Agroindustri'],
            ],
        ];
    }
}
