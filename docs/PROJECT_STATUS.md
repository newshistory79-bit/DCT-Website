# DTC Website — Project Status

**Project:** TCSP Administration System (Department of Technology and Communication of Savannakhet Province)
**Architecture:** PHP MVC (Native PHP 8+) + PDO + MariaDB 10.4.x
**Current Phase:** Phase 12 Completed ✅
**Next Phase:** ไม่มี Phase ถัดไปตาม Roadmap เดิม (Phase 1–12 ครบตาม MasterPrompt) — งานถัดไปขึ้นอยู่กับ Technical Debt/Requirement ใหม่ที่จะได้รับมอบหมาย

---

## Overall Progress

| Phase | Module | Status |
|-------|--------|--------|
| Phase 1 | Project Structure / MVC / Authentication Foundation | ✅ Completed |
| Phase 2 | Users & Login System | ✅ Completed |
| Phase 3 | Dashboard / Sidebar / Permission Foundation | ✅ Completed |
| Phase 3.5 | Shared Components (BaseController / BaseModel / ErrorHandler) | ✅ Completed |
| Phase 4 | Departments Module | ✅ Completed |
| Phase 5 | Employees Module | ✅ Completed |
| Phase 6 | News Module | ✅ Completed |
| Phase 7 | Legislation Module | ✅ Completed |
| Phase 8 | Documents Module | ✅ Completed |
| Phase 9 | Gallery Module | ✅ Completed |
| Phase 10 | Users Module & Database Permission System | ✅ Completed |
| Phase 11 | Activity Log | ✅ Completed |
| Phase 12 | Testing / Bug Fix / Optimization / Installation Guide | ✅ Completed |

---

## Phase 1 — Project Structure

- Project Structure / Folder Convention
- MVC Architecture
- Bootstrap (Config, Core, Database Connection)

## Phase 2 — Authentication

- Login / Logout
- Session Management
- CSRF Protection
- Role

## Phase 3 — Dashboard

- Dashboard Layout
- Sidebar / Header / Footer
- Permission Menu

## Phase 3.5 — Shared Components

- BaseController
- BaseModel
- ErrorHandler

## Phase 4 — Departments Module ✅ Completed

Features:
- CRUD
- Search / Filter / Sort / Pagination
- Soft Delete
- Permission
- CSRF Protection
- SQL Injection Protection
- XSS Protection

Migrations:
- `002_create_departments_table.sql` (Executed)
- `003_departments_soft_delete_unique_fix.sql` (Executed) — เพิ่ม Generated Columns `code_active`/`name_active` และย้าย UNIQUE INDEX มาไว้ที่ Generated Columns เพื่อรองรับ Soft Delete โดยไม่แก้ไข `code`/`name` เดิม

Seeder:
- `002_seed_departments.sql` — ข้อมูลตัวอย่าง 12 แถว

## Phase 5 — Employees Module ✅ Completed

Features:
- CRUD
- Image Upload / Image Replace
- MIME Validation / File Size Validation
- Search / Filter / Sort / Pagination
- Soft Delete
- Permission
- CSRF Protection
- SQL Injection Protection
- XSS Protection

Migration:
- `004_employee_add_soft_delete_and_timestamps.sql` (Executed) — เพิ่ม `created_at`, `updated_at`, `deleted_at`

## Phase 6 — News Module ✅ Completed

Features:
- CRUD
- Soft Delete
- Image Upload
- Search / Status Filter / Sort / Pagination
- Permission
- CSRF Protection
- SQL Injection Protection
- XSS Protection

Migration:
- `005_news_add_status_soft_delete_and_timestamps.sql` (Executed) — เพิ่ม `status`, `created_at`, `updated_at`, `deleted_at`, index `idx_news_status`

Testing: php -l PASS · HTTP Testing PASS · No PHP/SQL Error · No Security Issue

## Phase 7 — Legislation Module ✅ Completed

Features:
- Full CRUD
- Soft Delete
- Search / Filter (Draft / Published) / Sort / Pagination
- Permission Control
- CSRF Protection
- SQL Injection Protection
- XSS Protection

