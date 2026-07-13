<?php

namespace Database\Seeders;

use App\Models\Matakuliah;
use App\Models\Materi;
use App\Models\Praktikum;
use App\Models\Prodi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Mengisi konten untuk 2 mata kuliah praktikum THP Semester 4:
 * 1. Praktikum Pengemasan dan Penyimpanan
 * 2. Praktikum Mikrobiologi Pangan dan Hasil Pertanian
 * Mengikuti pola ThpSemester2KontenSeeder / ThpSemester3KontenSeeder.
 *
 * Sumber materi:
 * - MODUL_PRAKTIKUM_KEMASAN_DAN_PENYIMPANAN.docx (PS THP, Fakultas Pertanian,
 *   Universitas Jambi). Modul sumber ini tidak memiliki bagian teori/tinjauan
 *   pustaka eksplisit (hanya Tujuan, Bahan-Alat, Prosedur, Tabel Pengamatan),
 *   sehingga bagian Pendahuluan dan Tinjauan Pustaka pada seeder ini disusun
 *   ringkas oleh AI berdasarkan konteks topik — DISARANKAN untuk direview
 *   ulang oleh dosen pengampu sebelum dipakai mahasiswa.
 * - Penuntun_MIKRO_PANGAN.pdf: Penuntun Praktikum Mikrobiologi Pangan dan
 *   Hasil Pertanian, PS THP, Fakultas Pertanian, Universitas Jambi (2026).
 *
 * Pemisahan konten:
 * - Materi    = teori saja (Pendahuluan + Tinjauan Pustaka).
 * - Praktikum = pelaksanaan (Tujuan, Alat, Bahan, Prosedur/Langkah Kerja, Kuis).
 *
 * PENTING: sesuaikan $matakuliahSlug pada method run() dengan slug asli yang
 * sudah tersimpan di tabel matakuliahs (di sini saya menebak
 * 'thp-s4-praktikum-pengemasan-penyimpanan' dan
 * 'thp-s4-praktikum-mikrobiologi-pangan' mengikuti pola
 * 'thp-s2-praktikum-mipa' dst). Jika mata kuliah belum ada di database, buat
 * dulu recordnya (lihat contoh Matakuliah::create() di percakapan sebelumnya)
 * sebelum menjalankan seeder ini.
 *
 * CATATAN: kolom 'pokok_bahasan' pada tabel materis bersifat NOT NULL tanpa
 * default, jadi field ini WAJIB diisi (sudah disertakan di bawah).
 */
class ThpSemester4KontenSeeder extends Seeder
{
    protected string $formatLaporanKemasan = "LAPORAN AWAL\n\n"
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

    protected string $formatLaporanMikro = "LAPORAN PRAKTIKUM\n\n"
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
        ."(Minimal 2 buku selain penuntun praktikum, 2 jurnal nasional, dan "
        ."beberapa tambahan dari internet selain blog dan Wikipedia)";

    public function run(): void
    {
        $prodi = Prodi::where('kode', 'THP')->first();

        if (! $prodi) {
            $this->command?->warn('Prodi THP tidak ditemukan, seeder dilewati.');
            return;
        }

        $this->seedMatakuliah(
            $prodi,
            'thp-s4-praktikum-pengemasan-penyimpanan',
            $this->formatLaporanKemasan,
            $this->praktikumPengemasanPenyimpanan(),
            'KEMAS'
        );

        $this->seedMatakuliah(
            $prodi,
            'thp-s4-praktikum-mikrobiologi-pangan',
            $this->formatLaporanMikro,
            $this->praktikumMikrobiologiPangan(),
            'MIKRO'
        );
    }

