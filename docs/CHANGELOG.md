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

## Public Website — Stage 1: Foundation + Layout + Home

### Added
- `public/assets/css/public.css`, `public/assets/js/public.js` — Design System และ Interactivity ของ Public Website (Vanilla CSS/JS ล้วน)
- `app/helpers/icons.php` — ชุดไอคอน Inline SVG กลาง
- `app/includes/public_header.php`, `app/includes/public_footer.php` — Header/Nav/Footer ใช้ร่วมทุกหน้า Public
- `app/controllers/PublicHomeController.php`, `app/views/public/home.php` — หน้า Home (Hero, Quick Menu, ข่าวล่าสุด)

### Changed
- `public/index.php` — เปลี่ยนจาก Phase-1 DB Connection Test Stub เป็น Front Controller เรียก `PublicHomeController` จริง
- `app/core/bootstrap.php` — เพิ่มการโหลด `app/helpers/icons.php` (Additive เท่านั้น)

### Reused
- `NewsModel::paginate()` (ของเดิม, ไม่แก้ไข), `BaseController`, `BaseModel`, `app/helpers/functions.php`

### Testing
- `php -l` PASS ทุกไฟล์ที่สร้าง/แก้ไข
- HTTP Testing PASS: Home Page 200, Empty State ข่าวถูกต้อง, ไม่พบ PHP Warning/Notice
- Regression PASS: Admin Panel (`login.php`, `admin.css`) ไม่ถูกกระทบ

Status : Stable

### Revision — ปรับ Home ให้ตรงกับ `design/index.png` แบบละเอียด

ผู้ใช้ชี้ว่า `design/index.png` มีรายละเอียดครบกว่า `design/image.png` ที่ใช้วิเคราะห์ตอนแรก และขอให้ตาม Reference ให้ใกล้เคียงที่สุด

#### Fixed
- **บั๊ก `site-header` ไม่สมส่วน**: `.site-brand`/`.main-nav a` ไม่มี `flex-shrink: 0` ทำให้ Flexbox บีบข้อความจนล้น/ทับกันเมื่อพื้นที่ไม่พอ — แก้โดยล็อกขนาดจริงของ Brand และแต่ละลิงก์เมนู พร้อมเพิ่ม `overflow-x: auto` (ซ่อน Scrollbar) เป็นทางออกสำรอง

#### Changed
- Top Bar: เพิ่มเวลาทำการ + ลิงก์ "บริการออนไลน์/คำถามที่พบบ่อย/แผนผังเว็บไซต์", เปลี่ยนช่องค้นหาเป็นพื้นขาว
- Header: ข้อความชื่อหน่วยงานให้ตรงกับภาพ, เมนู Active เปลี่ยนจากพื้นหลังเป็นขีดเส้นใต้, ตัด "กฎหมาย/ระเบียบ" ออกจากเมนูหลัก (ภาพต้นฉบับมี 8 รายการ) ย้ายไปไว้ที่ Footer แทน
- Hero: ปุ่มรองเปลี่ยนเป็นปุ่มขาวทึบ, Dots จาก 3 เป็น 5 จุด, จัดข้อความกึ่งกลางแนวตั้ง
- Quick Menu: เอาพื้นหลังไอคอนออก, หัวข้อเปลี่ยนเป็นสีน้ำเงินกรมท่า
- News Card: Date Badge เปลี่ยนจากพื้นเข้มเป็นพื้นขาว + เงา
- Stats Bar: ปรับคำต่อท้ายป้ายให้ตรงภาพ

### Added
- `app/controllers/PublicPageController.php`, `app/views/public/sitemap.php`, `public/sitemap.php` — หน้าแผนผังเว็บไซต์ (Static, ไม่มี Database) เพื่อให้ลิงก์ใน Top Bar ใช้งานได้จริง

### Known Deliberate Deviations จาก Design Reference (ไม่ Copy 100%)
1. โลโก้ตราแผ่นดินลาวในภาพมีรายละเอียดสูง — ใช้ตราวงกลมสีทองแบบย่อแทน เพราะเป็นสัญลักษณ์ทางการที่ไม่ควรลอกเลียนแบบผิดเพี้ยน
2. Headline ในภาพใช้คำว่า "จังหวัด" (คำไทย) — แก้เป็น "แขวง" เพราะเป็นคำศัพท์ทางการปกครองที่ถูกต้องของ สปป.ลาว

