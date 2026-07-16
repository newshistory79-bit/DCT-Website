<?php

declare(strict_types=1);

namespace App\Middleware;

class AuthMiddleware
{
    // ตรวจสอบว่า Login อยู่หรือไม่ และ Session ยังไม่หมดอายุ
    public static function handle(): void
    {
        if (!isset($_SESSION['user_id'])) {
            redirect('admin/login.php');
        }

        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > SESSION_LIFETIME) {
            self::forceLogout('session_timeout');
        }

        $_SESSION['last_activity'] = time();

        if (($_SESSION['first_login'] ?? false) === true && !self::isChangePasswordPage()) {
            redirect('admin/change-password.php');
        }
    }

    // ตรวจสอบสิทธิ์ตาม Role ที่อนุญาต ต้อง Login แล้วเท่านั้น
    public static function requireRole(array $allowedRoles): void
    {
        self::handle();

        if (!in_array($_SESSION['role'] ?? '', $allowedRoles, true)) {
            http_response_code(403);
            echo 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้ (Role: ' . e((string) ($_SESSION['role'] ?? '-')) . ')';
            exit;
        }
    }

    // ตรวจสอบสิทธิ์ระดับ Module/Action ตาม Permission Matrix (app/config/permissions.php)
    // ใช้แทน requireRole() ในโมดูลที่ต้องแยกสิทธิ์ View/Create/Edit/Delete ต่อ Role (เริ่มใช้ตั้งแต่ Phase 4)
    public static function requirePermission(string $module, string $action): void
    {
        self::handle();

        $role = $_SESSION['role'] ?? '';

        if (!\App\Core\Permission::can($role, $module, $action)) {
            http_response_code(403);
            echo 'คุณไม่มีสิทธิ์ดำเนินการนี้ (Module: ' . e($module) . ', Action: ' . e($action) . ')';
            exit;
        }
    }

    private static function forceLogout(string $reason): void
    {
        $_SESSION = [];
        session_destroy();
        session_start();

        $_SESSION['login_error'] = ($reason === 'session_timeout')
            ? 'Session หมดอายุ กรุณาเข้าสู่ระบบใหม่อีกครั้ง'
            : 'กรุณาเข้าสู่ระบบ';

        redirect('admin/login.php');
    }

    private static function isChangePasswordPage(): bool
    {
        return isset($_SERVER['SCRIPT_NAME']) && str_contains($_SERVER['SCRIPT_NAME'], 'change-password.php');
    }
}
