<?php

declare(strict_types=1);

// Escape output กัน XSS
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

// สร้าง URL เต็มจาก path ที่ระบุ โดยอ้างอิงจาก BASE_URL
function baseUrl(string $path = ''): string
{
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}

// สร้าง URL สำหรับไฟล์ใน uploads/ (แยกจาก baseUrl() เพราะ uploads/ อยู่นอกโฟลเดอร์ public/)
function uploadUrl(string $path = ''): string
{
    return rtrim(UPLOAD_URL, '/') . '/' . ltrim($path, '/');
}

// Redirect ไปยัง path อื่นแล้วหยุดการทำงานทันที
function redirect(string $path): void
{
    header('Location: ' . baseUrl($path));
    exit;
}

// สร้าง CSRF Token ประจำ Session (สร้างครั้งเดียวแล้วใช้ซ้ำจนกว่า Session จะหมดอายุ)
function generateCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

// ตรวจสอบ CSRF Token ที่ส่งมากับ Form
function verifyCsrfToken(string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// ตรวจสอบสิทธิ์ของผู้ใช้ปัจจุบัน (Session) ต่อ Module/Action - ใช้ใน View เพื่อซ่อน/แสดงปุ่มตาม Permission
function can(string $module, string $action): bool
{
    return \App\Core\Permission::can($_SESSION['role'] ?? '', $module, $action);
}

// แปลงวันที่ (Y-m-d ฯลฯ) เป็นวัน/เดือนย่อไทย/ปี ค.ศ. สำหรับ Badge วันที่ฝั่ง Public เช่น 20 พ.ย. 2024
// คืนค่า null หาก $date ว่างหรือแปลงไม่ได้ (View ต้องเช็คก่อนแสดงผล)
function thaiDateParts(?string $date): ?array
{
    if ($date === null || $date === '') {
        return null;
    }

    $timestamp = strtotime($date);

    if ($timestamp === false) {
        return null;
    }

    $months = [
        1 => 'ม.ค.', 2 => 'ก.พ.', 3 => 'มี.ค.', 4 => 'เม.ย.', 5 => 'พ.ค.', 6 => 'มิ.ย.',
        7 => 'ก.ค.', 8 => 'ส.ค.', 9 => 'ก.ย.', 10 => 'ต.ค.', 11 => 'พ.ย.', 12 => 'ธ.ค.',
    ];

    return [
        'day'   => date('j', $timestamp),
        'month' => $months[(int) date('n', $timestamp)],
        'year'  => date('Y', $timestamp),
    ];
}

// แปลงขนาดไฟล์ (Byte) เป็นข้อความอ่านง่าย (KB/MB) สำหรับ Documents ฝั่ง Public
// คืนค่า '-' หาก $bytes เป็น null (ไม่ทราบขนาดไฟล์)
function formatFileSize(?int $bytes): string
{
    if ($bytes === null) {
        return '-';
    }

    if ($bytes >= 1048576) {
        return round($bytes / 1048576, 2) . ' MB';
    }

    return round($bytes / 1024, 1) . ' KB';
}

// หน้า 404 กลาง ใช้ร่วมกันได้ทุก Public Controller (News/Activities/Departments/ฯลฯ)
// เรียกแล้ว return ทันทีจาก Controller เสมอ ห้ามมีโค้ดทำงานต่อหลังเรียกฟังก์ชันนี้
function renderNotFound(): void
{
    http_response_code(404);

    $pageTitle       = 'ไม่พบหน้าที่คุณต้องการ';
    $metaDescription = 'ไม่พบหน้าที่คุณต้องการ กรุณาตรวจสอบ URL อีกครั้ง หรือกลับไปหน้าหลัก';
    $activeNav       = '';

    require APP_PATH . '/views/public/404.php';
}

// Empty State กลาง ใช้ข้อความ/โครงสร้างเดียวกันทุกโมดูลฝั่ง Public (ห้ามเขียนข้อความ Empty State แยกเอง)
function renderEmptyState(string $iconName = 'news'): void
{
    require APP_PATH . '/includes/public_empty_state.php';
}
