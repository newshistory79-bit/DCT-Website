<?php

declare(strict_types=1);

namespace App\Core;

class Autoloader
{
    public static function register(): void
    {
        spl_autoload_register([self::class, 'load']);
    }

    private static function load(string $class): void
    {
        $prefix = 'App\\';

        if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
            return;
        }

        $relative = substr($class, strlen($prefix));
        $parts    = explode('\\', $relative);
        $className = array_pop($parts);

        // โฟลเดอร์ใช้ lowercase ตาม Naming Convention ส่วนไฟล์คลาสใช้ PascalCase ตามชื่อคลาส
        $folders   = array_map('strtolower', $parts);
        $folders[] = $className;

        $file = APP_PATH . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $folders) . '.php';

        if (is_file($file)) {
            require_once $file;
        }
    }
}
