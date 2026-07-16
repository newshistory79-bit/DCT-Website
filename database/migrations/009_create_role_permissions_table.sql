-- Migration: 009_create_role_permissions_table.sql
-- Purpose: สร้างตาราง role_permissions เก็บสิทธิ์ต่อ Role/Module/Action ในฐานข้อมูล (Phase 10)
--          แทนที่ app/config/permissions.php เป็น Source of Truth หลัก (ไฟล์เดิมยังคงเก็บไว้เป็น Fallback)
-- Status : อนุมัติแล้ว แต่ห้าม Execute จนกว่าจะได้รับคำสั่งอนุมัติแยกอีกครั้ง

CREATE TABLE `role_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `role` enum('Admin','Editor','Staff') NOT NULL COMMENT 'Role ที่ได้รับสิทธิ์',
  `module` varchar(50) NOT NULL COMMENT 'ชื่อโมดูล เช่น departments, employees, news, users',
  `action` varchar(20) NOT NULL COMMENT 'สิทธิ์การกระทำ เช่น view, create, edit, delete',
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_permissions_unique` (`role`, `module`, `action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='ตารางสิทธิ์ต่อ Role/Module/Action แทนที่ app/config/permissions.php';
