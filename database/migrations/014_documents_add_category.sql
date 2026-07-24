-- Migration: 014_documents_add_category.sql
-- Purpose: เพิ่มประเภทเอกสาร (category) ให้ Documents Module รองรับ Filter บน Public Website
--          ใช้ VARCHAR แทน ENUM โดยเจตนา — Validate ค่าที่อนุญาตและ Mapping Label ทำในระดับ PHP
--          ผ่าน DocumentModel::CATEGORIES แทน เพื่อให้เพิ่มประเภทใหม่ในอนาคตได้โดยไม่ต้อง Migration/Deploy ซ้ำ
-- Status : อนุมัติแล้วโดยผู้ใช้ - Executed (Backup ก่อน Execute ไว้ที่ database/backups/documents_backup_20260724_pre_add_category.sql)

ALTER TABLE `documents`
  ADD COLUMN `category` VARCHAR(30) NOT NULL DEFAULT 'law'
    COMMENT 'ประเภทเอกสาร ตรวจสอบ/แปล Label ผ่าน DocumentModel::CATEGORIES เท่านั้น ไม่ผูก Constraint ระดับ DB'
    AFTER `description`;
