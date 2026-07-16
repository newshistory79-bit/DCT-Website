<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use Throwable;

class Permission
{
    private static ?array $map = null;

    // ตรวจสอบว่า Role ที่ระบุมีสิทธิ์ทำ Action นี้กับ Module นี้หรือไม่
    public static function can(string $role, string $module, string $action): bool
    {
        $map = self::load();

        return in_array($action, $map[$role][$module] ?? [], true);
    }

    private static function load(): array
    {
        if (self::$map === null) {
            self::$map = self::loadFromDatabase() ?? self::loadFromConfig();
        }

        return self::$map;
    }

    // Source of Truth หลัก (Phase 10): อ่านสิทธิ์จากตาราง role_permissions
    // คืนค่า null เมื่อ Query ล้มเหลว หรือตารางยังไม่มีข้อมูลเลย เพื่อให้ตกไปใช้ Fallback จาก Config
    private static function loadFromDatabase(): ?array
    {
        try {
            $pdo  = Database::getInstance()->getConnection();
            $stmt = $pdo->query('SELECT role, module, action FROM role_permissions');
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            error_log('[Permission] Query ตาราง role_permissions ล้มเหลว ใช้ Fallback จาก config/permissions.php: ' . $e->getMessage());

            return null;
        }

        if (empty($rows)) {
            return null;
        }

        $map = [];

        foreach ($rows as $row) {
            $map[$row['role']][$row['module']][] = $row['action'];
        }

        return $map;
    }

    // Fallback: อ่านจาก app/config/permissions.php (ใช้เมื่อฐานข้อมูลใช้งานไม่ได้ หรือยังไม่มีข้อมูลในตาราง)
    private static function loadFromConfig(): array
    {
        return require APP_PATH . '/config/permissions.php';
    }
}
