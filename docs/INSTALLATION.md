# Installation Guide

คู่มือติดตั้งระบบ TCSP Administration System (DTC Website) สำหรับ Deploy บนเครื่องใหม่หรือ Environment ใหม่

---

## 1. System Requirements

| รายการ | เวอร์ชันขั้นต่ำ |
|---|---|
| PHP | 8.0+ (ทดสอบจริงบน 8.2.12) พร้อม Extension: `pdo_mysql`, `fileinfo`, `mbstring` |
| Database | MariaDB 10.4+ (ทดสอบจริงบน 10.4.32) หรือ MySQL ที่รองรับ Syntax เดียวกัน |
| Web Server | Apache (แนะนำผ่าน XAMPP) พร้อมเปิดใช้งาน `mod_rewrite` |
| Environment | XAMPP for Windows (Apache + MariaDB + PHP ชุดเดียวกัน) |

ระบบพัฒนาด้วย Native PHP ล้วน — **ไม่ต้องติดตั้ง Composer หรือ Dependency ภายนอกใดๆ**

---

## 2. นำโปรเจกต์ไปวางในเครื่อง

1. คัดลอกโฟลเดอร์โปรเจกต์ทั้งหมดไปไว้ใน `htdocs` ของ XAMPP เช่น `D:\xampp\htdocs\DTC-Website`
2. เปิด XAMPP Control Panel แล้ว Start ทั้ง **Apache** และ **MySQL**

---

## 3. สร้างฐานข้อมูล

1. เปิด phpMyAdmin หรือใช้ MySQL Client แล้วสร้างฐานข้อมูลชื่อ `tcsp`:

   ```sql
   CREATE DATABASE tcsp CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
   ```

2. รัน Migration ทั้งหมดตามลำดับ (อยู่ในโฟลเดอร์ `database/migrations/`):

   ```
   001_create_users_table.sql
   002_create_departments_table.sql
   003_departments_soft_delete_unique_fix.sql
   004_employee_add_soft_delete_and_timestamps.sql
   005_news_add_status_soft_delete_and_timestamps.sql
   006_legislation_add_fields_and_soft_delete.sql
   007_create_documents_table.sql
   008_create_gallery_table.sql
   009_create_role_permissions_table.sql
   010_create_activity_logs_table.sql
   ```

3. รัน Seeder ทั้งหมดตามลำดับ (อยู่ในโฟลเดอร์ `database/seeders/`):

   ```
   001_seed_default_admin.sql
   002_seed_departments.sql
   003_seed_role_permissions.sql
   004_seed_activity_log_permissions.sql
   ```

   ตัวอย่างการรันผ่าน MySQL Client (Windows PowerShell/CMD, ปรับ Path ให้ตรงกับเครื่องจริง):

   ```
   mysql -u root tcsp < database/migrations/001_create_users_table.sql
   mysql -u root tcsp < database/migrations/002_create_departments_table.sql
   ... (รันต่อจนครบทุกไฟล์ตามลำดับเลขไฟล์)
   mysql -u root tcsp < database/seeders/001_seed_default_admin.sql
   ... (รันต่อจนครบ)
   ```

   > หมายเหตุ: ไฟล์ `database/database.sql` เป็น Schema เริ่มต้นที่ใช้อ้างอิงตอนเริ่มโปรเจกต์เท่านั้น (ยังไม่มี Column/Table ที่เพิ่มภายหลังจาก Migration) **ห้ามใช้ไฟล์นี้แทนการรัน Migration ทั้งชุดด้านบน**

4. ตรวจสอบว่ามีทั้งหมด 9 ตาราง: `users`, `departments`, `employee`, `news`, `legislation`, `documents`, `gallery`, `role_permissions`, `activity_logs`

---

## 4. ตั้งค่าไฟล์ Config

### 4.1 การเชื่อมต่อฐานข้อมูล — `app/config/database.php`

```php
return [
    'host'    => '127.0.0.1',
    'port'    => '3306',
    'dbname'  => 'tcsp',
    'user'    => 'root',
    'pass'    => '',
    'charset' => 'utf8mb4',
];
```

ปรับ `user`/`pass` ให้ตรงกับ MySQL Account จริงของเครื่อง (ค่าเริ่มต้นของ XAMPP คือ `root` ไม่มีรหัสผ่าน)

### 4.2 ค่าทั่วไปของระบบ — `app/config/config.php`