### Testing
- `php -l` PASS ทุกไฟล์ที่แก้ไข/สร้างเพิ่ม, CSS Brace Balanced (143/143)
- HTTP Testing PASS: `GET /public/` และ `GET /public/sitemap.php` → 200 ไม่มี PHP Warning/Error
- Regression PASS: Admin Panel ไม่ถูกกระทบ

Status : Stable (รอตรวจสอบ/อนุมัติก่อนเริ่ม Stage 2)

## Admin Panel — Design Refresh

### Changed
- `public/assets/css/admin.css`, `public/assets/css/crud.css`, `public/assets/css/auth.css` — ปรับ Design ใหม่ทั้งชุดให้ Identity เดียวกับ Public Website (สี/มุมโค้ง/เงาชุดเดียวกัน) โดยใช้ CSS Variables ร่วม — ไม่เปลี่ยนชื่อ Class ใดๆ ที่ View/JS เดิมอ้างอิงอยู่
- `app/includes/admin_sidebar.php` — เพิ่มไอคอนหน้าเมนู + เพิ่ม Logic ไฮไลท์เมนูปัจจุบันตาม URL จริง (ของใหม่ ไม่กระทบสิทธิ์/Role Filter เดิม)
- `app/views/admin/login.php`, `app/views/admin/dashboard.php` — เพิ่ม Element ตกแต่ง (ตราวงกลม/ไอคอน Stat Card) ไม่กระทบ Logic

### Added
- `app/helpers/icons.php` — เพิ่มไอคอน `dashboard`, `users`, `settings`, `log`, `logout`

### Testing
- `php -l` PASS ทุกไฟล์ที่แก้ไข, CSS Brace Balanced ทั้ง 3 ไฟล์
- HTTP Testing PASS: หน้า Login โหลด 200 ไม่มี Error, Asset CSS ทั้งหมดโหลด 200
- Regression PASS: Public Website ไม่ถูกกระทบ
- ไม่ได้ทดสอบ Login จริงเข้า Dashboard (หลีกเลี่ยงการเปลี่ยนสถานะบัญชี Admin จริงโดยไม่จำเป็น) — รอผู้ใช้ตรวจสอบเอง

## Admin Panel — UI/UX Redesign (อ้างอิง `design/admin.jpeg`, ทำทีละโมดูล)

### Module 1: Dashboard

#### Added
- `app/includes/admin_header.php` — Notification Dropdown (ดึงจาก `activity_logs` จริง เฉพาะสิทธิ์ `activity_log:view`), User Avatar Dropdown (เปลี่ยนรหัสผ่าน/ออกจากระบบ)
- `app/views/admin/dashboard.php` — Stat Card ไล่เฉดสี + ลิงก์ดูทั้งหมด, Quick Action Buttons (กรองด้วย `can()` เดิม), Timeline กิจกรรมล่าสุด, Line Chart CSS/SVG ล้วน (ไม่ใช้ Library ภายนอก)
- `app/models/ActivityLogModel::getDailyCounts()` — Method ใหม่ (Read-only) นับ Log รายวันย้อนหลัง 7 วันสำหรับกราฟ

