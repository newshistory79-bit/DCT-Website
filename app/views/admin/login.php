<?php

declare(strict_types=1);

/** @var string $csrfToken */
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>เข้าสู่ระบบ - <?= e(APP_NAME) ?></title>
<link rel="stylesheet" href="<?= e(baseUrl('assets/css/auth.css')) ?>">
</head>
<body>
<main class="login-box">
    <h1><?= e(APP_NAME) ?></h1>
    <h2>เข้าสู่ระบบผู้ดูแลระบบ</h2>

    <?php if (!empty($_SESSION['login_error'])): ?>
        <p class="error"><?= e($_SESSION['login_error']) ?></p>
        <?php unset($_SESSION['login_error']); ?>
    <?php endif; ?>

    <form method="post" action="<?= e(baseUrl('admin/login.php')) ?>">
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

        <label for="username">ชื่อผู้ใช้</label>
        <input type="text" id="username" name="username" required autofocus>

        <label for="password">รหัสผ่าน</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">เข้าสู่ระบบ</button>
    </form>
</main>
</body>
</html>
