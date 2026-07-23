# DTC Website — Project Status

**Project:** TCSP Administration System (Department of Technology and Communication of Savannakhet Province)
**Architecture:** PHP MVC (Native PHP 8+) + PDO + MariaDB 10.4.x
**Current Phase:** Remove Department Code (ลบ `code`/`code_active` ออกจากตาราง `departments` ถาวร) Completed ✅
**Next Phase:** รอผู้ใช้อนุมัติ Commit/Push งานนี้ รวมถึง Admin Panel Design System v2 (DS1–DS5) + v3 (UI-1–UI-6) ที่ค้างอยู่ก่อนหน้า จากนั้นดำเนินการตาม Task ถัดไปที่ผู้ใช้กำหนด

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

Status : Committed (`6bec1a9`)

---

## Admin Panel Design System v2 (Stage DS1–DS5) ✅ Completed

ยกระดับ Admin Panel ทั้งระบบสู่มาตรฐาน "Modern Government CMS" ตาม Design Language อ้างอิง (GitHub/Vercel/Supabase/Clerk/Linear/Notion/shadcn — เป็นแรงบันดาลใจเท่านั้น ไม่ Copy UI) โดยมีเงื่อนไขบังคับตลอดทั้ง 5 Stage: ห้ามแก้ Business Logic/Permission/Route/Controller/Model/Database/Authentication, ห้ามใช้ Framework (Bootstrap/Tailwind ฯลฯ), ห้าม Rewrite ไฟล์ทั้งไฟล์ (Incremental Refactor เท่านั้น), ต้องวิเคราะห์โค้ดเดิมและ Reuse Component ก่อนสร้างใหม่ทุกครั้ง — งานนี้เข้ามาแทนที่ "Admin Panel — UI/UX Redesign (อ้างอิง design/admin.jpeg)" รุ่นก่อนหน้าที่ทำค้างไว้แค่ Module Dashboard (ดูหัวข้อ CHANGELOG) ด้วยแนวทางที่ครอบคลุมครบทั้ง 9 โมดูล + Dashboard อย่างเป็นระบบกว่า

### Stage DS1 — Design System Foundation

สร้างโครงสร้างพื้นฐานที่ทุก Stage ถัดไปใช้ร่วมกัน ยังไม่ Retrofit View ใดๆ: `app/config/admin_menu.php` (Single Source of Truth ของเมนู Sidebar/Breadcrumb แทน Array Literal เดิม), `app/helpers/admin_components.php` (Component Layer ตั้งชื่อฟังก์ชันขึ้นต้น `renderAdmin*`/`findAdmin*` ทั้งหมด เพื่อไม่ให้ชนกับ `public_components.php`), ขยาย `app/helpers/icons.php` (9 ไอคอนใหม่), ขยาย `admin.js` (`initModal()`/`initConfirmDialog()`/`initToast()`/`initFormLoadingState()` เรียกจาก `DOMContentLoaded` จุดเดิม), เพิ่ม Layer ใหม่ต่อท้าย `admin.css`/`crud.css` แบบ Additive ล้วน (ไม่แก้ Rule เดิมแม้แต่จุดเดียว)

**บั๊กที่พบและแก้ไข**: `renderEmptyState()` ที่สร้างใหม่ชนชื่อกับฟังก์ชันเดิมใน `functions.php` (Public, Phase 13) ทำให้ Fatal Error "Cannot redeclare" ล่มทั้งเว็บไซต์ (ทั้ง Admin และ Public เพราะโหลดผ่าน `bootstrap.php` ร่วมกัน) พบจากการทดสอบ Login จริง — แก้โดยเปลี่ยนชื่อเป็น `renderAdminEmptyState()` และตรวจสอบ Inventory ชื่อฟังก์ชันทั้งหมดซ้ำอีกครั้งเพื่อยืนยันไม่มีจุดชนอื่น

