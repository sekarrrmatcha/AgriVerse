<?php

namespace Database\Seeders;

use App\Models\Matakuliah;
use App\Models\Semester;
use Illuminate\Database\Seeder;

/**
 * Mengisi data Matakuliah untuk semua Prodi (THP, TEP, TIP).
 * Dibuat berdasarkan data yang sudah ada di database lokal (per 14 Juli 2026),
 * supaya konsisten dan otomatis ter-seed di database manapun (termasuk saat
 * deploy ke server/hosting baru seperti Railway).
 *
 * Referensi ke Semester menggunakan slug (bukan id) supaya seeder ini tetap
 * benar walaupun auto-increment id berbeda antar environment/database.
 */
class MatakuliahSeeder extends Seeder
{
    protected string $formatLaporanDefault = "LAPORAN AWAL\n\n"
        ."Bab 1 Pendahuluan\n"
        ."A. Latar Belakang\n"
        ."B. Tujuan dan Manfaat\n\n"
        ."Bab 2 Tinjauan Pustaka\n"
        ."(Minimal 5 poin)\n\n"
        ."Bab 3 Metode Praktikum\n"
        ."A. Alat\n"
        ."B. Bahan\n"
        ."C. Prosedur Kerja\n\n"
        ."Daftar Pustaka\n\n"
        ."LAPORAN AKHIR\n\n"
        ."Bab 4 Hasil dan Pembahasan\n"
        ."A. Hasil\n"
        ."B. Pembahasan\n\n"
        ."Bab 5 Penutup\n"
        ."A. Kesimpulan\n"
        ."B. Saran\n\n"
        ."LAMPIRAN";

    protected string $formatLaporanMikrobiologi = "LAPORAN PRAKTIKUM\n\n"
        ."I. Pendahuluan\n"
        ."1.1 Latar Belakang\n"
        ."1.2 Tujuan\n\n"
        ."II. Tinjauan Pustaka\n\n"
        ."III. Metode\n"
        ."1.1 Alat\n"
        ."1.2 Bahan\n"
        ."1.3 Prosedur Kerja (diagram alir)\n\n"
        ."IV. Hasil dan Pembahasan\n\n"
        ."V. Kesimpulan\n\n"
        ."Daftar Pustaka\n"
        ."(Minimal 2 buku selain penuntun praktikum, 2 jurnal nasional, dan beberapa tambahan dari internet selain blog dan Wikipedia)";

    protected string $formatLaporanPba = "LAPORAN PRAKTIKUM\n\n"
        ."BAB I\n"
        ."PENDAHULUAN (Nilai 10)\n"
        ."1.1 Latar Belakang\n"
        ."1.2 Rumusan Masalah\n"
        ."1.3 Tujuan\n\n"
        ."BAB II\n"
        ."TINJAUAN PUSTAKA (Nilai 15)\n\n"
        ."BAB III\n"
        ."METODE PRAKTIKUM (Nilai 20)\n"
        ."3.1 Waktu dan Tempat\n"
        ."3.2 Alat dan Bahan\n"
        ."3.3 Prosedur Kerja\n\n"
        ."BAB IV\n"
        ."HASIL DAN PEMBAHASAN (Nilai 30)\n"
        ."4.1 Hasil\n"
        ."4.2 Pembahasan\n\n"
        ."BAB V\n"
        ."PENUTUP (Nilai 10)\n"
        ."5.1 Kesimpulan\n\n"
        ."DAFTAR PUSTAKA (Nilai 5)\n\n"
        ."LAMPIRAN (Nilai 5)";

