<?php

namespace Database\Seeders;

use App\Models\Matakuliah;
use App\Models\Materi;
use App\Models\Praktikum;
use App\Models\Prodi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Mengisi konten untuk mata kuliah Praktikum Kimia dan Biokimia
 * (Semester 3, prodi THP), mengikuti pola ThpSemester2KontenSeeder.
 *
 * Sumber materi: Penuntun Praktikum Kimia dan Biokimia 2023, PS Teknologi
 * Hasil Pertanian, Fakultas Pertanian, Universitas Jambi.
 * Penyusun: Dr. Fitry Tafzi, Ulyarti, Silvi Leila Rahmi, Mursyid, Rahayu Suseno.
 *
 * Pemisahan konten:
 * - Materi    = teori saja (Pendahuluan + Tinjauan Pustaka / Latar Belakang Teori).
 * - Praktikum = pelaksanaan (Tujuan, Alat, Bahan, Prosedur/Langkah Kerja, Kuis).
 *
 * Setiap Objek pada seeder ini merepresentasikan satu BAB pada buku
 * penuntun (BAB berisi beberapa sub-percobaan yang dirangkum menjadi satu
 * rangkaian alat/bahan/prosedur, karena satu BAB = satu pertemuan praktikum).
 *
 * PENTING: sesuaikan $matakuliahSlug di method run() dengan slug asli
 * yang sudah tersimpan di tabel matakuliahs untuk mata kuliah ini
 * (di sini saya menebak 'thp-s3-praktikum-kimia-biokimia' mengikuti pola
 * 'thp-s2-praktikum-mipa' dst). Jika mata kuliah belum ada di database,
 * buat dulu recordnya sebelum menjalankan seeder ini.
 *
 * CATATAN: kolom 'pokok_bahasan' pada tabel materis bersifat NOT NULL
 * tanpa default, jadi field ini WAJIB diisi (sudah disertakan di bawah).
 */
