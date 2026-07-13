<?php

namespace Database\Seeders;

use App\Models\Matakuliah;
use App\Models\Materi;
use App\Models\Praktikum;
use App\Models\Prodi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Mengisi konten untuk mata kuliah Termodinamika dan Perpindahan Panas serta
 * Karakteristik Bahan Hasil Pertanian (Semester 3, prodi TEP).
 *
 * Pemisahan konten:
 * - Materi  = teori saja (Pendahuluan + Tinjauan Pustaka).
 * - Praktikum = pelaksanaan (Tujuan, Alat, Bahan, Prosedur/Langkah Kerja, Kuis).
 *
 * Slug sudah dikonfirmasi langsung dari tabel matakuliahs:
 * - 'tep-s3-termodinamika' untuk Termodinamika dan Perpindahan Panas
 * - 'tep-s3-karakter-bahan-hasil-pertanian' untuk Karakteristik Bahan Hasil Pertanian
 */
class TepSemester3KontenSeeder extends Seeder
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

        $this->seedMatakuliah($prodi, 'tep-s3-termodinamika', $this->termodinamika());
        $this->seedMatakuliah($prodi, 'tep-s3-karakter-bahan-hasil-pertanian', $this->karakteristikBahanHasilPertanian());
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

            // Materi = teori saja: capaian, pendahuluan, tinjauan pustaka.
            // (alat/bahan/prosedur TIDAK dikirim ke sini lagi -- sudah pindah ke Praktikum)
            Materi::updateOrCreate(
                ['matakuliah_id' => $matakuliah->id, 'slug' => $materiSlug],
                [
                    'prodi_id' => $prodi->id,
                    'pertemuan_ke' => $nomor,
                    'judul' => 'Objek '.$nomor.': '.$objek['judul'],
                    'pokok_bahasan' => array_column($objek['tinjauan_pustaka'], 'judul'),
                    'capaian' => $objek['tujuan'],
                    'pendahuluan' => $objek['pendahuluan'],
                    'tinjauan_pustaka' => $objek['tinjauan_pustaka'],
                ]
            );

            // Praktikum = pelaksanaan: tujuan, alat, bahan (terpisah), langkah, kuis.
            // (alat_bahan gabungan TIDAK dikirim lagi -- kolomnya sudah dihapus)
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

    protected function termodinamika(): array
    {
        return [
            [
                'judul' => 'Kesetimbangan Termal dan Termodinamika 1',
                'tujuan' => [
                    'Menentukan kesetimbangan termal dalam kehidupan sehari-hari',
                    'Menentukan Hukum Termodinamika 1 dalam kehidupan sehari-hari',
                ],
                'pendahuluan' => 'Kesetimbangan termal terjadi ketika dua benda yang saling bersentuhan (berada dalam kontak termal) akhirnya mencapai suhu yang sama. Konsep ini menjadi dasar dalam mempelajari Hukum I Termodinamika, yang menyatakan bahwa kalor dan kerja mekanik dapat saling dipertukarkan, serta bahwa energi tidak dapat diciptakan maupun dimusnahkan, hanya berubah bentuk.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Konsep Kesetimbangan Termal', 'isi' => 'Kesetimbangan termal tercapai apabila dua benda yang berada dalam kontak termal akhirnya memiliki suhu yang sama karena kalor mengalir dari benda yang lebih panas ke benda yang lebih dingin.'],
                    ['judul' => 'Kontak Termal', 'isi' => 'Dua sistem dikatakan berada dalam kontak termal jika keduanya dapat saling memengaruhi suhu satu sama lain, misalnya benda yang ditempatkan berdampingan pada suhu ruangan.'],
                    ['judul' => 'Hukum Kekekalan Energi', 'isi' => 'Energi tidak dapat diciptakan atau dimusnahkan, melainkan hanya dapat diubah dari satu bentuk ke bentuk lainnya, termasuk pertukaran antara kalor dan kerja mekanik.'],
                    ['judul' => 'Hukum I Termodinamika', 'isi' => 'Secara matematis dituliskan sebagai Q = W + ΔU, dengan Q adalah kalor yang masuk/keluar sistem, W adalah usaha luar, dan ΔU adalah perubahan energi dalam sistem.'],
                    ['judul' => 'Usaha Luar dan Usaha Dalam', 'isi' => 'Usaha luar (W) adalah usaha yang dilakukan sistem terhadap lingkungannya, sedangkan usaha dalam (U) berkaitan dengan pergerakan molekul di dalam sistem itu sendiri, misalnya pada gas yang dipanaskan.'],
                ],
                'alat' => [
                    'Termometer', 'Panci', 'Kompor', 'Gelas',
                ],
                'bahan' => [
                    'Es batu 3 buah', 'Lilin', 'Air', 'Balon 9 buah', 'Korek api',
                ],
                'prosedur' => [
                    'Siapkan panci dan masukkan es batu secukupnya, ukur suhu awal es batu menggunakan termometer.',
                    'Panaskan es batu di dalam panci dengan nyala api sedang, ukur suhu setelah dipanaskan pada rentang waktu tertentu.',
                    'Catat perubahan suhu untuk mengamati proses menuju kesetimbangan termal.',
                    'Masukkan lilin ke dalam gelas dan nyalakan menggunakan korek api.',
                    'Letakkan balon kosong (tanpa air) di atas gelas berisi lilin yang menyala, amati dan catat reaksi yang terjadi.',
                    'Isi balon kedua dengan air, letakkan di atas gelas berisi lilin menyala, amati dan catat perbedaan hasil dibanding balon tanpa air.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Kapan dua benda dikatakan berada dalam kesetimbangan termal?',
                        'opsi' => ['Ketika massanya sama', 'Ketika suhunya sudah sama setelah kontak termal', 'Ketika volumenya sama', 'Ketika warnanya sama'],
                        'jawaban' => 1,
                        'penjelasan' => 'Kesetimbangan termal tercapai ketika dua benda yang berkontak termal akhirnya memiliki suhu yang sama.',
                    ],
                    [
                        'pertanyaan' => 'Bagaimana bunyi Hukum I Termodinamika secara matematis?',
                        'opsi' => ['Q = W - ΔU', 'Q = W + ΔU', 'W = Q x ΔU', 'ΔU = Q x W'],
                        'jawaban' => 1,
                        'penjelasan' => 'Hukum I Termodinamika dituliskan sebagai Q = W + ΔU, dimana kalor yang diberikan sama dengan usaha luar ditambah perubahan energi dalam.',
                    ],
                    [
                        'pertanyaan' => 'Mengapa balon berisi air tidak mudah meletus ketika diletakkan di atas nyala lilin, berbeda dengan balon kosong?',
                        'opsi' => ['Balon berisi air lebih tebal', 'Air menyerap panas sehingga suhu permukaan balon tidak cepat naik', 'Air membuat balon menjadi anti api', 'Tidak ada perbedaan di antara keduanya'],
                        'jawaban' => 1,
                        'penjelasan' => 'Air di dalam balon menyerap kalor dari api sehingga suhu dinding balon tetap relatif rendah dan tidak cepat mencapai titik leleh karet balon.',
                    ],
                ],
            ],
            [
                'judul' => 'Gas Ideal dan Mesin Pembeku',
                'tujuan' => [
                    'Menentukan gas ideal dalam kehidupan sehari-hari',
                    'Menentukan cara kerja mesin pembeku',
                ],
                'pendahuluan' => 'Gas ideal adalah kumpulan partikel yang bergerak acak dengan jarak antar partikel yang jauh lebih besar dibanding ukuran partikelnya, mengikuti persamaan keadaan PV = nRT. Prinsip serupa mendasari kerja mesin refrigerasi seperti freezer, yang mampu menurunkan suhu ruangan penyimpanan untuk mengawetkan bahan pangan.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Definisi dan Persamaan Gas Ideal', 'isi' => 'Gas ideal merupakan kumpulan partikel yang bergerak acak ke segala arah dengan tumbukan lenting sempurna, mengikuti hukum gas ideal PV = nRT.'],
                    ['judul' => 'Karakteristik Umum Gas', 'isi' => 'Gas dapat mengembang mengisi seluruh ruangan, mudah dimampatkan, berdifusi cepat, memberikan tekanan ke segala arah, dan volumenya meningkat seiring kenaikan suhu pada tekanan tetap.'],
                    ['judul' => 'Penyimpangan dari Sifat Gas Ideal', 'isi' => 'Pada temperatur rendah atau densitas tinggi, fluida nyata menyimpang jauh dari sifat gas ideal karena dapat terkondensasi menjadi cairan atau terdeposisi menjadi padatan.'],
                    ['judul' => 'Mesin Refrigerasi', 'isi' => 'Mesin yang mampu menghasilkan efek dingin disebut mesin refrigerasi, salah satu aplikasinya adalah freezer yang menghasilkan suhu ruangan sekitar 0°C untuk mengawetkan bahan pangan.'],
                    ['judul' => 'Komponen Utama Freezer', 'isi' => 'Freezer domestik terdiri dari tiga komponen utama yaitu kabinet (tempat penyimpanan), rangkaian listrik (kabel dan alat kontrol), dan mesin refrigerasi yang mengalirkan fluida kerja (refrigeran) melalui pipa membentuk suatu siklus.'],
                ],
                'alat' => [
                    'Termometer', 'Panci', 'Kompor', 'Botol plastik kosong', 'Stopwatch', 'Kulkas', 'Plastik', 'Gelas ukur',
                ],
                'bahan' => [
                    'Tisu/serbet', 'Air panas', 'Air dingin', 'Kacang hijau 1 kg',
                ],
                'prosedur' => [
                    'Buka tutup botol plastik kosong dan masukkan sedikit air ke dalamnya, tunggu beberapa menit.',
                    'Tutup rapat botol tersebut, kemudian masukkan ke dalam wadah berisi air es dan amati apa yang terjadi pada botol.',
                    'Posisikan kulkas dalam kondisi mati dengan termostat pengatur suhu berada pada titik nol, lalu ukur suhu kulkas saat itu.',
                    'Isikan air sebanyak 150 mL ke dalam plastik dan ikat kencang, siapkan sebanyak 3 buah.',
                    'Ukur suhu air awal dan massa air pada masing-masing plastik sebelum dimasukkan ke kulkas.',
                    'Pada interval 30 menit (menit ke-30, 60, dan 90), ukur suhu, volume, serta massa air/es pada masing-masing plastik secara bergantian.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Apa persamaan keadaan yang digunakan untuk menggambarkan gas ideal?',
                        'opsi' => ['PV = nRT', 'F = ma', 'E = mc²', 'Q = mcΔT'],
                        'jawaban' => 0,
                        'penjelasan' => 'Persamaan keadaan gas ideal adalah PV = nRT, yang menghubungkan tekanan, volume, jumlah mol, dan suhu gas.',
                    ],
                    [
                        'pertanyaan' => 'Mengapa botol plastik kosong yang ditutup rapat dan dimasukkan ke air es akan mengalami perubahan bentuk (mengempis)?',
                        'opsi' => ['Karena plastiknya meleleh', 'Karena tekanan udara di dalam botol menurun seiring turunnya suhu', 'Karena airnya menguap', 'Tidak ada perubahan yang terjadi'],
                        'jawaban' => 1,
                        'penjelasan' => 'Penurunan suhu menyebabkan tekanan gas di dalam botol menurun sesuai hukum gas, sehingga botol tampak mengempis akibat tekanan udara luar yang lebih besar.',
                    ],
                    [
                        'pertanyaan' => 'Apa komponen utama pada freezer domestik yang berfungsi mengalirkan fluida kerja membentuk suatu siklus?',
                        'opsi' => ['Kabinet', 'Rangkaian listrik', 'Mesin refrigerasi', 'Termostat'],
                        'jawaban' => 2,
                        'penjelasan' => 'Mesin refrigerasi merupakan susunan alat yang terhubung oleh pipa, tempat fluida kerja (refrigeran) mengalir membentuk sebuah siklus.',
                    ],
                ],
            ],
            [
                'judul' => 'Perpindahan Panas secara Konveksi dan Konduksi',
                'tujuan' => [
                    'Menentukan perpindahan panas secara konveksi',
                    'Menentukan perpindahan panas secara konduksi',
                ],
                'pendahuluan' => 'Perpindahan panas dapat terjadi melalui tiga mekanisme utama yaitu konduksi, konveksi, dan radiasi. Konveksi terjadi antara permukaan padat dan fluida yang mengalir, sedangkan konduksi terjadi melalui rambatan energi antar partikel yang berdekatan di dalam suatu zat, dari titik bersuhu tinggi menuju titik bersuhu rendah.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Perpindahan Panas Konveksi', 'isi' => 'Konveksi panas dapat dihitung dengan persamaan pendinginan Newton, q = -h.A.ΔT, dengan h adalah koefisien perpindahan kalor secara konveksi dan A adalah luas bidang permukaan.'],
                    ['judul' => 'Jenis Konveksi', 'isi' => 'Konveksi dibedakan menjadi konveksi alamiah, yang terjadi akibat perbedaan massa jenis fluida akibat pemanasan, dan konveksi paksa, yang mengarahkan fluida panas menggunakan blower atau pompa.'],
                    ['judul' => 'Perpindahan Panas Konduksi', 'isi' => 'Konduksi adalah pengangkutan kalor melalui satu jenis zat tanpa perpindahan zat itu sendiri, mengalir dari titik bersuhu tinggi ke titik bersuhu rendah.'],
                    ['judul' => 'Konduktor dan Isolator', 'isi' => 'Bahan yang dapat menghantar kalor dengan baik disebut konduktor, sedangkan penghantar yang buruk disebut isolator, yang dibedakan berdasarkan nilai koefisien konduktivitas termalnya.'],
                    ['judul' => 'Persamaan Konduksi (Hukum Fourier)', 'isi' => 'Perpindahan panas konduksi dirumuskan sebagai H = -kA(∂T/∂x), dengan k sebagai konduktivitas termal, A luas permukaan, dan ∂T/∂x sebagai gradien suhu ke arah perpindahan panas.'],
                ],
                'alat' => [
                    'Gelas erlenmeyer', 'Termometer', 'Kompor/hotplate', 'Korek api', 'Stopwatch', 'Bunsen', 'Penjepit',
                ],
                'bahan' => [
                    'Serpihan kertas', 'Air', 'Kaca', 'Aluminium', 'Lilin', 'Tembaga', 'Besi',
                ],
                'prosedur' => [
                    'Masukkan potongan kertas kecil dan air ke dalam gelas erlenmeyer.',
                    'Panaskan gelas erlenmeyer berisi kertas dan air menggunakan hotplate, ukur suhu di dalamnya menggunakan termometer.',
                    'Amati dan catat pola pergerakan potongan kertas di dalam air sebagai indikasi arus konveksi yang terjadi.',
                    'Nyalakan bunsen, kemudian panaskan salah satu ujung batang bahan uji (besi, tembaga, aluminium, atau kaca) secara bergantian.',
                    'Tempelkan sedikit lilin pada sisi lain dari batang bahan yang tidak dipanaskan langsung.',
                    'Ukur dan catat waktu yang dibutuhkan hingga lilin meleleh pada setiap jenis bahan, kemudian bandingkan hasilnya.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Persamaan apa yang digunakan untuk menghitung perpindahan panas secara konveksi?',
                        'opsi' => ['q = -h.A.ΔT', 'H = -kA(∂T/∂x)', 'PV = nRT', 'Q = W + ΔU'],
                        'jawaban' => 0,
                        'penjelasan' => 'Konveksi panas dihitung menggunakan persamaan pendinginan Newton, q = -h.A.ΔT.',
                    ],
                    [
                        'pertanyaan' => 'Jenis konveksi apa yang terjadi akibat perbedaan massa jenis fluida tanpa bantuan alat seperti blower?',
                        'opsi' => ['Konveksi paksa', 'Konveksi alamiah', 'Konveksi radiasi', 'Konveksi konduktif'],
                        'jawaban' => 1,
                        'penjelasan' => 'Konveksi alamiah terjadi akibat perbedaan massa jenis fluida, di mana fluida panas yang lebih ringan bergerak naik digantikan fluida dingin yang lebih berat.',
                    ],
                    [
                        'pertanyaan' => 'Di antara bahan berikut, manakah yang diperkirakan paling cepat menghantarkan panas sehingga lilin lebih cepat meleleh?',
                        'opsi' => ['Kaca', 'Kayu', 'Tembaga', 'Karet'],
                        'jawaban' => 2,
                        'penjelasan' => 'Tembaga merupakan konduktor logam dengan konduktivitas termal tinggi sehingga menghantarkan panas lebih cepat dibanding bahan seperti kaca.',
                    ],
                ],
            ],
            [
                'judul' => 'Heat Exchange',
                'tujuan' => [
                    'Menentukan laju perpindahan panas aktual pada penukar panas',
                    'Menghitung koefisien perpindahan panas menyeluruh untuk konfigurasi aliran searah dan aliran berlawanan',
                    'Menghitung efektivitas penukar panas untuk berbagai variasi laju alir fluida',
                    'Menganalisis pengaruh laju alir fluida terhadap kinerja heat exchanger',
                ],
                'pendahuluan' => 'Heat exchanger adalah perangkat yang memfasilitasi pertukaran panas antara dua fluida bersuhu berbeda tanpa saling bercampur. Jenis paling sederhana adalah double pipe heat exchanger, yang dapat dioperasikan dengan konfigurasi aliran searah (parallel flow) maupun aliran berlawanan (counter flow).',
                'tinjauan_pustaka' => [
                    ['judul' => 'Fungsi Heat Exchanger', 'isi' => 'Heat exchanger memfasilitasi pertukaran panas antara dua fluida bersuhu berbeda, melibatkan konveksi pada setiap fluida dan konduksi melalui dinding pemisah kedua fluida tersebut.'],
                    ['judul' => 'Konfigurasi Aliran', 'isi' => 'Terdapat dua jenis pengaturan aliran pada double pipe heat exchanger, yaitu aliran parallel (fluida panas dan dingin masuk pada ujung yang sama dan bergerak searah) dan aliran counter (fluida masuk dari ujung berlawanan dan mengalir berlawanan arah).'],
                    ['judul' => 'Mekanisme Perpindahan Panas', 'isi' => 'Panas dapat berpindah melalui tiga cara yaitu konduksi (perpindahan energi antar partikel yang berdekatan), konveksi (perpindahan energi oleh pergerakan fluida), dan radiasi (energi yang dipancarkan dalam bentuk gelombang elektromagnetik).'],
                    ['judul' => 'Konduktivitas dan Resistansi Termal', 'isi' => 'Konduktivitas termal menunjukkan kemampuan material menghantarkan panas, sedangkan resistansi termal berbanding terbalik dengan konduktivitas dan menunjukkan ketahanan material terhadap aliran panas.'],
                    ['judul' => 'Efektivitas Heat Exchanger', 'isi' => 'Efektivitas heat exchanger (εh) merupakan rasio antara kuantitas panas aktual yang dipertukarkan dengan kuantitas panas ideal maksimum yang mungkin dipertukarkan pada sistem tersebut.'],
                ],
                'alat' => [
                    'Unit double pipe heat exchanger', 'Termometer', 'Rotameter', 'Gelas ukur', 'Stopwatch',
                ],
                'bahan' => [
                    'Air',
                ],
                'prosedur' => [
                    'Siapkan unit double pipe heat exchanger dan pastikan seluruh alat ukur (termometer, rotameter) berfungsi dengan baik.',
                    'Atur konfigurasi aliran fluida panas dan dingin dalam mode aliran searah (parallel flow), kemudian alirkan air panas dan air dingin dengan laju alir tertentu.',
                    'Catat suhu masuk dan suhu keluar untuk masing-masing fluida (panas dan dingin) setelah kondisi tunak (steady state) tercapai.',
                    'Ukur laju alir masing-masing fluida menggunakan rotameter dan/atau gelas ukur beserta stopwatch.',
                    'Ulangi langkah di atas dengan mengubah konfigurasi aliran menjadi aliran berlawanan (counter flow) pada laju alir yang sama.',
                    'Ulangi seluruh prosedur untuk minimal tiga variasi laju alir fluida, lalu hitung jumlah panas yang dipertukarkan, koefisien transmisi kalor (U), dan efektivitas heat exchanger (εh) untuk setiap kondisi.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Apa perbedaan utama antara konfigurasi aliran parallel flow dan counter flow pada heat exchanger?',
                        'opsi' => ['Parallel flow menggunakan pipa lebih besar', 'Pada parallel flow kedua fluida masuk dari sisi yang sama dan bergerak searah, sedangkan counter flow dari sisi berlawanan dan mengalir berlawanan arah', 'Counter flow hanya untuk gas', 'Tidak ada perbedaan signifikan'],
                        'jawaban' => 1,
                        'penjelasan' => 'Pada aliran parallel, fluida panas dan dingin masuk dari ujung yang sama dan bergerak searah, sedangkan pada aliran counter keduanya masuk dari ujung berlawanan dan mengalir berlawanan arah.',
                    ],
                    [
                        'pertanyaan' => 'Bagaimana cara menghitung efektivitas heat exchanger (εh)?',
                        'opsi' => ['Rasio antara panas aktual yang dipertukarkan dengan panas ideal maksimum', 'Rasio antara suhu masuk dan suhu keluar', 'Perkalian antara luas permukaan dan konduktivitas termal', 'Selisih suhu antara kedua fluida'],
                        'jawaban' => 0,
                        'penjelasan' => 'Efektivitas heat exchanger dihitung sebagai rasio antara kuantitas panas aktual yang dipertukarkan (q) dengan kuantitas panas ideal maksimum yang mungkin dipertukarkan (qmax).',
                    ],
                    [
                        'pertanyaan' => 'Apa satuan yang umum digunakan untuk resistansi termal?',
                        'opsi' => ['kg/m³', '(m²K)/W', 'kcal/jam', 'W/m²'],
                        'jawaban' => 1,
                        'penjelasan' => 'Resistansi termal memiliki satuan (m²K)/W, berbanding terbalik dengan konduktivitas termal bahan.',
                    ],
                ],
            ],
        ];
    }

    protected function karakteristikBahanHasilPertanian(): array
    {
        return [
            [
                'judul' => 'Sifat Fisik Gabah dan Beras dan Biji-bijian',
                'tujuan' => [
                    'Menentukan bulk density (g/cm³)',
                    'Menentukan angle of repose gabah, beras, dan biji-bijian (°)',
                    'Menentukan angle of friction gabah, beras, dan biji-bijian (°)',
                ],
                'pendahuluan' => 'Sifat fisik bahan hasil pertanian seperti bentuk, ukuran, densitas, dan porositas sangat penting dalam merancang alat penanganan produk pertanian. Pemahaman terhadap angle of repose dan angle of friction pada bahan berbentuk granular seperti gabah, beras, dan biji-bijian menjadi dasar dalam desain hoper serta sistem penyimpanan dan pengangkutan bahan.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Karakteristik Sifat Fisik Pertanian', 'isi' => 'Sifat fisik bahan hasil pertanian meliputi bentuk, ukuran, luas permukaan, warna, penampakan, berat, porositas, densitas, dan kadar air, yang penting untuk analisis perilaku dan penanganan produk.'],
                    ['judul' => 'Angle of Repose', 'isi' => 'Angle of repose adalah sudut yang terbentuk antara permukaan gundukan berbentuk kerucut dari bahan granular yang dituang di atas permukaan horizontal terhadap bidang horizontal tersebut.'],
                    ['judul' => 'Angle of Friction', 'isi' => 'Angle of friction adalah sudut kemiringan bidang (misalnya triplek) saat bahan granular mulai meluncur akibat gaya beratnya sendiri.'],
                    ['judul' => 'Kriteria Bentuk Beras (SNI Beras)', 'isi' => 'Berdasarkan SNI Beras, butir beras dibagi menjadi tiga kelompok yaitu butir beras utuh, butir beras patah, dan butir menir, berdasarkan proporsi ukuran panjangnya.'],
                    ['judul' => 'Sphericity dan Geometric Mean Diameter (GMD)', 'isi' => 'Sphericity menyatakan kebulatan suatu bahan dibandingkan bola dengan volume yang sama, dihitung dari GMD = (a.b.c)^(1/3) dibagi dengan diameter mayor bahan.'],
                ],
                'alat' => [
                    'Timbangan digital/manual', 'Vernier caliper/jangka sorong', 'Papan triplek dan plat tipis (40x40) cm', 'Pipa', 'Busur', 'Gelas ukur/tabung kosong',
                ],
                'bahan' => [
                    'Beras 1 kg', 'Gabah 1 kg', 'Kedelai 1 kg', 'Kacang tanah 1 kg', 'Kacang merah 1 kg', 'Kacang hijau 1 kg',
                ],
                'prosedur' => [
                    'Ukur panjang (dmayor), lebar (dmoderat), dan tebal (dminor) untuk masing-masing bahan menggunakan vernier caliper, sebanyak 10 butir sampel per bahan.',
                    'Tentukan jenis dan subjenis bahan (untuk gabah dan beras) berdasarkan ukuran panjang dan rasio diameternya sesuai kriteria yang berlaku.',
                    'Timbang tabung kosong (W1) dan tabung kosong berisi bahan (W2), ukur volume tabung untuk menghitung bulk density (massa/volume).',
                    'Tuangkan sejumlah bahan di atas bidang datar dan ukur sudut kemiringan tumpukan bahan yang terbentuk sebagai angle of repose.',
                    'Letakkan 10 butir bahan di atas permukaan bidang datar (triplek/plat tipis), miringkan bidang secara perlahan, dan ukur sudut saat bahan mulai meluncur sebagai angle of friction.',
                    'Untuk beras, timbang 100 g sampel dan pisahkan menjadi butir utuh, patah, dan menir, kemudian hitung persentase masing-masing kelompok.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Apa yang dimaksud dengan angle of repose?',
                        'opsi' => ['Sudut kemiringan bidang saat bahan meluncur', 'Sudut yang terbentuk antara permukaan gundukan kerucut bahan granular dengan bidang horizontal', 'Sudut antara dua butir bahan', 'Sudut pada alat ukur jangka sorong'],
                        'jawaban' => 1,
                        'penjelasan' => 'Angle of repose adalah sudut antara permukaan gundukan berbentuk kerucut yang terbentuk saat bahan granular dituang, terhadap bidang horizontal.',
                    ],
                    [
                        'pertanyaan' => 'Bagaimana cara menentukan bulk density suatu bahan?',
                        'opsi' => ['Massa dibagi volume tabung', 'Massa dikalikan volume', 'Volume dibagi massa', 'Panjang dikalikan lebar'],
                        'jawaban' => 0,
                        'penjelasan' => 'Bulk density dihitung sebagai perbandingan antara massa bahan (g) dengan volume tabung (cm³).',
                    ],
                    [
                        'pertanyaan' => 'Berdasarkan SNI Beras, apa sebutan untuk butir beras patah yang berukuran lebih kecil atau sama dengan 2/10 bagian butir utuh?',
                        'opsi' => ['Beras kepala', 'Butir menir', 'Butir utuh', 'Butir sangat panjang'],
                        'jawaban' => 1,
                        'penjelasan' => 'Butir menir adalah butir beras patah dengan ukuran lebih kecil atau sama dengan 2/10 bagian dari butir beras utuh.',
                    ],
                ],
            ],
            [
                'judul' => 'Sifat Fisik Buah dan Sayur',
                'tujuan' => [
                    'Menentukan Geometric Mean Diameter (GMD) (cm)',
                    'Menentukan sphericity',
                    'Mengukur bentuk suatu buah dengan menggunakan chartered standard',
                    'Menentukan density (g/cm³)',
                ],
                'pendahuluan' => 'Sifat fisik buah dan sayur seperti bentuk, ukuran, berat, dan volume berperan penting dalam pemutuan hasil pertanian serta kegiatan pascapanen seperti pengemasan dan pengangkutan. Bentuk buah dapat ditentukan melalui pengamatan penampang memanjang dan melintang, dibandingkan dengan bentuk acuan standar (chart standard) maupun kemiripannya dengan bentuk-bentuk geometri tertentu.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Sifat Fisik dan Kimia Bahan Pangan', 'isi' => 'Pada bahan pangan, uji sifat fisik biasanya meliputi kekerasan, warna, rasa, dan bau, sementara berat dan volume sering digunakan untuk pemutuan buah berdasarkan kuantitas.'],
                    ['judul' => 'Berat Jenis dan Kematangan Buah', 'isi' => 'Berat jenis produk pertanian dapat digunakan untuk menduga tingkat kematangan buah, sedangkan volume digunakan dalam perhitungan awal untuk menduga sifat fisik lain seperti massa jenis.'],
                    ['judul' => 'Bentuk Acuan Standar (Chart Standard)', 'isi' => 'Bentuk dan ukuran buah dapat ditentukan dengan membandingkan penampang memanjang dan melintangnya terhadap bentuk-bentuk acuan standar yang telah ditetapkan, seperti bundar, oblate, kerucut, atau bujur telur.'],
                    ['judul' => 'Kemiripan dengan Bentuk Geometri', 'isi' => 'Selain bentuk standar, buah dapat didekati dengan bentuk geometri tertentu seperti prolate spheroid (misalnya lemon), oblate spheroid (misalnya anggur), atau right circular cone/silinder (misalnya wortel dan mentimun).'],
                    ['judul' => 'Metode Penentuan Volume', 'isi' => 'Volume buah dapat ditentukan melalui pendekatan aproksimasi geometris, metode platform scale, atau secara eksperimental dengan mengukur kenaikan volume air pada gelas ukur maupun massa air yang tumpah akibat perpindahan volume.'],
                ],
                'alat' => [
                    'Timbangan digital/manual', 'Kain lap/tisu', 'Vernier caliper/jangka sorong', 'Gelas ukur', 'Wadah', 'Pisau', 'Benang',
                ],
                'bahan' => [
                    'Kentang berbagai bentuk 3 buah', 'Pir berbagai bentuk 3 buah', 'Apel hijau, merah, gucci 3 buah', 'Mentimun 3 buah', 'Air secukupnya',
                ],
                'prosedur' => [
                    'Ukur panjang (dmayor), lebar (dmoderat), dan tebal (dminor) untuk masing-masing buah/sayuran menggunakan vernier caliper, sebanyak 5 sampel per bahan.',
                    'Hitung Geometric Mean Diameter (GMD) dan sphericity untuk masing-masing sampel berdasarkan hasil pengukuran.',
                    'Tentukan volume buah dengan Cara 1 (melihat kenaikan volume pada gelas ukur) dan Cara 2 (mengukur massa air yang terbuang dari gelas ukur).',
                    'Hitung density masing-masing buah dengan membandingkan berat terhadap volume yang telah diperoleh.',
                    'Potong penampang memanjang dan melintang buah, lalu bandingkan bentuknya dengan chartered standard yang tersedia untuk menentukan klasifikasi bentuknya.',
                    'Catat seluruh hasil pengukuran dan perhitungan pada tabel evaluasi untuk setiap jenis buah/sayuran.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Apa yang dimaksud dengan chartered standard dalam menentukan bentuk buah?',
                        'opsi' => ['Alat ukur berat buah', 'Bentuk acuan standar untuk membandingkan bentuk penampang buah', 'Metode penyimpanan buah', 'Cara mengemas buah'],
                        'jawaban' => 1,
                        'penjelasan' => 'Chartered standard adalah bentuk-bentuk acuan yang telah baku untuk membandingkan bentuk penampang memanjang dan melintang suatu buah.',
                    ],
                    [
                        'pertanyaan' => 'Buah apa yang umum didekati dengan bentuk right circular cone atau silinder?',
                        'opsi' => ['Anggur', 'Lemon', 'Wortel dan mentimun', 'Apel'],
                        'jawaban' => 2,
                        'penjelasan' => 'Wortel dan mentimun umumnya didekati dengan bentuk right circular cone atau silinder karena bentuknya yang memanjang dan relatif seragam.',
                    ],
                    [
                        'pertanyaan' => 'Bagaimana cara menentukan volume buah dengan Cara 2 pada praktikum ini?',
                        'opsi' => ['Menimbang buah secara langsung', 'Mengukur massa air yang terbuang dari gelas ukur akibat pencelupan buah', 'Mengukur panjang buah saja', 'Menghitung luas permukaan buah'],
                        'jawaban' => 1,
                        'penjelasan' => 'Cara 2 menentukan volume buah dengan mengukur massa air yang terbuang dari gelas ukur akibat perpindahan volume saat buah dicelupkan.',
                    ],
                ],
            ],
            [
                'judul' => 'Sifat Rheologi Produk Pertanian',
                'tujuan' => [
                    'Menentukan hubungan antara gaya dan deformasi',
                    'Menentukan nilai poisson ratio dari produk pertanian',
                ],
                'pendahuluan' => 'Reologi adalah ilmu yang mempelajari deformasi dan aliran suatu bahan seiring waktu, penting untuk memahami sifat mekanis produk pertanian seperti sawo, mangga, dan tomat merah. Kelakuan suatu bahan ditentukan oleh tiga variabel utama yaitu tegangan, deformasi (regangan), dan waktu.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Definisi Reologi', 'isi' => 'Reologi adalah cabang ilmu fisik yang mempelajari deformasi dan aliran (flow) suatu material, termasuk gesekan antar bahan padat dan sifat alir material.'],
                    ['judul' => 'Analisis Sensori vs Pendekatan Fisik', 'isi' => 'Sifat mekanis produk pangan dapat diuji melalui analisis sensori (menggunakan indera manusia) maupun pendekatan fisik menggunakan instrumen yang hasilnya dinyatakan dalam satuan meter, kilogram, dan detik.'],
                    ['judul' => 'Tegangan, Regangan, dan Modulus Young', 'isi' => 'Modulus Young dihitung dari perbandingan tegangan (gaya per luas permukaan) terhadap regangan (perubahan panjang relatif terhadap panjang awal) suatu bahan yang diberi beban.'],
                    ['judul' => 'Pengaruh Suhu dan Waktu terhadap Sifat Mekanis', 'isi' => 'Modulus Young suatu produk pertanian dapat berubah seiring waktu penyimpanan dan dipengaruhi oleh suhu penyimpanan, misalnya suhu pendingin dibandingkan suhu ruangan.'],
                ],
                'alat' => [
                    'Calibration mass', 'Mistar', 'Papan', 'Pisau/cutter', 'Jangka sorong',
                ],
                'bahan' => [
                    'Sawo', 'Mangga', 'Tomat merah',
                ],
                'prosedur' => [
                    'Ukur tinggi dan diameter awal masing-masing produk (sawo, tomat merah, dan produk lain) tanpa beban, sebanyak 3 sampel per produk.',
                    'Tempatkan papan dan beban dengan massa tertentu (100, 200, dan 500 g) di atas produk yang diuji.',
                    'Ukur deformasi yang terjadi dengan mengukur perubahan diameter dan tinggi produk selama diberi beban, perhatikan skala pada mistar.',
                    'Tambahkan beban secara bertahap dan ulangi prosedur pengukuran deformasi untuk setiap tingkat beban.',
                    'Catat seluruh hasil pengamatan (beban, D0, D1, L0, L1) pada tabel untuk menghitung Modulus Young setiap sampel.',
                    'Simpan sebagian sampel pada suhu pendingin dan sebagian pada suhu ruangan selama tiga hari, kemudian amati perubahan yang terjadi pada produk.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Apa yang dipelajari dalam ilmu reologi?',
                        'opsi' => ['Warna dan aroma bahan', 'Deformasi dan aliran suatu material', 'Kadar air bahan', 'Kandungan gizi bahan'],
                        'jawaban' => 1,
                        'penjelasan' => 'Reologi didefinisikan sebagai ilmu yang mempelajari deformasi dan aliran (flow) suatu material.',
                    ],
                    [
                        'pertanyaan' => 'Bagaimana Modulus Young dihitung dalam praktikum ini?',
                        'opsi' => ['F/A dikali L0/ΔL', 'F dikali A', 'L0 dibagi ΔL saja', 'A dibagi F'],
                        'jawaban' => 0,
                        'penjelasan' => 'Modulus Young dihitung dengan rumus (F/A) x (L0/ΔL), yaitu tegangan dibagi regangan.',
                    ],
                    [
                        'pertanyaan' => 'Apa tujuan menyimpan sampel pada suhu pendingin dan suhu ruangan selama tiga hari pada praktikum ini?',
                        'opsi' => ['Untuk mengukur berat sampel', 'Untuk mengamati pengaruh suhu dan waktu terhadap Modulus Young/sifat mekanis produk', 'Untuk mengeringkan sampel', 'Tidak memiliki tujuan khusus'],
                        'jawaban' => 1,
                        'penjelasan' => 'Penyimpanan pada suhu berbeda selama beberapa hari bertujuan untuk mengamati pengaruh suhu dan waktu terhadap perubahan sifat mekanis (Modulus Young) produk.',
                    ],
                ],
            ],
            [
                'judul' => 'Sifat Aerodinamis Produk Pertanian',
                'tujuan' => [
                    'Melihat hubungan kecepatan fluida untuk pemisahan produk',
                ],
                'pendahuluan' => 'Sifat aerodinamis produk pertanian berkaitan dengan kecepatan terminal partikel saat dipisahkan menggunakan aliran udara, misalnya untuk memisahkan gabah, beras, dan biji bunga matahari dari sekam, dedak, atau benda asing lainnya. Pemahaman ini penting untuk merancang alat pembersih dan pemisah berbasis hembusan udara.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Densitas Partikel vs Densitas Fluida', 'isi' => 'Partikel dengan densitas lebih besar dari densitas fluida akan tenggelam ke dalam fluida, sedangkan partikel dengan densitas lebih kecil akan mengapung di permukaan fluida.'],
                    ['judul' => 'Kecepatan Terminal', 'isi' => 'Kecepatan terminal digunakan sebagai karakteristik aerodinamik penting dari suatu material dalam penerapannya sebagai alat pengangkutan dan pemisahan bahan asing dari produk yang diinginkan.'],
                    ['judul' => 'Prinsip Pemisahan dengan Hembusan Udara', 'isi' => 'Proses pemisahan biji-bijian umumnya menggunakan prinsip perbedaan berat antara biji-bijian dengan kotoran atau benda lain, menggunakan tenaga hembusan udara yang optimal sesuai kecepatan terminal biji-bijian tersebut.'],
                ],
                'alat' => [
                    'Blower', 'Anemometer', 'Tabung',
                ],
                'bahan' => [
                    'Gabah', 'Beras', 'Biji bunga matahari',
                ],
                'prosedur' => [
                    'Siapkan bahan dan alat, ambil sampel masing-masing bahan sekitar 100 gram.',
                    'Pasang botol air mineral yang telah dilubangi pada blower, kemudian hidupkan blower.',
                    'Masukkan bahan (gabah, beras, atau biji bunga matahari) ke dalam tabung yang telah terpasang pada blower.',
                    'Lakukan pengamatan pada tiga kondisi bukaan blower: tertutup, terbuka setengah, dan terbuka penuh.',
                    'Ukur kecepatan fluida (udara) menggunakan anemometer pada setiap kondisi pengamatan.',
                    'Timbang bagian bahan yang terbuang (terhembus keluar) dan bagian yang tertinggal di dalam tabung untuk setiap kondisi bukaan blower.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Apa yang terjadi jika densitas partikel lebih besar dari densitas fluida?',
                        'opsi' => ['Partikel akan mengapung', 'Partikel akan tenggelam/bergerak ke bawah', 'Partikel akan melayang diam', 'Partikel akan menguap'],
                        'jawaban' => 1,
                        'penjelasan' => 'Bila densitas partikel lebih besar dari densitas fluida, partikel akan bergerak ke bawah atau tenggelam ke dalam fluida.',
                    ],
                    [
                        'pertanyaan' => 'Alat apa yang digunakan untuk mengukur kecepatan aliran udara pada praktikum ini?',
                        'opsi' => ['Termometer', 'Anemometer', 'Jangka sorong', 'Timbangan'],
                        'jawaban' => 1,
                        'penjelasan' => 'Anemometer digunakan untuk mengukur kecepatan fluida (udara) pada setiap kondisi pengamatan.',
                    ],
                    [
                        'pertanyaan' => 'Mengapa pengetahuan tentang kecepatan terminal penting dalam proses pemisahan biji-bijian dengan hembusan udara?',
                        'opsi' => ['Untuk menentukan warna biji-bijian', 'Agar hembusan udara yang digunakan optimal untuk memisahkan biji-bijian dari benda asing tanpa ikut terbuang', 'Untuk mempercepat proses pengeringan', 'Tidak ada kaitannya dengan proses pemisahan'],
                        'jawaban' => 1,
                        'penjelasan' => 'Pembersihan dengan hembusan udara akan optimal apabila kecepatan udara yang digunakan sesuai dengan kecepatan terminal biji-bijian, sehingga biji-bijian tidak ikut terbuang bersama kotoran.',
                    ],
                ],
            ],
            [
                'judul' => 'Sifat Hidrodinamis Produk Pertanian',
                'tujuan' => [
                    'Melihat pengaruh fluida (air) untuk pemisahan produk',
                    'Menentukan nilai rendemen produk',
                ],
                'pendahuluan' => 'Fluida seperti air sering digunakan sebagai medium dalam penanganan hasil pertanian, termasuk untuk memisahkan biji-bijian yang bermutu baik dari yang tidak layak (misalnya biji yang belum masak atau berkualitas rendah) berdasarkan perbedaan densitas atau spesifik gravity, seperti pada kacang hijau dan kedelai.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Fluida sebagai Medium Penanganan Hasil Pertanian', 'isi' => 'Fluida (air dan udara) sering digunakan sebagai medium dalam transportasi, pemisahan, dan pengeringan hasil pertanian, dengan prinsip kerja yang serupa dengan pemisahan menggunakan udara.'],
                    ['judul' => 'Dry Cleaning vs Wet Cleaning', 'isi' => 'Dry cleaning menghilangkan partikel tidak dikehendaki menggunakan aliran udara dan dipengaruhi kadar air bahan, sedangkan wet cleaning melarutkan kontaminan seperti tanah dan pestisida menggunakan air.'],
                    ['judul' => 'Pemisahan Berdasarkan Spesifik Gravity', 'isi' => 'Pemisahan berdasarkan spesifik gravity umumnya digunakan untuk memisahkan biji yang sudah masak (lebih berat, tenggelam) dari biji yang belum masak (lebih ringan, mengapung) dengan cara direndam dalam larutan tertentu.'],
                    ['judul' => 'Perhitungan Rendemen', 'isi' => 'Rendemen produk hasil pemisahan dihitung dari perbandingan antara berat produk yang layak setelah pemisahan (m2) terhadap berat awal bahan (m1), dikalikan 100%.'],
                ],
                'alat' => [
                    'Wadah plastik', 'Nampan', 'Timbangan digital', 'Saringan santan',
                ],
                'bahan' => [
                    'Kacang hijau 1000 g', 'Kedelai 1000 g', 'Air secukupnya',
                ],
                'prosedur' => [
                    'Siapkan bahan dan alat, ambil sampel kacang hijau dan kedelai masing-masing 1000 gram (dan variasi 200 g serta 500 g untuk evaluasi bertahap).',
                    'Masukkan air secukupnya ke dalam wadah plastik, kemudian masukkan bahan yang akan diuji ke dalam wadah tersebut.',
                    'Lakukan pengamatan setelah produk terpisah antara bagian yang tenggelam dan bagian yang terapung di permukaan air.',
                    'Angkat dan tiriskan produk yang terapung di atas air menggunakan saringan santan, kemudian timbang beratnya sebagai produk yang tidak layak.',
                    'Hitung persentase rendemen produk hasil pemisahan menggunakan rumus Rendemen = (m2/m1) x 100%, dengan m2 = berat awal dikurangi berat produk tidak layak.',
                    'Timbang kacang hijau dan kedelai masing-masing 200 gram, masukkan ke dalam botol bening berisi air, dan amati komoditas mana yang berada di posisi atas untuk membandingkan densitas relatifnya.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Apa perbedaan utama antara dry cleaning dan wet cleaning?',
                        'opsi' => ['Dry cleaning menggunakan air, wet cleaning menggunakan udara', 'Dry cleaning menggunakan aliran udara untuk memisahkan partikel, wet cleaning menggunakan air untuk melarutkan kontaminan', 'Keduanya menggunakan bahan kimia', 'Tidak ada perbedaan'],
                        'jawaban' => 1,
                        'penjelasan' => 'Dry cleaning menggunakan aliran udara untuk memisahkan partikel tidak dikehendaki, sedangkan wet cleaning menggunakan air untuk melarutkan bahan kontaminan yang menempel pada bahan.',
                    ],
                    [
                        'pertanyaan' => 'Bagaimana rumus untuk menghitung rendemen produk hasil pemisahan?',
                        'opsi' => ['Rendemen = (m1/m2) x 100%', 'Rendemen = (m2/m1) x 100%', 'Rendemen = m1 x m2', 'Rendemen = m1 - m2'],
                        'jawaban' => 1,
                        'penjelasan' => 'Rendemen dihitung dengan rumus (m2/m1) x 100%, dimana m1 adalah berat awal dan m2 adalah berat awal dikurangi berat produk yang tidak layak.',
                    ],
                    [
                        'pertanyaan' => 'Mengapa biji yang belum masak (muda) cenderung mengapung saat direndam dalam air dibandingkan biji yang sudah masak (tua)?',
                        'opsi' => ['Karena warnanya berbeda', 'Karena biji muda memiliki densitas lebih rendah dibanding biji tua yang lebih berat', 'Karena ukurannya lebih besar', 'Karena bentuknya berbeda'],
                        'jawaban' => 1,
                        'penjelasan' => 'Biji yang sudah masak umumnya lebih berat/padat sehingga tenggelam, sedangkan biji yang belum masak memiliki densitas lebih rendah sehingga cenderung mengapung.',
                    ],
                ],
            ],
        ];
    }
}
