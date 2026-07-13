<?php

namespace Database\Seeders;

use App\Models\Matakuliah;
use App\Models\Materi;
use App\Models\Praktikum;
use App\Models\Prodi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Mengisi konten untuk mata kuliah Mekanika Fluida, Teknik Pasca Panen,
 * dan Perbengkelan (Semester 4, prodi TEP).
 *
 * CATATAN: konten disusun sendiri (tidak berdasarkan modul yang diunggah),
 * karena belum ada modul penuntun praktikum semester 4 yang tersedia.
 * Silakan disesuaikan lagi kalau ada modul resmi dari kampus.
 *
 * PENTING: sesuaikan $matakuliahSlug di method run() dengan slug asli
 * di tabel matakuliahs untuk ketiga mata kuliah ini (di sini saya
 * menebak 'tep-s4-mekanika-fluida', 'tep-s4-teknik-pasca-panen',
 * dan 'tep-s4-perbengkelan' mengikuti pola 'tep-s3-termodinamika' dst).
 *
 * Pemisahan konten:
 * - Materi  = teori saja (Pendahuluan + Tinjauan Pustaka).
 * - Praktikum = pelaksanaan (Tujuan, Alat, Bahan, Prosedur/Langkah Kerja, Kuis).
 */
class TepSemester4KontenSeeder extends Seeder
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

        $this->seedMatakuliah($prodi, 'tep-s4-mekanika-fluida', $this->mekanikaFluida());
        $this->seedMatakuliah($prodi, 'tep-s4-teknik-pasca-panen', $this->teknikPascaPanen());
        $this->seedMatakuliah($prodi, 'tep-s4-perbengkelan', $this->perbengkelan());
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

    protected function mekanikaFluida(): array
    {
        return [
            [
                'judul' => 'Sifat-sifat Fisik Fluida',
                'tujuan' => [
                    'Menentukan densitas (massa jenis) berbagai jenis fluida',
                    'Menentukan viskositas fluida secara sederhana',
                    'Memahami perbedaan sifat fluida Newtonian dan non-Newtonian',
                ],
                'pendahuluan' => 'Fluida (zat cair dan gas) memiliki sifat-sifat fisik seperti densitas, viskositas, dan tegangan permukaan yang menentukan perilakunya saat mengalir. Pemahaman sifat-sifat dasar fluida ini penting sebagai landasan dalam mempelajari mekanika fluida terapan di bidang pertanian, seperti perancangan sistem irigasi dan penanganan hasil pertanian cair.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Densitas dan Berat Jenis Fluida', 'isi' => 'Densitas (massa jenis) adalah perbandingan massa fluida terhadap volumenya, sedangkan berat jenis adalah perbandingan densitas fluida terhadap densitas air sebagai acuan.'],
                    ['judul' => 'Viskositas Fluida', 'isi' => 'Viskositas menggambarkan kekentalan atau hambatan aliran suatu fluida akibat gesekan antar lapisan fluida yang bergerak relatif satu sama lain.'],
                    ['judul' => 'Fluida Newtonian dan Non-Newtonian', 'isi' => 'Fluida Newtonian memiliki viskositas yang konstan terlepas dari besar gaya geser yang diberikan, sedangkan fluida non-Newtonian viskositasnya berubah seiring perubahan gaya geser, contohnya santan dan saus kental.'],
                    ['judul' => 'Tegangan Permukaan', 'isi' => 'Tegangan permukaan adalah gaya yang bekerja pada permukaan fluida akibat tarik-menarik antar molekul, dan memengaruhi fenomena kapilaritas.'],
                    ['judul' => 'Aplikasi Sifat Fluida pada Pertanian', 'isi' => 'Sifat fluida seperti viskositas dan densitas penting dipertimbangkan dalam merancang pompa irigasi, alat penyemprot pestisida, dan pengolahan produk cair hasil pertanian.'],
                ],
                'alat' => ['Piknometer/gelas ukur', 'Neraca analitik', 'Tabung viskometer bola jatuh sederhana', 'Stopwatch', 'Termometer'],
                'bahan' => ['Air', 'Minyak goreng', 'Santan', 'Larutan gula pekat'],
                'prosedur' => [
                    'Timbang gelas ukur/piknometer kosong, lalu isi dengan sampel fluida dan timbang kembali untuk menghitung densitas.',
                    'Ulangi pengukuran densitas untuk setiap jenis fluida sampel (air, minyak goreng, santan, larutan gula) untuk dibandingkan.',
                    'Jatuhkan bola kecil ke dalam tabung berisi fluida sampel, ukur waktu tempuh bola pada jarak tertentu menggunakan stopwatch.',
                    'Hitung viskositas fluida secara sederhana berdasarkan kecepatan jatuh bola (metode bola jatuh).',
                    'Ulangi pengukuran pada suhu fluida yang berbeda untuk mengamati pengaruh suhu terhadap viskositas.',
                    'Bandingkan hasil viskositas antar fluida untuk mengidentifikasi mana yang bersifat Newtonian dan non-Newtonian.',
                ],
                'kuis' => [
                    ['pertanyaan' => 'Densitas fluida didefinisikan sebagai...', 'opsi' => ['Massa dibagi volume', 'Volume dibagi massa', 'Massa dikali volume', 'Berat dibagi luas'], 'jawaban' => 0, 'penjelasan' => 'Densitas (massa jenis) adalah perbandingan massa fluida terhadap volumenya.'],
                    ['pertanyaan' => 'Fluida yang viskositasnya berubah seiring perubahan gaya geser disebut fluida...', 'opsi' => ['Newtonian', 'Non-Newtonian', 'Ideal', 'Kompresibel'], 'jawaban' => 1, 'penjelasan' => 'Fluida non-Newtonian memiliki viskositas yang berubah seiring perubahan gaya geser yang diberikan, berbeda dengan fluida Newtonian yang viskositasnya konstan.'],
                    ['pertanyaan' => 'Metode bola jatuh pada praktikum ini digunakan untuk mengukur...', 'opsi' => ['Densitas', 'Viskositas', 'Tegangan permukaan', 'Suhu'], 'jawaban' => 1, 'penjelasan' => 'Metode bola jatuh mengukur viskositas fluida berdasarkan kecepatan jatuh bola di dalam fluida tersebut.'],
                    ['pertanyaan' => 'Pada umumnya, bagaimana pengaruh kenaikan suhu terhadap viskositas fluida cair?', 'opsi' => ['Viskositas meningkat', 'Viskositas menurun', 'Viskositas tidak berubah', 'Viskositas menjadi nol'], 'jawaban' => 1, 'penjelasan' => 'Pada fluida cair, viskositas umumnya menurun seiring kenaikan suhu karena gaya antar molekul melemah.'],
                    ['pertanyaan' => 'Fenomena kapilaritas pada fluida disebabkan oleh...', 'opsi' => ['Densitas fluida', 'Tegangan permukaan fluida', 'Viskositas fluida', 'Suhu fluida'], 'jawaban' => 1, 'penjelasan' => 'Tegangan permukaan yang timbul akibat tarik-menarik antar molekul fluida menyebabkan fenomena kapilaritas.'],
                ],
            ],
            [
                'judul' => 'Pengukuran Debit dan Kecepatan Aliran Fluida',
                'tujuan' => [
                    'Menentukan debit aliran fluida pada saluran/pipa',
                    'Menentukan kecepatan aliran fluida menggunakan metode volumetrik',
                    'Memahami hubungan debit, kecepatan, dan luas penampang aliran (persamaan kontinuitas)',
                ],
                'pendahuluan' => 'Debit aliran fluida merupakan volume fluida yang mengalir melalui suatu penampang per satuan waktu, dan menjadi parameter penting dalam perancangan sistem irigasi maupun pengolahan hasil pertanian. Pengukuran debit dapat dilakukan melalui metode volumetrik sederhana maupun berdasarkan kecepatan aliran dan luas penampang saluran.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Konsep Debit Aliran', 'isi' => 'Debit (Q) adalah volume fluida yang mengalir melalui suatu penampang per satuan waktu, dinyatakan dalam satuan seperti liter/detik atau m³/detik.'],
                    ['judul' => 'Metode Volumetrik', 'isi' => 'Debit dapat diukur secara langsung dengan menampung fluida yang keluar dalam wadah bervolume diketahui selama selang waktu tertentu.'],
                    ['judul' => 'Persamaan Kontinuitas', 'isi' => 'Debit aliran sama dengan hasil kali kecepatan aliran dan luas penampang (Q = v × A), sehingga kecepatan aliran meningkat jika penampang saluran menyempit.'],
                    ['judul' => 'Kecepatan Aliran pada Saluran Terbuka dan Tertutup', 'isi' => 'Kecepatan aliran fluida pada pipa tertutup umumnya lebih seragam dibanding saluran terbuka yang dipengaruhi gesekan dasar saluran.'],
                    ['judul' => 'Aplikasi Pengukuran Debit di Bidang Pertanian', 'isi' => 'Pengukuran debit penting dalam menentukan kapasitas pompa irigasi maupun merancang saluran distribusi air pada lahan pertanian.'],
                ],
                'alat' => ['Gelas ukur/ember bervolume diketahui', 'Stopwatch', 'Meteran', 'Pipa/selang'],
                'bahan' => ['Air'],
                'prosedur' => [
                    'Alirkan air melalui pipa/selang dengan bukaan kran tertentu.',
                    'Tampung air yang keluar ke dalam wadah bervolume diketahui selama selang waktu tertentu menggunakan stopwatch.',
                    'Hitung debit aliran dari volume air yang tertampung dibagi waktu penampungan.',
                    'Ukur diameter/luas penampang pipa atau saluran menggunakan meteran.',
                    'Hitung kecepatan aliran fluida menggunakan persamaan kontinuitas (v = Q/A).',
                    'Ulangi pengukuran pada bukaan kran yang berbeda untuk membandingkan debit dan kecepatan aliran yang dihasilkan.',
                ],
                'kuis' => [
                    ['pertanyaan' => 'Debit aliran fluida dihitung dari...', 'opsi' => ['Volume dibagi waktu', 'Waktu dibagi volume', 'Massa dibagi waktu', 'Luas dikali massa'], 'jawaban' => 0, 'penjelasan' => 'Debit aliran dihitung dari volume fluida yang mengalir dibagi dengan waktu pengukuran.'],
                    ['pertanyaan' => 'Bunyi persamaan kontinuitas pada aliran fluida adalah...', 'opsi' => ['Q = v + A', 'Q = v x A', 'Q = v / A', 'Q = A - v'], 'jawaban' => 1, 'penjelasan' => 'Persamaan kontinuitas menyatakan debit (Q) sama dengan kecepatan aliran (v) dikali luas penampang (A).'],
                    ['pertanyaan' => 'Jika luas penampang saluran menyempit sementara debit tetap, apa yang terjadi pada kecepatan aliran?', 'opsi' => ['Meningkat', 'Menurun', 'Tetap', 'Menjadi nol'], 'jawaban' => 0, 'penjelasan' => 'Berdasarkan persamaan kontinuitas, jika luas penampang mengecil pada debit yang sama, kecepatan aliran akan meningkat.'],
                    ['pertanyaan' => 'Satuan yang umum digunakan untuk menyatakan debit aliran adalah...', 'opsi' => ['kg/m³', 'm³/detik', 'N/m²', 'm/detik²'], 'jawaban' => 1, 'penjelasan' => 'Debit aliran dinyatakan dalam satuan volume per waktu, seperti m³/detik atau liter/detik.'],
                    ['pertanyaan' => 'Metode volumetrik mengukur debit dengan cara...', 'opsi' => ['Mengukur suhu fluida', 'Menampung fluida dalam wadah bervolume diketahui selama waktu tertentu', 'Mengukur berat jenis fluida', 'Mengukur viskositas fluida'], 'jawaban' => 1, 'penjelasan' => 'Metode volumetrik mengukur debit dengan menampung fluida yang mengalir ke dalam wadah bervolume diketahui selama selang waktu tertentu.'],
                ],
            ],
        ];
    }

    protected function teknikPascaPanen(): array
    {
        return [
            [
                'judul' => 'Penanganan Pascapanen dan Pengurangan Susut Hasil Pertanian',
                'tujuan' => [
                    'Mengidentifikasi jenis-jenis susut (losses) pascapanen hasil pertanian',
                    'Memahami teknik penanganan pascapanen untuk mengurangi susut kuantitas dan kualitas',
                    'Mempraktikkan teknik sortasi dan grading sederhana pada hasil pertanian',
                ],
                'pendahuluan' => 'Penanganan pascapanen yang tepat sangat menentukan kualitas dan daya simpan hasil pertanian setelah dipanen. Tanpa penanganan yang baik, hasil pertanian dapat mengalami susut kuantitas (kehilangan berat/jumlah) maupun susut kualitas (penurunan mutu) akibat kerusakan fisik, mikrobiologis, maupun fisiologis.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Jenis-jenis Susut Pascapanen', 'isi' => 'Susut pascapanen dapat berupa susut kuantitatif (kehilangan berat/jumlah produk) dan susut kualitatif (penurunan mutu seperti kesegaran, rasa, dan nilai gizi).'],
                    ['judul' => 'Faktor Penyebab Susut', 'isi' => 'Susut pascapanen dipengaruhi oleh kerusakan mekanis saat panen dan transportasi, serangan hama/penyakit, respirasi produk, serta kondisi penyimpanan yang tidak sesuai.'],
                    ['judul' => 'Sortasi dan Grading', 'isi' => 'Sortasi memisahkan produk berdasarkan kondisi baik atau rusak, sedangkan grading mengelompokkan produk berdasarkan mutu (ukuran, warna, tingkat kematangan) untuk keperluan pemasaran.'],
                    ['judul' => 'Penanganan Segera Pascapanen', 'isi' => 'Pendinginan awal (precooling), pembersihan, dan pengemasan segera setelah panen membantu memperlambat laju kerusakan produk.'],
                    ['judul' => 'Prinsip Rantai Dingin (Cold Chain)', 'isi' => 'Menjaga suhu rendah secara konsisten mulai dari panen hingga distribusi membantu mempertahankan kesegaran dan memperpanjang umur simpan produk hortikultura.'],
                ],
                'alat' => ['Timbangan', 'Keranjang panen', 'Pisau/gunting panen', 'Wadah sortasi'],
                'bahan' => ['Buah/sayur segar (misal tomat atau cabai)'],
                'prosedur' => [
                    'Panen sampel buah/sayur menggunakan alat panen yang sesuai, hindari kerusakan mekanis.',
                    'Timbang berat awal total sampel sebelum dilakukan sortasi.',
                    'Lakukan sortasi dengan memisahkan produk yang baik dan yang rusak/cacat.',
                    'Lakukan grading pada produk yang baik berdasarkan ukuran dan tingkat kematangan.',
                    'Timbang berat masing-masing kelompok (baik, rusak, hasil grading) dan hitung persentase susut kuantitas.',
                    'Simpan sampel pada dua kondisi berbeda (suhu ruang dan suhu dingin) selama beberapa hari, amati perbedaan tingkat kerusakannya.',
                ],
                'kuis' => [
                    ['pertanyaan' => 'Susut kuantitatif pascapanen berarti...', 'opsi' => ['Penurunan rasa produk', 'Kehilangan berat/jumlah produk', 'Perubahan warna produk', 'Penurunan nilai gizi saja'], 'jawaban' => 1, 'penjelasan' => 'Susut kuantitatif berarti kehilangan berat atau jumlah produk pascapanen, berbeda dengan susut kualitatif yang berkaitan dengan penurunan mutu.'],
                    ['pertanyaan' => 'Sortasi pada penanganan pascapanen bertujuan untuk...', 'opsi' => ['Mengukur berat produk', 'Memisahkan produk yang baik dan rusak', 'Mengemas produk', 'Mendinginkan produk'], 'jawaban' => 1, 'penjelasan' => 'Sortasi bertujuan memisahkan produk yang kondisinya baik dari yang rusak atau cacat.'],
                    ['pertanyaan' => 'Grading mengelompokkan produk berdasarkan...', 'opsi' => ['Mutu seperti ukuran, warna, dan tingkat kematangan', 'Harga jual saja', 'Lokasi panen', 'Jenis alat panen yang digunakan'], 'jawaban' => 0, 'penjelasan' => 'Grading mengelompokkan produk berdasarkan mutu, seperti ukuran, warna, dan tingkat kematangan, untuk keperluan pemasaran.'],
                    ['pertanyaan' => 'Precooling dilakukan segera setelah panen dengan tujuan...', 'opsi' => ['Meningkatkan berat produk', 'Memperlambat laju kerusakan produk dengan menurunkan suhunya', 'Mengubah warna produk', 'Mempercepat pematangan produk'], 'jawaban' => 1, 'penjelasan' => 'Precooling menurunkan suhu produk segera setelah panen untuk memperlambat laju respirasi dan kerusakan.'],
                    ['pertanyaan' => 'Prinsip rantai dingin (cold chain) penting untuk...', 'opsi' => ['Menjaga kesegaran produk mulai dari panen hingga distribusi', 'Mempercepat proses panen', 'Mengurangi biaya produksi', 'Meningkatkan rasa manis produk'], 'jawaban' => 0, 'penjelasan' => 'Rantai dingin menjaga suhu rendah secara konsisten mulai dari panen hingga distribusi untuk mempertahankan kesegaran produk.'],
                ],
            ],
            [
                'judul' => 'Pengeringan dan Penyimpanan Hasil Pertanian',
                'tujuan' => [
                    'Memahami prinsip pengeringan hasil pertanian untuk memperpanjang daya simpan',
                    'Menentukan kadar air bahan sebelum dan sesudah pengeringan',
                    'Memahami kondisi penyimpanan yang tepat untuk berbagai jenis hasil pertanian',
                ],
                'pendahuluan' => 'Pengeringan merupakan salah satu metode pascapanen yang menurunkan kadar air bahan hingga mencapai tingkat aman untuk disimpan, sehingga menghambat pertumbuhan mikroba dan reaksi kerusakan lainnya. Kombinasi pengeringan yang tepat dengan kondisi penyimpanan yang sesuai sangat menentukan kualitas dan daya simpan hasil pertanian dalam jangka panjang.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Prinsip Dasar Pengeringan', 'isi' => 'Pengeringan menurunkan kadar air bahan melalui penguapan menggunakan panas, baik secara alami (sinar matahari) maupun buatan (oven/alat pengering).'],
                    ['judul' => 'Kadar Air Kritis dan Kadar Air Kesetimbangan', 'isi' => 'Setiap bahan memiliki kadar air aman untuk penyimpanan (kadar air kritis) dan kadar air kesetimbangan dengan kelembaban lingkungan sekitarnya.'],
                    ['judul' => 'Metode Penentuan Kadar Air', 'isi' => 'Kadar air dapat ditentukan melalui metode oven (gravimetri) dengan membandingkan berat bahan sebelum dan sesudah pengeringan hingga berat konstan.'],
                    ['judul' => 'Kondisi Penyimpanan Ideal', 'isi' => 'Suhu, kelembaban relatif, dan sirkulasi udara ruang penyimpanan perlu disesuaikan dengan jenis komoditas untuk mencegah pertumbuhan mikroba dan serangan hama gudang.'],
                    ['judul' => 'Perubahan Mutu selama Penyimpanan', 'isi' => 'Selama penyimpanan, produk dapat mengalami perubahan mutu seperti penurunan kadar gizi, oksidasi lemak, maupun serangan hama/penyakit pascapanen jika kondisi penyimpanan tidak sesuai.'],
                ],
                'alat' => ['Oven pengering/alat pengering sederhana', 'Timbangan', 'Wadah sampel', 'Higrometer'],
                'bahan' => ['Gabah/jagung/kacang-kacangan'],
                'prosedur' => [
                    'Timbang berat awal sampel bahan sebelum dikeringkan.',
                    'Keringkan sampel menggunakan oven/alat pengering pada suhu tertentu selama waktu yang ditentukan.',
                    'Timbang kembali berat sampel setelah pengeringan dan hitung kadar air yang hilang.',
                    'Ulangi pengeringan hingga berat sampel konstan untuk memastikan kadar air akhir tercapai.',
                    'Simpan sebagian sampel kering pada wadah tertutup dan sebagian pada wadah terbuka, ukur kelembaban ruang penyimpanan menggunakan higrometer.',
                    'Amati perubahan kondisi bahan (warna, tekstur, ada tidaknya jamur) setelah disimpan beberapa hari pada kedua kondisi tersebut.',
                ],
                'kuis' => [
                    ['pertanyaan' => 'Tujuan utama pengeringan hasil pertanian adalah...', 'opsi' => ['Menambah berat bahan', 'Menurunkan kadar air untuk memperpanjang daya simpan', 'Mengubah warna bahan', 'Menambah kadar gula bahan'], 'jawaban' => 1, 'penjelasan' => 'Pengeringan bertujuan menurunkan kadar air bahan sehingga menghambat pertumbuhan mikroba dan memperpanjang daya simpan.'],
                    ['pertanyaan' => 'Kadar air kritis suatu bahan menunjukkan...', 'opsi' => ['Kadar air maksimum saat panen', 'Batas kadar air yang aman untuk penyimpanan', 'Kadar air ideal untuk konsumsi langsung', 'Kadar air pada saat bahan masih di lahan'], 'jawaban' => 1, 'penjelasan' => 'Kadar air kritis menunjukkan batas kadar air aman agar bahan dapat disimpan tanpa cepat rusak oleh mikroba.'],
                    ['pertanyaan' => 'Metode oven untuk menentukan kadar air didasarkan pada...', 'opsi' => ['Warna bahan sebelum dan sesudah dikeringkan', 'Selisih berat bahan sebelum dan sesudah pengeringan hingga berat konstan', 'Volume bahan sebelum dikeringkan', 'Waktu pengeringan saja'], 'jawaban' => 1, 'penjelasan' => 'Metode oven menghitung kadar air dari selisih berat bahan sebelum dan sesudah dikeringkan hingga mencapai berat konstan.'],
                    ['pertanyaan' => 'Higrometer pada praktikum ini digunakan untuk mengukur...', 'opsi' => ['Suhu bahan', 'Kelembaban ruang penyimpanan', 'Berat bahan', 'Kadar air bahan secara langsung'], 'jawaban' => 1, 'penjelasan' => 'Higrometer digunakan untuk mengukur kelembaban relatif pada ruang penyimpanan.'],
                    ['pertanyaan' => 'Penyimpanan pada wadah terbuka dengan kelembaban tinggi berisiko menyebabkan...', 'opsi' => ['Bahan menjadi lebih kering', 'Pertumbuhan jamur/mikroba pada bahan', 'Bahan menjadi lebih ringan', 'Tidak ada risiko khusus'], 'jawaban' => 1, 'penjelasan' => 'Kelembaban tinggi pada ruang penyimpanan terbuka dapat menyebabkan bahan menyerap uap air kembali dan memicu pertumbuhan jamur/mikroba.'],
                ],
            ],
        ];
    }

    protected function perbengkelan(): array
    {
        return [
            [
                'judul' => 'Pengenalan Alat dan Teknik Dasar Bengkel',
                'tujuan' => [
                    'Mengetahui nama dan fungsi peralatan dasar bengkel pertanian',
                    'Memahami teknik dasar penggunaan peralatan tangan (hand tools) di bengkel',
                    'Menerapkan prinsip keselamatan kerja di bengkel',
                ],
                'pendahuluan' => 'Bengkel pertanian menjadi tempat perawatan dan perbaikan alat mesin pertanian, sehingga penguasaan peralatan dasar bengkel dan teknik kerja yang aman menjadi bekal penting sebelum melakukan perawatan/perbaikan alat mesin pertanian yang lebih kompleks.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Peralatan Tangan (Hand Tools)', 'isi' => 'Kunci pas, obeng, tang, dan palu merupakan peralatan tangan dasar yang digunakan untuk membongkar, memasang, dan mengencangkan komponen mesin.'],
                    ['judul' => 'Peralatan Ukur Bengkel', 'isi' => 'Jangka sorong dan mistar baja digunakan untuk mengukur dimensi komponen mesin secara presisi sebelum perbaikan atau penggantian suku cadang.'],
                    ['judul' => 'Peralatan Bertenaga (Power Tools)', 'isi' => 'Bor listrik dan gerinda digunakan untuk mempercepat pekerjaan seperti pengeboran dan pemotongan komponen logam.'],
                    ['judul' => 'Teknik Dasar Pengelasan', 'isi' => 'Pengelasan menyambungkan dua logam menggunakan panas tinggi hingga meleleh dan menyatu, umum digunakan dalam perbaikan rangka alat mesin pertanian.'],
                    ['judul' => 'Keselamatan Kerja di Bengkel (K3)', 'isi' => 'Penggunaan alat pelindung diri seperti sarung tangan dan kacamata kerja penting untuk mencegah kecelakaan kerja di bengkel.'],
                ],
                'alat' => ['Kunci pas set', 'Obeng plus dan minus', 'Tang kombinasi', 'Palu', 'Jangka sorong', 'Gerinda tangan'],
                'bahan' => ['Baut dan mur berbagai ukuran', 'Plat besi bekas'],
                'prosedur' => [
                    'Kenali dan sebutkan nama serta fungsi masing-masing peralatan tangan yang tersedia di bengkel.',
                    'Praktikkan cara memegang dan menggunakan kunci pas untuk mengencangkan dan melepas baut/mur dengan ukuran berbeda.',
                    'Praktikkan penggunaan obeng plus dan minus pada sekrup dengan jenis kepala yang sesuai.',
                    'Ukur dimensi plat besi menggunakan jangka sorong untuk melatih ketelitian pengukuran di bengkel.',
                    'Praktikkan teknik dasar menggerinda plat besi bekas dengan memperhatikan prosedur keselamatan kerja (penggunaan sarung tangan dan kacamata).',
                    'Rapikan kembali seluruh peralatan pada tempatnya setelah selesai digunakan.',
                ],
                'kuis' => [
                    ['pertanyaan' => 'Alat apa yang digunakan untuk mengencangkan atau melepas baut dan mur?', 'opsi' => ['Kunci pas', 'Palu', 'Gerinda', 'Jangka sorong'], 'jawaban' => 0, 'penjelasan' => 'Kunci pas digunakan untuk mengencangkan atau melepas baut dan mur sesuai ukurannya.'],
                    ['pertanyaan' => 'Jangka sorong di bengkel digunakan untuk...', 'opsi' => ['Mengukur dimensi komponen secara presisi', 'Memotong logam', 'Mengelas komponen', 'Mengencangkan baut'], 'jawaban' => 0, 'penjelasan' => 'Jangka sorong digunakan untuk mengukur dimensi komponen mesin secara presisi.'],
                    ['pertanyaan' => 'Pengelasan menyambungkan dua logam dengan cara...', 'opsi' => ['Merekatkan dengan lem', 'Memanaskan hingga meleleh dan menyatu', 'Mengikat dengan tali', 'Menempelkan dengan magnet'], 'jawaban' => 1, 'penjelasan' => 'Pengelasan menyambungkan dua logam dengan memanaskannya hingga meleleh dan menyatu.'],
                    ['pertanyaan' => 'Apa fungsi utama alat pelindung diri seperti sarung tangan dan kacamata kerja di bengkel?', 'opsi' => ['Mencegah kecelakaan kerja', 'Mempercepat pekerjaan', 'Menghemat bahan bakar', 'Menambah kekuatan alat'], 'jawaban' => 0, 'penjelasan' => 'Alat pelindung diri berfungsi mencegah kecelakaan dan cedera kerja di bengkel.'],
                    ['pertanyaan' => 'Gerinda tangan pada bengkel umumnya digunakan untuk...', 'opsi' => ['Mengukur suhu logam', 'Memotong/menghaluskan permukaan logam', 'Mengecat komponen', 'Mengganti oli mesin'], 'jawaban' => 1, 'penjelasan' => 'Gerinda tangan digunakan untuk memotong maupun menghaluskan permukaan logam.'],
                ],
            ],
            [
                'judul' => 'Perawatan dan Perbaikan Alat Mesin Pertanian Sederhana',
                'tujuan' => [
                    'Memahami jadwal dan jenis perawatan berkala pada alat mesin pertanian',
                    'Mempraktikkan pembongkaran dan pemasangan komponen sederhana alat mesin pertanian',
                    'Mengidentifikasi kerusakan umum dan cara penanganannya pada alat mesin pertanian',
                ],
                'pendahuluan' => 'Perawatan berkala pada alat mesin pertanian penting untuk menjaga performa dan memperpanjang umur pakai alat, sekaligus mencegah kerusakan yang lebih besar. Praktikum ini melatih keterampilan dasar membongkar, memeriksa, dan memasang kembali komponen sederhana pada alat mesin pertanian, seperti mengganti oli atau membersihkan filter udara.',
                'tinjauan_pustaka' => [
                    ['judul' => 'Jenis Perawatan Alat Mesin Pertanian', 'isi' => 'Perawatan dibedakan menjadi perawatan preventif (rutin/terjadwal) dan perawatan korektif (perbaikan setelah kerusakan terjadi).'],
                    ['judul' => 'Pemeriksaan Rutin Harian', 'isi' => 'Pemeriksaan level oli, air radiator, dan tekanan ban perlu dilakukan sebelum alat mesin pertanian dioperasikan setiap harinya.'],
                    ['judul' => 'Penggantian Oli dan Filter', 'isi' => 'Oli mesin dan filter udara/oli perlu diganti secara berkala sesuai jam operasional untuk menjaga performa mesin.'],
                    ['judul' => 'Identifikasi Kerusakan Umum', 'isi' => 'Kerusakan umum pada alat mesin pertanian sederhana meliputi kebocoran oli, mesin sulit hidup, dan komponen aus akibat gesekan.'],
                    ['judul' => 'Prosedur Pembongkaran dan Pemasangan Komponen', 'isi' => 'Pembongkaran komponen perlu dilakukan secara berurutan dan hati-hati, dengan mencatat posisi/urutan pemasangan agar mudah dipasang kembali dengan benar.'],
                ],
                'alat' => ['Kunci pas set', 'Obeng', 'Corong oli', 'Wadah penampung oli bekas', 'Kompresor angin/kuas'],
                'bahan' => ['Oli mesin baru', 'Filter udara pengganti', 'Kain lap'],
                'prosedur' => [
                    'Periksa level oli, kondisi filter udara, dan komponen luar mesin sebelum melakukan pembongkaran.',
                    'Buka baut penutup bak oli menggunakan kunci pas, tampung oli bekas yang keluar pada wadah penampung.',
                    'Bersihkan area sekitar bak oli menggunakan kain lap sebelum memasang kembali penutup bak oli.',
                    'Isi oli mesin baru melalui corong sesuai takaran yang dianjurkan.',
                    'Lepas dan bersihkan filter udara menggunakan kompresor angin/kuas, ganti dengan filter baru jika sudah terlalu kotor.',
                    'Pasang kembali seluruh komponen sesuai urutan pembongkaran, lalu uji jalankan mesin untuk memastikan tidak ada kebocoran atau kerusakan.',
                ],
                'kuis' => [
                    ['pertanyaan' => 'Perawatan preventif pada alat mesin pertanian dilakukan...', 'opsi' => ['Setelah kerusakan terjadi', 'Secara rutin/terjadwal sebelum kerusakan terjadi', 'Hanya sekali seumur alat', 'Tidak perlu dijadwalkan'], 'jawaban' => 1, 'penjelasan' => 'Perawatan preventif dilakukan secara rutin/terjadwal untuk mencegah kerusakan sebelum terjadi.'],
                    ['pertanyaan' => 'Apa saja yang perlu diperiksa sebelum mengoperasikan alat mesin pertanian setiap harinya?', 'opsi' => ['Level oli, air radiator, dan tekanan ban', 'Warna cat alat', 'Jumlah operator', 'Harga bahan bakar'], 'jawaban' => 0, 'penjelasan' => 'Pemeriksaan rutin harian mencakup level oli, air radiator, dan tekanan ban sebelum alat dioperasikan.'],
                    ['pertanyaan' => 'Mengapa filter udara perlu diganti secara berkala?', 'opsi' => ['Agar warnanya tetap bagus', 'Karena filter yang kotor menghambat aliran udara ke mesin sehingga menurunkan performa', 'Untuk menambah berat mesin', 'Tidak ada alasan khusus'], 'jawaban' => 1, 'penjelasan' => 'Filter udara yang kotor dapat menghambat aliran udara ke mesin sehingga menurunkan performa mesin, oleh karena itu perlu diganti secara berkala.'],
                    ['pertanyaan' => 'Saat membongkar komponen mesin, hal penting yang perlu dicatat adalah...', 'opsi' => ['Warna komponen', 'Posisi/urutan pemasangan komponen', 'Harga komponen', 'Berat komponen'], 'jawaban' => 1, 'penjelasan' => 'Posisi dan urutan pemasangan komponen perlu dicatat agar mudah dipasang kembali dengan benar.'],
                    ['pertanyaan' => 'Apa yang harus dilakukan setelah memasang kembali komponen mesin yang telah diperbaiki?', 'opsi' => ['Langsung dijual', 'Uji jalankan mesin untuk memastikan tidak ada kebocoran/kerusakan', 'Dibiarkan tanpa diuji', 'Dibongkar kembali'], 'jawaban' => 1, 'penjelasan' => 'Setelah pemasangan kembali, mesin perlu diuji jalankan untuk memastikan tidak ada kebocoran atau kerusakan yang tersisa.'],
                ],
            ],
        ];
    }
}
