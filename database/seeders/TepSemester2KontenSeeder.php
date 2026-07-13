<?php

namespace Database\Seeders;

use App\Models\Matakuliah;
use App\Models\Materi;
use App\Models\Praktikum;
use App\Models\Prodi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Mengisi konten untuk mata kuliah Alat dan Mesin Pertanian dan
 * Mekanika Teknik (Semester 2, prodi TEP).
 *
 * Pemisahan konten:
 * - Materi  = teori saja (Pendahuluan + Tinjauan Pustaka).
 * - Praktikum = pelaksanaan (Tujuan, Alat, Bahan, Prosedur/Langkah Kerja, Kuis).
 *
 * PENTING: sesuaikan $matakuliahSlug di method run() dengan slug asli
 * yang sudah tersimpan di tabel matakuliahs untuk kedua mata kuliah ini
 * (di sini saya menebak 'tep-s2-alsintan' dan 'tep-s2-mekanika-teknik' mengikuti
 * pola 'tep-s1-kimia' dst).
 */
class TepSemester2KontenSeeder extends Seeder
{
    protected string $formatLaporan = "LAPORAN AWAL\n\n"
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

    public function run(): void
    {
        $prodi = Prodi::where('kode', 'TEP')->first();

        if (! $prodi) {
            $this->command?->warn('Prodi TEP tidak ditemukan, seeder dilewati.');
            return;
        }

        $this->seedMatakuliah($prodi, 'tep-s2-alsintan', $this->alatDanMesinPertanian());
        $this->seedMatakuliah($prodi, 'tep-s2-mekanika-teknik', $this->mekanikaTeknik());
    }

    protected function seedMatakuliah(Prodi $prodi, string $matakuliahSlug, array $objekList): void
    {
        $matakuliah = Matakuliah::where('slug', $matakuliahSlug)->first();

        if (! $matakuliah) {
            $this->command?->warn("Mata kuliah dengan slug {$matakuliahSlug} tidak ditemukan, dilewati.");
            return;
        }

        $matakuliah->update(['format_laporan' => $this->formatLaporan]);

        foreach (array_values($objekList) as $index => $objek) {
            $nomor = $index + 1;
            $materiSlug = $matakuliahSlug.'-objek-'.$nomor;
            $praktikumSlug = $matakuliahSlug.'-objek-'.$nomor.'-kuis';

            Materi::updateOrCreate(
                ['matakuliah_id' => $matakuliah->id, 'slug' => $materiSlug],
                [
                    'prodi_id' => $prodi->id,
                    'pertemuan_ke' => $nomor,
                    'judul' => 'Objek '.$nomor.': '.$objek['judul'],
                    'capaian' => $objek['tujuan'],
                    'pendahuluan' => $objek['pendahuluan'],
                    'tinjauan_pustaka' => $objek['tinjauan_pustaka'],
                ]
            );

            Praktikum::updateOrCreate(
                ['matakuliah_id' => $matakuliah->id, 'slug' => $praktikumSlug],
                [
                    'prodi_id' => $prodi->id,
                    'kode' => strtoupper(Str::substr($matakuliahSlug, -6)).'-P0'.$nomor,
                    'judul' => 'Objek '.$nomor.': '.$objek['judul'],
                    'tingkat' => 'Dasar',
                    'durasi' => '30 menit',
                    'tujuan' => implode(' ', $objek['tujuan']),
                    'alat' => $objek['alat'],
                    'bahan' => $objek['bahan'],
                    'langkah' => $objek['prosedur'],
                    'kuis' => $objek['kuis'],
                ]
            );
        }
    }

