# Changelog

## Phase 4

- Added Departments Module
- Added Permission Class
- Added DepartmentModel
- Added DepartmentController
- Added CRUD UI
- Added CRUD CSS
- Fixed Search parameter bug
- Fixed Soft Delete duplicate bug

Status : Stable

## Phase 11 — Activity Log

### Added
- Table `activity_logs` (Insert-only Audit Trail)
- `App\Core\ActivityLogger` — Helper กลางสำหรับบันทึก Log
- `ActivityLogModel`, `ActivityLogController`
- หน้า Activity Log List (`admin/activity-log/index.php`) พร้อม Search / Filter / Sort / Pagination
- Permission module `activity_log` (Admin เท่านั้น, action `view` เท่านั้น)

### Changed
- `AuthController` — บันทึก Log เมื่อ Login สำเร็จ, Login ล้มเหลว, Logout
- `DepartmentController`, `EmployeeController`, `NewsController`, `LegislationController`, `DocumentController`, `GalleryController`, `UserManagementController` — บันทึก Log ทุก Create/Update/Delete
- `app/config/permissions.php` — เพิ่มสิทธิ์ `activity_log` ให้ Admin
- `app/includes/admin_sidebar.php` — เปิดลิงก์เมนู Activity Log

### Security
- SQL Injection Protection ผ่าน PDO Prepared Statement + Whitelist คอลัมน์ Sort/Filter
- XSS Protection ผ่าน Output Escaping (`e()`)
- CSRF Protection บนทุกฟอร์ม CRUD ที่เกี่ยวข้อง
- Permission เฉพาะ Admin เท่านั้นที่เข้าถึง Activity Log ได้ (Editor/Staff = 403)

### Testing
- php -l PASS (82 files)
- HTTP Testing PASS ครบทุกหัวข้อ (List / Permission / Login-Login Failed-Logout / CRUD 7 โมดูล / Search / Filter / Sort / Pagination)
- Security Testing PASS (SQL Injection / XSS / Invalid Sort Column / Invalid Filter Value / CSRF / Permission)
- Regression Testing PASS (ไม่มี Feature เดิมเสีย)
- Fixed: `EmployeeController::destroy()` บันทึกชื่อพนักงานว่างเปล่าใน Log (อ้างอิง Array Key ผิด Case — `fname`/`lname` แทนที่จะเป็น `Fname`/`Lname`)
- Fixed: `ActivityLogModel` Search ครอบคลุมเฉพาะ `username` ไม่ครอบคลุม `description` — ปรับให้ค้นหาทั้งสองคอลัมน์

### Database
- Migration: `010_create_activity_logs_table.sql` (Executed)
- Seeder: `004_seed_activity_log_permissions.sql` (Executed)

Status : Stable