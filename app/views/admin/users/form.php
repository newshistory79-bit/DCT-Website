<?php

declare(strict_types=1);

/** @var array|null $user */
/** @var string $csrfToken */
/** @var string|null $formError */

$isEdit = $user !== null;
$title  = $isEdit ? 'แก้ไขผู้ใช้งาน' : 'เพิ่มผู้ใช้งาน';
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
            <a href="<?= e(baseUrl('admin/users/index.php')) ?>" class="btn-secondary">กลับไปรายการ</a>
        </div>

        <?php if ($formError !== null): ?>
            <p class="alert alert-error"><?= e($formError) ?></p>
        <?php endif; ?>

        <form method="post"
              action="<?= e(baseUrl('admin/users/form.php' . ($isEdit ? '?id=' . $user['id'] : ''))) ?>"
              class="data-form">
            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
            <?php endif; ?>

            <label for="username">ชื่อผู้ใช้</label>
            <input type="text" id="username" name="username" maxlength="50" required
                   value="<?= e($user['username'] ?? '') ?>">

            <label for="full_name">ชื่อ-นามสกุล</label>
            <input type="text" id="full_name" name="full_name" maxlength="255" required
                   value="<?= e($user['full_name'] ?? '') ?>">

            <label for="email">อีเมล</label>
            <input type="email" id="email" name="email" maxlength="100"
                   value="<?= e($user['email'] ?? '') ?>">

            <label for="password">รหัสผ่าน<?= $isEdit ? ' (เว้นว่างหากไม่ต้องการเปลี่ยน)' : '' ?></label>
            <input type="password" id="password" name="password" minlength="8" <?= $isEdit ? '' : 'required' ?>>
            <small>อย่างน้อย 8 ตัวอักษร</small>

            <label for="role">สิทธิ์</label>
            <select id="role" name="role">
                <option value="Admin" <?= ($user['role'] ?? '') === 'Admin' ? 'selected' : '' ?>>Admin</option>
                <option value="Editor" <?= ($user['role'] ?? '') === 'Editor' ? 'selected' : '' ?>>Editor</option>
                <option value="Staff" <?= ($user['role'] ?? 'Staff') === 'Staff' ? 'selected' : '' ?>>Staff</option>
            </select>

            <label for="status">สถานะ</label>
            <select id="status" name="status">
                <option value="Active" <?= ($user['status'] ?? 'Active') === 'Active' ? 'selected' : '' ?>>Active</option>
                <option value="Inactive" <?= ($user['status'] ?? '') === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>

            <button type="submit" class="btn-primary"><?= $isEdit ? 'บันทึกการแก้ไข' : 'เพิ่มผู้ใช้งาน' ?></button>
        </form>
    </main>
</div>

<?php require APP_PATH . '/includes/admin_footer.php'; ?>
</body>
</html>