ทดสอบ: Login จริงผ่าน PHP cURL ยืนยันทั้ง Admin และ Public โหลดได้ปกติหลังแก้บั๊ก, `php -l` PASS ทุกไฟล์

### Stage DS2 — Retrofit: Dashboard, Departments, Employees

Retrofit 3 โมดูลแรกด้วย Component จาก DS1: `renderAdminPageHeader()`, `renderAdminSectionCard()` (ครอบ Field เดิมด้วย Section เช่น "ข้อมูลทั่วไป"/"สถานะ"), `renderBadge()`, `renderAdminPagination()`, `renderAdminEmptyState()`, `.btn-icon` Action Button, `data-confirm-modal` แทน `window.confirm()` เดิม (คง `data-confirm`/Handler เดิมไว้ไม่แตะ เพื่อ Backward Compatibility) — Dashboard ถือเป็นแค่ Template อ้างอิง ไม่ต้องคงหน้าตาเดิม 100% ตามที่ผู้ใช้อนุมัติไว้

**บั๊กที่พบและแก้ไข**:
1. **Sticky Table Header ทำแถวแรกหาย**: `position:sticky` บน `<th>` ร่วมกับ `border-collapse:collapse` ทำให้แถวข้อมูลแรกมองไม่เห็นทั้งที่ DOM/Layout ถูกต้อง (ยืนยันด้วย `getBoundingClientRect()` และ Screenshot Clip จริง) ลองแก้ด้วย `border-collapse:separate` แล้วไม่หาย — ตัดสินใจถอด Sticky Header ออกจาก View จริง (Departments/Employees) คง CSS ไว้พร้อม Comment "Known Issue" ผู้ใช้ยืนยันภายหลังว่ายังไม่ต้องนำกลับมาจนกว่าจะมีวิธีทดสอบผ่านทุก Browser จริง
2. **Form Field ไหลติดกัน**: `.admin-section-card-body` ไม่มี `display:flex;flex-direction:column` (มีแค่ `.data-form` เดิมที่มี Property นี้โดยตรง) ทำให้ Label/Input เรียงเป็นข้อความยาวแทนที่จะเป็นบรรทัดแยก — เพิ่ม Rule ใหม่แก้

ทดสอบ: Login จริง + CDP Screenshot ยืนยัน Layout ถูกต้องหลังแก้ทั้ง 2 บั๊ก, Regression Departments/Employees/Dashboard เดิมไม่เสีย

### Stage DS3 — Retrofit: News, Legislation, Activities

Retrofit ตาม Pattern เดียวกับ DS2 เป๊ะ (Reuse Component ทั้งหมด ไม่มี Component ใหม่) ยึดตามคำสั่งชัดเจนว่ายังไม่นำ Sticky Header กลับมาใช้

**บั๊กที่พบและแก้ไข**: News List (8 คอลัมน์ + หัวข้อข่าวภาษาไทยจริงยาว) ทำให้เกิด Horizontal Overflow ระดับหน้าทั้งที่ `.table-wrapper` มี `overflow-x:auto` อยู่แล้ว — Root Cause คือ `.admin-content` (Flex Item ของ `.admin-layout`) ไม่มี `min-width` กำหนดไว้ Browser จึงไม่ยอมบีบเล็กกว่าความกว้างเนื้อหาจริง (Flexbox Overflow บั๊กมาตรฐานที่มีการบันทึกไว้ทั่วไป) — แก้ด้วย `.admin-content { min-width: 0; }` ยืนยันด้วยการวัด `scrollWidth`/`clientWidth` ก่อน-หลัง (1516→1440 ตรงกับ Viewport พอดี) และ Screenshot ยืนยันตารางมี Scrollbar ภายในตัวเองแทนที่จะดันทั้งหน้า

ทดสอบ: Regression News/Legislation/Activities ครบ, Responsive 375px ยืนยันไม่มี Overflow อีก

### Stage DS4 — Retrofit: Gallery, Documents, Users, Activity Log + Compliance Audit

