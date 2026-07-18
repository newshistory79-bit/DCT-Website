<?php

declare(strict_types=1);

$currentRole = $_SESSION['role'] ?? '';

// รายการเมนู Sidebar ทั้งหมดตาม MasterPrompt
// enabled=false คือโมดูลที่ยังไม่พัฒนา (แสดงแบบ Disabled กดไม่ได้)
// roles คือ Role ที่มองเห็นเมนูนี้ (Users/Activity Log/Settings เห็นเฉพาะ Admin)
// icon คือ Key ของ icon() Helper (app/helpers/icons.php) ใช้แสดงไอคอนหน้าชื่อเมนู
// group คือหมวดสำหรับแสดงเป็น Section Header ใน Sidebar (จัดกลุ่มการแสดงผลเท่านั้น ไม่กระทบ Permission)
$menuItems = [
    ['label' => 'Dashboard',    'url' => 'admin/index.php',              'enabled' => true,  'roles' => ['Admin', 'Editor', 'Staff'], 'icon' => 'dashboard',  'group' => 'Dashboard'],
    ['label' => 'News',         'url' => 'admin/news/index.php',         'enabled' => true,  'roles' => ['Admin', 'Editor', 'Staff'], 'icon' => 'news',       'group' => 'Content'],
    ['label' => 'Legislation',  'url' => 'admin/legislation/index.php',  'enabled' => true,  'roles' => ['Admin', 'Editor', 'Staff'], 'icon' => 'news',       'group' => 'Content'],
    ['label' => 'Activities',   'url' => 'admin/activities/index.php',   'enabled' => true,  'roles' => ['Admin', 'Editor', 'Staff'], 'icon' => 'activity',   'group' => 'Content'],
    ['label' => 'Gallery',      'url' => 'admin/gallery/index.php',      'enabled' => true,  'roles' => ['Admin', 'Editor', 'Staff'], 'icon' => 'image',      'group' => 'Content'],
    ['label' => 'Documents',    'url' => 'admin/documents/index.php',    'enabled' => true,  'roles' => ['Admin', 'Editor', 'Staff'], 'icon' => 'download',   'group' => 'Content'],
    ['label' => 'Departments',  'url' => 'admin/departments/index.php',  'enabled' => true,  'roles' => ['Admin', 'Editor', 'Staff'], 'icon' => 'department', 'group' => 'Management'],
    ['label' => 'Employees',   'url' => 'admin/employees/index.php',    'enabled' => true,  'roles' => ['Admin', 'Editor', 'Staff'], 'icon' => 'employee',   'group' => 'Management'],
    ['label' => 'Users',        'url' => 'admin/users/index.php',        'enabled' => true,  'roles' => ['Admin'], 'icon' => 'users',      'group' => 'Management'],
    ['label' => 'Activity Log', 'url' => 'admin/activity-log/index.php', 'enabled' => true, 'roles' => ['Admin'], 'icon' => 'log',        'group' => 'System'],
    ['label' => 'Settings',     'url' => null,              'enabled' => false, 'roles' => ['Admin'], 'icon' => 'settings', 'group' => 'System'],
];

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