Migration:
- `006_legislation_add_fields_and_soft_delete.sql` (Executed) — เพิ่ม `document_number`, `detail`, `effective_date`, `status`, `created_at`, `updated_at`, `deleted_at`, index `idx_legislation_status`

Testing: php -l PASS · HTTP Testing PASS · Security Testing PASS · No Bug Found

## Phase 8 — Documents Module ✅ Completed

Features:
- Full CRUD (Create บังคับแนบไฟล์, Edit ไม่บังคับ)
- Soft Delete (ไม่ลบไฟล์จริง)
- File Upload: pdf, doc, docx, xls, xlsx, ppt, pptx (สูงสุด 10 MB)
- Search (title/description) / Filter (Draft / Published) / Sort / Pagination
- Permission Control
- CSRF Protection
- SQL Injection Protection
- XSS Protection

Migration:
- `007_create_documents_table.sql` (Executed)

Bug Fixed:
- ไฟล์ `.doc/.xls/.ppt` (OLE Compound Format เดิม) ถูก libmagic ตรวจ MIME เป็น `application/CDFV2` แบบกลาง (แยก Word/Excel/PowerPoint ไม่ได้) — แก้โดยเพิ่ม MIME นี้เข้า Whitelist ของ Controller (ยังคงกรองด้วย Extension Whitelist เสมอ ไม่แก้ไข UploadHelper)

Testing: php -l PASS (63 files) · HTTP Testing PASS ครบทั้ง 7 นามสกุลไฟล์ · Security Testing PASS

## Phase 9 — Gallery Module ✅ Completed

Features:
- Full CRUD (Create บังคับแนบรูป, Edit ไม่บังคับ)
- Soft Delete (ไม่ลบไฟล์รูปจริง)
- Image Upload: jpg, jpeg, png, webp (สูงสุด 2 MB)
- Search (title/description) / Filter (Draft / Published) / Sort / Pagination
- Permission Control (Admin/Editor/Staff)
- CSRF Protection
- SQL Injection Protection
- XSS Protection

Migration:
- `008_create_gallery_table.sql` (Executed)

Bug Fixed:
- `app/views/admin/gallery/form.php` ใช้ตัวแปร `$item` ชื่อเดียวกับ Loop Variable ภายใน `app/includes/admin_sidebar.php` (`foreach ($menuItems as $item)`) เนื่องจาก `require` ใช้ Scope ร่วมกับไฟล์ที่เรียก ทำให้ Sidebar Overwrite ค่า `$item` ก่อนฟอร์มจะใช้งาน (แสดงผลข้อมูลว่างเปล่า/PHP Warning "Undefined array key") แก้โดย (1) เปลี่ยนชื่อตัวแปรใน `admin_sidebar.php` เป็น `$menuItem` (ป้องกันปัญหานี้ในทุกโมดูลอนาคต) และ (2) เปลี่ยนตัวแปรของ Gallery เองจาก `item`/`$item` เป็น `gallery`/`$gallery` ให้สอดคล้องกับชื่อโมดูลอื่น

Testing: php -l PASS (70 files) · HTTP Testing PASS ครบทุกหัวข้อ (Create/Read/Update/Soft Delete/Upload JPG-PNG-WEBP/Replace Image/Search/Filter/Sort/Pagination/Permission/CSRF/SQLi/XSS) · Security Testing PASS · Regression Testing PASS

## Phase 10 — Users Module & Database Permission System ✅ Completed

Features:
- Users CRUD
- Search / Filter / Sort / Pagination
- Validation
- Soft Delete
- Self Delete Protection
- Database Permission System (`role_permissions`, Database-first, Automatic Fallback ไป `app/config/permissions.php`)

Migration:
- `009_create_role_permissions_table.sql` (Executed)

Seeder:
- `003_seed_role_permissions.sql` — สร้างข้อมูลเริ่มต้น 52 แถว (Admin ทุก Module View/Create/Edit/Delete, Editor ทุก Module ยกเว้น Users View/Create/Edit, Staff ทุก Module ยกเว้น Users View)

