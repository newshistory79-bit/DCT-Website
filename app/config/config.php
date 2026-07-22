<?php

declare(strict_types=1);

// Environment: development หรือ production
define('APP_ENV', 'development');

define('APP_NAME', 'Department of Technology and Communications');
define('APP_VERSION', '1.0.0');

// URL หลักของเว็บไซต์ - ปรับตาม path ที่ติดตั้งจริงบนเครื่อง
define('BASE_URL', 'http://localhost/DTC-Website/public/');

// URL สำหรับเข้าถึงไฟล์ที่อัปโหลด - uploads/ อยู่นอกโฟลเดอร์ public/ จึงต้องคำนวณ URL แยกจาก BASE_URL
define('UPLOAD_URL', 'http://localhost/DTC-Website/uploads/');

date_default_timezone_set('Asia/Vientiane');

// อายุ Session (วินาที)
define('SESSION_LIFETIME', 3600);

if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}
