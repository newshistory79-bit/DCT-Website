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

## Public Website — Stage 2: Content Modules (News, Legislation, Documents, Gallery, Departments, Employees, Global Search)

### Added
- `app/helpers/public_components.php` — Shared Public Component Layer ใช้ร่วมกันทุกโมดูล: `renderBreadcrumb()`, `renderPageHeader()`, `renderCard()`, `renderPagination()`, `renderDetailMeta()`, `findAdjacent()`, `renderPrevNextNav()`, `renderRelatedItems()`, `renderBackToList()`, `renderDocumentCard()`
- `app/controllers/PublicNewsController.php`, `app/views/public/news/{index,detail}.php`, `public/news/{index,detail}.php`
- `app/controllers/PublicLegislationController.php`, `app/views/public/legislation/{index,detail}.php`, `public/legislation/{index,detail}.php`
- `app/controllers/PublicDocumentController.php`, `app/views/public/documents/index.php`, `public/documents/index.php`
- `app/controllers/PublicGalleryController.php`, `app/views/public/gallery/index.php`, `public/gallery/index.php` — Lightbox แบบ Vanilla JS ล้วน (Fullscreen/Prev-Next/ESC/Click-Outside/Keyboard/Touch Swipe/Focus Trap/Restore Focus)
- `app/controllers/PublicDepartmentController.php`, `app/views/public/departments/{index,detail}.php`, `public/departments/{index,detail}.php`
- `app/controllers/PublicEmployeeController.php`, `app/views/public/employees/{index,detail}.php`, `public/employees/{index,detail}.php`
- `app/controllers/PublicSearchController.php`, `app/views/public/search/index.php`, `public/search.php` — ค้นหาข้าม 7 Module พร้อมกันผ่าน `?q=` (Bookmark ได้) แบบ Section-based, Pagination อิสระต่อ Section
- `formatFileSize()` ใน `app/helpers/functions.php`
- `initLightbox()` ใน `public/assets/js/public.js` (Refactor เป็นโครงสร้าง Module: `initNavigation()` + `initLightbox()`)

### Changed
- `app/core/bootstrap.php` — โหลด `public_components.php` (Additive)
- `app/includes/public_header.php` — เพิ่ม `canonical`, Twitter Card, JSON-LD (`GovernmentOrganization` + `BreadcrumbList`)
- `public/assets/css/public.css` — เพิ่ม CSS สำหรับ Prev/Next Nav, Related Items, Document Card, Gallery Lightbox (Reuse Design Token/Utility เดิมทั้งหมด)
- `public/assets/js/public.js` — Refactor เป็นโครงสร้าง Module เดียว เรียกผ่าน `DOMContentLoaded` จุดเดียว

### Privacy Decision
- Employees (Stage 2.6): ไม่แสดง `phone`/`email`/`address`/`birth_date` บน Public เนื่องจาก Schema ไม่มี Field แยกข้อมูลติดต่อสาธารณะ/ภายใน (ยืนยันกับผู้ใช้ก่อนพัฒนา) — Public แสดงเฉพาะ ชื่อ-นามสกุล/ตำแหน่ง/รูปภาพ

### Fixed
- Gallery Lightbox (Stage 2.4): `closeBtn.focus()` ถูกเรียกก่อน Class `.active` (คุม `visibility`) ถูกเพิ่มใน `requestAnimationFrame` ถัดไป ทำให้ Browser เพิกเฉยการโฟกัสแบบเงียบๆ — แก้โดยย้าย `.focus()` เข้า Callback เดียวกับการเพิ่ม `.active` (พบและแก้ผ่านการทดสอบด้วย Chrome DevTools Protocol จำลอง Click/Keyboard จริง)

### Security
- Escape Output ทุกจุด (`e()`), Prepared Statement ทุก Query (Reuse Model เดิม), Cast `(int)` ทุก ID Parameter, Published/Active Only ทุกโมดูล, Soft Delete ไม่แสดงทุกจุด, Path Traversal ป้องกันโดย Filename มาจาก Database เท่านั้น (ไม่รับจาก User), ไม่มี Form แบบ POST ใน Public Stage 2 (CSRF ไม่เกี่ยวข้อง)

