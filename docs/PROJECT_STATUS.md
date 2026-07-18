# DTC Website — Project Status

**Project:** TCSP Administration System (Department of Technology and Communication of Savannakhet Province)
**Architecture:** PHP MVC (Native PHP 8+) + PDO + MariaDB 10.4.x
**Current Phase:** Public Website Stage 2 (Content Modules 2.1–2.7 + Final Quality Review) Completed ✅
**Next Phase:** รอผู้ใช้อนุมัติ Commit/Push Public Website Stage 2 จากนั้นเลือกกลับไปทำ Admin Panel Redesign Module 2 (Employees) ที่ค้างไว้ หรือ Task ถัดไปตามคำสั่งผู้ใช้

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
| Phase 13 | Activities Management System (Admin CRUD + Dashboard + Public + Quality Review) | ✅ Completed |

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

## Public Website — Stage 1: Foundation + Layout + Home ✅ Completed

ขอบเขต: สร้างโครงเว็บไซต์ Public (Header/Nav/Footer/Design System) และหน้า Home จริง แทนที่ Phase-1 stub เดิมใน `public/index.php` — ดึงข้อมูลข่าวล่าสุดจาก `NewsModel` เดิม (ไม่สร้าง Model ใหม่ซ้ำ, ไม่แก้ Database Schema, ไม่มี Dummy Data)

ไฟล์ที่สร้างใหม่ (7 ไฟล์):
- `app/helpers/icons.php` — ชุดไอคอน Inline SVG กลางของ Public Website
- `app/includes/public_header.php`, `app/includes/public_footer.php`
- `app/controllers/PublicHomeController.php`
- `app/views/public/home.php`
- `public/assets/css/public.css`, `public/assets/js/public.js`

ไฟล์ที่แก้ไข (2 ไฟล์):
- `public/index.php` — เปลี่ยนจาก Phase-1 DB Connection Test Stub เป็น Front Controller เรียก `PublicHomeController`
- `app/core/bootstrap.php` — เพิ่ม `require_once` โหลด `app/helpers/icons.php` (Additive เท่านั้น ไม่กระทบพฤติกรรมเดิม)

การตัดสินใจสำคัญ (อ้างอิงจากการวิเคราะห์ Design Reference `design/image.png` และข้อจำกัดของฐานข้อมูลจริง):
- ยังไม่มีไฟล์รูปภาพจริงในโปรเจกต์ (มีเฉพาะ Design Mockup และรูปพนักงานตัวอย่าง 1 ไฟล์) → Hero ใช้ Placeholder/Graphic (Inline SVG + CSS Gradient) แทนภาพถ่ายจริงไปก่อน
- ตาราง `employee` ไม่มี `department_id` เชื่อมกับ `departments` → ออกแบบให้ Departments/Employees เป็น 2 หมวดอิสระจากกัน (ไม่แก้ Schema)
- ตัด Language-switcher (ธง 2 ธง) ออกจาก Top Bar ของ Design Mockup เพราะข้อมูลใน DB เป็นภาษาเดียวต่อ Record ไม่มีคอลัมน์รองรับ Multi-language จริง — แทนที่ด้วยข้อมูลติดต่อ (โทร/อีเมล) ที่ใช้งานได้จริง

Reused Components: `BaseController`, `BaseModel`, `NewsModel::paginate()`, `Database`, `Autoloader`, `app/helpers/functions.php` (`e()`, `baseUrl()`, `uploadUrl()`) — ไม่แก้ไข Admin Controller/Model/View/CSS/JS แม้แต่ไฟล์เดียว

Testing:
- `php -l` PASS ทุกไฟล์ที่สร้าง/แก้ไข (7 ไฟล์)
- HTTP Testing PASS: `GET /public/` → 200, `public.css`/`public.js` → 200, แสดง Empty State ข่าวถูกต้อง (ตาราง `news` ยังไม่มีข้อมูล Published ในขณะนี้), ไม่พบ PHP Warning/Notice/Fatal ใน Output
- Regression PASS: `public/admin/login.php` และ `admin.css` ยังโหลดได้ปกติ (200) — Admin Panel ไม่ถูกกระทบ
- หมายเหตุ: ยังไม่ได้ตรวจสอบด้วย Browser Screenshot จริง (ไม่มีเครื่องมือ Browser Automation ในสภาพแวดล้อมนี้) ผู้ใช้ควรเปิด `http://localhost/DTC-Website/public/` ด้วยตนเองเพื่อตรวจสอบ UI ก่อนเริ่ม Stage 2

รอตรวจสอบ/อนุมัติจากผู้ใช้ก่อนเริ่ม **Stage 2 — Content Modules (News, Legislation, Documents, Gallery)**

### Revision — ปรับ Home ให้ตรงกับ `design/index.png` แบบละเอียด

