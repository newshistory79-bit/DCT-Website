<?php

declare(strict_types=1);

// รายชื่อ Role ทั้งหมดในระบบ ต้องตรงกับ ENUM ของคอลัมน์ users.role เสมอ
// หมายเหตุ: ไฟล์นี้เป็นข้อมูลอ้างอิงเตรียมไว้สำหรับ Phase 10 (Users, Roles, Permissions)
// ยังไม่ถูกเรียกใช้งานใน AuthMiddleware/AuthController ปัจจุบัน เพื่อไม่ให้กระทบระบบ Login ที่ทำงานอยู่
return [
    'Admin',
    'Editor',
    'Staff',
];
