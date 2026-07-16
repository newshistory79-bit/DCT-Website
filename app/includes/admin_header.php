<?php

declare(strict_types=1);
?>
<header class="admin-topbar">
    <button type="button" id="sidebarToggle" class="sidebar-toggle" aria-label="เปิด/ปิดเมนู">&#9776;</button>

    <div class="brand">
        <span class="brand-logo">DTC</span>
        <span class="brand-name"><?= e(APP_NAME) ?></span>
    </div>

    <div class="user-menu">
        <span class="user-name"><?= e($_SESSION['full_name'] ?? '') ?></span>
        <span class="user-role"><?= e($_SESSION['role'] ?? '') ?></span>
        <a href="<?= e(baseUrl('admin/logout.php')) ?>" class="logout-link">ออกจากระบบ</a>
    </div>
</header>
