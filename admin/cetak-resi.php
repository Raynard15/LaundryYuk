<?php
require __DIR__ . '/../config/database.php';
wajib_login();

$id = (int) ($_GET['id'] ?? 0);
$stmt = $conn->prepare('SELECT * FROM transaksi WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$d = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$d) {
    set_flash('error', 'Transaksi tidak ditemukan.');
    header('Location: dashboard.php');
    exit;
}

$auto = isset($_GET['auto']);
$skema = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$urlLacak = $skema . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/index.php';
$urlLacak = preg_replace('#(?<!:)//+#', '/', $urlLacak);
$berat = rtrim(rtrim(number_format((float) $d['berat_kg'], 2, ',', '.'), '0'), ',');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk <?= e($d['no_resi']) ?></title>
    <style>
        @page { size: 58mm auto; margin: 0; }
        * { box-sizing: border-box; }
        body { margin: 0; background: #e9edf2; font-family: "Courier New", monospace; color: #000; }
        .toolbar { display: flex; gap: 8px; justify-content: center; padding: 14px; font-family: system-ui, sans-serif; }
        .toolbar a, .toolbar button { padding: 8px 14px; border: 1px solid #1d4ed8; background: #1d4ed8; color: #fff; border-radius: 6px; font-size: 14px; cursor: pointer; text-decoration: none; }
        .toolbar a { background: #fff; color: #1d4ed8; }
        .struk { width: 58mm; margin: 0 auto 24px; background: #fff; padding: 4mm 3mm; font-size: 11px; line-height: 1.45; }
        .center { text-align: center; }
        .judul { font-size: 15px; font-weight: bold; }
        .garis { border: 0; border-top: 1px dashed #000; margin: 6px 0; }
        .baris { display: flex; justify-content: space-between; gap: 6px; }
        .baris span:last-child { text-align: right; }
        .resi { font-size: 16px; font-weight: bold; letter-spacing: 1px; margin: 4px 0; }
        .total { font-size: 13px; font-weight: bold; }
        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            .struk { margin: 0; width: 58mm; }
        }
    </style>
</head>
<body>
<div class="toolbar">
    <button type="button" onclick="window.print()">Cetak</button>
    <a href="dashboard.php">Kembali</a>
</div>

<div class="struk">
    <div class="center">
        <div class="judul">LAUNDRY YUK</div>
        <div>Struk Penerimaan Cucian</div>
    </div>
    <hr class="garis">
    <div class="center">
        <div>No Resi</div>
        <div class="resi"><?= e($d['no_resi']) ?></div>
    </div>
    <hr class="garis">
    <div class="baris"><span>Tanggal</span><span><?= e(tgl($d['tanggal_masuk'])) ?></span></div>
    <div class="baris"><span>Customer</span><span><?= e($d['nama_customer']) ?></span></div>
    <div class="baris"><span>No HP</span><span><?= e($d['no_hp']) ?></span></div>
    <hr class="garis">
    <div class="baris"><span>Layanan</span><span><?= e($d['jenis_layanan']) ?></span></div>
    <div class="baris"><span>Berat</span><span><?= e($berat) ?> Kg</span></div>
    <div class="baris"><span>Status</span><span><?= e($d['status_cucian']) ?></span></div>
    <hr class="garis">
    <div class="baris total"><span>TOTAL</span><span><?= rupiah($d['total_harga']) ?></span></div>
    <hr class="garis">
    <div class="center">
        <div>Lacak cucian Anda di:</div>
        <div><?= e($urlLacak) ?></div>
        <div>Masukkan nomor resi di atas.</div>
        <br>
        <div>Terima kasih atas kepercayaan Anda!</div>
        <div>Cucian yang tidak diambil &gt; 30 hari di luar tanggung jawab kami.</div>
    </div>
</div>

<?php if ($auto): ?>
<script>window.addEventListener('load', function () { setTimeout(window.print, 300); });</script>
<?php endif; ?>
</body>
</html>
