<?php

namespace Database\Seeders;

use App\Models\Matakuliah;
use App\Models\Materi;
use App\Models\Praktikum;
use App\Models\Prodi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Mengisi konten untuk mata kuliah praktikum TIP Semester 2:
 * Praktikum Pengetahuan Bahan Agroindustri (TIP337)
 * Mengikuti pola ThpSemester2KontenSeeder / ThpSemester3KontenSeeder / ThpSemester4KontenSeeder.
 *
 * Sumber materi:
 * - Modul_Praktikum_PBA.pdf : Modul Praktikum Pengetahuan Bahan Agroindustri,
 *   PS Teknologi Industri Pertanian (TIP), Jurusan Teknologi Pertanian,
 *   Fakultas Pertanian, Universitas Jambi (2026). Dosen pengampu: Rudi
 *   Prihantoro, S.TP., M.Sc. (Koordinator); Fauziah Fiardilla, S.TP., M.Si.;
 *   Ir. Anna Anggraini, S.TP., M.P; Ika Putri Sulistiana, S.TP., M.T.P.
 *   Modul sumber berisi 14 topik praktikum lengkap dengan Tujuan, Teori
 *   Singkat, Alat & Bahan, Prosedur Kerja, Rubrik, dan Tugas Laporan.
 *
 * Pemisahan konten:
 * - Materi    = teori saja (Pendahuluan + Tinjauan Pustaka), diringkas dari
 *   bagian "Teori Singkat" pada tiap objek dalam modul sumber.
 * - Praktikum = pelaksanaan (Tujuan, Alat, Bahan, Prosedur/Langkah Kerja, Kuis).
 *
 * PENTING: sesuaikan $matakuliahSlug pada method run() dengan slug asli yang
 * sudah tersimpan di tabel matakuliahs (di sini saya menebak
 * 'tip-s2-praktikum-pengetahuan-bahan-agroindustri' mengikuti pola
 * 'thp-s4-praktikum-...' dst). Jika mata kuliah belum ada di database, buat
 * dulu recordnya (lihat contoh Matakuliah::create() di percakapan
 * sebelumnya) sebelum menjalankan seeder ini.
 *
 * CATATAN: kolom 'pokok_bahasan' pada tabel materis bersifat NOT NULL tanpa
 * default, jadi field ini WAJIB diisi (sudah disertakan di bawah).
 */
class TipSemester2KontenSeeder extends Seeder
{
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
        $prodi = Prodi::where('kode', 'TIP')->first();

        if (! $prodi) {
            $this->command?->warn('Prodi TIP tidak ditemukan, seeder dilewati.');
            return;
        }