### Testing
- `php -l` PASS ทุกไฟล์ (33 ไฟล์)
- Regression PASS: Public 17 หน้า, Admin 11 จุด (รวม Dashboard/Login)
- Security Testing PASS: XSS, SQL Injection, Path Traversal ทุกโมดูล (ทดสอบด้วยข้อมูลจริงที่แทรกแล้วลบทิ้งทุกครั้ง)
- Responsive Testing PASS: Desktop/Tablet/Mobile ทุกหน้า (ตรวจ `scrollWidth`/`clientWidth` จริงผ่าน Headless Chrome ยืนยันไม่มี Horizontal Scroll)
- Global Search Testing PASS: ภาษาไทย/อังกฤษ/ลาว, Keyword ว่าง/ยาวผิดปกติ/Space หลายตัว/URL Encode
- ข้อมูลทดสอบทั้งหมดถูกลบออกจากฐานข้อมูล/ไฟล์อัปโหลดหลังทดสอบเสร็จทุกครั้ง (ยืนยันทุกตารางกลับสู่จำนวนเดิม)

### Known Issue (นอกขอบเขต Stage 2 — ไม่ใช่ Regression จากงานนี้)
- URL ที่ไม่มีไฟล์จริงเลย (เช่น `/nonexistent-page-xyz.php`) ตกไปที่ `.htaccess` Catch-all Rewrite เข้า Home แทน 404 จริง — เป็นพฤติกรรมเดิมจาก Stage 1 (Routing/Home อยู่นอกขอบเขต ตามข้อกำหนด "ไม่แตะ Home")
- ยังไม่มี `sitemap.xml`/`robots.txt` สำหรับ Search Engine (HTML Sitemap ที่ `sitemap.php` มีลิงก์ครบทุกโมดูลแล้ว) — เสนอเป็น Task แยกในอนาคต

### Database
- ไม่มีการแก้ไขฐานข้อมูล / ไม่มี Migration ใหม่ตลอดทั้ง Stage 2

Status : Committed (`6bec1a9`)

## Admin Panel Design System v2 (Stage DS1–DS5)

ยกระดับ Admin Panel ทั้งระบบสู่มาตรฐาน "Modern Government CMS" (แรงบันดาลใจจาก GitHub/Vercel/Supabase/Clerk/Linear/Notion/shadcn — ไม่ Copy UI ตรงๆ) โดยคง Business Logic/Permission/Route/Controller/Model/Database/Authentication ไว้ **100%** — เป็น Retrofit เฉพาะชั้น View/CSS/JS เท่านั้น ไม่แตะ Controller/Model/Database แม้แต่บรรทัดเดียวตลอดทั้ง 5 Stage แบ่งทำทีละ Stage หยุดรออนุมัติทุกครั้งตาม Workflow เดียวกับ Phase 13/Public Stage 2