    public function run(): void
    {
        foreach ($this->matakuliahList() as $data) {
            $semester = Semester::where('slug', $data['semester_slug'])->first();

            if (! $semester) {
                $this->command?->warn("Semester dengan slug {$data['semester_slug']} tidak ditemukan, dilewati ({$data['nama']}).");
                continue;
            }

            Matakuliah::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'semester_id' => $semester->id,
                    'urutan' => $data['urutan'],
                    'kode' => $data['kode'] ?? null,
                    'nama' => $data['nama'],
                    'sks' => $data['sks'],
                    'deskripsi' => $data['deskripsi'] ?? null,
                    'format_laporan' => $data['format_laporan'],
                ]
            );
        }
    }

    protected function matakuliahList(): array
    {
        return [
            // ===== TEP Semester 1 =====
            ['semester_slug' => 'tep-semester-1', 'urutan' => 1, 'kode' => null, 'nama' => 'Kimia', 'sks' => 3, 'slug' => 'tep-s1-kimia', 'format_laporan' => $this->formatLaporanDefault],
            ['semester_slug' => 'tep-semester-1', 'urutan' => 2, 'kode' => null, 'nama' => 'Fisika', 'sks' => 3, 'slug' => 'tep-s1-fisika', 'format_laporan' => $this->formatLaporanDefault],
            ['semester_slug' => 'tep-semester-1', 'urutan' => 3, 'kode' => null, 'nama' => 'Biologi', 'sks' => 3, 'slug' => 'tep-s1-biologi', 'format_laporan' => $this->formatLaporanDefault],

            // ===== TEP Semester 2 =====
            ['semester_slug' => 'tep-semester-2', 'urutan' => 1, 'kode' => null, 'nama' => 'Alat dan Mesin Pertanian', 'sks' => 3, 'slug' => 'tep-s2-alsintan', 'format_laporan' => $this->formatLaporanDefault],
            ['semester_slug' => 'tep-semester-2', 'urutan' => 2, 'kode' => null, 'nama' => 'Mekanika Teknik', 'sks' => 3, 'slug' => 'tep-s2-mekanika-teknik', 'format_laporan' => $this->formatLaporanDefault],

            // ===== TEP Semester 3 =====
            ['semester_slug' => 'tep-semester-3', 'urutan' => 1, 'kode' => null, 'nama' => 'Termodinamika dan Perpindahan Panas', 'sks' => 3, 'slug' => 'tep-s3-termodinamika', 'format_laporan' => $this->formatLaporanDefault],
            ['semester_slug' => 'tep-semester-3', 'urutan' => 2, 'kode' => null, 'nama' => 'Karakteristik Bahan Hasil Pertanian', 'sks' => 3, 'slug' => 'tep-s3-karakter-bahan-hasil-pertanian', 'format_laporan' => $this->formatLaporanDefault],

            // ===== TEP Semester 4 =====
            ['semester_slug' => 'tep-semester-4', 'urutan' => 1, 'kode' => null, 'nama' => 'Mekanika Fluida', 'sks' => 3, 'slug' => 'tep-s4-mekanika-fluida', 'format_laporan' => $this->formatLaporanDefault],
            ['semester_slug' => 'tep-semester-4', 'urutan' => 2, 'kode' => null, 'nama' => 'Teknik Pasca Panen', 'sks' => 3, 'slug' => 'tep-s4-teknik-pasca-panen', 'format_laporan' => $this->formatLaporanDefault],
            ['semester_slug' => 'tep-semester-4', 'urutan' => 3, 'kode' => null, 'nama' => 'Perbengkelan', 'sks' => 3, 'slug' => 'tep-s4-perbengkelan', 'format_laporan' => $this->formatLaporanDefault],

            // ===== THP Semester 2 =====
            ['semester_slug' => 'thp-semester-2', 'urutan' => 1, 'kode' => 'THP-S2-MIPA', 'nama' => 'Praktikum MIPA', 'sks' => 2, 'slug' => 'thp-s2-praktikum-mipa', 'format_laporan' => $this->formatLaporanDefault],

            // ===== THP Semester 3 =====
            ['semester_slug' => 'thp-semester-3', 'urutan' => 1, 'kode' => 'THP-S3-KIMBIO', 'nama' => 'Praktikum Kimia dan Biokimia', 'sks' => 2, 'slug' => 'thp-s3-praktikum-kimia-biokimia', 'format_laporan' => $this->formatLaporanDefault],

            // ===== THP Semester 4 =====
            ['semester_slug' => 'thp-semester-4', 'urutan' => 1, 'kode' => 'THP-S4-KEMAS', 'nama' => 'Praktikum Pengemasan dan Penyimpanan', 'sks' => 2, 'slug' => 'thp-s4-praktikum-pengemasan-penyimpanan', 'format_laporan' => $this->formatLaporanDefault],
            ['semester_slug' => 'thp-semester-4', 'urutan' => 2, 'kode' => 'THP-S4-MIKRO', 'nama' => 'Praktikum Mikrobiologi Pangan dan Hasil Pertanian', 'sks' => 2, 'slug' => 'thp-s4-praktikum-mikrobiologi-pangan', 'format_laporan' => $this->formatLaporanMikrobiologi],

            // ===== TIP Semester 2 =====
            [
                'semester_slug' => 'tip-semester-2',
                'urutan' => 1,
                'kode' => 'TIP337',
                'nama' => 'Praktikum Pengetahuan Bahan Agroindustri',
                'sks' => 1,
                'slug' => 'tip-s2-praktikum-pengetahuan-bahan-agroindustri',
                'deskripsi' => 'Praktikum pengenalan dan analisis sifat fisik serta kimia bahan baku agroindustri, mencakup buah, sayur, telur, daging, susu, serealia, kacang-kacangan, umbi-umbian, dan komoditas perkebunan.',
                'format_laporan' => $this->formatLaporanPba,
            ],
        ];
    }
}
