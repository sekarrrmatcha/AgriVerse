# AgriVerse — Paket Laravel (Landing, Login, Dashboard, Database)

Paket ini berisi file tambahan (bukan project Laravel penuh) untuk ditempelkan ke
project Laravel baru. Isinya: halaman awal (landing), login & register, Dashboard/Beranda
setelah login, serta migration + model + seeder untuk 3 program studi, materi, dan
praktikum — sesuai desain AgriVerse yang sudah dibuat sebelumnya.

Kamu bilang sisanya mau disesuaikan sendiri, jadi paket ini sengaja dibuat **minim
dependency** (tanpa Breeze/Jetstream, tanpa build step Tailwind/Vite) supaya gampang
dibaca dan dimodifikasi.

---

## 1. Isi Paket

```
database/migrations/      6 file migration (prodis, materis, praktikums, kolom user, progress)
database/seeders/         ProdiSeeder, MateriSeeder, PraktikumSeeder, DatabaseSeeder
app/Models/                Prodi, Materi, Praktikum, MateriProgress, PraktikumProgress, User
app/Http/Controllers/      LandingController, DashboardController, MateriController,
                            PraktikumController, Auth/LoginController, Auth/RegisterController
resources/views/           welcome, auth/login, auth/register, dashboard,
                            materi/index+show, praktikum/index+show, layouts, partials
routes/web.php             Semua route (landing, auth, dashboard, materi, praktikum)
.env.example                Contoh konfigurasi (SQLite, paling simpel)
```

Semua file PHP sudah dicek dengan `php -l` (tanpa error sintaks), dan semua
komponen Blade sudah dicek strukturnya (tag `@foreach/@endforeach`, dll. seimbang).
Karena project ini tidak menyertakan `vendor/` Laravel (perlu Composer & akses
internet ke Packagist), aku belum bisa menjalankan `php artisan serve` di sini —
jadi tetap tes di komputermu setelah instalasi (langkah di bawah).

---

## 2. Instalasi (di komputermu, butuh PHP ≥ 8.2 & Composer)

```bash
# 1. Buat project Laravel baru
composer create-project laravel/laravel agriverse
cd agriverse

# 2. Salin semua folder dari paket ini ke dalam folder "agriverse",
#    timpa file yang sudah ada (routes/web.php, app/Models/User.php,
#    database/seeders/DatabaseSeeder.php)

# 3. Siapkan .env
cp .env.example .env
php artisan key:generate

# 4. Siapkan database (SQLite — paling gampang, tanpa install MySQL)
touch database/database.sqlite
# Windows (PowerShell): New-Item database/database.sqlite -ItemType File -Force

# 5. Migrasi + isi data awal (3 prodi, 15 materi, 15 praktikum, 1 akun demo)
php artisan migrate --seed

# 6. Jalankan
php artisan serve
```

Buka `http://localhost:8000` → landing page AgriVerse.
Login demo: **mahasiswa@agriverse.test** / **password** (dibuat otomatis oleh seeder),
atau klik "Daftar" untuk bikin akun baru.

---

## 3. Alur yang Sudah Jadi

- `/` — Landing page publik (hero, statistik, 3 kartu program studi)
- `/register`, `/login` — Daftar & masuk (pakai `Auth::attempt`, session-based, bawaan Laravel)
- `/dashboard` — Beranda setelah login: sapaan, progres belajar (dihitung dari database),
  dan peta 3 program studi
- `/prodi/{prodi}/materi` → `/materi/{materi}` — Daftar & detail materi, tombol "Tandai selesai"
  yang beneran tersimpan ke tabel `materi_progresses`
- `/prodi/{prodi}/praktikum` → `/praktikum/{praktikum}` — Daftar & detail praktikum, checklist
  langkah kerja tersimpan ke `praktikum_progresses`, plus kuis singkat (dicek di sisi
  browser dengan JavaScript kecil, silakan dikembangkan ke server kalau mau direkap juga)

Semua controller sudah pakai **route model binding** lewat `slug`, jadi URL-nya rapi,
misalnya `/prodi/teknologi-hasil-pertanian/praktikum`.

### Yang perlu kamu sesuaikan sendiri (sesuai request-mu)
- Styling lanjutan / branding kampus (logo PENS, warna resmi, dll.)
- Validasi tambahan di form register (misal format NIM)
- Halaman profil, ganti password, upload foto
- Reset password via email (Laravel sudah punya fondasi `Illuminate\Auth\Notifications\ResetPassword`,
  tinggal tambahkan controller & view-nya)
- Rekap nilai kuis ke database (`praktikum_progresses.kuis_benar` sudah disediakan kolomnya)

---

## 4. Setup API (opsional, kalau nanti mau dipakai app mobile/eksternal)

