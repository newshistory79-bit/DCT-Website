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
<title>ປ່ຽນລະຫັດຜ່ານ - <?= e(APP_NAME) ?></title>
</head>
<body>
    <h1>ຕ້ອງປ່ຽນລະຫັດຜ່ານກ່ອນນຳໃຊ້ງານ</h1>
    <p>ບັນຊີຂອງທ່ານເຂົ້າສູ່ລະບົບເປັນຄັ້ງທຳອິດ ລະບົບກຳນົດໃຫ້ຕ້ອງປ່ຽນລະຫັດຜ່ານກ່ອນນຳໃຊ້ສ່ວນອື່ນຂອງລະບົບ</p>
    <p><em>ຟອມປ່ຽນລະຫັດຜ່ານຈະພັດທະນາໃນລຳດັບຖັດໄປ (ຍັງບໍ່ຢູ່ໃນຂອບເຂດຂອງ Phase 2)</em></p>
    <a href="<?= e(baseUrl('admin/logout.php')) ?>">ອອກຈາກລະບົບ</a>
</body>
</html>
