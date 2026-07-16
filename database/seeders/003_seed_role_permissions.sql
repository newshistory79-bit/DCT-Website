-- Seeder: 003_seed_role_permissions.sql
-- Purpose: กำหนดสิทธิ์เริ่มต้นให้ตรงกับ app/config/permissions.php เดิมทุกประการ (Phase 10)
--          เพิ่มเติมเฉพาะโมดูลใหม่ users (Admin เท่านั้น ตามที่อนุมัติ)
-- Status : อนุมัติแล้ว แต่ห้าม Execute จนกว่าจะได้รับคำสั่งอนุมัติแยกอีกครั้ง
-- ต้องรันไฟล์ 009_create_role_permissions_table.sql ก่อนเสมอ

-- Admin: ทุกสิทธิ์ทุกโมดูล (view, create, edit, delete)
INSERT INTO `role_permissions` (`role`, `module`, `action`) VALUES
  ('Admin', 'departments',  'view'),
  ('Admin', 'departments',  'create'),
  ('Admin', 'departments',  'edit'),
  ('Admin', 'departments',  'delete'),
  ('Admin', 'employees',    'view'),
  ('Admin', 'employees',    'create'),
  ('Admin', 'employees',    'edit'),
  ('Admin', 'employees',    'delete'),
  ('Admin', 'news',         'view'),
  ('Admin', 'news',         'create'),
  ('Admin', 'news',         'edit'),
  ('Admin', 'news',         'delete'),
  ('Admin', 'legislation',  'view'),
  ('Admin', 'legislation',  'create'),
  ('Admin', 'legislation',  'edit'),
  ('Admin', 'legislation',  'delete'),
  ('Admin', 'documents',    'view'),
  ('Admin', 'documents',    'create'),
  ('Admin', 'documents',    'edit'),
  ('Admin', 'documents',    'delete'),
  ('Admin', 'gallery',      'view'),
  ('Admin', 'gallery',      'create'),
  ('Admin', 'gallery',      'edit'),
  ('Admin', 'gallery',      'delete'),
  ('Admin', 'users',        'view'),
  ('Admin', 'users',        'create'),
  ('Admin', 'users',        'edit'),
  ('Admin', 'users',        'delete');

-- Editor: view/create/edit เท่านั้น ไม่มี delete และไม่มีสิทธิ์ users
INSERT INTO `role_permissions` (`role`, `module`, `action`) VALUES
  ('Editor', 'departments',  'view'),
  ('Editor', 'departments',  'create'),
  ('Editor', 'departments',  'edit'),
  ('Editor', 'employees',    'view'),
  ('Editor', 'employees',    'create'),
  ('Editor', 'employees',    'edit'),
  ('Editor', 'news',         'view'),
  ('Editor', 'news',         'create'),
  ('Editor', 'news',         'edit'),
  ('Editor', 'legislation',  'view'),
  ('Editor', 'legislation',  'create'),
  ('Editor', 'legislation',  'edit'),
  ('Editor', 'documents',    'view'),
  ('Editor', 'documents',    'create'),
  ('Editor', 'documents',    'edit'),
  ('Editor', 'gallery',      'view'),
  ('Editor', 'gallery',      'create'),
  ('Editor', 'gallery',      'edit');

-- Staff: view เท่านั้น ทุกโมดูล ยกเว้น users
INSERT INTO `role_permissions` (`role`, `module`, `action`) VALUES
  ('Staff', 'departments',  'view'),
  ('Staff', 'employees',    'view'),
  ('Staff', 'news',         'view'),
  ('Staff', 'legislation',  'view'),
  ('Staff', 'documents',    'view'),
  ('Staff', 'gallery',      'view');
