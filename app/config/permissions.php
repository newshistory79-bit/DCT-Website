<?php

declare(strict_types=1);

// Permission Matrix ต่อ Role/Module/Action
// เริ่มใช้งานจริงตั้งแต่ Phase 4 (Departments) เพื่อวางโครงรองรับระบบ Permissions เต็มรูปแบบใน Phase 10
// เมื่อถึง Phase 10 ไฟล์นี้จะถูกย้ายไปเก็บในตาราง role_permissions แทน โดย App\Core\Permission
// จะยังคงเป็นจุดเดียวที่ Controller/Middleware/View เรียกใช้ (ไม่ต้องแก้โค้ดที่เรียกใช้งาน)
return [
    'Admin' => [
        'departments'  => ['view', 'create', 'edit', 'delete'],
        'employees'    => ['view', 'create', 'edit', 'delete'],
        'news'         => ['view', 'create', 'edit', 'delete'],
        'legislation'  => ['view', 'create', 'edit', 'delete'],
    ],
    'Editor' => [
        'departments'  => ['view', 'create', 'edit'],
        'employees'    => ['view', 'create', 'edit'],
        'news'         => ['view', 'create', 'edit'],
        'legislation'  => ['view', 'create', 'edit'],
    ],
    'Staff' => [
        'departments'  => ['view'],
        'employees'    => ['view'],
        'news'         => ['view'],
        'legislation'  => ['view'],
    ],
];