### Added
- `app/config/admin_menu.php` — Single Source of Truth ของเมนู Sidebar และ Breadcrumb (label/url/enabled/roles/icon/group/title/description ต่อรายการ) ใช้แทน Array เดิมที่เคย Hardcode ใน `admin_sidebar.php`
- `app/helpers/admin_components.php` — Shared Component Layer ฝั่ง Admin (ตั้งชื่อฟังก์ชันขึ้นต้นด้วย `renderAdmin*`/`findAdmin*` ทั้งหมดเพื่อไม่ชนกับ `public_components.php`): `renderAdminBreadcrumb()`, `renderAdminPageHeader()`, `renderAdminSectionCard()`, `renderBadge()`, `renderAdminPagination()`, `renderAdminEmptyState()`, `renderModal()`, `renderConfirmDialog()`, `renderSkeletonRows()`, `findAdminMenuItemByUrl()`, `findActiveAdminMenuItem()`
- `app/helpers/icons.php` — เพิ่มไอคอนใหม่ 9 รายการ (edit/trash/plus/eye/spinner/filter/sort/alert-triangle/check) ต่อท้ายไฟล์เดิม ไม่แก้ไอคอนเดิม
- `public/assets/js/admin.js` — เพิ่ม `initModal()`, `initConfirmDialog()` (Progressive Enhancement ผ่าน `data-confirm-modal`, ไม่ยุ่งกับ `data-confirm`/Handler เดิมที่ยังใช้งานได้ปกติ), `initToast()`, `initFormLoadingState()` เรียกทั้งหมดจาก `DOMContentLoaded` จุดเดียวเดิม
- `public/assets/css/admin.css`, `public/assets/css/crud.css` — เพิ่ม Layer ใหม่ต่อท้ายไฟล์แบบ Additive เท่านั้น (Design Token/Focus Style/Page Header/Section Card/Badge/Pagination/Empty State/Modal/Confirm Dialog/Toast/Skeleton/`.btn-icon`/`.btn-ghost`) — **ไม่มีการแก้ไขหรือลบ Rule Block เดิมแม้แต่จุดเดียวตลอดทั้ง 4 Stage**

### Changed — Retrofit ทีละโมดูล (DS2–DS4)
- Dashboard, Departments, Employees (DS2), News, Legislation, Activities (DS3), Gallery, Documents, Users, Activity Log (DS4) — ทุกหน้า List ใช้ `renderAdminPageHeader()` + `.search-input-icon` + `renderBadge()` สำหรับ Status + `.btn-icon` Action Button + `data-confirm-modal` (แทน `window.confirm()`) + `renderAdminPagination()` + `renderAdminEmptyState()` ร่วมกันทั้งหมด, ทุกหน้า Form ใช้ `renderAdminSectionCard()` แบ่ง Section (ข้อมูลทั่วไป/สถานะ/ไฟล์-รูปภาพ) แทน Layout เดิม
- `app/includes/admin_header.php` — เพิ่ม Breadcrumb อัตโนมัติจาก `admin_menu.php`
- `app/includes/admin_sidebar.php` — เปลี่ยนมาอ่านเมนูจาก `admin_menu.php` แทน Array Literal เดิม (Logic Active-Highlight/Role-Filter เดิมไม่ถูกแตะ)
- `app/includes/admin_footer.php` — เพิ่ม `renderConfirmDialog()` (Render ครั้งเดียวทั้งระบบ)
- `app/core/bootstrap.php` — เพิ่ม `require_once` โหลด `admin_components.php` (Additive บรรทัดเดียว)
- Documents: ลบ Closure `$formatFileSize` ที่ซ้ำกับ `formatFileSize()` ใน `functions.php` (Phase 13/Stage 2 เพิ่มไว้แล้ว) เปลี่ยนมาเรียก Global Function แทน (DRY Cleanup)

