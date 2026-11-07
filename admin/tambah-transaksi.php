<?php
require __DIR__ . '/../config/database.php';
wajib_login();

$errors = [];
$old = ['nama_customer' => '', 'no_hp' => '', 'jenis_layanan' => '', 'berat_kg' => ''];

/** Buat nomor resi unik, contoh: RSI-7K3Q9A */
function buat_no_resi(mysqli $conn): string
{
    $karakter = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // tanpa I, O, 0, 1 agar tidak membingungkan
    $stmt = $conn->prepare('SELECT 1 FROM transaksi WHERE no_resi = ? LIMIT 1');
    do {
        $kode = '';
        for ($i = 0; $i < 6; $i++) {
            $kode .= $karakter[random_int(0, strlen($karakter) - 1)];
        }
        $resi = 'RSI-' . $kode;
        $stmt->bind_param('s', $resi);
        $stmt->execute();
        $ada = $stmt->get_result()->num_rows > 0;
    } while ($ada);
    $stmt->close();
    return $resi;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['nama_customer'] = trim($_POST['nama_customer'] ?? '');
    $old['no_hp']         = trim($_POST['no_hp'] ?? '');
    $old['jenis_layanan'] = $_POST['jenis_layanan'] ?? '';
    $old['berat_kg']      = str_replace(',', '.', trim($_POST['berat_kg'] ?? ''));

    if (!csrf_valid($_POST['csrf'] ?? null)) {
        $errors[] = 'Sesi tidak valid, silakan muat ulang halaman.';
    }
    if ($old['nama_customer'] === '' || mb_strlen($old['nama_customer']) > 100) {
        $errors[] = 'Nama customer wajib diisi (maksimal 100 karakter).';
    }
    if (!preg_match('/^[0-9+][0-9\s-]{7,18}$/', $old['no_hp'])) {
        $errors[] = 'No HP tidak valid (8-20 digit angka).';
    }
    if (!array_key_exists($old['jenis_layanan'], DAFTAR_LAYANAN)) {
        $errors[] = 'Jenis layanan tidak valid.';
    }
    if (!is_numeric($old['berat_kg']) || (float) $old['berat_kg'] < 0.1 || (float) $old['berat_kg'] > 999) {
        $errors[] = 'Berat harus antara 0,1 dan 999 Kg.';
    }

    if (!$errors) {
        $berat  = round((float) $old['berat_kg'], 2);
        $total  = (int) round($berat * DAFTAR_LAYANAN[$old['jenis_layanan']]); // dihitung di server
        $resi   = buat_no_resi($conn);
        $status = DAFTAR_STATUS[0];

        $stmt = $conn->prepare(
            'INSERT INTO transaksi (no_resi, nama_customer, no_hp, jenis_layanan, berat_kg, total_harga, status_cucian)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->bind_param('ssssdis', $resi, $old['nama_customer'], $old['no_hp'], $old['jenis_layanan'], $berat, $total, $status);
        $stmt->execute();
        $idBaru = $stmt->insert_id;
        $stmt->close();

        set_flash('success', "Transaksi $resi berhasil disimpan.");
        header('Location: cetak-resi.php?id=' . $idBaru . '&auto=1');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Transaksi Baru - LaundryYuk</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<header class="navbar">
    <div class="container navbar-inner">
        <a href="dashboard.php" class="brand">Laundry<span>Yuk</span> <small class="tag">Admin</small></a>
        <nav class="nav-actions"><a href="dashboard.php" class="btn btn-outline btn-sm">&larr; Dashboard</a></nav>
    </div>
</header>

<main class="container page-narrow">
    <div class="card">
        <h1>Transaksi Baru</h1>

        <?php if ($errors): ?>
            <div class="notice notice-error" role="alert">
                <ul class="plain-list">
                    <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" class="form form-grid" id="formTransaksi" novalidate data-validate>
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

            <label class="field">
                <span>Nama Customer</span>
                <input type="text" name="nama_customer" value="<?= e($old['nama_customer']) ?>" maxlength="100" required>
            </label>

            <label class="field">
                <span>No HP</span>
                <input type="tel" name="no_hp" value="<?= e($old['no_hp']) ?>" maxlength="20" inputmode="tel" placeholder="08xxxxxxxxxx" required>
            </label>

            <label class="field">
                <span>Jenis Layanan</span>
                <select name="jenis_layanan" id="jenisLayanan" required>
                    <option value="">-- Pilih layanan --</option>
                    <?php foreach (DAFTAR_LAYANAN as $nama => $harga): ?>
                        <option value="<?= e($nama) ?>" data-harga="<?= (int) $harga ?>" <?= $nama === $old['jenis_layanan'] ? 'selected' : '' ?>>
                            <?= e($nama) ?> (<?= rupiah($harga) ?>/Kg)
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label class="field">
                <span>Berat (Kg)</span>
                <input type="number" name="berat_kg" id="beratKg" value="<?= e($old['berat_kg']) ?>" min="0.1" max="999" step="0.1" inputmode="decimal" required>
            </label>

            <div class="total-box field-full">
                <span>Total Harga</span>
                <strong id="totalHarga">Rp 0</strong>
            </div>

            <div class="field-full form-actions">
                <a href="dashboard.php" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan &amp; Cetak Resi</button>
            </div>
        </form>
    </div>
</main>

<script src="../assets/js/script.js"></script>
</body>
</html>
