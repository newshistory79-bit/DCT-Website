# Database Documentation

## Project
DTC-Website

## Database

- Database Name: tcsp
- DBMS: MariaDB 10.4.32
- Character Set: utf8mb4
- Collation: utf8mb4_general_ci

---

# Current Tables

| Table | Description |
|--------|-------------|
| users | ระบบผู้ใช้งาน |
| departments | ข้อมูลแผนก |
| employee | ข้อมูลพนักงาน |
| legislation | ข้อมูลกฎหมาย |
| news | ข่าวสาร |

---

# Migration History

## Phase 2

### 001_create_users_table.sql
สร้างตาราง users

### 001_seed_default_admin.sql
เพิ่มผู้ใช้เริ่มต้น

Username: admin

Password: Admin@123456

first_login = TRUE

---

## Phase 4

### 002_create_departments_table.sql
สร้างตาราง departments

### 002_seed_departments.sql
เพิ่มข้อมูลตัวอย่าง 12 แถว

### 003_departments_soft_delete_unique_fix.sql
เพิ่ม Generated Columns

- code_active
- name_active

ย้าย UNIQUE INDEX ไปยัง Generated Columns

รองรับ Soft Delete โดยไม่แก้ไข code/name เดิม

---

# Current Status

Completed

- Phase 1
- Phase 2
- Phase 3
- Phase 3.5
- Phase 4

Next Phase

- Phase 5 Employees CRUD

---

# Notes

- ใช้ PDO Prepared Statement ทุก Query
- ใช้ Soft Delete ผ่าน deleted_at
- ใช้ CSRF Protection ทุก Form
- ใช้ Permission Middleware
- ใช้ BaseController/BaseModel
- ใช้ MariaDB 10.4.32

# DATABASE DOCUMENTATION

Project Database

**Database Name:** tcsp

**DBMS:** MariaDB 10.4.x

---

# Tables

## users

Purpose

ระบบผู้ใช้งาน

Main Columns

- id
- username
- password
- role
- first_login
- created_at
- updated_at
- deleted_at

---

## departments

Purpose

ข้อมูลแผนกภายในองค์กร

Columns

- id
- code
- name
- description
- status
- sort_order
- created_at
- updated_at
- deleted_at

Generated Columns

- code_active
- name_active

Indexes

PRIMARY KEY

UNIQUE

- code_active
- name_active

Soft Delete

ใช้ deleted_at

---

## employee

Purpose

ข้อมูลพนักงาน

Columns

- ID
- Fname
- Lname
- birth_date
- gender
- phone
- email
- position
- address
- image

Additional Columns

- created_at
- updated_at
- deleted_at

Primary Key

- ID

Soft Delete

ใช้ deleted_at

---

## news

Purpose

ข่าวประชาสัมพันธ์

Status

ยังไม่ได้พัฒนา

---

## legislation

Purpose

กฎหมาย / ระเบียบ

Status

ยังไม่ได้พัฒนา

---

# Executed Migrations

## 002_create_departments_table.sql

Created

departments

---

## 003_departments_soft_delete_unique_fix.sql

Added

- code_active
- name_active

Updated Unique Index

- departments_code_active_unique
- departments_name_active_unique

---

## 004_employee_add_soft_delete_and_timestamps.sql

Added

employee

- created_at
- updated_at
- deleted_at

---

# Upload Directories

uploads/

employees/

Used by Employee Module

Validation

- jpg
- jpeg
- png
- webp

Maximum Size

2 MB

Filename

Generated using

random_bytes()

---

# Security

PDO Prepared Statements

Enabled

CSRF Protection

Enabled

XSS Protection

Enabled

Permission System

Enabled

Soft Delete

Enabled

Upload MIME Validation

Enabled

Random Filename

Enabled

---

# Current Database Status

Tables

- users
- departments
- employee
- news
- legislation

Status

Database Ready for Phase 6

## Migration 005
File:
database/migrations/005_news_add_status_soft_delete_and_timestamps.sql

Purpose:
เพิ่มคอลัมน์สำหรับระบบจัดการข่าว

Columns
- status ENUM('Draft','Published')
- created_at TIMESTAMP
- updated_at TIMESTAMP
- deleted_at DATETIME

Index
- idx_news_status(status)

Status:
Created ✅
Executed ❌
# Table: news

Columns