### Fixed
- **Sticky Table Header**: `position:sticky` บน `<th>` ร่วมกับ `border-collapse:collapse` ทำให้แถวข้อมูลแรกมองไม่เห็น (ยืนยันด้วย `getBoundingClientRect()` + Screenshot จริง) ลองแก้ด้วย `border-collapse:separate` แล้วไม่หาย — **ตัดสินใจถอด Sticky Header ออกจาก View จริง** (คง CSS ไว้พร้อม Comment "Known Issue" ห้ามใช้จนกว่าจะมีวิธีทดสอบผ่านทุก Browser)
- **Form Field เรียงตัวผิด**: `.admin-section-card-body` ไม่มี `display:flex;flex-direction:column` ทำให้ Label/Input ไหลติดกัน — เพิ่ม Rule ใหม่แก้ (DS2)
- **Horizontal Overflow ระดับหน้า (News, ตาราง 8 คอลัมน์)**: `.admin-content` (Flex Item) ไม่มี `min-width` ทำให้ Browser ไม่ยอมบีบเล็กกว่าความกว้างเนื้อหาจริง (Flexbox Overflow บั๊กมาตรฐาน) — เพิ่ม `.admin-content { min-width: 0; }` (DS3)
- **Horizontal Overflow บนมือถือ (Activity Log)**: คอลัมน์ "รายละเอียด" ไม่มี Class `.truncate` (จุดเดียวที่ตกหล่นจากทุกโมดูล) ทำให้ตารางกว้างผิดปกติ ประกอบกับ `min-width:0` อย่างเดียวไม่พอสำหรับกรณีนี้ — เพิ่ม `.truncate` ที่คอลัมน์ดังกล่าว และเพิ่ม `.admin-content { overflow-x: hidden; }` เป็นชั้นป้องกันเสริม (DS4) ยืนยันด้วย `window.scrollTo(9999,0)` + อ่าน `scrollX` กลับ (ไม่ใช่เทียบ `scrollWidth` เฉยๆ เพราะ `overflow-x:hidden` ไม่เปลี่ยนค่า `scrollWidth`)
- **Inline Style หลงเหลือ**: `departments/form.php` มี `style="text-transform:uppercase"` — ย้ายเป็น Class `.input-uppercase` ใน `crud.css` (DS4)
- **Component Name ชนกัน**: `renderEmptyState()` ใหม่ชนกับฟังก์ชันชื่อเดียวกันใน `functions.php` (Public) ทำให้ Fatal Error "Cannot redeclare" ล่มทั้งเว็บไซต์ — เปลี่ยนชื่อเป็น `renderAdminEmptyState()` และตรวจสอบไม่ให้ชื่อฟังก์ชันใหม่ชนกับของเดิมอีกทุกจุด (DS1)

### Security
- Escape ผ่าน `e()` ทุกจุดที่ Interpolate ข้อมูลจาก Database เข้า `renderAdminPageHeader()`/`data-confirm-modal` (รวมกรณี Concatenate String ก่อนส่งเข้าฟังก์ชัน — ยืนยันว่า `e()` ทำงานกับ String รวมทั้งก้อนก่อน Output จริง ไม่มีช่องโหว่ XSS)
- CSRF Token ครบทุกฟอร์ม POST (Create/Edit/Delete) เดิมไม่ถูกแตะ, Self-Delete Protection ของ Users คงเดิม
- Permission (`can()`) Gate ทุกปุ่ม Action เดิมไม่ถูกแตะ

### Testing
- `php -l` PASS ทุกไฟล์ (28 ไฟล์ที่สร้าง/แก้ไขตลอด Stage DS1–DS5), CSS Brace Balanced ทั้ง `admin.css` (130/130) และ `crud.css` (102/102)
- Regression PASS: Public 18 หน้า + Admin 21 จุด (List/Form/Search/Filter/Pagination/Empty State/Login-Logout/Permission) ผ่าน HTTP จริง (Login จริงผ่าน PHP cURL), Full CRUD Cycle (Create → Edit → Soft Delete) ทดสอบจริงกับ Departments ยืนยัน Field Name/CSRF/Redirect ไม่เสีย
- Security PASS: ตรวจ Escape/CSRF/Permission/Path Traversal/Self-Delete Protection ทั้งระบบ ไม่พบช่องโหว่ใหม่จากการ Retrofit
- Accessibility PASS: `role="dialog"`/`role="alertdialog"` + `aria-modal`/`aria-labelledby`/`aria-describedby` บน Modal/Confirm Dialog, Focus Trap + Escape + Restore Focus ครบ, Breadcrumb Semantic, `aria-label` บนปุ่ม Icon-only ครบทุกโมดูล, Global `:focus-visible`
- Responsive PASS: ทดสอบ 375px จริงผ่าน Headless Chrome + CDP (Users/Documents/Activities/Gallery/Employees/Departments Form) ยืนยัน `document.documentElement.scrollWidth === clientWidth` และ `window.scrollTo(9999,0)` แล้ว `scrollX` คงเป็น 0 ทุกหน้า (ไม่มี Horizontal Scroll)
- Performance PASS: ไม่มี Inline `style=`/`<script>` หลงเหลือ, DOMContentLoaded Listener จุดเดียวใน `admin.js`, ไม่มี N+1 Query (Controller เดิมไม่ถูกแตะ), CSS Selector ที่ซ้ำกันทุกจุด (`.admin-content` ×3, `.quick-action-btn` ×2, `.admin-topbar` ×2, `.btn-link` ×2, `.alert` ×2, `.admin-section-card-body` ×2) ตรวจแล้วเป็น Property คนละชุดจริง (Additive Layer ตามหลักการที่ตั้งไว้ ไม่ใช่ Duplicate โดยไม่ตั้งใจ)
- ข้อมูลทดสอบทั้งหมด (Department ทดลอง 1 แถว) ลบออกจากฐานข้อมูลหลังทดสอบเสร็จ ยืนยันจำนวนกลับสู่ Baseline เดิม (9 แถว)

