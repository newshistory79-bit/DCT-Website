<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/core/bootstrap.php';

use App\Middleware\AuthMiddleware;

// Phase 2: หน้านี้เป็นเพียงจุดที่ first_login เด้งมาบังคับให้เปลี่ยนรหัสผ่าน
// ฟอร์มเปลี่ยนรหัสผ่านจริง (รับค่า/บันทึกรหัสผ่านใหม่) จะพัฒนาในลำดับถัดไป ยังไม่อยู่ในขอบเขต Phase 2
AuthMiddleware::handle();
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>เปลี่ยนรหัสผ่าน - <?= e(APP_NAME) ?></title>
</head>
<body>
    <h1>ต้องเปลี่ยนรหัสผ่านก่อนใช้งาน</h1>
    <p>บัญชีของคุณเข้าสู่ระบบเป็นครั้งแรก ระบบกำหนดให้ต้องเปลี่ยนรหัสผ่านก่อนใช้งานส่วนอื่นของระบบ</p>
    <p><em>ฟอร์มเปลี่ยนรหัสผ่านจะพัฒนาในลำดับถัดไป (ยังไม่อยู่ในขอบเขตของ Phase 2)</em></p>
    <a href="<?= e(baseUrl('admin/logout.php')) ?>">ออกจากระบบ</a>
</body>
</html>
