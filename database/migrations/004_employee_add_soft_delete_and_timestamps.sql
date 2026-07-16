-- Migration: 004_employee_add_soft_delete_and_timestamps.sql
-- Purpose: เพิ่ม created_at, updated_at, deleted_at ให้ตาราง employee
--          รองรับ Requirement Soft Delete และ Sort by Created Date (Phase 5)
-- Status : อนุมัติแนวทางแล้ว แต่ห้าม Execute จนกว่าจะได้รับคำสั่งอนุมัติแยกอีกครั้ง
-- หมายเหตุ: ไม่แตะ/ไม่เปลี่ยนคอลัมน์เดิมของ employee แม้แต่คอลัมน์เดียว
--          (ID, Fname, Lname, birth_date, gender, phone, email, position, address, image คงเดิมทั้งหมด)

ALTER TABLE `employee`
  ADD COLUMN `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างข้อมูล',
  ADD COLUMN `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันที่แก้ไขล่าสุด',
  ADD COLUMN `deleted_at` datetime NULL DEFAULT NULL COMMENT 'Soft Delete Timestamp';