### Known Issue (คงไว้ตามเดิม ไม่ใช่ Regression จากงานนี้)
- `.data-table-sticky` (Sticky Table Header) มี CSS พร้อมใช้แต่ยังมีบั๊กแถวแรกมองไม่เห็น — ไม่ถูกใช้งานจริงใน View ใดๆ จนกว่าจะพบวิธีแก้ที่ผ่านการทดสอบทุก Browser
- URL ที่ไม่มีไฟล์จริงเลย (`.htaccess` Catch-all Rewrite) ยังคงพฤติกรรมเดิมจาก Stage 1 (นอกขอบเขต Admin Panel Design System v2)

### Database
- ไม่มีการแก้ไขฐานข้อมูล / ไม่มี Migration ใหม่ตลอดทั้ง Stage DS1–DS5

Status : รอผู้ใช้อนุมัติ Commit/Push (ยังไม่ Commit/Push ตามคำสั่ง)

## Admin Panel Design System v3 (Stage UI-1–UI-6)

ต่อยอดจาก v2 (DS1–DS5) ตาม Mockup ใหม่ (`design/Dashboard.png`) เงื่อนไขเดิมทุกประการ (ห้ามแก้ Database/Model/Controller/Business Logic/Auth/Permission/Route/Public Website, Incremental Refactor เท่านั้น, Reuse Component ก่อนสร้างใหม่เสมอ, หยุดรออนุมัติทุก Stage)

### Added
- `.admin-hero`, `renderAdminHero()` — Hero Header Component (Stage UI-1, Wire เข้า Dashboard จริงใน UI-2)
- `.quick-action-card`, `.segmented-control`, `renderAdminSegmentedControl()`, `initSegmentedControl()` (admin.js) — Segmented Control ทั่วไป (ใช้ครั้งแรกกับปุ่ม 7/30/90 วันของ Analytics)
- `.btn-success`, `.btn-danger` (Solid), `.btn-outline`, `.btn-fab`, Ripple แบบ CSS ล้วน, `.glass-card` (Opt-in)
- Token สี `--color-teal`, `--color-pink`, `--color-indigo` (Accent สีที่ 5-7 ของ Stat Card/Module)
- ไอคอน `bell`, `moon`, `trend-up`, `trend-down`
- Topbar: ช่องค้นหา + ปุ่ม Dark Mode Toggle (ทั้งคู่ `disabled` — ยังไม่มี Backend/Theme System จริงรองรับ)
- `renderAdminSectionCard()` — เพิ่ม Optional Parameter `$headExtra` (Backward Compatible, Default `''`)

