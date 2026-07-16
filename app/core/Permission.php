<?php

declare(strict_types=1);

namespace App\Core;

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
            self::$map = require APP_PATH . '/config/permissions.php';
        }

        return self::$map;
    }
}
