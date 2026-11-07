<?php
/**
 * Konfigurasi koneksi database + helper bersama.
 * Ubah kredensial di bawah sesuai server MySQL Anda.
 */

date_default_timezone_set('Asia/Jakarta');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'laundry_db';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    die('Koneksi database gagal. Periksa pengaturan di config/database.php.');
}

/* Urutan status pengerjaan cucian */
const DAFTAR_STATUS = [
    'Baru Masuk',
    'Sedang Dicuci',
    'Disetrika',
    'Siap Diambil',
    'Selesai',
];

/* Jenis layanan => harga per Kg (Rupiah) */
const DAFTAR_LAYANAN = [
    'Cuci Kiloan'   => 7000,
    'Cuci + Setrika' => 9000,
    'Express'       => 12000,
    'Setrika Saja'  => 5000,
];

/** Escape output HTML. */
function e($teks): string
{
    return htmlspecialchars((string) $teks, ENT_QUOTES, 'UTF-8');
}

/** Format angka ke Rupiah, contoh: Rp 21.000 */
function rupiah($angka): string
{
    return 'Rp ' . number_format((float) $angka, 0, ',', '.');
}

/** Format tanggal Indonesia sederhana: 03-11-2026 14:30 */
function tgl($datetime): string
{
    return date('d-m-Y H:i', strtotime($datetime));
}

/** Ubah status menjadi slug untuk class CSS: "Sedang Dicuci" => "sedang-dicuci" */
function status_slug(string $status): string
{
    return strtolower(str_replace(' ', '-', $status));
}

/** Hentikan akses jika admin belum login (dipanggil dari folder admin/). */
function wajib_login(): void
{
    if (empty($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit;
    }
}

/** Token CSRF sederhana untuk form POST. */
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_valid(?string $token): bool
{
    return !empty($_SESSION['csrf']) && is_string($token) && hash_equals($_SESSION['csrf'], $token);
}

/** Simpan pesan flash untuk ditampilkan di halaman berikutnya. */
function set_flash(string $tipe, string $pesan): void
{
    $_SESSION['flash'] = ['tipe' => $tipe, 'pesan' => $pesan];
}

function ambil_flash(): ?array
{
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}