- `BASE_URL` — ต้องตรงกับ Path ที่วางโปรเจกต์จริง เช่น `http://localhost/DTC-Website/public/`
- `UPLOAD_URL` — URL สำหรับเข้าถึงไฟล์ในโฟลเดอร์ `uploads/` เช่น `http://localhost/DTC-Website/uploads/`
- `APP_ENV` — ตั้งเป็น `development` ระหว่างพัฒนา (แสดง Error เต็มรูปแบบ) และเปลี่ยนเป็น `production` ก่อนใช้งานจริง (ซ่อนรายละเอียด Error จากผู้ใช้)

---

## 5. สิทธิ์การเข้าถึงโฟลเดอร์ (Uploads)

ตรวจสอบว่าโฟลเดอร์ต่อไปนี้มีสิทธิ์ให้ Apache เขียนไฟล์ได้:

```
uploads/employees/
uploads/news/
uploads/documents/
uploads/gallery/
logs/
```

บน Windows/XAMPP โดยทั่วไปไม่ต้องปรับสิทธิ์เพิ่มเติม แต่หาก Deploy บน Linux ต้องตั้งสิทธิ์เขียนให้กับ User ที่รัน Apache (เช่น `www-data`)

---

## 6. เข้าสู่ระบบครั้งแรก

1. เปิดเบราว์เซอร์ไปที่ `http://localhost/DTC-Website/public/admin/login.php`
2. เข้าสู่ระบบด้วยบัญชีเริ่มต้นจาก Seeder:
   - **Username:** `admin`
   - **Password:** `Admin@123456`
3. ระบบจะบังคับให้เปลี่ยนรหัสผ่านทันทีในการ Login ครั้งแรก (`first_login = TRUE`) — เปลี่ยนรหัสผ่านใหม่แล้วเข้าใช้งานตามปกติ

---

## 7. โครงสร้างโปรเจกต์โดยสรุป

```
app/
  config/       ค่าตั้งค่าระบบ (database, config, permissions, roles)
  controllers/  Controller ของแต่ละโมดูล
  core/         BaseController, BaseModel, Database, Permission, ActivityLogger, ErrorHandler ฯลฯ
  helpers/      ฟังก์ชันช่วยเหลือ (functions.php)
  middleware/   AuthMiddleware
  models/       Model ของแต่ละโมดูล
  views/        หน้า HTML/PHP View
  includes/     Header/Sidebar/Footer ที่ใช้ร่วมกัน
public/         Public Entry Point ทั้งหมด (จุดที่เว็บเซิร์ฟเวอร์ชี้มาที่นี่)
uploads/        ไฟล์ที่ผู้ใช้อัปโหลด (อยู่นอก public/ เพื่อความปลอดภัย)
database/       Migration และ Seeder ทั้งหมด
docs/           เอกสารประกอบโปรเจกต์
```

---

## 8. โมดูลที่มีในระบบ (ณ Phase 11)

Authentication, Dashboard, Departments, Employees, News, Legislation, Documents, Gallery, Users (พร้อม Database Permission System), Activity Log

แต่ละโมดูลรองรับ CRUD, Search, Filter, Sort, Pagination, CSRF Protection, SQL Injection Protection, XSS Protection, Soft Delete (ยกเว้น Activity Log ที่เป็น Insert-only) และตรวจสอบ Permission ตาม Role (Admin/Editor/Staff) ทุกจุด

---

## 9. Troubleshooting เบื้องต้น

| อาการ | สาเหตุที่เป็นไปได้ | วิธีแก้ |
|---|---|---|
| หน้าเว็บขึ้น "ไม่สามารถเชื่อมต่อฐานข้อมูลได้" | ค่าใน `app/config/database.php` ไม่ตรงกับ MySQL จริง หรือ MySQL Service ไม่ได้ Start | ตรวจสอบ `app/config/database.php` และสถานะ MySQL ใน XAMPP Control Panel |
| Login ไม่ผ่านทั้งที่รหัสถูก | ยังไม่ได้รัน Seeder `001_seed_default_admin.sql` | รัน Seeder ตามข้อ 3 |
| อัปโหลดไฟล์ไม่สำเร็จ | โฟลเดอร์ `uploads/` ไม่มีสิทธิ์เขียน | ตรวจสอบสิทธิ์ตามข้อ 5 |
| ลิงก์ในเว็บพัง/รูปไม่ขึ้น | `BASE_URL`/`UPLOAD_URL` ใน `app/config/config.php` ไม่ตรงกับ Path จริง | แก้ไขตามข้อ 4.2 |
