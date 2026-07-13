<?php

namespace Database\Seeders;

use App\Models\Matakuliah;
use App\Models\Materi;
use App\Models\Praktikum;
use App\Models\Prodi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Mengisi konten untuk mata kuliah Kimia, Fisika, dan Biologi
 * (Semester 1, prodi TEP).
 *
 * Pemisahan konten:
 * - Materi  = teori saja (Pendahuluan + Tinjauan Pustaka).
 * - Praktikum = pelaksanaan (Tujuan, Alat, Bahan, Prosedur/Langkah Kerja, Kuis).
 */
class TepSemester1KontenSeeder extends Seeder
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

        $this->seedMatakuliah($prodi, 'tep-s1-kimia', $this->kimia());
        $this->seedMatakuliah($prodi, 'tep-s1-fisika', $this->fisika());
        $this->seedMatakuliah($prodi, 'tep-s1-biologi', $this->biologi());
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

    protected function kimia(): array
    {
        return [
            [
                'judul' => 'Pengenalan Alat-alat dan Teknik Dasar Bekerja di Laboratorium',
                'tujuan' => [
                    'Mengetahui nama alat-alat yang umum dipakai di dalam laboratorium',
                    'Mengetahui kegunaan alat-alat tersebut',
                    'Mengetahui teknik-teknik dasar kerja di laboratorium',
                    'Dapat melakukan teknik-teknik dasar bekerja di laboratorium',
                ],
                'pendahuluan' => 'Bekerja di laboratorium kimia menuntut penguasaan alat-alat dasar dan teknik kerja yang benar agar hasil pengukuran akurat dan aman. Sebelum melakukan analisis kimia lebih lanjut, praktikan perlu mengenal nama, fungsi, dan cara penggunaan alat gelas maupun alat ukur volume/berat yang umum dipakai di laboratorium.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Alat Ukur Volume', 'isi' => 'Gelas ukur, pipet, buret, dan labu ukur memiliki tingkat ketelitian berbeda; mikropipet misalnya mampu mengukur hingga ketelitian 0,001 mL sehingga cocok untuk volume sangat kecil.'],
                    ['judul' => 'Neraca Analitik dan Teknik Penimbangan', 'isi' => 'Neraca analitik digunakan untuk menimbang massa zat dengan ketelitian tinggi, memerlukan teknik penimbangan yang benar agar hasil tidak bias.'],
                    ['judul' => 'Teknik Membaca Meniskus', 'isi' => 'Pembacaan skala pada alat ukur volume cairan dilakukan pada bagian bawah meniskus (garis lengkung permukaan cairan) untuk menghindari kesalahan paralaks.'],
                    ['judul' => 'Teknik Pembuatan Larutan', 'isi' => 'Pembuatan larutan dengan konsentrasi tertentu memerlukan ketepatan penimbangan zat terlarut dan pengenceran menggunakan labu ukur.'],
                    ['judul' => 'Keselamatan Kerja di Laboratorium', 'isi' => 'Penggunaan alat pelindung dan penanganan alat/zat kimia yang benar (misalnya memakai penjepit untuk benda panas) penting untuk mencegah kecelakaan kerja.'],
                ],
                'alat' => [
                    'Gelas ukur', 'Pipet volume dan pipet tetes', 'Buret dan statif', 'Labu ukur',
                    'Neraca analitik', 'Gelas kimia (beaker glass)', 'Erlenmeyer', 'Batang pengaduk',
                ],
                'bahan' => [
                    'Aquades', 'Larutan NaCl 0,1 M', 'Tisu laboratorium',
                ],
                'prosedur' => [
                    'Kenali dan catat nama serta fungsi setiap alat yang tersedia di meja praktikum.',
                    'Latih teknik memegang dan membaca skala pada gelas ukur menggunakan aquades.',
                    'Timbang sampel menggunakan neraca analitik dengan teknik penimbangan yang benar.',
                    'Praktikkan pembacaan meniskus pada buret yang telah diisi larutan NaCl 0,1 M.',
                    'Buat larutan dengan konsentrasi tertentu menggunakan labu ukur dan catat hasil pengenceran.',
                    'Bersihkan dan kembalikan seluruh alat ke tempat semula sesuai prosedur keselamatan kerja.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Alat manakah yang paling tepat digunakan untuk mengukur volume cairan dengan ketelitian sangat tinggi?',
                        'opsi' => ['Gelas ukur', 'Mikropipet', 'Gelas kimia', 'Erlenmeyer'],
                        'jawaban' => 1,
                        'penjelasan' => 'Mikropipet memiliki ketelitian hingga 0,001 mL sehingga cocok untuk volume yang sangat kecil dan presisi.',
                    ],
                    [
                        'pertanyaan' => 'Di bagian manakah pembacaan skala pada alat ukur volume cairan seharusnya dilakukan?',
                        'opsi' => ['Bagian atas meniskus', 'Bagian bawah meniskus', 'Bagian tengah meniskus', 'Tidak masalah di bagian mana saja'],
                        'jawaban' => 1,
                        'penjelasan' => 'Pembacaan dilakukan pada bagian bawah meniskus untuk menghindari kesalahan paralaks.',
                    ],
                    [
                        'pertanyaan' => 'Alat apa yang digunakan untuk menimbang massa zat dengan ketelitian tinggi?',
                        'opsi' => ['Gelas ukur', 'Buret', 'Neraca analitik', 'Labu ukur'],
                        'jawaban' => 2,
                        'penjelasan' => 'Neraca analitik dirancang untuk menimbang massa zat dengan tingkat ketelitian tinggi.',
                    ],
                ],
            ],
            [
                'judul' => 'Sifat-sifat Fisika dan Kimia',
                'tujuan' => [
                    'Mengamati beberapa sifat-sifat kimia logam dan nonlogam',
                    'Menentukan titik didih metanol dan cairan lain',
                    'Menentukan dapat atau tidaknya suatu senyawa padat larut dalam air',
                    'Menentukan dapat atau tidaknya suatu cairan bercampur dengan air',
                    'Menentukan suatu zat mengalami perubahan fisika atau kimia',
                ],
                'pendahuluan' => 'Setiap zat memiliki sifat fisika (dapat diamati tanpa perubahan komposisi) dan sifat kimia (teramati saat terjadi reaksi/perubahan komposisi). Memahami perbedaan ini penting sebagai dasar identifikasi dan karakterisasi suatu zat dalam ilmu kimia.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Definisi Sifat Fisika', 'isi' => 'Sifat fisika adalah karakteristik zat yang dapat diukur atau diamati tanpa mengubah komposisi kimianya, misalnya titik didih, titik leleh, dan kelarutan.'],
                    ['judul' => 'Definisi Sifat Kimia', 'isi' => 'Sifat kimia teramati ketika zat mengalami perubahan komposisi melalui reaksi kimia, seperti mudah tidaknya suatu zat terbakar atau bereaksi dengan zat lain.'],
                    ['judul' => 'Titik Didih sebagai Sifat Fisika', 'isi' => 'Titik didih suatu cairan merupakan sifat fisika yang khas dan dapat digunakan untuk membantu identifikasi suatu senyawa.'],
                    ['judul' => 'Reaksi Kimia sebagai Indikator Perubahan Kimia', 'isi' => 'Terbentuknya gas, perubahan warna, atau endapan saat dua zat direaksikan menunjukkan telah terjadi perubahan kimia.'],
                    ['judul' => 'Materi Homogen dan Heterogen', 'isi' => 'Materi serbasama (homogen) memiliki sifat fisika dan kimia yang tetap di seluruh bagiannya, berbeda dengan materi serbaneka (heterogen).'],
                ],
                'alat' => [
                    'Tabung reaksi dan rak tabung', 'Penjepit tabung reaksi', 'Termometer',
                    'Pembakar spiritus (bunsen)', 'Gelas kimia', 'Kaki tiga dan kasa asbes',
                ],
                'bahan' => [
                    'Logam Mg (magnesium)', 'Logam Cu (tembaga)', 'Serbuk belerang (nonlogam)',
                    'Metanol', 'Minyak goreng', 'Air (aquades)', 'Larutan HCl encer',
                ],
                'prosedur' => [
                    'Amati bentuk fisik dan warna dari sampel logam dan nonlogam yang tersedia.',
                    'Reaksikan sedikit logam Mg dengan larutan HCl encer, amati gelembung gas yang terbentuk.',
                    'Panaskan metanol dalam tabung reaksi menggunakan pembakar spiritus, catat suhu saat mulai mendidih.',
                    'Masukkan garam dapur ke dalam air, amati apakah larut sempurna.',
                    'Campurkan minyak goreng dengan air, amati apakah kedua cairan bercampur atau memisah.',
                    'Simpulkan setiap pengamatan sebagai perubahan fisika atau kimia berdasarkan ciri yang muncul.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Manakah dari berikut ini yang termasuk sifat fisika suatu zat?',
                        'opsi' => ['Mudah terbakar', 'Titik didih', 'Bereaksi dengan asam', 'Berkarat'],
                        'jawaban' => 1,
                        'penjelasan' => 'Titik didih dapat diukur tanpa mengubah komposisi kimia zat, sehingga tergolong sifat fisika.',
                    ],
                    [
                        'pertanyaan' => 'Tanda apa yang paling umum menunjukkan telah terjadi perubahan kimia?',
                        'opsi' => ['Perubahan bentuk', 'Perubahan suhu ruangan', 'Terbentuknya gas atau endapan baru', 'Perubahan volume akibat pemanasan'],
                        'jawaban' => 2,
                        'penjelasan' => 'Terbentuknya gas, endapan, atau perubahan warna menandakan terjadinya reaksi kimia.',
                    ],
                    [
                        'pertanyaan' => 'Mengapa minyak goreng dan air tidak bercampur ketika dikocok bersama?',
                        'opsi' => ['Keduanya bereaksi kimia', 'Perbedaan massa jenis dan sifat polaritas', 'Air lebih panas dari minyak', 'Minyak lebih kental sehingga mengendap'],
                        'jawaban' => 1,
                        'penjelasan' => 'Minyak bersifat nonpolar sedangkan air bersifat polar, sehingga keduanya tidak saling melarutkan.',
                    ],
                ],
            ],
        ];
    }

    protected function fisika(): array
    {
        return [
            [
                'judul' => 'Pengukuran',
                'tujuan' => [
                    'Mengukur besaran panjang suatu objek menggunakan jangka sorong, mikrometer sekrup, dan mistar',
                    'Mampu mengukur massa dengan benar',
                    'Menentukan nilai dari pengukuran, ketelitian, serta kesalahan pengukuran',
                ],
                'pendahuluan' => 'Pengukuran merupakan dasar dari seluruh kegiatan eksperimen fisika, di mana besaran seperti panjang dan massa diukur menggunakan alat dengan tingkat ketelitian berbeda. Pemahaman prinsip kerja jangka sorong, mikrometer sekrup, mistar, dan timbangan penting untuk memperoleh data pengukuran yang akurat.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Jangka Sorong dan Skala Nonius', 'isi' => 'Jangka sorong dapat mengukur diameter dalam, diameter luar, dan kedalaman benda, dengan skala nonius yang menunjukkan tingkat ketelitiannya.'],
                    ['judul' => 'Mikrometer Sekrup dan Ketelitiannya', 'isi' => 'Mikrometer sekrup digital memiliki ketelitian hingga 0,001 mm, sehingga lebih presisi dibanding jangka sorong untuk benda berukuran kecil.'],
                    ['judul' => 'Mistar sebagai Alat Ukur Panjang Sederhana', 'isi' => 'Mistar merupakan alat ukur panjang paling sederhana dengan ketelitian sekitar 0,1 cm, cocok untuk pengukuran yang tidak memerlukan presisi tinggi.'],
                    ['judul' => 'Ketelitian dan Kesalahan Pengukuran', 'isi' => 'Setiap alat ukur memiliki batas ketelitian tertentu, dan pengulangan pengukuran membantu mengurangi kesalahan acak serta meningkatkan keandalan data.'],
                    ['judul' => 'Neraca/Timbangan untuk Pengukuran Massa', 'isi' => 'Timbangan digunakan untuk mengukur massa benda dan perlu dikalibrasi (dinolkan/disetimbangkan) sebelum digunakan agar hasil akurat.'],
                ],
                'alat' => [
                    'Jangka sorong', 'Mikrometer sekrup', 'Mistar/penggaris', 'Neraca/timbangan digital',
                ],
                'bahan' => [
                    'Kubus logam', 'Bola kecil (kelereng)', 'Silinder pendek', 'Koin',
                ],
                'prosedur' => [
                    'Kalibrasikan setiap alat ukur (nolkan skala) sebelum digunakan.',
                    'Ukur panjang sisi kubus logam menggunakan mistar, catat hasilnya.',
                    'Ukur diameter bola kecil menggunakan jangka sorong, lakukan tiga kali pengulangan.',
                    'Ukur ketebalan koin menggunakan mikrometer sekrup, catat hasil dan bandingkan dengan jangka sorong.',
                    'Timbang massa masing-masing benda menggunakan neraca digital.',
                    'Hitung rata-rata dan ketidakpastian dari setiap hasil pengukuran yang dilakukan berulang.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Alat ukur manakah yang paling tepat digunakan untuk mengukur ketebalan koin secara presisi?',
                        'opsi' => ['Mistar', 'Jangka sorong', 'Mikrometer sekrup', 'Timbangan digital'],
                        'jawaban' => 2,
                        'penjelasan' => 'Mikrometer sekrup memiliki ketelitian hingga 0,001 mm, lebih presisi untuk benda tipis seperti koin.',
                    ],
                    [
                        'pertanyaan' => 'Mengapa pengukuran perlu dilakukan berulang kali?',
                        'opsi' => ['Agar praktikum terlihat lebih lama', 'Untuk mengurangi kesalahan acak dan meningkatkan keandalan data', 'Karena alat ukur cepat rusak', 'Supaya semua praktikan mendapat giliran'],
                        'jawaban' => 1,
                        'penjelasan' => 'Pengulangan pengukuran membantu mengurangi kesalahan acak dan membuat data lebih dapat diandalkan.',
                    ],
                    [
                        'pertanyaan' => 'Apa fungsi skala nonius pada jangka sorong?',
                        'opsi' => ['Menambah panjang alat ukur', 'Menunjukkan tingkat ketelitian pengukuran', 'Mengganti fungsi mistar', 'Mengukur suhu benda'],
                        'jawaban' => 1,
                        'penjelasan' => 'Skala nonius memungkinkan pembacaan yang lebih teliti dibanding skala utama saja.',
                    ],
                ],
            ],
            [
                'judul' => 'Gerak Jatuh Bebas',
                'tujuan' => [
                    'Menghitung besarnya nilai percepatan gravitasi di titik tertentu dengan variasi ketinggian',
                ],
                'pendahuluan' => 'Gerak jatuh bebas adalah gerak benda yang jatuh akibat pengaruh gravitasi tanpa kecepatan awal. Percobaan ini mendasari pemahaman tentang percepatan gravitasi bumi dan hubungan antara ketinggian, waktu jatuh, dan percepatan.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Konsep Gerak Jatuh Bebas', 'isi' => 'Pada gerak jatuh bebas, benda dijatuhkan tanpa kecepatan awal (vo = 0) dan bergerak akibat percepatan gravitasi bumi.'],
                    ['judul' => 'Percepatan Gravitasi Bumi', 'isi' => 'Percepatan gravitasi (g) menyebabkan benda yang jatuh bebas mengalami percepatan konstan menuju pusat bumi.'],
                    ['judul' => 'Hubungan Ketinggian dan Waktu Jatuh', 'isi' => 'Persamaan h = ½ g t² menghubungkan ketinggian jatuh (h), percepatan gravitasi (g), dan waktu jatuh (t), sehingga nilai g dapat dihitung dari data h dan t.'],
                    ['judul' => 'Faktor yang Memengaruhi Akurasi Pengukuran Waktu Jatuh', 'isi' => 'Pengulangan pengukuran pada ketinggian dan objek yang sama membantu memperoleh data yang lebih akurat dan mengurangi kesalahan acak akibat reaksi pengamat.'],
                ],
                'alat' => [
                    'Statif dan penjepit', 'Stopwatch', 'Mistar/meteran', 'Bola logam kecil',
                ],
                'bahan' => [
                    'Bola pejal berbagai ukuran',
                ],
                'prosedur' => [
                    'Pasang statif dan tentukan ketinggian awal benda yang akan dijatuhkan.',
                    'Jatuhkan bola dari ketinggian tertentu tanpa kecepatan awal, catat waktu jatuh menggunakan stopwatch.',
                    'Ulangi pengukuran pada ketinggian yang sama sebanyak tiga kali, catat waktu setiap percobaan.',
                    'Ubah ketinggian jatuh dan ulangi langkah 2-3 untuk minimal tiga variasi ketinggian.',
                    'Hitung percepatan gravitasi menggunakan persamaan h = ½ g t² untuk setiap variasi ketinggian.',
                    'Bandingkan hasil perhitungan dengan nilai percepatan gravitasi standar dan diskusikan sumber kesalahannya.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Berapa kecepatan awal benda pada gerak jatuh bebas?',
                        'opsi' => ['Sama dengan kecepatan akhir', 'Nol', 'Tergantung ketinggian', 'Tidak dapat ditentukan'],
                        'jawaban' => 1,
                        'penjelasan' => 'Pada gerak jatuh bebas, benda dijatuhkan tanpa kecepatan awal sehingga vo = 0.',
                    ],
                    [
                        'pertanyaan' => 'Persamaan apa yang digunakan untuk menghitung percepatan gravitasi dari data ketinggian dan waktu jatuh?',
                        'opsi' => ['v = v0 + at', 'h = ½ g t²', 'F = m.a', 'E = m.c²'],
                        'jawaban' => 1,
                        'penjelasan' => 'Persamaan h = ½ g t² menghubungkan ketinggian, percepatan gravitasi, dan waktu jatuh.',
                    ],
                    [
                        'pertanyaan' => 'Mengapa pengukuran waktu jatuh perlu diulang beberapa kali pada ketinggian yang sama?',
                        'opsi' => ['Agar alat tidak cepat rusak', 'Untuk mengurangi kesalahan acak akibat reaksi pengamat', 'Karena bola bisa berubah bentuk', 'Tidak ada alasan khusus'],
                        'jawaban' => 1,
                        'penjelasan' => 'Pengulangan membantu mengurangi kesalahan acak, terutama akibat keterlambatan reaksi saat menekan stopwatch.',
                    ],
                ],
            ],
        ];
    }

    protected function biologi(): array
    {
        return [
            [
                'judul' => 'Klasifikasi Tanaman',
                'tujuan' => [
                    'Melakukan deskripsi tumbuhan kemudian mengelompokkannya berdasarkan kesamaan dan perbedaan yang dimiliki',
                    'Menyusun tabel karakter dan bagan klasifikasi sebagai langkah awal menyusun kunci determinasi tumbuhan',
                ],
                'pendahuluan' => 'Keanekaragaman tumbuhan yang sangat besar memerlukan sistem klasifikasi untuk memudahkan identifikasi dan pengelompokan. Klasifikasi tumbuhan didasarkan pada kesamaan dan perbedaan ciri morfologi yang kemudian disusun dalam bentuk kunci determinasi.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Sistem Klasifikasi Alami, Buatan, dan Filogeni', 'isi' => 'Klasifikasi alami mengelompokkan berdasarkan kemiripan morfologi, klasifikasi buatan berdasarkan kepentingan manusia, sedangkan filogeni berdasarkan kekerabatan evolusi.'],
                    ['judul' => 'Binomial Nomenclature', 'isi' => 'Carolus Linnaeus meletakkan dasar tata nama ganda (binomial nomenclature) yang menjadi standar penamaan ilmiah makhluk hidup hingga sekarang.'],
                    ['judul' => 'Tabel Karakterisasi dan Diagram Dikotomi', 'isi' => 'Tabel karakter mencatat ciri-ciri spesimen, sedangkan diagram dikotomi memisahkan kelompok tumbuhan secara bertahap berdasarkan karakter pembeda.'],
                    ['judul' => 'Kunci Determinasi Tumbuhan', 'isi' => 'Kunci determinasi disusun untuk membandingkan suatu tumbuhan dengan tumbuhan lain yang sudah dikenal guna memastikan identitasnya.'],
                ],
                'alat' => [
                    'Lup (kaca pembesar)', 'Alat tulis dan tabel pengamatan', 'Kamera/alat dokumentasi',
                ],
                'bahan' => [
                    'Minimal 5 jenis daun/tumbuhan berbeda dari lingkungan sekitar',
                ],
                'prosedur' => [
                    'Kumpulkan minimal lima sampel tumbuhan/daun yang berbeda dari lingkungan sekitar.',
                    'Amati dan catat ciri morfologi tiap sampel (bentuk daun, tepi daun, susunan tulang daun, dll) menggunakan lup.',
                    'Susun tabel karakter berdasarkan ciri-ciri yang telah diamati untuk seluruh sampel.',
                    'Kelompokkan sampel berdasarkan kesamaan dan perbedaan ciri yang dicatat dalam tabel.',
                    'Buat bagan/diagram dikotomi sederhana yang memisahkan kelompok tumbuhan secara bertahap.',
                    'Susun kunci determinasi sederhana berdasarkan bagan yang telah dibuat.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Sistem klasifikasi apa yang mengelompokkan tumbuhan berdasarkan kekerabatan evolusi?',
                        'opsi' => ['Klasifikasi alami', 'Klasifikasi buatan', 'Klasifikasi filogeni', 'Klasifikasi manual'],
                        'jawaban' => 2,
                        'penjelasan' => 'Klasifikasi filogeni mengelompokkan makhluk hidup berdasarkan kekerabatan evolusinya.',
                    ],
                    [
                        'pertanyaan' => 'Siapakah yang meletakkan dasar tata nama ganda (binomial nomenclature)?',
                        'opsi' => ['Charles Darwin', 'Carolus Linnaeus', 'Gregor Mendel', 'Louis Pasteur'],
                        'jawaban' => 1,
                        'penjelasan' => 'Carolus Linnaeus dikenal sebagai pencetus sistem tata nama ganda yang masih digunakan hingga kini.',
                    ],
                    [
                        'pertanyaan' => 'Apa fungsi utama kunci determinasi tumbuhan?',
                        'opsi' => ['Menentukan umur tumbuhan', 'Membandingkan tumbuhan untuk memastikan identitasnya', 'Mengukur tinggi tumbuhan', 'Menentukan habitat tumbuhan'],
                        'jawaban' => 1,
                        'penjelasan' => 'Kunci determinasi digunakan untuk membandingkan suatu tumbuhan dengan tumbuhan yang sudah dikenal guna memastikan identitasnya.',
                    ],
                ],
            ],
            [
                'judul' => 'Morfologi Akar',
                'tujuan' => [
                    'Menjelaskan bagian-bagian radikula',
                    'Menjelaskan perbedaan struktur morfologi akar berdasarkan percabangannya',
                    'Menjelaskan modifikasi akar pada tumbuhan',
                ],
                'pendahuluan' => 'Akar merupakan salah satu organ vegetatif utama tumbuhan yang berfungsi menyerap air dan hara serta menopang tubuh tumbuhan. Struktur dan modifikasi akar bervariasi antar spesies sesuai fungsi dan lingkungan tumbuhnya.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Radikula dan Perkembangan Akar', 'isi' => 'Radikula pada biji yang berkecambah akan berkembang menjadi akar utama tumbuhan.'],
                    ['judul' => 'Akar Tunggang vs Akar Serabut', 'isi' => 'Akar tunggang berkembang dari radikula menjadi akar utama tunggal, sedangkan akar serabut tersusun dari akar-akar kecil berbentuk benang seperti pada padi.'],
                    ['judul' => 'Ciri Pembeda Akar dan Batang', 'isi' => 'Akar umumnya tidak berwarna hijau, tidak memiliki nodus/internodus/mata tunas, dan bersifat (+) geotropik serta (−) fototropik, berbeda dengan batang.'],
                    ['judul' => 'Modifikasi Akar', 'isi' => 'Akar dapat termodifikasi menjadi akar penyimpanan (misal pada lobak/wortel), akar pengisap pada tumbuhan parasit seperti benalu, maupun akar udara pada anggrek.'],
                ],
                'alat' => [
                    'Lup (kaca pembesar)', 'Pisau/cutter', 'Alat tulis dan tabel pengamatan',
                ],
                'bahan' => [
                    'Akar tumbuhan dikotil (misal: kacang tanah)', 'Akar tumbuhan monokotil (misal: padi/jagung)', 'Wortel atau lobak sebagai contoh akar modifikasi',
                ],
                'prosedur' => [
                    'Amati struktur akar tumbuhan dikotil dan catat bentuk percabangannya.',
                    'Amati struktur akar tumbuhan monokotil dan bandingkan dengan akar dikotil.',
                    'Identifikasi ciri-ciri yang membedakan akar dari batang pada sampel yang diamati.',
                    'Belah wortel/lobak dan amati bagian yang menunjukkan modifikasi akar sebagai tempat penyimpanan cadangan makanan.',
                    'Gambarkan hasil pengamatan struktur akar tunggang dan akar serabut pada lembar kerja.',
                    'Diskusikan fungsi masing-masing tipe dan modifikasi akar yang telah diamati.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Radikula pada biji yang berkecambah akan berkembang menjadi apa?',
                        'opsi' => ['Batang utama', 'Akar utama tumbuhan', 'Daun pertama', 'Bunga'],
                        'jawaban' => 1,
                        'penjelasan' => 'Radikula merupakan calon akar yang akan berkembang menjadi akar utama tumbuhan.',
                    ],
                    [
                        'pertanyaan' => 'Tumbuhan manakah yang umumnya memiliki sistem akar serabut?',
                        'opsi' => ['Kacang tanah', 'Padi', 'Mangga', 'Jambu'],
                        'jawaban' => 1,
                        'penjelasan' => 'Padi merupakan tumbuhan monokotil yang memiliki sistem akar serabut, bukan akar tunggang.',
                    ],
                    [
                        'pertanyaan' => 'Modifikasi akar pada wortel berfungsi sebagai apa?',
                        'opsi' => ['Alat pernapasan', 'Tempat penyimpanan cadangan makanan', 'Alat fotosintesis', 'Alat pengisap zat hara dari inang'],
                        'jawaban' => 1,
                        'penjelasan' => 'Akar wortel termodifikasi menjadi organ penyimpanan cadangan makanan bagi tumbuhan.',
                    ],
                ],
            ],
        ];
    }
}
