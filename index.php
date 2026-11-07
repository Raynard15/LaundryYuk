<?php
require __DIR__ . '/config/database.php';
$error = isset($_GET['error']) ? 'Nomor resi tidak ditemukan. Periksa kembali nomor pada struk Anda.' : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cek Resi Laundry</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="navbar">
    <div class="container navbar-inner">
        <a href="index.php" class="brand">Laundry<span>Yuk</span></a>
        <nav><a href="admin/login.php" class="nav-link">Login Admin</a></nav>
    </div>
</header>

<main>
    <section class="hero">
        <div class="container hero-inner">
            <h1>Pantau cucian Anda,<br>kapan saja &amp; di mana saja.</h1>
            <p>Masukkan nomor resi dari struk untuk melihat status cucian secara langsung.</p>

            <form class="search-form" action="detail-resi.php" method="get" id="formCariResi" novalidate>
                <input type="text" name="resi" id="inputResi" placeholder="Contoh: RSI-A1B2C3"
                       maxlength="20" autocomplete="off" required aria-label="Nomor resi">
                <button type="submit" class="btn btn-primary">Lacak</button>
            </form>
            <p class="form-error" id="pesanResi" role="alert"><?= e($error) ?></p>
        </div>
    </section>

    <section class="container">
        <h2 class="section-title">Layanan Kami</h2>
        <div class="grid grid-4">
            <?php foreach (DAFTAR_LAYANAN as $nama => $harga): ?>
                <article class="card service-card">
                    <h3><?= e($nama) ?></h3>
                    <p class="price"><?= rupiah($harga) ?><small> / Kg</small></p>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="container">
        <h2 class="section-title">Cara Kerja</h2>
        <div class="grid grid-3">
            <article class="card step-card"><span class="step-no">1</span><h3>Antar Cucian</h3><p>Serahkan cucian ke outlet, Anda akan menerima struk berisi nomor resi.</p></article>
            <article class="card step-card"><span class="step-no">2</span><h3>Lacak Status</h3><p>Masukkan nomor resi di halaman ini untuk melihat progres pengerjaan.</p></article>
            <article class="card step-card"><span class="step-no">3</span><h3>Ambil Cucian</h3><p>Datang saat status <strong>Siap Diambil</strong> dan tunjukkan struk Anda.</p></article>
        </div>
    </section>
</main>

<footer class="footer">
    <div class="container">&copy; <?= date('Y') ?> LaundryKu.</div>
</footer>

<script src="assets/js/script.js"></script>
</body>
</html>
