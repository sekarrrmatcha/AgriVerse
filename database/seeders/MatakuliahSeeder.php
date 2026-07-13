<?php

namespace Database\Seeders;

use App\Models\Matakuliah;
use App\Models\Prodi;
use App\Models\Semester;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MatakuliahSeeder extends Seeder
{
    /**
     * Daftar mata kuliah per semester, dikelompokkan berdasarkan kode prodi.
     *
     * 'slug' sengaja dipisah dari 'nama' supaya kalau nama mata kuliah
     * diganti nanti (misalnya "Termodinamika" -> "Termodinamika dan
     * Perpindahan Panas"), slug/URL-nya tetap sama dan seeder ini akan
     * meng-update baris yang sudah ada, bukan membuat baris baru.
     */
    protected array $data = [
        'TEP' => [
            1 => [
                ['slug' => 'kimia', 'nama' => 'Kimia', 'sks' => 3],
                ['slug' => 'fisika', 'nama' => 'Fisika', 'sks' => 3],
                ['slug' => 'biologi', 'nama' => 'Biologi', 'sks' => 3],
            ],
            2 => [
                ['slug' => 'alsintan', 'nama' => 'Alat dan Mesin Pertanian', 'sks' => 3],
                ['slug' => 'mekanika-teknik', 'nama' => 'Mekanika Teknik', 'sks' => 3],
            ],
            3 => [
                ['slug' => 'termodinamika', 'nama' => 'Termodinamika dan Perpindahan Panas', 'sks' => 3],
                ['slug' => 'karakter-bahan-hasil-pertanian', 'nama' => 'Karakteristik Bahan Hasil Pertanian', 'sks' => 3],
            ],
            4 => [
                ['slug' => 'mekanika-fluida', 'nama' => 'Mekanika Fluida', 'sks' => 3],
                ['slug' => 'teknik-pasca-panen', 'nama' => 'Teknik Pasca Panen', 'sks' => 3],
                ['slug' => 'perbengkelan', 'nama' => 'Perbengkelan', 'sks' => 3],
            ],
        ],
    ];

    public function run(): void
    {
        foreach ($this->data as $kodeProdi => $semesterList) {
            $prodi = Prodi::where('kode', $kodeProdi)->first();

            if (! $prodi) {
                $this->command?->warn("Prodi dengan kode {$kodeProdi} tidak ditemukan, dilewati.");
                continue;
            }

            foreach ($semesterList as $nomorSemester => $matakuliahs) {
                $semester = Semester::where('prodi_id', $prodi->id)
                    ->where('nomor', $nomorSemester)
                    ->first();

                if (! $semester) {
                    $this->command?->warn("Semester {$nomorSemester} untuk prodi {$kodeProdi} tidak ditemukan, dilewati. Jalankan SemesterSeeder dulu.");
                    continue;
                }

                foreach (array_values($matakuliahs) as $index => $mk) {
                    $slug = Str::slug($kodeProdi.'-s'.$nomorSemester.'-'.$mk['slug']);

                    Matakuliah::updateOrCreate(
                        ['semester_id' => $semester->id, 'slug' => $slug],
                        [
                            'urutan' => $index + 1,
                            'nama' => $mk['nama'],
                            'sks' => $mk['sks'],
                            'kode' => null,
                            'deskripsi' => null,
                        ]
                    );
                }
            }
        }
    }
}
