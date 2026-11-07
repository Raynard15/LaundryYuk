<?php
require __DIR__ . '/config/database.php';

$resi = strtoupper(trim($_GET['resi'] ?? ''));
if ($resi === '') {
    header('Location: index.php');
    exit;
}

$stmt = $conn->prepare('SELECT * FROM transaksi WHERE no_resi = ? LIMIT 1');
$stmt->bind_param('s', $resi);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$data) {
    header('Location: index.php?error=1');
    exit;
}

$indexStatus = array_search($data['status_cucian'], DAFTAR_STATUS, true);
if ($indexStatus === false) {
    $indexStatus = 0;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resi <?= e($data['no_resi']) ?> - LaundryKu</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="navbar">
    <div class="container navbar-inner">
        <a href="index.php" class="brand">🧺 Laundry<span>Ku</span></a>
        <nav><a href="index.php" class="nav-link">&larr; Cek Resi Lain</a></nav>
    </div>
</header>

<main class="container page-narrow">
    <div class="card">
        <div class="resi-head">
            <div>
                <p class="muted">Nomor Resi</p>
                <h1 class="resi-no"><?= e($data['no_resi']) ?></h1>
            </div>
            <span class="badge badge-<?= e(status_slug($data['status_cucian'])) ?>"><?= e($data['status_cucian']) ?></span>
        </div>

        <ol class="tracker" aria-label="Status pengerjaan">
            <?php foreach (DAFTAR_STATUS as $i => $s):
                $kelas = $i < $indexStatus ? 'done' : ($i === $indexStatus ? 'active' : '');
            ?>
                <li class="tracker-item <?= $kelas ?>">
                    <span class="tracker-dot"><?= $i < $indexStatus ? '✓' : $i + 1 ?></span>
                    <span class="tracker-label"><?= e($s) ?></span>
                </li>
            <?php endforeach; ?>
        </ol>

        <dl class="detail-grid">
            <div><dt>Nama Customer</dt><dd><?= e($data['nama_customer']) ?></dd></div>
            <div><dt>Jenis Layanan</dt><dd><?= e($data['jenis_layanan']) ?></dd></div>
            <div><dt>Berat</dt><dd><?= e(rtrim(rtrim(number_format((float) $data['berat_kg'], 2, ',', '.'), '0'), ',')) ?> Kg</dd></div>
            <div><dt>Total Harga</dt><dd><strong><?= rupiah($data['total_harga']) ?></strong></dd></div>
            <div><dt>Tanggal Masuk</dt><dd><?= e(tgl($data['tanggal_masuk'])) ?></dd></div>
        </dl>

        <?php if ($data['status_cucian'] === 'Siap Diambil'): ?>
            <p class="notice notice-success">Cucian Anda sudah siap! Silakan ambil di outlet dengan membawa struk.</p>
        <?php elseif ($data['status_cucian'] === 'Selesai'): ?>
            <p class="notice notice-info">Transaksi ini sudah selesai. Terima kasih telah menggunakan layanan kami.</p>
        <?php endif; ?>
    </div>
</main>

<footer class="footer">
    <div class="container">&copy; <?= date('Y') ?> LaundryKu. Semua hak dilindungi.</div>
</footer>
</body>
</html>
