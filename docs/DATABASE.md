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