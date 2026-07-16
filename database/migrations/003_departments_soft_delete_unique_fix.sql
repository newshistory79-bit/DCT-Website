-- Migration: 003_departments_soft_delete_unique_fix.sql
-- Purpose: แก้ปัญหา UNIQUE Constraint ชนกับแถวที่ถูก Soft Delete โดยไม่แก้ไขข้อมูลจริง (code/name คงเดิมเสมอ)
-- Approach: ใช้ Generated Column แบบ STORED (ตามที่อนุมัติ) แทน VIRTUAL เพื่อความเข้ากันได้/เสถียรภาพของ Index บน MariaDB 10.4.x
-- Status : รอการอนุมัติก่อน Execute (ยังไม่รันเข้าฐานข้อมูล)

ALTER TABLE `departments`
  ADD COLUMN `code_active` VARCHAR(20)
      GENERATED ALWAYS AS (IF(`deleted_at` IS NULL, `code`, NULL)) STORED
      COMMENT 'ใช้สำหรับ Unique Index เท่านั้น เป็น NULL อัตโนมัติเมื่อถูก Soft Delete',
  ADD COLUMN `name_active` VARCHAR(255)
      GENERATED ALWAYS AS (IF(`deleted_at` IS NULL, `name`, NULL)) STORED
      COMMENT 'ใช้สำหรับ Unique Index เท่านั้น เป็น NULL อัตโนมัติเมื่อถูก Soft Delete';

ALTER TABLE `departments`
  DROP INDEX `departments_code_unique`,
  DROP INDEX `departments_name_unique`,
  ADD UNIQUE KEY `departments_code_active_unique` (`code_active`),
  ADD UNIQUE KEY `departments_name_active_unique` (`name_active`);
