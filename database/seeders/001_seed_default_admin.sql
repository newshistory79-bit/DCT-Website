-- Seeder: 001_seed_default_admin.sql
-- Purpose: สร้างบัญชี Admin เริ่มต้น เพื่อให้ทดสอบ Login เข้าระบบครั้งแรกได้ (Phase 2)
-- Status : รอการอนุมัติก่อน Execute (ยังไม่รันเข้าฐานข้อมูล) — ต้องรันไฟล์ 001_create_users_table.sql ก่อนเสมอ
--
-- Default Username : admin
-- Default Password : Admin@123456
-- คำเตือน         : ข้อมูลชุดนี้ใช้สำหรับ Development เท่านั้น ห้ามใช้ค่านี้บน Production
-- ข้อบังคับ         : ระบบต้องบังคับให้เปลี่ยนรหัสผ่านทันทีในการ Login ครั้งแรก (ควบคุมด้วยคอลัมน์ first_login = TRUE)

INSERT INTO `users`
  (`username`, `password`, `full_name`, `email`, `role`, `status`, `first_login`)
VALUES
  ('admin', '$2y$10$rOhVPNwc4alKm6bbcjnSA.LWbfyIEjxv/mwzOHnrMuHeRcitxYa6C', 'System Administrator', NULL, 'Admin', 'Active', TRUE);

-- Username : admin
-- Password (plaintext ก่อน hash): Admin@123456
-- Hash ด้านบนสร้างจริงด้วย password_hash($password, PASSWORD_BCRYPT) บน PHP 8.2.12 และผ่านการตรวจสอบด้วย password_verify() แล้วว่าถูกต้อง
-- first_login = TRUE บังคับให้ผู้ใช้ต้องเปลี่ยนรหัสผ่านทันทีหลัง Login ครั้งแรก
