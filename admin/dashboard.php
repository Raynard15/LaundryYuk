<?php
require __DIR__ . '/../config/database.php';
wajib_login();

$q = trim($_GET['q'] ?? '');
$filterStatus = $_GET['status'] ?? '';
if ($filterStatus !== '' && !in_array($filterStatus, DAFTAR_STATUS, true)) {
    $filterStatus = '';
}

/* Ringkasan jumlah per status */
$ringkasan = array_fill_keys(DAFTAR_STATUS, 0);
$res = $conn->query('SELECT status_cucian, COUNT(*) AS jml FROM transaksi GROUP BY status_cucian');
while ($r = $res->fetch_assoc()) {
    $ringkasan[$r['status_cucian']] = (int) $r['jml'];
}
$totalTransaksi = array_sum($ringkasan);

/* Daftar transaksi (dengan pencarian & filter) */
$sql = 'SELECT * FROM transaksi WHERE 1=1';
$tipe = '';
$param = [];
if ($q !== '') {
    $sql .= ' AND (no_resi LIKE ? OR nama_customer LIKE ? OR no_hp LIKE ?)';
    $like = '%' . $q . '%';
    $tipe .= 'sss';
    array_push($param, $like, $like, $like);
}
if ($filterStatus !== '') {
    $sql .= ' AND status_cucian = ?';
    $tipe .= 's';
    $param[] = $filterStatus;
}
$sql .= ' ORDER BY id DESC';

$stmt = $conn->prepare($sql);
if ($param) {
    $stmt->bind_param($tipe, ...$param);
}
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$flash = ambil_flash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Admin - LaundryKu</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<header class="navbar">
    <div class="container navbar-inner">
        <a href="dashboard.php" class="brand">Laundry<span>Yuk</span> <small class="tag">Admin</small></a>
        <nav class="nav-actions">
            <span class="muted small hide-mobile">Halo, <?= e($_SESSION['admin_username']) ?></span>
            <a href="logout.php" class="btn btn-outline btn-sm">Keluar</a>
        </nav>
    </div>
</header>

<main class="container">
    <div class="page-head">
        <h1>Dashboard</h1>
        <a href="tambah-transaksi.php" class="btn btn-primary">+ Transaksi Baru</a>
    </div>

    <?php if ($flash): ?>
        <p class="notice notice-<?= e($flash['tipe']) ?>" role="status"><?= e($flash['pesan']) ?></p>
    <?php endif; ?>

    <section class="grid grid-stats">
        <div class="card stat"><span class="stat-num"><?= $totalTransaksi ?></span><span class="stat-label">Total Transaksi</span></div>
        <?php foreach (DAFTAR_STATUS as $s): ?>
            <a class="card stat stat-link" href="?status=<?= urlencode($s) ?>">
                <span class="stat-num"><?= $ringkasan[$s] ?></span>
                <span class="stat-label"><?= e($s) ?></span>
            </a>
        <?php endforeach; ?>
    </section>

    <form method="get" class="filter-bar">
        <input type="search" name="q" value="<?= e($q) ?>" placeholder="Cari resi / nama / no HP" aria-label="Cari">
        <select name="status" aria-label="Filter status">
            <option value="">Semua Status</option>
            <?php foreach (DAFTAR_STATUS as $s): ?>
                <option value="<?= e($s) ?>" <?= $s === $filterStatus ? 'selected' : '' ?>><?= e($s) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary">Cari</button>
        <?php if ($q !== '' || $filterStatus !== ''): ?>
            <a href="dashboard.php" class="btn btn-outline">Reset</a>
        <?php endif; ?>
    </form>

    <div class="table-wrap">
        <table class="table">
            <thead>
            <tr>
                <th>No Resi</th>
                <th>Customer</th>
                <th>Layanan</th>
                <th class="num">Berat</th>
                <th class="num">Total</th>
                <th>Masuk</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!$rows): ?>
                <tr><td colspan="8" class="empty">Belum ada data transaksi.</td></tr>
            <?php endif; ?>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td data-label="No Resi"><strong><?= e($r['no_resi']) ?></strong></td>
                    <td data-label="Customer"><?= e($r['nama_customer']) ?><br><small class="muted"><?= e($r['no_hp']) ?></small></td>
                    <td data-label="Layanan"><?= e($r['jenis_layanan']) ?></td>
                    <td data-label="Berat" class="num"><?= e(rtrim(rtrim(number_format((float) $r['berat_kg'], 2, ',', '.'), '0'), ',')) ?> Kg</td>
                    <td data-label="Total" class="num"><?= rupiah($r['total_harga']) ?></td>
                    <td data-label="Masuk"><?= e(tgl($r['tanggal_masuk'])) ?></td>
                    <td data-label="Status">
                        <form method="post" action="update-status.php" class="inline-form">
                            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                            <select name="status" class="status-select badge-<?= e(status_slug($r['status_cucian'])) ?>" data-autosubmit aria-label="Ubah status">
                                <?php foreach (DAFTAR_STATUS as $s): ?>
                                    <option value="<?= e($s) ?>" <?= $s === $r['status_cucian'] ? 'selected' : '' ?>><?= e($s) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <noscript><button class="btn btn-sm">Simpan</button></noscript>
                        </form>
                    </td>
                    <td data-label="Aksi">
                        <a href="cetak-resi.php?id=<?= (int) $r['id'] ?>" target="_blank" rel="noopener" class="btn btn-outline btn-sm">Cetak</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<script src="../assets/js/script.js"></script>
</body>
</html>
