-- Phase 11: สร้างตาราง activity_logs สำหรับบันทึกประวัติการใช้งานระบบ (Audit Trail)
-- Insert-only: ไม่มี updated_at / deleted_at เพราะ Log ต้องไม่ถูกแก้ไขหรือลบโดยผู้ใช้ระบบ

CREATE TABLE `activity_logs` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `user_id` INT(11) NULL COMMENT 'อ้างอิงผู้ทำรายการ (ไม่ใส่ FK Constraint ตามมาตรฐานโปรเจกต์) NULL ได้กรณี Login ล้มเหลวก่อนทราบตัวตน',
  `username` VARCHAR(50) NOT NULL COMMENT 'Snapshot ชื่อผู้ใช้ ณ เวลาที่เกิดเหตุการณ์',
  `role` VARCHAR(20) NOT NULL COMMENT 'Snapshot สิทธิ์ ณ เวลาที่เกิดเหตุการณ์',
  `module` VARCHAR(50) NOT NULL COMMENT 'ชื่อโมดูลที่ถูกกระทำ เช่น departments, users, auth',
  `action` VARCHAR(20) NOT NULL COMMENT 'ประเภทการกระทำ เช่น create, update, delete, login, login_failed, logout',
  `description` VARCHAR(255) NOT NULL COMMENT 'ข้อความสรุปเหตุการณ์แบบอ่านง่าย',
  `ip_address` VARCHAR(45) NULL COMMENT 'IP Address ผู้ทำรายการ รองรับทั้ง IPv4/IPv6',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'เวลาที่เกิดเหตุการณ์',
  PRIMARY KEY (`id`),
  KEY `idx_activity_logs_module` (`module`),
  KEY `idx_activity_logs_action` (`action`),
  KEY `idx_activity_logs_user_id` (`user_id`),
  KEY `idx_activity_logs_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
  COMMENT='ตารางบันทึกประวัติการใช้งานระบบ (Audit Trail) - Insert-only ห้ามแก้ไข/ลบ';
