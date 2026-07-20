<?php

declare(strict_types=1);

/** @var array|null $department */
/** @var string $csrfToken */
/** @var string|null $formError */

$isEdit = $department !== null;
$title  = $isEdit ? 'แก้ไขแผนก' : 'เพิ่มแผนก';
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
            $isEdit ? 'แก้ไขข้อมูลแผนก "' . $department['name'] . '"' : 'กรอกข้อมูลเพื่อเพิ่มแผนกใหม่เข้าสู่ระบบ',
            [['label' => 'กลับไปรายการ', 'url' => baseUrl('admin/departments/index.php'), 'class' => 'btn-secondary']]
        ); ?>

        <?php if ($formError !== null): ?>
            <p class="alert alert-error"><?= e($formError) ?></p>
        <?php endif; ?>

        <form method="post"
              action="<?= e(baseUrl('admin/departments/form.php' . ($isEdit ? '?id=' . $department['id'] : ''))) ?>"
              class="data-form admin-form-sectioned">
            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= (int) $department['id'] ?>">
            <?php endif; ?>

            <?php renderAdminSectionCard('ข้อมูลทั่วไป', function () use ($department): void { ?>
                <label for="code">รหัสแผนก</label>
                <input type="text" id="code" name="code" maxlength="20" required
                       pattern="[A-Z0-9\-]+" class="input-uppercase"
                       value="<?= e($department['code'] ?? '') ?>">
                <small>อนุญาตเฉพาะตัวอักษร A-Z, ตัวเลข 0-9 และเครื่องหมาย - เท่านั้น</small>

                <label for="name">ชื่อแผนก</label>
                <input type="text" id="name" name="name" maxlength="255" required
                       value="<?= e($department['name'] ?? '') ?>">

                <label for="description">คำอธิบาย</label>
                <textarea id="description" name="description" rows="4"><?= e($department['description'] ?? '') ?></textarea>

                <label for="sort_order">ลำดับการแสดงผล</label>
                <input type="number" id="sort_order" name="sort_order" min="0" step="1"
                       value="<?= (int) ($department['sort_order'] ?? 0) ?>">
            <?php }, 'ข้อมูลพื้นฐานของแผนก'); ?>

            <?php renderAdminSectionCard('สถานะ', function () use ($department): void { ?>
                <label for="status">สถานะการใช้งาน</label>
                <select id="status" name="status">
                    <option value="Active" <?= ($department['status'] ?? 'Active') === 'Active' ? 'selected' : '' ?>>Active</option>
                    <option value="Inactive" <?= ($department['status'] ?? '') === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            <?php }, 'กำหนดว่าแผนกนี้เปิดใช้งานอยู่หรือไม่'); ?>

            <?php renderAdminSectionCard('การดำเนินการ', function () use ($isEdit): void { ?>
                <div class="admin-form-actions">
                    <button type="submit" class="btn-primary"><?= $isEdit ? 'บันทึกการแก้ไข' : 'เพิ่มแผนก' ?></button>
                    <a href="<?= e(baseUrl('admin/departments/index.php')) ?>" class="btn-ghost">ยกเลิก</a>
                </div>
            <?php }, 'ตรวจสอบข้อมูลให้ถูกต้องก่อนบันทึก'); ?>
        </form>
    </main>
</div>

<?php require APP_PATH . '/includes/admin_footer.php'; ?>
</body>
</html>
