<?php

declare(strict_types=1);

/** @var array|null $activity */
/** @var string $csrfToken */
/** @var string|null $formError */

$isEdit = $activity !== null;
$title  = $isEdit ? 'ແກ້ໄຂກິດຈະກຳ' : 'ເພີ່ມກິດຈະກຳ';
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
            $isEdit ? 'ແກ້ໄຂຂໍ້ມູນກິດຈະກຳ "' . $activity['title'] . '"' : 'ປ້ອນຂໍ້ມູນເພື່ອເພີ່ມກິດຈະກຳໃໝ່ເຂົ້າສູ່ລະບົບ',
            [['label' => 'ກັບຄືນລາຍການ', 'url' => baseUrl('admin/activities/index.php'), 'class' => 'btn-secondary']]
        ); ?>

        <?php if ($formError !== null): ?>
            <p class="alert alert-error"><?= e($formError) ?></p>
        <?php endif; ?>

        <form method="post"
              action="<?= e(baseUrl('admin/activities/form.php' . ($isEdit ? '?id=' . $activity['id'] : ''))) ?>"
              class="data-form admin-form-sectioned"
              enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= (int) $activity['id'] ?>">
            <?php endif; ?>

            <?php renderAdminSectionCard('ຂໍ້ມູນທົ່ວໄປ', function () use ($activity): void { ?>
                <label for="title">ຫົວຂໍ້ກິດຈະກຳ</label>
                <input type="text" id="title" name="title" maxlength="255" required
                       value="<?= e((string) ($activity['title'] ?? '')) ?>">

                <label for="activity_date">ວັນທີຈັດກິດຈະກຳ</label>
                <input type="date" id="activity_date" name="activity_date" required
                       value="<?= e((string) ($activity['activity_date'] ?? '')) ?>">

                <label for="location">ສະຖານທີ່ຈັດກິດຈະກຳ</label>
                <input type="text" id="location" name="location" maxlength="255"
                       value="<?= e((string) ($activity['location'] ?? '')) ?>">
            <?php }, 'ຫົວຂໍ້ ວັນທີ ແລະ ສະຖານທີ່ຈັດກິດຈະກຳ'); ?>

            <?php renderAdminSectionCard('ເນື້ອຫາ', function () use ($activity): void { ?>
                <label for="description">ລາຍລະອຽດ</label>
                <textarea id="description" name="description" rows="4"><?= e((string) ($activity['description'] ?? '')) ?></textarea>
            <?php }, 'ລາຍລະອຽດສະບັບເຕັມຂອງກິດຈະກຳ'); ?>

            <?php renderAdminSectionCard('ຮູບພາບ', function () use ($activity, $isEdit): void { ?>
                <?php if ($isEdit && !empty($activity['image'])): ?>
                    <div class="current-image">
                        <img src="<?= e(uploadUrl('activities/' . $activity['image'])) ?>" alt="">
                    </div>
                <?php endif; ?>
                <label for="image">ຮູບພາບກິດຈະກຳ</label>
                <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp">
                <small>
                    ອະນຸຍາດສະເພາະໄຟລ໌ jpg, jpeg, png, webp ຂະໜາດບໍ່ເກີນ 2 MB (ບໍ່ບັງຄັບແນບ)
                    <?php if ($isEdit): ?>— ຫາກບໍ່ເລືອກໄຟລ໌ໃໝ່ຈະຄົງຮູບເດີມໄວ້<?php endif; ?>
                </small>
            <?php }, 'ຮູບພາບປະກອບກິດຈະກຳ (ບໍ່ບັງຄັບ)'); ?>

            <?php renderAdminSectionCard('ສະຖານະ', function () use ($activity): void { ?>
                <label for="status">ສະຖານະການເຜີຍແຜ່</label>
                <select id="status" name="status">
                    <option value="Draft" <?= ($activity['status'] ?? 'Draft') === 'Draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="Published" <?= ($activity['status'] ?? '') === 'Published' ? 'selected' : '' ?>>Published</option>
                </select>
            <?php }, 'ກຳນົດວ່າກິດຈະກຳນີ້ເຜີຍແຜ່ເທິງເວັບໄຊທ໌ແລ້ວຫລືບໍ່'); ?>

            <?php renderAdminSectionCard('ການດຳເນີນການ', function () use ($isEdit): void { ?>
                <div class="admin-form-actions">
                    <button type="submit" class="btn-primary"><?= $isEdit ? 'ບັນທຶກການແກ້ໄຂ' : 'ເພີ່ມກິດຈະກຳ' ?></button>
                    <a href="<?= e(baseUrl('admin/activities/index.php')) ?>" class="btn-ghost">ຍົກເລີກ</a>
                </div>
            <?php }, 'ກວດສອບຂໍ້ມູນໃຫ້ຖືກຕ້ອງກ່ອນບັນທຶກ'); ?>
        </form>
    </main>
</div>

<?php require APP_PATH . '/includes/admin_footer.php'; ?>
</body>
</html>
