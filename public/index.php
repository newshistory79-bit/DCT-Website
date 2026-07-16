<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/core/bootstrap.php';

use App\Core\Database;

/**
 * Phase 1 Front Controller
 * ยังไม่มี Router/Controller ใช้งานจริง - หน้านี้ใช้ทดสอบการเชื่อมต่อฐานข้อมูลเท่านั้น
 * ระบบ Routing ไปยัง Controller จะเริ่มใช้งานตั้งแต่ Phase 2 เป็นต้นไป
 */
try {
    $pdo    = Database::getInstance()->getConnection();
    $stmt   = $pdo->query('SELECT DATABASE() AS db_name, VERSION() AS db_version');
    $result = $stmt->fetch();

    echo '<h1>' . e(APP_NAME) . '</h1>';
    echo '<p>Phase 1: Foundation setup สำเร็จ</p>';
    echo '<p>เชื่อมต่อฐานข้อมูล: <strong>' . e((string) $result['db_name']) . '</strong></p>';
    echo '<p>MySQL/MariaDB Version: <strong>' . e((string) $result['db_version']) . '</strong></p>';
} catch (\Throwable $ex) {
    error_log('[Front Controller Error] ' . $ex->getMessage());
    http_response_code(500);
    echo 'เกิดข้อผิดพลาดในการเชื่อมต่อระบบ กรุณาลองใหม่ภายหลัง';
}
