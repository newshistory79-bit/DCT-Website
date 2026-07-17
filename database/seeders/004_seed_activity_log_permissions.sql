-- Phase 11: เพิ่มสิทธิ์ Admin สำหรับโมดูล activity_log (view เท่านั้น - ไม่มี create/edit/delete เพราะระบบสร้าง Log เอง)
INSERT INTO `role_permissions` (`role`, `module`, `action`) VALUES
('Admin', 'activity_log', 'view');
