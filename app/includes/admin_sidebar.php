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
    ['label' => 'Documents',    'url' => null,              'enabled' => false, 'roles' => ['Admin', 'Editor', 'Staff']],
    ['label' => 'Gallery',      'url' => null,              'enabled' => false, 'roles' => ['Admin', 'Editor', 'Staff']],
    ['label' => 'Users',        'url' => null,              'enabled' => false, 'roles' => ['Admin']],
    ['label' => 'Activity Log', 'url' => null,              'enabled' => false, 'roles' => ['Admin']],
    ['label' => 'Settings',     'url' => null,              'enabled' => false, 'roles' => ['Admin']],
];
?>
<aside class="admin-sidebar" id="adminSidebar">
    <nav>
        <ul>
            <?php foreach ($menuItems as $item): ?>
                <?php if (!in_array($currentRole, $item['roles'], true)) { continue; } ?>
                <li class="<?= $item['enabled'] ? '' : 'disabled' ?>">
                    <?php if ($item['enabled']): ?>
                        <a href="<?= e(baseUrl($item['url'])) ?>"><?= e($item['label']) ?></a>
                    <?php else: ?>
                        <span title="โมดูลนี้ยังไม่เปิดใช้งาน"><?= e($item['label']) ?></span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
            <li class="menu-logout">
                <a href="<?= e(baseUrl('admin/logout.php')) ?>">Logout</a>
            </li>
        </ul>
    </nav>
</aside>
