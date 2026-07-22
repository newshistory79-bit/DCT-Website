<?php

declare(strict_types=1);

// Single Source of Truth ของเมนู Admin Panel — ใช้ร่วมกันโดย admin_sidebar.php (Render เมนู + Active Highlight)
// และ admin_header.php (Auto-generate Breadcrumb/Page Header ในอนาคต) ห้ามประกาศ $menuItems ซ้ำที่อื่น
//
// ย้ายมาจาก app/includes/admin_sidebar.php แบบ Value/Key เดิมทุกตัว (label/url/enabled/roles/icon/group)
// ไม่เปลี่ยน Permission/Active Logic ใดๆ — เพิ่มเฉพาะ 'title'/'description' ใหม่ (Additive) สำหรับ
// Page Header/Breadcrumb ในอนาคต (DS2+) โดย 'title' ตรงกับข้อความ <h1> ของแต่ละหน้าที่มีอยู่จริงในปัจจุบัน
// (ยังไม่ถูกใช้ Retrofit View ใดใน DS1 ตามข้อกำหนด)
return [
    ['label' => 'Dashboard',    'url' => 'admin/index.php',              'enabled' => true,  'roles' => ['Admin', 'Editor', 'Staff'], 'icon' => 'dashboard',  'group' => 'Dashboard',   'title' => 'Dashboard',                         'description' => 'ພາບລວມສະຖິຕິ ແລະ ກິດຈະກຳຫລ້າສຸດຂອງລະບົບ'],
    ['label' => 'News',         'url' => 'admin/news/index.php',         'enabled' => true,  'roles' => ['Admin', 'Editor', 'Staff'], 'icon' => 'news',       'group' => 'Content',     'title' => 'ຈັດການຂ່າວສານ',                      'description' => 'ຈັດການຂ່າວສານຂອງພະແນກ'],
    ['label' => 'Activities',   'url' => 'admin/activities/index.php',   'enabled' => true,  'roles' => ['Admin', 'Editor', 'Staff'], 'icon' => 'activity',   'group' => 'Content',     'title' => 'ຈັດການກິດຈະກຳ',                      'description' => 'ຈັດການກິດຈະກຳ ແລະ ໂຄງການຂອງພະແນກ'],
    ['label' => 'Documents',    'url' => 'admin/documents/index.php',    'enabled' => true,  'roles' => ['Admin', 'Editor', 'Staff'], 'icon' => 'download',   'group' => 'Content',     'title' => 'ຈັດການເອກະສານ',                      'description' => 'ຈັດການເອກະສານ ແລະ ໄຟລ໌ດາວໂຫລດ'],
    ['label' => 'Departments',  'url' => 'admin/departments/index.php',  'enabled' => true,  'roles' => ['Admin', 'Editor', 'Staff'], 'icon' => 'department', 'group' => 'Management',  'title' => 'ຈັດການພະແນກ',                        'description' => 'ຈັດການຂໍ້ມູນພະແນກພາຍໃນອົງກອນ'],
    ['label' => 'Employees',    'url' => 'admin/employees/index.php',    'enabled' => true,  'roles' => ['Admin', 'Editor', 'Staff'], 'icon' => 'employee',   'group' => 'Management',  'title' => 'ຈັດການພະນັກງານ',                     'description' => 'ຈັດການຂໍ້ມູນພະນັກງານຂອງພະແນກ'],
    ['label' => 'Users',        'url' => 'admin/users/index.php',        'enabled' => true,  'roles' => ['Admin'],                    'icon' => 'users',      'group' => 'Management',  'title' => 'ຈັດການຜູ້ໃຊ້ງານ',                    'description' => 'ຈັດການບັນຊີຜູ້ໃຊ້ງານລະບົບ ແລະ ສິດທິ'],
    ['label' => 'Activity Log', 'url' => 'admin/activity-log/index.php', 'enabled' => true,  'roles' => ['Admin'],                    'icon' => 'log',        'group' => 'System',      'title' => 'ປະຫວັດການນຳໃຊ້ລະບົບ (Activity Log)', 'description' => 'ກວດສອບປະຫວັດການນຳໃຊ້ລະບົບທັງໝົດ'],
    ['label' => 'Settings',     'url' => null,                            'enabled' => false, 'roles' => ['Admin'],                    'icon' => 'settings',   'group' => 'System',      'title' => 'ຕັ້ງຄ່າລະບົບ',                       'description' => 'ຕັ້ງຄ່າລະບົບ (ຍັງບໍ່ໄດ້ເປີດໃຊ້ງານ)'],
];
