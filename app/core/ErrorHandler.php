<?php

declare(strict_types=1);

namespace App\Core;

use Throwable;

class ErrorHandler
{
    public static function register(): void
    {
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    // Error/Warning/Notice ทั่วไป
    public static function handleError(int $severity, string $message, string $file, int $line): bool
    {
        if (!(error_reporting() & $severity)) {
            return false;
        }

        error_log(sprintf('[Error] %s in %s:%d', $message, $file, $line));

        // คืนค่า false เสมอ เพื่อให้ PHP แสดงผล error ตาม display_errors เดิม (ไม่เปลี่ยนพฤติกรรมที่มีอยู่)
        return false;
    }

    // Uncaught Exception
    public static function handleException(Throwable $exception): void
    {
        error_log(sprintf(
            '[Exception] %s in %s:%d%s%s',
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            PHP_EOL,
            $exception->getTraceAsString()
        ));

        self::renderFriendlyError($exception->getMessage(), $exception->getTraceAsString());
    }

    // Fatal Error ที่ set_error_handler ดักไม่ได้ (เช่น Parse Error, Out of Memory)
    public static function handleShutdown(): void
    {
        $error = error_get_last();

        if ($error === null || !in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
            return;
        }

        error_log(sprintf('[Fatal Error] %s in %s:%d', $error['message'], $error['file'], $error['line']));

        self::renderFriendlyError($error['message']);
    }

    private static function renderFriendlyError(string $message, string $trace = ''): void
    {
        if (!headers_sent()) {
            http_response_code(500);
        }

        if (defined('APP_ENV') && APP_ENV === 'development') {
            echo '<pre>' . e($message) . ($trace !== '' ? PHP_EOL . e($trace) : '') . '</pre>';
            return;
        }

        echo 'เกิดข้อผิดพลาดในระบบ กรุณาลองใหม่ภายหลัง';
    }
}
