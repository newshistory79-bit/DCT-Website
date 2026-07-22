<?php

declare(strict_types=1);

/** @var array|null $user */
/** @var string $csrfToken */
/** @var string|null $formError */

$isEdit = $user !== null;
$title  = $isEdit ? 'ແກ້ໄຂຜູ້ໃຊ້ງານ' : 'ເພີ່ມຜູ້ໃຊ້ງານ';
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
            $isEdit ? 'ແກ້ໄຂຂໍ້ມູນຜູ້ໃຊ້ "' . $user['username'] . '"' : 'ປ້ອນຂໍ້ມູນເພື່ອເພີ່ມຜູ້ໃຊ້ງານໃໝ່ເຂົ້າສູ່ລະບົບ',
            [['label' => 'ກັບຄືນລາຍການ', 'url' => baseUrl('admin/users/index.php'), 'class' => 'btn-secondary']]
        ); ?>

        <?php if ($formError !== null): ?>
            <p class="alert alert-error"><?= e($formError) ?></p>
        <?php endif; ?>

        <form method="post"
              action="<?= e(baseUrl('admin/users/form.php' . ($isEdit ? '?id=' . $user['id'] : ''))) ?>"
              class="data-form admin-form-sectioned">
            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
            <?php endif; ?>

            <?php renderAdminSectionCard('ຂໍ້ມູນທົ່ວໄປ', function () use ($user): void { ?>
                <label for="username">ຊື່ຜູ້ໃຊ້</label>
                <input type="text" id="username" name="username" maxlength="50" required
                       value="<?= e($user['username'] ?? '') ?>">

                <label for="full_name">ຊື່-ນາມສະກຸນ</label>
                <input type="text" id="full_name" name="full_name" maxlength="255" required
                       value="<?= e($user['full_name'] ?? '') ?>">

                <label for="email">ອີເມວ</label>
                <input type="email" id="email" name="email" maxlength="100"
                       value="<?= e($user['email'] ?? '') ?>">
            <?php }, 'ຂໍ້ມູນບັນຊີ ແລະ ຊ່ອງທາງຕິດຕໍ່'); ?>

            <?php renderAdminSectionCard('ລະຫັດຜ່ານ', function () use ($isEdit): void { ?>
                <label for="password">ລະຫັດຜ່ານ<?= $isEdit ? ' (ເວັ້ນວ່າງຫາກບໍ່ຕ້ອງການປ່ຽນ)' : '' ?></label>
                <input type="password" id="password" name="password" minlength="8" <?= $isEdit ? '' : 'required' ?>>
                <small>ຢ່າງໜ້ອຍ 8 ໂຕອັກສອນ</small>
            <?php }, $isEdit ? 'ປ່ຽນລະຫັດຜ່ານ (ບໍ່ບັງຄັບ)' : 'ກຳນົດລະຫັດຜ່ານເຂົ້າສູ່ລະບົບ'); ?>

            <?php renderAdminSectionCard('ສິດທິ ແລະ ສະຖານະ', function () use ($user): void { ?>
                <label for="role">ສິດທິ</label>
                <select id="role" name="role">
                    <option value="Admin" <?= ($user['role'] ?? '') === 'Admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="Editor" <?= ($user['role'] ?? '') === 'Editor' ? 'selected' : '' ?>>Editor</option>
                    <option value="Staff" <?= ($user['role'] ?? 'Staff') === 'Staff' ? 'selected' : '' ?>>Staff</option>
                </select>

                <label for="status">ສະຖານະ</label>
                <select id="status" name="status">
                    <option value="Active" <?= ($user['status'] ?? 'Active') === 'Active' ? 'selected' : '' ?>>Active</option>
                    <option value="Inactive" <?= ($user['status'] ?? '') === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            <?php }, 'ກຳນົດລະດັບສິດທິການນຳໃຊ້ ແລະ ສະຖານະບັນຊີ'); ?>

            <div class="admin-form-actions">
                <button type="submit" class="btn-primary"><?= $isEdit ? 'ບັນທຶກການແກ້ໄຂ' : 'ເພີ່ມຜູ້ໃຊ້ງານ' ?></button>
                <a href="<?= e(baseUrl('admin/users/index.php')) ?>" class="btn-ghost">ຍົກເລີກ</a>
            </div>
        </form>
    </main>
</div>

<?php require APP_PATH . '/includes/admin_footer.php'; ?>
</body>
</html>