        $this->seedMatakuliah(
            $prodi,
            'tip-s2-praktikum-pengetahuan-bahan-agroindustri',
            $this->formatLaporanPba,
            $this->praktikumPengetahuanBahanAgroindustri(),
            'PBA'
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

    protected function praktikumPengetahuanBahanAgroindustri(): array
    {
        return [
            // Objek 1 - Praktikum 1: Buah Klimaterik
            [
                'judul' => 'Buah Klimaterik',
                'tujuan' => [
                    'Mengamati perubahan fisik yang terjadi pada buah klimaterik setelah pemanenan',
                    'Mengetahui pola perubahan warna, tekstur, dan aroma buah klimaterik selama penyimpanan',
                    'Memahami proses pematangan dan karakteristik buah klimaterik',
                ],
                'pendahuluan' => 'Buah klimaterik adalah buah yang mengalami lonjakan laju respirasi (klimaterik) setelah dipanen disertai peningkatan produksi etilen secara besar-besaran. Etilen berperan penting sebagai hormon pematangan yang memicu perubahan warna, pelunakan tekstur, dan perkembangan aroma khas buah. Contoh buah klimaterik yang umum adalah pepaya dan pisang, yang dapat dipanen dalam kondisi belum matang dan akan matang selama proses penyimpanan.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Peran Etilen dalam Pematangan Buah Klimaterik', 'isi' => 'Etilen berperan sebagai hormon pematangan yang memicu perubahan warna, pelunakan tekstur, dan perkembangan aroma khas pada buah klimaterik, dengan produksi etilen meningkat secara besar-besaran setelah pemanenan.'],
                    ['judul' => 'Perubahan Warna, Pati, dan Tekstur selama Pematangan', 'isi' => 'Selama pematangan klimaterik, kandungan klorofil berkurang dan digantikan pigmen karotenoid atau antosianin, kandungan pati dikonversi menjadi gula sederhana, dan aktivitas enzim pektinase menyebabkan dinding sel melunak sehingga tekstur buah menjadi lembut.'],
                ],
                'alat' => ['Timbangan analitik', 'Jangka sorong/micrometer', 'Penetrometer (alat pengukur kekerasan)', 'Refraktometer', 'Termometer', 'Pisau', 'Talenan', 'Cawan/wadah', 'Alat tulis dan kamera'],
                'bahan' => ['Buah pepaya (muda, setengah matang, matang)', 'Buah pisang (muda, setengah matang, matang)'],
                'prosedur' => [
                    'Siapkan buah pepaya dan pisang dalam tiga tingkat kematangan (muda, setengah matang, matang), bersihkan permukaan buah dengan kain bersih, lalu timbang dan catat beratnya.',
                    'Amati dan catat warna kulit buah pada setiap tingkat kematangan, ukur dimensi buah (panjang dan diameter) menggunakan jangka sorong, amati kondisi kulit (mulus, berbintik, atau mengerut), serta cium dan catat karakteristik aroma buah.',
                    'Belah buah menjadi dua bagian secara membujur, gambar penampang dalam buah dan identifikasi bagian-bagiannya, lalu amati warna daging buah dan bagian biji.',
                    'Ukur ketebalan daging buah dari kulit ke bagian biji, ukur tingkat kekerasan daging buah menggunakan penetrometer (dinyatakan dalam kg/cm²), dan ukur kadar gula (TSS) menggunakan refraktometer (dinyatakan dalam °Brix).',
                    'Lakukan pengukuran kekerasan dan TSS pada 3 titik berbeda lalu ambil rata-ratanya, dan catat seluruh hasil pengamatan fisik eksternal maupun internal ke dalam tabel pengamatan.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Apa yang menjadi ciri utama buah klimaterik setelah dipanen?',
                        'opsi' => ['Penurunan laju respirasi secara bertahap dan stabil', 'Lonjakan laju respirasi disertai peningkatan produksi etilen secara besar-besaran', 'Buah tidak akan mengalami perubahan warna sama sekali', 'Buah harus selalu dipanen dalam kondisi matang penuh di pohon'],
                        'jawaban' => 1,
                        'penjelasan' => 'Buah klimaterik mengalami lonjakan laju respirasi (klimaterik) setelah dipanen disertai peningkatan produksi etilen secara besar-besaran, yang memicu proses pematangan.',
                    ],
                    [
                        'pertanyaan' => 'Alat apa yang digunakan untuk mengukur kadar gula (TSS) buah pada praktikum ini?',
                        'opsi' => ['Penetrometer', 'Refraktometer', 'Jangka sorong', 'Termometer'],
                        'jawaban' => 1,
                        'penjelasan' => 'Kadar gula (TSS) buah diukur menggunakan refraktometer dan dinyatakan dalam satuan °Brix, dengan pengukuran dilakukan pada 3 titik berbeda lalu dirata-ratakan.',
                    ],
                    [
                        'pertanyaan' => 'Mengapa buah pepaya dan pisang dapat dipanen dalam kondisi belum matang?',
                        'opsi' => ['Karena termasuk buah klimaterik yang tetap dapat matang selama proses penyimpanan', 'Karena kedua buah tersebut tidak akan pernah membusuk', 'Karena kedua buah tersebut tidak mengandung etilen sama sekali', 'Karena warna kulitnya tidak akan pernah berubah'],
                        'jawaban' => 0,
                        'penjelasan' => 'Pepaya dan pisang merupakan contoh buah klimaterik sehingga dapat dipanen dalam kondisi belum matang dan akan mengalami proses pematangan selama penyimpanan.',
                    ],
                ],
            ],
            // Objek 2 - Praktikum 2: Buah Non Klimaterik
            [
                'judul' => 'Buah Non Klimaterik',
                'tujuan' => [
                    'Mengetahui dan memahami karakteristik buah non klimaterik',
                    'Mengamati sifat fisik buah non klimaterik pada berbagai tingkat kematangan',
                    'Membedakan karakteristik buah non klimaterik dengan buah klimaterik',
                ],
                'pendahuluan' => 'Buah non klimaterik adalah buah yang tidak mengalami lonjakan respirasi setelah dipanen dan tidak menghasilkan etilen dalam jumlah besar, sehingga harus dipanen dalam keadaan sudah matang di pohon karena tidak akan mengalami proses pematangan lanjutan setelah pemanenan. Salak, nanas, dan jeruk merupakan contoh buah non klimaterik yang mempertahankan tingkat kesegarannya lebih lama jika disimpan pada suhu rendah.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Karakteristik Respirasi Buah Non Klimaterik', 'isi' => 'Buah non klimaterik menunjukkan penurunan laju respirasi yang bertahap dan stabil tanpa lonjakan tajam setelah dipanen, berbeda dengan pola respirasi buah klimaterik yang menunjukkan lonjakan besar.'],
                    ['judul' => 'Stabilitas Fisik Buah Non Klimaterik Pascapanen', 'isi' => 'Karakteristik fisik buah non klimaterik relatif lebih stabil setelah panen; penurunan kualitas yang terjadi lebih bersifat degradatif (kerusakan) bukan pematangan, sehingga pemanenan pada saat tepat matang sangat penting dilakukan.'],
                ],
                'alat' => ['Timbangan analitik', 'Jangka sorong', 'Penetrometer', 'Refraktometer', 'Pisau', 'Talenan', 'Cawan/wadah', 'Alat tulis dan kamera'],
                'bahan' => ['Buah salak', 'Buah nanas', 'Buah jeruk'],
                'prosedur' => [
                    'Amati dan catat warna kulit masing-masing buah non klimaterik, ukur dimensi buah (panjang dan diameter) menggunakan jangka sorong, dan timbang berat masing-masing buah.',
                    'Catat kondisi permukaan kulit buah (kasar, halus, berduri, bersisik, dan lain-lain), lalu cium dan catat karakteristik aroma masing-masing buah.',
                    'Kupas atau belah buah dan gambar penampang dalamnya, amati warna daging buah, keberadaan biji, dan susunan daging buah.',
                    'Ukur ketebalan daging buah, ukur tingkat kekerasan daging menggunakan penetrometer, dan ukur kadar gula (TSS) menggunakan refraktometer.',
                    'Catat rasa buah (manis, asam, atau kombinasi) dan seluruh hasil pengamatan ke dalam tabel pengamatan.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Mengapa buah non klimaterik harus dipanen dalam keadaan sudah matang di pohon?',
                        'opsi' => ['Karena buah tersebut tidak akan mengalami proses pematangan lanjutan setelah pemanenan', 'Karena buah tersebut akan menjadi lebih manis setelah dipanen', 'Karena buah tersebut memerlukan etilen tambahan dari luar', 'Karena buah tersebut tidak dapat disimpan pada suhu rendah'],
                        'jawaban' => 0,
                        'penjelasan' => 'Buah non klimaterik harus dipanen dalam keadaan sudah matang di pohon karena tidak akan mengalami proses pematangan lanjutan setelah pemanenan.',
                    ],
                    [
                        'pertanyaan' => 'Manakah yang termasuk contoh buah non klimaterik pada praktikum ini?',
                        'opsi' => ['Pepaya, pisang, dan mangga', 'Salak, nanas, dan jeruk', 'Tomat, alpukat, dan sawo', 'Melon, semangka, dan durian'],
                        'jawaban' => 1,
                        'penjelasan' => 'Salak (Salacca zalacca), nanas (Ananas comosus), dan jeruk (Citrus sp.) merupakan contoh buah non klimaterik yang digunakan pada praktikum ini.',
                    ],
                    [
                        'pertanyaan' => 'Apa perbedaan utama pola penurunan kualitas buah non klimaterik dibandingkan buah klimaterik?',
                        'opsi' => ['Bersifat degradatif (kerusakan) bertahap dan stabil, bukan proses pematangan', 'Buah non klimaterik menjadi lebih manis seiring waktu penyimpanan', 'Buah non klimaterik mengalami lonjakan respirasi yang lebih tajam', 'Buah non klimaterik tidak pernah mengalami penurunan kualitas'],
                        'jawaban' => 0,
                        'penjelasan' => 'Penurunan kualitas pada buah non klimaterik lebih bersifat degradatif (kerusakan) bukan pematangan, berbeda dengan buah klimaterik yang mengalami lonjakan respirasi disertai pematangan.',
                    ],
                ],
            ],
            // Objek 3 - Praktikum 3: Pola Respirasi Buah
            [
                'judul' => 'Pola Respirasi Buah',
                'tujuan' => [
                    'Mengetahui dan memahami perbedaan pola respirasi buah klimaterik dan non klimaterik',
                    'Mengamati perubahan parameter fisik buah selama proses penyimpanan sebagai indikator respirasi',
                    'Membandingkan laju penurunan mutu buah klimaterik dan non klimaterik',
                ],
                'pendahuluan' => 'Respirasi adalah proses metabolisme yang mengubah senyawa organik kompleks (karbohidrat, lemak, protein) menjadi senyawa lebih sederhana (CO2, H2O, dan energi) menggunakan oksigen. Pada buah-buahan pascapanen, respirasi berjalan terus meskipun buah telah dipisahkan dari pohonnya, dan laju respirasi yang tinggi berkorelasi dengan cepatnya penurunan cadangan makanan dan kualitas buah.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Fase Respirasi pada Buah Klimaterik dan Non Klimaterik', 'isi' => 'Buah klimaterik menunjukkan pola respirasi yang ditandai dengan fase pra-klimaterik (laju respirasi rendah), klimaterik (lonjakan tajam laju respirasi), dan pasca-klimaterik (penurunan), sementara buah non klimaterik menunjukkan penurunan laju respirasi yang bertahap dan stabil tanpa lonjakan.'],
                    ['judul' => 'Hubungan Laju Respirasi dengan Umur Simpan Buah', 'isi' => 'Laju respirasi yang tinggi berkorelasi dengan cepatnya penurunan cadangan makanan dan kualitas buah, sedangkan suhu penyimpanan yang rendah dapat memperlambat laju respirasi sehingga memperpanjang umur simpan buah.'],
                ],
                'alat' => ['Timbangan analitik', 'Penetrometer', 'Refraktometer', 'Termometer', 'Pisau', 'Talenan', 'Wadah penyimpanan', 'Alat tulis dan kamera'],
                'bahan' => ['Buah pisang (klimaterik)', 'Buah pepaya (klimaterik)', 'Buah jeruk (non klimaterik)', 'Buah nanas (non klimaterik)'],
                'prosedur' => [
                    'Pilih buah dalam kondisi seragam (tingkat kematangan, ukuran, bebas cacat), lalu timbang dan catat berat awal setiap buah pada hari ke-0.',
                    'Ukur kekerasan menggunakan penetrometer dan kadar gula (TSS) menggunakan refraktometer, amati dan catat warna, aroma, dan kondisi kulit buah, lalu dokumentasikan dengan kamera.',
                    'Simpan buah pada suhu ruang (25-30°C) dan lakukan pengamatan berkala setiap 2 hari sekali selama 8 hari.',
                    'Pada setiap pengamatan, timbang berat buah, ukur kekerasan dan TSS, catat perubahan warna dan aroma, lalu dokumentasikan perubahan yang terjadi dengan kamera.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Apa yang dimaksud dengan respirasi pada buah pascapanen?',
                        'opsi' => ['Proses fotosintesis yang menghasilkan oksigen', 'Proses metabolisme yang mengubah senyawa organik kompleks menjadi senyawa sederhana menggunakan oksigen', 'Proses penguapan air dari permukaan kulit buah', 'Proses pembentukan pigmen warna baru pada buah'],
                        'jawaban' => 1,
                        'penjelasan' => 'Respirasi adalah proses metabolisme yang mengubah senyawa organik kompleks (karbohidrat, lemak, protein) menjadi senyawa lebih sederhana (CO2, H2O, dan energi) menggunakan oksigen.',
                    ],
                    [
                        'pertanyaan' => 'Berapa interval waktu pengamatan berkala pada praktikum pola respirasi buah ini?',
                        'opsi' => ['Setiap hari selama 4 hari', 'Setiap 2 hari sekali selama 8 hari', 'Setiap 3 hari sekali selama 12 hari', 'Setiap minggu selama 4 minggu'],
                        'jawaban' => 1,
                        'penjelasan' => 'Prosedur meminta buah disimpan pada suhu ruang dan dilakukan pengamatan berkala setiap 2 hari sekali selama 8 hari.',
                    ],
                    [
                        'pertanyaan' => 'Fase apa saja yang menandai pola respirasi buah klimaterik?',
                        'opsi' => ['Fase awal, tengah, dan akhir', 'Fase pra-klimaterik, klimaterik, dan pasca-klimaterik', 'Fase aerob dan anaerob', 'Fase panas dan dingin'],
                        'jawaban' => 1,
                        'penjelasan' => 'Buah klimaterik menunjukkan pola respirasi yang ditandai dengan fase pra-klimaterik (laju respirasi rendah), klimaterik (lonjakan tajam), dan pasca-klimaterik (penurunan).',
                    ],
                ],
            ],
            // Objek 4 - Praktikum 4: Sifat Jaringan Buah
            [
                'judul' => 'Sifat Jaringan Buah',
                'tujuan' => [
                    'Memahami sifat jaringan buah berdasarkan tingkat kematangan',
                    'Mengamati perubahan struktur jaringan buah dari muda hingga matang',
                    'Menganalisis hubungan antara tingkat kematangan dengan karakteristik jaringan buah',
                ],
                'pendahuluan' => 'Struktur jaringan buah sangat dipengaruhi oleh tingkat kematangannya. Pada buah muda, dinding sel masih kokoh dengan kandungan protopektin (pektin tidak larut) yang tinggi sehingga buah memiliki tekstur yang keras dan renyah. Selama pematangan, enzim poligalakturonase dan pektinase mendegradasi protopektin menjadi pektin larut, menyebabkan ikatan antar sel melemah dan buah menjadi lunak.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Degradasi Dinding Sel selama Pematangan Buah', 'isi' => 'Enzim poligalakturonase dan pektinase mendegradasi protopektin menjadi pektin larut selama pematangan, menyebabkan ikatan antar sel melemah sehingga tekstur buah berubah dari keras menjadi lunak.'],
                    ['judul' => 'Perubahan Warna dan Kandungan Air Jaringan Buah', 'isi' => 'Degradasi klorofil oleh enzim klorofilase menyebabkan perubahan warna dari hijau menjadi warna matang khas (kuning, merah, oranye), sementara kandungan air dalam sel meningkat dan tekanan turgor sel berubah selama pematangan.'],
                ],
                'alat' => ['Pisau tajam', 'Talenan', 'Jangka sorong', 'Penetrometer', 'Kaca pembesar/lup', 'Cawan petri', 'Alat tulis dan kamera'],
                'bahan' => ['Pepaya muda', 'Pepaya matang', 'Pisang muda', 'Pisang matang'],
                'prosedur' => [
                    'Amati permukaan kulit buah muda dan matang secara visual, catat perbedaan warna, tekstur permukaan, dan keadaan kulit, ukur ketebalan kulit buah menggunakan jangka sorong, lalu dokumentasikan dengan kamera.',
                    'Buat irisan melintang pada bagian tengah buah, amati susunan jaringan (epidermis, mesokarp, endokarp), gambar penampang melintang buah dan beri keterangan bagian-bagiannya.',
                    'Amati warna, tekstur, dan keberadaan getah atau cairan lain pada jaringan, serta ukur ketebalan masing-masing lapisan jaringan menggunakan jangka sorong.',
                    'Tempelkan ujung penusuk penetrometer tegak lurus pada permukaan buah hingga jarum masuk sedalam ±8 mm, ukur kekerasan pada 3 titik berbeda lalu hitung rata-ratanya, dan amati kemampuan buah menahan tekanan (elastisitas jaringan) pada buah muda maupun matang.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Apa yang menyebabkan buah muda memiliki tekstur keras dan renyah?',
                        'opsi' => ['Kandungan protopektin (pektin tidak larut) yang tinggi pada dinding sel', 'Kandungan air yang sangat rendah pada jaringan buah', 'Tingginya kadar gula sederhana dalam buah', 'Tebalnya lapisan epidermis buah'],
                        'jawaban' => 0,
                        'penjelasan' => 'Pada buah muda, dinding sel masih kokoh dengan kandungan protopektin (pektin tidak larut) yang tinggi sehingga buah memiliki tekstur yang keras dan renyah.',
                    ],
                    [
                        'pertanyaan' => 'Enzim apa yang berperan mendegradasi protopektin menjadi pektin larut selama pematangan buah?',
                        'opsi' => ['Amilase dan lipase', 'Poligalakturonase dan pektinase', 'Klorofilase dan katalase', 'Protease dan selulase'],
                        'jawaban' => 1,
                        'penjelasan' => 'Selama pematangan, enzim poligalakturonase dan pektinase mendegradasi protopektin menjadi pektin larut sehingga ikatan antar sel melemah dan buah menjadi lunak.',
                    ],
                    [
                        'pertanyaan' => 'Di mana buah pepaya muda menyimpan getah lateks putih?',
                        'opsi' => ['Pada lapisan epidermis buah', 'Pada saluran latisifer', 'Pada bagian biji buah', 'Pada permukaan kulit luar buah'],
                        'jawaban' => 1,
                        'penjelasan' => 'Buah pepaya muda mengandung getah lateks putih dalam saluran latisifer, sedangkan pada pepaya matang getah tersebut sudah berkurang.',
                    ],
                ],
            ],
            // Objek 5 - Praktikum 5: Sifat Jaringan Sayur
            [
                'judul' => 'Sifat Jaringan Sayur',
                'tujuan' => [
                    'Memahami sifat jaringan sayuran berdasarkan tingkat pertumbuhan/kematangan',
                    'Mengamati perubahan struktur jaringan sayuran dari muda hingga tua',
                    'Menganalisis hubungan antara umur panen dengan mutu sayuran',
                ],
                'pendahuluan' => 'Kualitas sayuran sangat dipengaruhi oleh umur panen. Sayuran yang dipanen terlalu muda umumnya memiliki kandungan nutrisi yang belum optimal, sedangkan sayuran yang terlalu tua mengalami penurunan kualitas akibat lignifikasi atau mengerasnya dinding sel. Daun bayam dan buncis merupakan contoh sayuran yang menunjukkan perubahan jaringan yang jelas seiring umur panen.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Lignifikasi pada Sayuran yang Dipanen Terlalu Tua', 'isi' => 'Pada sayuran tua terjadi lignifikasi pada tulang daun atau serat pada dinding polong sehingga tekstur menjadi keras dan berserat, berbeda dari sayuran muda yang mulus, lunak, dan kaya gizi.'],
                    ['judul' => 'Perbedaan Kandungan Gizi Berdasarkan Umur Panen', 'isi' => 'Kandungan klorofil, vitamin C, dan mineral cenderung lebih tinggi pada sayuran muda-optimal dibandingkan sayuran yang dipanen terlalu tua, yang telah mengalami penurunan kualitas akibat lignifikasi.'],
                ],
                'alat' => ['Pisau', 'Talenan', 'Penggaris', 'Jangka sorong', 'Timbangan', 'Kaca pembesar', 'Cawan/wadah', 'Alat tulis dan kamera'],
                'bahan' => ['Daun bayam muda', 'Daun bayam tua', 'Buncis muda', 'Buncis tua'],
                'prosedur' => [
                    'Siapkan sampel sayuran dalam dua kategori (muda dan tua), bersihkan sayuran dari kotoran, lalu timbang dan catat berat masing-masing sampel.',
                    'Amati dan catat warna, tekstur permukaan, dan penampilan umum setiap sampel, ukur dimensi (panjang/lebar daun atau panjang/diameter buncis), cium dan catat karakteristik aroma, lalu dokumentasikan dengan kamera.',
                    'Buat irisan melintang pada bagian tengah tangkai daun bayam dan polong buncis, amati susunan jaringan internal menggunakan kaca pembesar, gambar penampang melintang dan beri keterangan bagian-bagiannya.',
                    'Amati tingkat lignifikasi pada tulang daun dan serat polong buncis, lalu catat perbedaan antara sampel muda dan tua.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Apa dampak yang terjadi jika sayuran dipanen terlalu tua?',
                        'opsi' => ['Kandungan nutrisi menjadi lebih optimal', 'Mengalami penurunan kualitas akibat lignifikasi atau mengerasnya dinding sel', 'Sayuran menjadi lebih lunak dan berair', 'Warna sayuran menjadi lebih hijau cerah'],
                        'jawaban' => 1,
                        'penjelasan' => 'Sayuran yang terlalu tua mengalami penurunan kualitas akibat lignifikasi atau mengerasnya dinding sel, sehingga tekstur menjadi keras dan berserat.',
                    ],
                    [
                        'pertanyaan' => 'Sayuran apa saja yang digunakan sebagai sampel pada praktikum sifat jaringan sayur ini?',
                        'opsi' => ['Kangkung dan wortel', 'Daun bayam dan buncis', 'Kubis dan brokoli', 'Terong dan tomat'],
                        'jawaban' => 1,
                        'penjelasan' => 'Daun bayam (Amaranthus sp.) dan buncis (Phaseolus vulgaris) digunakan sebagai contoh sayuran yang menunjukkan perubahan jaringan seiring umur panen.',
                    ],
                    [
                        'pertanyaan' => 'Bagaimana perbandingan kandungan klorofil, vitamin C, dan mineral pada sayuran muda-optimal dibandingkan sayuran tua?',
                        'opsi' => ['Cenderung lebih rendah pada sayuran muda-optimal', 'Cenderung lebih tinggi pada sayuran muda-optimal', 'Selalu sama pada kedua kondisi tersebut', 'Tidak dapat dibandingkan sama sekali'],
                        'jawaban' => 1,
                        'penjelasan' => 'Kandungan klorofil, vitamin C, dan mineral cenderung lebih tinggi pada sayuran muda-optimal dibandingkan sayuran yang dipanen terlalu tua.',
                    ],
                ],
            ],
            // Objek 6 - Praktikum 6: Perubahan Kimia Buah Klimaterik
            [
                'judul' => 'Perubahan Kimia Buah Klimaterik',
                'tujuan' => [
                    'Memahami pengaruh suhu penyimpanan terhadap perubahan kimia buah klimaterik',
                    'Mengetahui pengaruh penggunaan kemasan terhadap laju pematangan buah',
                    'Membandingkan perubahan yang terjadi pada penyimpanan suhu ruang dan suhu rendah',
                ],
                'pendahuluan' => 'Perubahan kimia selama pematangan buah klimaterik meliputi perubahan kandungan pati menjadi gula, degradasi asam organik, perubahan pigmen, serta degradasi pektin. Semua perubahan ini dipengaruhi oleh suhu penyimpanan dan kondisi atmosfer sekitar buah, sehingga kombinasi suhu rendah dan kemasan yang tepat merupakan teknik penanganan pascapanen yang efektif untuk memperpanjang umur simpan buah klimaterik.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Pengaruh Suhu Rendah terhadap Laju Respirasi Buah', 'isi' => 'Penyimpanan pada suhu rendah (5-10°C) memperlambat aktivitas enzimatik dan laju respirasi sehingga proses pematangan buah klimaterik berlangsung lebih lambat dibandingkan penyimpanan pada suhu ruang.'],
                    ['judul' => 'Peran Kemasan dalam Memperpanjang Umur Simpan Buah', 'isi' => 'Penggunaan kemasan plastik (modified atmosphere) dapat mengurangi kehilangan air (susut bobot) dan memperlambat perubahan komposisi atmosfer sekitar buah, sehingga kombinasi suhu rendah dan kemasan merupakan teknik penanganan pascapanen yang efektif.'],
                ],
                'alat' => ['Timbangan analitik', 'Refraktometer', 'Penetrometer', 'Termometer', 'Lemari pendingin', 'Kantong plastik PE', 'Alat tulis dan kamera'],
                'bahan' => ['Buah pisang (seragam, setengah matang)', 'Buah pepaya (seragam, setengah matang)'],
                'prosedur' => [
                    'Bagi buah menjadi 4 kelompok perlakuan: (1) suhu ruang tanpa kemasan, (2) suhu ruang dengan kemasan plastik, (3) suhu rendah tanpa kemasan, (4) suhu rendah dengan kemasan plastik. Timbang dan catat berat awal setiap kelompok, lalu lakukan pengamatan awal (warna, kekerasan, dan TSS).',
                    'Simpan kelompok 1 dan 2 pada suhu ruang (25-30°C), simpan kelompok 3 dan 4 dalam lemari pendingin (10-15°C), serta bungkus kelompok 2 dan 4 dalam kantong plastik PE sebelum disimpan.',
                    'Lakukan pengamatan setiap 2 hari sekali selama 8 hari: ukur susut bobot, kekerasan (penetrometer), TSS (refraktometer), serta amati dan catat perubahan warna dan kondisi fisik buah.',
                    'Hitung susut bobot dengan rumus [(berat awal - berat akhir)/berat awal] x 100%, lalu bandingkan hasil yang diperoleh antar keempat perlakuan.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Berapa interval waktu pengamatan pada praktikum perubahan kimia buah klimaterik ini?',
                        'opsi' => ['Setiap hari selama 4 hari', 'Setiap 2 hari sekali selama 8 hari', 'Setiap minggu selama 4 minggu', 'Setiap 3 hari sekali selama 9 hari'],
                        'jawaban' => 1,
                        'penjelasan' => 'Pengamatan berkala dilakukan setiap 2 hari sekali selama 8 hari, meliputi susut bobot, kekerasan, TSS, warna, dan kondisi fisik buah.',
                    ],
                    [
                        'pertanyaan' => 'Apa manfaat penggunaan kemasan plastik (modified atmosphere) bagi buah yang disimpan?',
                        'opsi' => ['Mempercepat proses pematangan buah', 'Mengurangi kehilangan air (susut bobot) dan memperlambat perubahan komposisi atmosfer sekitar buah', 'Meningkatkan produksi etilen pada buah', 'Menambah kadar gula (TSS) buah secara signifikan'],
                        'jawaban' => 1,
                        'penjelasan' => 'Kemasan plastik (modified atmosphere) dapat mengurangi kehilangan air (susut bobot) dan memperlambat perubahan komposisi atmosfer sekitar buah.',
                    ],
                    [
                        'pertanyaan' => 'Berapa rentang suhu rendah yang digunakan untuk kelompok penyimpanan suhu rendah pada praktikum ini?',
                        'opsi' => ['0-5°C', '10-15°C', '20-25°C', '25-30°C'],
                        'jawaban' => 1,
                        'penjelasan' => 'Kelompok 3 dan 4 disimpan dalam lemari pendingin pada suhu 10-15°C, sedangkan kelompok 1 dan 2 disimpan pada suhu ruang 25-30°C.',
                    ],
                ],
            ],
            // Objek 7 - Praktikum 7: Jenis dan Penyimpanan Telur
            [
                'judul' => 'Jenis dan Penyimpanan Telur',
                'tujuan' => [
                    'Mengetahui dan memahami berbagai jenis telur unggas beserta karakteristik fisiknya',
                    'Menentukan kualitas fisik telur melalui pengamatan eksternal dan internal',
                    'Mengetahui pengaruh penyimpanan terhadap kualitas telur',
                ],
                'pendahuluan' => 'Telur merupakan bahan pangan bergizi tinggi yang mengandung protein bermutu tinggi, lemak, vitamin, dan mineral. Telur yang umum dikonsumsi masyarakat antara lain telur ayam ras, telur ayam kampung, telur bebek, dan telur puyuh. Kualitas telur dapat ditentukan secara fisik melalui pengamatan eksternal (warna dan kebersihan cangkang, bentuk) maupun internal (kondisi putih telur, kuning telur, ruang udara).',
                'tinjauan_pustaka' => [
                    ['judul' => 'Indeks Putih dan Kuning Telur sebagai Indikator Kesegaran', 'isi' => 'Indeks putih telur (IPT) dan indeks kuning telur (IKT) merupakan parameter penting yang mencerminkan kesegaran telur, yang dihitung dari pengukuran tinggi dan diameter putih maupun kuning telur.'],
                    ['judul' => 'Perubahan Fisik Telur selama Penyimpanan', 'isi' => 'Selama penyimpanan, terjadi penguapan air dan CO2 melalui pori-pori cangkang sehingga ruang udara membesar dan pH putih telur meningkat, akibatnya konsistensi putih telur menjadi lebih encer dan kuning telur mudah pecah; penyimpanan pada suhu rendah dapat memperlambat proses deteriorasi ini.'],
                ],
                'alat' => ['Timbangan analitik', 'Jangka sorong', 'Cawan petri', 'Gelas beaker', 'Mangkok kecil', 'Sendok', 'Alat tulis dan kamera'],
                'bahan' => ['Telur ayam ras', 'Telur ayam kampung', 'Telur bebek', 'Telur puyuh', 'Larutan metilen blue (opsional)'],
                'prosedur' => [
                    'Amati dan catat warna, kebersihan, keretakan, dan kehalusan cangkang setiap jenis telur, ukur panjang (diameter terpanjang) dan lebar (diameter terpendek) menggunakan jangka sorong, timbang berat setiap telur, lalu hitung indeks bentuk telur dengan rumus (lebar/panjang) x 100%.',
                    'Pecahkan telur di atas cawan petri dengan hati-hati, amati warna putih telur, kejernihan, dan konsistensinya, serta amati warna dan bentuk kuning telur.',
                    'Ukur tinggi putih telur (a) dan diameter putih telur (b), serta ukur tinggi kuning telur (x) dan diameter kuning telur (y) menggunakan jangka sorong.',
                    'Hitung IPT = a/b dan IKT = x/y, lalu dokumentasikan hasil pengamatan dengan kamera.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Bagaimana cara menghitung indeks bentuk telur?',
                        'opsi' => ['(panjang/lebar) x 100%', '(lebar/panjang) x 100%', 'panjang x lebar', 'panjang - lebar'],
                        'jawaban' => 1,
                        'penjelasan' => 'Indeks bentuk telur dihitung dengan rumus (lebar/panjang) x 100%, berdasarkan pengukuran panjang (diameter terpanjang) dan lebar (diameter terpendek) telur.',
                    ],
                    [
                        'pertanyaan' => 'Apa yang terjadi pada ruang udara telur selama penyimpanan?',
                        'opsi' => ['Ruang udara mengecil karena penyerapan air dari luar', 'Ruang udara membesar akibat penguapan air dan CO2 melalui pori-pori cangkang', 'Ruang udara tetap sama tanpa perubahan', 'Ruang udara berubah warna menjadi kuning'],
                        'jawaban' => 1,
                        'penjelasan' => 'Selama penyimpanan terjadi penguapan air dan CO2 melalui pori-pori cangkang sehingga ruang udara membesar dan pH putih telur meningkat.',
                    ],
                    [
                        'pertanyaan' => 'Bagaimana rumus perhitungan Indeks Kuning Telur (IKT) pada praktikum ini?',
                        'opsi' => ['IKT = a/b', 'IKT = x/y', 'IKT = panjang/lebar', 'IKT = berat/volume'],
                        'jawaban' => 1,
                        'penjelasan' => 'IKT dihitung dengan rumus x/y, yaitu tinggi kuning telur (x) dibagi diameter kuning telur (y), sedangkan IPT dihitung dari a/b (tinggi dan diameter putih telur).',
                    ],
                ],
            ],
            // Objek 8 - Praktikum 8: Jenis dan Penyimpanan Daging
            [
                'judul' => 'Jenis dan Penyimpanan Daging',
                'tujuan' => [
                    'Mengetahui dan memahami karakteristik fisik berbagai jenis daging',
                    'Mengidentifikasi perbedaan antara daging ayam, sapi, dan kambing',
                    'Memahami pengaruh penyimpanan terhadap kualitas daging',
                ],
                'pendahuluan' => 'Daging merupakan sumber protein hewani penting yang mengandung asam amino esensial lengkap. Komposisi utama daging meliputi air (70-75%), protein (19%), lemak (2,5-5%), dan mineral (3,5%). Berbagai jenis daging memiliki karakteristik yang berbeda, dan kualitas daging dipengaruhi oleh berbagai faktor seperti pH, warna, keempukan, aroma, serta kandungan air.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Karakteristik Fisik Berbagai Jenis Daging', 'isi' => 'Daging ayam berwarna putih keabuan dengan serat pendek dan halus serta lemak yang terdistribusi di bawah kulit, daging sapi berwarna merah dengan serat lebih kasar dan lemak intramuskular (marbling), sedangkan daging kambing berwarna merah muda dengan aroma khas.'],
                    ['judul' => 'Faktor yang Memengaruhi Kualitas Daging', 'isi' => 'Kualitas daging dipengaruhi oleh berbagai faktor seperti pH, warna, keempukan, aroma, dan kandungan air, dengan pH daging segar berkisar 5,4-5,8 (pH ultimate); penyimpanan pada suhu rendah (0-4°C) dapat mempertahankan kualitas daging meski tetap harus dikonsumsi dalam waktu 3-5 hari.'],
                ],
                'alat' => ['Timbangan analitik', 'pH meter atau kertas lakmus', 'Termometer', 'Pisau', 'Talenan', 'Panci', 'Kompor', 'Alat tulis dan kamera'],
                'bahan' => ['Daging ayam', 'Daging sapi', 'Daging kambing'],
                'prosedur' => [
                    'Amati dan catat warna, tekstur permukaan, dan kondisi umum setiap jenis daging, cium dan catat karakteristik aroma, timbang berat setiap sampel, dan amati marbling (lemak intramuskular) pada daging sapi.',
                    'Kalibrasi pH meter menggunakan larutan buffer pH 4,00 dan pH 7,00 sesuai petunjuk alat, lalu tusukkan elektroda pH meter pada sampel daging (bagian tengah) sedalam 2-3 cm pada 3 titik berbeda, tunggu hingga nilai stabil, lalu baca dan catat nilai pH.',
                    'Timbang daging sebanyak 50 gram (berat awal = A), rebus dalam panci selama 30 menit (air terlebih dahulu dididihkan), tiriskan dan keringkan permukaan daging dengan kertas tisu, lalu timbang kembali berat daging setelah dimasak (berat akhir = B).',
                    'Hitung persentase susut masak (cooking loss) dengan rumus [(A-B)/A] x 100%.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Berapa kisaran pH daging segar (pH ultimate) menurut modul ini?',
                        'opsi' => ['3,0-3,5', '4,5-5,0', '5,4-5,8', '6,5-7,0'],
                        'jawaban' => 2,
                        'penjelasan' => 'pH daging segar (pH ultimate) berkisar 5,4-5,8 menurut modul praktikum ini.',
                    ],
                    [
                        'pertanyaan' => 'Apa yang dimaksud dengan marbling pada daging sapi?',
                        'opsi' => ['Warna merah pekat pada permukaan daging', 'Lemak intramuskular yang terdistribusi di antara serat daging', 'Tekstur daging yang sangat lunak', 'Aroma khas pada daging kambing'],
                        'jawaban' => 1,
                        'penjelasan' => 'Marbling adalah lemak intramuskular yang terdistribusi di antara serat daging, dan merupakan salah satu ciri khas daging sapi.',
                    ],
                    [
                        'pertanyaan' => 'Bagaimana rumus menghitung persentase susut masak (cooking loss) pada praktikum ini?',
                        'opsi' => ['(berat akhir/berat awal) x 100%', '[(berat awal - berat akhir)/berat awal] x 100%', 'berat awal - berat akhir', '(berat awal + berat akhir)/2'],
                        'jawaban' => 1,
                        'penjelasan' => 'Persentase susut masak dihitung dengan rumus [(A-B)/A] x 100%, di mana A adalah berat awal daging dan B adalah berat akhir daging setelah dimasak.',
                    ],
                ],
            ],
            // Objek 9 - Praktikum 9: Jenis dan Proses Pembuatan Susu
            [
                'judul' => 'Jenis dan Proses Pembuatan Susu',
                'tujuan' => [
                    'Mengetahui dan memahami jenis-jenis produk susu yang beredar di pasaran',
                    'Mengidentifikasi perbedaan karakteristik antara susu UHT, susu bubuk, dan susu kental manis',
                    'Memahami proses pembuatan dan pengolahan berbagai produk susu',
                ],
                'pendahuluan' => 'Susu merupakan cairan bergizi yang dihasilkan oleh kelenjar susu mamalia betina, mengandung protein, lemak, laktosa, mineral, dan vitamin. Susu segar sangat mudah rusak sehingga diperlukan pengolahan untuk memperpanjang umur simpannya, misalnya menjadi susu UHT, susu bubuk, atau susu kental manis, yang masing-masing memiliki karakteristik fisik, gizi, dan kegunaan berbeda.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Proses Pengolahan Susu UHT', 'isi' => 'Susu UHT (Ultra High Temperature) diproses pada suhu sangat tinggi (135-150°C) selama 2-5 detik sehingga semua bakteri patogen dan sebagian besar bakteri lain mati, memberikan umur simpan 6-12 bulan pada suhu ruang tanpa bahan pengawet.'],
                    ['judul' => 'Proses Pembuatan Susu Bubuk dan Susu Kental Manis', 'isi' => 'Susu bubuk dibuat melalui proses pengeringan (spray drying atau drum drying) untuk mengurangi kadar air hingga di bawah 5%, sedangkan susu kental manis dibuat dengan menguapkan sebagian air susu dan menambahkan gula sebagai pengawet alami.'],
                ],
                'alat' => ['Gelas ukur', 'Timbangan', 'Termometer', 'Sendok', 'Cawan/wadah', 'pH meter atau kertas lakmus', 'Alat tulis dan kamera'],
                'bahan' => ['Susu UHT (minimal 2 merek berbeda)', 'Susu bubuk (minimal 2 merek berbeda)', 'Susu kental manis (minimal 2 merek berbeda)'],
                'prosedur' => [
                    'Amati kemasan setiap produk susu meliputi jenis kemasan, warna, dan informasi yang tercantum, catat komposisi/kandungan gizi yang tertera pada label (protein, lemak, karbohidrat, energi), serta catat informasi tanggal produksi, tanggal kedaluwarsa, dan petunjuk penyimpanan.',
                    'Amati dan catat warna produk susu, cium dan catat karakteristik aroma, serta ukur pH menggunakan pH meter atau kertas lakmus.',
                    'Amati konsistensi/viskositas (cair, kental, bubuk) masing-masing produk, lalu dokumentasikan setiap produk dengan kamera.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Pada suhu berapa susu UHT diproses menurut modul ini?',
                        'opsi' => ['60-70°C selama 30 menit', '100-110°C selama 1 menit', '135-150°C selama 2-5 detik', '180-200°C selama 10 detik'],
                        'jawaban' => 2,
                        'penjelasan' => 'Susu UHT diproses pada suhu sangat tinggi (135-150°C) selama 2-5 detik sehingga semua bakteri patogen dan sebagian besar bakteri lain mati.',
                    ],
                    [
                        'pertanyaan' => 'Bagaimana proses pembuatan susu bubuk menurut modul ini?',
                        'opsi' => ['Melalui fermentasi menggunakan bakteri asam laktat', 'Melalui proses pengeringan (spray drying atau drum drying) hingga kadar air di bawah 5%', 'Melalui pembekuan pada suhu sangat rendah', 'Melalui penambahan bahan pengawet kimia'],
                        'jawaban' => 1,
                        'penjelasan' => 'Susu bubuk dibuat melalui proses pengeringan (spray drying atau drum drying) untuk mengurangi kadar air hingga di bawah 5%.',
                    ],
                    [
                        'pertanyaan' => 'Apa yang ditambahkan pada pembuatan susu kental manis sebagai pengawet alami?',
                        'opsi' => ['Garam', 'Gula', 'Asam sitrat', 'Natrium benzoat'],
                        'jawaban' => 1,
                        'penjelasan' => 'Susu kental manis dibuat dengan menguapkan sebagian air susu dan menambahkan gula sebagai pengawet alami.',
                    ],
                ],
            ],
            // Objek 10 - Praktikum 10: Serealia, Kacang-kacangan, dan Umbi-umbian
            [
                'judul' => 'Serealia, Kacang-kacangan, dan Umbi-umbian',
                'tujuan' => [
                    'Memahami karakteristik fisik serealia, kacang-kacangan, dan umbi-umbian',
                    'Mengetahui cara penanganan pasca panen bahan-bahan tersebut',
                    'Membandingkan sifat fisik berbagai jenis serealia, kacang-kacangan, dan umbi-umbian',
                ],
                'pendahuluan' => 'Serealia adalah tanaman biji-bijian dari keluarga Poaceae yang merupakan sumber karbohidrat utama bagi manusia, dengan komponen utama berupa pati (70-90%). Kacang-kacangan termasuk famili Leguminosae yang kaya akan protein nabati (20-40%) dan serat pangan, sedangkan umbi-umbian merupakan organ tanaman penyimpan cadangan makanan yang mengandung pati tinggi.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Komposisi Utama Serealia dan Kacang-kacangan', 'isi' => 'Komponen utama serealia adalah pati (70-90%) yang tersusun dari amilosa dan amilopektin, sedangkan kacang-kacangan kaya akan protein nabati (20-40%) dan serat pangan, dengan kandungan protein kedelai mencapai 40% sehingga menjadi salah satu sumber protein nabati terkaya.'],
                    ['judul' => 'Peran Umbi-umbian sebagai Sumber Karbohidrat', 'isi' => 'Umbi-umbian seperti ubi kayu, ubi jalar, dan kentang merupakan sumber karbohidrat penting yang mengandung pati tinggi, sehingga penanganan pasca panen yang tepat sangat penting untuk mempertahankan kualitas ketiga kelompok bahan ini.'],
                ],
                'alat' => ['Jangka sorong', 'Timbangan', 'Gelas ukur 100 ml', 'Panci', 'Tabung reaksi', 'Termometer', 'Alat tulis dan kamera'],
                'bahan' => ['Beras (beras putih, beras merah)', 'Jagung', 'Kacang tanah', 'Kacang kedelai', 'Kacang hijau', 'Ubi kayu', 'Ubi jalar', 'Kentang'],
                'prosedur' => [
                    'Amati dan catat warna, bentuk, dan aroma masing-masing bahan, gambar bentuk bahan secara utuh, identifikasi bagian-bagian bahan yang terlihat, lalu dokumentasikan dengan kamera.',
                    'Ukur panjang, lebar, dan tebal menggunakan jangka sorong untuk setiap jenis bahan, timbang 100 butir bahan dan nyatakan dalam gram/100 butir, lalu catat semua data pada tabel.',
                    'Masukkan bahan ke dalam gelas ukur 100 ml hingga penuh, keluarkan bahan dari gelas ukur dan timbang beratnya, hitung densitas kamba dengan rumus berat bahan (g)/volume (ml), lalu bandingkan densitas kamba antar bahan.',
                    'Untuk pengembangan serealia: siapkan sampel serealia (beras dan jagung) masing-masing 10 gram, ukur volume awal bahan menggunakan gelas ukur, rendam bahan dalam air selama ±30 menit untuk membantu proses hidrasi, lalu rebus bahan dalam panci hingga matang dan mengembang sempurna.',
                    'Tiriskan bahan dan biarkan hingga suhu ruang, ukur kembali volume bahan setelah perebusan menggunakan gelas ukur, timbang berat akhir bahan, lalu hitung persentase pengembangan dengan rumus [(volume akhir - volume awal)/volume awal] x 100% dan bandingkan kemampuan pengembangan masing-masing jenis serealia.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Bagaimana rumus menghitung densitas kamba suatu bahan?',
                        'opsi' => ['Volume (ml) dibagi berat bahan (g)', 'Berat bahan (g) dibagi volume (ml)', 'Berat bahan (g) dikali volume (ml)', 'Berat bahan (g) dikurangi volume (ml)'],
                        'jawaban' => 1,
                        'penjelasan' => 'Densitas kamba dihitung dengan rumus berat bahan (g) dibagi volume (ml), setelah bahan dimasukkan ke dalam gelas ukur 100 ml hingga penuh.',
                    ],
                    [
                        'pertanyaan' => 'Berapa lama sampel serealia direndam sebelum direbus pada uji pengembangan serealia?',
                        'opsi' => ['±10 menit', '±30 menit', '±60 menit', '±120 menit'],
                        'jawaban' => 1,
                        'penjelasan' => 'Bahan direndam dalam air selama ±30 menit untuk membantu proses hidrasi sebelum direbus hingga matang dan mengembang sempurna.',
                    ],
                    [
                        'pertanyaan' => 'Apa komponen utama yang menyusun pati pada serealia?',
                        'opsi' => ['Protein dan lemak', 'Amilosa dan amilopektin', 'Selulosa dan hemiselulosa', 'Glukosa dan fruktosa bebas'],
                        'jawaban' => 1,
                        'penjelasan' => 'Komponen utama serealia adalah pati (70-90%) yang tersusun dari amilosa dan amilopektin.',
                    ],
                ],
            ],
            // Objek 11 - Praktikum 11: Tanaman Karet, Kelapa, dan Kelapa Sawit
            [
                'judul' => 'Tanaman Karet, Kelapa, dan Kelapa Sawit',
                'tujuan' => [
                    'Memahami karakteristik tanaman karet, kelapa, dan kelapa sawit',
                    'Mengidentifikasi berbagai produk turunan dari karet, kelapa, dan kelapa sawit',
                    'Mengetahui proses pengolahan dan penanganan bahan perkebunan tersebut',
                ],
                'pendahuluan' => 'Karet alam (Hevea brasiliensis) merupakan tanaman perkebunan penghasil lateks yang menjadi bahan baku industri karet. Kelapa (Cocos nucifera) merupakan tanaman serba guna yang hampir seluruh bagiannya dapat dimanfaatkan. Kelapa sawit (Elaeis guineensis) menghasilkan CPO dari mesokarp buah dan PKO dari inti biji, dan Indonesia merupakan produsen minyak sawit terbesar di dunia.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Lateks sebagai Bahan Baku Industri Karet', 'isi' => 'Lateks adalah cairan berwarna putih seperti susu yang mengandung 25-40% karet (poliisoprena), protein, lipid, dan air, yang dapat diolah menjadi berbagai produk seperti RSS (Ribbed Smoked Sheet), SIR (Standard Indonesian Rubber), dan lateks pekat.'],
                    ['judul' => 'Produk Turunan Kelapa dan Kelapa Sawit', 'isi' => 'Daging buah kelapa dapat diolah menjadi santan, minyak kelapa, VCO (Virgin Coconut Oil), dan kelapa parut kering, sedangkan kelapa sawit menghasilkan CPO (Crude Palm Oil) dari mesokarp buah dan PKO (Palm Kernel Oil) dari inti biji yang digunakan untuk industri pangan maupun non-pangan.'],
                ],
                'alat' => ['Pisau', 'Talenan', 'Timbangan', 'Gelas ukur', 'Wadah/cawan', 'Alat tulis dan kamera'],
                'bahan' => ['Buah kelapa (muda, setengah tua, tua)', 'Buah kelapa sawit (TBS atau brondolan)', 'Lateks segar atau bahan karet jadi (jika tersedia)', 'Produk olahan kelapa (VCO, santan kemasan, minyak kelapa)'],
                'prosedur' => [
                    'Amati sampel lateks atau produk karet yang tersedia, catat warna, konsistensi, dan bau lateks, identifikasi produk-produk turunan karet yang ada, lalu dokumentasikan dengan kamera.',
                    'Amati buah kelapa muda, setengah tua, dan tua, catat perbedaan ukuran, berat, warna kulit, dan kondisi sabut, lalu buka buah kelapa dan amati ketebalan daging, warna, tekstur, dan volume air.',
                    'Timbang berat daging kelapa dari masing-masing tingkat kematangan, lalu dokumentasikan perbedaan yang teramati.',
                    'Amati buah kelapa sawit meliputi warna, ukuran, dan bagian-bagiannya (eksokarp, mesokarp, endokarp, inti), lalu identifikasi perbedaan Tandan Buah Segar (TBS) yang sudah matang dan yang belum matang.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Berapa persen kandungan karet (poliisoprena) dalam lateks segar?',
                        'opsi' => ['5-10%', '25-40%', '60-70%', '80-90%'],
                        'jawaban' => 1,
                        'penjelasan' => 'Lateks adalah cairan berwarna putih seperti susu yang mengandung 25-40% karet (poliisoprena), protein, lipid, dan air.',
                    ],
                    [
                        'pertanyaan' => 'Bagian buah kelapa sawit manakah yang menghasilkan CPO (Crude Palm Oil)?',
                        'opsi' => ['Inti biji (kernel)', 'Mesokarp buah', 'Eksokarp (kulit luar)', 'Endokarp (cangkang)'],
                        'jawaban' => 1,
                        'penjelasan' => 'Kelapa sawit menghasilkan CPO (Crude Palm Oil) dari mesokarp buah dan PKO (Palm Kernel Oil) dari inti biji.',
                    ],
                    [
                        'pertanyaan' => 'Produk apa saja yang dapat diolah dari daging buah kelapa menurut modul ini?',
                        'opsi' => ['Santan, minyak kelapa, VCO, dan kelapa parut kering', 'Karet RSS dan SIR', 'CPO dan PKO', 'Gula pasir dan gula merah'],
                        'jawaban' => 0,
                        'penjelasan' => 'Daging buah kelapa dapat diolah menjadi santan, minyak kelapa, VCO (Virgin Coconut Oil), dan kelapa parut kering (desiccated coconut).',
                    ],
                ],
            ],
            // Objek 12 - Praktikum 12: Tanaman Teh, Kopi, dan Kakao
            [
                'judul' => 'Tanaman Teh, Kopi, dan Kakao',
                'tujuan' => [
                    'Memahami karakteristik bahan baku teh, kopi, dan kakao',
                    'Mengidentifikasi berbagai produk olahan dari teh, kopi, dan kakao',
                    'Mengetahui proses pengolahan teh, kopi, dan kakao secara umum',
                ],
                'pendahuluan' => 'Teh (Camellia sinensis) merupakan tanaman perkebunan yang daunnya diproses menjadi berbagai jenis minuman teh, diklasifikasikan menjadi teh hijau (tanpa fermentasi), teh oolong (semi fermentasi), dan teh hitam (fermentasi penuh). Kopi (Coffea sp.) menghasilkan biji kopi yang mengandung kafein, sedangkan kakao (Theobroma cacao) menghasilkan biji yang diolah menjadi produk cokelat.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Klasifikasi Teh Berdasarkan Proses Fermentasi', 'isi' => 'Teh diklasifikasikan menjadi teh hijau (tanpa fermentasi), teh oolong (semi fermentasi), dan teh hitam (fermentasi penuh), dengan katekin (polifenol antioksidan) dan kafein sebagai senyawa aktif utamanya.'],
                    ['judul' => 'Proses Pengolahan Biji Kopi dan Kakao', 'isi' => 'Biji kopi mengalami serangkaian proses pascapanen meliputi pengupasan, fermentasi, pencucian, pengeringan, sortasi, dan penyangraian, sementara fermentasi biji kakao merupakan tahap kritis yang menentukan cita rasa cokelat pada produk olahannya.'],
                ],
                'alat' => ['Timbangan', 'Gelas ukur', 'Cawan/wadah', 'Pisau', 'Kompor', 'Alat tulis dan kamera'],
                'bahan' => ['Teh (teh hijau, teh hitam, teh oolong)', 'Biji kopi sangrai (arabika, robusta, dan liberika)', 'Biji kakao atau produk kakao', 'Air panas'],
                'prosedur' => [
                    'Amati dan catat warna, bentuk, ukuran, dan aroma bahan baku teh, kopi, dan kakao, bandingkan perbedaan antar jenis teh, kopi, dan produk kakao, lalu timbang masing-masing sampel dan dokumentasikan dengan kamera.',
                    'Seduh 5 gram teh, kopi, dan kakao bubuk dengan 200 ml air panas (90-95°C) selama 5 menit, amati warna, kejernihan, aroma, dan rasa seduhan masing-masing, lalu dokumentasikan hasil seduhan.',
                    'Kumpulkan berbagai produk olahan teh, kopi, dan kakao yang tersedia, amati dan catat karakteristik setiap produk, baca informasi label, lalu bandingkan produk-produk tersebut berdasarkan karakteristik fisik dan informasi label.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Bagaimana teh diklasifikasikan berdasarkan proses fermentasinya?',
                        'opsi' => ['Teh muda, teh sedang, dan teh tua', 'Teh hijau (tanpa fermentasi), teh oolong (semi fermentasi), dan teh hitam (fermentasi penuh)', 'Teh manis, teh pahit, dan teh asam', 'Teh panas, teh dingin, dan teh hangat'],
                        'jawaban' => 1,
                        'penjelasan' => 'Berdasarkan proses pengolahannya, teh diklasifikasikan menjadi teh hijau (tanpa fermentasi), teh oolong (semi fermentasi), dan teh hitam (fermentasi penuh).',
                    ],
                    [
                        'pertanyaan' => 'Berapa suhu air yang digunakan untuk menyeduh sampel teh, kopi, dan kakao pada praktikum ini?',
                        'opsi' => ['60-70°C selama 10 menit', '90-95°C selama 5 menit', '100°C selama 1 menit', '50-60°C selama 15 menit'],
                        'jawaban' => 1,
                        'penjelasan' => 'Sampel diseduh menggunakan 200 ml air panas dengan suhu 90-95°C selama 5 menit, kemudian diamati warna, kejernihan, aroma, dan rasanya.',
                    ],
                    [
                        'pertanyaan' => 'Tahap apa yang menjadi kunci penentu cita rasa cokelat pada pengolahan biji kakao?',
                        'opsi' => ['Pengupasan kulit buah kakao', 'Fermentasi biji kakao', 'Pengeringan di bawah sinar matahari', 'Pengemasan produk akhir'],
                        'jawaban' => 1,
                        'penjelasan' => 'Proses fermentasi biji kakao merupakan tahap kritis yang menentukan cita rasa cokelat pada produk olahannya.',
                    ],
                ],
            ],
            // Objek 13 - Praktikum 13: Bahan Perkebunan (Kayu Manis, Cengkeh, Tebu)
            [
                'judul' => 'Bahan Perkebunan (Kayu Manis, Cengkeh, dan Tebu)',
                'tujuan' => [
                    'Memahami karakteristik fisik kayu manis, cengkeh, dan tebu',
                    'Mengidentifikasi produk-produk yang dihasilkan dari masing-masing bahan perkebunan tersebut',
                    'Mengetahui proses penanganan dan pengolahan bahan perkebunan tersebut',
                ],
                'pendahuluan' => 'Kayu manis (Cinnamomum verum) adalah rempah yang kulit batangnya digunakan sebagai bumbu masak dan bahan industri dengan sinamaldehid sebagai komponen aktif utama. Cengkeh (Syzygium aromaticum) adalah rempah yang bunganya digunakan sebagai bumbu dan bahan baku industri. Tebu (Saccharum officinarum) merupakan tanaman penghasil gula utama di dunia.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Komponen Aktif Kayu Manis dan Cengkeh', 'isi' => 'Kayu manis mengandung sinamaldehid sebagai komponen aktif utama yang memberikan aroma dan rasa khas, sedangkan cengkeh mengandung minyak atsiri eugenol berkisar 72-90% yang menjadi ciri khasnya.'],
                    ['judul' => 'Ekstraksi Gula dari Batang Tebu', 'isi' => 'Batang tebu mengandung sukrosa 12-16% yang diekstraksi melalui proses penggilingan untuk menghasilkan gula sebagai produk turunan utamanya.'],
                ],
                'alat' => ['Pisau', 'Talenan', 'Timbangan', 'Penggaris', 'Wadah/cawan', 'Alat tulis dan kamera'],
                'bahan' => ['Batang kayu manis', 'Bunga cengkeh kering', 'Batang tebu segar', 'Produk turunan (gula pasir, minyak cengkeh jika tersedia)'],
                'prosedur' => [
                    'Amati kulit kayu manis meliputi warna luar, warna dalam, aroma, dan tekstur, lalu patahkan satu batang kayu manis dan amati bagian dalamnya.',
                    'Amati cengkeh kering meliputi warna, bentuk (kepala dan tangkai), dan aroma, timbang 10 butir cengkeh, lalu ukur dimensinya menggunakan penggaris.',
                    'Amati batang tebu meliputi warna, bentuk ruas-ruas, dan kondisi permukaan, lalu timbang dan ukur panjang serta diameter batang tebu.',
                    'Kupas kulit tebu dan amati warna serta tekstur daging batang, cicipi dan catat tingkat kemanisan, lalu dokumentasikan dengan kamera.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Berapa kandungan minyak atsiri eugenol pada cengkeh menurut modul ini?',
                        'opsi' => ['10-20%', '30-50%', '72-90%', '95-99%'],
                        'jawaban' => 2,
                        'penjelasan' => 'Kandungan minyak atsiri eugenol pada cengkeh berkisar 72-90%, yang menjadi ciri khasnya.',
                    ],
                    [
                        'pertanyaan' => 'Berapa persen kandungan sukrosa pada batang tebu menurut modul ini?',
                        'opsi' => ['2-5%', '12-16%', '25-30%', '40-50%'],
                        'jawaban' => 1,
                        'penjelasan' => 'Batang tebu mengandung sukrosa 12-16% yang diekstraksi melalui proses penggilingan.',
                    ],
                    [
                        'pertanyaan' => 'Bagian tanaman cengkeh manakah yang digunakan sebagai bumbu dan bahan baku industri?',
                        'opsi' => ['Akar', 'Bunga (kuncup bunga/buds)', 'Batang kayu', 'Umbi'],
                        'jawaban' => 1,
                        'penjelasan' => 'Cengkeh adalah rempah yang bunganya (buds) digunakan sebagai bumbu dan bahan baku industri.',
                    ],
                ],
            ],
            // Objek 14 - Praktikum 14: Rempah dan Oleoresin
            [
                'judul' => 'Rempah dan Oleoresin',
                'tujuan' => [
                    'Mengetahui dan mengidentifikasi berbagai jenis rempah-rempah',
                    'Mengenal karakteristik fisik rempah meliputi warna, aroma, dan tekstur',
                    'Memahami pengertian oleoresin dan sumber rempah penghasilnya',
                ],
                'pendahuluan' => 'Rempah-rempah adalah bagian tanaman (akar, rimpang, kulit, bunga, biji, atau daun) yang mengandung senyawa aromatik dan digunakan sebagai bumbu atau pemberi cita rasa makanan. Oleoresin adalah campuran resin dan minyak atsiri yang diperoleh melalui ekstraksi menggunakan pelarut organik dari bahan rempah-rempah, dan lebih stabil serta mudah diaplikasikan dibanding minyak atsiri murni.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Definisi dan Komponen Oleoresin', 'isi' => 'Oleoresin adalah campuran resin dan minyak atsiri yang diperoleh melalui ekstraksi menggunakan pelarut organik, mengandung komponen volatile (minyak atsiri) dan non-volatile (resin) yang memberikan karakter aroma dan rasa khas setiap rempah.'],
                    ['judul' => 'Senyawa Aktif Khas pada Berbagai Jenis Rempah', 'isi' => 'Kunyit mengandung kurkumin sebagai pigmen kuning dan minyak atsiri, jahe mengandung gingerol dan shogaol yang memberikan rasa pedas dan hangat, sedangkan lengkuas, kencur, kemiri, dan pala masing-masing memiliki senyawa aktif khas yang memberikan aroma dan manfaat spesifik.'],
                ],
                'alat' => ['Pisau', 'Talenan', 'Timbangan', 'Wadah/cawan', 'Alat tulis dan kamera'],
                'bahan' => ['Kunyit', 'Jahe', 'Lengkuas', 'Kencur', 'Kemiri', 'Pala', 'Bawang putih', 'Bawang merah', 'Cabai merah', 'Lada'],
                'prosedur' => [
                    'Amati dan gambar bentuk masing-masing rempah secara utuh, catat ukuran (panjang dan diameter rimpang atau biji), serta catat tekstur permukaan (halus, kasar, berserabut, dan lain-lain).',
                    'Amati dan catat warna bagian luar (kulit) masing-masing rempah, kupas atau iris rempah dan amati warna bagian dalam setelah diiris, lalu catat perubahan warna yang terjadi akibat oksidasi.',
                    'Buat irisan melintang dan membujur masing-masing rempah, amati dan gambar lapisan-lapisan yang terlihat, beri keterangan bagian-bagiannya.',
                    'Kenali dan catat aroma khas masing-masing rempah dengan pembauan, lalu bandingkan intensitas aroma antar rempah.',
                ],
                'kuis' => [
                    [
                        'pertanyaan' => 'Apa yang dimaksud dengan oleoresin?',
                        'opsi' => ['Larutan garam yang digunakan untuk mengawetkan rempah', 'Campuran resin dan minyak atsiri hasil ekstraksi menggunakan pelarut organik dari bahan rempah-rempah', 'Ekstrak air rempah yang direbus dalam air mendidih', 'Serbuk rempah yang dihaluskan tanpa proses ekstraksi'],
                        'jawaban' => 1,
                        'penjelasan' => 'Oleoresin adalah campuran resin dan minyak atsiri yang diperoleh melalui ekstraksi menggunakan pelarut organik dari bahan rempah-rempah.',
                    ],
                    [
                        'pertanyaan' => 'Senyawa apa yang memberikan warna kuning pada kunyit?',
                        'opsi' => ['Gingerol', 'Kurkumin', 'Eugenol', 'Sinamaldehid'],
                        'jawaban' => 1,
                        'penjelasan' => 'Kunyit (Curcuma longa) mengandung kurkumin sebagai pigmen kuning dan minyak atsiri.',
                    ],
                    [
                        'pertanyaan' => 'Senyawa apa pada jahe yang memberikan rasa pedas dan hangat?',
                        'opsi' => ['Kurkumin dan eugenol', 'Gingerol dan shogaol', 'Sinamaldehid dan mentol', 'Kafein dan katekin'],
                        'jawaban' => 1,
                        'penjelasan' => 'Jahe (Zingiber officinale) mengandung gingerol dan shogaol yang memberikan rasa pedas dan hangat.',
                    ],
                ],
            ],
        ];
    }
}
