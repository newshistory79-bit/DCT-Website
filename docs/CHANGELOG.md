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

## Phase 12 — Testing / Bug Fix / Optimization / Installation Guide

### Added
- `docs/INSTALLATION.md` — คู่มือติดตั้งระบบครบวงจร

### Changed
- `app/core/bootstrap.php` — เพิ่ม `session_set_cookie_params()` ก่อน `session_start()` (httponly, samesite=Lax, secure ตามเงื่อนไข HTTPS)
- `app/models/DashboardModel.php` — เชื่อม Mapping สถิติของ `departments`/`documents`/`gallery`/`legislation` ให้ครบ (เดิมบางโมดูล Map เป็น `null` หรือไม่มีในรายการ)
- `app/views/admin/dashboard.php` — เพิ่ม Stat Card สำหรับ Gallery และ Legislation

### Security
- Session Cookie เพิ่ม Attribute `HttpOnly` และ `SameSite=Lax` ป้องกัน Session ถูกอ่านผ่าน JavaScript และลดความเสี่ยง CSRF บางรูปแบบ

### Testing
- Regression Testing เต็มรูปแบบทุก Module (Authentication, Dashboard, Departments, Employees, News, Legislation, Documents, Gallery, Users, Permission, Activity Log) รวม Upload/Search/Filter/Sort/Pagination/Validation/SQL Injection/XSS/CSRF/Session — 92 รายการ PASS
- Fixed: Session Cookie ไม่มี HttpOnly/SameSite (`app/core/bootstrap.php`)
- Fixed: Dashboard แสดงสถิติ Departments/Documents ผิดพลาดและไม่มี Gallery/Legislation (`app/models/DashboardModel.php`, `app/views/admin/dashboard.php`)
- Regression Testing เฉพาะจุดหลังแก้ไข PASS ทั้งหมด (Session Cookie / Dashboard Statistics / Permission / Login-Logout) ไม่พบ Regression
- php -l PASS (82 files)

### Technical Debt (บันทึกไว้ ไม่แก้ในเฟสนี้)
- `paginate()` ซ้ำใน 8 Model — เหมาะสำหรับรวมเป็น Shared Helper ใน `BaseModel` ในอนาคต (ต้องแยก Task เฉพาะ)
- `app/config/roles.php` เป็น Dead Code (ไม่มีการเรียกใช้งานจริง) — คงไฟล์ไว้ตามคำสั่ง
- Dashboard "กิจกรรมล่าสุด" ยังไม่เชื่อมกับ `activity_logs`

### Database
- ไม่มีการแก้ไขฐานข้อมูล / ไม่มี Migration ใหม่ในเฟสนี้

Status : Stable