Retrofit 4 โมดูลสุดท้าย พร้อม Audit ข้าม 9 โมดูลตามเงื่อนไขที่ผู้ใช้กำหนดเพิ่ม (ไม่มี Inline Style/JS, ไม่มี HTML ซ้ำ, บังคับใช้ Component ทุกจุดตามรายการที่กำหนด) — พบและแก้ไข 3 จุด:

1. **DRY Cleanup (Documents)**: Closure `$formatFileSize` ในไฟล์ซ้ำกับ `formatFileSize()` ที่มีอยู่แล้วใน `functions.php` (เพิ่มไว้ตั้งแต่ Public Stage 2) — ลบ Closure ออก เปลี่ยนไปเรียก Global Function แทน
2. **Inline Style หลงเหลือ**: `departments/form.php` มี `style="text-transform:uppercase"` บน Input รหัสแผนก — ย้ายเป็น Class `.input-uppercase` ใหม่ใน `crud.css`
3. **Horizontal Overflow บนมือถือ (Activity Log)**: คอลัมน์ "รายละเอียด" เป็นจุดเดียวในทุกโมดูลที่ไม่มี Class `.truncate` ทำให้ตารางกว้างผิดปกติจาก Log จริงที่มีข้อความยาว ประกอบกับ `min-width:0` จาก DS3 อย่างเดียวไม่พอในกรณีนี้ — เพิ่ม `.truncate` ที่คอลัมน์ดังกล่าว และเพิ่ม `.admin-content { overflow-x: hidden; }` เป็นชั้นป้องกันเสริม
   - **ข้อผิดพลาดของวิธีทดสอบที่แก้ไขได้เอง**: การทดสอบซ้ำครั้งแรกใช้ `document.body.scrollWidth` เทียบ `clientWidth` แล้วเห็นตัวเลขเดิมไม่เปลี่ยนหลังแก้ ทำให้เข้าใจผิดว่าบั๊กยังไม่หาย ตรวจสอบพบว่า `overflow-x:hidden` **ไม่เปลี่ยนค่า** `scrollWidth` (เปลี่ยนแค่พฤติกรรม Scroll จริง) — แก้วิธีทดสอบเป็น `window.scrollTo(9999,0)` แล้วอ่าน `scrollX` กลับมา (ยืนยันเป็น 0 = Scroll ไม่ได้จริง) ร่วมกับใช้ `document.documentElement.scrollWidth` แทน `document.body.scrollWidth`

พบข้อมูลจริง 1 รายการ (Document ID 20 "dadawd") ที่ถูกสร้างผ่าน Admin UI จริงระหว่างช่วงทดสอบ (ไม่ใช่ข้อมูลทดสอบของ Claude) ยืนยันจาก Activity Log ตรงกับ Session การทดสอบ — สอบถามผู้ใช้ก่อนลบตามหลัก Safety แทนที่จะลบเงียบๆ ผู้ใช้ยืนยันเป็นข้อมูลของตัวเองและอนุมัติให้ลบ จึงลบทั้งแถวฐานข้อมูลและไฟล์อัปโหลดแล้ว

ทดสอบ: Regression 9 โมดูลครบ, Responsive 375px ยืนยันด้วยวิธีที่แก้ไขแล้วทั้ง Activity Log และ News

### Stage DS5 — Final Quality Review (ก่อน Commit)

ตรวจซ้ำทั้ง 5 Stage ครบ 10 หัวข้อตามที่ผู้ใช้กำหนด: Full Code Audit, Duplicate Audit, Regression, Security, Accessibility, Responsive, Performance, Documentation, Git Review, Final Summary — ไม่พบ Bug ใหม่ในรอบนี้ (Bug ทั้งหมดที่พบตลอด Stage DS1–DS4 ถูกแก้และรายงานไปแล้วในแต่ละ Stage) จึงไม่มีการแก้โค้ดเพิ่มเติมในรอบ DS5 ตามเงื่อนไข "หากไม่พบ Bug จริง ห้ามแก้โค้ดเพิ่ม"