Testing: Regression Test PASS · php -l PASS · ไม่มี Bug ค้าง

## Phase 11 — Activity Log ✅ Completed

Features:
- ตาราง `activity_logs` (Insert-only / Immutable Audit Trail — ไม่มี Update, Delete, Soft Delete, `updated_at`)
- บันทึก Login / Login Failed / Logout
- บันทึก Create / Update / Delete ของทุกโมดูล: Departments, Employees, News, Legislation, Documents, Gallery, Users
- หน้า Activity Log List: Search (username + description), Filter (Module / Action / Date Range), Sort (id/username/module/action/created_at, ASC/DESC), Pagination (10/25/50/100)
- Permission: เฉพาะ Admin เท่านั้น (module `activity_log`, action `view`)

Migration:
- `010_create_activity_logs_table.sql` (Executed)

Seeder:
- `004_seed_activity_log_permissions.sql` (Executed) — เพิ่มสิทธิ์ Admin/activity_log/view 1 แถว

ไฟล์ที่สร้างใหม่ (7 ไฟล์):
- `app/controllers/ActivityLogController.php`
- `app/core/ActivityLogger.php`
- `app/models/ActivityLogModel.php`
- `app/views/admin/activity-log/index.php`
- `public/admin/activity-log/index.php`
- `database/migrations/010_create_activity_logs_table.sql`
- `database/seeders/004_seed_activity_log_permissions.sql`

ไฟล์ที่แก้ไข (10 ไฟล์):
- `app/config/permissions.php`
- `app/controllers/AuthController.php`
- `app/controllers/DepartmentController.php`
- `app/controllers/DocumentController.php`
- `app/controllers/EmployeeController.php`
- `app/controllers/GalleryController.php`
- `app/controllers/LegislationController.php`
- `app/controllers/NewsController.php`
- `app/controllers/UserManagementController.php`
- `app/includes/admin_sidebar.php`

Bug ที่พบและแก้ไข:
1. `app/controllers/EmployeeController.php` (`destroy()`) — Log การลบพนักงานบันทึกชื่อว่างเปล่า ("ลบพนักงาน:  ") เพราะอ้างอิง `$employee['fname']`/`$employee['lname']` (ตัวพิมพ์เล็ก) แต่ตาราง `employee` ใช้คอลัมน์ `Fname`/`Lname` (ตัวพิมพ์ใหญ่ ห้ามเปลี่ยนตามมาตรฐานโปรเจกต์) — แก้เป็น `$employee['Fname']`/`$employee['Lname']`
2. `app/models/ActivityLogModel.php` (`buildWhere()`) — Search ด้วย keyword ค้นหาได้เฉพาะ `username` ไม่ครอบคลุม `description` — แก้เป็นค้นหาทั้ง `username OR description` พร้อมปรับ placeholder ใน view ให้ตรงกับพฤติกรรมใหม่

Testing:
- php -l PASS (82 files)
- HTTP Testing PASS ครบทุกหัวข้อ: Activity Log List, Permission (Admin/Editor/Staff), Login/Login Failed/Logout, CRUD 7 โมดูล, Search, Filter, Sort, Pagination
- Security Testing PASS: SQL Injection, XSS, Invalid Sort Column, Invalid Filter Value, CSRF, Permission
- Data Integrity PASS: ตรวจสอบทุกแถวใน `activity_logs` มีครบ user_id (ยกเว้น login_failed ที่ไม่ทราบตัวตน) / username / role / module / action / description / ip_address / created_at
- Regression Testing PASS: Login, Permission, CRUD ทุกโมดูล, Upload, Soft Delete, Database Permission System — ไม่มี Feature เดิมเสีย
- ทำความสะอาดข้อมูลทดสอบเรียบร้อย (ไม่กระทบ `activity_logs` ซึ่งเป็น Insert-only)

Reused Components:
- BaseController, AuthMiddleware, Permission, crud.css, admin.css, admin.js, admin_header.php, admin_sidebar.php, admin_footer.php

