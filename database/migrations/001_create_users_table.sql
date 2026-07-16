-- Migration: 001_create_users_table.sql
-- Purpose: สร้างตาราง users รองรับระบบ Authentication (Phase 2)
--          ออกแบบให้รองรับการขยายระบบ Roles/Permissions เต็มรูปแบบใน Phase 10 โดยไม่ต้องแก้ schema หลัก
--          รองรับ Soft Delete ผ่าน deleted_at เพื่อให้ลบ/กู้คืนบัญชีผู้ใช้ได้โดยไม่สูญเสียประวัติ (Activity Log อ้างอิงย้อนหลังได้)
-- Status : รอการอนุมัติก่อน Execute (ยังไม่รันเข้าฐานข้อมูล)

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `username` varchar(50) NOT NULL COMMENT 'ชื่อผู้ใช้สำหรับ Login ต้องไม่ซ้ำ',
  `password` varchar(255) NOT NULL COMMENT 'เก็บเฉพาะ Hash จาก password_hash() ห้ามเก็บ Plain Text',
  `full_name` varchar(255) NOT NULL COMMENT 'ชื่อ-นามสกุลสำหรับแสดงผล',
  `email` varchar(100) DEFAULT NULL COMMENT 'อีเมลผู้ใช้ ต้องไม่ซ้ำหากระบุ',
  `role` enum('Admin','Editor','Staff') NOT NULL DEFAULT 'Staff' COMMENT 'สิทธิ์การใช้งานระบบ',
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active' COMMENT 'สถานะบัญชีผู้ใช้',
  `first_login` boolean NOT NULL DEFAULT TRUE COMMENT 'TRUE = ยังไม่เคยเปลี่ยนรหัสผ่าน บังคับเปลี่ยนตอน Login ครั้งแรก',
  `last_login_at` datetime DEFAULT NULL COMMENT 'เวลาที่เข้าสู่ระบบล่าสุด',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างบัญชี',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันที่แก้ไขล่าสุด',
  `deleted_at` datetime NULL DEFAULT NULL COMMENT 'Soft Delete Timestamp',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_status_index` (`role`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='ตารางผู้ใช้งานระบบสำหรับ Authentication และ Authorization';
