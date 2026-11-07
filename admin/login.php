<?php
require __DIR__ . '/../config/database.php';

if (!empty($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!csrf_valid($_POST['csrf'] ?? null)) {
        $error = 'Sesi tidak valid, silakan muat ulang halaman.';
    } elseif ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        $stmt = $conn->prepare('SELECT id, username, password FROM admin WHERE username = ? LIMIT 1');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $admin = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($admin && password_verify($password, $admin['password'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = (int) $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            header('Location: dashboard.php');
            exit;
        }
        $error = 'Username atau password salah.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin - LaundryYuk</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="login-body">
<main class="login-wrap">
    <div class="card login-card">
        <a href="../index.php" class="brand center">Laundry<span>Yuk</span></a>
        <h1>Login Admin</h1>

        <?php if ($error): ?>
            <p class="notice notice-error" role="alert"><?= e($error) ?></p>
        <?php endif; ?>

        <form method="post" class="form" novalidate data-validate>
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <label class="field">
                <span>Username</span>
                <input type="text" name="username" value="<?= e($_POST['username'] ?? '') ?>" required autofocus autocomplete="username">
            </label>
            <label class="field">
                <span>Password</span>
                <input type="password" name="password" required autocomplete="current-password">
            </label>
            <button type="submit" class="btn btn-primary btn-block">Masuk</button>
        </form>
        <p class="center muted small"><a href="../index.php">&larr; Kembali ke halaman utama</a></p>
    </div>
</main>
<script src="../assets/js/script.js"></script>
</body>
</html>
