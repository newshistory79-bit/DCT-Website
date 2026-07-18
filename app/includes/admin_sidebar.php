<?php

declare(strict_types=1);

$currentRole = $_SESSION['role'] ?? '';

// รายการเมนู Sidebar ทั้งหมด — ย้ายไปเป็น Single Source of Truth ที่ app/config/admin_menu.php แล้ว
// (ใช้ร่วมกับ admin_header.php สำหรับ Breadcrumb) ค่า/Key เดิมทุกตัวไม่เปลี่ยน (label/url/enabled/roles/icon/group)
$menuItems = require APP_PATH . '/config/admin_menu.php';

$currentPath = (string) parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH);

// ไฮไลท์เมนูปัจจุบัน: โมดูลย่อย (มี Subfolder เช่น admin/departments/) ให้ Active ทุกหน้าในโฟลเดอร์เดียวกัน (index/form/delete)
// ส่วนเมนูระดับบนสุด (เช่น admin/index.php) ต้องตรงไฟล์เป๊ะเท่านั้น กัน Dashboard Active ค้างทุกหน้า
$isMenuActive = static function (?string $url) use ($currentPath): bool {
    if ($url === null) {
        return false;
    }

    $urlDir = dirname($url);

    if ($urlDir === 'admin') {
        return str_ends_with($currentPath, '/' . $url);
    }

    return str_contains($currentPath, '/' . $urlDir . '/');
};

$visibleMenuItems = array_values(array_filter(
    $menuItems,
    static fn (array $menuItem): bool => in_array($currentRole, $menuItem['roles'], true)
));

$lastGroup = null;
?>
<aside class="admin-sidebar" id="adminSidebar">
    <nav>
        <ul>
            <?php foreach ($visibleMenuItems as $menuItem): ?>
                <?php if ($menuItem['group'] !== $lastGroup): $lastGroup = $menuItem['group']; ?>
                    <li class="menu-group-label"><span><?= e($menuItem['group']) ?></span></li>
                <?php endif; ?>
                <li class="<?= $menuItem['enabled'] ? '' : 'disabled' ?>">
                    <?php if ($menuItem['enabled']): ?>
                        <a href="<?= e(baseUrl($menuItem['url'])) ?>"<?= $isMenuActive($menuItem['url']) ? ' class="active"' : '' ?> title="<?= e($menuItem['label']) ?>">
                            <span class="menu-icon"><?= icon($menuItem['icon'], 18) ?></span>
                            <span class="menu-label"><?= e($menuItem['label']) ?></span>
                        </a>
                    <?php else: ?>
                        <span title="โมดูลนี้ยังไม่เปิดใช้งาน">
                            <span class="menu-icon"><?= icon($menuItem['icon'], 18) ?></span>
                            <span class="menu-label"><?= e($menuItem['label']) ?></span>
                        </span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
            <li class="menu-logout">
                <a href="<?= e(baseUrl('admin/logout.php')) ?>" title="Logout">
                    <span class="menu-icon"><?= icon('logout', 18) ?></span>
                    <span class="menu-label">Logout</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>