class ThpSemester3KontenSeeder extends Seeder
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

        $this->seedMatakuliah($prodi, 'thp-s3-praktikum-kimia-biokimia', $this->praktikumKimiaBiokimia());
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
                    'kode' => 'KB-P'.str_pad((string) $nomor, 2, '0', STR_PAD_LEFT),
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

    protected function praktikumKimiaBiokimia(): array
    {
        return [
            // Objek 1 - Bab 1: Air
            [
                'judul' => 'Air',
                'tujuan' => [
                    'Mahasiswa mengetahui cara penentuan kadar air suatu bahan pangan menggunakan metode gravimetri',
                ],
                'pendahuluan' => 'Air merupakan komponen yang penting dalam bahan makanan, dan setiap bahan makanan mengandung air dalam jumlah yang berbeda-beda. Penentuan kadar air dapat dilakukan dengan metode oven, oven-vakum, atau distilasi. Metode oven dengan prinsip pemanasan pada suhu 105±2°C sering digunakan karena mudah dilaksanakan, namun tidak cocok untuk sampel berkadar gula tinggi yang sebaiknya diukur menggunakan oven-vakum bersuhu rendah.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Peranan Air dalam Bahan Pangan', 'isi' => 'Air mempengaruhi tekstur, kesegaran dan keawetan bahan pangan, berfungsi sebagai pelarut universal bagi garam, vitamin, gula, dan pigmen, serta dapat berionisasi menjadi H3O+ dan OH-. Air juga berperan dalam reaksi kimia seperti hidrolisis, mempengaruhi aktivitas enzim, menentukan keamanan dan stabilitas pangan terkait pertumbuhan mikroorganisme, dan berfungsi sebagai medium pindah panas.'],
                    ['judul' => 'Klasifikasi Air dalam Bahan Pangan', 'isi' => 'Air dalam bahan pangan dikelompokkan menjadi 4 tipe berdasarkan kekuatan ikatannya: Tipe 1 adalah air terikat sebenarnya yang membentuk hidrat dengan karbohidrat/protein/garam dan tidak dapat membeku; Tipe 2 membentuk ikatan hidrogen pada mikrokapiler; Tipe 3 terikat secara fisik dalam matriks bahan dan dapat dimanfaatkan mikroba; Tipe 4 adalah air bebas dengan sifat air murni.'],
                    ['judul' => 'Prinsip Analisis Gravimetri untuk Kadar Air', 'isi' => 'Analisis gravimetri adalah proses pengisolasian dan penimbangan suatu unsur atau senyawa dalam kondisi semurni mungkin. Kadar air dapat ditentukan dengan gravimetri evolusi langsung maupun tidak langsung; untuk senyawa hidrat, kadar air kristal ditentukan dari selisih berat sebelum dan sesudah pemanasan pada suhu 110-130°C.'],
                ],
                'alat' => [
                    'Timbangan',
                    'Oven',
                    'Cawan',
                    'Desikator',
                ],
                'bahan' => [
                    'Jahe, Tomat, Tepung, Kentang, Beras',
                ],
                'prosedur' => [
                    'Oven cawan kosong pada suhu 105°C, lalu dinginkan dalam desikator selama ±15 menit dan timbang berat cawan kosong (catat).',
                    'Tambahkan sampel sebanyak ±3 gram ke dalam cawan yang telah ditimbang.',
                    'Oven sampel selama 3 jam pada suhu 105°C, dinginkan dalam desikator ±15 menit, lalu timbang.',
                    'Oven kembali selama 1 jam pada suhu 105°C dan timbang ulang, ulangi hingga diperoleh berat konstan dengan selisih berat maksimal ±0,02 gram.',
                    'Hitung kadar air (%b/b) dan (%b/k) menggunakan rumus (a-b)/a x 100%, dengan a = berat sampel awal dan b = berat sampel akhir.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Pada suhu berapa metode oven umumnya digunakan untuk penentuan kadar air bahan pangan?',
                        'opsi' => ['60±2°C', '80±2°C', '105±2°C', '150±2°C'],
                        'jawaban' => 2,
                        'penjelasan' => 'Metode oven yang digunakan pada praktikum ini menggunakan prinsip pemanasan dalam oven bersuhu 105±2°C.',
                    ],
                    [
                        'pertanyaan' => 'Mengapa sampel berkadar gula tinggi tidak cocok diukur kadar airnya dengan metode oven biasa?',
                        'opsi' => ['Karena gula akan menguap terlebih dahulu', 'Karena gula dapat mengalami karamelisasi/degradasi pada suhu tinggi sehingga mengganggu hasil pengukuran', 'Karena gula tidak mengandung air', 'Karena gula mempercepat proses pengeringan'],
                        'jawaban' => 1,
                        'penjelasan' => 'Sampel berkadar gula tinggi tidak bisa diukur kadar airnya dengan metode oven biasa, sehingga digunakan oven-vakum bersuhu rendah untuk produk semacam ini.',
                    ],
                    [
                        'pertanyaan' => 'Apa ciri khas air Tipe 4 menurut klasifikasi air dalam bahan pangan?',
                        'opsi' => ['Terikat kuat pada karbohidrat', 'Membentuk hidrat dengan protein', 'Tidak terikat dalam jaringan bahan dan bersifat sebagai air murni', 'Hanya ditemukan pada mikrokapiler'],
                        'jawaban' => 2,
                        'penjelasan' => 'Air Tipe 4 adalah air yang tidak terikat dalam jaringan suatu bahan dan bersifat air murni (air biasa) dengan keaktifan penuh.',
                    ],
                ],
            ],
            // Objek 2 - Bab 2: Karbohidrat
            [
                'judul' => 'Karbohidrat',
                'tujuan' => [
                    'Mahasiswa dapat membuktikan adanya karbohidrat secara kualitatif melalui uji Molisch',
                    'Mahasiswa mampu membedakan antara monosakarida dan disakarida melalui Uji Barfoed dan Uji Bial',
                    'Mahasiswa dapat membuktikan adanya polisakarida (amilum, glikogen, dan dekstrin) melalui Uji Iodium',
                    'Mahasiswa mampu membuktikan adanya gula reduksi melalui Uji Benedict dan Uji Fehling',
                    'Mahasiswa mampu membuktikan adanya ketosa (fruktosa) melalui Uji Seliwanoff',
                    'Mahasiswa mampu mengekstrak pati dari sumber pati',
                    'Mahasiswa dapat menjelaskan perubahan karakteristik pati dan gula selama proses pemanasan',
                ],
                'pendahuluan' => 'Karbohidrat adalah senyawa organik yang paling banyak terdapat pada makhluk hidup, berperan sebagai sumber energi, pembangun struktur, dan penyusun ATP, DNA, serta RNA. Berdasarkan jumlah unit penyusunnya, karbohidrat dikelompokkan menjadi monosakarida, disakarida, dan polisakarida. Pengolahan bahan pangan dapat mengubah karakteristik karbohidrat di dalamnya, sehingga mahasiswa perlu memahami reaksi kimia spesifik yang dapat digunakan untuk mendeteksi dan membedakan jenis-jenis karbohidrat.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Uji Kualitatif Karbohidrat (Molisch, Bial, Seliwanoff)', 'isi' => 'Uji Molisch mendeteksi keberadaan karbohidrat secara umum melalui pembentukan warna merah-ungu antara furfural dan naphthol. Uji Bial membedakan mono- dan disakarida berdasarkan warna yang terbentuk (biru-hijau untuk pentosa, kuning-hijau untuk heksosa, kuning untuk disakarida). Uji Seliwanoff mendeteksi ketosa (seperti fruktosa) yang bereaksi lebih cepat membentuk kompleks warna merah dibandingkan aldosa.'],
                    ['judul' => 'Uji Gula Pereduksi (Benedict, Fehling, Barfoed)', 'isi' => 'Uji Benedict dan Fehling mendeteksi gula pereduksi melalui reduksi ion Cu2+ menjadi Cu2O yang mengendap berwarna merah bata/kuning. Uji Barfoed membedakan monosakarida dan disakarida berdasarkan kecepatan reduksi Cu2+ dalam suasana asam, dimana monosakarida bereaksi lebih cepat daripada disakarida.'],
                    ['judul' => 'Reaksi Pencoklatan: Karamelisasi dan Maillard', 'isi' => 'Karamelisasi terjadi ketika gula sederhana dipanaskan melewati titik leburnya, menghasilkan warna coklat melalui reaksi enolisasi, dehidrasi menjadi furfural, dan fragmentasi gula. Reaksi Maillard terjadi antara gula pereduksi dan gugus amin dari asam amino melalui pembentukan basa Schiff, menghasilkan Produk Amadori atau Heyns yang berlanjut membentuk polimer kecoklatan (melanoidin).'],
                    ['judul' => 'Gelatinisasi Pati', 'isi' => 'Granula pati tersusun dari amilosa dan amilopektin yang dapat menyerap air bila dipanaskan hingga membengkak dan pecah. Gelatinisasi terjadi ketika granula pati tidak dapat kembali ke ukuran semula, menyebabkan peningkatan viskositas dan pembentukan gel, dengan karakteristik yang dipengaruhi jenis pati, konsentrasi, suhu, pH, dan keberadaan garam.'],
                ],
                'alat' => [
                    'Tabung reaksi, rak tabung, penangas air, hotplate, fume hood',
                    'Wajan, sendok, timbangan, gelas ukur, pipet',
                    'Conical well of heat dissipation block',
                ],
                'bahan' => [
                    'Reagen Molisch (α-naphthol, etanol), reagen Bial (orcinol, HCl, FeCl3), reagen Seliwanoff (resorcinol, HCl)',
                    'Larutan Benedict (Na sitrat, Na2CO3, CuSO4), larutan Fehling A dan B (CuSO4, KOH, kalium natrium tartrat)',
                    'Larutan Barfoed (Cu asetat, asam asetat glasial), larutan Iodium (KI, kristal iodium)',
                    'Gula pasir, tepung terigu, tepung singkong, pati berbagai konsentrasi, H2SO4 pekat',
                ],
                'prosedur' => [
                    'Uji Molisch: tambahkan reagen Molisch ke sampel, kocok, lalu alirkan H2SO4 pekat pada sisi tabung dan amati warna pada lapisan antar fase.',
                    'Uji Bial dan Seliwanoff: tambahkan reagen ke masing-masing sampel, panaskan hingga hampir mendidih, amati warna yang terbentuk.',
                    'Uji Benedict, Fehling, dan Barfoed: campurkan reagen dengan sampel, panaskan dalam penangas air, amati perubahan warna dan endapan yang terbentuk.',
                    'Uji Iodium: tambahkan larutan iodium encer ke sampel dan amati warna spesifik yang terbentuk untuk analisis kualitatif pati.',
                    'Karamelisasi: panaskan gula pasir di atas wajan sambil diaduk hingga mencair dan berwarna coklat, dokumentasikan setiap tahap perubahan.',
                    'Reaksi Maillard: buat adonan tepung terigu dan air, panaskan di atas wajan hingga matang, amati perubahan warna dan aroma, bandingkan dengan tepung singkong.',
                    'Gelatinisasi pati: buat larutan pati pada beberapa konsentrasi, suhu, dan pH berbeda, panaskan di atas hotplate, amati viskositas dan gel yang terbentuk setelah didinginkan.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Warna apa yang terbentuk pada uji Iodium bila sampel mengandung pati (amilum)?',
                        'opsi' => ['Merah anggur', 'Merah coklat', 'Biru', 'Kuning'],
                        'jawaban' => 2,
                        'penjelasan' => 'Pati dengan iodium menghasilkan warna biru, berbeda dengan dekstrin (merah anggur) dan glikogen (merah coklat).',
                    ],
                    [
                        'pertanyaan' => 'Apa perbedaan utama antara reaksi karamelisasi dan reaksi Maillard?',
                        'opsi' => ['Karamelisasi hanya melibatkan gula sederhana, sedangkan Maillard melibatkan gula pereduksi dan gugus amin dari asam amino', 'Karamelisasi memerlukan suhu rendah', 'Maillard tidak menghasilkan warna coklat', 'Keduanya adalah reaksi yang identik'],
                        'jawaban' => 0,
                        'penjelasan' => 'Karamelisasi hanya melibatkan gula sederhana yang dipanaskan melewati titik leburnya, sedangkan reaksi Maillard terjadi antara gula pereduksi dengan gugus amin dari asam amino.',
                    ],
                    [
                        'pertanyaan' => 'Apa yang dimaksud dengan gelatinisasi pati?',
                        'opsi' => ['Proses pengeringan pati', 'Kondisi dimana granula pati yang telah menyerap air tidak dapat kembali ke ukuran semula', 'Proses pembentukan kristal pati', 'Pemisahan amilosa dari amilopektin'],
                        'jawaban' => 1,
                        'penjelasan' => 'Gelatinisasi terjadi bila proses pembengkakan granula pati melewati titik dimana granula tidak dapat kembali ke ukuran awalnya, menyebabkan peningkatan viskositas dan pembentukan gel.',
                    ],
                ],
            ],
            // Objek 3 - Bab 3: Reaksi Oksidasi Saponifikasi pada Lemak
            [
                'judul' => 'Reaksi Oksidasi Saponifikasi pada Lemak',
                'tujuan' => [
                    'Mahasiswa dapat menentukan bilangan penyabunan',
                    'Mahasiswa dapat mengukur derajat oksidasi lemak/minyak',
                    'Mahasiswa dapat menentukan kadar asam lemak bebas',
                    'Mahasiswa dapat memahami reaksi saponifikasi pada pembuatan sabun',
                ],
                'pendahuluan' => 'Lipid merupakan makromolekul penting dalam tubuh yang tersusun atas asam lemak, gliserida, dan lipid kompleks. Lemak adalah trigliserida yaitu satu molekul gliserol yang mengikat tiga molekul asam lemak. Hidrolisis lemak menggunakan basa disebut proses penyabunan (saponifikasi) yang menghasilkan gliserol dan sabun, sedangkan pembiaran lemak di udara dapat menyebabkan hidrolisis dan oksidasi yang menimbulkan bau tengik.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Struktur dan Sifat Trigliserida', 'isi' => 'Lemak tersusun atas unsur C, H, dan O dengan gliserol dan asam lemak sebagai unit dasar penyusunnya. Lemak hewan umumnya berupa zat padat karena mengandung asam lemak jenuh, sedangkan lemak dari tumbuhan (minyak) berupa zat cair karena mengandung asam lemak tidak jenuh.'],
                    ['judul' => 'Reaksi Saponifikasi dan Bilangan Penyabunan', 'isi' => 'Hidrolisis lemak menggunakan basa menghasilkan gliserol dan garam asam lemak (sabun). Bilangan penyabunan adalah jumlah miligram KOH yang diperlukan untuk menyabunkan 1 gram lemak, dan besarnya berbanding terbalik dengan berat molekul lemak tersebut.'],
                    ['judul' => 'Ketengikan, Oksidasi Lemak, dan Kadar Asam Lemak Bebas', 'isi' => 'Ketengikan disebabkan oleh hidrolisis yang menghasilkan asam lemak bebas serta oksidasi asam lemak tidak jenuh yang menghasilkan peroksida dan aldehida. Mutu minyak/lemak dapat diketahui melalui bilangan peroksida dan kadar asam lemak bebas, yang dihitung berdasarkan jumlah basa yang digunakan untuk menetralkan asam lemak bebas dalam sampel.'],
                ],
                'alat' => [
                    'Erlenmeyer, pipet volume, hotplate stirer, refluks, buret',
                    'Kaca pengaduk, gelas ukur, neraca analitik',
                    'Wadah plastik/mangkuk, sendok, wadah cetakan',
                ],
                'bahan' => [
                    'Sampel minyak, KOH 0,5N dalam alkohol, indikator PP, HCl 0,5N',
                    'Asam asetat, kloroform, larutan KI, akuades, Na2S2O3, indikator pati 1%',
                    'Etanol, NaOH 0,1N, minyak goreng, soda api (NaOH), air',
                ],
                'prosedur' => [
                    'Penentuan bilangan penyabunan: refluks sampel minyak dengan KOH selama 1 jam, titrasi kelebihan KOH dengan HCl menggunakan indikator PP, hitung bilangan penyabunan menggunakan volume titran blanko dan sampel.',
                    'Pengukuran derajat oksidasi minyak: larutkan sampel dalam pelarut asam asetat-kloroform, tambahkan larutan KI, titrasi dengan Na2S2O3 menggunakan indikator pati hingga warna kekuningan hilang, hitung bilangan peroksida.',
                    'Penentuan kadar asam lemak bebas: larutkan sampel dalam alkohol netral, titrasi dengan NaOH 0,1N menggunakan indikator PP hingga warna pink konstan, hitung kadar asam lemak bebas menggunakan berat molekul asam lemak dominan.',
                    'Reaksi saponifikasi pembuatan sabun: larutkan soda api dalam air, campurkan perlahan ke dalam minyak goreng sambil diaduk hingga mengental, tuang ke cetakan dan diamkan hingga mengeras menjadi sabun padat.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Apa yang dimaksud dengan bilangan penyabunan?',
                        'opsi' => ['Jumlah mol asam lemak dalam 1 gram lemak', 'Jumlah miligram KOH yang diperlukan untuk menyabunkan 1 gram lemak', 'Jumlah gram gliserol yang dihasilkan', 'Jumlah mol air yang bereaksi'],
                        'jawaban' => 1,
                        'penjelasan' => 'Bilangan penyabunan adalah jumlah miligram KOH yang diperlukan untuk menyabunkan 1 gram lemak, dan nilainya berbanding terbalik dengan berat molekul lemak.',
                    ],
                    [
                        'pertanyaan' => 'Apa penyebab utama terjadinya bau tengik pada lemak yang dibiarkan lama di udara?',
                        'opsi' => ['Penguapan air dalam lemak', 'Hidrolisis dan oksidasi asam lemak tidak jenuh yang menghasilkan peroksida dan aldehida', 'Pembentukan gliserol murni', 'Kristalisasi lemak'],
                        'jawaban' => 1,
                        'penjelasan' => 'Ketengikan disebabkan oleh proses hidrolisis yang menghasilkan asam lemak bebas serta oksidasi asam lemak tidak jenuh yang hasilnya berupa peroksida dan aldehida.',
                    ],
                    [
                        'pertanyaan' => 'Zat apa yang dihasilkan bersama sabun ketika trigliserida bereaksi dengan basa (NaOH) dalam proses saponifikasi?',
                        'opsi' => ['Asam lemak bebas', 'Gliserol', 'Peroksida', 'Aldehida'],
                        'jawaban' => 1,
                        'penjelasan' => 'Reaksi saponifikasi antara trigliserida (lemak/minyak) dengan basa menghasilkan gliserol dan sabun (garam asam lemak).',
                    ],
                ],
            ],
            // Objek 4 - Bab 4: Protein
            [
                'judul' => 'Protein',
                'tujuan' => [
                    'Melakukan uji kualitatif protein pada bahan pangan',
                    'Melakukan uji kelarutan protein',
                    'Mempelajari dan mengidentifikasi faktor-faktor yang mempengaruhi denaturasi protein',
                    'Mengidentifikasi dan mempraktekkan uji sifat fungsional protein',
                ],
                'pendahuluan' => 'Protein merupakan makromolekul penting dalam sistem biologis yang berperan sebagai zat pengatur, pembangun jaringan, hormon, enzim, dan antibodi, serta menyumbangkan energi 4 KKal/gram. Protein tersusun dari lebih 100 asam amino yang dihubungkan oleh ikatan peptida, dan dalam pengolahan pangan protein berperan penting dalam mengentalkan, membentuk gel, menstabilkan emulsi, dan membentuk buih.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Uji Kualitatif Protein (Ninhidrin dan Biuret)', 'isi' => 'Uji ninhidrin mendeteksi asam-α amino bebas melalui pembentukan senyawa kompleks berwarna biru-ungu (kecuali prolin dan hidroksiprolin yang berwarna kuning). Uji biuret mendeteksi ikatan peptida melalui interaksi Cu2+ yang menghasilkan warna ungu.'],
                    ['judul' => 'Kelarutan Protein dan Titik Isoelektrik', 'isi' => 'Kelarutan protein ditentukan oleh sifat ionisasi asam aminonya, yang bersifat amfoter tergantung pH larutan. Pada titik isoelektrik (pI), protein berada sebagai zwitter ion netral dan kelarutannya berada pada titik minimal.'],
                    ['judul' => 'Denaturasi Protein', 'isi' => 'Denaturasi adalah modifikasi struktur sekunder, tersier, dan kuarter protein tanpa memutus ikatan peptida, yang menyebabkan perubahan sifat fisikokimia secara irreversible seperti hilangnya kelarutan. Denaturasi dapat disebabkan oleh pemanasan, perubahan pH, pelarut organik, dan penambahan garam.'],
                    ['judul' => 'Sifat Fungsional Protein', 'isi' => 'Protein memiliki sifat fungsional penting dalam pengolahan pangan seperti daya ikat air (Water Holding Capacity), kapasitas dan stabilitas buih, serta kemampuan menahan minyak (Oil Holding Capacity), yang masing-masing dapat diukur berdasarkan perbandingan berat atau volume sebelum dan sesudah perlakuan.'],
                ],
                'alat' => [
                    'Tabung reaksi, water bath, tabung sentrifuse, sentrifuse',
                    'Gelas piala, hand mixer, vortex, neraca analitik',
                ],
                'bahan' => [
                    'Sampel protein/asam amino (glycine, tyrosine, glutamic acid, cysteine), ninhidrin 0,2%',
                    'Larutan CuSO4 encer, NaOH 40%, aseton',
                    'Putih telur, tepung kedelai, larutan kapur, HCl, NaOH, etanol 95%',
                ],
                'prosedur' => [
                    'Uji ninhidrin: tambahkan reagen ninhidrin ke larutan sampel, didihkan dalam water bath 2 menit, amati warna biru yang terbentuk setelah didinginkan.',
                    'Uji biuret: tambahkan larutan CuSO4 encer dan NaOH ke larutan protein, amati perubahan warna ungu yang terbentuk.',
                    'Uji kelarutan protein: larutkan sampel asam amino/protein dalam berbagai pelarut (air, alkohol, HCl encer, NaOH encer), amati kelarutannya, serta tentukan titik isoelektrik protein kedelai melalui pengaturan pH ekstrak.',
                    'Uji denaturasi: amati kekeruhan, endapan, dan pembentukan padatan pada putih telur yang diberi perlakuan suhu berbeda, pH berbeda (asam-basa), dan pelarut organik berbeda.',
                    'Uji sifat fungsional: ukur daya ikat air (WHC) dan kemampuan menahan minyak (OHC) melalui sentrifugasi sampel protein, serta ukur kapasitas dan stabilitas buih menggunakan hand mixer.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Warna apa yang terbentuk pada sebagian besar asam amino ketika bereaksi dengan ninhidrin?',
                        'opsi' => ['Merah bata', 'Kuning', 'Biru-ungu', 'Hijau'],
                        'jawaban' => 2,
                        'penjelasan' => 'Sebagian besar asam-α amino bebas akan bereaksi dengan ninhidrin membentuk senyawa kompleks berwarna biru-ungu, kecuali prolin dan hidroksiprolin yang berwarna kuning.',
                    ],
                    [
                        'pertanyaan' => 'Pada titik isoelektrik (pI), bagaimana kondisi kelarutan protein?',
                        'opsi' => ['Kelarutan maksimal', 'Kelarutan minimal', 'Protein tidak dapat larut sama sekali', 'Kelarutan tidak terpengaruh'],
                        'jawaban' => 1,
                        'penjelasan' => 'Pada titik isoelektrik, protein berada sebagai zwitter ion netral sehingga kelarutan protein berada pada titik minimal.',
                    ],
                    [
                        'pertanyaan' => 'Manakah yang BUKAN merupakan penyebab denaturasi protein?',
                        'opsi' => ['Pemanasan', 'Perubahan pH', 'Penambahan garam', 'Pendinginan pada suhu rendah tanpa perlakuan lain'],
                        'jawaban' => 3,
                        'penjelasan' => 'Denaturasi protein dapat disebabkan oleh proses pemanasan, perubahan pH, pelarut organik, dan penambahan garam, bukan oleh pendinginan biasa.',
                    ],
                ],
            ],
            // Objek 5 - Bab 5: Biokimia Enzim
            [
                'judul' => 'Biokimia Enzim',
                'tujuan' => [
                    'Memahami pengaruh suhu dan pH terhadap aktivitas enzim katalase secara kualitatif',
                    'Menganalisis aktivitas enzim katalase dari berbagai macam sumber enzim dalam menghidrolisis H2O2',
                ],
                'pendahuluan' => 'Enzim adalah protein yang berfungsi sebagai biokatalisator reaksi-reaksi biokimia dengan spesifisitas tinggi terhadap substratnya. Aktivitas enzim (kinetik enzim) dapat diukur melalui perubahan absorbansi, jumlah produk, atau berkurangnya substrat dalam satuan waktu tertentu, dan dipengaruhi oleh suhu, pH, kadar substrat, kadar enzim, serta keberadaan inhibitor.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Sifat dan Klasifikasi Enzim', 'isi' => 'Enzim bersifat sebagai protein, bekerja sebagai katalis tanpa ikut bereaksi, bekerja secara spesifik, dan dibutuhkan dalam jumlah sedikit. Enzim dikelompokkan menjadi 6 kelas utama yaitu oksidoreduktase, transferase, hidrolase, liase, isomerase, dan ligase, masing-masing dengan fungsi katalitik yang berbeda.'],
                    ['judul' => 'Faktor yang Mempengaruhi Aktivitas Enzim', 'isi' => 'Kenaikan suhu di atas suhu optimum menyebabkan denaturasi enzim yang menghilangkan kemampuan katalisnya, umumnya terjadi pada suhu 55-65°C. Aktivitas enzim juga sangat peka terhadap pH karena mempengaruhi muatan dan struktur protein, serta dipengaruhi oleh konsentrasi substrat, konsentrasi enzim, dan keberadaan inhibitor yang bersaing dengan substrat pada sisi aktif enzim.'],
                    ['judul' => 'Enzim Katalase dan Ekstraksi Enzim Bromelin', 'isi' => 'Enzim katalase menghidrolisis H2O2 menjadi air dan oksigen, dengan aktivitas yang dapat diamati secara kualitatif melalui ketinggian gelembung gas yang terbentuk. Enzim bromelin dari buah nanas dapat diekstrak melalui sentrifugasi dan dimanfaatkan sebagai pengempuk daging karena kemampuannya menghidrolisis protein.'],
                ],
                'alat' => [
                    'Tabung reaksi, beaker glass, gelas ukur, pisau, penggaris, termometer, kompor listrik',
                    'Juicer, sentrifuge, neraca analitik, batang pengaduk',
                ],
                'bahan' => [
                    'Kentang, hati sapi/ayam, jantung, pepaya, pasir, H2O2, air panas, air dingin/es',
                    'HCl 1M, NaOH 1M, nanas, garam (NaCl), daging sapi',
                ],
                'prosedur' => [
                    'Pengaruh suhu: masukkan potongan kentang ke tabung reaksi, tambahkan H2O2 yang telah direndam pada suhu berbeda (suhu ruang, air panas 40-45°C, air dingin), ukur ketinggian gelembung gas yang terbentuk.',
                    'Pengaruh pH: tambahkan H2O2 ke gerusan hati yang telah ditetesi HCl 1M atau NaOH 1M, ukur ketinggian gelembung gas dan bandingkan dengan kontrol.',
                    'Pengaruh jenis substrat: tambahkan H2O2 ke berbagai gerusan bahan (hati, jantung, pepaya, pasir), ukur dan bandingkan ketinggian gelembung gas pada masing-masing tabung.',
                    'Ekstraksi enzim bromelin: haluskan nanas, ambil ekstraknya, tambahkan garam dengan konsentrasi berbeda, sentrifugasi untuk mendapatkan endapan ekstrak kasar enzim bromelin.',
                    'Aplikasi pengempukan daging: rendam potongan daging sapi dalam ekstrak nanas atau pepaya selama 30 menit, bandingkan tekstur dengan daging kontrol yang direbus, amati setiap 10 menit.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Pada suhu berapa sebagian besar enzim mengalami denaturasi yang tidak dapat balik (irreversible)?',
                        'opsi' => ['0-10°C', '25-35°C', '55-65°C', '90-100°C'],
                        'jawaban' => 2,
                        'penjelasan' => 'Sebagian besar enzim mengalami denaturasi yang tidak dapat balik pada suhu 55-65°C, sehingga kehilangan kemampuan katalisnya.',
                    ],
                    [
                        'pertanyaan' => 'Kelompok enzim apa yang mengkatalisis reaksi hidrolisis suatu substrat dengan bantuan molekul air?',
                        'opsi' => ['Oksidoreduktase', 'Transferase', 'Hidrolase', 'Ligase'],
                        'jawaban' => 2,
                        'penjelasan' => 'Hidrolase adalah enzim yang mengkatalisis reaksi hidrolisis suatu substrat dengan bantuan molekul air, contohnya lipase dan amino-peptidase.',
                    ],
                    [
                        'pertanyaan' => 'Bagaimana cara mengamati aktivitas enzim katalase secara kualitatif pada praktikum ini?',
                        'opsi' => ['Mengukur suhu sampel', 'Mengukur ketinggian busa/gelembung gas yang terbentuk dari reaksi dengan H2O2', 'Mengukur pH sampel', 'Menimbang berat sampel'],
                        'jawaban' => 1,
                        'penjelasan' => 'Aktivitas enzim katalase diamati secara kualitatif dengan mengukur ketinggian busa/gelembung gas oksigen yang terbentuk saat enzim menghidrolisis H2O2.',
                    ],
                ],
            ],
            // Objek 6 - Bab 6: Pigmen
            [
                'judul' => 'Pigmen',
                'tujuan' => [
                    'Mahasiswa dapat memahami pengaruh cara pemasakan terhadap klorofil',
                    'Mahasiswa dapat memahami pengaruh asam, basa, dan logam terhadap pigmen',
                    'Mahasiswa dapat memahami perubahan warna pada daging',
                    'Mahasiswa dapat memahami pengawetan warna daging',
                ],
                'pendahuluan' => 'Pigmen merupakan zat warna alami yang terdapat pada tanaman atau hewan, memiliki peranan luas dalam pengolahan makanan, obat-obatan, dan kosmetika. Pigmen terdiri dari berbagai jenis seperti klorofil, antosianin, karotenoid, dan melanin, yang dapat dipengaruhi oleh faktor asam, basa, logam, suhu, dan pengolahan, sehingga warna bahan pangan dapat menjadi indikator mutu dan kematangan.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Jenis-jenis Pigmen Alami', 'isi' => 'Karotenoid adalah pigmen berwarna kuning, merah, dan oranye yang terdapat pada wortel, tomat, dan jeruk, bersifat tidak larut air. Antosianin adalah pigmen dengan kisaran warna merah yang larut dalam air, banyak terdapat pada bunga dan ubi ungu. Kurkuminoid adalah pigmen non-fotosintetik berwarna kuning yang banyak ditemukan pada kunyit dan temu lawak.'],
                    ['judul' => 'Pengaruh Pemasakan, Asam, Basa, dan Logam terhadap Pigmen', 'isi' => 'Cara pemasakan (panci terbuka atau tertutup) dapat mempengaruhi warna, tekstur, dan pH klorofil pada sayuran. Penambahan asam, basa, atau ion logam seperti Mg, Fe, dan Ca dapat mengubah kestabilan dan warna pigmen pada bahan pangan seperti cabai, kunyit, dan bayam.'],
                    ['judul' => 'Perubahan dan Pengawetan Warna Daging', 'isi' => 'Warna daging dapat berubah akibat paparan udara maupun proses pemanasan. Pengawetan warna daging dapat dilakukan dengan penambahan senyawa seperti asam askorbat, NaNO2, dan NaNO3 yang membantu menstabilkan warna merah daging melalui interaksi dengan pigmen mioglobin.'],
                ],
                'alat' => [
                    'Neraca analitik, pisau, talenan, spatula, panci, kompor',
                    'Penangas air, pipet tetes, tabung reaksi',
                ],
                'bahan' => [
                    'Akuades, bayam, daun pandan, daun suji, cabai merah, kunyit, terong',
                    'Asam asetat, MgCl2, FeCl3, CaCl2, NaHCO3',
                    'Daging sapi, asam askorbat, NaNO2, NaNO3, asam cuka',
                ],
                'prosedur' => [
                    'Pengaruh cara pemasakan terhadap klorofil: rebus sampel (bayam, daun pandan, daun suji) selama 15 menit pada panci terbuka dan tertutup, amati dan bandingkan warna, tekstur, dan pH sebelum-sesudah.',
                    'Pengaruh asam, basa, dan logam: rendam sampel pigmen (cabai, kunyit, bayam) ke dalam larutan MgCl2, FeCl3, CaCl2, NaHCO3, dan asam asetat, panaskan 15 menit, amati perubahan warna, tekstur, dan pH.',
                    'Perubahan warna daging: amati perubahan warna daging sapi segar yang didiamkan di udara selama 20 menit, bandingkan dengan daging yang direbus hingga mendidih.',
                    'Pengawetan warna daging: rendam sampel daging dalam larutan asam askorbat, NaNO2, NaNO3, dan campuran ketiganya, tambahkan asam cuka, amati perubahan warna sebelum dan sesudah pemanasan.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Pigmen apa yang bersifat larut dalam air dan memberikan kisaran warna merah pada bunga?',
                        'opsi' => ['Karotenoid', 'Antosianin', 'Klorofil', 'Kurkuminoid'],
                        'jawaban' => 1,
                        'penjelasan' => 'Antosianin merupakan pigmen alami dengan kisaran warna merah yang luas, larut dalam air, dan banyak terdapat pada bunga.',
                    ],
                    [
                        'pertanyaan' => 'Senyawa apa yang umum digunakan untuk membantu mengawetkan warna merah pada daging?',
                        'opsi' => ['NaCl saja', 'Asam askorbat dan nitrit (NaNO2/NaNO3)', 'Air biasa', 'Minyak goreng'],
                        'jawaban' => 1,
                        'penjelasan' => 'Pengawetan warna daging dapat dilakukan dengan penambahan asam askorbat, NaNO2, dan NaNO3 yang membantu menstabilkan warna merah daging.',
                    ],
                    [
                        'pertanyaan' => 'Tanaman apa yang menjadi sumber utama pigmen kurkuminoid?',
                        'opsi' => ['Bayam dan daun suji', 'Wortel dan tomat', 'Kunyit dan temu lawak', 'Ubi ungu'],
                        'jawaban' => 2,
                        'penjelasan' => 'Kurkuminoid adalah pigmen non-fotosintetik berwarna kuning yang banyak ditemukan pada tanaman keluarga Zingiberaceae seperti kunyit dan temu lawak.',
                    ],
                ],
            ],
        ];
    }
}