    protected function alatDanMesinPertanian(): array
    {
        return [
            [
                'judul' => 'Traktor',
                'tujuan' => [
                    'Mengenal macam-macam traktor roda empat',
                    'Memperkenalkan fungsi dan cara kerja traktor roda empat',
                    'Mempraktekkan cara mengendarai traktor roda empat',
                ],
                'pendahuluan' => 'Traktor roda empat merupakan alat mesin pertanian utama untuk pengolahan tanah dan penarikan implemen. Memahami macam-macam tipe traktor serta fungsi komponennya penting sebelum mempraktikkan pengoperasiannya secara aman.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Jenis-jenis Traktor Roda Empat', 'isi' => 'Traktor roda empat memiliki beberapa tipe seperti Standard Tread Tractor, Row Crop Tractors yang letak rodanya dapat distel sesuai baris tanaman, dan High Clearance Tractors dengan kerenggangan tinggi untuk perkebunan seperti tebu.'],
                    ['judul' => 'Komponen Utama Traktor', 'isi' => 'Traktor tersusun dari engine (motor penggerak), power train (kopling, transmisi, diferensial/gardan), serta PTO (Power Take Off) sebagai sumber tenaga putar untuk menggerakkan implemen.'],
                    ['judul' => 'Prinsip Pengoperasian Traktor yang Aman', 'isi' => 'Pengoperasian traktor yang aman mencakup urutan menghidupkan, menjalankan, membelokkan, serta melewati tanjakan/turunan dengan gigi persneling yang sesuai tanpa memindah gigi saat traktor sedang bergerak menanjak/menurun.'],
                ],
                'alat' => [
                    'Traktor roda empat', 'Alat pelindung diri (helm, sarung tangan)', 'Lembar observasi komponen',
                ],
                'bahan' => [
                    'Bahan bakar solar secukupnya',
                ],
                'prosedur' => [
                    'Identifikasi tipe traktor roda empat yang tersedia dan catat perbedaan konstruksinya.',
                    'Amati dan sebutkan komponen utama traktor: engine, power train, dan PTO.',
                    'Pelajari urutan langkah menghidupkan traktor sesuai prosedur keselamatan.',
                    'Praktikkan menjalankan traktor pada lintasan lurus dengan kecepatan rendah didampingi instruktur.',
                    'Praktikkan membelokkan traktor dengan gigi persneling yang sesuai.',
                    'Matikan traktor sesuai prosedur dan catat kendala yang ditemui selama praktik.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Tipe traktor roda empat manakah yang cocok digunakan pada perkebunan seperti tebu?',
                        'opsi' => ['Standard Tread Tractor', 'Row Crop Tractor', 'High Clearance Tractor', 'Garden Tractor'],
                        'jawaban' => 2,
                        'penjelasan' => 'High Clearance Tractor memiliki kerenggangan tinggi sehingga cocok untuk perkebunan seperti tebu.',
                    ],
                    [
                        'pertanyaan' => 'Komponen apa pada traktor yang berfungsi sebagai sumber tenaga putar untuk menggerakkan implemen?',
                        'opsi' => ['Engine', 'Power Take Off (PTO)', 'Diferensial', 'Kopling'],
                        'jawaban' => 1,
                        'penjelasan' => 'PTO (Power Take Off) berfungsi sebagai sumber tenaga putar yang diteruskan ke implemen.',
                    ],
                    [
                        'pertanyaan' => 'Apa yang sebaiknya dihindari saat traktor sedang melewati tanjakan atau turunan?',
                        'opsi' => ['Menjaga kecepatan tetap rendah', 'Memindah gigi persneling saat bergerak', 'Menggunakan gigi yang sesuai', 'Memakai alat pelindung diri'],
                        'jawaban' => 1,
                        'penjelasan' => 'Memindah gigi persneling saat traktor sedang bergerak menanjak/menurun berbahaya dan harus dihindari.',
                    ],
                ],
            ],
            [
                'judul' => 'Hand Traktor',
                'tujuan' => [
                    'Mengenal macam-macam hand traktor',
                    'Memperkenalkan fungsi dan cara kerja hand traktor',
                    'Mempraktekkan cara mengendarai hand traktor',
                ],
                'pendahuluan' => 'Hand traktor (traktor roda dua) banyak digunakan pada lahan sempit dan berlumpur karena ukurannya yang ringkas. Pemahaman komponen dan cara pengoperasiannya penting agar penggunaan di lapangan berjalan efektif dan aman.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Komponen Unit Penggerak Hand Traktor', 'isi' => 'Hand traktor umumnya digerakkan oleh motor satu silinder berdaya 3 sampai 12 HP yang meneruskan tenaga melalui kopling utama dan sabuk V.'],
                    ['judul' => 'Sistem Transmisi dan Kopling', 'isi' => 'Hand traktor sempurna memiliki 6 verseneling maju dan 2 mundur, sedangkan tipe sederhana hanya memiliki verseneling mundur dengan transmisi ke gardan memakai rantai.'],
                    ['judul' => 'Prosedur Menghidupkan dan Menghentikan Hand Traktor', 'isi' => 'Sebelum menghidupkan mesin, ungkit kopling perlu ditekan terlebih dahulu; sebaliknya saat menghentikan, gas dikecilkan dan handle dikembalikan ke posisi netral/rem secara bertahap.'],
                ],
                'alat' => [
                    'Hand traktor (traktor roda dua)', 'Alat pelindung diri', 'Lembar observasi komponen',
                ],
                'bahan' => [
                    'Bahan bakar bensin/solar secukupnya',
                ],
                'prosedur' => [
                    'Identifikasi komponen unit penggerak hand traktor yang tersedia di laboratorium/lapangan.',
                    'Amati sistem transmisi dan kopling pada hand traktor yang digunakan.',
                    'Pelajari dan praktikkan urutan menekan ungkit kopling sebelum menghidupkan mesin.',
                    'Hidupkan hand traktor sesuai prosedur yang benar didampingi instruktur.',
                    'Praktikkan mengendalikan hand traktor pada lintasan datar dengan kecepatan rendah.',
                    'Matikan hand traktor dengan mengecilkan gas dan mengembalikan handle ke posisi netral/rem secara bertahap.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Berapa kisaran daya motor penggerak yang umum digunakan pada hand traktor?',
                        'opsi' => ['1-2 HP', '3-12 HP', '20-30 HP', '50-100 HP'],
                        'jawaban' => 1,
                        'penjelasan' => 'Hand traktor umumnya digerakkan oleh motor satu silinder berdaya 3 sampai 12 HP.',
                    ],
                    [
                        'pertanyaan' => 'Berapa jumlah verseneling maju pada hand traktor tipe sempurna?',
                        'opsi' => ['2 maju', '4 maju', '6 maju', '8 maju'],
                        'jawaban' => 2,
                        'penjelasan' => 'Hand traktor sempurna memiliki 6 verseneling maju dan 2 mundur.',
                    ],
                    [
                        'pertanyaan' => 'Apa yang harus dilakukan sebelum menghidupkan mesin hand traktor?',
                        'opsi' => ['Menekan ungkit kopling terlebih dahulu', 'Langsung menarik tuas gas penuh', 'Melepas rantai transmisi', 'Mengisi bahan bakar sampai penuh'],
                        'jawaban' => 0,
                        'penjelasan' => 'Sebelum menghidupkan mesin, ungkit kopling perlu ditekan terlebih dahulu.',
                    ],
                ],
            ],
        ];
    }

    protected function mekanikaTeknik(): array
    {
        return [
            [
                'judul' => 'Resultan Gaya I dan Resultan Gaya II',
                'tujuan' => [
                    'Menentukan gaya pada sistem koplanar',
                    'Menentukan gaya-gaya sejajar',
                    'Menentukan rumus-rumus resultan gaya',
                ],
                'pendahuluan' => 'Dalam sistem mekanika, beberapa gaya yang bekerja pada suatu titik dapat digantikan oleh satu gaya pengganti yang disebut resultan. Pemahaman prinsip resultan gaya penting sebagai dasar analisis kesetimbangan dan kekuatan struktur.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Gaya Segaris dan Resultannya', 'isi' => 'Apabila beberapa gaya searah/segaris bekerja pada suatu titik, resultannya adalah jumlah aljabar seluruh gaya tersebut (R = F1 + F2 + ... + Fn).'],
                    ['judul' => 'Gaya Tegak Lurus dan Hukum Phytagoras', 'isi' => 'Untuk dua gaya yang saling tegak lurus, besar resultannya dihitung menggunakan hukum Phytagoras (R = akar dari F1² + F2²).'],
                    ['judul' => 'Kondisi Kesetimbangan Gaya', 'isi' => 'Suatu sistem gaya dikatakan seimbang apabila nilai resultan totalnya sama dengan nol.'],
                ],
                'alat' => [
                    'Meja gaya (force table)', 'Spring balance/neraca pegas', 'Busur derajat', 'Benang dan katrol kecil',
                ],
                'bahan' => [
                    'Beban gantung dengan variasi massa',
                ],
                'prosedur' => [
                    'Susun meja gaya dengan beberapa katrol pada sudut yang berbeda-beda.',
                    'Gantungkan beban pada tiap tali melalui katrol dan catat besar gaya yang bekerja pada masing-masing tali.',
                    'Ukur sudut antar gaya menggunakan busur derajat.',
                    'Hitung resultan gaya secara teoritis menggunakan rumus jumlah aljabar dan hukum Phytagoras.',
                    'Bandingkan hasil perhitungan resultan gaya dengan pengukuran menggunakan spring balance.',
                    'Analisis apakah sistem berada dalam kondisi seimbang (resultan sama dengan nol) atau tidak.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Bagaimana cara menentukan resultan dari beberapa gaya yang segaris dan searah?',
                        'opsi' => ['Dikalikan satu sama lain', 'Dijumlahkan secara aljabar', 'Diambil nilai rata-ratanya', 'Diambil nilai terbesarnya saja'],
                        'jawaban' => 1,
                        'penjelasan' => 'Resultan gaya segaris dihitung dengan menjumlahkan seluruh gaya secara aljabar.',
                    ],
                    [
                        'pertanyaan' => 'Rumus apa yang digunakan untuk menghitung resultan dua gaya yang saling tegak lurus?',
                        'opsi' => ['R = F1 + F2', 'R = F1 x F2', 'Hukum Phytagoras (R = akar F1² + F2²)', 'R = F1 - F2'],
                        'jawaban' => 2,
                        'penjelasan' => 'Untuk dua gaya tegak lurus, resultannya dihitung menggunakan hukum Phytagoras.',
                    ],
                    [
                        'pertanyaan' => 'Kapan suatu sistem gaya dikatakan dalam keadaan seimbang?',
                        'opsi' => ['Ketika resultan gayanya maksimum', 'Ketika resultan gayanya sama dengan nol', 'Ketika hanya ada satu gaya yang bekerja', 'Ketika semua gaya searah'],
                        'jawaban' => 1,
                        'penjelasan' => 'Sistem gaya dikatakan seimbang apabila nilai resultan totalnya sama dengan nol.',
                    ],
                ],
            ],
            [
                'judul' => 'Uraian Gaya I dan Uraian Gaya II',
                'tujuan' => [
                    'Membuktikan rumus-rumus uraian gaya',
                    'Menganalisa gaya yang bekerja dalam satu bidang',
                    'Menghitung besarnya gaya-gaya batang dalam rangka batang',
                ],
                'pendahuluan' => 'Gaya yang bekerja pada suatu sistem rangka batang dapat diuraikan menjadi komponen-komponen gaya batang untuk menganalisis kekuatan struktur. Pemahaman jenis-jenis sistem gaya (koplanar, konkuren, koliner, kopel) menjadi dasar dalam analisis rangka batang.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Gaya Koplanar dan Konkuren', 'isi' => 'Gaya koplanar bekerja pada garis kerja dalam satu bidang datar, sedangkan gaya konkuren memiliki garis kerja yang berpotongan pada satu titik yang sama.'],
                    ['judul' => 'Gaya Koliner dan Kopel', 'isi' => 'Gaya koliner memiliki garis kerja yang terletak pada satu garis lurus yang sama, sedangkan kopel adalah dua gaya sejajar yang sama besar namun berlawanan arah.'],
                    ['judul' => 'Analisis Gaya Batang pada Rangka', 'isi' => 'Uraian gaya pada rangka batang dilakukan dengan mengukur gaya-gaya yang bekerja pada tiap batang menggunakan spring balance untuk memverifikasi rumus uraian gaya secara eksperimental.'],
                ],
                'alat' => [
                    'Model rangka batang sederhana', 'Spring balance/neraca pegas', 'Busur derajat', 'Statif dan penjepit',
                ],
                'bahan' => [
                    'Beban gantung dengan variasi massa',
                ],
                'prosedur' => [
                    'Susun model rangka batang sederhana pada statif yang telah disiapkan.',
                    'Identifikasi jenis sistem gaya yang bekerja pada rangka (koplanar, konkuren, koliner, atau kopel).',
                    'Berikan beban pada titik tertentu pada rangka batang dan amati gaya yang timbul pada tiap batang.',
                    'Ukur besar gaya pada masing-masing batang menggunakan spring balance.',
                    'Hitung uraian gaya secara teoritis dan bandingkan dengan hasil pengukuran eksperimen.',
                    'Diskusikan penyebab selisih antara hasil perhitungan teoritis dan hasil pengukuran.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Gaya apa yang memiliki garis kerja berpotongan pada satu titik yang sama?',
                        'opsi' => ['Gaya koplanar', 'Gaya konkuren', 'Gaya koliner', 'Kopel'],
                        'jawaban' => 1,
                        'penjelasan' => 'Gaya konkuren memiliki garis kerja yang berpotongan pada satu titik yang sama.',
                    ],
                    [
                        'pertanyaan' => 'Apa yang dimaksud dengan kopel dalam sistem gaya?',
                        'opsi' => ['Satu gaya tunggal yang besar', 'Dua gaya sejajar sama besar tapi berlawanan arah', 'Gaya yang bekerja pada satu titik', 'Gaya yang searah dan segaris'],
                        'jawaban' => 1,
                        'penjelasan' => 'Kopel adalah dua gaya sejajar yang sama besar namun berlawanan arah.',
                    ],
                    [
                        'pertanyaan' => 'Alat apa yang digunakan untuk mengukur gaya pada tiap batang rangka secara eksperimental?',
                        'opsi' => ['Busur derajat', 'Spring balance', 'Stopwatch', 'Jangka sorong'],
                        'jawaban' => 1,
                        'penjelasan' => 'Spring balance (neraca pegas) digunakan untuk mengukur besar gaya pada tiap batang rangka.',
                    ],
                ],
            ],
        ];
    }
}
