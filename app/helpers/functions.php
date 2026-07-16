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

// Debug helper - ใช้เฉพาะ development
function dd(mixed ...$vars): void
{
    echo '<pre>';
    foreach ($vars as $var) {
        var_dump($var);
    }
    echo '</pre>';
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