    protected function seedMatakuliah(Prodi $prodi, string $matakuliahSlug, string $formatLaporan, array $objekList, string $kodePrefix): void
    {
        $matakuliah = Matakuliah::where('slug', $matakuliahSlug)->first();

        if (! $matakuliah) {
            $this->command?->warn("Mata kuliah dengan slug {$matakuliahSlug} tidak ditemukan, dilewati.");
            return;
        }

        $matakuliah->update(['format_laporan' => $formatLaporan]);

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
                    'kode' => $kodePrefix.'-P'.str_pad((string) $nomor, 2, '0', STR_PAD_LEFT),
                    'judul' => 'Objek '.$nomor.': '.$objek['judul'],
                    'tingkat' => 'Menengah',
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

    protected function praktikumPengemasanPenyimpanan(): array
    {
        return [
            // Objek 1
            [
                'judul' => 'Perubahan Karakteristik Serealia yang Dikemas dengan Berbagai Jenis Kemasan',
                'tujuan' => [
                    'Mengetahui pengaruh jenis kemasan terhadap karakteristik komoditi yang dikemas',
                    'Menentukan jenis kemasan yang paling sesuai untuk masing-masing komoditi tersebut',
                ],
                'pendahuluan' => 'Serealia dan kacang-kacangan merupakan komoditas pangan kering yang rentan mengalami perubahan mutu selama penyimpanan akibat faktor kelembaban, suhu, dan jenis kemasan yang digunakan. Pemilihan jenis kemasan yang tepat berperan penting dalam mempertahankan mutu fisik bahan pangan kering selama masa penyimpanan.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Fungsi Kemasan dalam Penyimpanan Pangan Kering', 'isi' => 'Kemasan berfungsi melindungi bahan dari kontaminasi, kelembaban, serangga, dan kerusakan mekanis selama penyimpanan. Jenis bahan kemasan seperti karung kain, goni, sak, dan plastik memiliki tingkat proteksi yang berbeda terhadap perpindahan uap air dan udara.'],
                    ['judul' => 'Perubahan Mutu Fisik Serealia Selama Penyimpanan', 'isi' => 'Penyimpanan yang tidak tepat dapat menyebabkan perubahan berat, warna, tekstur, dan kemunculan hama atau jamur pada serealia/kacang-kacangan, yang dipengaruhi oleh permeabilitas kemasan terhadap uap air dan udara.'],
                ],
                'alat' => ['Timbangan', 'Jangka sorong', 'Mikrometer'],
                'bahan' => ['Karung kain, karung goni, kantong sak, kantong plastik', 'Beras ketan, jagung, kacang hijau, kacang kedelai'],
                'prosedur' => [
                    'Pilih serealia/kacang-kacangan yang mutunya baik, buang bagian yang tidak diperlukan.',
                    'Masukkan 10 gram serealia/kacang-kacangan ke dalam masing-masing jenis karung (kain, goni, sak, plastik), ikat dengan karet.',
                    'Letakkan sampel pada suhu kamar.',
                    'Amati dan catat perubahan berat serta keadaan fisik serealia/kacang-kacangan setiap hari selama 1 minggu.',
                    'Bandingkan perubahan berat dan keadaan fisik antar jenis kemasan untuk menentukan kemasan yang paling sesuai.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Apa tujuan utama pengemasan pada serealia/kacang-kacangan yang disimpan?',
                        'opsi' => ['Menambah berat bahan', 'Melindungi bahan dari kelembaban, hama, dan kontaminasi', 'Mengubah warna bahan', 'Mempercepat pembusukan'],
                        'jawaban' => 1,
                        'penjelasan' => 'Kemasan berfungsi melindungi bahan pangan kering dari kontaminasi, kelembaban, serangga, dan kerusakan mekanis selama penyimpanan.',
                    ],
                    [
                        'pertanyaan' => 'Parameter apa yang diamati dan dicatat setiap hari selama seminggu pada percobaan ini?',
                        'opsi' => ['Suhu ruangan', 'Perubahan berat dan keadaan fisik serealia/kacang-kacangan', 'Kelembaban udara luar', 'Jumlah karung yang digunakan'],
                        'jawaban' => 1,
                        'penjelasan' => 'Prosedur percobaan meminta pengamatan perubahan berat bahan dan keadaan fisik serealia/kacang-kacangan setiap hari selama 1 minggu.',
                    ],
                    [
                        'pertanyaan' => 'Mengapa jenis kemasan yang berbeda dapat menghasilkan perubahan berat serealia yang berbeda pula?',
                        'opsi' => ['Karena warna kemasan berbeda', 'Karena permeabilitas kemasan terhadap uap air dan udara berbeda-beda', 'Karena harga kemasan berbeda', 'Karena berat kemasan itu sendiri'],
                        'jawaban' => 1,
                        'penjelasan' => 'Setiap jenis bahan kemasan (kain, goni, sak, plastik) memiliki tingkat permeabilitas yang berbeda terhadap perpindahan uap air dan udara, sehingga memengaruhi perubahan berat bahan yang dikemas.',
                    ],
                ],
            ],
            // Objek 2
            [
                'judul' => 'Pengaruh Jenis Kemasan dan Ventilasi pada Kemasan Hortikultura',
                'tujuan' => [
                    'Melihat pengaruh jenis kemasan dan jumlah ventilasi yang digunakan untuk melindungi dan memperpanjang masa simpan produk hortikultura',
                ],
                'pendahuluan' => 'Produk hortikultura seperti sayur dan buah bersifat mudah rusak (perishable) dan tetap melakukan respirasi setelah panen, sehingga memerlukan kemasan dengan ventilasi yang sesuai untuk mengatur pertukaran gas dan kelembaban agar masa simpannya optimal.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Respirasi Produk Hortikultura Pascapanen', 'isi' => 'Produk hortikultura segar terus melakukan respirasi yang menghasilkan panas dan uap air setelah dipanen, sehingga kemasan yang tertutup rapat tanpa ventilasi dapat menyebabkan penumpukan kelembaban dan mempercepat kebusukan.'],
                    ['judul' => 'Peran Ventilasi pada Kemasan', 'isi' => 'Jumlah lubang ventilasi pada kemasan plastik memengaruhi laju pertukaran udara dan uap air, sehingga kemasan dengan jumlah ventilasi yang tepat dapat memperlambat pembusukan dibandingkan kemasan tanpa ventilasi maupun tanpa kemasan sama sekali.'],
                ],
                'alat' => ['Pisau', 'Gunting', 'Karet'],
                'bahan' => ['Kemasan plastik, kemasan kertas', 'Sayur bayam, sayur sawi, buah pir, tomat'],
                'prosedur' => [
                    'Pilih buah/sayur yang mutunya baik, buang bagian yang tidak diperlukan, cuci bersih, tiriskan dan angin-anginkan hingga kering.',
                    'Kemas sayur dan buah sesuai perlakuan: tanpa kemasan, kemasan tanpa lubang, kemasan berlubang 6, 8, 10, dan 12 buah.',
                    'Simpan seluruh sampel pada suhu kamar.',
                    'Amati perubahan yang terjadi setiap hari selama 1 minggu, hingga sampel tidak layak pakai lagi (misalnya plastik berkeringat, buah/sayur berakar atau busuk).',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Apa fungsi utama ventilasi (lubang) pada kemasan produk hortikultura?',
                        'opsi' => ['Mempercantik tampilan kemasan', 'Mengatur pertukaran udara dan uap air akibat respirasi produk', 'Menambah berat kemasan', 'Mengurangi biaya produksi kemasan'],
                        'jawaban' => 1,
                        'penjelasan' => 'Ventilasi berfungsi mengatur laju pertukaran udara dan uap air yang dihasilkan dari respirasi produk hortikultura, sehingga memengaruhi masa simpannya.',
                    ],
                    [
                        'pertanyaan' => 'Mengapa produk hortikultura seperti sayur dan buah tetap melakukan respirasi setelah dipanen?',
                        'opsi' => ['Karena produk tersebut masih hidup secara metabolik pascapanen', 'Karena terkena sinar matahari', 'Karena disimpan dalam kemasan plastik', 'Karena kandungan airnya tinggi'],
                        'jawaban' => 0,
                        'penjelasan' => 'Produk hortikultura bersifat perishable dan tetap melakukan respirasi setelah dipanen karena masih merupakan jaringan hidup secara metabolik.',
                    ],
                    [
                        'pertanyaan' => 'Manakah tanda kerusakan yang disebutkan dalam prosedur percobaan ini sebagai indikasi sampel tidak layak pakai?',
                        'opsi' => ['Plastik berkeringat dan buah/sayur berakar/busuk', 'Warna sayur menjadi lebih hijau', 'Berat sayur bertambah', 'Kemasan menjadi lebih tebal'],
                        'jawaban' => 0,
                        'penjelasan' => 'Prosedur menyebutkan pengamatan dilakukan hingga sampel tidak dapat dipakai lagi, misalnya plastik berkeringat atau buah/sayur berakar/busuk.',
                    ],
                ],
            ],
            // Objek 3
            [
                'judul' => 'Perbandingan Cara Pengemasan dan Penyimpanan Produk pada Pasar Tradisional dengan Mini Market/Toko',
                'tujuan' => [
                    'Mengamati dan membandingkan cara pengemasan dan penyimpanan produk pada pasar tradisional dengan mini market/toko sebagai bahan pertimbangan dalam memilih dan membeli produk',
                ],
                'pendahuluan' => 'Cara pengemasan dan penyimpanan produk pangan dapat berbeda antara pasar tradisional dan minimarket/toko modern, yang berdampak pada mutu, keamanan, dan daya simpan produk yang diterima konsumen. Observasi lapangan diperlukan untuk memahami praktik pengemasan dan penyimpanan yang diterapkan di kedua jenis tempat penjualan tersebut.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Praktik Pengemasan dan Penyimpanan pada Ritel Modern dan Tradisional', 'isi' => 'Minimarket/toko umumnya menerapkan sistem penyimpanan dengan kontrol suhu dan kemasan yang lebih standar, sedangkan pasar tradisional cenderung menggunakan kemasan sederhana dengan kontrol suhu yang minim.'],
                    ['judul' => 'Dampak Cara Pengemasan terhadap Mutu dan Keamanan Produk', 'isi' => 'Perbedaan cara pengemasan dan penyimpanan dapat memengaruhi risiko kontaminasi, kerusakan fisik, dan masa simpan produk yang dijual kepada konsumen.'],
                ],
                'alat' => ['Peralatan tulis/dokumentasi (kamera, buku catatan)'],
                'bahan' => ['Tidak ada bahan khusus — kegiatan observasi/wawancara lapangan'],
                'prosedur' => [
                    'Tentukan 15 jenis produk yang akan diamati dan dibandingkan.',
                    'Siapkan peralatan tulis untuk mencatat hasil pengamatan secara detail; lakukan wawancara dengan penjual bila diperlukan.',
                    'Bandingkan cara pengemasan dan penyimpanan produk yang telah ditentukan antara pasar tradisional dan mini market/toko.',
                    'Susun pembahasan dan kesimpulan mengenai cara pengemasan dan penyimpanan mana yang lebih baik di antara kedua tempat tersebut.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Berapa jumlah jenis produk yang ditentukan untuk diamati dan dibandingkan pada percobaan ini?',
                        'opsi' => ['5 jenis produk', '10 jenis produk', '15 jenis produk', '20 jenis produk'],
                        'jawaban' => 2,
                        'penjelasan' => 'Prosedur kerja menetapkan 15 jenis produk yang akan diamati dan dibandingkan antara pasar tradisional dengan mini market/toko.',
                    ],
                    [
                        'pertanyaan' => 'Metode apa yang dapat digunakan untuk melengkapi data pengamatan pada percobaan ini?',
                        'opsi' => ['Uji laboratorium', 'Wawancara dengan penjual produk', 'Pengukuran pH', 'Analisis mikroskopis'],
                        'jawaban' => 1,
                        'penjelasan' => 'Prosedur menyarankan untuk melakukan wawancara dengan penjual produk jika diperlukan untuk melengkapi hasil pengamatan.',
                    ],
                    [
                        'pertanyaan' => 'Apa tujuan akhir dari perbandingan cara pengemasan pasar tradisional dan minimarket pada percobaan ini?',
                        'opsi' => ['Menentukan harga produk termurah', 'Sebagai bahan pertimbangan dalam memilih dan membeli produk', 'Menentukan lokasi pasar terbaik', 'Menentukan jenis kemasan yang paling murah'],
                        'jawaban' => 1,
                        'penjelasan' => 'Tujuan percobaan ini adalah mengamati dan membandingkan cara pengemasan dan penyimpanan sebagai bahan pertimbangan dalam memilih dan membeli produk.',
                    ],
                ],
            ],
            // Objek 4
            [
                'judul' => 'Perubahan Karakteristik Produk Kering yang Dikemas dengan Kemasan Kertas',
                'tujuan' => [
                    'Mengetahui perubahan karakteristik produk kering selama penyimpanan dalam berbagai jenis kemasan kertas',
                    'Menentukan jenis kemasan kertas yang paling sesuai untuk produk-produk kering tersebut',
                ],
                'pendahuluan' => 'Produk kering seperti teh, gula, kopi, dan tepung terigu memiliki sifat higroskopis yang mudah menyerap uap air dari lingkungan, sehingga jenis kemasan kertas yang digunakan (kraft, minyak, tissu, roti, karton) dapat memengaruhi perubahan berat dan sifat organoleptik produk selama penyimpanan.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Sifat Higroskopis Produk Kering', 'isi' => 'Bahan pangan kering cenderung menyerap kelembaban dari lingkungan sekitarnya, yang dapat menyebabkan penggumpalan, perubahan tekstur, dan penurunan mutu bila kemasan tidak mampu menahan uap air dengan baik.'],
                    ['judul' => 'Karakteristik Berbagai Jenis Kemasan Kertas', 'isi' => 'Kertas kraft, minyak, tissu, roti, dan karton memiliki ketebalan dan sifat permeabilitas yang berbeda terhadap uap air, sehingga menghasilkan tingkat perlindungan yang berbeda terhadap produk kering yang dikemas.'],
                ],
                'alat' => ['Gunting, penggaris, selotip', 'Timbangan analitik, kompor, panci, sendok'],
                'bahan' => ['Teh, gula, kopi, tepung terigu', 'Kertas kraft, kertas minyak, kertas tissu, kertas roti, karton'],
                'prosedur' => [
                    'Potong kertas kemasan dengan ukuran 10cm x 10cm dan timbang beratnya.',
                    'Timbang 10-20 gram bahan (teh, gula, kopi, atau tepung terigu) dan amati sifat organoleptiknya.',
                    'Bungkus bahan menggunakan kertas yang telah dipotong, simpan di tempat kering selama 1 minggu.',
                    'Amati perubahan berat setiap hari dan perubahan sifat organoleptik setelah 1 minggu penyimpanan.',
                    'Khusus untuk teh dan kopi, lakukan penyeduhan dengan air mendidih selama 2-3 menit dan bandingkan hasilnya sebelum-sesudah penyimpanan.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Berapa ukuran kertas kemasan yang dipotong dan ditimbang pada percobaan ini?',
                        'opsi' => ['5cm x 5cm', '10cm x 10cm', '15cm x 15cm', '20cm x 20cm'],
                        'jawaban' => 1,
                        'penjelasan' => 'Prosedur meminta kertas dipotong dengan ukuran 10cm x 10cm sebelum ditimbang dan digunakan untuk membungkus sampel.',
                    ],
                    [
                        'pertanyaan' => 'Mengapa produk kering seperti gula dan tepung terigu mudah mengalami perubahan mutu selama penyimpanan?',
                        'opsi' => ['Karena mengandung banyak lemak', 'Karena bersifat higroskopis dan mudah menyerap uap air', 'Karena berwarna terang', 'Karena mudah terbakar'],
                        'jawaban' => 1,
                        'penjelasan' => 'Produk kering memiliki sifat higroskopis yang mudah menyerap kelembaban dari lingkungan, sehingga dapat mengalami penggumpalan atau perubahan tekstur.',
                    ],
                    [
                        'pertanyaan' => 'Bagaimana cara menguji perubahan mutu pada sampel teh dan kopi setelah masa penyimpanan?',
                        'opsi' => ['Diseduh dengan air mendidih selama 2-3 menit lalu dibandingkan', 'Dibakar hingga habis', 'Direndam dalam air dingin selama 24 jam', 'Dijemur di bawah sinar matahari'],
                        'jawaban' => 0,
                        'penjelasan' => 'Prosedur khusus untuk teh dan kopi adalah melakukan penyeduhan dengan air mendidih selama 2-3 menit, kemudian dibandingkan sebelum dan sesudah penyimpanan.',
                    ],
                ],
            ],
            // Objek 5
            [
                'judul' => 'Desain Kemasan dan Labelling',
                'tujuan' => [
                    'Mendisain kemasan pangan yang sesuai dengan sifat-sifat pangan yang akan dikemas',
                    'Mendisain kemasan pangan yang sesuai dengan kriteria atau ketentuan yang harus ada pada kemasan',
                ],
                'pendahuluan' => 'Desain kemasan dan label pangan tidak hanya berfungsi melindungi produk, tetapi juga sebagai media informasi dan pemasaran yang harus memenuhi ketentuan pelabelan pangan yang berlaku, seperti nama produk, komposisi, berat bersih, tanggal kedaluwarsa, dan identitas produsen.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Prinsip Desain Kemasan Pangan', 'isi' => 'Desain kemasan harus mempertimbangkan sifat fisik dan kimia produk yang dikemas, misalnya kadar air dan kepekaan terhadap cahaya atau oksigen, agar mutu produk tetap terjaga selama distribusi dan penyimpanan.'],
                    ['judul' => 'Ketentuan Label Pangan', 'isi' => 'Label kemasan pangan umumnya wajib mencantumkan informasi seperti nama produk, daftar bahan/komposisi, berat/isi bersih, tanggal produksi dan kedaluwarsa, serta identitas produsen sesuai peraturan pelabelan pangan yang berlaku.'],
                ],
                'alat' => ['Alat desain (komputer/gawai)', 'Alat tulis'],
                'bahan' => ['Produk olahan pangan hasil kreasi mahasiswa (bebas dipilih tiap kelompok)'],
                'prosedur' => [
                    'Tentukan produk olahan pangan sesuai dengan kreasi masing-masing kelompok.',
                    'Buat desain kemasan yang sesuai dengan syarat pengemasan pangan, termasuk elemen label yang wajib dicantumkan.',
                    'Presentasikan dan promosikan hasil desain kemasan pada pertemuan praktikum selanjutnya.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Manakah yang termasuk informasi yang umumnya wajib dicantumkan pada label kemasan pangan?',
                        'opsi' => ['Warna favorit produsen', 'Nama produk, komposisi, berat bersih, dan tanggal kedaluwarsa', 'Jumlah karyawan produsen', 'Riwayat pendidikan pemilik usaha'],
                        'jawaban' => 1,
                        'penjelasan' => 'Label kemasan pangan umumnya wajib mencantumkan nama produk, daftar bahan/komposisi, berat/isi bersih, tanggal produksi dan kedaluwarsa, serta identitas produsen.',
                    ],
                    [
                        'pertanyaan' => 'Apa yang perlu dipertimbangkan dalam mendesain kemasan pangan agar mutu produk tetap terjaga?',
                        'opsi' => ['Harga kemasan termurah', 'Sifat fisik dan kimia produk seperti kadar air dan kepekaan terhadap cahaya/oksigen', 'Warna kesukaan konsumen saja', 'Ukuran logo perusahaan'],
                        'jawaban' => 1,
                        'penjelasan' => 'Desain kemasan harus mempertimbangkan sifat fisik dan kimia produk yang dikemas agar mutu produk tetap terjaga selama distribusi dan penyimpanan.',
                    ],
                    [
                        'pertanyaan' => 'Kapan hasil desain kemasan dipresentasikan menurut prosedur praktikum ini?',
                        'opsi' => ['Pada hari yang sama sebelum praktikum dimulai', 'Pada pertemuan praktikum selanjutnya', 'Tidak perlu dipresentasikan', 'Pada akhir semester'],
                        'jawaban' => 1,
                        'penjelasan' => 'Prosedur menyebutkan bahwa hasil desain kemasan akan dipromosikan/dipresentasikan pada pertemuan praktikum selanjutnya.',
                    ],
                ],
            ],
            // Objek 6
            [
                'judul' => 'Kunjungan Industri',
                'tujuan' => [
                    'Mendapatkan pemahaman langsung mengenai proses produksi, teknologi, dan praktik terbaik dalam industri pengemasan/pengolahan pangan',
                ],
                'pendahuluan' => 'Kunjungan industri memberikan pengalaman belajar langsung bagi mahasiswa untuk mengamati penerapan teknologi pengemasan dan penyimpanan produk pangan pada skala industri, sehingga mahasiswa dapat membandingkan antara teori yang dipelajari di laboratorium dengan praktik nyata di lapangan.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Penerapan Teknologi Pengemasan Skala Industri', 'isi' => 'Industri pangan skala besar menerapkan teknologi pengemasan dan sistem penyimpanan yang lebih kompleks dibandingkan skala laboratorium, mencakup otomasi proses, kontrol mutu, dan standar keamanan pangan.'],
                    ['judul' => 'Manfaat Kunjungan Industri bagi Pembelajaran', 'isi' => 'Kunjungan industri memberikan wawasan praktis mengenai alur produksi, distribusi, dan penerapan standar mutu yang melengkapi pemahaman teoritis mahasiswa yang diperoleh di perkuliahan dan laboratorium.'],
                ],
                'alat' => ['Kamera/alat dokumentasi'],
                'bahan' => ['Tidak ada bahan khusus — kegiatan kunjungan lapangan'],
                'prosedur' => [
                    'Kunjungan dilakukan ke PT. Indofood CBP Sukses Makmur Jambi dan Bulog Divisi Regional Jambi.',
                    'Amati dan catat proses produksi, teknologi pengemasan, dan sistem penyimpanan yang diterapkan di lokasi kunjungan.',
                    'Lakukan wawancara/diskusi dengan pihak industri terkait praktik terbaik yang diterapkan.',
                    'Susun laporan hasil kunjungan industri berupa deskripsi proses dan pembahasan perbandingan dengan teori yang telah dipelajari.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Sebutkan salah satu lokasi kunjungan industri pada praktikum ini.',
                        'opsi' => ['PT. Indofood CBP Sukses Makmur Jambi', 'Pabrik gula di Jawa Timur', 'PT. Unilever Indonesia', 'Pabrik semen di Padang'],
                        'jawaban' => 0,
                        'penjelasan' => 'Lokasi kunjungan industri yang ditentukan pada modul ini adalah PT. Indofood CBP Sukses Makmur Jambi dan Bulog Divisi Regional Jambi.',
                    ],
                    [
                        'pertanyaan' => 'Apa manfaat utama kunjungan industri bagi mahasiswa dalam praktikum ini?',
                        'opsi' => ['Mendapatkan libur dari perkuliahan', 'Memperoleh pemahaman langsung mengenai proses produksi, teknologi, dan praktik terbaik industri', 'Mengurangi jumlah tugas laporan', 'Mendapatkan pekerjaan langsung setelah lulus'],
                        'jawaban' => 1,
                        'penjelasan' => 'Tujuan kunjungan industri adalah untuk mendapatkan pemahaman langsung mengenai proses produksi, teknologi, dan praktik terbaik dalam industri tersebut.',
                    ],
                    [
                        'pertanyaan' => 'Apa yang perlu disusun mahasiswa setelah melakukan kunjungan industri?',
                        'opsi' => ['Tidak perlu menyusun apa pun', 'Laporan hasil kunjungan berupa deskripsi proses dan pembahasan perbandingan dengan teori', 'Hanya foto dokumentasi tanpa laporan', 'Surat izin kunjungan baru'],
                        'jawaban' => 1,
                        'penjelasan' => 'Setelah kunjungan, mahasiswa perlu menyusun laporan hasil kunjungan industri berupa deskripsi proses dan pembahasan perbandingan dengan teori yang telah dipelajari.',
                    ],
                ],
            ],
        ];
    }

    protected function praktikumMikrobiologiPangan(): array
    {
        return [
            // Objek 1 - I. Uji Kontaminasi Pekerja
            [
                'judul' => 'Uji Kontaminasi Pekerja',
                'tujuan' => [
                    'Mahasiswa dapat mengetahui cemaran yang terdapat pada bagian tubuh manusia',
                ],
                'pendahuluan' => 'Dalam proses pengolahan makanan sering terjadi kontaminasi atau pencemaran yang dapat berasal dari udara, peralatan, ruangan, ataupun pekerja. Salah satu sumber kontaminasi terbesar berasal dari pekerja, yang dapat menularkan mikroorganisme patogen melalui tangan, kaki, rambut, mulut, kulit, maupun pakaian selama proses pengolahan pangan. Sanitasi pekerja berperan penting dalam mencegah kontaminasi makanan.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Jenis dan Sumber Kontaminasi Pangan', 'isi' => 'Kontaminasi pangan dikelompokkan menjadi pencemaran mikroba (bakteri, jamur, kapang, virus), pencemaran fisik (rambut, debu, tanah), dan pencemaran kimia (pupuk, pestisida, merkuri, kadmium, arsen), dengan pekerja sebagai salah satu sumber kontaminasi terbesar.'],
                    ['judul' => 'Jalur Kontaminasi dari Pekerja', 'isi' => 'Kontaminasi yang berasal dari pekerja dapat melalui tangan, kaki, rambut, mulut, kulit, maupun pakaian yang digunakan dalam proses pengolahan, dan mikroorganisme patogen dari manusia dapat menimbulkan penyakit yang ditularkan melalui makanan.'],
                ],
                'alat' => ['4 cawan petri ukuran sedang', 'Pinset', 'Inkubator'],
                'bahan' => ['PCA steril'],
                'prosedur' => [
                    'Siapkan 2 cawan petri berisi PCA steril untuk setiap kelompok, beri tanda nama contoh yang akan diuji.',
                    'Cabut satu helai rambut menggunakan pinset, usap dengan swab, kemudian usapkan swab pada cawan berisi PCA, tutup cawan.',
                    'Usapkan swab pada jari tangan, kemudian usapkan pada cawan PCA yang lain, tutup cawan.',
                    'Inkubasikan kedua cawan secara terbalik pada suhu ±30°C selama 2-3 hari.',
                    'Amati pertumbuhan mikroba pada masing-masing cawan, nyatakan dengan skala - sampai +++.',
                    'Berikan evaluasi tentang kemungkinan rambut dan kebersihan tangan sebagai salah satu sumber kontaminasi.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Bagian tubuh manusia mana saja yang diuji sebagai sumber cemaran pada praktikum ini?',
                        'opsi' => ['Rambut dan jari tangan', 'Mata dan telinga', 'Gigi dan lidah', 'Kuku kaki'],
                        'jawaban' => 0,
                        'penjelasan' => 'Sampel yang diambil pada praktikum ini adalah dari bagian rambut dan jari tangan yang diusapkan pada media PCA.',
                    ],
                    [
                        'pertanyaan' => 'Media apa yang digunakan untuk menumbuhkan cemaran mikroba dari tubuh praktikan?',
                        'opsi' => ['NA (Nutrient Agar)', 'PDA (Potato Dextrose Agar)', 'PCA (Plate Count Agar)', 'MRS Agar'],
                        'jawaban' => 2,
                        'penjelasan' => 'Sampel dari rambut dan jari tangan ditumbuhkan pada media PCA (Plate Count Agar) untuk mengetahui ada tidaknya cemaran mikroba.',
                    ],
                    [
                        'pertanyaan' => 'Pada suhu berapa cawan diinkubasikan pada praktikum uji kontaminasi pekerja ini?',
                        'opsi' => ['±10°C', '±20°C', '±30°C', '±40°C'],
                        'jawaban' => 2,
                        'penjelasan' => 'Cawan diinkubasikan pada suhu ±30°C selama 2-3 hari dengan posisi cawan terbalik.',
                    ],
                ],
            ],
            // Objek 2 - II. Uji Pengaruh Sanitasi Terhadap Tingkat Kebersihan Tangan Pekerja
            [
                'judul' => 'Uji Pengaruh Sanitasi Terhadap Tingkat Kebersihan Tangan Pekerja',
                'tujuan' => [
                    'Mahasiswa dapat mengetahui pengaruh sanitasi terhadap tingkat kebersihan tangan',
                ],
                'pendahuluan' => 'Sanitasi pangan menurut Undang-undang RI No.18 Tahun 2012 didefinisikan sebagai upaya menciptakan dan mempertahankan kondisi pangan yang sehat dan higienis, bebas dari bahaya cemaran biologis, kimia, dan benda lain. Kesehatan dan kebersihan pekerja mempunyai pengaruh besar terhadap mutu produk pangan, karena mikroba patogen pada tangan pekerja dapat dengan mudah berpindah ke makanan yang diolah.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Definisi Sanitasi Pangan', 'isi' => 'Menurut UU RI No.18 Tahun 2012 tentang pangan, sanitasi pangan adalah upaya menciptakan dan mempertahankan kondisi pangan yang sehat dan higienis yang bebas dari bahaya cemaran biologis, kimia, dan benda lain.'],
                    ['judul' => 'Mikroba Patogen pada Tangan Pekerja', 'isi' => 'Mikroba berbahaya seperti Staphylococcus aureus, Salmonella, Clostridium perfringens, dan Streptococcus dapat ditularkan melalui kulit, hidung, mulut, dan tenggorokan pekerja, kemudian berpindah ke makanan melalui kebiasaan tangan yang tidak disadari.'],
                ],
                'alat' => ['4 cawan petri ukuran sedang dan bertutup', 'Inkubator'],
                'bahan' => ['Agar PCA cair steril', 'Sabun mandi (batangan/cair)', 'Sabun cuci tangan', 'Hand sanitizer'],
                'prosedur' => [
                    'Siapkan 1 cawan petri berisi PCA steril per kelompok, beri garis lima kuadran.',
                    'Setiap anggota kelompok melakukan perlakuan berbeda pada tangan: belum dicuci, dicuci air kran, dicuci sabun mandi, dicuci sabun cuci tangan, dan dibasuh hand sanitizer.',
                    'Usapkan masing-masing tangan pada kuadran cawan sesuai perlakuan.',
                    'Inkubasikan cawan dengan posisi terbalik pada suhu ±30°C selama 2-3 hari.',
                    'Amati pertumbuhan mikroba pada setiap kuadran, nyatakan dengan skala - sampai +++, dan bahas hasil pengamatan.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Menurut UU RI No.18 Tahun 2012, apa yang dimaksud dengan sanitasi pangan?',
                        'opsi' => ['Proses memasak makanan hingga matang', 'Upaya menciptakan dan mempertahankan kondisi pangan yang sehat dan higienis, bebas cemaran biologis, kimia, dan benda lain', 'Kegiatan mengemas makanan agar tahan lama', 'Proses pendinginan makanan sebelum disajikan'],
                        'jawaban' => 1,
                        'penjelasan' => 'UU RI No.18 Tahun 2012 mendefinisikan sanitasi pangan sebagai upaya menciptakan dan mempertahankan kondisi pangan yang sehat dan higienis yang bebas dari bahaya cemaran biologis, kimia, dan benda lain.',
                    ],
                    [
                        'pertanyaan' => 'Berapa jumlah perlakuan berbeda yang diuji pada tangan pekerja dalam praktikum ini?',
                        'opsi' => ['3 perlakuan', '4 perlakuan', '5 perlakuan', '6 perlakuan'],
                        'jawaban' => 2,
                        'penjelasan' => 'Terdapat 5 perlakuan yang diuji: belum dicuci, dicuci air kran, dicuci sabun mandi, dicuci sabun cuci tangan, dan dibasuh hand sanitizer.',
                    ],
                    [
                        'pertanyaan' => 'Manakah mikroba berbahaya yang disebutkan dapat ditularkan melalui kulit dan tangan pekerja?',
                        'opsi' => ['Saccharomyces cerevisiae', 'Staphylococcus aureus', 'Lactobacillus', 'Rhizopus oryzae'],
                        'jawaban' => 1,
                        'penjelasan' => 'Staphylococcus aureus merupakan salah satu mikroba berbahaya yang dapat ditularkan melalui kulit, hidung, mulut, dan tenggorokan pekerja.',
                    ],
                ],
            ],
            // Objek 3 - III. Uji Kontaminasi Bahan Pangan
            [
                'judul' => 'Uji Kontaminasi Bahan Pangan',
                'tujuan' => [
                    'Mahasiswa dapat mengetahui pengaruh sanitasi terhadap kontaminasi bahan pangan',
                ],
                'pendahuluan' => 'Bahan makanan selain merupakan sumber gizi bagi manusia, juga merupakan sumber makanan bagi mikroorganisme. Terdapat tiga jalur utama mikroorganisme dapat mengkontaminasi bahan pangan, yaitu melalui bahan baku dan ingredient, pada saat pengolahan pangan, dan dari lingkungan pengolahan. Penanganan bahan pangan segar yang tepat sebelum diolah sangat penting untuk mempertahankan mutu dan sanitasi bahan.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Jalur Kontaminasi Mikroorganisme pada Bahan Pangan', 'isi' => 'Terdapat tiga jalur utama kontaminasi bahan pangan, yaitu melalui bahan baku dan ingredient, pada saat proses pengolahan pangan, dan dari lingkungan pengolahan.'],
                    ['judul' => 'Dampak Pertumbuhan Mikroorganisme pada Bahan Pangan', 'isi' => 'Pertumbuhan mikroorganisme dalam bahan pangan dapat memberikan efek menguntungkan seperti perbaikan gizi/daya cerna/daya simpan, namun juga dapat menyebabkan perubahan fisik atau kimia yang tidak diinginkan hingga menyebabkan pembusukan.'],
                ],
                'alat' => ['Cawan petri', 'Tabung reaksi', 'Inkubator', 'Autoklaf', 'Erlenmeyer', 'Pipet ukur 10 mL dan 1 mL', 'Bulb'],
                'bahan' => ['Agar PCA steril', 'Larutan pengencer steril', 'Sayur belum dicuci, sayur dicuci-rendam, sayur dicuci air mengalir, sayur dicuci kemudian ditiriskan', 'Kapas, aluminium foil, plastik wrap'],
                'prosedur' => [
                    'Buat larutan stok dengan mencampurkan 1 mL/gram sampel dengan 9 mL larutan pengencer, goyangkan hingga tercampur rata (pengenceran 10⁻¹).',
                    'Ambil 1 mL larutan pengenceran ke-1, tambahkan ke larutan pengencer berikutnya (pengenceran 10⁻²).',
                    'Masukkan 1 mL dari masing-masing pengenceran ke cawan petri, tambahkan 15-20 mL media PCA steril (metode tuang/pour plate), putar cawan membentuk angka 8.',
                    'Inkubasikan pada suhu ±30°C selama 2-3 hari.',
                    'Amati perkembangan mikroba dan hitung jumlah koloni mengikuti standar USDA (rentang jumlah koloni 30-300) menggunakan rumus N = ΣC / [(1×n1) + (0,1×n2) × d].',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Berapa jumlah jalur utama yang dapat digunakan mikroorganisme untuk mengkontaminasi bahan pangan?',
                        'opsi' => ['Dua jalur', 'Tiga jalur', 'Empat jalur', 'Lima jalur'],
                        'jawaban' => 1,
                        'penjelasan' => 'Terdapat tiga jalur kontaminasi bahan pangan yaitu melalui bahan baku dan ingredient, pada pengolahan pangan, dan dari lingkungan pengolahan.',
                    ],
                    [
                        'pertanyaan' => 'Metode penanaman apa yang digunakan pada praktikum uji kontaminasi bahan pangan ini?',
                        'opsi' => ['Streak plate', 'Pour plate (metode tuang)', 'Spread plate', 'Slant culture'],
                        'jawaban' => 1,
                        'penjelasan' => 'Sampel pengenceran dimasukkan ke cawan petri kemudian ditambahkan media PCA steril menggunakan metode tuang (pour plate).',
                    ],
                    [
                        'pertanyaan' => 'Berapa rentang jumlah koloni yang dianggap valid untuk dihitung menurut standar USDA pada praktikum ini?',
                        'opsi' => ['1-10 koloni', '10-25 koloni', '30-300 koloni', '300-1000 koloni'],
                        'jawaban' => 2,
                        'penjelasan' => 'Penghitungan jumlah koloni mengikuti standar USDA dengan rentang jumlah koloni 30-300 yang dianggap valid untuk dihitung.',
                    ],
                ],
            ],
            // Objek 4 - IV. Uji Pengaruh Sanitasi Terhadap Kontaminasi Alat
            [
                'judul' => 'Uji Pengaruh Sanitasi Terhadap Kontaminasi Alat',
                'tujuan' => [
                    'Mahasiswa dapat mengetahui pengaruh sanitasi terhadap kontaminasi alat pengolahan',
                ],
                'pendahuluan' => 'Sanitasi dalam industri pangan merupakan kegiatan yang mengarah pada pemeliharaan kondisi sehat, meliputi bebas kontaminan dan bebas dari faktor yang dapat memicu keadaan tidak sehat. Penggunaan wadah dan alat-alat pengolahan yang kotor dan mengandung mikroba dalam jumlah tinggi merupakan salah satu sumber kontaminasi utama dalam pengolahan pangan, sehingga pencucian peralatan yang baik sangat penting untuk mencegah kontaminasi makanan.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Peran Alat Pengolahan dalam Food Hygiene', 'isi' => 'Penggunaan wadah dan alat pengolahan yang kotor dan mengandung mikroba dalam jumlah tinggi merupakan salah satu sumber kontaminasi utama dalam pengolahan pangan, sehingga alat yang aman, bersih, dan bebas kontaminasi menjadi bagian penting dari prinsip food hygiene.'],
                    ['judul' => 'Faktor Penyebab Keracunan Makanan Terkait Alat', 'isi' => 'Higiene perorangan yang buruk, cara penanganan makanan yang tidak sehat, dan perlengkapan pengolahan makanan yang tidak bersih merupakan faktor-faktor yang dapat menyebabkan keracunan makanan.'],
                ],
                'alat' => ['2 cawan petri steril', 'Inkubator', 'Sendok', 'Pisau'],
                'bahan' => ['Agar PCA steril', 'Larutan pengencer', 'Sabun cuci (sunlight/mama lemon)'],
                'prosedur' => [
                    'Dinginkan cawan petri berisi agar PCA hingga suhu ruang (30-35°C).',
                    'Oleskan permukaan alat (pisau/sendok, baik sebelum maupun sesudah dicuci sabun) pada cawan berisi media agar selama kurang lebih 4 detik.',
                    'Inkubasikan pada suhu ±30°C selama 1-2 hari.',
                    'Hitung jumlah koloni yang tumbuh.',
                    'Amati pertumbuhan mikroba dan berikan pembahasan; nyatakan pertumbuhan dengan skala - sampai +++.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Berapa lama alat (pisau/sendok) dioleskan pada permukaan media agar dalam praktikum ini?',
                        'opsi' => ['1 detik', '4 detik', '10 detik', '30 detik'],
                        'jawaban' => 1,
                        'penjelasan' => 'Prosedur meminta permukaan alat dioleskan pada cawan berisi media agar selama kurang lebih 4 detik.',
                    ],
                    [
                        'pertanyaan' => 'Apa salah satu faktor yang disebutkan dapat menyebabkan keracunan makanan terkait alat pengolahan?',
                        'opsi' => ['Perlengkapan pengolahan makanan yang tidak bersih', 'Warna alat yang mencolok', 'Bahan alat dari stainless steel', 'Ukuran alat yang besar'],
                        'jawaban' => 0,
                        'penjelasan' => 'Perlengkapan pengolahan makanan yang tidak bersih merupakan salah satu faktor yang dapat menyebabkan keracunan makanan, selain higiene perorangan yang buruk dan cara penanganan makanan yang tidak sehat.',
                    ],
                    [
                        'pertanyaan' => 'Alat apa saja yang dibandingkan kontaminasinya sebelum dan sesudah dicuci pada praktikum ini?',
                        'opsi' => ['Sendok dan pisau', 'Talenan dan gelas', 'Mangkuk dan piring', 'Panci dan wajan'],
                        'jawaban' => 0,
                        'penjelasan' => 'Praktikum ini membandingkan pertumbuhan mikroba pada pisau dan sendok, baik yang belum dicuci maupun yang sudah dicuci dengan sabun dan air.',
                    ],
                ],
            ],
            // Objek 5 - V. Fermentasi Alkohol
            [
                'judul' => 'Fermentasi Alkohol',
                'tujuan' => [
                    'Mahasiswa dapat mengetahui proses pembuatan produk fermentasi alkohol serta mikroorganisme yang terlibat dalam proses fermentasi',
                ],
                'pendahuluan' => 'Fermentasi merupakan proses perubahan kimia pada substrat organik melalui aktivitas enzim yang dihasilkan oleh mikroorganisme, dan dapat berlangsung secara spontan maupun tidak spontan (menggunakan starter/ragi). Fermentasi alkohol merupakan reaksi perubahan glukosa menjadi etanol dan karbondioksida yang digolongkan sebagai respirasi anaerob, dengan Saccharomyces cerevisiae sebagai mikroorganisme utama yang berperan dalam pembuatan tape, roti, atau minuman keras.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Prinsip Fermentasi Spontan dan Tidak Spontan', 'isi' => 'Fermentasi spontan berlangsung tanpa penambahan mikroorganisme dalam bentuk starter atau ragi, sedangkan fermentasi tidak spontan menggunakan starter atau ragi yang ditambahkan dalam proses pembuatannya.'],
                    ['judul' => 'Mekanisme Fermentasi Alkohol', 'isi' => 'Fermentasi alkohol adalah reaksi perubahan glukosa menjadi etanol dan karbondioksida yang digolongkan sebagai respirasi anaerob, dengan khamir Saccharomyces cerevisiae sebagai mikroorganisme utama karena mampu memproduksi alkohol dalam jumlah besar.'],
                ],
                'alat' => ['Menyesuaikan produk fermentasi yang dipilih (proyek mandiri kelompok)'],
                'bahan' => ['Menyesuaikan produk fermentasi yang dipilih (proyek mandiri kelompok)'],
                'prosedur' => [
                    'Setiap kelompok memilih secara bebas jenis produk fermentasi alkohol yang akan dikerjakan sebagai proyek mandiri.',
                    'Lakukan proses pembuatan produk fermentasi alkohol sesuai jenis produk yang dipilih.',
                    'Lakukan pengujian organoleptik pada produk yang dihasilkan.',
                    'Ukur kadar asam/pH produk fermentasi yang dihasilkan.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Apa yang dihasilkan dari reaksi fermentasi alkohol pada glukosa?',
                        'opsi' => ['Asam laktat dan air', 'Etanol dan karbondioksida', 'Protein dan lemak', 'Oksigen dan air'],
                        'jawaban' => 1,
                        'penjelasan' => 'Fermentasi alkohol merupakan reaksi perubahan glukosa menjadi etanol (etil alkohol) dan karbondioksida.',
                    ],
                    [
                        'pertanyaan' => 'Mikroorganisme apa yang berperan utama dalam fermentasi alkohol?',
                        'opsi' => ['Saccharomyces cerevisiae', 'Lactobacillus bulgaricus', 'Rhizopus oligosporus', 'Acetobacter xylinum'],
                        'jawaban' => 0,
                        'penjelasan' => 'Saccharomyces cerevisiae (ragi) berperan utama dalam fermentasi alkohol untuk pembuatan tape, roti, atau minuman keras.',
                    ],
                    [
                        'pertanyaan' => 'Fermentasi alkohol digolongkan sebagai jenis respirasi apa?',
                        'opsi' => ['Respirasi aerob', 'Respirasi anaerob', 'Fotosintesis', 'Respirasi eksternal'],
                        'jawaban' => 1,
                        'penjelasan' => 'Fermentasi alkohol digolongkan sebagai respirasi anaerob karena berlangsung tanpa memerlukan oksigen.',
                    ],
                ],
            ],
            // Objek 6 - VI. Fermentasi Asam Laktat
            [
                'judul' => 'Fermentasi Asam Laktat',
                'tujuan' => [
                    'Mahasiswa dapat mengetahui proses pembuatan produk fermentasi asam laktat serta mikroorganisme yang terlibat dalam proses fermentasi',
                ],
                'pendahuluan' => 'Mikroorganisme yang menguntungkan dapat digunakan dalam produk pangan, contohnya pertumbuhan sel mikroorganisme dalam pengolahan kedelai menjadi tempe yang menghasilkan senyawa seperti asam laktat, asam asetat, asam amino, dan bakteriosin. Bakteri asam laktat memiliki kemampuan memetabolisme karbohidrat dan menghasilkan asam laktat dalam jumlah besar, serta berperan sebagai pangan fungsional dan pengawet alami.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Peran Bakteri Asam Laktat dalam Pangan', 'isi' => 'Bakteri asam laktat memiliki kemampuan memetabolisme karbohidrat menjadi asam laktat dalam jumlah besar dan berkontribusi besar sebagai pangan fungsional maupun pengawet alami (biopreservatif) dalam industri pangan.'],
                    ['judul' => 'Manfaat Fungsional Bakteri Asam Laktat', 'isi' => 'Selain sebagai pengawet alami, bakteri asam laktat memiliki fungsi probiotik yang dapat meningkatkan kesehatan saluran pencernaan, serta mampu menghasilkan amilase ekstraseluler untuk memfermentasi pati langsung menjadi asam laktat.'],
                ],
                'alat' => ['Menyesuaikan produk fermentasi yang dipilih (proyek mandiri kelompok)'],
                'bahan' => ['Menyesuaikan produk fermentasi yang dipilih (proyek mandiri kelompok)'],
                'prosedur' => [
                    'Setiap kelompok memilih secara bebas jenis produk fermentasi asam laktat yang akan dikerjakan sebagai proyek mandiri.',
                    'Lakukan proses pembuatan produk fermentasi asam laktat sesuai jenis produk yang dipilih.',
                    'Lakukan pengujian organoleptik pada produk yang dihasilkan.',
                    'Ukur kadar asam/pH produk fermentasi yang dihasilkan.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Apa kemampuan utama bakteri asam laktat dalam proses fermentasi pangan?',
                        'opsi' => ['Memetabolisme karbohidrat menghasilkan asam laktat dalam jumlah besar', 'Menghasilkan etanol dalam jumlah besar', 'Menghasilkan gas oksigen', 'Menguraikan lemak menjadi gliserol'],
                        'jawaban' => 0,
                        'penjelasan' => 'Bakteri asam laktat memiliki kemampuan untuk memetabolisme karbohidrat dan menghasilkan asam laktat dalam jumlah besar.',
                    ],
                    [
                        'pertanyaan' => 'Selain sebagai pengawet alami, apa fungsi lain dari bakteri asam laktat bagi kesehatan?',
                        'opsi' => ['Fungsi probiotik yang meningkatkan kesehatan saluran pencernaan', 'Menyebabkan keracunan makanan', 'Meningkatkan kadar gula darah', 'Mempercepat pembusukan makanan'],
                        'jawaban' => 0,
                        'penjelasan' => 'Bakteri asam laktat memiliki fungsi probiotik yang dapat meningkatkan kesehatan saluran pencernaan.',
                    ],
                    [
                        'pertanyaan' => 'Pengujian apa yang dilakukan pada produk fermentasi asam laktat menurut instruksi praktikum ini?',
                        'opsi' => ['Uji organoleptik dan kadar asam/pH', 'Uji kadar lemak dan protein', 'Uji kandungan logam berat', 'Uji ketahanan terhadap suhu tinggi'],
                        'jawaban' => 0,
                        'penjelasan' => 'Instruksi praktikum menyebutkan pengujian yang dilakukan pada produk adalah uji organoleptik dan kadar asam/pH.',
                    ],
                ],
            ],
            // Objek 7 - VII. Isolasi Mikroorganisme Fermentasi Pangan
            [
                'judul' => 'Isolasi Mikroorganisme Fermentasi Pangan',
                'tujuan' => [
                    'Mahasiswa mampu melakukan isolasi mikroorganisme fermentasi pangan',
                    'Mahasiswa mampu mengetahui karakteristik mikroorganisme fermentasi pangan',
                    'Mahasiswa mampu mengetahui jenis dan macam mikroorganisme fermentasi pangan',
                ],
                'pendahuluan' => 'Mikroorganisme yang tergolong kapang, khamir, dan bakteri dapat ditemukan hampir di mana-mana, termasuk pada bahan pangan hasil fermentasi seperti tape dan tempe. Berdasarkan morfologinya, bakteri dapat berbentuk batang, bulat, atau spiral; kapang umumnya berupa jamur multiseluler; sedangkan khamir merupakan jamur uniseluler berbentuk ovoid atau spheroid, yang menjadi dasar pengklasifikasian mikroorganisme.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Klasifikasi Morfologi Mikroorganisme', 'isi' => 'Bakteri, kapang, dan khamir memiliki ciri morfologi berbeda yang dapat diamati secara langsung: bakteri berbentuk batang/bulat/spiral, kapang sebagai jamur multiseluler berbentuk askus/basidium, dan khamir sebagai jamur uniseluler berbentuk ovoid/spheroid.'],
                    ['judul' => 'Prinsip Pewarnaan Gram', 'isi' => 'Pewarnaan Gram digunakan untuk mengamati bentuk sel, sifat pewarnaan, dan pengelompokan sel bakteri, menggunakan rangkaian reagen kristal violet, mordant (lugol\'s iodine), larutan pemucat, dan counterstain (safranin).'],
                ],
                'alat' => ['Pipet, tabung reaksi, cawan petri', 'Gelas obyek, gelas penutup', 'Mortar dan pestle', 'Mikroskop', 'Inkubator'],
                'bahan' => ['Kultur (sesuai produk fermentasi yang dikerjakan pada objek Fermentasi Alkohol dan Fermentasi Asam Laktat)', 'Media PCA, PDA, NA, larutan pengencer steril', 'Larutan-larutan pewarnaan Gram (mordant/lugol\'s iodine, kristal violet, etanol 96%, safranin)'],
                'prosedur' => [
                    'Hancurkan bahan pangan; jika berbentuk padatan timbang 2,5 gram, jika berbentuk cairan ukur 2,5 mL.',
                    'Tambahkan larutan pengencer (akuades) sebanyak 22,5 mL, kemudian vortex (pengenceran 10⁻¹).',
                    'Pipet 1 mL dan lakukan inokulasi pada cawan petri, tuang sesuai media identifikasi, inkubasi selama 48 jam, amati dan catat hasilnya.',
                    'Buat preparat ulas (smear) dari cairan makanan/kultur yang dipilih.',
                    'Lakukan pewarnaan Gram: teteskan kristal violet (±1 menit), cuci, teteskan mordant/lugol\'s iodine (±1 menit), cuci, beri larutan pemucat (etanol 96%/aseton) tetes demi tetes, cuci, teteskan safranin (±45 detik), cuci, keringkan.',
                    'Amati bentuk sel, sifat pewarnaan, dan pengelompokan sel di bawah mikroskop, deskripsikan karakteristik koloni (warna, bentuk, elevasi, permukaan).',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Berapa lama waktu tunggu setelah meneteskan kristal violet dalam prosedur pewarnaan Gram?',
                        'opsi' => ['±30 detik', '±1 menit', '±5 menit', '±10 menit'],
                        'jawaban' => 1,
                        'penjelasan' => 'Setelah kristal violet diteteskan sebagai pewarna utama, ditunggu selama ±1 menit sebelum dicuci dengan akuades mengalir.',
                    ],
                    [
                        'pertanyaan' => 'Reagen apa yang berfungsi sebagai counterstain (pewarna kedua) dalam pewarnaan Gram?',
                        'opsi' => ['Kristal violet', 'Lugol\'s iodine', 'Safranin', 'Etanol 96%'],
                        'jawaban' => 2,
                        'penjelasan' => 'Safranin berfungsi sebagai counterstain yang diteteskan setelah proses dekolorisasi dengan larutan pemucat, ditunggu selama ±45 detik.',
                    ],
                    [
                        'pertanyaan' => 'Bagaimana bentuk khas sel khamir yang membedakannya dari kapang dan bakteri?',
                        'opsi' => ['Berbentuk batang atau spiral', 'Berupa jamur uniseluler berbentuk ovoid atau spheroid', 'Berupa jamur multiseluler berbentuk askus/basidium', 'Berbentuk kubus'],
                        'jawaban' => 1,
                        'penjelasan' => 'Khamir merupakan jenis jamur uniseluler yang umumnya berbentuk ovoid (avoid) atau spheroid, berbeda dari kapang yang multiseluler dan bakteri yang berbentuk batang/bulat/spiral.',
                    ],
                ],
            ],
            // Objek 8 - VIII. Isolasi dan Identifikasi Mikroorganisme Pembusuk Pada Pangan
            [
                'judul' => 'Isolasi dan Identifikasi Mikroorganisme Pembusuk Pada Pangan',
                'tujuan' => [
                    'Mahasiswa mampu melakukan isolasi mikroorganisme pembusuk pangan',
                    'Mahasiswa mampu mengidentifikasi mikroorganisme pembusuk pangan',
                ],
                'pendahuluan' => 'Mikroorganisme yang menyebabkan kerusakan atau kebusukan makanan dapat memecah komponen dalam makanan menjadi senyawa yang lebih sederhana sehingga menimbulkan perubahan cita rasa. Salah satu contoh kerja mikroorganisme perusak pangan adalah hidrolisis protein yang menghasilkan bau busuk akibat terbentuknya senyawa seperti amonia dan H2S. Prinsip isolasi mikroba adalah memisahkan satu jenis mikroba dari campuran menggunakan metode cawan gores atau cawan tuang.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Mekanisme Kerusakan Pangan oleh Mikroorganisme', 'isi' => 'Mikroorganisme perusak pangan menghidrolisis protein dalam makanan sehingga menimbulkan bau busuk dan perubahan cita rasa akibat terbentuknya senyawa-senyawa seperti amonia dan H2S.'],
                    ['judul' => 'Prinsip dan Metode Isolasi Mikroba', 'isi' => 'Isolasi mikroba bertujuan memisahkan satu jenis mikroba dari campuran bermacam-macam mikroba, dilakukan melalui metode cawan gores atau cawan tuang berdasarkan prinsip pengenceran untuk memperoleh biakan murni.'],
                ],
                'alat' => ['Pipet, tabung reaksi, cawan petri, erlenmeyer (steril)', 'Inkubator'],
                'bahan' => ['Pangan busuk (nasi basi, buah/sayur busuk, pangan olahan basi)', 'Media NA', 'Larutan pengencer steril'],
                'prosedur' => [
                    'Hancurkan bahan pangan busuk; timbang 2,5 gram (padatan) atau ukur 2,5 mL (cairan), tambahkan larutan pengencer 22,5 mL, vortex (pengenceran 10⁻¹).',
                    'Lanjutkan pengenceran bertingkat hingga 10⁻⁸.',
                    'Ambil 1 mL dari tiga pengenceran terakhir (10⁻⁶, 10⁻⁷, 10⁻⁸), tanam secara pour plate pada medium NA, inkubasi pada suhu 37°C selama 2×24 jam.',
                    'Pilih koloni yang relatif terpisah dan mudah dikenali dari ketiga cawan, lakukan penghitungan koloni.',
                    'Murnikan koloni terpilih ke NA baru menggunakan teknik streak kuadran, inkubasi 1×24 jam; ulangi penggoresan bila belum murni.',
                    'Pindahkan koloni murni ke agar miring, identifikasi kemungkinan mikroorganisme berdasarkan karakteristik koloni (warna, bentuk, elevasi, permukaan).',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Hingga tingkat pengenceran berapa sampel pangan busuk diencerkan pada praktikum ini?',
                        'opsi' => ['10⁻³', '10⁻⁵', '10⁻⁸', '10⁻¹⁰'],
                        'jawaban' => 2,
                        'penjelasan' => 'Prosedur meminta pengenceran bertingkat dilanjutkan sampai 10⁻⁸, dengan tiga pengenceran terakhir (10⁻⁶, 10⁻⁷, 10⁻⁸) yang ditanam pada medium NA.',
                    ],
                    [
                        'pertanyaan' => 'Teknik apa yang digunakan untuk memurnikan koloni terpilih ke media NA baru?',
                        'opsi' => ['Teknik streak kuadran', 'Teknik pour plate langsung', 'Teknik spread plate', 'Teknik agar tegak'],
                        'jawaban' => 0,
                        'penjelasan' => 'Koloni yang terpilih kemudian dimurnikan ke NA baru menggunakan teknik streak kuadran.',
                    ],
                    [
                        'pertanyaan' => 'Senyawa apa yang terbentuk akibat hidrolisis protein oleh mikroorganisme pembusuk yang menimbulkan bau busuk?',
                        'opsi' => ['Amonia dan H2S', 'Etanol dan CO2', 'Asam laktat dan air', 'Glukosa dan fruktosa'],
                        'jawaban' => 0,
                        'penjelasan' => 'Hidrolisis protein oleh mikroorganisme sering mengakibatkan timbulnya bau busuk karena terbentuknya senyawa seperti amonia dan H2S.',
                    ],
                ],
            ],
            // Objek 9 - IX. Aktivitas Antimikroba
            [
                'judul' => 'Aktivitas Antimikroba',
                'tujuan' => [
                    'Mahasiswa mampu melakukan pengujian aktivitas antimikroba',
                    'Mahasiswa mampu mengetahui kemampuan antimikroba pada suatu bahan',
                ],
                'pendahuluan' => 'Aktivitas antimikroba suatu bahan dapat diuji melalui kemampuannya menghambat pertumbuhan mikroorganisme, yang ditandai dengan terbentuknya zona bening (zona hambat) di sekitar bahan uji pada media yang telah ditumbuhi mikroba. Pengujian ini penting untuk mengevaluasi potensi suatu bahan, misalnya minyak esensial, sebagai agen antimikroba alami dalam pengawetan pangan.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Prinsip Uji Aktivitas Antimikroba', 'isi' => 'Aktivitas antimikroba suatu bahan diuji melalui pembentukan zona hambat (zona bening) di sekitar bahan uji pada media yang telah ditumbuhi mikroba, menggunakan metode cakram atau metode sumur.'],
                    ['judul' => 'Metode Cakram dan Metode Sumur', 'isi' => 'Metode cakram menggunakan kertas cakram yang direndam bahan uji dan diletakkan pada permukaan media, sedangkan metode sumur menggunakan lubang pada media agar yang diisi bahan uji; zona hambat pada kedua metode diukur menggunakan jangka sorong.'],
                ],
                'alat' => ['Cawan petri, kertas cakram', 'Inkubator', 'Jangka sorong'],
                'bahan' => ['Pangan busuk (nasi basi, buah/sayur busuk, pangan olahan basi)', 'Media NA', 'Larutan pengencer steril', 'Minyak esensial', 'Alkohol'],
                'prosedur' => [
                    'Rendam kertas cakram pada sampel yang akan diuji aktivitas antimikrobanya.',
                    'Lakukan penanaman spread plate pada pengenceran 10⁻² (dari pengenceran pada objek Isolasi dan Identifikasi Mikroorganisme Pembusuk) pada 2 cawan.',
                    'Pada cawan pertama, letakkan 3-5 kertas cakram pada permukaan media yang sudah mengeras.',
                    'Pada cawan kedua, buat 3-5 lubang sumur menggunakan ujung pipet steril, masukkan sebanyak 0,5 mL sampel ke setiap lubang.',
                    'Inkubasikan kedua cawan selama 24 jam, amati zona bening di sekitar kertas cakram/sumur.',
                    'Ukur zona hambat yang terbentuk menggunakan jangka sorong sebanyak tiga kali pada posisi berbeda, kemudian rata-ratakan nilainya.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Apa yang menandakan adanya aktivitas antimikroba pada suatu bahan yang diuji?',
                        'opsi' => ['Terbentuknya zona bening (zona hambat) di sekitar bahan uji', 'Media menjadi berwarna gelap', 'Kertas cakram menjadi larut', 'Koloni tumbuh lebih cepat'],
                        'jawaban' => 0,
                        'penjelasan' => 'Aktivitas antimikroba diamati melalui pembentukan zona bening (zona hambat) di sekitar kertas cakram atau sumur yang berisi bahan uji.',
                    ],
                    [
                        'pertanyaan' => 'Alat apa yang digunakan untuk mengukur zona hambat pada praktikum ini?',
                        'opsi' => ['Mikrometer sekrup', 'Jangka sorong', 'Termometer', 'Spektrofotometer'],
                        'jawaban' => 1,
                        'penjelasan' => 'Zona hambat yang terbentuk diukur menggunakan jangka sorong sebanyak tiga kali pada posisi berbeda kemudian dirata-ratakan.',
                    ],
                    [
                        'pertanyaan' => 'Berapa volume sampel yang dimasukkan ke setiap lubang pada metode sumur?',
                        'opsi' => ['0,1 mL', '0,5 mL', '1 mL', '2 mL'],
                        'jawaban' => 1,
                        'penjelasan' => 'Pada metode sumur, sebanyak 0,5 mL sampel dimasukkan ke setiap lubang yang telah dibuat menggunakan ujung pipet steril.',
                    ],
                ],
            ],
        ];
    }
}