### Changed
- `app/views/admin/dashboard.php` — Retrofit เต็มรูปแบบ: Hero Header (Greeting ตามเวลา/Role Badge/วันที่-เวลา), Stat Card แนวนอน 7 สีไม่ซ้ำ, Quick Action Card, Timeline Scrollable, Analytics Card + Segmented Control, Summary Card (คำนวณจาก `$dailyCounts` เดิมในระดับ View)
- `app/views/admin/departments/form.php`, `employees/form.php` — เพิ่ม Section "การดำเนินการ" ห่อปุ่ม Save/Cancel
- `app/views/admin/employees/index.php` — Position Badge (Reuse `renderBadge()`)
- `app/views/admin/news/form.php`, `activities/form.php`, `legislation/form.php` — แยก Section "เนื้อหา" จาก "ข้อมูลทั่วไป" + เพิ่ม Section "การดำเนินการ"
- `app/views/admin/news/index.php`, `activities/index.php`, `legislation/index.php` — Accent Icon Chip ที่ Page Header (Reuse `.stat-icon-*` ผ่าน Parameter `$extra` เดิม)
- `app/includes/admin_header.php` — เพิ่ม Search Box + Dark Mode Toggle, แก้ไอคอนกระดิ่งแจ้งเตือน

### Fixed
- ปุ่มกระดิ่งแจ้งเตือนใน Topbar ใช้ `icon('log')` (รูปเอกสาร) แทนรูประฆังมาตลอด — เพิ่มไอคอน `bell` จริงและสลับให้ถูกต้อง (Stage UI-1)

### Not Changed (ตรวจแล้วผ่านอยู่แล้ว — Stage UI-5)
- Gallery, Documents, Users, Activity Log (List + Form ทั้ง 7 ไฟล์) — ใช้ Component ครบตามข้อกำหนดอยู่แล้วจากการ Retrofit DS2–DS4 ไม่มีการแก้โค้ด

### Known Deliberate Gaps (Database ไม่รองรับ ไม่สร้างข้อมูล/UI ปลอมทดแทน)
- Employees ไม่มี Section "สถานะ" — ตาราง `employee` ไม่มีคอลัมน์ Status จริง
- Legislation ไม่มี Section "รูปภาพ" — ตาราง `legislation` ไม่มีคอลัมน์รูปภาพจริง
- Stat Card บน Dashboard แสดง Trend เป็นสถานะกลาง "ยังไม่มีข้อมูลเปรียบเทียบ" — `DashboardModel::getModuleCounts()` ไม่มีข้อมูลเปรียบเทียบย้อนหลังจริง
- Segmented Control 30/90 วันของ Analytics แสดง Empty State — `ActivityLogModel::getDailyCounts()` คำนวณให้แค่ 7 วัน (ตกลงกับผู้ใช้แล้วว่ายังไม่เชื่อม Backend ใน v3)

### Security
- Escape Output ผ่าน `e()` ครบทุกจุดที่เพิ่มใหม่ (Hero/Segmented Control/Accent Chip)
- CSRF Token เดิมไม่ถูกแตะทุกฟอร์ม, Self-Delete Protection ของ Users ยืนยันด้วย Live Test ยังทำงานถูกต้อง

### Testing
- `php -l` PASS ทุกไฟล์ที่สร้าง/แก้ไขตลอด Stage UI-1–UI-5
- Regression PASS: Login จริงผ่าน PHP cURL ครบ 19 หน้า (Dashboard + 10 โมดูล List/Form + Change Password), Full CRUD Cycle จริง (Create → Search → Delete) กับ Departments, Logout ยืนยัน Session ถูกทำลาย — ไม่พบ PHP Warning/Notice/Fatal Error จุดใด
- Duplicate Audit: Selector CSS ที่ปรากฏซ้ำ 13 จุดตรวจแล้วเป็น Additive Layer ตามหลักการเดิม (คนละ Property หรือ Override ที่ตั้งใจ) ไม่ใช่ Bug
- ไม่พบ Bug ใหม่ในรอบ Final Quality Review (Stage UI-6) จึงไม่มีการแก้โค้ดเพิ่มเติมนอกจาก Documentation

Status : รอผู้ใช้อนุมัติ Commit/Push (ยังไม่ Commit/Push ตามคำสั่ง)

