<?php

declare(strict_types=1);

/** @var array|null $department */
/** @var string $csrfToken */
/** @var string|null $formError */

$isEdit = $department !== null;
$title  = $isEdit ? 'ແກ້ໄຂພະແນກ' : 'ເພີ່ມພະແນກ';
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
            $isEdit ? 'ແກ້ໄຂຂໍ້ມູນພະແນກ "' . $department['name'] . '"' : 'ປ້ອນຂໍ້ມູນເພື່ອເພີ່ມພະແນກໃໝ່ເຂົ້າສູ່ລະບົບ',
            [['label' => 'ກັບຄືນລາຍການ', 'url' => baseUrl('admin/departments/index.php'), 'class' => 'btn-secondary']]
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

            <?php renderAdminSectionCard('ຂໍ້ມູນທົ່ວໄປ', function () use ($department): void { ?>
                <label for="name">ຊື່ພະແນກ</label>
                <input type="text" id="name" name="name" maxlength="255" required
                       value="<?= e($department['name'] ?? '') ?>">

                <label for="description">ຄຳອະທິບາຍ</label>
                <textarea id="description" name="description" rows="4"><?= e($department['description'] ?? '') ?></textarea>

                <label for="sort_order">ລຳດັບການສະແດງຜົນ</label>
                <input type="number" id="sort_order" name="sort_order" min="0" step="1"
                       value="<?= (int) ($department['sort_order'] ?? 0) ?>">
            <?php }, 'ຂໍ້ມູນພື້ນຖານຂອງພະແນກ'); ?>

            <?php renderAdminSectionCard('ສະຖານະ', function () use ($department): void { ?>
                <label for="status">ສະຖານະການນຳໃຊ້</label>
                <select id="status" name="status">
                    <option value="Active" <?= ($department['status'] ?? 'Active') === 'Active' ? 'selected' : '' ?>>Active</option>
                    <option value="Inactive" <?= ($department['status'] ?? '') === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            <?php }, 'ກຳນົດວ່າພະແນກນີ້ເປີດໃຊ້ງານຢູ່ຫລືບໍ່'); ?>

            <?php renderAdminSectionCard('ການດຳເນີນການ', function () use ($isEdit): void { ?>
                <div class="admin-form-actions">
                    <button type="submit" class="btn-primary"><?= $isEdit ? 'ບັນທຶກການແກ້ໄຂ' : 'ເພີ່ມພະແນກ' ?></button>
                    <a href="<?= e(baseUrl('admin/departments/index.php')) ?>" class="btn-ghost">ຍົກເລີກ</a>
                </div>
            <?php }, 'ກວດສອບຂໍ້ມູນໃຫ້ຖືກຕ້ອງກ່ອນບັນທຶກ'); ?>
        </form>
    </main>
</div>

<?php require APP_PATH . '/includes/admin_footer.php'; ?>
</body>
</html>