- **Full Code Audit**: ตรวจ TODO/FIXME/DEBUG/`console.log`/`var_dump`/`print_r`/`dd()` — ไม่พบ; พบ `exit;` 3 จุด (`functions.php` ใน `redirect()`, `AuthMiddleware.php` 2 จุด) ยืนยันเป็นโค้ด Control-flow ที่จำเป็นจาก Phase ต้นๆ ไม่ใช่ Debug หลงเหลือ
- **Duplicate Audit**: ตรวจฟังก์ชันซ้ำใน `app/helpers`/`app/core` (32 ฟังก์ชัน ไม่ซ้ำ), DOMContentLoaded จุดเดียวทั้ง `admin.js`/`public.js`, Selector ที่ปรากฏซ้ำใน CSS (`.admin-content` ×3, `.quick-action-btn` ×2, `.admin-topbar` ×2, `.btn-link` ×2, `.alert` ×2, `.admin-section-card-body` ×2) ตรวจ Property จริงทีละจุดยืนยันเป็น Additive Layer ตามหลักการที่ตั้งไว้ (คนละ Property กัน) ไม่ใช่การซ้ำโดยไม่ตั้งใจ, ตรวจ View ทุกไฟล์ไม่พบ HTML ที่ควรใช้ Component แต่เขียน Markup มือแทน
- **Regression**: Public 18 หน้า + Admin 21 จุดผ่าน HTTP จริง (Login จริง) ไม่มี PHP Warning/Notice/Fatal Error จุดใดเลย, ทดสอบ Full CRUD Cycle จริง (Create → Edit → Soft Delete) กับ Departments ยืนยัน Field Name/CSRF Token/Redirect/Flash Message ไม่เสียหลัง Retrofit, Empty State Component แสดงถูกต้องเมื่อค้นหาไม่พบผลลัพธ์
- **Security**: ยืนยัน `e()` Escape ทำงานกับ String ที่ Concatenate ก่อน Output จริง (ไม่มี XSS จากการต่อ String ใน `renderAdminPageHeader()`/`data-confirm-modal`), CSRF Token ครบทุกฟอร์ม POST, Self-Delete Protection ของ Users ยังทำงาน, ไม่มี SQL ดิบใน View, Path การอัปโหลดไฟล์ยังใช้ชื่อไฟล์ที่ Server สุ่มเองไม่รับจาก User
- **Accessibility**: Modal ใช้ `role="dialog"` + `aria-modal`/`aria-labelledby`, Confirm Dialog ใช้ `role="alertdialog"` + เพิ่ม `aria-describedby`, Focus Trap + Escape + Restore Focus ให้ Trigger เดิมครบทั้งสองแบบ, Breadcrumb Semantic (`<nav aria-label="breadcrumb"><ol>` + `aria-current="page"`), `aria-label` บนปุ่ม Icon-only ครบทุกโมดูล, Global `:focus-visible`
- **Responsive**: ทดสอบ 375px จริงผ่าน Headless Chrome + Chrome DevTools Protocol (Users/Documents/Activities/Gallery/Employees/Departments Form) ด้วยวิธีที่แก้ไขแล้วจาก DS4 (`scrollTo` + อ่าน `scrollX`) ยืนยันไม่มี Horizontal Scroll ทุกหน้าที่สุ่มตรวจ ยืนยันบั๊กจาก DS3/DS4 แก้ที่ระดับ Container กลาง (`admin-content`) จึงครอบคลุมทุกโมดูลจริงไม่ใช่แค่หน้าที่เจอบั๊กตอนแรก
- **Performance**: ไม่มี Inline `style=`/`<script>` หลงเหลือจุดใด, ไม่มี N+1 Query (ไม่มี Loop เรียก Query ซ้ำใน Controller ที่ไม่ถูกแตะเลยตลอดงานนี้), CSS Brace Balanced ทั้ง `admin.css` (130/130) และ `crud.css` (102/102)

