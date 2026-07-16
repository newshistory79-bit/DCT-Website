-- Migration: 005_news_add_status_soft_delete_and_timestamps.sql
-- Purpose: เพิ่ม status (Draft/Published), created_at, updated_at, deleted_at ให้ตาราง news
--          รองรับ Requirement Soft Delete, Status Filter และ Sort by Created Date (Phase 6)
-- Status : อนุมัติแล้ว แต่ห้าม Execute จนกว่าจะได้รับคำสั่งอนุมัติแยกอีกครั้ง
-- หมายเหตุ: ไม่แตะ/ไม่เปลี่ยนคอลัมน์เดิมของ news แม้แต่คอลัมน์เดียว (ID, title, detail, image, activity_date คงเดิมทั้งหมด)

ALTER TABLE `news`
ADD COLUMN `status` ENUM('Draft','Published') NOT NULL DEFAULT 'Published',
ADD COLUMN `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
ADD COLUMN `deleted_at` DATETIME NULL DEFAULT NULL,
ADD INDEX `idx_news_status` (`status`);