#### Changed
- `public/assets/css/admin.css` — Design Token ตามสีที่กำหนด (#1A3D7C/#2563EB/#22C55E/#F59E0B/#EF4444), Topbar/Sidebar/Dashboard ใหม่ทั้งหมด
- `app/includes/admin_sidebar.php` — จัดกลุ่มเมนู (Dashboard/Content/Management/System), รองรับ Sidebar Collapsed
- `public/assets/js/admin.js` — เพิ่ม Dropdown Toggle + Sidebar Collapse (จอใหญ่) ไม่กระทบพฤติกรรม Slide-in เดิมบนจอเล็ก
- `app/controllers/DashboardController.php` — ดึง `recentActivity`/`dailyCounts` เพิ่ม (Additive, ไม่แก้ Logic เดิม)

#### Security
- Timeline/Notification/Chart เช็ค `can('activity_log','view')` ก่อนดึงข้อมูลทุกจุด (Controller + Topbar) ป้องกัน Editor/Staff เห็นข้อมูล Audit Log ผ่านช่องทางอ้อม — คงพฤติกรรม Permission เดิมของ Phase 11 ไว้ครบถ้วน

#### Testing
- `php -l` PASS ทุกไฟล์, CSS Brace Balanced
- Render Test ผ่าน CLI จำลอง Session Admin (ไม่แตะบัญชีจริง) — ไม่มี PHP Warning/Notice/Exception, ข้อมูลจริงจาก DB แสดงถูกต้องครบ
- Regression PASS: Login/Public Website ไม่ถูกกระทบ

Status : รอผู้ใช้ตรวจสอบและอนุมัติก่อนไป Module 2 (Employees) — ถูกคั่นกลางด้วย Phase 13

## Phase 13 — Activities Management System

### Added
- `database/migrations/011_create_activities_table.sql`, `database/seeders/005_seed_activities_permissions.sql`
- `app/models/ActivityModel.php`, `app/controllers/ActivityController.php` — Admin CRUD (Pattern เดียวกับ News/Gallery) — คนละ Class กับ `ActivityLogModel`/`ActivityLogController` (Phase 11)
- `app/views/admin/activities/{index,form}.php`, `public/admin/activities/{index,form,delete}.php`
- `app/controllers/PublicActivityController.php`, `app/views/public/activities/{index,detail}.php`, `public/activities/{index,detail}.php` — Public List/Detail (Published เท่านั้น, Sort `activity_date DESC`)
- `app/views/public/404.php`, `app/includes/public_empty_state.php` — หน้า 404 และ Empty State กลาง ใช้ร่วมได้ทุก Public Controller ในอนาคต

### Changed
- `app/includes/admin_sidebar.php` — เปิดเมนู Activities
- `app/models/DashboardModel.php` — Map ตาราง activities จริง + นับเฉพาะ `deleted_at IS NULL` เฉพาะ activities (ไม่กระทบตัวเลข Stat Card โมดูลอื่น)
- `app/views/admin/dashboard.php`, `app/config/permissions.php` — ลิงก์ Stat Card + Permission Fallback ให้ตรง Seeder
- `app/includes/public_header.php` — เมนู "กิจกรรม" ชี้ Activities, เพิ่มเมนู "คลังภาพ" แยก; เพิ่ม SEO Meta ครบ (keywords/og:title/description/type/image/url)
- `app/includes/public_footer.php` — เพิ่ม `defer` บน Script
- `app/views/public/{home,sitemap}.php` — Breadcrumb Semantic, Empty State ใช้ `renderEmptyState()`, ลิงก์กิจกรรม/คลังภาพ
- `app/controllers/PublicHomeController.php` — Stat "กิจกรรมโครงการ" นับจาก ActivityModel จริงแทน Gallery
- `app/helpers/functions.php` — เพิ่ม `renderNotFound()`, `renderEmptyState()`
- `public/assets/css/public.css` — Breadcrumb Semantic Style, Global `:focus-visible`, Empty State Actions, Detail Article (Reuse ได้กับโมดูลอนาคต)
- `public/assets/js/public.js` — `.js-back-link` Handler

### Fixed
- `public/activities/index.php`, `public/activities/detail.php` — Path ผิด (`dirname(__DIR__)` → `dirname(__DIR__, 2)`) ทำให้ Fatal Error หา bootstrap.php ไม่เจอ
- `public/assets/css/public.css` — `.search-box input { outline: none; }` ไม่มี Focus Indicator ทดแทน (WCAG 2.4.7) — เพิ่ม `:focus-within`

### Security
- Permission Matrix Activities ตรงกับ Seeder ทุกประการ (Admin/Editor/Staff)
- Public Detail แสดงเฉพาะ `status='Published' AND deleted_at IS NULL` เท่านั้น (Draft/Deleted → 404)
- Output ทุกจุด Escape ผ่าน `e()`, GET Parameter ผ่าน `(int)` Cast, รูปภาพผ่าน `uploadUrl()` ครบ ไม่มี Path Traversal

### Testing
- `php -l` PASS ทุกไฟล์ที่สร้าง/แก้ไข, CSS Brace Balanced
- ทดสอบผ่าน HTTP จริง (Login จริง + PHP cURL เพื่อความแม่นยำของ UTF-8 ไทย): CRUD ครบ (มี/ไม่มีรูป), Soft Delete, CSRF, XSS, SQL Injection, Permission 3 Role, Published/Draft Visibility, Pagination, Sort, 404, Empty State, OG Meta — PASS ทั้งหมด
- Regression PASS: Admin ทุกโมดูล (9 โมดูล + Dashboard), Public Website, Login — ไม่ถูกกระทบ
- ข้อมูลทดสอบทั้งหมดลบออกจากฐานข้อมูล/ไฟล์อัปโหลดหลังทดสอบเสร็จทุกครั้ง

Status : รอผู้ใช้อนุมัติ Commit/Push (ยังไม่ Commit/Push ตามคำสั่ง)