Testing: `php -l` PASS ทั้ง 28 ไฟล์ที่สร้าง/แก้ไขตลอด Stage DS1–DS5

Status : รอผู้ใช้อนุมัติ Commit/Push (ยังไม่ Commit/Push ตามคำสั่ง)

---

## Admin Panel Design System v3 (Stage UI-1–UI-6) ✅ Completed

ต่อยอดจาก Design System v2 (DS1–DS5) ตาม Mockup ใหม่ (`design/Dashboard.png`) ยกระดับ Admin Panel ให้ใกล้เคียง Modern Government CMS Dashboard มากขึ้น (Hero Header, Stat Card แนวนอน, Quick Action Card, Segmented Control ฯลฯ) เงื่อนไขเหมือนเดิมทุกประการ: ห้ามแก้ Database/Model/Controller/Business Logic/Auth/Permission/Route/Public Website, ห้ามใช้ Framework, Incremental Refactor เท่านั้น, ต้อง Reuse Component เดิมก่อนสร้างใหม่ทุกครั้ง, หยุดรออนุมัติทุก Stage

### Stage UI-1 — Design System (Global Components)

เพิ่ม Layer ใหม่ต่อท้าย `admin.css`/`crud.css` แบบ Additive (ไม่แก้ Rule เดิม): Token สีใหม่ (Teal/Pink), `.admin-hero` (Hero Header Component ยังไม่ถูกเรียกใช้), `.quick-action-card`, `.segmented-control`, `.btn-success`/`.btn-danger`/`.btn-outline`/`.btn-fab`, Ripple แบบ CSS, `.glass-card` (Opt-in) — เพิ่ม `renderAdminHero()`, `renderAdminSegmentedControl()` ใน `admin_components.php` (Foundation เท่านั้น), เพิ่มไอคอน `bell`/`moon`/`trend-up`/`trend-down`, เพิ่ม `initSegmentedControl()` ใน `admin.js` — แก้ Topbar (`admin_header.php`) เพิ่มช่องค้นหา + ปุ่ม Dark Mode (ทั้งคู่ `disabled` เพราะยังไม่มี Backend รองรับจริง ตามที่ผู้ใช้ตอบใน AskUserQuestion)

**บั๊กที่พบและแก้ไข**: ปุ่มกระดิ่งแจ้งเตือนใช้ `icon('log')` (รูปเอกสาร) แทนที่จะเป็นรูประฆัง — เพิ่มไอคอน `bell` จริงและสลับให้ถูกต้อง

### Stage UI-2 — Dashboard Redesign

Retrofit `dashboard.php` เต็มรูปแบบด้วย Component จาก UI-1 (ไม่แก้ `DashboardController`/`DashboardModel` แม้แต่บรรทัดเดียว — ข้อมูลเดิมทั้งหมด): `renderAdminHero()` (Greeting ตามช่วงเวลา/ชื่อ/Role Badge/วันที่-เวลาแบบไทย), Stat Card แนวนอน 7 สีไม่ซ้ำ (เพิ่ม Token Indigo เป็นสีที่ 7), Quick Action Card, Timeline แบบ Scrollable, Segmented Control 7/30/90 วันที่การ์ด Analytics (30/90 แสดง Empty State เพราะ Controller คำนวณให้แค่ 7 วัน — ตกลงกับผู้ใช้แล้วว่ายังไม่เชื่อม Backend ใน Stage นี้), Summary Card คำนวณจาก `$dailyCounts` ที่มีอยู่แล้วในระดับ View (Total/Peak/Average) — ขยาย `renderAdminSectionCard()` เพิ่ม Optional Parameter `$headExtra` (Backward Compatible)

