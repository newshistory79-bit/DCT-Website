# Database Documentation

## Project
DTC-Website — TCSP Administration System

## Database

- Database Name: `tcsp`
- DBMS: MariaDB 10.4.32
- Character Set: utf8mb4
- Collation: utf8mb4_general_ci

---

## Current Tables (ณ Phase 13)

| Table | Description |
|-------|-------------|
| `users` | ระบบผู้ใช้งาน |
| `departments` | ข้อมูลแผนก |
| `employee` | ข้อมูลพนักงาน |
| `news` | ข่าวประชาสัมพันธ์ |
| `legislation` | กฎหมาย / ระเบียบ |
| `documents` | เอกสารดาวน์โหลด |
| `gallery` | คลังภาพกิจกรรม |
| `activities` | กิจกรรมของหน่วยงาน (Phase 13) |
| `role_permissions` | สิทธิ์การใช้งานตาม Role/Module/Action (Database-first Permission) |
| `activity_logs` | ประวัติการใช้งานระบบ (Audit Trail, Insert-only) |

รวมทั้งหมด 10 ตาราง

---

## Table Details

### users

Purpose: ระบบผู้ใช้งาน

Columns:
- id, username, password, full_name, email
- role — enum(`Admin`, `Editor`, `Staff`)
- status — enum(`Active`, `Inactive`)
- first_login, last_login_at
- created_at, updated_at, deleted_at

Primary Key: id
Soft Delete: ใช้ `deleted_at`

---

### departments

Purpose: ข้อมูลแผนกภายในองค์กร

Columns: id, code, name, description, status, sort_order, created_at, updated_at, deleted_at

Generated Columns: `code_active`, `name_active`

Index:
- PRIMARY KEY (id)
- UNIQUE (`code_active`)
- UNIQUE (`name_active`)

Soft Delete: ใช้ `deleted_at` (Unique Index อยู่บน Generated Columns เพื่อไม่ต้องแก้ `code`/`name` เดิมตอน Soft Delete)

---

### employee

Purpose: ข้อมูลพนักงาน

Columns: ID, Fname, Lname, birth_date, gender, phone, email, position, address, image, created_at, updated_at, deleted_at

Primary Key: ID
Soft Delete: ใช้ `deleted_at`

หมายเหตุ: ตารางเดิมใช้ชื่อคอลัมน์ Mixed Case (`Fname`, `Lname`) ตามมาตรฐานโปรเจกต์ที่ห้ามเปลี่ยนชื่อคอลัมน์เดิม — Controller/Model ที่อ้างอิงตารางนี้ต้องใช้ชื่อคอลัมน์ตัวพิมพ์ใหญ่ให้ตรงเสมอ

---

### news

Purpose: ข่าวประชาสัมพันธ์

Columns: ID, title, detail, image, activity_date, status, created_at, updated_at, deleted_at

Index:
- PRIMARY KEY (ID)
- `idx_news_status` (status)

Soft Delete: ใช้ `deleted_at`
Image Upload: `uploads/news/` — jpg, jpeg, png, webp, สูงสุด 2 MB
Status: `Draft`, `Published`

---

### legislation

Purpose: จัดเก็บข้อมูลกฎหมาย ระเบียบ และประกาศ

| Column | Type | Description |
|--------|------|-------------|
| ID | int | Primary Key |
| title | varchar(255) | ชื่อกฎหมาย |
| document_number | varchar(50) | เลขที่ประกาศ |
| detail | text | รายละเอียด |
| effective_date | date | วันที่มีผลบังคับใช้ |
| status | enum(Draft, Published) | สถานะ |
| created_at | timestamp | วันที่สร้าง |
| updated_at | timestamp | วันที่แก้ไข |
| deleted_at | datetime | Soft Delete |

Index: PRIMARY KEY (ID), `idx_legislation_status`

Notes: ใช้ Soft Delete · Filter ตาม Status · Search title/document_number/detail · ไม่มี File Upload

---

### documents (Phase 8)

Purpose: จัดเก็บเอกสารดาวน์โหลด (PDF/Office Files)