- ID
- title
- detail
- image
- activity_date
- status
- created_at
- updated_at
- deleted_at

Indexes

- PRIMARY KEY(ID)
- idx_news_status(status)

Soft Delete
- deleted_at

Image Upload
- uploads/news/

Status
- Draft
- Published

---

# legislation

Purpose

จัดเก็บข้อมูลกฎหมาย ระเบียบ และประกาศ

## Columns

| Column | Type | Description |
|---------|------|-------------|
| ID | int | Primary Key |
| title | varchar(255) | ชื่อกฎหมาย |
| document_number | varchar(50) | เลขที่ประกาศ |
| detail | text | รายละเอียด |
| effective_date | date | วันที่มีผลบังคับใช้ |
| status | enum(Draft, Published) | สถานะ |
| created_at | timestamp | วันที่สร้าง |
| updated_at | timestamp | วันที่แก้ไข |
| deleted_at | datetime | Soft Delete |

## Index

- PRIMARY KEY (ID)
- idx_legislation_status

## Notes

- ใช้ Soft Delete
- Filter ตาม Status
- Search title/document_number/detail
- ไม่มี File Upload (ย้ายไป Phase 8)

---

# documents (Phase 8)

Purpose

จัดเก็บเอกสารดาวน์โหลด (PDF/Office Files)

## Columns

| Column | Type | Description |
|---------|------|-------------|
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

## Index

- PRIMARY KEY (id)
- idx_documents_status

## Upload Rules

- Extensions: pdf, doc, docx, xls, xlsx, ppt, pptx
- Maximum Size: 10 MB
- Create บังคับแนบไฟล์ / Edit ไม่บังคับ
- Soft Delete ไม่ลบไฟล์จริง
- uploads/documents/

## Notes

- MIME Whitelist รวม `application/CDFV2`/`application/x-cfb` (ไฟล์ .doc/.xls/.ppt แบบเก่าใช้ OLE Compound
  Format ร่วมกัน libmagic แยกชนิดเฉพาะไม่ได้) และ `application/zip` (ไฟล์ .docx/.xlsx/.pptx เป็น ZIP Container)

---

# gallery (Phase 9)

Purpose

คลังภาพกิจกรรม (Single Table, 1 รูป = 1 รายการ, ไม่มี Album/Foreign Key)

## Columns

| Column | Type | Description |
|---------|------|-------------|
| id | int | Primary Key |
| title | varchar(255) | ชื่อภาพ/ชุดกิจกรรม |
| description | text | คำอธิบายภาพ |
| image | varchar(255) | ชื่อไฟล์ภาพที่จัดเก็บจริง (สุ่มด้วย UploadHelper) |
| status | enum(Draft, Published) | สถานะ |
| created_at | timestamp | วันที่สร้าง |
| updated_at | timestamp | วันที่แก้ไข |
| deleted_at | datetime | Soft Delete |

## Index

- PRIMARY KEY (id)
- idx_gallery_status

## Upload Rules

- Extensions: jpg, jpeg, png, webp (ไม่รองรับ gif)
- Maximum Size: 2 MB
- Create บังคับแนบรูป / Edit ไม่บังคับ
- Soft Delete ไม่ลบไฟล์จริง
- uploads/gallery/

---

# Current Database Status (อัปเดตล่าสุด — หลัง Phase 9)

Tables

- users
- departments
- employee
- news
- legislation
- documents
- gallery

Executed Migrations (เพิ่มเติมจากเดิม)

- 005_news_add_status_soft_delete_and_timestamps.sql ✅ Executed
- 006_legislation_add_fields_and_soft_delete.sql ✅ Executed
- 007_create_documents_table.sql ✅ Executed
- 008_create_gallery_table.sql ✅ Executed

Status

Database Ready for Phase 10 (Final Permission & System Review)

เพิ่มหัวข้อ

## gallery

อธิบายโครงสร้างตาราง

- id
- title
- description
- image
- status
- created_at
- updated_at
- deleted_at

ระบุ

Primary Key

id

Index

idx_gallery_status(status)

อธิบายความหมายของแต่ละคอลัมน์

อธิบายว่าใช้ Soft Delete

อธิบายว่า image เก็บชื่อไฟล์จริงที่ UploadHelper สุ่ม

ระบุว่ารองรับ

jpg
jpeg
png
webp

ขนาดไม่เกิน

2 MB