**การตัดสินใจสำคัญ**: Trend/Percentage บน Stat Card ไม่มีข้อมูลเปรียบเทียบจริงในระบบ (Model ให้แค่ยอดปัจจุบัน) จึงแสดงเป็นสถานะกลาง "ยังไม่มีข้อมูลเปรียบเทียบ" (สีเทา) แทนการมั่วตัวเลขปลอม

### Stage UI-3 — Departments, Employees

ตรวจโค้ดเดิมพบว่าหน้า List ทั้งสองผ่านครบตาม Checklist อยู่แล้วจาก DS2 (ไม่แก้) — เพิ่มเฉพาะ Section "การดำเนินการ" ห่อปุ่ม Save/Cancel ในฟอร์มทั้งสอง (Reuse `renderAdminSectionCard()`) และ Position Badge ในตาราง Employees (Reuse `renderBadge()`) — **ข้าม Section "สถานะ" ของ Employees** เพราะตาราง `employee` ไม่มีคอลัมน์ Status จริง (สร้าง Field ปลอมขัดกับเงื่อนไขห้ามแก้ Database)

### Stage UI-4 — News, Activities, Legislation

แยก Section "เนื้อหา" ออกจาก "ข้อมูลทั่วไป" เดิมในฟอร์มทั้ง 3 โมดูล + เพิ่ม Section "การดำเนินการ" (Reuse `renderAdminSectionCard()` เดิมทั้งหมด) เพิ่ม Accent Icon Chip ที่ Page Header ของ List (Reuse `.stat-icon-{blue/orange/indigo}` จาก Dashboard ผ่าน Parameter `$extra` เดิมของ `renderAdminPageHeader()`) — **ข้าม Section "รูปภาพ" ของ Legislation** เพราะตารางไม่มีคอลัมน์รูปภาพจริง

### Stage UI-5 — Gallery, Documents, Users, Activity Log

ตรวจโค้ดเดิมทั้ง 7 ไฟล์ (List/Form ของ 4 โมดูล) พบว่าผ่านครบทุกข้อกำหนดอยู่แล้ว (Page Header/Search/Filter/Badge/Pagination/Empty State/Action Button/Role Badge/Action Type Badge ทุกจุดใช้ `renderBadge()` อยู่แล้ว ไม่มี Badge Component ใหม่) — **ไม่มีการแก้โค้ดแม้แต่บรรทัดเดียว** ตามเงื่อนไข "หากไม่พบ Bug ห้ามแก้โค้ด"

### Stage UI-6 — Final Quality Review

ตรวจซ้ำทั้ง 5 Stage ครบ 10 หัวข้อ ไม่พบ Bug ใหม่ จึงไม่มีการแก้โค้ดเพิ่มเติมในรอบนี้ (นอกจาก Documentation)