| Column | Type | Description |
|--------|------|-------------|
| id | int | Primary Key |
| title | varchar(255) | ชื่อเอกสาร |
| description | text | รายละเอียด |
| file_name | varchar(255) | ชื่อไฟล์ที่จัดเก็บจริง (สุ่มด้วย UploadHelper) |
| original_file_name | varchar(255) | ชื่อไฟล์ต้นฉบับตอนอัปโหลด |
| file_extension | varchar(10) | นามสกุลไฟล์ |
| file_size | int | ขนาดไฟล์ (ไบต์) |
| status | enum(Draft, Published) | สถานะ |
| created_at | timestamp | วันที่สร้าง |
| updated_at | timestamp | วันที่แก้ไข |
| deleted_at | datetime | Soft Delete |

Index: PRIMARY KEY (id), `idx_documents_status`

Upload Rules: pdf, doc, docx, xls, xlsx, ppt, pptx · สูงสุด 10 MB · Create บังคับแนบไฟล์ / Edit ไม่บังคับ · Soft Delete ไม่ลบไฟล์จริง · `uploads/documents/`

Notes: MIME Whitelist รวม `application/CDFV2`/`application/x-cfb` (ไฟล์ .doc/.xls/.ppt แบบเก่าใช้ OLE Compound Format ร่วมกัน libmagic แยกชนิดเฉพาะไม่ได้) และ `application/zip` (ไฟล์ .docx/.xlsx/.pptx เป็น ZIP Container)

---

### gallery (Phase 9)

Purpose: คลังภาพกิจกรรม (Single Table, 1 รูป = 1 รายการ, ไม่มี Album/Foreign Key)

| Column | Type | Description |
|--------|------|-------------|
| id | int | Primary Key |
| title | varchar(255) | ชื่อภาพ/ชุดกิจกรรม |
| description | text | คำอธิบายภาพ |
| image | varchar(255) | ชื่อไฟล์ภาพที่จัดเก็บจริง (สุ่มด้วย UploadHelper) |
| status | enum(Draft, Published) | สถานะ |
| created_at | timestamp | วันที่สร้าง |
| updated_at | timestamp | วันที่แก้ไข |
| deleted_at | datetime | Soft Delete |

Index: PRIMARY KEY (id), `idx_gallery_status`

Upload Rules: jpg, jpeg, png, webp (ไม่รองรับ gif) · สูงสุด 2 MB · Create บังคับแนบรูป / Edit ไม่บังคับ · Soft Delete ไม่ลบไฟล์จริง · `uploads/gallery/`

---

### activities (Phase 13)

Purpose: จัดเก็บกิจกรรมของหน่วยงาน (แยกจาก `gallery` โดยเจตนา — `gallery` คือคลังภาพ ส่วน `activities` คือกิจกรรม/โครงการที่มีวันที่และสถานที่จัดงาน)

| Column | Type | Description |
|--------|------|-------------|
| id | int | Primary Key |
| title | varchar(255) | หัวข้อกิจกรรม |
| description | text | รายละเอียดกิจกรรม |
| activity_date | date | วันที่จัดกิจกรรม (NOT NULL) |
| location | varchar(255) | สถานที่จัดกิจกรรม |
| image | varchar(255) | ชื่อไฟล์ภาพที่จัดเก็บจริง (สุ่มด้วย UploadHelper) — ไม่บังคับ (Pattern เดียวกับ `news`) |
| status | enum(Draft, Published) | สถานะ |
| created_at | timestamp | วันที่สร้าง |
| updated_at | timestamp | วันที่แก้ไข |
| deleted_at | datetime | Soft Delete |

Index: PRIMARY KEY (id), `idx_activities_status`, `idx_activities_activity_date`

Upload Rules: jpg, jpeg, png, webp · สูงสุด 2 MB · ไม่บังคับแนบทั้ง Create/Edit · Soft Delete ไม่ลบไฟล์จริง · `uploads/activities/`