## Phase 12 — Testing / Bug Fix / Optimization / Installation Guide ✅ Completed

ขอบเขต: Regression Testing ครบทั้งระบบ (Authentication, Dashboard, Departments, Employees, News, Legislation, Documents, Gallery, Users, Permission, Activity Log รวม Upload/Search/Filter/Sort/Pagination/Validation/SQL Injection/XSS/CSRF/Session) ตามด้วย Bug Fix และ Query Optimization/Code Cleanup ขนาดเล็กเฉพาะรายการที่อนุมัติเท่านั้น (ไม่ Refactor ข้ามโมดูล/ไม่เปลี่ยน Architecture) และจัดทำ Installation Guide — ไม่มีการแก้ไขฐานข้อมูล/Migration ในเฟสนี้

Bug ที่พบและแก้ไข:
1. `app/core/bootstrap.php` — Session Cookie (`PHPSESSID`) ไม่มี Attribute `HttpOnly`/`Secure`/`SameSite` (ใช้ค่า Default จาก php.ini ซึ่งปิดอยู่ทั้งหมด) ทำให้ขาด Defense-in-Depth ป้องกัน Session ถูกอ่านผ่าน JavaScript หากเกิดช่องโหว่ XSS ในอนาคต — แก้โดยเพิ่ม `session_set_cookie_params()` ก่อนเรียก `session_start()` กำหนด `httponly = true`, `samesite = 'Lax'`, `secure` ตามเงื่อนไข HTTPS จริงเท่านั้น (ไม่บังคับ true บน HTTP/localhost)

Feature Gap ที่พบและแก้ไข:
1. Dashboard แสดงสถิติไม่ครบและไม่ถูกต้องตาม MasterPrompt หัวข้อ DASHBOARD — `app/models/DashboardModel.php` (`MODULE_TABLES`) เดิม Map `departments`/`documents` เป็น `null` ทั้งที่มีตารางจริงอยู่แล้วตั้งแต่ Phase 4/8 และไม่มี `gallery`/`legislation` อยู่ในรายการเลย ทำให้หน้า Dashboard แสดง "ยังไม่มีโมดูล" ผิดพลาดและไม่แสดงสถิติ Gallery/Legislation — แก้โดยเชื่อม Mapping ให้ครบทั้ง 4 โมดูล ใน `app/models/DashboardModel.php` และเพิ่ม Stat Card สำหรับ Gallery/Legislation ใน `app/views/admin/dashboard.php`

Documentation:
- สร้าง `docs/INSTALLATION.md` — คู่มือติดตั้งระบบครบวงจร (System Requirements, การสร้างฐานข้อมูลและรัน Migration/Seeder ตามลำดับ, การตั้งค่า Config, สิทธิ์โฟลเดอร์ Upload, การ Login ครั้งแรก, โครงสร้างโปรเจกต์, Troubleshooting)

Technical Debt (บันทึกไว้ ยังไม่แก้ในเฟสนี้):
- `paginate()` มีโครงสร้างซ้ำกันเกือบทั้งหมดใน 8 Model (`DepartmentModel`, `EmployeeModel`, `NewsModel`, `LegislationModel`, `DocumentModel`, `GalleryModel`, `UserManagementModel`, `ActivityLogModel`) — เหมาะสำหรับรวมเป็น Shared Helper ใน `BaseModel` ในอนาคต แต่ต้องแยกเป็น Task เฉพาะที่มี Regression Test ครอบคลุมทุกโมดูลพร้อมกัน เนื่องจากกระทบทุก CRUD Module
- `app/config/roles.php` เป็น Dead Code ยืนยันแล้วว่าไม่มีไฟล์ใดในโปรเจกต์ `require`/`include` ไฟล์นี้เลย — คงไฟล์ไว้ตามคำสั่ง (ไม่อนุมัติให้ลบในเฟสนี้)
- Dashboard "กิจกรรมล่าสุด" ตาม MasterPrompt ยังไม่ได้เชื่อมกับตาราง `activity_logs` (Phase 11) — ปัจจุบันมีเฉพาะ "ผู้ใช้ Login ล่าสุด" (Recent Logins จากตาราง `users`) ซึ่งเป็นคนละความหมายกัน

