<?php

declare(strict_types=1);

/** @var array|null $employee */
/** @var string $csrfToken */
/** @var string|null $formError */

$isEdit = $employee !== null;
$title  = $isEdit ? 'แก้ไขข้อมูลพนักงาน' : 'เพิ่มพนักงาน';
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($title) ?> - <?= e(APP_NAME) ?></title>
<link rel="stylesheet" href="<?= e(baseUrl('assets/css/admin.css')) ?>">
<link rel="stylesheet" href="<?= e(baseUrl('assets/css/crud.css')) ?>">
</head>
<body>
<?php require APP_PATH . '/includes/admin_header.php'; ?>

<div class="admin-layout">
    <?php require APP_PATH . '/includes/admin_sidebar.php'; ?>

    <main class="admin-content">
        <?php renderAdminPageHeader(
            $title,
            $isEdit ? 'แก้ไขข้อมูลของ "' . trim(($employee['Fname'] ?? '') . ' ' . ($employee['Lname'] ?? '')) . '"' : 'กรอกข้อมูลเพื่อเพิ่มพนักงานใหม่เข้าสู่ระบบ',
            [['label' => 'กลับไปรายการ', 'url' => baseUrl('admin/employees/index.php'), 'class' => 'btn-secondary']]
        ); ?>

        <?php if ($formError !== null): ?>
            <p class="alert alert-error"><?= e($formError) ?></p>
        <?php endif; ?>

        <form method="post"
              action="<?= e(baseUrl('admin/employees/form.php' . ($isEdit ? '?id=' . $employee['ID'] : ''))) ?>"
              class="data-form admin-form-sectioned"
              enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= (int) $employee['ID'] ?>">
            <?php endif; ?>

            <?php renderAdminSectionCard('ข้อมูลทั่วไป', function () use ($employee): void { ?>
                <label for="fname">ชื่อ</label>
                <input type="text" id="fname" name="fname" maxlength="255" required
                       value="<?= e($employee['Fname'] ?? '') ?>">

                <label for="lname">นามสกุล</label>
                <input type="text" id="lname" name="lname" maxlength="255" required
                       value="<?= e($employee['Lname'] ?? '') ?>">

                <label for="birth_date">วันเกิด</label>
                <input type="date" id="birth_date" name="birth_date"
                       value="<?= e($employee['birth_date'] ?? '') ?>">

                <label for="gender">เพศ</label>
                <select id="gender" name="gender">
                    <option value="Male" <?= ($employee['gender'] ?? 'Male') === 'Male' ? 'selected' : '' ?>>ชาย</option>
                    <option value="Female" <?= ($employee['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>หญิง</option>
                    <option value="Other" <?= ($employee['gender'] ?? '') === 'Other' ? 'selected' : '' ?>>อื่นๆ</option>
                </select>

                <label for="phone">เบอร์โทรศัพท์</label>
                <input type="text" id="phone" name="phone" maxlength="20"
                       value="<?= e($employee['phone'] ?? '') ?>">

                <label for="email">อีเมล</label>
                <input type="email" id="email" name="email" maxlength="100"
                       value="<?= e($employee['email'] ?? '') ?>">

                <label for="position">ตำแหน่ง</label>
                <input type="text" id="position" name="position" maxlength="100"
                       value="<?= e($employee['position'] ?? '') ?>">

                <label for="address">ที่อยู่</label>
                <textarea id="address" name="address" rows="3"><?= e($employee['address'] ?? '') ?></textarea>
            <?php }, 'ข้อมูลส่วนตัวและช่องทางติดต่อของพนักงาน'); ?>

            <?php renderAdminSectionCard('รูปภาพ', function () use ($employee, $isEdit): void { ?>
                <?php if ($isEdit && !empty($employee['image'])): ?>
                    <div class="current-image">
                        <img src="<?= e(uploadUrl('employees/' . $employee['image'])) ?>" alt="">
                    </div>
                <?php endif; ?>
                <label for="image">รูปภาพพนักงาน</label>
                <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp">
                <small>อนุญาตเฉพาะไฟล์ jpg, jpeg, png, webp ขนาดไม่เกิน 2 MB</small>
            <?php }, 'รูปโปรไฟล์ของพนักงาน (ไม่บังคับ)'); ?>

            <div class="admin-form-actions">
                <button type="submit" class="btn-primary"><?= $isEdit ? 'บันทึกการแก้ไข' : 'เพิ่มพนักงาน' ?></button>
                <a href="<?= e(baseUrl('admin/employees/index.php')) ?>" class="btn-ghost">ยกเลิก</a>
            </div>
        </form>
    </main>
</div>

<?php require APP_PATH . '/includes/admin_footer.php'; ?>
</body>
</html>
