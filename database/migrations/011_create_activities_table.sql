-- Migration: 011_create_activities_table.sql
-- Purpose: สร้างตาราง activities รองรับ Activities Module (Phase 13)
-- Status : อนุมัติแล้ว โดยผู้ใช้ผ่าน Plan Mode

CREATE TABLE `activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `title` varchar(255) NOT NULL COMMENT 'หัวข้อกิจกรรม',
  `description` text DEFAULT NULL COMMENT 'รายละเอียดกิจกรรม',
  `activity_date` date NOT NULL COMMENT 'วันที่จัดกิจกรรม',
  `location` varchar(255) DEFAULT NULL COMMENT 'สถานที่จัดกิจกรรม',
  `image` varchar(255) DEFAULT NULL COMMENT 'ชื่อไฟล์ภาพที่จัดเก็บจริง (สุ่มด้วย UploadHelper) - ไม่บังคับ',
  `status` enum('Draft','Published') NOT NULL DEFAULT 'Draft' COMMENT 'สถานะการเผยแพร่',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างข้อมูล',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันที่แก้ไขล่าสุด',
  `deleted_at` datetime DEFAULT NULL COMMENT 'Soft Delete Timestamp',
  PRIMARY KEY (`id`),
  KEY `idx_activities_status` (`status`),
  KEY `idx_activities_activity_date` (`activity_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='ตารางกิจกรรมของหน่วยงาน';