ผู้ใช้พบว่า `design/index.png` (คนละไฟล์กับ `design/image.png` ที่ใช้วิเคราะห์ตอนแรก) มีรายละเอียดครบกว่ามาก และขอให้ทำตามภาพนี้ให้ใกล้เคียงที่สุด จึงปรับ Header/Hero/Quick Menu/News Card/Stats Bar ใหม่ทั้งหมด (ไฟล์เดิม ไม่มีไฟล์ใหม่เพิ่มด้าน Layout):

- **Top Bar**: เพิ่มเวลาทำการ, ลิงก์ "บริการออนไลน์ / คำถามที่พบบ่อย / แผนผังเว็บไซต์", ช่องค้นหาเปลี่ยนเป็นพื้นขาวทึบ + ปุ่มค้นหาวงกลมกรมท่า (จากเดิมโปร่งแสง)
- **Header**: แก้บั๊ก Flexbox ที่ทำให้ข้อความล้น/ทับกัน (`flex-shrink: 0`), เปลี่ยนข้อความชื่อหน่วยงานให้ตรงกับตัวอักษรในภาพ ("พะแนก เทคโนโลยีและการสื่อสาร / แขวงสะหวันนะเขต"), เปลี่ยนสไตล์เมนู Active จากพื้นหลังทึบเป็นขีดเส้นใต้สีน้ำเงินตามภาพ, ตัด "กฎหมาย/ระเบียบ" ออกจากเมนูหลัก (ภาพต้นฉบับมี 8 รายการ ไม่มี Legislation) — ย้ายไปไว้ที่ Footer แทนเพื่อให้ยังเข้าถึงได้
- **Hero**: เปลี่ยนปุ่มรองจากปุ่มโปร่งใสเป็นปุ่มขาวทึบ, จุด Slider จาก 3 เป็น 5 จุด (วงแหวนโปร่ง/วงกลมทึบ แทน Pill), จัดข้อความกึ่งกลางแนวตั้งแทนการชิดล่าง
- **Quick Menu**: ไอคอนเอาพื้นหลังวงกลมออก (ใช้ไอคอนเปล่าตามภาพ), หัวข้อเปลี่ยนเป็นสีน้ำเงินกรมท่า
- **News Card**: Badge วันที่เปลี่ยนจากพื้นเข้มเป็นพื้นขาว + เงา ตามภาพ
- **Stats Bar**: ปรับคำต่อท้ายป้ายให้ตรงภาพ ("...รายการ", "...คน", "...โครงการ")
- เพิ่มหน้า **แผนผังเว็บไซต์** (`public/sitemap.php`, `PublicPageController::sitemap()`, `app/views/public/sitemap.php`) เพื่อให้ลิงก์ใน Top Bar ใช้งานได้จริง ไม่ใช่ลิงก์หลอก

**ข้อยกเว้นที่ตั้งใจไม่ Copy ตรงเป๊ะ 2 จุด (แจ้งผู้ใช้แล้ว)**:
1. โลโก้ตราแผ่นดินลาว (รายละเอียดสูง) — ใช้ตราวงกลมสีทองแบบย่อแทน เพราะเป็นสัญลักษณ์ทางการที่ไม่ควรลอกเลียนแบบผิดเพี้ยน
2. หัวข้อ Hero ในภาพต้นฉบับใช้คำว่า "จังหวัด" (คำไทย) แต่แก้ไขเป็น "แขวง" (คำศัพท์ทางการปกครองที่ถูกต้องของลาว) เพื่อความถูกต้อง เนื่องจากหน่วยงานอยู่ใน สปป.ลาว

Testing: `php -l` PASS ทุกไฟล์ (10 ไฟล์ที่แก้/สร้างเพิ่ม), CSS Brace Balanced, HTTP Testing PASS (`GET /public/` และ `GET /public/sitemap.php` → 200 ไม่มี PHP Warning/Error), Regression PASS (Admin Panel ปกติ)

## Admin Panel — Design Refresh (คั่นระหว่าง Public Website Stage 1 และ 2 ตามคำขอผู้ใช้)

ผู้ใช้ขอให้ปรับ Design ของ Admin Panel ทั้งระบบให้มี Identity เดียวกับ Public Website ที่เพิ่งทำ (สีกรมท่า/ฟ้า, มุมโค้ง, เงานุ่ม) ก่อนกลับไปทำ Public Stage 2 ต่อ

ขอบเขต: **ปรับเฉพาะ CSS + Include ที่ใช้ร่วมกันทุกโมดูล เท่านั้น** — ไม่แตะ Controller/Model และไม่แก้ไข View รายโมดูล (Departments/Employees/News/Legislation/Documents/Gallery/Users/Activity Log) แม้แต่ไฟล์เดียว เพราะทุกโมดูลใช้ Class เดียวกันจาก `admin.css`/`crud.css` อยู่แล้ว (ยืนยันจากการอ่านโค้ดจริงของ `departments/index.php` ก่อนแก้) ทำให้ Design ใหม่ไหลไปถึงทุกหน้าโดยอัตโนมัติ

