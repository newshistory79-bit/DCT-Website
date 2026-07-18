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
    ['label' => 'Dashboard',    'url' => 'admin/index.php',              'enabled' => true,  'roles' => ['Admin', 'Editor', 'Staff'], 'icon' => 'dashboard',  'group' => 'Dashboard',   'title' => 'Dashboard',                         'description' => 'ภาพรวมสถิติและกิจกรรมล่าสุดของระบบ'],
    ['label' => 'News',         'url' => 'admin/news/index.php',         'enabled' => true,  'roles' => ['Admin', 'Editor', 'Staff'], 'icon' => 'news',       'group' => 'Content',     'title' => 'จัดการข่าว',                         'description' => 'จัดการข่าวประชาสัมพันธ์ของหน่วยงาน'],
    ['label' => 'Legislation',  'url' => 'admin/legislation/index.php',  'enabled' => true,  'roles' => ['Admin', 'Editor', 'Staff'], 'icon' => 'news',       'group' => 'Content',     'title' => 'จัดการกฎหมาย/ระเบียบ',                'description' => 'จัดการกฎหมายและระเบียบที่เกี่ยวข้อง'],
    ['label' => 'Activities',   'url' => 'admin/activities/index.php',   'enabled' => true,  'roles' => ['Admin', 'Editor', 'Staff'], 'icon' => 'activity',   'group' => 'Content',     'title' => 'จัดการกิจกรรม',                      'description' => 'จัดการกิจกรรมและโครงการของหน่วยงาน'],
    ['label' => 'Gallery',      'url' => 'admin/gallery/index.php',      'enabled' => true,  'roles' => ['Admin', 'Editor', 'Staff'], 'icon' => 'image',      'group' => 'Content',     'title' => 'จัดการคลังภาพ',                      'description' => 'จัดการรูปภาพกิจกรรมและผลงาน'],
    ['label' => 'Documents',    'url' => 'admin/documents/index.php',    'enabled' => true,  'roles' => ['Admin', 'Editor', 'Staff'], 'icon' => 'download',   'group' => 'Content',     'title' => 'จัดการเอกสาร',                       'description' => 'จัดการเอกสารและไฟล์ดาวน์โหลด'],
    ['label' => 'Departments',  'url' => 'admin/departments/index.php',  'enabled' => true,  'roles' => ['Admin', 'Editor', 'Staff'], 'icon' => 'department', 'group' => 'Management',  'title' => 'จัดการแผนก',                         'description' => 'จัดการข้อมูลแผนกภายในองค์กร'],
    ['label' => 'Employees',    'url' => 'admin/employees/index.php',    'enabled' => true,  'roles' => ['Admin', 'Editor', 'Staff'], 'icon' => 'employee',   'group' => 'Management',  'title' => 'จัดการพนักงาน',                      'description' => 'จัดการข้อมูลบุคลากรของหน่วยงาน'],
    ['label' => 'Users',        'url' => 'admin/users/index.php',        'enabled' => true,  'roles' => ['Admin'],                    'icon' => 'users',      'group' => 'Management',  'title' => 'จัดการผู้ใช้งาน',                    'description' => 'จัดการบัญชีผู้ใช้งานระบบและสิทธิ์'],
    ['label' => 'Activity Log', 'url' => 'admin/activity-log/index.php', 'enabled' => true,  'roles' => ['Admin'],                    'icon' => 'log',        'group' => 'System',      'title' => 'ประวัติการใช้งานระบบ (Activity Log)', 'description' => 'ตรวจสอบประวัติการใช้งานระบบทั้งหมด'],
    ['label' => 'Settings',     'url' => null,                            'enabled' => false, 'roles' => ['Admin'],                    'icon' => 'settings',   'group' => 'System',      'title' => 'ตั้งค่าระบบ',                        'description' => 'ตั้งค่าระบบ (ยังไม่เปิดใช้งาน)'],
];
