<?php

declare(strict_types=1);

/** @var string $csrfToken */
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>ເຂົ້າສູ່ລະບົບ - <?= e(APP_NAME) ?></title>
<link rel="stylesheet" href="<?= e(baseUrl('assets/css/auth.css')) ?>">
</head>
<body>
<main class="login-box">
    <img src="<?= e(baseUrl('assets/images/logo.jpg')) ?>" alt="<?= e(APP_NAME) ?>" class="login-mark">
    <h1><?= e(APP_NAME) ?></h1>
    <h2>ເຂົ້າສູ່ລະບົບຜູ້ດູແລລະບົບ</h2>

    <?php if (!empty($_SESSION['login_error'])): ?>
        <p class="error"><?= e($_SESSION['login_error']) ?></p>
        <?php unset($_SESSION['login_error']); ?>
    <?php endif; ?>

    <form method="post" action="<?= e(baseUrl('admin/login.php')) ?>">
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

        <label for="username">ຊື່ຜູ້ໃຊ້</label>
        <input type="text" id="username" name="username" required autofocus>

        <label for="password">ລະຫັດຜ່ານ</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">ເຂົ້າສູ່ລະບົບ</button>
    </form>
</main>
</body>
</html>