ไฟล์ที่แก้ไข (7 ไฟล์):
- `public/assets/css/admin.css` — เพิ่ม CSS Variables (Design Token ชุดเดียวกับ `public.css`), ปรับ Topbar/Sidebar/Stat Card/Info Box ใหม่ทั้งหมด
- `public/assets/css/crud.css` — ปรับปุ่ม/Filter Bar/ตาราง/Badge/Pagination/ฟอร์มให้ใช้ Token ชุดเดียวกัน
- `public/assets/css/auth.css` — ปรับหน้า Login ใหม่ (พื้นหลัง Gradient, ปุ่ม/Input Focus State)
- `app/helpers/icons.php` — เพิ่มไอคอนใหม่ 5 แบบ (`dashboard`, `users`, `settings`, `log`, `logout`) สำหรับ Sidebar/Dashboard
- `app/includes/admin_sidebar.php` — เพิ่มไอคอนหน้าเมนูทุกรายการ + เพิ่ม Logic ไฮไลท์เมนูปัจจุบัน (Active State ตาม URL จริง, เดิมไม่มี Logic นี้)
- `app/views/admin/login.php` — เพิ่ม `<div class="login-mark">` (ตราวงกลมเหนือชื่อระบบ)
- `app/views/admin/dashboard.php` — เพิ่มไอคอนใน Stat Card แต่ละใบ

Reused: ทุก Class เดิม (`.page-heading`, `.btn-primary`, `.filter-bar`, `.data-table`, `.badge`, `.pagination-links`, `.data-form` ฯลฯ) และ ID ที่ `admin.js` อ้างอิง (`#sidebarToggle`, `#adminSidebar`, `data-confirm`) **ไม่มีการเปลี่ยนชื่อ Class/ID ใดๆ ที่กระทบ JavaScript หรือ PHP เดิม**

Testing:
- `php -l` PASS ทุกไฟล์ที่แก้ (4 ไฟล์ PHP), CSS Brace Balanced ทั้ง 3 ไฟล์ (admin.css 49/49, crud.css 50/50, auth.css 13/13)
- HTTP Testing PASS: `GET /admin/login.php` → 200 ไม่มี PHP Warning/Error, Asset ทั้ง 3 ไฟล์ CSS โหลด 200
- Regression PASS: Public Website ไม่ถูกกระทบ (ตรวจแล้ว `icons.php` เป็นการเพิ่ม Entry ใหม่ ไม่กระทบของเดิม)
- **หมายเหตุ**: ไม่ได้ทดสอบ Login จริงเข้าหน้า Dashboard เพราะจะต้องเปลี่ยน Session/Password ของบัญชี Admin จริงตาม `first_login` Flag ซึ่งเป็นการเปลี่ยนสถานะข้อมูลจริงโดยไม่จำเป็น — ผู้ใช้ควร Login ตรวจสอบหน้า Dashboard/Sidebar/CRUD ด้วยตนเองอีกครั้ง

รอตรวจสอบจากผู้ใช้ ก่อนกลับไปทำ **Public Website Stage 2**

## Admin Panel — UI/UX Redesign (Module 1/9: Dashboard) — อ้างอิง `design/admin.jpeg`

ผู้ใช้ขอ Redesign Admin Panel ทั้งระบบแบบละเอียด (Sidebar จัดกลุ่ม, Topbar มี Notification/User Dropdown, Dashboard มี Stat Card ไล่เฉด/Quick Action/Timeline/Chart, ตารางแบบ Modern ฯลฯ) พร้อมกำหนดเงื่อนไขชัดเจน: **ห้ามแก้ Business Logic/Controller/Model/Routing/Database/Permission/Authentication ใดๆ ทำเฉพาะ UI/UX** และให้ทำทีละโมดูล หยุดรออนุมัติทุกครั้งก่อนไปต่อ ลำดับ: Dashboard → Employees → Departments → News → Documents → Gallery → Users → Activity Log → Settings

**Module 1: Dashboard — เสร็จแล้ว**

