<?php

declare(strict_types=1);

// Path หลักของระบบ
define('ROOT_PATH', dirname(__DIR__, 2));
define('APP_PATH', ROOT_PATH . '/app');
define('UPLOAD_PATH', ROOT_PATH . '/uploads');
define('LOG_PATH', ROOT_PATH . '/logs');

// โหลด Configuration ของระบบ
require_once APP_PATH . '/config/config.php';

// ลงทะเบียน Autoloader (ทดแทน Composer autoload)
require_once APP_PATH . '/core/Autoloader.php';
\App\Core\Autoloader::register();

// โหลด Helper Function
require_once APP_PATH . '/helpers/functions.php';

// ตั้งค่า Error Logging ไปยัง logs/error.log
ini_set('log_errors', '1');
ini_set('error_log', LOG_PATH . '/error.log');

// ลงทะเบียน Global Error/Exception/Fatal Error Handler
\App\Core\ErrorHandler::register();

// เริ่ม Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
