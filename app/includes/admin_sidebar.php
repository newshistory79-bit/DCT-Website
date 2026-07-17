<?php

declare(strict_types=1);

$currentRole = $_SESSION['role'] ?? '';

// รายการเมนู Sidebar ทั้งหมดตาม MasterPrompt
// enabled=false คือโมดูลที่ยังไม่พัฒนา (แสดงแบบ Disabled กดไม่ได้)
// roles คือ Role ที่มองเห็นเมนูนี้ (Users/Activity Log/Settings เห็นเฉพาะ Admin)
$menuItems = [
    ['label' => 'Dashboard',    'url' => 'admin/index.php',              'enabled' => true,  'roles' => ['Admin', 'Editor', 'Staff']],
    ['label' => 'Departments',  'url' => 'admin/departments/index.php',  'enabled' => true,  'roles' => ['Admin', 'Editor', 'Staff']],
    ['label' => 'Employees',   'url' => 'admin/employees/index.php',    'enabled' => true,  'roles' => ['Admin', 'Editor', 'Staff']],
    ['label' => 'News',         'url' => 'admin/news/index.php',         'enabled' => true,  'roles' => ['Admin', 'Editor', 'Staff']],
    ['label' => 'Legislation',  'url' => 'admin/legislation/index.php',  'enabled' => true,  'roles' => ['Admin', 'Editor', 'Staff']],
    ['label' => 'Activities',   'url' => null,              'enabled' => false, 'roles' => ['Admin', 'Editor', 'Staff']],
    ['label' => 'Documents',    'url' => 'admin/documents/index.php',    'enabled' => true,  'roles' => ['Admin', 'Editor', 'Staff']],
    ['label' => 'Gallery',      'url' => 'admin/gallery/index.php',      'enabled' => true,  'roles' => ['Admin', 'Editor', 'Staff']],
    ['label' => 'Users',        'url' => 'admin/users/index.php',        'enabled' => true,  'roles' => ['Admin']],
    ['label' => 'Activity Log', 'url' => 'admin/activity-log/index.php', 'enabled' => true, 'roles' => ['Admin']],
    ['label' => 'Settings',     'url' => null,              'enabled' => false, 'roles' => ['Admin']],
];
?>
<aside class="admin-sidebar" id="adminSidebar">
    <nav>
        <ul>
            <?php foreach ($menuItems as $menuItem): ?>
                <?php if (!in_array($currentRole, $menuItem['roles'], true)) { continue; } ?>
                <li class="<?= $menuItem['enabled'] ? '' : 'disabled' ?>">
                    <?php if ($menuItem['enabled']): ?>
                        <a href="<?= e(baseUrl($menuItem['url'])) ?>"><?= e($menuItem['label']) ?></a>
                    <?php else: ?>
                        <span title="โมดูลนี้ยังไม่เปิดใช้งาน"><?= e($menuItem['label']) ?></span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
            <li class="menu-logout">
                <a href="<?= e(baseUrl('admin/logout.php')) ?>">Logout</a>
            </li>
        </ul>
    </nav>
</aside>
