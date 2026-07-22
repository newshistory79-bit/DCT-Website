-- Migration: 012_drop_gallery_table.sql
-- Purpose: ลบ Gallery Module ออกจากระบบทั้งหมด (โค้ด/View/Route ถูกลบไปแล้ว) - ตาราง gallery
--          ว่างเปล่า (0 แถว) และไม่มี Foreign Key จากตารางอื่นอ้างอิงมา จึงลบได้อย่างปลอดภัย
--          เก็บ activity_logs ที่ module='gallery' ไว้ตามเดิมเป็นประวัติ (Audit Trail) - ไม่ลบ
-- Status : อนุมัติแล้วโดยผู้ใช้ - Executed

DELETE FROM `role_permissions` WHERE `module` = 'gallery';

DROP TABLE IF EXISTS `gallery`;
