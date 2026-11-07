# 🧺 LaundryKu - Sistem Manajemen Laundry

Aplikasi web sederhana untuk mengelola usaha laundry. **Customer** dapat melacak status cucian secara mandiri memakai nomor resi (tanpa login), sedangkan **admin** dapat mengelola transaksi, memperbarui status pengerjaan, dan mencetak struk.

Dibangun dengan **PHP native**, **MySQL**, **HTML**, **CSS** (Flexbox, Grid, responsif), dan **JavaScript** murni, tanpa framework.

---

## Daftar Isi

1. [Fitur](#fitur)
2. [Teknologi](#teknologi)
3. [Struktur Folder](#struktur-folder)
4. [Struktur Database](#struktur-database)
5. [Instalasi](#instalasi)
6. [Konfigurasi](#konfigurasi)
7. [Cara Penggunaan](#cara-penggunaan)
8. [Penjelasan Setiap File](#penjelasan-setiap-file)
9. [Keamanan](#keamanan)
10. [Keterbatasan & Rencana Pengembangan](#keterbatasan--rencana-pengembangan)
11. [Pemecahan Masalah](#pemecahan-masalah)
12. [Deployment](#deployment)

---

## Fitur

### Sisi Customer (publik, tanpa login)

- **Halaman beranda** berisi informasi layanan, daftar harga per Kg, dan cara kerja.
- **Pencarian resi** dengan validasi format di browser.
- **Halaman pelacakan** yang menampilkan nama customer, jenis layanan, berat (Kg), total harga, tanggal masuk, dan indikator 5 tahap status:

  `Baru Masuk → Sedang Dicuci → Disetrika → Siap Diambil → Selesai`

### Sisi Admin (wajib login)

- **Login admin** dengan session.
- **Dashboard** berisi ringkasan jumlah transaksi per status, tabel seluruh transaksi, pencarian (no resi, nama, no HP), dan filter status.
- **Tambah transaksi** dengan form data customer, total harga otomatis, dan nomor resi unik (`RSI-XXXXXX`).
- **Update status** langsung dari tabel dashboard.
- **Cetak struk** berukuran 58 mm untuk printer thermal, otomatis terbuka setelah transaksi disimpan.
- **Logout** yang menghapus session.

---

## Teknologi

| Lapisan | Teknologi |
|---|---|
| Frontend | HTML5, CSS3 (Flexbox, Grid, CSS Variables, media query), JavaScript (vanilla) |
| Backend | PHP 8.x native (mysqli, prepared statements) |
| Database | MySQL / MariaDB |
| Server lokal | Laragon atau XAMPP (Apache + PHP + MySQL) |
| Font | Google Fonts (Poppins) |

> Tidak memakai framework atau library eksternal (Laravel, Bootstrap, jQuery, dan sejenisnya).

**Kebutuhan minimum:** PHP 7.4 atau lebih baru (disarankan 8.x) dengan ekstensi `mysqli`, serta MySQL 5.7+ atau MariaDB 10+.

---

## Struktur Folder

```
laundry-app/
│
├── assets/
│   ├── css/
│   │   └── style.css          # Styling utama, Flexbox, Grid, & Responsive
│   └── js/
│       └── script.js          # Validasi & interaksi tambahan
│
├── config/
│   └── database.php           # Koneksi MySQL + fungsi bantu + konstanta
│
├── admin/
│   ├── login.php              # Halaman login admin
│   ├── logout.php             # Keluar & hapus session
│   ├── dashboard.php          # Daftar transaksi, pencarian, filter
│   ├── tambah-transaksi.php   # Form & proses tambah transaksi
│   ├── update-status.php      # Proses ubah status cucian
│   └── cetak-resi.php         # Struk thermal 58 mm
│
├── index.php                  # Halaman utama (cek resi untuk customer)
├── detail-resi.php            # Hasil pelacakan status cucian
├── database.sql               # Struktur & data awal database
└── README.md                  # Dokumentasi ini
```

---

## Struktur Database

Nama database: **`laundry_db`**

### Tabel `admin`

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | INT UNSIGNED, PK, AUTO_INCREMENT | ID admin |
| `username` | VARCHAR(50), UNIQUE | Nama pengguna |
| `password` | VARCHAR(255) | Hash bcrypt (`password_hash`) |

### Tabel `transaksi`

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | INT UNSIGNED, PK, AUTO_INCREMENT | ID transaksi |
| `no_resi` | VARCHAR(20), UNIQUE | Nomor resi untuk pelacakan |
| `nama_customer` | VARCHAR(100) | Nama customer |
| `no_hp` | VARCHAR(20) | Nomor HP customer |
| `jenis_layanan` | VARCHAR(50) | Cuci Kiloan, Cuci + Setrika, Express, Setrika Saja |
| `berat_kg` | DECIMAL(6,2) | Berat cucian (mendukung desimal) |
| `total_harga` | INT UNSIGNED | Total dalam Rupiah |
| `status_cucian` | ENUM | `Baru Masuk`, `Sedang Dicuci`, `Disetrika`, `Siap Diambil`, `Selesai` (default: `Baru Masuk`) |
| `tanggal_masuk` | DATETIME | Terisi otomatis saat data dibuat |

---

## Instalasi

### Opsi A: Laragon (Windows)

1. Pasang dan jalankan **Laragon**, lalu klik **Start All**.
2. Salin folder proyek ke `C:\laragon\www\laundry-app\`. Pastikan `index.php`, `admin/`, dan `assets/` berada langsung di dalam folder tersebut.
3. Buat database dan import `database.sql`:
   - Klik tombol **Database** di Laragon (HeidiSQL) lalu pilih *File → Run SQL file…* dan pilih `database.sql`, **atau**
   - lewat terminal: `mysql -u root < database.sql`
4. Buka di browser:
   - Customer: `http://localhost/laundry-app/`
   - Admin: `http://localhost/laundry-app/admin/login.php`

> Laragon juga dapat membuat alamat otomatis `http://laundry-app.test` jika *Auto Virtual Hosts* aktif.

### Opsi B: XAMPP

1. Jalankan **Apache** dan **MySQL** di XAMPP Control Panel.
2. Salin folder proyek ke `C:\xampp\htdocs\laundry-app\`.
3. Buka `http://localhost/phpmyadmin`, pilih tab **Import**, pilih `database.sql`, lalu klik **Go**.
4. Buka `http://localhost/laundry-app/`.

### Opsi C: Server bawaan PHP (untuk uji cepat)

```bash
cd laundry-app
php -S localhost:8000
```

Buka `http://localhost:8000/`. MySQL tetap harus berjalan dan `database.sql` sudah di-import.

---

## Konfigurasi

Pengaturan ada di **`config/database.php`**:

```php
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';          // Laragon & XAMPP default: kosong
$DB_NAME = 'laundry_db';
```

### Mengubah harga layanan

Harga per Kg diatur di konstanta `DAFTAR_LAYANAN` pada file yang sama:

```php
const DAFTAR_LAYANAN = [
    'Cuci Kiloan'    => 7000,
    'Cuci + Setrika' => 9000,
    'Express'        => 12000,
    'Setrika Saja'   => 5000,
];
```

Menambah atau mengubah layanan cukup dilakukan di sini. Halaman beranda dan form transaksi akan menyesuaikan otomatis. Jika menambah layanan baru, tambahkan juga ikon dan keterangannya di array `$infoLayanan` pada `index.php` (opsional).

### Akun admin default

| Username | Password |
|---|---|
| `admin` | `admin123` |

> ⚠️ **Segera ganti password setelah instalasi.** Buat hash baru dengan:
>
> ```bash
> php -r "echo password_hash('PasswordBaruAnda', PASSWORD_DEFAULT);"
> ```
>
> Lalu jalankan di MySQL:
>
> ```sql
> UPDATE admin SET password = 'HASH_HASIL_DI_ATAS' WHERE username = 'admin';
> ```

### Data contoh

`database.sql` menyertakan tiga transaksi contoh (`RSI-DEMO01`, `RSI-DEMO02`, `RSI-DEMO03`) untuk uji coba. Hapus jika tidak diperlukan:

```sql
DELETE FROM transaksi WHERE no_resi LIKE 'RSI-DEMO%';
```

---

## Cara Penggunaan

### Customer

1. Buka halaman utama.
2. Masukkan nomor resi dari struk (contoh: `RSI-DEMO01`), lalu klik **Lacak**.
3. Lihat status dan detail cucian. Jika nomor tidak ditemukan, akan muncul pesan kesalahan.

### Admin

1. Masuk di `/admin/login.php`.
2. Klik **+ Transaksi Baru**, isi nama, no HP, jenis layanan, dan berat. Total harga terhitung otomatis.
3. Klik **Simpan & Cetak Resi**. Nomor resi dibuat otomatis dan struk langsung terbuka untuk dicetak.
4. Di dashboard, ubah status lewat dropdown di kolom **Status** (akan ada konfirmasi).
5. Cari transaksi dengan kotak pencarian atau filter status. Klik **Cetak** untuk mencetak ulang struk.

### Mencetak struk thermal

Struk dirancang untuk kertas **58 mm**. Pada dialog cetak browser:

- pilih printer thermal Anda,
- atur **Margins: None** dan **Scale: 100%**,
- matikan *Headers and footers*.

---

## Penjelasan Setiap File

| File | Fungsi |
|---|---|
| `config/database.php` | Koneksi MySQL, session, konstanta `DAFTAR_STATUS` dan `DAFTAR_LAYANAN`, serta fungsi bantu: `e()` (escape HTML), `rupiah()`, `tgl()`, `status_slug()`, `wajib_login()`, `csrf_token()`, `csrf_valid()`, `set_flash()`, `ambil_flash()` |
| `index.php` | Landing page, pencarian resi, daftar layanan |
| `detail-resi.php` | Mengambil transaksi berdasarkan `no_resi` dan menampilkan tracker status |
| `admin/login.php` | Autentikasi admin (`password_verify`, session) |
| `admin/logout.php` | Menghapus session dan cookie |
| `admin/dashboard.php` | Ringkasan per status, tabel transaksi, pencarian, filter, ubah status |
| `admin/tambah-transaksi.php` | Validasi input, hitung total di server, buat resi unik, simpan |
| `admin/update-status.php` | Memperbarui status (hanya menerima POST, dengan token CSRF) |
| `admin/cetak-resi.php` | Struk 58 mm dengan CSS khusus cetak |
| `assets/js/script.js` | Validasi format resi, hitung total otomatis, validasi form, konfirmasi ubah status |
| `assets/css/style.css` | Seluruh tampilan dan responsivitas |
| `database.sql` | Pembuatan database, tabel, dan data awal |

### Alur pembuatan nomor resi

Format `RSI-` diikuti 6 karakter acak dari huruf dan angka yang tidak membingungkan (tanpa `I`, `O`, `0`, `1`). Sebelum disimpan, sistem memeriksa ke database agar nomor tidak pernah kembar.

---

## Keamanan

- **SQL Injection:** seluruh query memakai *prepared statements*.
- **XSS:** semua output di-escape dengan `htmlspecialchars()`.
- **Password:** disimpan sebagai hash bcrypt dan diverifikasi dengan `password_verify()`.
- **Session:** `session_regenerate_id()` saat login, semua halaman admin dilindungi `wajib_login()`.
- **CSRF:** setiap form POST memakai token.
- **Integritas harga:** total dihitung ulang di server dan tidak mengambil angka dari browser.
- **Validasi ganda:** di JavaScript (kenyamanan) dan PHP (keamanan). Status hanya boleh salah satu nilai yang terdaftar.
- **Privasi pelacakan:** halaman publik tidak menampilkan nomor HP customer.

### Saran sebelum dipakai sungguhan

- Ganti password admin default.
- Gunakan **HTTPS** di server produksi.
- Buat user MySQL khusus (jangan `root`) dengan hak akses terbatas pada `laundry_db`.
- Nonaktifkan tampilan error PHP di produksi (`display_errors = Off`).
- Lakukan backup database secara berkala.

---

## Keterbatasan & Rencana Pengembangan

**Belum tersedia:**

- Edit dan hapus transaksi
- Pagination pada tabel transaksi
- Manajemen admin (tambah admin, ganti password lewat web)
- Pembatasan percobaan login (*rate limiting*)
- Notifikasi WhatsApp/SMS saat cucian siap diambil
- Laporan dan rekap pendapatan (harian, bulanan)
- Pembayaran dan status lunas
- Ekspor data ke Excel/PDF

**Ide pengembangan:** peran multi-user (kasir/pemilik), diskon dan member, pelacakan dengan QR code pada struk, serta REST API.

---

## Pemecahan Masalah

| Masalah | Penyebab & solusi |
|---|---|
| **"Koneksi database gagal"** | MySQL belum menyala, `database.sql` belum di-import, atau kredensial di `config/database.php` salah |
| **Halaman menampilkan kode PHP** atau 404 | Dibuka lewat `file:///`. Gunakan `http://localhost/...` dan pastikan folder ada di `www`/`htdocs` |
| **Login selalu gagal** | Pastikan data `admin` ada di database. Jika hash rusak, buat ulang dengan `password_hash` (lihat bagian Konfigurasi) |
| **Port 80 bentrok** (Apache tidak mau start) | Ubah port Apache (misalnya 8080) lalu buka `http://localhost:8080/laundry-app/` |
| **Tampilan tidak berubah setelah edit CSS** | Cache browser. Tekan `Ctrl + F5` |
| **Struk terpotong saat dicetak** | Atur margin ke *None*, skala 100%, dan ukuran kertas sesuai printer |
| **Font tidak tampil seperti desain** | Font Poppins dimuat dari Google Fonts dan butuh internet. Tanpa internet, font sistem dipakai |

---

## Deployment

Aplikasi ini membutuhkan **PHP dan MySQL**, sehingga cocok di hosting yang mendukung keduanya (misalnya shared hosting cPanel, Hostinger, Niagahoster, atau InfinityFree). Langkah umum:

1. Unggah seluruh isi folder ke `public_html` (atau `htdocs`).
2. Buat database di panel hosting lalu import `database.sql`.
3. Sesuaikan kredensial di `config/database.php`.
4. Ganti password admin dan aktifkan HTTPS.

> **Vercel/Netlify tidak mendukung** PHP dan MySQL secara langsung, jadi tidak cocok untuk proyek ini tanpa penulisan ulang ke JavaScript.

---

## Lisensi

Proyek ini bebas digunakan dan dimodifikasi untuk keperluan belajar maupun pengembangan pribadi.