- **Full Code Audit**: ตรวจ TODO/FIXME/DEBUG/`console.log`/`var_dump`/`print_r`/`dd()`/`exit`/`die` ทั่ว `app/` และ `public/assets` — ไม่พบเลย
- **Duplicate Audit**: ไม่พบฟังก์ชัน PHP/JS ซ้ำชื่อ (ตรวจทั้ง `app/helpers/*.php` และ `admin.js`) — พบ Selector CSS ที่ปรากฏซ้ำ 13 จุดใน `admin.css`/`crud.css` ตรวจทีละคู่ยืนยันเป็น Additive Layer ตามหลักการเดิม (คนละ Property หรือ Override ที่ตั้งใจและมีผลถูกต้องตามทดสอบจริง เช่น `.stat-card` เปลี่ยน `flex-direction` จาก column→row) ไม่ใช่ Duplicate โดยไม่ตั้งใจ — พบ Pattern ซ้ำระดับ Markup/PHP ที่มีมาก่อน Stage นี้ (Closure `$sortUrl`/`$sortIndicator` ซ้ำทุกโมดูล List, Filter Bar Markup คล้ายกันทุกโมดูล) บันทึกเป็น Technical Debt ที่มีอยู่แล้ว ไม่ใช่สิ่งที่เกิดจากงานนี้ ไม่แก้ตามเงื่อนไข "ไม่ใช่ Bug จริง"
- **Regression**: Login จริงผ่าน PHP cURL ทดสอบ 19 หน้า (Dashboard + 10 โมดูล List/Form + Change Password) → 200 ไม่มี Error/Warning/Notice จุดใดเลย, ทดสอบ Full CRUD Cycle จริง (Create → Search → Delete) กับ Departments ยืนยัน Business Logic/CSRF/Redirect/Activity Log ไม่เสียหลัง Retrofit ทั้ง 5 Stage, ทดสอบ Logout ยืนยัน Session ถูกทำลายจริง (Redirect กลับ Login)
- **Security**: ยืนยัน Escape Output ผ่าน `e()` ครบทุกจุดที่ Interpolate ตัวแปร (ไม่พบ Raw Variable ที่ไม่ผ่าน `e()`/Cast `(int)`/Boolean-ternary), CSRF Token ครบทุกฟอร์ม POST (17 ไฟล์), Self-Delete Protection ของ Users ยืนยันด้วย Live Test จริง (ปุ่มลบหายไปจากแถวของตัวเอง)
- **Accessibility**: `disabled` (ไม่ใช่แค่ Visual) บน Control ที่ยังไม่ทำงานจริง (Search Box/Dark Mode Toggle), Focus-visible/ARIA เดิมจาก DS1–DS5 ไม่ถูกกระทบ
- **Responsive**: ตรวจ CSS ใหม่ทั้งหมดจาก UI-1–UI-4 ไม่พบ Fixed-width ที่ไม่มี Media Query รองรับ
- **Performance**: DOMContentLoaded จุดเดียวใน `admin.js`, ไม่มี Inline `style=`/`<script>`, พบ Component ที่ตั้งใจสร้างไว้ล่วงหน้ายังไม่ถูกเรียกใช้จริง (`.btn-fab`, `.btn-success`, `.glass-card`, `.stat-trend-up/down`, `trend-down` icon) — เป็น Foundation ที่ตั้งใจไว้ตั้งแต่ UI-1 (Pattern เดียวกับ `renderModal()`/`renderSkeletonRows()` จาก DS1 ที่ยังไม่ถูกใช้เช่นกัน) ไม่ใช่ Dead Code จาก Feature ที่ถูกลบ
- **Git Review**: `git status` สะอาด ไม่มีไฟล์ Temp/Backup/Debug หลงเหลือในโปรเจกต์

Testing: `php -l` PASS ทุกไฟล์ที่แก้ตลอด Stage UI-1–UI-5

Status : รอผู้ใช้อนุมัติ Commit/Push (ยังไม่ Commit/Push ตามคำสั่ง)

---

## Remove Department Code

ยกเลิกการใช้งาน Department Code (`code`) ทั้งระบบอย่างถาวรตามคำขอผู้ใช้ ดำเนินการครบ 4 Stage ตาม Workflow ที่ผู้ใช้กำหนด

### Stage 1 — Impact Analysis
วิเคราะห์ผลกระทบครบ 9 หัวข้อ (Database/Model/Controller/View/Helper/Other Module/Activity Log/Documentation/Regression) — สรุป: ไม่มี Foreign Key ผูกกับ `departments`, ไม่มี Module อื่นพึ่งพา code, จุดที่ต้องแก้จำกัดเฉพาะ Departments Module (5 ไฟล์โค้ด + 1 CSS) — ผู้ใช้อนุมัติให้ดำเนินการต่อ