## Remove Department Code

### Removed
- คอลัมน์ `departments.code`, Generated Column `code_active`, UNIQUE INDEX `departments_code_active_unique` — ลบถาวรผ่าน `database/migrations/013_departments_drop_code.sql` (Executed, มี Backup ก่อน Execute)
- Validation ทั้งหมดของ code (required/ความยาว/รูปแบบ A-Z0-9-/ตรวจซ้ำ) ใน `DepartmentController::validate()`
- Method `DepartmentModel::codeExists()`
- ช่องกรอกรหัสแผนกในฟอร์ม Admin, คอลัมน์รหัสแผนกในตาราง Admin List, การแสดงรหัสแผนกในหน้ารายละเอียด Public
- Class CSS `.input-uppercase` (`crud.css`) — สร้างมาเฉพาะช่องรหัสแผนก กลายเป็น Dead CSS หลังลบช่องนี้

### Changed
- `DepartmentModel::SORTABLE_COLUMNS` เหลือ `id, name, status, created_at`
- ค้นหาแผนก (Admin List + Public Search) เปลี่ยนจากค้นหาด้วย code หรือ name → ค้นหาด้วยชื่อแผนกเท่านั้น
- Placeholder ช่องค้นหาใน Admin List: "ค้นหารหัสหลือชื่อแผนก" → "ค้นหาชื่อแผนก"

### Not Affected (ตรวจสอบแล้ว)
- Employees, News, Activities, Documents, Users, Dashboard, Public Home, Activity Log — ไม่มี Module ใดอ้างอิง Department Code (ไม่มี Foreign Key ผูกกับ `departments`)
- ข้อความ Activity Log ของ Departments ใช้ชื่อแผนก (`$data['name']`) อยู่แล้วตั้งแต่เดิม ไม่ต้องแก้ไข

### Testing
- `php -l` PASS ทุกไฟล์ที่แก้ไข (Model/Controller/Views)
- Model Regression ผ่าน PHP CLI กับฐานข้อมูลจริงหลัง Migration — 18/18 PASS: Create/Find/Update/SoftDelete ไม่มี code, nameExists, ค้นหาด้วยชื่อ, ป้องกัน SQL Injection ผ่าน keyword, Sort Whitelist ปฏิเสธ `code` และ fallback เป็น `id` อย่างปลอดภัย, ชื่อที่มี `<script>` ถูกเก็บ/ดึงคืนถูกต้อง (Escape เป็นหน้าที่ View Layer ผ่าน `e()` เดิม)
- HTTP Smoke Test: `public/departments/index.php`, `public/departments/detail.php`, `public/search/index.php` → 200 ไม่มี PHP Warning/Notice/Fatal Error; Route Admin Departments (List/Form) → 302 Redirect ไปหน้า Login ตามปกติเมื่อไม่ได้ Authenticate (ยืนยัน Bootstrap/Controller ไม่ Fatal Error)
- ข้อมูลจริงในฐานข้อมูล (13 แถว) ไม่เปลี่ยนแปลงหลัง Test เสร็จ (Insert/Update/Delete ทดสอบใช้แถวชั่วคราวที่ลบออกหลังทดสอบเสร็จทุกครั้ง)
- Full HTTP Login + CRUD ผ่าน Session จริงไม่ได้ทดสอบรอบนี้ — รหัสผ่าน Admin ปัจจุบันถูกเปลี่ยนไปแล้วจาก Default ในการทดสอบรอบก่อนหน้า (`first_login = 0`) ไม่ทราบค่าปัจจุบัน และไม่ได้รับอนุญาตให้ Reset รหัสผ่านโดยไม่ขออนุมัติก่อน — ทดสอบ Logic ระดับ Model แทนแบบครบถ้วนทุก Method

Status : รอผู้ใช้ตรวจสอบและอนุมัติ Commit/Push (ยังไม่ Commit/Push ตามคำสั่ง)