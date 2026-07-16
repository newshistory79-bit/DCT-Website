-- Migration: 006_legislation_add_fields_and_soft_delete.sql
-- Purpose: เพิ่มคอลัมน์ที่จำเป็นสำหรับ Legislation Module (Phase 7)
--          ไม่รวมไฟล์เอกสารแนบ (จะดำเนินการใน Phase 8: Documents Module)
-- Status : อนุมัติแนวทางแล้ว แต่ห้าม Execute จนกว่าจะได้รับคำสั่งอนุมัติแยกอีกครั้ง
-- หมายเหตุ: ไม่แตะ/ไม่เปลี่ยนคอลัมน์เดิมของ legislation แม้แต่คอลัมน์เดียว (ID, title คงเดิมทั้งหมด)

ALTER TABLE `legislation`
  ADD COLUMN `document_number` VARCHAR(50) NULL DEFAULT NULL COMMENT 'เลขที่ประกาศ/ระเบียบ',
  ADD COLUMN `detail` TEXT NULL DEFAULT NULL COMMENT 'เนื้อหา/รายละเอียดของกฎหมายหรือระเบียบ',
  ADD COLUMN `effective_date` DATE NULL DEFAULT NULL COMMENT 'วันที่มีผลบังคับใช้',
  ADD COLUMN `status` ENUM('Draft','Published') NOT NULL DEFAULT 'Draft' COMMENT 'สถานะการเผยแพร่',
  ADD COLUMN `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'วันที่สร้างข้อมูล',
  ADD COLUMN `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'วันที่แก้ไขล่าสุด',
  ADD COLUMN `deleted_at` DATETIME NULL DEFAULT NULL COMMENT 'Soft Delete Timestamp',
  ADD INDEX `idx_legislation_status` (`status`);