### Stage 2 — Migration Proposal & Execute
สร้าง `database/migrations/013_departments_drop_code.sql` (DROP INDEX `departments_code_active_unique` → DROP COLUMN `code_active` → DROP COLUMN `code`) พบระหว่างวิเคราะห์ว่าคอลัมน์ `code` เดิมเป็น `NOT NULL` ไม่มี Default — หากแก้โค้ดก่อน Execute Migration จะทำให้ Create/Edit พังทันที จึงหยุดรายงานและขอการตัดสินใจจากผู้ใช้ — **ผู้ใช้เลือกให้ Execute Migration ก่อนเข้า Stage 3** จึง Backup ตาราง `departments` ทั้ง 13 แถวไว้ที่ `database/backups/departments_backup_20260723_pre_drop_code.sql` แล้ว Execute จริง ยืนยันด้วย `SHOW COLUMNS`/`SHOW INDEX` ว่าลบสำเร็จและข้อมูลยังครบ 13 แถว

### Stage 3 — Implementation
แก้ไข `DepartmentModel.php` (SELECT/INSERT/UPDATE/SORTABLE_COLUMNS/buildWhere ลบ code, ลบ method `codeExists()`), `DepartmentController.php` (ลบ Validation/Flash Message ของ code ทั้งหมดใน `validate()`), `admin/departments/form.php` (ลบช่องกรอกรหัสแผนก), `admin/departments/index.php` (ลบคอลัมน์ตาราง + ปรับ placeholder ค้นหา), `public/departments/detail.php` (ลบการแสดงรหัสแผนก), `crud.css` (ลบ `.input-uppercase` ที่กลายเป็น Dead CSS) — `php -l` PASS ทุกไฟล์, ตรวจ Grep ทั้งโปรเจกต์ยืนยันไม่มี `code`/`ລະຫັດພະແນກ` หลงเหลือ

### Stage 4 — Regression Test
- Model Regression ผ่าน PHP CLI กับฐานข้อมูลจริงหลัง Migration: 18/18 PASS (Create/Find/Update/SoftDelete ไม่มี code, nameExists, ค้นหาด้วยชื่อ, SQL Injection-style keyword ปลอดภัย, Sort Whitelist ปฏิเสธ `code` fallback เป็น `id`, ชื่อที่มี `<script>` เก็บ/ดึงคืนถูกต้อง)
- HTTP Smoke Test: หน้า Public Departments List/Detail/Search → 200 ไม่มี PHP Warning/Notice/Fatal Error; Route Admin Departments → 302 Redirect Login ตามปกติเมื่อไม่ได้ Authenticate
- ข้อมูลจริง 13 แถวไม่เปลี่ยนแปลงหลังทดสอบ (ทดสอบด้วยแถวชั่วคราวที่ลบออกทุกครั้ง)
- **ข้อจำกัด**: ไม่ได้ทดสอบ Full HTTP Login + CRUD ผ่าน Session จริง เพราะรหัสผ่าน Admin ปัจจุบันถูกเปลี่ยนไปแล้วจาก Default (`first_login = 0`) ในการทดสอบรอบก่อนหน้า และไม่ได้รับอนุญาตให้ Reset โดยไม่ขออนุมัติก่อน — ใช้การทดสอบระดับ Model แทนแบบครบทุก Method
- Documentation: อัปเดต `docs/DATABASE.md` (Schema ปัจจุบันของ `departments`), `docs/CHANGELOG.md` (Section ใหม่ "Remove Department Code"), `docs/PROJECT_STATUS.md` (Section นี้) — ไม่แก้ประวัติเดิมของ Phase 4/DS4 ที่เคยกล่าวถึง code

Status : Completed ✅ — รอผู้ใช้ตรวจสอบและอนุมัติ Commit/Push (ยังไม่ Commit/Push ตามคำสั่ง)

---

## Next Task

รอคำสั่งอนุมัติ Commit/Push Admin Panel Design System v2 (Stage DS1–DS5) และ v3 (Stage UI-1–UI-6) จากผู้ใช้ จากนั้นดำเนินการตาม Task ถัดไปที่ผู้ใช้กำหนด

---

**Last Updated:** 2026-07-23 — Remove Department Code Completed (Migration Executed, Regression PASS)
