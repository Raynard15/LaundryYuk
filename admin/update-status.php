<?php
require __DIR__ . '/../config/database.php';
wajib_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$id     = (int) ($_POST['id'] ?? 0);
$status = $_POST['status'] ?? '';

if (!csrf_valid($_POST['csrf'] ?? null)) {
    set_flash('error', 'Sesi tidak valid, silakan coba lagi.');
} elseif ($id <= 0 || !in_array($status, DAFTAR_STATUS, true)) {
    set_flash('error', 'Data status tidak valid.');
} else {
    $stmt = $conn->prepare('UPDATE transaksi SET status_cucian = ? WHERE id = ?');
    $stmt->bind_param('si', $status, $id);
    $stmt->execute();
    $stmt->close();
    set_flash('success', 'Status cucian diperbarui menjadi "' . $status . '".');
}

header('Location: dashboard.php');
exit;
