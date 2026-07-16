-- Migration: 002_create_departments_table.sql
-- Purpose: สร้างตาราง departments รองรับ Departments Module (Phase 4)
-- Status : อนุมัติ Schema แล้ว แต่ห้าม Execute จนกว่าจะได้รับคำสั่งอนุมัติแยกอีกครั้ง

CREATE TABLE `departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `code` varchar(20) NOT NULL COMMENT 'รหัสแผนก',
  `name` varchar(255) NOT NULL COMMENT 'ชื่อแผนก ต้องไม่ซ้ำ',
  `description` text DEFAULT NULL COMMENT 'คำอธิบายแผนก',
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active' COMMENT 'สถานะการใช้งานของแผนก',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT 'ลำดับการแสดงผล',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างข้อมูล',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันที่แก้ไขล่าสุด',
  `deleted_at` datetime NULL DEFAULT NULL COMMENT 'Soft Delete Timestamp',
  PRIMARY KEY (`id`),
  UNIQUE KEY `departments_code_unique` (`code`),
  UNIQUE KEY `departments_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='ตารางแผนก/หน่วยงานภายในองค์กร';