ไฟล์ที่แก้ไข (7 ไฟล์):
- `public/assets/css/admin.css` — Design Token ใหม่ตามสีที่ผู้ใช้กำหนด (Primary #1A3D7C, Secondary #2563EB, Success/Warning/Danger), Topbar/Sidebar/Stat Card/Quick Action/Timeline/Chart ทั้งหมด
- `app/includes/admin_header.php` — เพิ่ม Notification Dropdown + User Avatar Dropdown (แทนลิงก์ออกจากระบบเปล่าๆ เดิม)
- `app/includes/admin_sidebar.php` — เพิ่มไอคอน, จัดกลุ่มเมนู (Dashboard/Content/Management/System), รองรับ Sidebar แบบย่อ (Collapsed)
- `public/assets/js/admin.js` — เพิ่ม Logic เปิด/ปิด Dropdown และ Sidebar Collapse (จอใหญ่) แยกจากพฤติกรรม Slide-in เดิมบนจอเล็ก (ของเดิมยังทำงานเหมือนเดิมทุกประการ)
- `app/views/admin/dashboard.php` — Stat Card ไล่เฉดสี + ลิงก์ดูทั้งหมด, Quick Action (กรอง Permission ด้วย `can()` เดิม), Timeline กิจกรรมล่าสุด, กราฟเส้น CSS/SVG (ไม่ใช้ Library ภายนอก)
- `app/controllers/DashboardController.php`, `app/models/ActivityLogModel.php` — เพิ่ม Method อ่านข้อมูลอย่างเดียว (`getDailyCounts()`) และดึงข้อมูล `recentActivity`/`dailyCounts` เพิ่มเติม (Additive เท่านั้น ไม่แก้ไข Logic เดิมที่มีอยู่ - `$stats`/`$recentLogins` ยังทำงานเหมือนเดิมทุกประการ)

**การตัดสินใจสำคัญด้านความปลอดภัย**: ข้อมูล Timeline/กราฟดึงจากตาราง `activity_logs` ซึ่งตาม Phase 11 กำหนดให้ดูได้เฉพาะ Role ที่มีสิทธิ์ `activity_log:view` (Admin เท่านั้น) — จึงเช็ค `can('activity_log','view')` ก่อนดึงข้อมูลทุกจุด (ทั้งใน Controller และ Topbar) เพื่อไม่ให้ Editor/Staff เห็นข้อมูล Audit Log ผ่าน Dashboard/Notification โดยไม่ได้ตั้งใจ (เดิมหน้า Activity Log จำกัดเฉพาะ Admin อยู่แล้ว ต้องคงพฤติกรรมเดิมไว้) — สำหรับ Role ที่ไม่มีสิทธิ์ จะเห็น Fallback เป็นข้อมูลบัญชีตัวเอง/ผู้ Login ล่าสุดแทน (เนื้อหาเดิมก่อน Redesign)

**Known Deliberate Deviation จาก Design Reference**: ไม่ได้ทำระบบ Notification จริง (ตาราง `notifications`/สถานะอ่านแล้ว-ยังไม่อ่านไม่มีในฐานข้อมูล) — ใช้ Feed กิจกรรมล่าสุดจาก `activity_logs` แทน (ข้อมูลจริง ไม่ใช่ Dummy) ไม่มีตัวเลข Badge ปลอมบนกระดิ่ง

Testing:
- `php -l` PASS ทุกไฟล์ที่แก้ (5 ไฟล์ PHP), CSS Brace Balanced (admin.css 106/106)
- Render Test ผ่าน CLI จำลอง Session Admin (ไม่แตะ Database/Password จริงของบัญชี Admin จริง เพื่อเลี่ยงปัญหา `first_login` บังคับเปลี่ยนรหัสผ่าน) — ยืนยันไม่มี PHP Warning/Notice/Exception, Timeline/Chart/Quick Action/Dropdown แสดงข้อมูลจริงจากฐานข้อมูลถูกต้องครบถ้วน
- Regression PASS: หน้า Login และ Public Website โหลดปกติ (200) ไม่ถูกกระทบ

รอผู้ใช้ตรวจสอบและอนุมัติ ก่อนเริ่ม **Module 2: Employees** (งานนี้ถูกคั่นกลางด้วย Phase 13 — ยังไม่ได้กลับไปทำต่อ)

---

## Phase 13 — Activities Management System ✅ Completed

สร้าง Module "Activities" (กิจกรรม) ที่ Dashboard/Public Website เตรียม Placeholder ไว้ตั้งแต่ต้นแต่ยังไม่มีตารางฐานข้อมูลรองรับจริง ทำตามมาตรฐานเดียวกับ News/Gallery ทุกประการ วิเคราะห์ก่อนแล้วขออนุมัติทีละ Stage (Stage 1: DB → Stage 2: Admin CRUD → Stage 3: Dashboard → Stage 4: Public → Quality Review) ตามที่ผู้ใช้กำหนด

### Stage 1 — Database
- Migration `011_create_activities_table.sql`: id, title, description, activity_date (NOT NULL), location, image (Optional), status, timestamps, deleted_at, Index บน status และ activity_date
- Seeder `005_seed_activities_permissions.sql`: Admin (view/create/edit/delete), Editor (view/create/edit), Staff (view) — Pattern เดียวกับ gallery — Execute แล้ว ยืนยันด้วย `DESCRIBE`/`SELECT` จริง

### Stage 2 — Admin CRUD
ไฟล์ใหม่: `app/models/ActivityModel.php`, `app/controllers/ActivityController.php`, `app/views/admin/activities/{index,form}.php`, `public/admin/activities/{index,form,delete}.php`
แก้ไข: `app/includes/admin_sidebar.php` (เปิดเมนู Activities)
ทดสอบผ่าน HTTP จริงครบ: Create (มี/ไม่มีรูป), Edit (เปลี่ยน/ไม่เปลี่ยนรูป), Soft Delete, CSRF, XSS, SQL Injection, Search/Filter/Sort/Pagination, Permission Matrix 3 Role — PASS ทั้งหมด

### Stage 3 — Dashboard Integration
แก้ไข: `app/models/DashboardModel.php` (Map ตาราง activities จริง + นับเฉพาะ `deleted_at IS NULL` **เฉพาะ activities** เพื่อไม่กระทบตัวเลข Stat Card เดิมของโมดูลอื่นที่มีข้อมูล Soft Delete อยู่แล้วจริง เช่น departments 12 total/9 active), `app/views/admin/dashboard.php` (ลิงก์ Stat Card), `app/config/permissions.php` (Fallback ให้ตรง Seeder)
ทดสอบ Dashboard ทั้ง Admin/Editor/Staff ผ่าน CLI Session จำลอง — PASS ทั้งหมด, Departments Stat Card ยืนยันยังแสดง 12 เหมือนเดิม (ไม่มี Regression)

### Stage 4 — Public Website Integration
ไฟล์ใหม่: `app/controllers/PublicActivityController.php`, `app/views/public/activities/{index,detail}.php`, `public/activities/{index,detail}.php`
แก้ไข: `public_header.php`/`public_footer.php`/`home.php` (เมนู "กิจกรรม" ชี้ Activities แทน Gallery, เพิ่มเมนู "คลังภาพ" แยก), `PublicHomeController.php` (Stat กิจกรรมนับจาก ActivityModel จริง)
**บั๊กที่พบและแก้ไข**: Front Controller ใช้ `dirname(__DIR__)` ผิด (ต้องเป็น `dirname(__DIR__, 2)`) ทำให้ Fatal Error หา bootstrap.php ไม่เจอ — แก้ไขและทดสอบซ้ำผ่านแล้ว
ทดสอบ: Published/Draft/Soft-Delete Visibility, Sort `activity_date DESC`, Pagination, Empty State, XSS — PASS ทั้งหมด

### Quality Review (ก่อน Stage 5) — ขอบเขตเฉพาะหน้าที่สร้างจริงแล้ว (Home + Activities)
ผู้ใช้ยืนยันขอบเขตเฉพาะหน้าที่มีอยู่จริง ไม่สร้าง Public Module อื่น (News/Departments ฯลฯ) ที่ยังไม่เริ่ม (Public Website Stage 2)

ไฟล์ใหม่ (3 ไฟล์): `app/views/public/404.php`, `app/includes/public_empty_state.php`
แก้ไข (11 ไฟล์): `app/helpers/functions.php` (`renderNotFound()`, `renderEmptyState()`), `app/includes/public_header.php` (SEO Meta ครบ: keywords/og:title/description/type/image/url), `app/includes/public_footer.php` (`defer`), `public/assets/css/public.css` (Breadcrumb Semantic, Focus State, Empty State Actions), `public/assets/js/public.js` (`.js-back-link`), `app/views/public/{home,sitemap}.php`, `app/views/public/activities/{index,detail}.php`, `app/controllers/{PublicHomeController,PublicActivityController,PublicPageController}.php`

รายละเอียด:
1. **404 กลาง** — `renderNotFound()` Helper ใช้ร่วมได้ทุก Public Controller ในอนาคต, Wire เข้ากับ `PublicActivityController::detail()` แล้ว (เดิมเขียน Inline แยกเอง)
2. **SEO** — เพิ่ม keywords/og:title/description/type/image/url ครบทุกหน้า ใช้ข้อมูลจริงต่อหน้า (Activity Detail ใช้ชื่อกิจกรรมจริง, og:image ใช้รูปจริงถ้ามี — **ไม่ใส่ og:image ปลอมเมื่อไม่มีรูปจริง** เพราะยังไม่มี Asset ภาพจริงของเว็บไซต์)
3. **Breadcrumb** — อัปเกรดเป็น Semantic `<nav aria-label="breadcrumb"><ol>` ทุกหน้า (Activities Index/Detail, Sitemap) — Home ไม่ใส่ตามธรรมเนียม UX มาตรฐาน (Root Page ไม่ต้องมี Breadcrumb ชี้ตัวเอง)
4. **Empty State** — รวมเป็นข้อความเดียวกันทั้งเว็บไซต์ "ยังไม่มีข้อมูลในขณะนี้" + ปุ่มกลับหน้าหลัก ผ่าน `renderEmptyState()`
5. **Performance** — เพิ่ม `defer` บน Script, `loading="lazy"` บนรูปทั้งหมด, ตรวจสอบแล้วไม่มี CSS/JS ซ้ำ
6. **Accessibility** — พบและแก้ `outline: none` ที่ Search Box ไม่มี Focus Indicator ทดแทน (เพิ่ม `:focus-within`), เพิ่ม Global `:focus-visible` Style, ตรวจ alt/aria-label ครบทุกจุดอยู่แล้ว
7. **Security Re-check** — ตรวจ Output ทุกจุด Escape ด้วย `e()` ครบ, GET Parameter ผ่าน `(int)` Cast ครบ, รูปภาพผ่าน `uploadUrl()` ครบ ไม่มี Path Traversal (ชื่อไฟล์มาจาก UploadHelper สุ่มเสมอ)

Testing: `php -l` PASS 12/12 ไฟล์, CSS Brace Balanced (163/163), ทดสอบ 404/Empty State/OG Meta/Breadcrumb ผ่าน HTTP จริงทั้งหมด, Regression ครบทุกโมดูล Admin (9 โมดูล + Dashboard คืน 302 ตามปกติเมื่อไม่ Login) และ Public Website ไม่ถูกกระทบ

**ข้อมูลทดสอบทั้งหมดถูกลบออกจากฐานข้อมูลและไฟล์อัปโหลดหลังทดสอบเสร็จทุกครั้ง**

รอผู้ใช้อนุมัติ Commit/Push (ยังไม่ Commit/Push ตามคำสั่ง)

---

## Public Website — Stage 2: Content Modules (News, Legislation, Documents, Gallery, Departments, Employees, Global Search) ✅ Completed

สร้างหน้า Public ที่เหลือทั้งหมดให้ครบตามลิงก์ที่ Nav/Footer/Sitemap/Quick Menu เตรียมไว้ตั้งแต่ Stage 1 (เดิมกด 404) วิเคราะห์ทั้งระบบก่อนแล้วขออนุมัติแผน แบ่งทำทีละ Stage (2.1–2.7) หยุดรายงานผล/ทดสอบ/รออนุมัติทุก Stage ตาม Workflow เดียวกับ Phase 13 — **ไม่มีการแก้ไข Database Schema/Migration ตลอดทั้ง Stage 2** (Reuse Model เดิม 7 ตัวทั้งหมด: `NewsModel`, `LegislationModel`, `DocumentModel`, `GalleryModel`, `DepartmentModel`, `EmployeeModel`, `ActivityModel`) และ **ไม่กระทบฝั่ง Admin แม้แต่ไฟล์เดียว**

### Stage 2.1 — News + Shared Component Layer

สร้าง `app/helpers/public_components.php` เป็นครั้งแรก (Foundation ที่ทุก Stage ถัดไปใช้ร่วมกัน): `renderBreadcrumb()`, `renderPageHeader()`, `renderCard()`, `renderPagination()`, `renderDetailMeta()` — ป้องกันการเขียน Markup ซ้ำในทุกโมดูล

ไฟล์ใหม่: `PublicNewsController.php`, `app/views/public/news/{index,detail}.php`, `public/news/{index,detail}.php`

### มาตรฐานเพิ่มเติมก่อน Stage 2.2 (ใช้ตลอดทุก Stage ที่เหลือ)

ผู้ใช้กำหนดให้ทุก Detail Page รองรับ Previous/Next Navigation, Back to List, Related Items, SEO ครบ (canonical/OG/Twitter Card/JSON-LD), Accessibility, Performance (Lazy Loading), Security — เพิ่ม Component ใหม่ใน `public_components.php`: `findAdjacent()`, `renderPrevNextNav()`, `renderRelatedItems()`, `renderBackToList()` และ Retrofit News ให้ใช้ Component ชุดใหม่ทั้งหมด — ขยาย `app/includes/public_header.php` เพิ่ม canonical, Twitter Card, JSON-LD (`GovernmentOrganization` + `BreadcrumbList` จาก Array เดียวกับที่ใช้วาด Breadcrumb จริง ไม่ต้องกำหนดข้อมูลซ้ำสองที่)

### Stage 2.2 — Legislation

ไฟล์ใหม่: `PublicLegislationController.php`, `app/views/public/legislation/{index,detail}.php`, `public/legislation/{index,detail}.php` — ใช้ Shared Component ทั้งหมด ไม่มี Markup ใหม่

### Stage 2.3 — Documents

เพิ่ม Component ใหม่ `renderDocumentCard()` (โครงสร้างการ์ดเฉพาะ Documents เพราะไม่มี Detail Page และปุ่ม Download ไม่ใช่ Pattern การ์ดทั้งใบเป็นลิงก์แบบ `renderCard()`) และ `formatFileSize()` ใน `app/helpers/functions.php` — ตรวจ `is_file()` ก่อนแสดงปุ่มดาวน์โหลดเสมอ (ไฟล์หาย → ข้อความ "ไฟล์ไม่พร้อมให้บริการ" แทน ไม่เกิด Warning)

ไฟล์ใหม่: `PublicDocumentController.php`, `app/views/public/documents/index.php`, `public/documents/index.php`

### Stage 2.4 — Gallery (Lightbox)

โมดูลที่ซับซ้อนที่สุด — Photo Grid + Lightbox แบบ Vanilla JS ล้วน (Fullscreen, Prev/Next, ESC, Click-Outside, Keyboard Arrow, Touch Swipe, Focus Trap, Restore Focus) Refactor `public/assets/js/public.js` เป็นโครงสร้าง Module (`initNavigation()` ครอบ Nav เดิม + `initLightbox()` ใหม่) เรียกผ่าน `DOMContentLoaded` จุดเดียว — ขยาย `renderCard()` แบบ Backward-compatible เพิ่ม `attrs`/`actionLabel` (Optional, Default เดิมไม่กระทบ News/Legislation) สำหรับผูก Lightbox Data-attribute และข้อความปุ่มที่ถูกบริบท

**บั๊กที่พบและแก้ไข**: ทดสอบ Lightbox ด้วย Headless Chrome + Chrome DevTools Protocol จำลอง Click/Keyboard จริง (ไม่ใช่แค่อ่าน HTML) พบว่า `closeBtn.focus()` ถูกเรียกก่อนที่ Class `.active` (คุม `visibility`) จะถูกเพิ่มใน `requestAnimationFrame` ถัดไป ทำให้ Browser เพิกเฉยการโฟกัสแบบเงียบๆ เพราะ Element ยังมองไม่เห็นอยู่ ณ ขณะนั้น — แก้โดยย้าย `.focus()` เข้าไปใน Callback เดียวกับการเพิ่ม `.active` ทดสอบซ้ำผ่านทุกกรณี (เปิด/ปิด/Prev-Next/Focus Trap วนสองทิศทาง/Escape คืน Focus/คลิกพื้นหลังปิด/คลิกภาพไม่ปิด)

ไฟล์ใหม่: `PublicGalleryController.php`, `app/views/public/gallery/index.php`, `public/gallery/index.php`

### Stage 2.5 — Departments

ไฟล์ใหม่: `PublicDepartmentController.php`, `app/views/public/departments/{index,detail}.php`, `public/departments/{index,detail}.php` — ไม่มีคอลัมน์รูปภาพและไม่มีความสัมพันธ์กับ Employee (ไม่มี `department_id`) จึง Detail Page ไม่มี Contact Box/Related Links (ไม่ใช้ข้อมูลปลอมทดแทน) ทดสอบ XSS/SQL Injection/Path Traversal ผ่าน `id` Parameter จริง — ไม่พบ Bug

### Stage 2.6 — Employees

**การตัดสินใจสำคัญด้าน Privacy**: ตรวจ Schema จริงพบคอลัมน์ `phone`/`email` แต่ไม่มี Field แยกระหว่างข้อมูลติดต่อสาธารณะกับข้อมูลภายใน — สอบถามผู้ใช้แล้วได้รับคำตอบให้ **ไม่แสดง phone/email/address/birth_date บน Public** ด้วยเหตุผลเดียวกัน → Public แสดงเฉพาะ ชื่อ-นามสกุล/ตำแหน่ง/รูปภาพ เท่านั้น ไม่มีชื่อแผนก (ไม่มี Relationship จริงในฐานข้อมูล)

ไฟล์ใหม่: `PublicEmployeeController.php`, `app/views/public/employees/{index,detail}.php`, `public/employees/{index,detail}.php` — ทดสอบ Privacy Leak ด้วยข้อมูลจริง (phone/email/birth_date/address) ยืนยันไม่หลุดแม้แต่จุดเดียว

### Stage 2.7 — Global Search

ค้นหาข้าม 7 Module พร้อมกัน (Activities/News/Legislation/Documents/Gallery/Departments/Employees) แบบ Section-based ผ่าน `?q=` (Bookmark ได้) — Reuse `keyword` Filter ที่มีอยู่แล้วในทุก Model (`buildWhere()`) 100% ไม่แก้ Model ใดๆ — เพิ่ม Optional Parameter `$pageParam` ให้ `renderPagination()` (Default `'page'` เดิมไม่กระทบ 6 โมดูลที่ใช้อยู่ก่อน) เพื่อให้แต่ละ Section มี Pagination อิสระ (`news_page`, `gallery_page` ฯลฯ) โดยคง `q` ไว้เสมอ — Section ที่ไม่มีผลลัพธ์ถูกซ่อนอัตโนมัติ, ไม่มีผลลัพธ์เลยใช้ Empty State กลาง, Gallery Section ใน Search ใช้ Lightbox เดียวกับ Stage 2.4 ได้ทันทีโดยไม่แก้ JS

ไฟล์ใหม่: `PublicSearchController.php`, `app/views/public/search/index.php`, `public/search.php`

ทดสอบครบตาม Checklist: หลาย Module/Module เดียว/ไม่มีผลลัพธ์/ภาษาไทย/อังกฤษ/**ลาว** (`ກິດຈະກໍາ`)/XSS/SQL Injection/Keyword ยาวผิดปกติ (Cap 150 ตัวอักษร)/Keyword ว่าง/Space หลายตัว (Normalize เป็นช่องเดียว)/URL Encode — PASS ทั้งหมด (พบปัญหาการทดสอบ 1 จุดไม่ใช่ Bug ของแอป — ดูหัวข้อ Final Quality Review)

### Final Quality Review (ก่อน Commit)

ตรวจซ้ำทั้ง Stage 2 ครบ 8 หัวข้อ: php -l, Regression, Security, Performance, Accessibility, SEO, Documentation, Git Review

- **php -l**: PASS ทุกไฟล์ (33 ไฟล์ที่สร้าง/แก้ไขใน Stage 2)
- **Regression**: Public 17 หน้า (รวม 404/Invalid ID ทุกโมดูล) → ถูกต้องครบ, Admin 11 จุด (รวม Dashboard/Login) → 302/200 ตามปกติ — พบ 1 จุดที่ไม่เกี่ยวกับ Stage 2: URL ที่ไม่มีไฟล์จริงเลย (เช่น `/nonexistent-page-xyz.php`) ตกไปที่ `.htaccess` Catch-all Rewrite เข้า Home แทนที่จะเป็น 404 จริง — เป็นพฤติกรรมเดิมของ Stage 1 (Routing/Home อยู่นอกขอบเขต Stage 2 ตามข้อกำหนด "ไม่แตะ Home") บันทึกไว้เป็น Known Issue ไม่ใช่ Regression ที่เกิดจากงานนี้
- **Security**: ทดสอบ XSS ซ้ำครบ Gallery/Documents/Activities (ผ่าน Search) ด้วย Payload จริง → Escape ถูกต้อง 100%, ยืนยันไม่มี Form แบบ POST ใน Public Stage 2 เลย (มีแต่ Search Box แบบ GET) จึงไม่มีความเสี่ยง CSRF ในส่วนนี้, Path Traversal ตรวจทุกจุดที่ต่อ Path ไฟล์ (Documents/Gallery/Search) ยืนยัน Filename มาจาก DB เท่านั้น ไม่มีจุดรับ Path จาก User
- **Performance**: ทุก Function ใน `public_components.php`/`functions.php` ถูกเรียกใช้งานจริง (ไม่มี Dead Code), DOMContentLoaded Listener มีจุดเดียวใน `public.js`, ไม่พบ `console.log`/`var_dump`/`print_r`/`dd()`/TODO/FIXME/DEBUG ใน Stage 2 ทั้งหมด
- **Accessibility**: ไม่มี `onclick` แบบ Inline, ไม่มี `tabindex` ผิดปกติ, ทุก Interactive Element เป็น `<a>`/`<button>` จริง (Keyboard-operable โดยธรรมชาติ)
- **SEO**: `sitemap.php` (Quick Menu) มีลิงก์ครบทุกโมดูลรวม Search แล้วตั้งแต่ Stage 1 (ไม่มี Dead Link) — หมายเหตุ: โปรเจกต์ยังไม่มี `sitemap.xml`/`robots.txt` (XML Sitemap สำหรับ Search Engine) อยู่นอกขอบเขต Stage 2 เดิม บันทึกไว้เป็นข้อเสนอสำหรับ Task ในอนาคต

**ไม่พบ Bug ใหม่ในรอบ Final Quality Review** (Bug เดียวที่พบตลอดทั้ง Stage 2 คือ Lightbox Focus ใน Stage 2.4 ซึ่งแก้ไปแล้วก่อนรายงานผล Stage นั้น)

รอผู้ใช้อนุมัติ Commit/Push (ยังไม่ Commit/Push ตามคำสั่ง)

---

## Next Task

รอคำสั่งอนุมัติ Commit/Push Public Website Stage 2 จากผู้ใช้ จากนั้นกลับไปทำ Admin Panel Redesign Module 2 (Employees) ที่ค้างไว้ หรือ Task ถัดไปตามคำสั่งผู้ใช้

---

**Last Updated:** 2026-07-18 — Public Website Stage 2 (Content Modules 2.1–2.7) + Final Quality Review Completed
