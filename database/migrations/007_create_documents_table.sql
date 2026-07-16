-- Migration: 007_create_documents_table.sql
-- Purpose: สร้างตาราง documents รองรับ Documents Module (Phase 8)
-- Status : อนุมัติแล้ว แต่ห้าม Execute จนกว่าจะได้รับคำสั่งอนุมัติแยกอีกครั้ง

CREATE TABLE `documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `title` varchar(255) NOT NULL COMMENT 'ชื่อเอกสาร',
  `description` text DEFAULT NULL COMMENT 'รายละเอียดเอกสาร',
  `file_name` varchar(255) NOT NULL COMMENT 'ชื่อไฟล์ที่จัดเก็บจริง (สุ่มด้วย UploadHelper)',
  `original_file_name` varchar(255) DEFAULT NULL COMMENT 'ชื่อไฟล์ต้นฉบับตอนอัปโหลด (แสดงตอนดาวน์โหลด)',
  `file_extension` varchar(10) DEFAULT NULL COMMENT 'นามสกุลไฟล์ เช่น pdf, docx, xlsx',
  `file_size` int(11) DEFAULT NULL COMMENT 'ขนาดไฟล์ (ไบต์)',
  `status` enum('Draft','Published') NOT NULL DEFAULT 'Draft' COMMENT 'สถานะการเผยแพร่',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างข้อมูล',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันที่แก้ไขล่าสุด',
  `deleted_at` datetime DEFAULT NULL COMMENT 'Soft Delete Timestamp',
  PRIMARY KEY (`id`),
  KEY `idx_documents_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='ตารางเอกสารดาวน์โหลด';
