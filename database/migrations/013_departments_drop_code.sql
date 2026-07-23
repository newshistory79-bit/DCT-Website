-- Migration: 013_departments_drop_code.sql
-- Purpose: ยกเลิกการใช้งาน Department Code (code) ทั้งระบบอย่างถาวรตามคำสั่งอนุมัติ
--          ลบ Unique Index บน Generated Column ก่อน จากนั้นลบ Generated Column `code_active`
--          (ซึ่งอ้างอิงจาก `code`) แล้วจึงลบคอลัมน์ `code` จริงเป็นลำดับสุดท้าย
--          ตรวจสอบแล้วไม่มี Foreign Key จากตารางอื่นอ้างอิงมาที่ departments.code/id
--          หลังการเปลี่ยนแปลง departments จะเหลือฟิลด์ใช้งานจริง: name, description, status, sort_order
-- Status : รอการอนุมัติก่อน Execute (ยังไม่รันเข้าฐานข้อมูล)

ALTER TABLE `departments`
  DROP INDEX `departments_code_active_unique`;

ALTER TABLE `departments`
  DROP COLUMN `code_active`;

ALTER TABLE `departments`
  DROP COLUMN `code`;