ไฟล์ที่แก้ไข (3 ไฟล์):
- `app/core/bootstrap.php`
- `app/models/DashboardModel.php`
- `app/views/admin/dashboard.php`

ไฟล์ที่สร้างใหม่ (1 ไฟล์):
- `docs/INSTALLATION.md`

Testing:
- Regression Testing เต็มรูปแบบก่อนแก้ไข: รวม 92 รายการ PASS (พบ 1 Bug + 1 Feature Gap ตามที่ระบุข้างต้น ไม่พบ Bug ที่กระทบข้อมูล/บายพาส Permission ได้)
- Regression Testing เฉพาะจุดหลังแก้ไข: Session Cookie (ยืนยัน `HttpOnly; SameSite=Lax` จาก Response Header จริง), Dashboard Statistics (departments=12, documents/gallery/legislation=0 แสดงค่าจริงถูกต้อง แทน "ยังไม่มีโมดูล"), Permission (Admin/Editor/Staff ทุก Module ผลลัพธ์เหมือนเดิมทุกจุด), Login/Logout (ทำงานปกติหลังเปลี่ยน Session Cookie Params) — PASS ทั้งหมด ไม่พบ Regression
- php -l PASS (82 files)

---

## Shared / Reusable Components

- BaseController / BaseModel
- AuthMiddleware
- Permission (Database-first + Fallback)
- UploadHelper
- ActivityLogger
- crud.css / admin.css / admin.js
- admin_header.php / admin_sidebar.php / admin_footer.php

---

## Migration Status

| Migration | Status |
|-----------|--------|
| `001_create_users_table.sql` | ✅ Executed |
| `002_create_departments_table.sql` | ✅ Executed |
| `003_departments_soft_delete_unique_fix.sql` | ✅ Executed |
| `004_employee_add_soft_delete_and_timestamps.sql` | ✅ Executed |
| `005_news_add_status_soft_delete_and_timestamps.sql` | ✅ Executed |
| `006_legislation_add_fields_and_soft_delete.sql` | ✅ Executed |
| `007_create_documents_table.sql` | ✅ Executed |
| `008_create_gallery_table.sql` | ✅ Executed |
| `009_create_role_permissions_table.sql` | ✅ Executed |
| `010_create_activity_logs_table.sql` | ✅ Executed |

## Seeder Status

| Seeder | Status |
|--------|--------|
| `001_seed_default_admin.sql` | ✅ Executed |
| `002_seed_departments.sql` | ✅ Executed |
| `003_seed_role_permissions.sql` | ✅ Executed |
| `004_seed_activity_log_permissions.sql` | ✅ Executed |

---

## Current Project Status

PHP Syntax:
- ✅ php -l ผ่านทุกไฟล์ (82 ไฟล์ ล่าสุด ณ Phase 12)

Security:
- ✅ CSRF Protection
- ✅ SQL Injection Protection
- ✅ XSS Protection
- ✅ Permission System (Database-first + Fallback)
- ✅ Session Cookie: HttpOnly + SameSite=Lax (Secure ตามเงื่อนไข HTTPS จริง) — เพิ่มใน Phase 12

Database:
- ✅ Migration ผ่านทั้งหมด (010 รายการ)
- ✅ ไม่มี SQL Error

Uploads:
- ✅ UploadHelper
- ✅ Random Filename
- ✅ MIME Validation
- ✅ Size Validation

---

## Next Task

ไม่มี Phase ถัดไปตาม Roadmap เดิมของ MasterPrompt (ครบ Phase 1–12 แล้ว) — รายการ Technical Debt ที่บันทึกไว้ในหัวข้อ Phase 12 สามารถพิจารณาเป็นงานถัดไปได้หากต้องการ

---

**Last Updated:** 2026-07-17 — Phase 12 (Testing / Bug Fix / Optimization / Installation Guide) Completed