Public Website: `/activities/index.php` (List, Sort `activity_date` DESC, Published เท่านั้น), `/activities/detail.php` (Detail, 404 หากไม่ Published/ไม่พบ)

หมายเหตุ Class: `App\Models\ActivityModel` / `App\Controllers\ActivityController` — คนละ Class กับ `App\Models\ActivityLogModel` / `App\Controllers\ActivityLogController` (Phase 11, Audit Trail) แม้ชื่อจะใกล้เคียงกัน

---

### role_permissions (Phase 10)

Purpose: กำหนดสิทธิ์การใช้งานระบบตาม Role/Module/Action (Database-first Permission)

Columns: id, role (enum: Admin/Editor/Staff), module, action

Primary Key: id
Unique Key: `role_permissions_unique` (role, module, action)

Notes:
- Permission ของระบบอ่านจากฐานข้อมูลเป็นหลัก หากไม่สามารถอ่านฐานข้อมูลได้ จะ Fallback ไปใช้ `app/config/permissions.php` โดยอัตโนมัติ
- Seeder `003_seed_role_permissions.sql` สร้างข้อมูลเริ่มต้น 52 แถว: Admin (ทุก Module — View/Create/Edit/Delete), Editor (ทุก Module ยกเว้น Users — View/Create/Edit), Staff (ทุก Module ยกเว้น Users — View)
- Phase 11 เพิ่มอีก 1 แถวผ่าน `004_seed_activity_log_permissions.sql` (Admin/activity_log/view) รวมเป็น 53 แถว
- Phase 13 เพิ่มอีก 8 แถวผ่าน `005_seed_activities_permissions.sql` (Admin 4 / Editor 3 / Staff 1 — Pattern เดียวกับ gallery) รวมเป็น 61 แถว

---

### activity_logs (Phase 11)

Purpose: บันทึกประวัติการใช้งานระบบ (Audit Trail) — Login / Login Failed / Logout และ Create/Update/Delete ของทุกโมดูล

| Column | Type | Description |
|--------|------|-------------|
| id | int(11) | Primary Key, Auto Increment |
| user_id | int(11) NULL | อ้างอิงผู้ทำรายการ — ไม่ใส่ FK Constraint ตามมาตรฐานโปรเจกต์ — เป็น NULL ได้กรณี Login ล้มเหลวก่อนทราบตัวตน |
| username | varchar(50) | Snapshot ชื่อผู้ใช้ ณ เวลาที่เกิดเหตุการณ์ |
| role | varchar(20) | Snapshot สิทธิ์ ณ เวลาที่เกิดเหตุการณ์ |
| module | varchar(50) | ชื่อโมดูลที่ถูกกระทำ เช่น departments, users, auth |
| action | varchar(20) | ประเภทการกระทำ เช่น create, update, delete, login, login_failed, logout |
| description | varchar(255) | ข้อความสรุปเหตุการณ์แบบอ่านง่าย |
| ip_address | varchar(45) NULL | IP Address ผู้ทำรายการ รองรับทั้ง IPv4/IPv6 |
| created_at | timestamp | เวลาที่เกิดเหตุการณ์ (DEFAULT CURRENT_TIMESTAMP) |

Index:
- PRIMARY KEY (id)
- `idx_activity_logs_module` (module)
- `idx_activity_logs_action` (action)
- `idx_activity_logs_user_id` (user_id)
- `idx_activity_logs_created_at` (created_at)

Design Notes:
- **Insert-only / Immutable** — ไม่มี Update, ไม่มี Delete, ไม่มี Soft Delete
- **ไม่มีคอลัมน์ `updated_at`** — ใช้ `created_at` เพียงคอลัมน์เดียว
- **ไม่มีคอลัมน์ `deleted_at`**
- **ไม่มี Foreign Key Constraint** — สอดคล้องกับมาตรฐานโปรเจกต์ที่ไม่ใส่ FK ในทุกตาราง โดยเก็บ Snapshot ของ `username`/`role` ไว้แทน เพื่อรักษาประวัติแม้ผู้ใช้ที่เกี่ยวข้องจะถูกแก้ไข/ลบในภายหลัง
- การ Insert ทำผ่าน `App\Core\ActivityLogger::log()` เท่านั้น (ครอบ try/catch ไม่ให้ Business Logic หลักล้มเหลวหากบันทึก Log ไม่สำเร็จ)

