<?php

declare(strict_types=1);

// Permission Matrix ต่อ Role/Module/Action
// ตั้งแต่ Phase 10 เป็นต้นไป: ไฟล์นี้เป็นเพียง FALLBACK เท่านั้น
// Source of Truth หลักคือตาราง `role_permissions` ในฐานข้อมูล (อ่านผ่าน App\Core\Permission)
// ไฟล์นี้จะถูกใช้งานเฉพาะเมื่อ Query ตาราง role_permissions ล้มเหลว หรือยังไม่มีข้อมูลในตารางเท่านั้น
// ต้องปรับข้อมูลในไฟล์นี้ให้ตรงกับ role_permissions เสมอ เพื่อไม่ให้พฤติกรรมสิทธิ์เปลี่ยนไปตอน Fallback ทำงาน
return [
    'Admin' => [
        'departments'  => ['view', 'create', 'edit', 'delete'],
        'employees'    => ['view', 'create', 'edit', 'delete'],
        'news'         => ['view', 'create', 'edit', 'delete'],
        'documents'    => ['view', 'create', 'edit', 'delete'],
        'activities'   => ['view', 'create', 'edit', 'delete'],
        'users'        => ['view', 'create', 'edit', 'delete'],
        'activity_log' => ['view'],
    ],
    'Editor' => [
        'departments'  => ['view', 'create', 'edit'],
        'employees'    => ['view', 'create', 'edit'],
        'news'         => ['view', 'create', 'edit'],
        'documents'    => ['view', 'create', 'edit'],
        'activities'   => ['view', 'create', 'edit'],
    ],
    'Staff' => [
        'departments'  => ['view'],
        'employees'    => ['view'],
        'news'         => ['view'],
        'documents'    => ['view'],
        'activities'   => ['view'],
    ],
];