Laravel 11 tidak lagi menyertakan `routes/api.php` secara default. Untuk mengaktifkannya:

```bash
php artisan install:api
```

Perintah ini otomatis meng-install **Laravel Sanctum** (paket resmi, gratis, sudah
termasuk di ekosistem Laravel — bukan layanan pihak ketiga berbayar) dan membuat
`routes/api.php`. Setelah itu kamu bisa expose data yang sama lewat API, misalnya:

```php
// routes/api.php
Route::middleware('auth:sanctum')->get('/prodi', function () {
    return \App\Models\Prodi::with('materis', 'praktikums')->get();
});
```

Sanctum cocok untuk autentikasi token sederhana (SPA terpisah atau aplikasi mobile)
tanpa biaya tambahan apa pun.

---

## 5. Domain & Hosting Gratis — opsi yang realistis per Juli 2026

Lanskap hosting gratis berubah cukup cepat, jadi ini kondisi paling update yang aku
temukan saat menyusun paket ini:

### A. Untuk demo cepat ke dosen/pembimbing (paling gampang, 100% gratis)
Jalankan lokal, lalu buka tunnel sementara pakai **ngrok**:
```bash
php artisan serve
ngrok http 8000
```
Ngrok kasih URL publik sementara (`https://xxxx.ngrok-free.app`) tanpa perlu kartu
kredit. Cocok untuk presentasi TA atau demo dadakan, tapi URL-nya berubah tiap kali
ngrok dijalankan ulang (kecuali pakai akun berbayar).

### B. Untuk hosting yang lebih permanen: Render (gratis, tanpa kartu kredit)
Saat ini **Render** masih punya free tier yang benar-benar gratis untuk web service:
- Gratis, tidak perlu kartu kredit.
- Dapat subdomain otomatis: `nama-proyek.onrender.com`.
- **Catatan penting**: service gratis "tidur" setelah ±15 menit tanpa trafik, jadi
  akses pertama setelah idle bisa lambat (30–50 detik). Database PostgreSQL gratisnya
  juga ada batas masa aktif (perlu dicek ulang di dashboard Render saat kamu daftar,
  karena kebijakan ini beberapa kali berubah). Untuk proyek kuliah skala kecil,
  alternatifnya pakai SQLite seperti di `.env.example` di atas supaya tidak bergantung
  pada database terpisah.
- Langkah besarnya: push project ke GitHub → hubungkan repo di dashboard Render →
  pilih "Web Service" → Render otomatis mendeteksi PHP/Laravel (atau kamu bisa
  sediakan `Dockerfile` sederhana kalau mau kontrol penuh) → deploy.

### C. Railway — sekarang **bukan lagi pilihan gratis permanen**
Dulu Railway terkenal karena gratis, tapi sejak 2024 mereka menghapus free tier
permanennya. Sekarang akun baru cuma dapat kredit trial sekali (sekitar $5) lalu
wajib upgrade ke paket berbayar (mulai ~$5/bulan) untuk tetap online. Jadi kalau
tujuannya benar-benar gratis, Render lebih cocok dibanding Railway saat ini.

### D. Hosting shared PHP gratis (InfinityFree, 000webhost, dsb.)
Secara teknis mendukung PHP, tapi biasanya **tidak menyediakan akses SSH/composer/artisan**
yang dibutuhkan Laravel untuk migrasi database dan cache config — jadi kurang
direkomendasikan kecuali kamu siap banyak akal-akalan. Render (opsi B) jauh lebih
cocok untuk aplikasi Laravel.

### E. Domain sendiri (custom domain, kalau subdomain gratis dirasa kurang meyakinkan)
Domain **.id gratis sepenuhnya sekarang sudah tidak ada** (program lama PANDI untuk
`my.id` gratis sudah dihentikan). Tapi domain **`.my.id`** resmi dari PANDI sekarang
sangat murah (mulai sekitar Rp25.000/tahun, cukup modal KTP, aktif dalam hitungan
menit) — jadi kalau nanti proyek ini mau dipakai serius/dipamerkan sebagai portofolio,
ini opsi paling realistis dan tetap murah. Untuk tahap tugas kuliah/TA sekarang,
subdomain gratis dari Render (opsi B) sudah lebih dari cukup.

**Ringkasnya:** mulai dari opsi A (ngrok) untuk demo cepat → naik ke opsi B (Render)
kalau butuh URL yang lebih permanen untuk dipamerkan → baru pertimbangkan domain
`.my.id` (opsi E) kalau proyeknya mau dilanjutkan jadi portofolio serius.

---

## 6. Kredensial Demo

| Email | Password |
|---|---|
| mahasiswa@agriverse.test | password |

Dibuat otomatis oleh `DatabaseSeeder`. Hapus atau ganti sebelum deploy ke publik.
