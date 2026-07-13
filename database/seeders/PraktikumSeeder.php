<?php

namespace Database\Seeders;

use App\Models\Praktikum;
use App\Models\Prodi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PraktikumSeeder extends Seeder
{
    public function run(): void
    {
        $byProdi = [
            'THP' => [
                ['kode' => 'THP-P01', 'judul' => 'Uji Kadar Air dan Kadar Abu', 'tingkat' => 'Dasar', 'durasi' => '60 menit',
                    'tujuan' => 'Menentukan kadar air dan kadar abu bahan pangan menggunakan metode gravimetri secara virtual.',
                    'alat' => ['Oven pengering virtual', 'Neraca analitik virtual', 'Cawan porselen', 'Desikator virtual'],
                    'bahan' => ['Sampel bahan pangan (padatan/cairan)'],
                    'langkah' => ['Timbang cawan kosong dan catat sebagai berat awal.', 'Masukkan sampel ke cawan, timbang berat sampel basah.', 'Simulasikan pengeringan pada suhu 105°C hingga berat konstan.', 'Dinginkan dalam desikator virtual, lalu timbang berat akhir.', 'Hitung kadar air dan catat hasil pada lembar kerja.'],
                    'kuis' => ['pertanyaan' => 'Sampel bahan pangan seberat 10 g dikeringkan hingga berat konstan menjadi 8 g. Berapa kadar air bahan tersebut?', 'opsi' => ['10%', '20%', '80%', '8%'], 'jawaban' => 1, 'penjelasan' => 'Kadar air = (berat awal − berat akhir) ÷ berat awal × 100% = (10−8)/10 × 100% = 20%.']],
                ['kode' => 'THP-P02', 'judul' => 'Isolasi Mikroba pada Sampel Pangan', 'tingkat' => 'Dasar', 'durasi' => '75 menit',
                    'tujuan' => 'Melakukan isolasi dan identifikasi morfologi mikroba sederhana dari sampel pangan.',
                    'alat' => ['Cawan petri', 'Jarum ose virtual', 'Inkubator virtual'],
                    'bahan' => ['Media agar virtual', 'Sampel pangan yang akan diuji'],
                    'langkah' => ['Siapkan media agar steril di cawan petri.', 'Inokulasikan sampel pangan ke permukaan media.', 'Inkubasi virtual pada suhu ruang selama 24–48 jam.', 'Amati bentuk, warna, dan tepi koloni yang tumbuh.', 'Catat hasil identifikasi morfologi koloni.'],
                    'kuis' => ['pertanyaan' => 'Media agar yang umum digunakan untuk menumbuhkan bakteri secara umum adalah?', 'opsi' => ['Nutrient Agar', 'Kertas saring', 'Larutan garam pekat', 'Minyak goreng'], 'jawaban' => 0, 'penjelasan' => 'Nutrient Agar adalah media umum (non-selektif) yang mendukung pertumbuhan berbagai jenis bakteri.']],
                ['kode' => 'THP-P03', 'judul' => 'Praktikum Fermentasi Tempe', 'tingkat' => 'Menengah', 'durasi' => '90 menit',
                    'tujuan' => 'Mensimulasikan tahapan proses fermentasi kedelai menjadi tempe dan mengevaluasi hasilnya.',
                    'alat' => ['Inkubator virtual', 'Kantong pembungkus berlubang'],
                    'bahan' => ['Kedelai virtual', 'Ragi tempe (Rhizopus sp.)'],
                    'langkah' => ['Simulasikan perebusan dan pengupasan kulit kedelai.', 'Tiriskan dan dinginkan kedelai hingga suhu ruang.', 'Taburkan ragi tempe secara merata pada kedelai.', 'Bungkus dan inkubasi pada suhu ruang selama 36–48 jam.', 'Evaluasi hasil: warna, tekstur, dan pertumbuhan miselium.'],
                    'kuis' => ['pertanyaan' => 'Suhu inkubasi optimal untuk fermentasi tempe umumnya berkisar pada?', 'opsi' => ['4–10°C', '30–37°C', '60–70°C', '90–100°C'], 'jawaban' => 1, 'penjelasan' => 'Kapang Rhizopus sp. tumbuh optimal pada suhu ruang hangat, sekitar 30–37°C.']],
                ['kode' => 'THP-P04', 'judul' => 'Uji Sensori Produk Pangan', 'tingkat' => 'Menengah', 'durasi' => '60 menit',
                    'tujuan' => 'Melakukan uji organoleptik (hedonik) terhadap produk pangan menggunakan panelis virtual.',
                    'alat' => ['Score sheet virtual', 'Skala hedonik 1–7'],
                    'bahan' => ['Sampel produk pangan'],
                    'langkah' => ['Siapkan sampel produk dengan kode acak.', 'Bagikan score sheet kepada panelis virtual.', 'Lakukan uji hedonik terhadap warna, aroma, rasa, dan tekstur.', 'Kumpulkan dan rekap skor dari seluruh panelis.', 'Olah data dan simpulkan tingkat penerimaan produk.'],
                    'kuis' => ['pertanyaan' => 'Skala hedonik pada uji sensori digunakan untuk mengukur apa?', 'opsi' => ['Tingkat kesukaan panelis', 'Berat sampel', 'Kadar air produk', 'Warna kemasan'], 'jawaban' => 0, 'penjelasan' => 'Skala hedonik menilai tingkat kesukaan atau preferensi panelis terhadap suatu produk.']],
                ['kode' => 'THP-P05', 'judul' => 'Simulasi Penentuan Umur Simpan', 'tingkat' => 'Lanjut', 'durasi' => '60 menit',
                    'tujuan' => 'Menduga umur simpan produk pangan menggunakan pendekatan Arrhenius sederhana.',
                    'alat' => ['Kalkulator virtual'],
                    'bahan' => ['Data laju kerusakan produk pada beberapa suhu penyimpanan'],
                    'langkah' => ['Kumpulkan data laju kerusakan pada beberapa suhu penyimpanan.', 'Hitung konstanta laju reaksi (k) pada tiap suhu.', 'Plot hubungan suhu dan laju kerusakan.', 'Proyeksikan umur simpan pada suhu penyimpanan target.', 'Bandingkan hasil proyeksi antar skenario penyimpanan.'],
                    'kuis' => ['pertanyaan' => 'Metode Arrhenius pada pendugaan umur simpan mengaitkan laju kerusakan produk dengan faktor apa?', 'opsi' => ['Suhu penyimpanan', 'Warna kemasan', 'Harga produk', 'Berat produk'], 'jawaban' => 0, 'penjelasan' => 'Persamaan Arrhenius menghubungkan laju reaksi kerusakan produk dengan suhu penyimpanan.']],
            ],
            'TPB' => [
                ['kode' => 'TPB-P01', 'judul' => 'Kalibrasi Sensor Kelembaban Tanah', 'tingkat' => 'Dasar', 'durasi' => '60 menit',
                    'tujuan' => 'Mengkalibrasi pembacaan sensor soil moisture virtual terhadap sampel tanah dengan kadar air diketahui.',
                    'alat' => ['Sensor kelembaban tanah virtual', 'Multimeter virtual'],
                    'bahan' => ['Sampel tanah referensi dengan kadar air diketahui'],
                    'langkah' => ['Siapkan sampel tanah dengan kadar air yang telah diketahui.', 'Hubungkan sensor virtual ke sampel tanah.', 'Catat nilai pembacaan sensor pada tiap sampel.', 'Bandingkan pembacaan sensor dengan nilai kadar air aktual.', 'Susun kurva kalibrasi dari data yang diperoleh.'],
                    'kuis' => ['pertanyaan' => 'Tujuan utama kalibrasi sensor kelembaban tanah adalah?', 'opsi' => ['Memastikan pembacaan sensor sesuai nilai aktual', 'Mengganti baterai sensor', 'Mengecat sensor', 'Mengurangi jumlah sensor'], 'jawaban' => 0, 'penjelasan' => 'Kalibrasi memastikan hasil pembacaan sensor mendekati kondisi kadar air yang sebenarnya.']],
                ['kode' => 'TPB-P02', 'judul' => 'Simulasi Sistem Irigasi Tetes', 'tingkat' => 'Dasar', 'durasi' => '75 menit',
                    'tujuan' => 'Merancang tata letak sistem irigasi tetes sederhana pada lahan virtual.',
                    'alat' => ['Layout lahan virtual', 'Emitter irigasi tetes', 'Pipa distribusi virtual'],
                    'bahan' => ['Data kebutuhan air tanaman pada lahan simulasi'],
                    'langkah' => ['Tentukan kebutuhan air tanaman pada lahan simulasi.', 'Atur jarak dan jumlah emitter sepanjang jalur tanam.', 'Hitung debit total kebutuhan sistem irigasi.', 'Simulasikan distribusi air ke seluruh area lahan.', 'Evaluasi keseragaman distribusi air pada hasil simulasi.'],
                    'kuis' => ['pertanyaan' => 'Keunggulan utama sistem irigasi tetes dibanding irigasi permukaan adalah?', 'opsi' => ['Penggunaan air lebih efisien', 'Biaya instalasi paling murah', 'Tidak memerlukan sumber air', 'Cocok untuk semua topografi tanpa pompa'], 'jawaban' => 0, 'penjelasan' => 'Irigasi tetes menyalurkan air langsung ke zona akar tanaman sehingga lebih hemat air.']],
                ['kode' => 'TPB-P03', 'judul' => 'Pengoperasian Traktor dan Implemen', 'tingkat' => 'Menengah', 'durasi' => '90 menit',
                    'tujuan' => 'Memahami prosedur pengoperasian traktor dan implemen bajak secara aman melalui simulator virtual.',
                    'alat' => ['Simulator traktor virtual', 'Implemen bajak singkal'],
                    'bahan' => ['Lintasan lahan simulasi untuk uji pengolahan tanah'],
                    'langkah' => ['Lakukan pemeriksaan pra-operasi (pre-check) pada simulator.', 'Hidupkan mesin dan periksa indikator dasar.', 'Pasang dan atur kedalaman implemen bajak.', 'Jalankan simulasi pengolahan tanah pada lintasan lahan.', 'Matikan mesin dan catat hal-hal yang perlu perawatan.'],
                    'kuis' => ['pertanyaan' => 'Langkah pertama sebelum mengoperasikan traktor adalah?', 'opsi' => ['Pemeriksaan pra-operasi (pre-check)', 'Langsung menjalankan implemen', 'Mengisi bahan bakar tanpa pengecekan', 'Menaikkan RPM maksimum'], 'jawaban' => 0, 'penjelasan' => 'Pemeriksaan pra-operasi penting untuk memastikan keselamatan dan kesiapan alat sebelum digunakan.']],
                ['kode' => 'TPB-P04', 'judul' => 'Perancangan Greenhouse Otomatis', 'tingkat' => 'Menengah', 'durasi' => '90 menit',
                    'tujuan' => 'Merancang sistem kontrol suhu dan kelembaban greenhouse berbasis sensor dan aktuator virtual.',
                    'alat' => ['Mikrokontroler virtual', 'Sensor suhu dan kelembaban', 'Kipas dan mist otomatis'],
                    'bahan' => ['Data setpoint suhu dan kelembaban ideal greenhouse'],
                    'langkah' => ['Tentukan setpoint suhu dan kelembaban ideal.', 'Programkan logika kontrol sederhana (if-then) pada mikrokontroler virtual.', 'Uji simulasi respons sistem terhadap perubahan suhu.', 'Analisis grafik suhu terhadap waktu hasil simulasi.', 'Optimasi parameter kontrol agar respons lebih stabil.'],
                    'kuis' => ['pertanyaan' => 'Pada sistem kontrol greenhouse otomatis, sensor suhu berfungsi untuk?', 'opsi' => ['Memberi sinyal ke aktuator agar menyesuaikan kondisi', 'Mengukur berat tanaman', 'Mengganti media tanam', 'Menentukan harga jual produk'], 'jawaban' => 0, 'penjelasan' => 'Sensor suhu mengirim data ke sistem kontrol untuk mengaktifkan aktuator (kipas/mist) sesuai setpoint.']],
                ['kode' => 'TPB-P05', 'judul' => 'Analisis Neraca Air Lahan', 'tingkat' => 'Lanjut', 'durasi' => '60 menit',
                    'tujuan' => 'Menghitung neraca air sederhana untuk menentukan kebutuhan irigasi pada lahan pertanian.',
                    'alat' => ['Kalkulator virtual'],
                    'bahan' => ['Data curah hujan virtual', 'Data evapotranspirasi lahan'],
                    'langkah' => ['Kumpulkan data curah hujan dan evapotranspirasi lahan.', 'Hitung total kebutuhan air tanaman pada periode tertentu.', 'Hitung surplus atau defisit air dari selisih data.', 'Susun rekomendasi jadwal irigasi berdasarkan hasil analisis.'],
                    'kuis' => ['pertanyaan' => 'Jika curah hujan lebih kecil dari evapotranspirasi, kondisi lahan mengalami?', 'opsi' => ['Defisit air', 'Surplus air', 'Keseimbangan sempurna', 'Banjir'], 'jawaban' => 0, 'penjelasan' => 'Curah hujan yang lebih kecil dari evapotranspirasi berarti kebutuhan air tanaman belum terpenuhi (defisit).']],
            ],
            'TIP' => [
                ['kode' => 'TIP-P01', 'judul' => 'Studi Waktu dan Gerak', 'tingkat' => 'Dasar', 'durasi' => '60 menit',
                    'tujuan' => 'Mengukur waktu baku suatu proses produksi menggunakan simulasi stopwatch virtual.',
                    'alat' => ['Stopwatch virtual', 'Lembar pengamatan kerja'],
                    'bahan' => ['Data elemen kerja pada proses produksi simulasi'],
                    'langkah' => ['Amati dan uraikan elemen-elemen kerja pada proses simulasi.', 'Catat waktu siklus untuk setiap elemen kerja.', 'Tentukan rating performance operator.', 'Hitung waktu normal dan waktu baku.', 'Susun laporan hasil studi waktu.'],
                    'kuis' => ['pertanyaan' => 'Waktu baku dalam studi waktu memperhitungkan faktor apa selain waktu siklus?', 'opsi' => ['Rating performance dan kelonggaran (allowance)', 'Harga bahan baku', 'Jumlah karyawan', 'Luas pabrik'], 'jawaban' => 0, 'penjelasan' => 'Waktu baku dihitung dari waktu siklus dikalikan rating performance, ditambah kelonggaran (allowance).']],
                ['kode' => 'TIP-P02', 'judul' => 'Simulasi Tata Letak Pabrik', 'tingkat' => 'Dasar', 'durasi' => '75 menit',
                    'tujuan' => 'Merancang tata letak lantai produksi agroindustri yang efisien pada denah virtual.',
                    'alat' => ['Denah pabrik virtual', 'Blok fasilitas produksi'],
                    'bahan' => ['Data urutan proses produksi'],
                    'langkah' => ['Identifikasi urutan aliran proses produksi.', 'Susun blok fasilitas sesuai urutan proses.', 'Hitung total jarak perpindahan material (material handling).', 'Bandingkan dua alternatif tata letak.', 'Pilih tata letak dengan efisiensi terbaik.'],
                    'kuis' => ['pertanyaan' => 'Tujuan utama perancangan tata letak pabrik yang baik adalah?', 'opsi' => ['Meminimalkan jarak dan biaya material handling', 'Memaksimalkan luas kantor', 'Mengurangi jumlah mesin', 'Menambah jumlah gudang'], 'jawaban' => 0, 'penjelasan' => 'Tata letak yang baik meminimalkan jarak dan biaya perpindahan material antar proses.']],
                ['kode' => 'TIP-P03', 'judul' => 'Pengendalian Kualitas Statistik', 'tingkat' => 'Menengah', 'durasi' => '90 menit',
                    'tujuan' => 'Membuat peta kendali sederhana untuk memantau kestabilan proses produksi.',
                    'alat' => ['Kalkulator statistik virtual'],
                    'bahan' => ['Data sampel produk dari beberapa subgrup produksi'],
                    'langkah' => ['Kumpulkan data sampel produk dari beberapa subgrup.', 'Hitung rata-rata dan rentang (range) tiap subgrup.', 'Buat peta kendali X-bar dan R.', 'Interpretasikan titik data terhadap batas kendali.', 'Identifikasi kemungkinan penyebab variasi proses.'],
                    'kuis' => ['pertanyaan' => 'Titik data yang berada di luar batas kendali pada peta kendali menunjukkan?', 'opsi' => ['Kemungkinan adanya variasi penyebab khusus', 'Proses selalu berjalan sempurna', 'Data harus dihapus', 'Tidak ada arti khusus'], 'jawaban' => 0, 'penjelasan' => 'Titik di luar batas kendali mengindikasikan variasi penyebab khusus yang perlu diselidiki lebih lanjut.']],
                ['kode' => 'TIP-P04', 'judul' => 'Analisis Kelayakan Usaha Agroindustri', 'tingkat' => 'Menengah', 'durasi' => '75 menit',
                    'tujuan' => 'Menghitung kelayakan investasi usaha agroindustri sederhana menggunakan indikator NPV.',
                    'alat' => ['Kalkulator NPV virtual'],
                    'bahan' => ['Data biaya investasi dan proyeksi pendapatan usaha'],
                    'langkah' => ['Input data biaya investasi awal usaha.', 'Proyeksikan arus kas masuk dan keluar selama beberapa periode.', 'Hitung Net Present Value (NPV) dan payback period.', 'Simpulkan kelayakan usaha berdasarkan hasil perhitungan.'],
                    'kuis' => ['pertanyaan' => 'Jika nilai NPV suatu usaha positif, maka usaha tersebut dikategorikan?', 'opsi' => ['Layak dijalankan', 'Merugi pasti', 'Tidak dapat dihitung', 'Selalu berisiko tinggi'], 'jawaban' => 0, 'penjelasan' => 'NPV positif menunjukkan proyeksi arus kas menutup investasi dan menghasilkan keuntungan, sehingga usaha dikategorikan layak.']],
                ['kode' => 'TIP-P05', 'judul' => 'Simulasi Pengolahan Limbah Agroindustri', 'tingkat' => 'Lanjut', 'durasi' => '75 menit',
                    'tujuan' => 'Merancang skema pengolahan limbah cair agroindustri secara sederhana.',
                    'alat' => ['Diagram alir proses (template)'],
                    'bahan' => ['Data karakteristik limbah cair agroindustri'],
                    'langkah' => ['Identifikasi jenis dan karakteristik limbah yang dihasilkan.', 'Pilih metode pengolahan yang sesuai (fisik/biologis).', 'Susun diagram alir proses pengolahan limbah.', 'Estimasikan efisiensi pengolahan berdasarkan simulasi.', 'Evaluasi potensi dampak lingkungan dari hasil olahan.'],
                    'kuis' => ['pertanyaan' => 'Pengolahan limbah cair agroindustri secara biologis umumnya memanfaatkan?', 'opsi' => ['Mikroorganisme pengurai bahan organik', 'Pemanasan bertekanan tinggi', 'Pewarnaan kimia', 'Pembekuan limbah'], 'jawaban' => 0, 'penjelasan' => 'Pengolahan biologis memanfaatkan mikroorganisme untuk menguraikan bahan organik yang terkandung dalam limbah.']],
            ],
        ];

        foreach ($byProdi as $kode => $items) {
            $prodi = Prodi::where('kode', $kode)->firstOrFail();

            foreach ($items as $item) {
                Praktikum::updateOrCreate(
                    ['slug' => Str::slug($item['kode'].'-'.$item['judul'])],
                    [
                        'prodi_id' => $prodi->id,
                        'kode' => $item['kode'],
                        'judul' => $item['judul'],
                        'tingkat' => $item['tingkat'],
                        'durasi' => $item['durasi'],
                        'tujuan' => $item['tujuan'],
                        'alat' => $item['alat'],
                        'bahan' => $item['bahan'],
                        'langkah' => $item['langkah'],
                        'kuis' => $item['kuis'],
                    ]
                );
            }
        }
    }
}
