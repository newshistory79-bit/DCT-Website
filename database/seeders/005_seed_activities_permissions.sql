-- Seeder: 005_seed_activities_permissions.sql
-- Purpose: กำหนดสิทธิ์เริ่มต้นของโมดูล activities ให้ตรงกับ Pattern ของ gallery ทุกประการ (Phase 13)
-- Status : อนุมัติแล้ว โดยผู้ใช้ผ่าน Plan Mode
-- ต้องรันไฟล์ 011_create_activities_table.sql ก่อนเสมอ

INSERT INTO `role_permissions` (`role`, `module`, `action`) VALUES
  ('Admin',  'activities', 'view'),
  ('Admin',  'activities', 'create'),
  ('Admin',  'activities', 'edit'),
  ('Admin',  'activities', 'delete'),
  ('Editor', 'activities', 'view'),
  ('Editor', 'activities', 'create'),
  ('Editor', 'activities', 'edit'),
  ('Staff',  'activities', 'view');
