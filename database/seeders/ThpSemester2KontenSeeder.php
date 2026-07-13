<?php

namespace Database\Seeders;

use App\Models\Matakuliah;
use App\Models\Materi;
use App\Models\Praktikum;
use App\Models\Prodi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Mengisi konten untuk mata kuliah Praktikum MIPA
 * (Semester 2, prodi THP), mengikuti pola TepSemester2KontenSeeder.
 *
 * Sumber materi: Pedoman Praktikum MIPA 2023, PS Teknologi Hasil
 * Pertanian, Fakultas Pertanian, Universitas Jambi.
 *
 * Pemisahan konten:
 * - Materi    = teori saja (Pendahuluan + Tinjauan Pustaka).
 * - Praktikum = pelaksanaan (Tujuan, Alat, Bahan, Prosedur/Langkah Kerja, Kuis).
 *
 * PENTING: sesuaikan $matakuliahSlug di method run() dengan slug asli
 * yang sudah tersimpan di tabel matakuliahs untuk mata kuliah ini
 * (di sini saya menebak 'thp-s2-praktikum-mipa' mengikuti pola
 * 'tep-s2-alsintan' dst). Jika mata kuliah belum ada di database,
 * buat dulu recordnya sebelum menjalankan seeder ini.
 */
class ThpSemester2KontenSeeder extends Seeder
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
        $prodi = Prodi::where('kode', 'THP')->first();

        if (! $prodi) {
            $this->command?->warn('Prodi THP tidak ditemukan, seeder dilewati.');
            return;
        }

        $this->seedMatakuliah($prodi, 'thp-s2-praktikum-mipa', $this->praktikumMipa());
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
                    'pokok_bahasan' => array_column($objek['tinjauan_pustaka'], 'judul'),
                    'capaian' => $objek['tujuan'],
                    'pendahuluan' => $objek['pendahuluan'],
                    'tinjauan_pustaka' => $objek['tinjauan_pustaka'],
                ]
            );

            Praktikum::updateOrCreate(
                ['matakuliah_id' => $matakuliah->id, 'slug' => $praktikumSlug],
                [
                    'prodi_id' => $prodi->id,
                    'kode' => 'MIPA-P'.str_pad((string) $nomor, 2, '0', STR_PAD_LEFT),
                    'judul' => 'Objek '.$nomor.': '.$objek['judul'],
                    'tingkat' => 'Dasar',
                    'durasi' => '2 x 50 menit',
                    'tujuan' => implode(' ', $objek['tujuan']),
                    'alat' => $objek['alat'],
                    'bahan' => $objek['bahan'],
                    'langkah' => $objek['prosedur'],
                    'kuis' => $objek['kuis'],
                ]
            );
        }
    }

    protected function praktikumMipa(): array
    {
        return [
            // Objek 1 - Bab 2: Good Laboratory Practice (GLP)
            [
                'judul' => 'Good Laboratory Practice (GLP)',
                'tujuan' => [
                    'Memiliki pengetahuan tentang definisi dan cakupan Good Laboratory Practices',
                    'Memiliki pengetahuan tentang peraturan-peraturan yang berlaku di laboratorium',
                    'Mampu menerapkan cara bekerja yang aman di laboratorium',
                ],
                'pendahuluan' => 'Good Laboratory Practice (GLP) adalah kumpulan aturan, prosedur, dan praktik kerja di laboratorium yang menjamin mutu dan keandalan data analitik. Penerapan GLP membantu mencegah kesalahan fatal, kecelakaan kerja, dan kekeliruan data sehingga hasil analisis dapat dipertanggungjawabkan secara ilmiah maupun hukum.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Sejarah dan Definisi GLP', 'isi' => 'GLP pertama kali dikemukakan melalui New Zealand Testing Laboratory Registration Act tahun 1972 dan kemudian diadopsi berbagai negara termasuk melalui peraturan FDA Amerika Serikat pada tahun 1976-1978. GLP mencakup pengorganisasian laboratorium, fasilitas, tenaga kerja, dan kondisi kerja agar pengujian dapat dilaksanakan, dimonitor, dicatat, dan dilaporkan dengan baik.'],
                    ['judul' => 'Alat Proteksi Diri dan Peraturan Keamanan Umum', 'isi' => 'Jas lab dan alas kaki tertutup wajib digunakan setiap bekerja di laboratorium, sedangkan masker, kacamata, dan sarung tangan digunakan sesuai kebutuhan. Peraturan umum mencakup larangan makan/minum/merokok di laboratorium, larangan bekerja sendirian, serta kewajiban mengetahui lokasi alat pengaman.'],
                    ['judul' => 'Prosedur Standar Operasi dan Penanganan Kecelakaan', 'isi' => 'Setiap laboratorium menyusun SOP (Standard Operating Procedures) yang unik sesuai kondisinya untuk menjamin keabsahan data. Prosedur penanganan kecelakaan mencakup pembilasan dengan air mengalir bila kulit/mata terkena bahan kimia, serta penggunaan alat pemadam kebakaran bila terjadi kebakaran kecil.'],
                ],
                'alat' => [
                    'Alat pelindung diri (jas lab, sarung tangan, masker, kacamata)',
                    'Buku katalog/Material Safety Data Sheet (MSDS) bahan kimia',
                    'Lembar kerja identifikasi simbol bahaya',
                ],
                'bahan' => [
                    'Daftar bahan kimia yang digunakan pada topik-topik praktikum MIPA',
                ],
                'prosedur' => [
                    'Buat daftar seluruh bahan kimia yang akan digunakan pada semua topik praktikum semester ini.',
                    'Cari dan kumpulkan MSDS (Material Safety Data Sheet) untuk setiap bahan kimia pada daftar tersebut.',
                    'Rangkum simbol bahaya pada label setiap bahan kimia dan jelaskan maknanya berdasarkan MSDS yang didapat.',
                    'Dapatkan minimal satu salinan SOP laboratorium tempat praktikum dilaksanakan dan pelajari isinya.',
                    'Diskusikan dengan kelompok mengenai prosedur keadaan darurat yang berlaku di laboratorium tersebut.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Apa tujuan utama penerapan Good Laboratory Practice (GLP)?',
                        'opsi' => ['Mempercepat waktu praktikum', 'Menjamin mutu dan keandalan data serta keselamatan kerja', 'Mengurangi jumlah alat yang digunakan', 'Menghemat biaya bahan kimia'],
                        'jawaban' => 1,
                        'penjelasan' => 'GLP dirancang untuk menjamin mutu dan intensitas data analitik serta keselamatan kerja di laboratorium.',
                    ],
                    [
                        'pertanyaan' => 'Manakah alat proteksi diri yang wajib digunakan setiap saat bekerja di laboratorium?',
                        'opsi' => ['Masker saja', 'Sarung tangan saja', 'Jas lab dan alas kaki tertutup', 'Kacamata saja'],
                        'jawaban' => 2,
                        'penjelasan' => 'Jas lab dan alas kaki tertutup harus selalu digunakan, sedangkan masker, kacamata, dan sarung tangan digunakan sesuai keperluan.',
                    ],
                    [
                        'pertanyaan' => 'Dokumen apa yang memuat informasi keamanan dan pencegahan suatu bahan kimia?',
                        'opsi' => ['SOP', 'MSDS', 'Format Laporan', 'Kurva Standar'],
                        'jawaban' => 1,
                        'penjelasan' => 'MSDS (Material Safety Data Sheet) memuat informasi keamanan dan pencegahan terkait suatu bahan kimia.',
                    ],
                ],
            ],
            // Objek 2 - Bab 3: Peralatan Laboratorium
            [
                'judul' => 'Peralatan Laboratorium',
                'tujuan' => [
                    'Memiliki pengetahuan tentang jenis-jenis peralatan yang umum digunakan di laboratorium beserta fungsinya',
                    'Terampil menggunakan peralatan yang umum digunakan di laboratorium dengan benar',
                    'Mampu membedakan tingkat ketelitian berbagai alat ukur volume',
                ],
                'pendahuluan' => 'Peralatan laboratorium dapat dikelompokkan berdasarkan bahan pembuat atau fungsinya, yaitu sebagai alat ukur, wadah, tempat reaksi, atau alat bantu. Mengenal jenis dan fungsi peralatan merupakan langkah awal penting agar mahasiswa dapat bekerja di laboratorium dengan benar dan menerapkan GLP secara konsisten.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Klasifikasi Alat Berdasarkan Fungsi', 'isi' => 'Alat laboratorium dapat dibagi menjadi alat ukur (volume dan berat), alat wadah, alat tempat reaksi, dan alat bantu seperti botol semprot, penjepit, pinset, dan bola hisap.'],
                    ['judul' => 'Alat Ukur Volume', 'isi' => 'Gelas ukur bersifat kasar dan tidak cocok untuk titrasi, sedangkan pipet volumetri dan buret memiliki ketelitian lebih tinggi. Labu ukur digunakan untuk membuat larutan hingga volume tertentu yang tepat, sementara pipet mikro dapat mengukur hingga skala 0,001 ml.'],
                    ['judul' => 'Instrumen Laboratorium Modern', 'isi' => 'Perkembangan teknologi menghadirkan instrumen berbasis digital dan terkontrol komputer seperti Gas Chromatography (GC), Infra Red Spectrometer (IR), dan UV-Vis Spectrometer yang melengkapi alat-alat gelas konvensional.'],
                ],
                'alat' => [
                    'Gelas ukur, pipet volumetri, pipet ukur, buret, labu ukur',
                    'Tabung reaksi, gelas beaker, labu erlenmeyer',
                    'Bola hisap (suction bulb), penjepit tabung, statif dan klem',
                ],
                'bahan' => [
                    'NaCl, NaOH, air destilata',
                ],
                'prosedur' => [
                    'Pilih alat gelas yang sesuai untuk menimbang, melarutkan, menitrasi, menyaring, dan memindahkan cairan dalam berbagai volume sesuai kebutuhan percobaan.',
                    'Praktikkan cara memanaskan cairan di dalam tabung reaksi menggunakan penjepit dengan posisi mulut tabung diarahkan menjauhi wajah.',
                    'Praktikkan cara membersihkan dan mengeringkan buret dan labu erlenmeyer dengan benar.',
                    'Latih penggunaan bola hisap untuk mengambil dan memindahkan cairan menggunakan pipet ukur secara tepat.',
                    'Bandingkan ketelitian pengukuran volume pada gelas beaker, erlenmeyer, labu ukur, dan gelas ukur menggunakan pipet volumetri dan pipet ukur, ulangi sebanyak 3 kali.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Mengapa gelas ukur tidak digunakan untuk kegiatan titrasi yang memerlukan ketelitian tinggi?',
                        'opsi' => ['Karena mudah pecah', 'Karena merupakan alat pengukur yang kasar', 'Karena tidak transparan', 'Karena tidak tahan panas'],
                        'jawaban' => 1,
                        'penjelasan' => 'Gelas ukur merupakan alat pengukur yang kasar sehingga tidak digunakan untuk mengukur cairan dengan teliti seperti kegiatan titrasi.',
                    ],
                    [
                        'pertanyaan' => 'Alat apa yang digunakan untuk mengalirkan sejumlah zat cair dengan volume tertentu menggunakan kran, dan sering dipakai dalam titrasi?',
                        'opsi' => ['Gelas ukur', 'Labu ukur', 'Buret', 'Corong pisah'],
                        'jawaban' => 2,
                        'penjelasan' => 'Buret digunakan untuk mengalirkan sejumlah zat cair dengan volume tertentu menggunakan kran, biasa digunakan pada percobaan titrasi.',
                    ],
                    [
                        'pertanyaan' => 'Apa fungsi bola hisap (suction bulb) saat bekerja dengan pipet volumetri?',
                        'opsi' => ['Memanaskan cairan', 'Membantu menghisap dan mengeluarkan cairan dari pipet', 'Menyaring larutan', 'Mengukur suhu larutan'],
                        'jawaban' => 1,
                        'penjelasan' => 'Bola hisap membantu menghisap cairan masuk ke dalam pipet dan mengeluarkannya kembali secara terkendali.',
                    ],
                ],
            ],
            // Objek 3 - Bab 4: Pengukuran Standar
            [
                'judul' => 'Pengukuran Standar',
                'tujuan' => [
                    'Memahami prinsip kerja berbagai jenis neraca massa dan mampu mengoperasikannya dengan benar',
                    'Menguasai prinsip pengukuran menggunakan jangka sorong dan mikrometer sekrup dan mampu mengoperasikannya dengan benar',
                    'Mampu mengoperasikan termometer dan mengukur tekanan udara maupun tekanan air dengan benar',
                ],
                'pendahuluan' => 'Mengukur adalah membandingkan sesuatu yang diukur dengan sesuatu lain yang sejenis dan ditetapkan sebagai satuan. Untuk menyeragamkan satuan digunakan Satuan Sistem Internasional (SI) yang terdiri dari besaran pokok dan besaran turunan. Pengukuran standar meliputi pengukuran massa, panjang, suhu, dan tekanan menggunakan alat yang sesuai.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Besaran Pokok dan Besaran Turunan', 'isi' => 'Terdapat 7 besaran pokok dengan satuan SI yaitu panjang (meter), massa (kilogram), waktu (sekon), kuat arus listrik (ampere), suhu (kelvin), jumlah zat (mol), dan intensitas cahaya (kandela). Besaran turunan seperti luas dan volume diturunkan dari besaran pokok tersebut.'],
                    ['judul' => 'Alat Ukur Massa dan Panjang', 'isi' => 'Massa dapat diukur menggunakan neraca O\'Hauss atau timbangan digital. Panjang dapat diukur menggunakan mistar (ketelitian 0,5 mm), jangka sorong (ketelitian 0,1 mm), atau mikrometer sekrup (ketelitian 0,01 mm) tergantung tingkat presisi yang dibutuhkan.'],
                    ['judul' => 'Pengukuran Suhu dan Tekanan', 'isi' => 'Suhu diukur menggunakan termometer zat cair (raksa/alkohol) atau termometer digital. Tekanan udara diukur dengan barometer, sedangkan tekanan pada ruang tertutup diukur dengan manometer; tekanan hidrostatis air dihitung dengan rumus P = ρ x g x h.'],
                ],
                'alat' => [
                    'Neraca O\'Hauss 3 lengan, timbangan digital',
                    'Jangka sorong, mikrometer sekrup, penggaris',
                    'Termometer, barometer',
                ],
                'bahan' => [
                    'Beberapa benda uji dengan ukuran dan massa berbeda',
                    'Air',
                ],
                'prosedur' => [
                    'Ukur massa beberapa benda menggunakan neraca O\'Hauss dan timbangan digital, masing-masing dengan 2-3 kali ulangan.',
                    'Ukur panjang, tebal, atau diameter benda menggunakan penggaris, jangka sorong, dan mikrometer sekrup, catat hasil dengan angka penting yang sesuai tingkat ketelitian alat.',
                    'Ukur suhu beberapa cairan menggunakan termometer, konversikan hasil pengukuran ke satuan Fahrenheit dan Kelvin.',
                    'Ukur tekanan udara di dalam dan di luar ruangan menggunakan barometer, konversikan ke beberapa satuan tekanan.',
                    'Lakukan percobaan tekanan air dengan melubangi botol pada beberapa ketinggian berbeda dan hitung tekanan hidrostatis menggunakan rumus yang berlaku.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Berapa tingkat ketelitian pengukuran menggunakan mikrometer sekrup?',
                        'opsi' => ['0,5 mm', '0,1 mm', '0,01 mm', '1 mm'],
                        'jawaban' => 2,
                        'penjelasan' => 'Mikrometer sekrup memiliki tingkat ketelitian 0,01 mm, lebih presisi dibandingkan jangka sorong (0,1 mm) atau mistar (0,5 mm).',
                    ],
                    [
                        'pertanyaan' => 'Alat apa yang digunakan untuk mengukur tekanan udara terbuka?',
                        'opsi' => ['Manometer', 'Barometer', 'Termometer', 'Jangka sorong'],
                        'jawaban' => 1,
                        'penjelasan' => 'Barometer digunakan untuk mengukur tekanan pada udara terbuka, sedangkan manometer untuk tekanan pada tempat tertutup.',
                    ],
                    [
                        'pertanyaan' => 'Manakah yang termasuk besaran pokok dalam Satuan Sistem Internasional?',
                        'opsi' => ['Luas', 'Volume', 'Massa', 'Kecepatan'],
                        'jawaban' => 2,
                        'penjelasan' => 'Massa (kilogram) merupakan salah satu dari 7 besaran pokok SI, sedangkan luas, volume, dan kecepatan adalah besaran turunan.',
                    ],
                ],
            ],
            // Objek 4 - Bab 5: Teknik Dasar Bekerja di Laboratorium
            [
                'judul' => 'Teknik Dasar Bekerja di Laboratorium',
                'tujuan' => [
                    'Memiliki keterampilan membersihkan peralatan, membaca meniskus, dan melakukan penimbangan dengan benar',
                    'Memiliki keterampilan menggunakan pipet, buret, dan tabung reaksi serta membuat larutan dengan benar',
                    'Memiliki keterampilan menyaring, menggunakan kertas indikator pH, serta melakukan pemijaran dan pengabuan dengan benar',
                ],
                'pendahuluan' => 'Kebersihan meja praktikum serta penataan alat dan zat kimia yang rapi merupakan teknik dasar yang menunjang keberhasilan praktikum. Teknik dasar bekerja di laboratorium meliputi mencuci alat gelas, mengukur volume, menimbang, menyaring, dan melakukan reaksi pencampuran atau pelarutan dengan benar.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Teknik Mencuci dan Mengeringkan Alat Gelas', 'isi' => 'Alat gelas dicuci dengan sabun/detergen, atau larutan pencuci oksidator kuat bila diperlukan. Alat ukur volume tidak boleh dikeringkan pada suhu tinggi karena dapat memuai dan mengubah keakuratan skalanya; pengeringan dilakukan pada suhu 20-50°C.'],
                    ['judul' => 'Membaca Meniskus dan Menimbang', 'isi' => 'Pembacaan volume pada alat ukur dilakukan pada batas bawah lengkung permukaan cairan (meniskus) secara sejajar mata (paralaks). Penimbangan harus dilakukan pada bidang datar dengan wadah yang sesuai, tidak langsung dengan tangan, dan tidak melebihi kapasitas timbangan.'],
                    ['judul' => 'Memipet, Titrasi, Menyaring, dan Pemijaran', 'isi' => 'Pengambilan cairan menggunakan propipet dengan ujung pipet tercelup penuh; penyaringan menggunakan kertas saring berbentuk kerucut pada corong; pemijaran menggunakan api bunsen sedangkan pengabuan menggunakan furnace bersuhu hingga 1000°C.'],
                ],
                'alat' => [
                    'Buret, statif, klem, corong, kertas saring, kertas indikator pH (lakmus)',
                    'Tabung reaksi, gelas piala, labu volumetrik, batang pengaduk',
                    'Bunsen, furnace, oven, desikator',
                ],
                'bahan' => [
                    'NaOH, larutan standar asam',
                    'Sampel bahan pangan',
                    'Aquadest',
                ],
                'prosedur' => [
                    'Buat diagram alir pembuatan 500 ml larutan NaOH 1M, kemudian buat larutannya dengan memperhatikan cara menimbang, membaca meniskus, dan melarutkan yang benar.',
                    'Set buret pada statif, isi dengan larutan NaOH yang telah dibuat.',
                    'Titrasi larutan standar asam menggunakan NaOH hingga titik akhir titrasi, perhatikan cara mengeluarkan cairan dan membaca meniskus pada buret.',
                    'Masukkan 1 gram sampel bahan pangan ke dalam tabung reaksi, tambahkan 5 ml larutan NaOH 1M, aduk dan ukur pH menggunakan kertas indikator.',
                    'Panaskan sampel di atas api bunsen selama 1 menit, dinginkan, saring dengan kertas saring yang telah diketahui beratnya.',
                    'Keringkan kertas saring dalam oven suhu 105°C hingga berat konstan, lalu lakukan pengabuan pada kertas saring sampel dan kertas saring kontrol, hitung selisih beratnya.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Bagaimana cara membaca meniskus yang benar pada alat ukur volume?',
                        'opsi' => ['Dilihat dari atas permukaan cairan', 'Dilihat sejajar pada bagian bawah lengkung cairan', 'Dilihat dari samping alat', 'Diperkirakan tanpa melihat langsung'],
                        'jawaban' => 1,
                        'penjelasan' => 'Pembacaan meniskus dilakukan pada bagian bawah permukaan lengkung cairan dan harus dilihat tepat segaris di mukanya (paralaks).',
                    ],
                    [
                        'pertanyaan' => 'Mengapa alat gelas yang berfungsi sebagai alat ukur tidak boleh dikeringkan pada suhu tinggi?',
                        'opsi' => ['Akan berubah warna', 'Dapat memuai sehingga skala volumenya tidak akurat', 'Akan menjadi keruh', 'Tidak akan kering sempurna'],
                        'jawaban' => 1,
                        'penjelasan' => 'Pemanasan suhu tinggi dapat memuaikan alat gelas ukur sehingga skala yang tertera tidak lagi menunjukkan volume yang sebenarnya.',
                    ],
                    [
                        'pertanyaan' => 'Apa perbedaan utama antara teknik pemijaran dan pengabuan?',
                        'opsi' => ['Pemijaran menggunakan api bunsen, pengabuan menggunakan panas kumparan listrik pada furnace', 'Keduanya menggunakan alat yang sama', 'Pemijaran hanya untuk cairan', 'Pengabuan tidak memerlukan suhu tinggi'],
                        'jawaban' => 0,
                        'penjelasan' => 'Pemijaran menggunakan sumber panas dari api bunsen, sedangkan pengabuan menggunakan suhu tinggi dari panas kumparan listrik pada alat furnace.',
                    ],
                ],
            ],
            // Objek 5 - Bab 6: Sterilisasi dan Desinfeksi
            [
                'judul' => 'Sterilisasi dan Desinfeksi',
                'tujuan' => [
                    'Dapat menjelaskan perbedaan sterilisasi dan disinfeksi',
                    'Dapat membedakan jenis sterilisasi atau disinfeksi yang cocok untuk bahan/alat tertentu',
                    'Dapat melakukan proses sterilisasi dan disinfeksi di laboratorium dengan benar',
                ],
                'pendahuluan' => 'Sterilisasi adalah proses membunuh semua mikroorganisme hidup pada alat, instrumen, kultur media, atau reagen, sedangkan disinfeksi mengeliminasi sebagian atau seluruh mikroorganisme patogen tanpa membunuh spora bakteri. Setiap alat atau bahan memerlukan penanganan khusus dan prosedur yang telah divalidasi agar sterilitas terjamin.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Perbedaan Sterilisasi dan Disinfeksi', 'isi' => 'Sterilisasi membunuh seluruh mikroorganisme termasuk spora, sedangkan disinfeksi biasanya dilakukan pada permukaan alat/tempat kerja dan tidak membunuh spora bakteri.'],
                    ['judul' => 'Metode Sterilisasi Kering dan Basah', 'isi' => 'Sterilisasi kering menggunakan pijaran api atau oven pengering suhu 160-180°C dengan waktu lebih lama, cocok untuk alat gelas. Sterilisasi basah menggunakan uap air seperti perebusan, pengukusan, atau autoclave pada suhu 121°C selama 15 menit yang dapat membunuh spora bakteri.'],
                    ['judul' => 'Sterilisasi Radiasi dan Bioindikator', 'isi' => 'Sinar UV pada panjang gelombang sekitar 254-260 nm digunakan untuk sterilisasi ruang kerja aseptis. Validasi sterilisasi panas menggunakan bioindikator seperti Bacillus stearothermophillus (sterilisasi basah) atau Bacillus subtilis (sterilisasi kering).'],
                ],
                'alat' => [
                    'Oven pengering, autoclave',
                    'Aluminium foil tebal, tray',
                ],
                'bahan' => [
                    'Beaker glass, erlenmeyer, tabung reaksi beserta rak, pipet volumetrik, petri dish',
                ],
                'prosedur' => [
                    'Siapkan oven pengering dan atur suhu pada 180°C.',
                    'Bungkus mulut beaker glass dan erlenmeyer dengan aluminium foil tebal.',
                    'Tempatkan pipet volumetrik pada wadah tertutup atau pouch aluminium foil dengan ujung pipet menghadap ke bawah.',
                    'Tutup tabung reaksi dan bungkus setiap petri dish satu per satu menggunakan aluminium foil tebal.',
                    'Tempatkan seluruh alat di atas tray di dalam oven dan panaskan selama 30 menit sejak titik terdingin alat mencapai suhu 180°C.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Apa perbedaan mendasar antara sterilisasi dan disinfeksi?',
                        'opsi' => ['Sterilisasi hanya untuk cairan', 'Sterilisasi membunuh semua mikroorganisme termasuk spora, disinfeksi tidak', 'Disinfeksi lebih mahal', 'Tidak ada perbedaan'],
                        'jawaban' => 1,
                        'penjelasan' => 'Sterilisasi bertujuan membunuh semua mikroorganisme yang hidup termasuk spora, sedangkan disinfeksi mengeliminasi sebagian mikroorganisme patogen tanpa membunuh spora bakteri.',
                    ],
                    [
                        'pertanyaan' => 'Pada suhu dan waktu berapa autoclave umumnya direkomendasikan untuk sterilisasi basah?',
                        'opsi' => ['100°C selama 5 menit', '121°C selama 15 menit', '160°C selama 180 menit', '60°C selama 60 menit'],
                        'jawaban' => 1,
                        'penjelasan' => 'Rekomendasi umum sterilisasi menggunakan autoclave adalah 15 menit pada suhu 121-124°C, 2 atm.',
                    ],
                    [
                        'pertanyaan' => 'Jenis sterilisasi apa yang lebih cocok untuk bahan seperti tepung atau minyak yang tidak boleh dipenetrasi panas basah?',
                        'opsi' => ['Sterilisasi kering menggunakan oven', 'Sterilisasi menggunakan autoclave', 'Perebusan', 'Pengukusan'],
                        'jawaban' => 0,
                        'penjelasan' => 'Sterilisasi kering lebih cocok untuk bahan yang tidak boleh dipenetrasi panas basah seperti tepung, minyak, dan produk berbasis minyak.',
                    ],
                ],
            ],
            // Objek 6 - Bab 7: Sel Makhluk Hidup
            [
                'judul' => 'Sel Makhluk Hidup dan Penggunaan Mikroskop',
                'tujuan' => [
                    'Mampu mengoperasikan mikroskop dan mempersiapkan bahan pengamatan dengan benar',
                    'Dapat menjelaskan karakteristik sel hewan dan sel tanaman yang diamati di bawah mikroskop',
                    'Dapat menjelaskan karakteristik sel bakteri, kapang, dan khamir yang diamati di bawah mikroskop',
                ],
                'pendahuluan' => 'Sel merupakan organel terkecil penyusun makhluk hidup, baik yang bersel tunggal (uniselular) maupun bersel banyak (multiselular). Keadaan sel dapat diamati menggunakan mikroskop, alat bantu untuk melihat obyek berukuran mikroskopis sehingga bayangannya tampak lebih besar dan jelas.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Jenis dan Bagian-bagian Mikroskop', 'isi' => 'Mikroskop cahaya menggunakan sinar tampak (400-700 nm) dengan perbesaran hingga 1000x, sedangkan mikroskop elektron menggunakan berkas elektron dengan resolusi jauh lebih tinggi. Bagian utama mikroskop cahaya meliputi lensa okuler, lensa objektif, kondensor, diafragma, makrometer, dan mikrometer.'],
                    ['judul' => 'Cara Menggunakan Mikroskop dan Menyiapkan Preparat', 'isi' => 'Preparat basah dibuat dengan meletakkan obyek tipis di atas kaca objek, ditetesi air atau pewarna, lalu ditutup kaca penutup tanpa gelembung udara. Bayangan dicari mulai dari perbesaran terkecil, kemudian diperjelas dengan mengatur mikrometer, kondensor, atau diafragma.'],
                    ['judul' => 'Perbedaan Sel Prokariotik dan Eukariotik', 'isi' => 'Sel prokariotik berukuran 1-10 µm, tidak memiliki inti sel, dan DNA-nya berbentuk melingkar dalam sitoplasma, contohnya bakteri. Sel eukariotik berukuran 10-100 µm, memiliki inti sel dan organel lengkap seperti mitokondria dan kloroplas, contohnya sel tumbuhan dan hewan.'],
                ],
                'alat' => [
                    'Mikroskop cahaya, kaca preparat dan kaca penutup',
                    'Pinset, cotton bud, jarum ose, pisau silet',
                ],
                'bahan' => [
                    'Sel mukosa pipi, bawang merah, daun singkong, kapang pada bahan berjamur',
                    'Biakan bakteri (yoghurt, Escherichia coli, Acetobacter aceti, Bacillus subtilis)',
                    'Pewarna: metilen blue, reagen JKJ, phenol blue, kristal violet, lugol, safranin, nigrosin, hijau malasit',
                ],
                'prosedur' => [
                    'Siapkan kaca preparat bersih dan kering, buat preparat basah dari sel mukosa pipi, sel bawang merah, dan sel daun singkong sesuai bahan masing-masing.',
                    'Amati preparat di bawah mikroskop mulai dari perbesaran kecil hingga tinggi, gambar dan beri keterangan bagian sel yang teramati.',
                    'Ambil miselia kapang dari bahan berjamur secara hati-hati menggunakan pinset, amati struktur selnya di bawah mikroskop.',
                    'Lakukan pewarnaan Gram, pewarnaan negatif, dan pewarnaan spora pada biakan bakteri yang berbeda sesuai prosedur masing-masing secara aseptis.',
                    'Amati preparat bakteri dengan perbesaran 10x40, gambar dan beri keterangan hasil pengamatan.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Bagian mikroskop apa yang berfungsi mengatur banyaknya cahaya yang masuk?',
                        'opsi' => ['Kondensor', 'Diafragma', 'Lensa okuler', 'Makrometer'],
                        'jawaban' => 1,
                        'penjelasan' => 'Diafragma berfungsi mengatur banyaknya sinar yang masuk dengan mengatur bukaan iris.',
                    ],
                    [
                        'pertanyaan' => 'Apa ciri utama yang membedakan sel prokariotik dari sel eukariotik?',
                        'opsi' => ['Sel prokariotik tidak memiliki inti sel', 'Sel prokariotik selalu lebih besar', 'Sel eukariotik tidak memiliki DNA', 'Tidak ada perbedaan'],
                        'jawaban' => 0,
                        'penjelasan' => 'Sel prokariotik tidak memiliki inti sel dan DNA-nya berada dalam sitoplasma, berbeda dengan sel eukariotik yang memiliki inti sel.',
                    ],
                    [
                        'pertanyaan' => 'Apa tujuan dilakukannya pewarnaan Gram pada preparat bakteri?',
                        'opsi' => ['Membuat bakteri lebih cepat tumbuh', 'Membantu mengamati dan membedakan karakteristik bakteri di bawah mikroskop', 'Mensterilkan bakteri', 'Mengukur suhu bakteri'],
                        'jawaban' => 1,
                        'penjelasan' => 'Pewarnaan digunakan agar struktur dan karakteristik sel bakteri lebih mudah diamati dan dibedakan di bawah mikroskop.',
                    ],
                ],
            ],
            // Objek 7 - Bab 8: Larutan dan Konsentrasi
            [
                'judul' => 'Larutan dan Konsentrasi',
                'tujuan' => [
                    'Mampu membuat larutan dengan berbagai satuan konsentrasi dengan benar',
                    'Dapat menerapkan angka penting untuk melaporkan konsentrasi larutan yang dibuat',
                    'Mampu melakukan standarisasi larutan menggunakan larutan baku',
                ],
                'pendahuluan' => 'Larutan adalah campuran homogen dari zat terlarut (solute) dan pelarut (solvent). Konsentrasi larutan digunakan untuk menyatakan banyaknya zat terlarut dalam sejumlah pelarut atau larutan, dan dapat dinyatakan dalam beberapa satuan seperti persen volume, persen massa/volume, molaritas, normalitas, molalitas, atau bagian per juta (ppm).',
                'tinjauan_pustaka' => [
                    ['judul' => 'Satuan Konsentrasi Larutan', 'isi' => 'Konsentrasi dapat dinyatakan sebagai %(v/v), %(b/v), Molaritas (mol zat terlarut per volume larutan), Normalitas (mol ekivalen per volume larutan), Molalitas (mol zat terlarut per kg pelarut), atau ppm (bagian zat terlarut per satu juta bagian larutan).'],
                    ['judul' => 'Prinsip Pengenceran', 'isi' => 'Pengenceran adalah penambahan pelarut pada larutan pekat sehingga konsentrasi menjadi lebih rendah. Karena jumlah mol zat terlarut tidak berubah selama pengenceran, berlaku hubungan V1 x M1 = V2 x M2 antara volume dan konsentrasi larutan awal dan akhir.'],
                    ['judul' => 'Pembuatan dan Standarisasi Larutan', 'isi' => 'Larutan dibuat dengan melarutkan zat pada labu volumetrik agar volume akhirnya tepat diketahui. Larutan yang dibuat dapat distandarisasi melalui titrasi terhadap larutan baku dengan konsentrasi yang telah diketahui secara pasti.'],
                ],
                'alat' => [
                    'Labu ukur berbagai ukuran, gelas beaker, batang pengaduk, corong',
                    'Neraca analitik, buret, erlenmeyer',
                ],
                'bahan' => [
                    'Gliserol, NaCl, Na2CO3, NaOH',
                    'KMnO4, HCl, alkohol, CH3COOH, glukosa',
                ],
                'prosedur' => [
                    'Buat larutan gliserol 30% (v/v) dengan mengencerkan gliserol menggunakan labu volumetrik 100 ml, ulangi untuk konsentrasi 50% dan 70%.',
                    'Buat larutan NaCl 5% (b/v) dengan menimbang NaCl, melarutkannya di gelas beaker, lalu memindahkannya ke labu volumetrik hingga tanda batas.',
                    'Buat larutan Na2CO3 0,1 M sebanyak 250 ml dan larutan KMnO4 50 ppm sebanyak 1 liter dengan cara serupa.',
                    'Lakukan standarisasi larutan NaOH 0,1 M menggunakan larutan HCl 0,1 M dengan indikator pp hingga terjadi perubahan warna menjadi merah muda.',
                    'Lakukan pengenceran larutan NaOH, H2SO4, dan alkohol menjadi konsentrasi yang lebih rendah menggunakan rumus V1M1 = V2M2.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Bagaimana rumus hubungan antara larutan sebelum dan sesudah pengenceran?',
                        'opsi' => ['V1 + M1 = V2 + M2', 'V1 x M1 = V2 x M2', 'V1 - M1 = V2 - M2', 'V1 / M1 = V2 / M2'],
                        'jawaban' => 1,
                        'penjelasan' => 'Karena jumlah mol zat terlarut tidak berubah selama pengenceran, berlaku hubungan V1 x M1 = V2 x M2.',
                    ],
                    [
                        'pertanyaan' => 'Satuan konsentrasi apa yang menyatakan jumlah mol zat terlarut per satuan volume larutan?',
                        'opsi' => ['Molalitas', 'Molaritas', 'Persen massa', 'ppm'],
                        'jawaban' => 1,
                        'penjelasan' => 'Molaritas didefinisikan sebagai jumlah mol suatu zat terlarut dalam tiap satuan volume larutan.',
                    ],
                    [
                        'pertanyaan' => 'Apa fungsi proses standarisasi suatu larutan?',
                        'opsi' => ['Mengubah warna larutan', 'Menentukan konsentrasi larutan secara tepat menggunakan larutan baku', 'Mempercepat reaksi', 'Menghilangkan bau larutan'],
                        'jawaban' => 1,
                        'penjelasan' => 'Standarisasi bertujuan menentukan konsentrasi suatu larutan secara tepat dengan mentitrasinya terhadap larutan baku yang telah diketahui konsentrasinya.',
                    ],
                ],
            ],
            // Objek 8 - Bab 9: Spektrofotometri
            [
                'judul' => 'Spektrofotometri',
                'tujuan' => [
                    'Mahasiswa dapat menjelaskan landasan teori spektrofotometri',
                    'Mahasiswa dapat menyimpulkan karakteristik absorbansi suatu materi pada berbagai panjang gelombang',
                    'Mahasiswa dapat menerapkan analisis kuantitatif menggunakan metode spektrofotometri',
                ],
                'pendahuluan' => 'Spektrofotometri adalah metode analisis yang memanfaatkan interaksi antara materi dan energi cahaya. Spektrofotometer mengukur transmitan atau absorbans suatu zat sebagai fungsi panjang gelombang, dan hasil pengukuran ini dapat digunakan untuk menentukan konsentrasi suatu zat dalam larutan.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Prinsip Interaksi Materi dan Cahaya', 'isi' => 'Setiap materi dapat memantulkan, menyerap, atau meneruskan energi cahaya pada panjang gelombang tertentu, tergantung pada energi yang dimilikinya sesuai persamaan E = h.v.'],
                    ['judul' => 'Komponen Spektrofotometer', 'isi' => 'Spektrofotometer terdiri dari sumber cahaya, monokromator untuk menyempitkan pita spektrum, wadah sampel (kuvet), detektor pengubah energi cahaya menjadi sinyal listrik, dan sistem pembacaan hasil.'],
                    ['judul' => 'Hukum Lambert-Beer dan Kurva Standar', 'isi' => 'Hukum Lambert-Beer menyatakan absorbansi berbanding lurus dengan konsentrasi larutan (A = ε.l.c). Kurva standar dibuat dari beberapa larutan dengan konsentrasi diketahui untuk menentukan konsentrasi sampel yang belum diketahui.'],
                ],
                'alat' => [
                    'Spektrofotometer UV-Vis, kuvet, labu ukur',
                ],
                'bahan' => [
                    'Larutan crystal violet berbagai konsentrasi',
                    'Larutan sampel',
                ],
                'prosedur' => [
                    'Siapkan larutan crystal violet 1,5 x 10⁻⁶ M, ukur absorbansinya pada panjang gelombang 300-500 nm dengan selisih 20 nm, tentukan panjang gelombang maksimum.',
                    'Siapkan larutan crystal violet pada beberapa konsentrasi berbeda, ukur absorbansinya pada panjang gelombang maksimum, plot sebagai kurva standar dan tentukan persamaan liniernya.',
                    'Ukur absorbansi larutan sampel pada panjang gelombang maksimum yang sama, hitung konsentrasinya menggunakan persamaan kurva standar.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Sesuai hukum Lambert-Beer, apa hubungan antara absorbansi dan konsentrasi larutan?',
                        'opsi' => ['Berbanding terbalik', 'Tidak berhubungan', 'Berbanding lurus', 'Berbanding kuadrat'],
                        'jawaban' => 2,
                        'penjelasan' => 'Hukum Lambert-Beer menyatakan bahwa absorbansi suatu larutan berbanding lurus dengan konsentrasi zat dalam larutan tersebut.',
                    ],
                    [
                        'pertanyaan' => 'Apa fungsi monokromator pada spektrofotometer?',
                        'opsi' => ['Mengukur suhu sampel', 'Menyempitkan pita spektrum cahaya dari sumber cahaya', 'Menampung sampel', 'Memperkuat sinyal listrik'],
                        'jawaban' => 1,
                        'penjelasan' => 'Monokromator berfungsi menyempitkan pita spektrum cahaya yang dipancarkan sumber cahaya sebelum mengenai sampel.',
                    ],
                    [
                        'pertanyaan' => 'Bagaimana cara menentukan konsentrasi zat pada sampel yang tidak diketahui menggunakan spektrofotometri?',
                        'opsi' => ['Menimbang sampel secara langsung', 'Mengukur absorbansi sampel dan memasukkannya ke persamaan kurva standar', 'Mengukur suhu sampel', 'Menghitung volume sampel'],
                        'jawaban' => 1,
                        'penjelasan' => 'Konsentrasi sampel ditentukan dengan mengukur absorbansinya lalu memasukkan nilai tersebut ke dalam persamaan matematika kurva standar yang telah dibuat.',
                    ],
                ],
            ],
            // Objek 9 - Bab 10: Kinetika Reaksi
            [
                'judul' => 'Kinetika Reaksi',
                'tujuan' => [
                    'Dapat menyimpulkan melalui eksperimen pengaruh suhu dan konsentrasi terhadap laju reaksi kimia',
                    'Mendapatkan orde suatu reaksi kimia melalui eksperimen',
                    'Dapat menerapkan prinsip dasar kalkulus tentang differensial dan integral dalam analisis laju reaksi',
                ],
                'pendahuluan' => 'Kinetika reaksi mempelajari kecepatan terjadinya suatu reaksi kimia, yang dipengaruhi oleh suhu, konsentrasi zat yang bereaksi, dan keberadaan katalis. Laju reaksi dapat diukur melalui perubahan konsentrasi pereaksi atau hasil reaksi per satuan waktu, dan dinyatakan dalam persamaan laju reaksi.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Faktor yang Mempengaruhi Laju Reaksi', 'isi' => 'Suhu yang lebih tinggi mempercepat reaksi karena meningkatkan energi kinetik molekul. Konsentrasi zat yang lebih tinggi meningkatkan tubrukan antar molekul, sedangkan katalis menurunkan energi aktivasi reaksi tanpa ikut bereaksi secara permanen.'],
                    ['judul' => 'Persamaan Laju dan Orde Reaksi', 'isi' => 'Laju reaksi dinyatakan sebagai Laju = k[A]ˣ, dengan k sebagai tetapan laju reaksi dan x sebagai orde reaksi yang didapat melalui eksperimen. Reaksi orde kesatu memiliki bentuk integrasi ln([A]/[A]₀) = kt, sedangkan orde kedua 1/[A] - 1/[A]₀ = kt.'],
                    ['judul' => 'Reaksi Crystal Violet dengan NaOH', 'isi' => 'Warna ungu crystal violet (CV) akan hilang saat bereaksi dengan NaOH membentuk CV-OH yang tidak berwarna. Perubahan konsentrasi CV selama reaksi dapat diamati melalui perubahan absorbansi menggunakan spektrofotometer sesuai hukum Lambert-Beer.'],
                ],
                'alat' => [
                    'Stopwatch, gelas beaker, waterbath',
                    'Spektrofotometer UV-Vis, kuvet',
                ],
                'bahan' => [
                    'Larutan crystal violet 1,5 x 10⁻⁶ M',
                    'Larutan NaOH berbagai konsentrasi (0,05M; 0,075M; 0,1M)',
                ],
                'prosedur' => [
                    'Campurkan larutan crystal violet dengan larutan NaOH pada berbagai konsentrasi, catat waktu yang dibutuhkan hingga warna ungu hilang, ulangi minimal 2 kali per konsentrasi.',
                    'Ulangi percobaan pada satu konsentrasi NaOH tetapi dengan suhu larutan yang berbeda (30°C, 35°C, 40°C) menggunakan waterbath.',
                    'Tentukan panjang gelombang maksimum crystal violet menggunakan spektrofotometer pada rentang 400-700 nm.',
                    'Ukur absorbansi campuran crystal violet dan NaOH setiap 10 detik hingga absorbansi konstan, plot ln A dan 1/A terhadap waktu untuk menentukan orde reaksi.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Bagaimana pengaruh kenaikan suhu terhadap laju reaksi kimia pada umumnya?',
                        'opsi' => ['Memperlambat reaksi', 'Mempercepat reaksi', 'Tidak berpengaruh', 'Menghentikan reaksi'],
                        'jawaban' => 1,
                        'penjelasan' => 'Suhu yang lebih tinggi meningkatkan energi kinetik molekul sehingga tubrukan antar molekul lebih sering terjadi dan reaksi berjalan lebih cepat.',
                    ],
                    [
                        'pertanyaan' => 'Apa yang dimaksud dengan orde reaksi?',
                        'opsi' => ['Urutan pereaksi ditambahkan', 'Jumlah semua pangkat konsentrasi dalam persamaan laju reaksi', 'Volume total reaksi', 'Jumlah produk yang dihasilkan'],
                        'jawaban' => 1,
                        'penjelasan' => 'Orde reaksi adalah jumlah semua pangkat dari konsentrasi pereaksi dalam persamaan laju reaksi, ditentukan melalui eksperimen.',
                    ],
                    [
                        'pertanyaan' => 'Bagaimana cara menentukan apakah suatu reaksi termasuk orde kesatu melalui data absorbansi terhadap waktu?',
                        'opsi' => ['Jika plot 1/A terhadap waktu berupa garis lurus', 'Jika plot ln A terhadap waktu berupa garis lurus', 'Jika absorbansi selalu konstan', 'Jika absorbansi naik terus'],
                        'jawaban' => 1,
                        'penjelasan' => 'Bila ln A diplot terhadap waktu menghasilkan garis lurus, maka reaksi tersebut termasuk reaksi orde kesatu.',
                    ],
                ],
            ],
            // Objek 10 - Bab 11: Teknik Pengenceran dan Perhitungan Mikroba
            [
                'judul' => 'Teknik Pengenceran dan Perhitungan Mikroba',
                'tujuan' => [
                    'Dapat melakukan penanaman mikroba menggunakan metode cawan',
                    'Dapat melakukan perhitungan mikroba dengan hitungan cawan',
                    'Memahami prinsip pengenceran bertingkat (serial dilution) dengan benar',
                ],
                'pendahuluan' => 'Pengenceran bertingkat digunakan untuk menurunkan konsentrasi mikroorganisme ke tingkat yang lebih mudah dihitung. Perhitungan total mikroba dapat dilakukan dengan metode hitung cawan (Total Plate Count) yang menumbuhkan mikroba hidup pada media hingga membentuk koloni yang dapat dihitung.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Prinsip Pengenceran Bertingkat', 'isi' => 'Pengenceran log (log dilution) adalah pengenceran sepuluh kali lipat dengan rasio 1:10, misalnya 1 ml sampel ditambah 9 ml pengencer menghasilkan pengenceran 10⁻¹, dan seterusnya hingga tingkat pengenceran yang diperlukan.'],
                    ['judul' => 'Metode Pour Plate dan Spread Plate', 'isi' => 'Metode pour plate menuangkan media cair hangat ke cawan berisi inokulum lalu memutarnya membentuk angka 8, sedangkan metode spread plate menyebarkan inokulum di permukaan media padat menggunakan batang L yang telah disterilkan.'],
                    ['judul' => 'Kisaran dan Syarat Perhitungan Koloni', 'isi' => 'Standar AOAC menyarankan kisaran 30-300 koloni per cawan untuk dihitung. Koloni yang bersinggungan dihitung sebagai satu koloni, kecuali memiliki penampakan berbeda sehingga dapat dihitung sebagai koloni terpisah.'],
                ],
                'alat' => [
                    'Tabung reaksi, cawan petri, pipet, batang L, vortex',
                ],
                'bahan' => [
                    'Sampel mengandung mikroba, larutan pengencer, media agar',
                ],
                'prosedur' => [
                    'Timbang atau ambil sampel sebanyak 1 g/ml, masukkan ke tabung berisi 9 ml pengencer, kocok hingga merata sebagai pengenceran pertama.',
                    'Lanjutkan pengenceran bertingkat hingga 10⁻⁶ dengan memindahkan 1 ml dari pengenceran sebelumnya ke tabung pengencer baru.',
                    'Tanam 3 pengenceran terakhir ke cawan petri berisi media menggunakan metode pour plate atau spread plate secara duplo dan aseptis, inkubasi terbalik selama 24 jam.',
                    'Hitung jumlah koloni pada setiap cawan sesuai kisaran valid (30-300 koloni), catat pada tabel jumlah total mikroba.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Berapa rasio pengenceran pada satu kali pengenceran log (log dilution)?',
                        'opsi' => ['1:2', '1:5', '1:10', '1:100'],
                        'jawaban' => 2,
                        'penjelasan' => 'Pengenceran log adalah pengenceran sepuluh kali lipat dengan rasio 1:10.',
                    ],
                    [
                        'pertanyaan' => 'Berapa kisaran jumlah koloni per cawan yang dianggap valid menurut standar AOAC?',
                        'opsi' => ['1-10 koloni', '10-25 koloni', '30-300 koloni', '300-1000 koloni'],
                        'jawaban' => 2,
                        'penjelasan' => 'AOAC memberikan kisaran 30-300 koloni per cawan sebagai standar perhitungan yang digunakan pada praktikum ini.',
                    ],
                    [
                        'pertanyaan' => 'Apa yang harus dilakukan bila dua koloni bersinggungan namun memiliki penampakan yang berbeda?',
                        'opsi' => ['Dihitung sebagai satu koloni', 'Dihitung sebagai dua koloni terpisah', 'Diabaikan dari perhitungan', 'Cawan harus diulang'],
                        'jawaban' => 1,
                        'penjelasan' => 'Koloni yang bersinggungan namun memiliki penampakan berbeda dihitung sebagai dua koloni individual.',
                    ],
                ],
            ],
            // Objek 11 - Bab 12: Kurva Pertumbuhan Mikroba
            [
                'judul' => 'Kurva Pertumbuhan Mikroba',
                'tujuan' => [
                    'Mahasiswa dapat membuat kurva pertumbuhan bakteri Escherichia coli',
                    'Mahasiswa dapat memahami kurva pertumbuhan bakteri',
                    'Mahasiswa dapat menentukan tahapan pertumbuhan logaritmik pada kurva pertumbuhan bakteri',
                ],
                'pendahuluan' => 'Pertumbuhan mikroba merupakan penambahan jumlah atau massa sel dari waktu ke waktu. Kurva pertumbuhan mikroba yang diinokulasi pada medium cair menunjukkan empat fase pertumbuhan yang berbeda, dan dapat diamati menggunakan metode turbidimetri berbasis pengukuran kekeruhan (optical density).',
                'tinjauan_pustaka' => [
                    ['judul' => 'Empat Fase Pertumbuhan Mikroba', 'isi' => 'Fase lag merupakan tahap adaptasi tanpa pembelahan sel; fase logaritmik (log) ditandai pembelahan biner yang cepat dan seragam; fase stasioner terjadi ketika jumlah sel yang membelah sama dengan yang mati; fase kematian terjadi akibat penipisan nutrisi dan akumulasi produk beracun.'],
                    ['judul' => 'Metode Turbidimetri', 'isi' => 'Metode ini menggunakan spektrofotometer untuk melacak perubahan optical density (OD) dari waktu ke waktu; semakin banyak jumlah sel, semakin berkurang transmisi cahaya melalui sampel. Pengukuran OD standar biasanya dilakukan pada panjang gelombang 600 nm setiap 15 menit.'],
                    ['judul' => 'Interpretasi Kurva Pertumbuhan', 'isi' => 'Data OD terhadap waktu inkubasi diplot menjadi kurva dengan OD sebagai ordinat dan waktu sebagai absis. Fase logaritmik dapat dikenali dari bagian kurva yang menanjak tajam sebelum mencapai fase stasioner yang relatif datar.'],
                ],
                'alat' => [
                    'Spektrofotometer, waterbath shaker inkubator, kuvet',
                ],
                'bahan' => [
                    'Kultur Escherichia coli, kaldu nutrisi cair steril',
                ],
                'prosedur' => [
                    'Masukkan 5 ml kultur E. coli ke dalam kaldu nutrisi cair steril, ukur optical density (OD) awal pada panjang gelombang 600 nm.',
                    'Inkubasi kultur pada waterbath shaker suhu 37°C, ukur kembali OD setiap 15 menit hingga absorbansi tidak meningkat lagi.',
                    'Plot data OD terhadap waktu inkubasi untuk membentuk kurva pertumbuhan mikroba, tentukan fase-fase pertumbuhan yang teramati.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Pada fase apa jumlah sel bakteri meningkat pesat melalui pembelahan biner yang cepat dan seragam?',
                        'opsi' => ['Fase lag', 'Fase logaritmik', 'Fase stasioner', 'Fase kematian'],
                        'jawaban' => 1,
                        'penjelasan' => 'Fase logaritmik (log) ditandai dengan peningkatan pesat jumlah populasi melalui pembelahan biner yang seragam dan cepat.',
                    ],
                    [
                        'pertanyaan' => 'Apa penyebab utama terjadinya fase stasioner pada kurva pertumbuhan mikroba?',
                        'opsi' => ['Semua sel mati sekaligus', 'Penipisan metabolit esensial dan akumulasi produk beracun', 'Suhu yang terlalu rendah', 'Tidak ada cahaya'],
                        'jawaban' => 1,
                        'penjelasan' => 'Fase stasioner terjadi karena penipisan beberapa metabolit esensial dan akumulasi produk akhir yang bersifat racun dalam medium.',
                    ],
                    [
                        'pertanyaan' => 'Apa prinsip dasar metode turbidimetri dalam mengukur pertumbuhan mikroba?',
                        'opsi' => ['Menghitung koloni secara manual', 'Mengukur perubahan kekeruhan (optical density) larutan kultur', 'Mengukur suhu kultur', 'Mengukur pH kultur'],
                        'jawaban' => 1,
                        'penjelasan' => 'Turbidimetri mengukur perubahan optical density (kekeruhan) sampel dari waktu ke waktu menggunakan spektrofotometer sebagai indikator pertumbuhan sel.',
                    ],
                ],
            ],
            // Objek 12 - Bab 13: Titrimetri
            [
                'judul' => 'Titrimetri',
                'tujuan' => [
                    'Mahasiswa dapat menjelaskan prinsip dasar analisis kuantitatif metode titrimetri',
                    'Mahasiswa dapat memilih indikator yang tepat dalam analisis suatu senyawa menggunakan titrimetri',
                    'Mahasiswa terampil melakukan titrasi langsung maupun titrasi balik',
                ],
                'pendahuluan' => 'Titrimetri merupakan metode analisis kuantitatif berdasarkan hubungan stoikiometri dari suatu reaksi kimia, dengan larutan titran berkonsentrasi diketahui ditambahkan sedikit demi sedikit hingga tercapai titik ekuivalen. Indikator digunakan untuk mendeteksi titik akhir titrasi melalui perubahan warna.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Titik Ekuivalen dan Titik Akhir Titrasi', 'isi' => 'Titik ekuivalensi tercapai saat jumlah titran secara kimia setara dengan analit, sedangkan titik akhir titrasi ditandai oleh perubahan warna indikator. Idealnya titik akhir titrasi sedekat mungkin dengan titik ekuivalensi agar data akurat.'],
                    ['judul' => 'Syarat Reaksi untuk Titrimetri', 'isi' => 'Reaksi titrimetri harus berjalan sesuai persamaan tertentu tanpa reaksi samping, berlangsung lengkap pada titik ekuivalen, titik ekuivalennya dapat dideteksi, dan reaksinya berjalan cepat.'],
                    ['judul' => 'Aplikasi Titrimetri pada Analisis Protein', 'isi' => 'Analisis protein melibatkan tahap digestion (membebaskan N dari protein menjadi amonium sulfat), destilasi (menghasilkan gas amonia), lalu titrasi langsung menggunakan H3BO3 atau titrasi balik menggunakan H2SO4 untuk menentukan jumlah nitrogen.'],
                ],
                'alat' => [
                    'Buret, statif, labu erlenmeyer, pipet',
                ],
                'bahan' => [
                    'Larutan NH3 hasil destilasi, H3BO3, H2SO4, NaOH',
                    'Indikator metil red dan bromcresol green',
                ],
                'prosedur' => [
                    'Untuk titrasi langsung: tambahkan indikator metil red dan bromcresol green pada larutan NH3 yang telah direaksikan dengan H3BO3, titrasi menggunakan H2SO4 hingga terjadi perubahan warna dari pink violet ke emerald green, catat volume titran.',
                    'Untuk titrasi balik: tambahkan indikator metil red pada larutan NH3 yang telah direaksikan dengan H2SO4, titrasi menggunakan NaOH hingga warna berubah dari merah ke kuning, catat volume titran.',
                    'Hitung konsentrasi NH3 dalam larutan sampel berdasarkan volume titran yang digunakan pada masing-masing metode.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Apa perbedaan antara titik ekuivalen dan titik akhir titrasi?',
                        'opsi' => ['Keduanya sama persis', 'Titik ekuivalen adalah saat titran setara secara kimia dengan analit, titik akhir ditandai perubahan warna indikator', 'Titik akhir selalu tercapai lebih dulu', 'Titik ekuivalen tidak dapat diukur'],
                        'jawaban' => 1,
                        'penjelasan' => 'Titik ekuivalen adalah saat jumlah titran secara kimia setara dengan analit, sedangkan titik akhir titrasi ditandai perubahan warna indikator dan idealnya sedekat mungkin dengan titik ekuivalen.',
                    ],
                    [
                        'pertanyaan' => 'Pada titrasi balik dalam analisis protein, zat apa yang sebenarnya dititrasi?',
                        'opsi' => ['Seluruh NH3 yang dihasilkan', 'Kelebihan asam yang tidak bereaksi dengan amonia', 'Protein secara langsung', 'Katalis Se atau Cu'],
                        'jawaban' => 1,
                        'penjelasan' => 'Pada titrasi balik, yang dititrasi adalah kelebihan H2SO4 (asam) yang tidak bereaksi dengan amonia.',
                    ],
                    [
                        'pertanyaan' => 'Apa fungsi indikator dalam proses titrasi?',
                        'opsi' => ['Mempercepat reaksi', 'Mendeteksi kelebihan titran melalui perubahan warna yang kasat mata', 'Menambah volume larutan', 'Menstabilkan suhu larutan'],
                        'jawaban' => 1,
                        'penjelasan' => 'Indikator digunakan untuk mendeteksi kelebihan titran melalui perubahan warna yang dapat diamati secara visual sebagai tanda titik akhir titrasi.',
                    ],
                ],
            ],
            // Objek 13 - Bab 14: Gravimetri
            [
                'judul' => 'Gravimetri',
                'tujuan' => [
                    'Mahasiswa dapat menjelaskan prinsip dasar analisis kuantitatif metode gravimetri',
                    'Mahasiswa dapat melakukan proses pengendapan untuk memisahkan suatu analit',
                    'Mahasiswa mahir melakukan perhitungan dalam analisis gravimetri',
                ],
                'pendahuluan' => 'Gravimetri adalah metode analisis kuantitatif yang menetapkan kadar analit dengan cara menimbang beratnya setelah melalui proses pengendapan dan pemisahan dari komponen lain dalam contoh uji. Keberhasilan metode ini bergantung pada kesempurnaan proses pemisahan dan kemurnian senyawa yang ditimbang.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Prinsip Dasar Gravimetri', 'isi' => 'Analit A bereaksi dengan senyawa B membentuk senyawa baru AaBb yang bersifat sedikit larut dan diketahui susunannya, sehingga berat analit dapat ditentukan melalui konversi berat endapan yang ditimbang.'],
                    ['judul' => 'Syarat Keberhasilan Analisis Gravimetri', 'isi' => 'Proses pemisahan harus bersifat sempurna sehingga seluruh analit terbawa dalam massa yang ditimbang, dan senyawa baru yang terbentuk harus memiliki susunan pasti dan bersifat murni.'],
                    ['judul' => 'Faktor Gravimetri dan Perhitungan Kadar', 'isi' => 'Massa zat yang dicari dihitung dengan mengalikan berat endapan dengan faktor gravimetri (perbandingan BM zat yang dicari dengan BM senyawa yang diendapkan), sesuai persamaan: Massa zat = Berat endapan x Faktor Gravimetri.'],
                ],
                'alat' => [
                    'Kertas saring whatman, corong, oven, desikator, neraca analitik',
                ],
                'bahan' => [
                    'Sampel garam, larutan AgNO3, HNO3, metanol',
                ],
                'prosedur' => [
                    'Timbang sampel garam sebanyak 1 gram, larutkan dengan 100 ml asam nitrat 0,1M, panaskan hingga 80°C tanpa mendidih.',
                    'Teteskan larutan AgNO3 5% secara perlahan ke dalam larutan sampel panas hingga tidak terbentuk kekeruhan lagi, tanda pengendapan telah sempurna.',
                    'Lanjutkan pemanasan hingga supernatan jernih, saring endapan menggunakan kertas saring whatman, bilas dengan larutan pencuci HNO3 encer dan metanol.',
                    'Keringkan endapan di dalam oven, dinginkan di desikator, lalu timbang untuk menghitung kadar klorida dalam sampel menggunakan faktor gravimetri.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Apa syarat utama agar analisis gravimetri berhasil dengan baik?',
                        'opsi' => ['Reaksi harus berlangsung sangat lambat', 'Proses pemisahan harus sempurna dan senyawa yang ditimbang harus murni', 'Sampel harus berwarna', 'Endapan harus larut sepenuhnya'],
                        'jawaban' => 1,
                        'penjelasan' => 'Gravimetri membutuhkan proses pemisahan yang sempurna dan senyawa yang terbentuk harus memiliki susunan pasti serta bersifat murni.',
                    ],
                    [
                        'pertanyaan' => 'Apa yang dimaksud dengan faktor gravimetri?',
                        'opsi' => ['Berat sampel awal', 'Perbandingan BM zat yang dicari dengan BM senyawa yang diendapkan', 'Volume larutan pencuci', 'Suhu pengeringan oven'],
                        'jawaban' => 1,
                        'penjelasan' => 'Faktor gravimetri adalah perbandingan antara berat molekul zat yang dicari dengan berat molekul senyawa yang diendapkan, digunakan untuk mengonversi berat endapan menjadi massa analit.',
                    ],
                    [
                        'pertanyaan' => 'Mengapa larutan asam nitrat encer ditambahkan sebelum pengendapan klorida dengan AgNO3?',
                        'opsi' => ['Untuk mewarnai larutan', 'Untuk mencegah ion lain seperti karbonat ikut mengendap', 'Untuk mempercepat penguapan', 'Untuk menetralkan AgNO3'],
                        'jawaban' => 1,
                        'penjelasan' => 'Penambahan asam nitrat mencegah ion lain seperti karbonat ikut mengendap bersama perak klorida sehingga hasil pengendapan lebih selektif.',
                    ],
                ],
            ],
            // Objek 14 - Bab 15: Kromatografi
            [
                'judul' => 'Kromatografi',
                'tujuan' => [
                    'Mahasiswa dapat menjelaskan landasan teori analisis menggunakan kromatografi kertas',
                    'Mahasiswa dapat melakukan analisis suatu zat secara kualitatif menggunakan kromatografi kertas',
                    'Mahasiswa dapat menyimpulkan melalui eksperimen bahwa polaritas zat berpengaruh pada nilai Rf',
                ],
                'pendahuluan' => 'Kromatografi adalah teknik pemisahan campuran dengan mendistribusikan zat pada dua fase, yaitu fase stasioner dan fase gerak. Perbedaan kemampuan bermigrasi setiap zat pada fase stasioner menjadi dasar pemisahan, dan hasil pemisahan diidentifikasi menggunakan nilai Rf (Retention Factor).',
                'tinjauan_pustaka' => [
                    ['judul' => 'Prinsip Fase Diam dan Fase Gerak', 'isi' => 'Fase stasioner dapat berupa padatan atau cairan pada permukaan luas, sedangkan fase gerak (eluen) berupa cairan atau gas yang bergerak membawa zat terlarut. Pergerakan zat dipengaruhi oleh keseimbangan antara kekuatan fase stasioner menahan dan fase gerak membawa zat, tergantung pada polaritas keduanya.'],
                    ['judul' => 'Perhitungan Nilai Rf', 'isi' => 'Nilai Rf dihitung sebagai perbandingan jarak migrasi zat (D) terhadap jarak migrasi eluen (F), yaitu Rf = D/F. Zat dengan nilai Rf yang sama dengan senyawa standar dapat diidentifikasi sebagai senyawa yang sama.'],
                    ['judul' => 'Aplikasi Kromatografi Kertas', 'isi' => 'Kromatografi kertas dapat digunakan untuk memisahkan campuran asam amino berdasarkan perbedaan kepolaran rantai sampingnya, maupun memisahkan pigmen tanaman seperti klorofil, karoten, antosianin, dan betalain berdasarkan perbedaan polaritas.'],
                ],
                'alat' => [
                    'Kertas kromatografi/whatman no.1, gelas beaker atau tabung reaksi, pipet kapiler, hair dryer, oven',
                ],
                'bahan' => [
                    'Standar asam amino, sampel campuran asam amino',
                    'Eluen butanol:asam asetat:air destillata (60:15:25), larutan ninhydrin',
                    'Ekstrak daun bayam, eluen petroleum eter:aseton (9:1)',
                ],
                'prosedur' => [
                    'Beri garis awal pada kertas whatman, teteskan standar asam amino dan sampel pada titik yang telah ditandai menggunakan pipet kapiler.',
                    'Elusi kertas di dalam bejana tertutup berisi eluen hingga mencapai garis batas atas, keringkan, semprot dengan ninhydrin, lalu panaskan hingga muncul spot berwarna ungu.',
                    'Ukur jarak migrasi setiap spot (D) dan jarak migrasi eluen (F), hitung nilai Rf masing-masing dan bandingkan dengan Rf standar untuk mengidentifikasi komponen sampel.',
                    'Ulangi prosedur serupa menggunakan ekstrak daun bayam dan eluen petroleum eter:aseton untuk memisahkan pigmen tanaman, hitung nilai Rf setiap spot warna yang muncul.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Bagaimana rumus untuk menghitung nilai Rf pada kromatografi kertas?',
                        'opsi' => ['Rf = F/D', 'Rf = D/F', 'Rf = D x F', 'Rf = D - F'],
                        'jawaban' => 1,
                        'penjelasan' => 'Nilai Rf (Retention Factor) dihitung sebagai jarak migrasi zat (D) dibagi jarak migrasi eluen (F), yaitu Rf = D/F.',
                    ],
                    [
                        'pertanyaan' => 'Faktor apa yang menyebabkan setiap zat bermigrasi dengan kecepatan berbeda pada kromatografi?',
                        'opsi' => ['Warna zat', 'Perbedaan polaritas zat', 'Suhu ruangan', 'Ukuran kertas'],
                        'jawaban' => 1,
                        'penjelasan' => 'Perbedaan polaritas antar zat menyebabkan kecepatan migrasi yang berbeda saat disapu oleh eluen pada fase gerak.',
                    ],
                    [
                        'pertanyaan' => 'Apa fungsi larutan ninhydrin pada kromatografi kertas asam amino?',
                        'opsi' => ['Sebagai eluen', 'Untuk menampakkan spot asam amino yang tidak berwarna', 'Untuk mempercepat elusi', 'Untuk melarutkan kertas'],
                        'jawaban' => 1,
                        'penjelasan' => 'Ninhydrin disemprotkan untuk menampakkan spot asam amino yang semula tidak berwarna, biasanya berubah menjadi warna ungu setelah dipanaskan.',
                    ],
                ],
            ],
        ];
    }
}
