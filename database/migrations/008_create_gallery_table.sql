-- Migration: 008_create_gallery_table.sql
-- Purpose: สร้างตาราง gallery รองรับ Gallery Module (Phase 9) - Single Table, 1 รูป = 1 รายการ
-- Status : อนุมัติแล้ว แต่ห้าม Execute จนกว่าจะได้รับคำสั่งอนุมัติแยกอีกครั้ง

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `title` varchar(255) NOT NULL COMMENT 'ชื่อภาพ/ชุดกิจกรรม',
  `description` text DEFAULT NULL COMMENT 'คำอธิบายภาพ',
  `image` varchar(255) NOT NULL COMMENT 'ชื่อไฟล์ภาพที่จัดเก็บจริง (สุ่มด้วย UploadHelper)',
  `status` enum('Draft','Published') NOT NULL DEFAULT 'Draft' COMMENT 'สถานะการเผยแพร่',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างข้อมูล',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันที่แก้ไขล่าสุด',
  `deleted_at` datetime DEFAULT NULL COMMENT 'Soft Delete Timestamp',
  PRIMARY KEY (`id`),
  KEY `idx_gallery_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='ตารางคลังภาพกิจกรรม';