Migration: `010_create_activity_logs_table.sql` ✅ Executed

Permission: Module `activity_log` — เฉพาะ Admin เท่านั้นที่มีสิทธิ์ `view` (ไม่มี create/edit/delete เพราะระบบสร้าง Log เอง) — Seeder `004_seed_activity_log_permissions.sql` ✅ Executed

---

## Migration History

### Phase 2
- `001_create_users_table.sql` — สร้างตาราง users
- `001_seed_default_admin.sql` — เพิ่มผู้ใช้เริ่มต้น (username: `admin`, first_login = TRUE)

### Phase 4
- `002_create_departments_table.sql` — สร้างตาราง departments
- `002_seed_departments.sql` — เพิ่มข้อมูลตัวอย่าง 12 แถว
- `003_departments_soft_delete_unique_fix.sql` — เพิ่ม Generated Columns `code_active`/`name_active`, ย้าย UNIQUE INDEX ไปยัง Generated Columns เพื่อรองรับ Soft Delete โดยไม่แก้ไข code/name เดิม

### Phase 5
- `004_employee_add_soft_delete_and_timestamps.sql` — เพิ่ม created_at/updated_at/deleted_at ให้ employee

### Phase 6
- `005_news_add_status_soft_delete_and_timestamps.sql` — เพิ่ม status/created_at/updated_at/deleted_at/idx_news_status ให้ news

### Phase 7
- `006_legislation_add_fields_and_soft_delete.sql` — เพิ่ม document_number/detail/effective_date/status/created_at/updated_at/deleted_at/idx_legislation_status

### Phase 8
- `007_create_documents_table.sql` — สร้างตาราง documents

### Phase 9
- `008_create_gallery_table.sql` — สร้างตาราง gallery

### Phase 10
- `009_create_role_permissions_table.sql` — สร้างตาราง role_permissions
- `003_seed_role_permissions.sql` — seed ข้อมูลเริ่มต้น 52 แถว

### Phase 11
- `010_create_activity_logs_table.sql` — สร้างตาราง activity_logs
- `004_seed_activity_log_permissions.sql` — seed สิทธิ์ Admin/activity_log/view

### Phase 13
- `011_create_activities_table.sql` — สร้างตาราง activities
- `005_seed_activities_permissions.sql` — seed สิทธิ์ Admin/Editor/Staff (8 แถว)

---

## Upload Directories

`uploads/`

| Folder | Extensions | Max Size |
|--------|-----------|----------|
| `employees/` | jpg, jpeg, png, webp | 2 MB |
| `news/` | jpg, jpeg, png, webp | 2 MB |
| `documents/` | pdf, doc, docx, xls, xlsx, ppt, pptx | 10 MB |
| `gallery/` | jpg, jpeg, png, webp | 2 MB |
| `activities/` | jpg, jpeg, png, webp | 2 MB |

Filename: สุ่มด้วย `random_bytes()` ผ่าน UploadHelper

---

## Security

- PDO Prepared Statements — Enabled (ทุก Query)
- CSRF Protection — Enabled
- XSS Protection — Enabled
- Permission System — Enabled (Database-first ผ่าน `role_permissions` + Fallback ไป `app/config/permissions.php`)
- Soft Delete — Enabled (ทุกโมดูลยกเว้น `activity_logs` ซึ่งเป็น Insert-only)
- Upload MIME Validation — Enabled
- Random Filename — Enabled

---

## Current Database Status (อัปเดตล่าสุด — หลัง Phase 13)

Tables: users, departments, employee, news, legislation, documents, gallery, activities, role_permissions, activity_logs (รวม 10 ตาราง)

Executed Migrations: 001–011 (ครบทั้งหมด)
Executed Seeders: 001–005 (ครบทั้งหมด)

Status: Database Ready — Phase 13 (Activities Management System) Completed
