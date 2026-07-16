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
        <div class="page-heading">
            <h1><?= e($title) ?></h1>
            <a href="<?= e(baseUrl('admin/departments/index.php')) ?>" class="btn-secondary">กลับไปรายการ</a>
        </div>

        <?php if ($formError !== null): ?>
            <p class="alert alert-error"><?= e($formError) ?></p>
        <?php endif; ?>

        <form method="post"
              action="<?= e(baseUrl('admin/departments/form.php' . ($isEdit ? '?id=' . $department['id'] : ''))) ?>"
              class="data-form">
            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= (int) $department['id'] ?>">
            <?php endif; ?>

            <label for="code">รหัสแผนก</label>
            <input type="text" id="code" name="code" maxlength="20" required
                   pattern="[A-Z0-9\-]+" style="text-transform:uppercase"
                   value="<?= e($department['code'] ?? '') ?>">
            <small>อนุญาตเฉพาะตัวอักษร A-Z, ตัวเลข 0-9 และเครื่องหมาย - เท่านั้น</small>

            <label for="name">ชื่อแผนก</label>
            <input type="text" id="name" name="name" maxlength="255" required
                   value="<?= e($department['name'] ?? '') ?>">

            <label for="description">คำอธิบาย</label>
            <textarea id="description" name="description" rows="4"><?= e($department['description'] ?? '') ?></textarea>

            <label for="status">สถานะ</label>
            <select id="status" name="status">
                <option value="Active" <?= ($department['status'] ?? 'Active') === 'Active' ? 'selected' : '' ?>>Active</option>
                <option value="Inactive" <?= ($department['status'] ?? '') === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>

            <label for="sort_order">ลำดับการแสดงผล</label>
            <input type="number" id="sort_order" name="sort_order" min="0" step="1"
                   value="<?= (int) ($department['sort_order'] ?? 0) ?>">

            <button type="submit" class="btn-primary"><?= $isEdit ? 'บันทึกการแก้ไข' : 'เพิ่มแผนก' ?></button>
        </form>
    </main>
</div>

<?php require APP_PATH . '/includes/admin_footer.php'; ?>
</body>
</html>
