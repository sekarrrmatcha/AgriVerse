<?php

namespace Database\Seeders;

use App\Models\Matakuliah;
use App\Models\Materi;
use App\Models\Prodi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MateriSeeder extends Seeder
{
    public function run(): void
    {
        $byProdi = [
            'THP' => [
                'THP101' => [
                    'judul' => 'Kimia dan Komposisi Bahan Pangan',
                    'capaian' => [
                        'Memahami komponen kimia utama bahan pangan: air, karbohidrat, protein, dan lemak.',
                        'Mampu menghubungkan komposisi kimia dengan mutu produk.',
                    ],
                    'pendahuluan' => 'Bahan pangan tersusun dari komponen kimia utama berupa air, karbohidrat, protein, dan lemak yang saling memengaruhi struktur, tekstur, dan daya simpan produk. Pemahaman komposisi kimia ini menjadi dasar penting dalam menentukan mutu bahan baku serta merancang proses pengolahan pangan yang tepat.',
                    'tinjauan_pustaka' => [
                        ['judul' => 'Air dan Aktivitas Air (aw)', 'isi' => 'Kandungan air bebas dan air terikat pada bahan pangan memengaruhi laju kerusakan mikrobiologis dan kimiawi; nilai aktivitas air (aw) digunakan untuk memprediksi stabilitas produk selama penyimpanan.'],
                        ['judul' => 'Karbohidrat pada Bahan Pangan', 'isi' => 'Karbohidrat berperan sebagai sumber energi utama dan penentu tekstur produk, mulai dari pati, gula sederhana, hingga serat pangan yang memengaruhi sifat gelatinisasi dan kekentalan.'],
                        ['judul' => 'Protein dan Sifat Fungsionalnya', 'isi' => 'Protein memberikan sifat fungsional seperti kemampuan membentuk gel, buih, dan emulsi yang penting dalam pengolahan produk seperti roti, sosis, dan produk susu.'],
                        ['judul' => 'Lemak dan Oksidasi Lipida', 'isi' => 'Lemak memengaruhi cita rasa dan tekstur produk, namun rentan mengalami oksidasi yang menyebabkan ketengikan sehingga perlu dikendalikan melalui penyimpanan dan pengemasan yang tepat.'],
                        ['judul' => 'Analisis Proksimat dan Standar Mutu SNI', 'isi' => 'Analisis proksimat (kadar air, abu, protein, lemak, karbohidrat) digunakan untuk mengevaluasi kesesuaian komposisi bahan pangan terhadap standar mutu SNI yang berlaku.'],
                    ],
                ],
                'THP102' => [
                    'judul' => 'Mikrobiologi Pangan Dasar',
                    'capaian' => [
                        'Mengenal kelompok mikroorganisme yang berperan pada pangan.',
                        'Memahami faktor yang memengaruhi pertumbuhan mikroba.',
                    ],
                    'pendahuluan' => 'Mikroorganisme seperti bakteri, kapang, dan khamir dapat berperan menguntungkan (fermentasi) maupun merugikan (pembusukan) pada bahan pangan. Pemahaman tentang jenis dan faktor pertumbuhan mikroba menjadi dasar dalam upaya menjaga keamanan dan mutu produk pangan.',
                    'tinjauan_pustaka' => [
                        ['judul' => 'Kelompok Mikroorganisme Pangan', 'isi' => 'Bakteri, kapang, dan khamir memiliki karakteristik pertumbuhan dan dampak yang berbeda terhadap pangan, mulai dari fermentasi menguntungkan hingga penyebab kerusakan.'],
                        ['judul' => 'Faktor Pertumbuhan Mikroba', 'isi' => 'Suhu, pH, aktivitas air, dan ketersediaan oksigen merupakan faktor intrinsik dan ekstrinsik yang menentukan laju pertumbuhan mikroorganisme pada bahan pangan.'],
                        ['judul' => 'Kontaminasi dan Pembusukan Pangan', 'isi' => 'Kontaminasi dapat terjadi pada berbagai tahap rantai pangan dan menyebabkan pembusukan yang ditandai dengan perubahan bau, warna, dan tekstur produk.'],
                        ['judul' => 'Prinsip Pengendalian Mikroba', 'isi' => 'Pengendalian mikroba dilakukan melalui kombinasi metode fisik (pemanasan, pendinginan) dan kimiawi (pengawet) untuk memperpanjang umur simpan produk.'],
                    ],
                ],
                'THP201' => [
                    'judul' => 'Teknologi Pengolahan Pangan',
                    'capaian' => [
                        'Memahami prinsip pengolahan pangan termal dan non-termal.',
                        'Mampu memilih metode pengolahan sesuai jenis bahan.',
                    ],
                    'pendahuluan' => 'Pengolahan pangan bertujuan meningkatkan daya simpan, keamanan, dan nilai tambah produk melalui berbagai metode termal maupun non-termal. Pemilihan metode pengolahan yang tepat perlu disesuaikan dengan karakteristik bahan baku dan produk akhir yang diinginkan.',
                    'tinjauan_pustaka' => [
                        ['judul' => 'Pengeringan dan Pemanasan', 'isi' => 'Pengeringan menurunkan kadar air bahan sehingga menghambat pertumbuhan mikroba, sedangkan pemanasan digunakan untuk inaktivasi enzim dan mikroorganisme patogen.'],
                        ['judul' => 'Pasteurisasi dan Sterilisasi', 'isi' => 'Pasteurisasi mengurangi jumlah mikroba patogen pada suhu di bawah titik didih, sementara sterilisasi bertujuan menghilangkan seluruh mikroorganisme termasuk sporanya.'],
                        ['judul' => 'Fermentasi sebagai Metode Pengolahan', 'isi' => 'Fermentasi memanfaatkan aktivitas mikroorganisme untuk mengubah komposisi bahan baku, meningkatkan daya simpan, serta menghasilkan cita rasa khas.'],
                        ['judul' => 'Prinsip Dasar Pengemasan', 'isi' => 'Pengemasan berfungsi melindungi produk dari kontaminasi, kerusakan fisik, dan perubahan lingkungan yang dapat menurunkan mutu selama distribusi dan penyimpanan.'],
                    ],
                ],
                'THP202' => [
                    'judul' => 'Fermentasi dan Bioproses Pangan',
                    'capaian' => [
                        'Memahami mekanisme fermentasi pangan tradisional dan industrial.',
                        'Mengenal peran starter culture dalam bioproses.',
                    ],
                    'pendahuluan' => 'Fermentasi merupakan salah satu teknik pengolahan pangan tertua yang memanfaatkan aktivitas mikroorganisme untuk mengubah bahan baku menjadi produk dengan karakteristik baru. Pemahaman mekanisme fermentasi dan peran starter culture penting untuk mengendalikan konsistensi dan mutu produk fermentasi.',
                    'tinjauan_pustaka' => [
                        ['judul' => 'Fermentasi Tempe dan Tape', 'isi' => 'Fermentasi tempe memanfaatkan kapang Rhizopus sp. untuk mengikat biji kedelai menjadi massa kompak, sedangkan fermentasi tape memanfaatkan ragi untuk menghasilkan rasa manis dan sedikit alkohol.'],
                        ['judul' => 'Fermentasi Susu (Yoghurt)', 'isi' => 'Fermentasi susu menggunakan bakteri asam laktat yang mengubah laktosa menjadi asam laktat, menghasilkan tekstur kental dan rasa asam khas yoghurt.'],
                        ['judul' => 'Starter Culture dan Kultur Murni', 'isi' => 'Starter culture berupa kultur murni mikroorganisme digunakan untuk menjamin konsistensi proses fermentasi dan mutu produk akhir.'],
                        ['judul' => 'Parameter Keberhasilan Fermentasi', 'isi' => 'Keberhasilan fermentasi dapat dievaluasi dari perubahan pH, tekstur, aroma, dan pertumbuhan mikroorganisme target selama proses berlangsung.'],
                    ],
                ],
                'THP301' => [
                    'judul' => 'Keamanan Pangan dan Sanitasi',
                    'capaian' => [
                        'Menerapkan prinsip HACCP pada proses produksi pangan.',
                        'Memahami regulasi keamanan pangan di Indonesia.',
                    ],
                    'pendahuluan' => 'Keamanan pangan menjadi aspek krusial dalam industri pangan untuk melindungi konsumen dari bahaya fisik, kimia, dan biologis. Penerapan sistem HACCP dan sanitasi yang baik merupakan langkah preventif untuk menjamin produk yang dihasilkan aman dikonsumsi.',
                    'tinjauan_pustaka' => [
                        ['judul' => 'Titik Kritis Kendali (CCP)', 'isi' => 'CCP merupakan titik dalam proses produksi yang harus dikendalikan untuk mencegah, menghilangkan, atau mengurangi bahaya keamanan pangan hingga batas yang dapat diterima.'],
                        ['judul' => 'Good Manufacturing Practice (GMP)', 'isi' => 'GMP mengatur persyaratan dasar produksi pangan yang higienis, mencakup desain bangunan, peralatan, hingga perilaku pekerja.'],
                        ['judul' => 'Sanitasi Industri Pangan', 'isi' => 'Sanitasi mencakup pembersihan dan desinfeksi peralatan serta lingkungan produksi untuk mencegah kontaminasi silang.'],
                        ['judul' => 'Regulasi dan Standar BPOM', 'isi' => 'Regulasi keamanan pangan di Indonesia diatur oleh BPOM yang menetapkan standar mutu dan keamanan produk pangan yang beredar di pasaran.'],
                    ],
                ],
            ],
            'TPB' => [
                'TPB101' => [
                    'judul' => 'Mekanisasi dan Alat Mesin Pertanian',
                    'capaian' => [
                        'Mengenal jenis dan fungsi alat mesin pertanian (alsintan).',
                        'Memahami prinsip kerja alat olah tanah dan alat panen.',
                    ],
                    'pendahuluan' => 'Alat dan mesin pertanian (alsintan) berperan penting dalam meningkatkan efisiensi kerja pada berbagai tahap budidaya, mulai dari pengolahan tanah hingga panen. Pemahaman jenis dan prinsip kerja alsintan menjadi dasar dalam memilih teknologi yang sesuai dengan kondisi lahan dan komoditas.',
                    'tinjauan_pustaka' => [
                        ['judul' => 'Traktor Roda Dua dan Roda Empat', 'isi' => 'Traktor roda dua umumnya digunakan pada lahan sempit dan berlumpur, sedangkan traktor roda empat lebih sesuai untuk lahan luas dengan kapasitas kerja lebih besar.'],
                        ['judul' => 'Alat Pengolahan Tanah', 'isi' => 'Bajak singkal, bajak piring, dan garu digunakan untuk membalik, menghancurkan, dan meratakan tanah sebelum penanaman.'],
                        ['judul' => 'Alat Tanam dan Pemupuk', 'isi' => 'Alat tanam dan pemupuk mekanis mempercepat proses penanaman benih serta pemberian pupuk secara lebih seragam dibanding cara manual.'],
                        ['judul' => 'Alat dan Mesin Panen', 'isi' => 'Mesin panen seperti combine harvester mempercepat proses pemanenan dan mengurangi kehilangan hasil dibandingkan panen manual.'],
                    ],
                ],
                'TPB102' => [
                    'judul' => 'Teknik Irigasi dan Drainase',
                    'capaian' => [
                        'Memahami sistem penyediaan dan pembuangan air pada lahan pertanian.',
                        'Mampu membandingkan jenis-jenis sistem irigasi.',
                    ],
                    'pendahuluan' => 'Ketersediaan air yang cukup dan pembuangan kelebihan air yang baik sangat menentukan keberhasilan budidaya tanaman. Sistem irigasi dan drainase yang tepat perlu dirancang sesuai karakteristik lahan dan kebutuhan air tanaman.',
                    'tinjauan_pustaka' => [
                        ['judul' => 'Irigasi Permukaan', 'isi' => 'Irigasi permukaan mengalirkan air melalui saluran di atas permukaan tanah dan merupakan metode paling umum digunakan pada lahan sawah.'],
                        ['judul' => 'Irigasi Tetes dan Sprinkler', 'isi' => 'Irigasi tetes menyalurkan air langsung ke zona akar tanaman secara perlahan, sedangkan sprinkler menyemprotkan air menyerupai hujan buatan.'],
                        ['judul' => 'Perhitungan Kebutuhan Air Tanaman', 'isi' => 'Kebutuhan air tanaman dihitung berdasarkan evapotranspirasi dan koefisien tanaman untuk menentukan jadwal dan jumlah air irigasi yang tepat.'],
                        ['judul' => 'Sistem Drainase Lahan', 'isi' => 'Drainase berfungsi membuang kelebihan air pada lahan untuk mencegah genangan yang dapat merusak perakaran tanaman.'],
                    ],
                ],
                'TPB201' => [
                    'judul' => 'Instrumentasi dan Kontrol Otomatis',
                    'capaian' => [
                        'Memahami peran sensor dan sistem kendali pada pertanian presisi.',
                        'Mengenal konsep dasar IoT untuk pertanian.',
                    ],
                    'pendahuluan' => 'Perkembangan pertanian presisi mendorong penggunaan sensor dan sistem kendali otomatis untuk memantau serta mengatur kondisi lingkungan budidaya secara real-time. Pemahaman dasar instrumentasi dan konsep IoT menjadi bekal penting dalam merancang sistem pertanian cerdas.',
                    'tinjauan_pustaka' => [
                        ['judul' => 'Sensor Kelembaban dan Suhu', 'isi' => 'Sensor kelembaban dan suhu digunakan untuk memantau kondisi lingkungan tanaman secara kontinu sebagai dasar pengambilan keputusan irigasi maupun ventilasi.'],
                        ['judul' => 'Mikrokontroler dan Aktuator', 'isi' => 'Mikrokontroler memproses data dari sensor dan mengendalikan aktuator seperti pompa atau kipas berdasarkan logika yang telah diprogram.'],
                        ['judul' => 'Logika Kontrol Otomatis', 'isi' => 'Logika kontrol sederhana seperti if-then digunakan untuk mengatur respons sistem terhadap perubahan kondisi lingkungan secara otomatis.'],
                        ['judul' => 'Sistem IoT Pertanian', 'isi' => 'Internet of Things (IoT) memungkinkan integrasi data sensor, kontrol otomatis, dan pemantauan jarak jauh dalam satu sistem pertanian cerdas.'],
                    ],
                ],
                'TPB202' => [
                    'judul' => 'Energi Terbarukan untuk Pertanian',
                    'capaian' => [
                        'Memahami pemanfaatan energi surya dan biomassa di bidang pertanian.',
                    ],
                    'pendahuluan' => 'Pemanfaatan energi terbarukan seperti surya dan biomassa semakin penting untuk mendukung operasional pertanian yang efisien dan berkelanjutan. Pemahaman prinsip dasar teknologi energi terbarukan membantu petani mengurangi ketergantungan pada energi fosil.',
                    'tinjauan_pustaka' => [
                        ['judul' => 'Panel Surya untuk Pompa Air', 'isi' => 'Panel surya dapat dimanfaatkan sebagai sumber energi untuk menggerakkan pompa air irigasi tanpa bergantung pada listrik konvensional.'],
                        ['judul' => 'Biogas dari Limbah Pertanian', 'isi' => 'Limbah pertanian dan peternakan dapat diolah menjadi biogas melalui proses fermentasi anaerobik sebagai sumber energi alternatif.'],
                        ['judul' => 'Efisiensi Energi pada Alsintan', 'isi' => 'Optimalisasi penggunaan energi pada alat dan mesin pertanian dapat menekan biaya operasional sekaligus mengurangi emisi.'],
                    ],
                ],
                'TPB301' => [
                    'judul' => 'Teknik Tanah dan Konservasi Air',
                    'capaian' => [
                        'Memahami sifat fisik tanah dan pengelolaan air lahan.',
                        'Mampu menjelaskan prinsip konservasi lahan miring.',
                    ],
                    'pendahuluan' => 'Sifat fisik tanah dan pengelolaan air yang tepat sangat memengaruhi produktivitas lahan pertanian, terutama pada lahan miring yang rentan terhadap erosi. Pemahaman prinsip konservasi tanah dan air menjadi dasar dalam menjaga keberlanjutan lahan pertanian.',
                    'tinjauan_pustaka' => [
                        ['judul' => 'Struktur dan Tekstur Tanah', 'isi' => 'Struktur dan tekstur tanah menentukan kemampuan tanah menahan air dan udara yang dibutuhkan oleh akar tanaman.'],
                        ['judul' => 'Erosi dan Faktor Penyebabnya', 'isi' => 'Erosi dipengaruhi oleh kemiringan lahan, curah hujan, jenis tanah, dan tutupan vegetasi yang dapat menyebabkan hilangnya lapisan tanah subur.'],
                        ['judul' => 'Konservasi Lahan Miring', 'isi' => 'Teknik konservasi seperti terasering dan penanaman menurut kontur digunakan untuk mengurangi laju erosi pada lahan miring.'],
                        ['judul' => 'Neraca Air Lahan', 'isi' => 'Neraca air lahan menghitung keseimbangan antara curah hujan, evapotranspirasi, dan kebutuhan irigasi untuk perencanaan pengelolaan air yang efisien.'],
                    ],
                ],
            ],
            'TIP' => [
                'TIP101' => [
                    'judul' => 'Pengantar Manajemen Agroindustri',
                    'capaian' => [
                        'Memahami ruang lingkup dan karakteristik agroindustri.',
                        'Mengenal tantangan utama pengelolaan agroindustri di Indonesia.',
                    ],
                    'pendahuluan' => 'Agroindustri merupakan sektor yang mengolah hasil pertanian menjadi produk bernilai tambah, dengan karakteristik bahan baku yang khas seperti mudah rusak dan bersifat musiman. Pemahaman ruang lingkup dan tantangan agroindustri menjadi dasar dalam pengelolaan usaha berbasis pertanian.',
                    'tinjauan_pustaka' => [
                        ['judul' => 'Struktur Industri Berbasis Pertanian', 'isi' => 'Industri berbasis pertanian mencakup rangkaian kegiatan dari produksi bahan baku hingga pengolahan menjadi produk akhir yang siap dipasarkan.'],
                        ['judul' => 'Rantai Nilai Agroindustri', 'isi' => 'Rantai nilai menggambarkan aliran nilai tambah dari petani, pengolah, hingga konsumen akhir dalam sistem agroindustri.'],
                        ['judul' => 'Karakteristik Bahan Baku Pertanian', 'isi' => 'Bahan baku pertanian umumnya bersifat mudah rusak (perishable), musiman, dan beragam mutu sehingga memerlukan penanganan khusus.'],
                        ['judul' => 'Tantangan dan Peluang Agroindustri', 'isi' => 'Agroindustri di Indonesia menghadapi tantangan seperti keterbatasan teknologi dan pasar, namun memiliki peluang besar dari kekayaan sumber daya alam.'],
                    ],
                ],
                'TIP102' => [
                    'judul' => 'Perencanaan dan Pengendalian Produksi',
                    'capaian' => [
                        'Mampu menyusun rencana produksi agroindustri sederhana.',
                    ],
                    'pendahuluan' => 'Perencanaan produksi yang baik diperlukan agar agroindustri dapat memenuhi permintaan pasar secara efisien tanpa kelebihan atau kekurangan persediaan. Pemahaman proses peramalan, penjadwalan, dan pengendalian persediaan menjadi dasar dalam menyusun rencana produksi yang efektif.',
                    'tinjauan_pustaka' => [
                        ['judul' => 'Peramalan Permintaan', 'isi' => 'Peramalan permintaan menggunakan data historis untuk memprediksi kebutuhan produksi pada periode mendatang.'],
                        ['judul' => 'Perencanaan Kapasitas Produksi', 'isi' => 'Perencanaan kapasitas memastikan sumber daya produksi (mesin, tenaga kerja) mencukupi untuk memenuhi target produksi.'],
                        ['judul' => 'Penjadwalan Produksi', 'isi' => 'Penjadwalan produksi mengatur urutan dan waktu pelaksanaan proses produksi agar berjalan efisien.'],
                        ['judul' => 'Pengendalian Persediaan', 'isi' => 'Pengendalian persediaan bertujuan menjaga keseimbangan antara ketersediaan bahan baku/produk dengan biaya penyimpanan.'],
                    ],
                ],
                'TIP201' => [
                    'judul' => 'Teknik Tata Cara Kerja',
                    'capaian' => [
                        'Mampu menganalisis efisiensi kerja pada lini produksi.',
                    ],
                    'pendahuluan' => 'Efisiensi kerja pada lini produksi agroindustri dapat ditingkatkan melalui analisis metode dan waktu kerja. Teknik tata cara kerja memberikan dasar ilmiah untuk merancang metode kerja yang lebih produktif dan ergonomis.',
                    'tinjauan_pustaka' => [
                        ['judul' => 'Studi Waktu (Time Study)', 'isi' => 'Studi waktu mengukur waktu yang dibutuhkan untuk menyelesaikan suatu elemen kerja sebagai dasar penentuan waktu baku.'],
                        ['judul' => 'Studi Gerak (Motion Study)', 'isi' => 'Studi gerak menganalisis pola gerakan pekerja untuk mengidentifikasi gerakan yang tidak efisien dan dapat dihilangkan.'],
                        ['judul' => 'Peta Kerja', 'isi' => 'Peta kerja menggambarkan urutan proses kerja secara visual untuk membantu identifikasi area perbaikan.'],
                        ['judul' => 'Penentuan Waktu Baku', 'isi' => 'Waktu baku dihitung dari waktu normal ditambah kelonggaran (allowance) untuk kebutuhan pribadi, kelelahan, dan hambatan tak terhindarkan.'],
                    ],
                ],
                'TIP202' => [
                    'judul' => 'Sistem Manajemen Mutu Agroindustri',
                    'capaian' => [
                        'Menerapkan prinsip standar mutu ISO dan HACCP pada agroindustri.',
                    ],
                    'pendahuluan' => 'Penerapan sistem manajemen mutu seperti ISO dan HACCP penting untuk menjamin konsistensi mutu produk agroindustri serta memenuhi tuntutan pasar dan regulasi. Pemahaman dokumentasi, audit, dan sertifikasi mutu menjadi dasar dalam membangun sistem manajemen mutu yang efektif.',
                    'tinjauan_pustaka' => [
                        ['judul' => 'Dokumentasi Sistem Mutu', 'isi' => 'Dokumentasi sistem mutu mencatat seluruh prosedur dan catatan produksi sebagai bukti kepatuhan terhadap standar yang ditetapkan.'],
                        ['judul' => 'Audit Internal', 'isi' => 'Audit internal dilakukan secara berkala untuk memastikan penerapan sistem mutu berjalan sesuai standar yang ditetapkan.'],
                        ['judul' => 'Tindakan Korektif dan Pencegahan', 'isi' => 'Tindakan korektif dilakukan untuk mengatasi ketidaksesuaian yang ditemukan, sedangkan tindakan pencegahan mencegah terulangnya masalah serupa.'],
                        ['judul' => 'Sertifikasi Mutu Produk', 'isi' => 'Sertifikasi mutu memberikan pengakuan resmi bahwa produk telah memenuhi standar mutu dan keamanan yang dipersyaratkan.'],
                    ],
                ],
                'TIP301' => [
                    'judul' => 'Manajemen Rantai Pasok dan Limbah Agroindustri',
                    'capaian' => [
                        'Memahami aliran bahan baku hingga produk jadi serta pengelolaan limbahnya.',
                    ],
                    'pendahuluan' => 'Pengelolaan rantai pasok yang efisien serta penanganan limbah yang tepat menjadi kunci keberlanjutan usaha agroindustri. Pemahaman aliran logistik dan prinsip ekonomi sirkular membantu mengurangi biaya sekaligus dampak lingkungan dari kegiatan produksi.',
                    'tinjauan_pustaka' => [
                        ['judul' => 'Logistik Hulu-Hilir', 'isi' => 'Logistik hulu-hilir mencakup aliran bahan baku dari petani hingga distribusi produk jadi ke konsumen.'],
                        ['judul' => 'Pengolahan Limbah Agroindustri', 'isi' => 'Limbah agroindustri dapat diolah melalui metode fisik, kimia, maupun biologis untuk mengurangi dampak pencemaran lingkungan.'],
                        ['judul' => 'Prinsip Ekonomi Sirkular', 'isi' => 'Ekonomi sirkular mendorong pemanfaatan kembali limbah dan produk samping menjadi produk bernilai guna, mengurangi limbah terbuang.'],
                        ['judul' => 'Efisiensi Rantai Pasok', 'isi' => 'Efisiensi rantai pasok dicapai melalui koordinasi yang baik antar pelaku mulai dari petani, pengolah, hingga distributor.'],
                    ],
                ],
            ],
        ];

        foreach ($byProdi as $kode => $matakuliahs) {
            $prodi = Prodi::where('kode', $kode)->firstOrFail();

            foreach ($matakuliahs as $mkKode => $item) {
                $matakuliah = Matakuliah::where('kode', $mkKode)->firstOrFail();

                Materi::updateOrCreate(
                    ['slug' => Str::slug($kode.'-'.$item['judul'])],
                    [
                        'prodi_id' => $prodi->id,
                        'matakuliah_id' => $matakuliah->id,
                        'pertemuan_ke' => 1,
                        'judul' => $item['judul'],
                        'capaian' => $item['capaian'],
                        'pendahuluan' => $item['pendahuluan'],
                        'tinjauan_pustaka' => $item['tinjauan_pustaka'],
                    ]
                );
            }
        }
    }
}
