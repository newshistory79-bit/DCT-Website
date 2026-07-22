<?php

declare(strict_types=1);

/** @var array|null $news */
/** @var string $csrfToken */
/** @var string|null $formError */

$isEdit = $news !== null;
$title  = $isEdit ? 'ແກ້ໄຂຂ່າວ' : 'ເພີ່ມຂ່າວສານ';
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
            $isEdit ? 'ແກ້ໄຂຂໍ້ມູນຂ່າວ "' . $news['title'] . '"' : 'ປ້ອນຂໍ້ມູນເພື່ອເພີ່ມຂ່າວໃໝ່ເຂົ້າສູ່ລະບົບ',
            [['label' => 'ກັບຄືນລາຍການ', 'url' => baseUrl('admin/news/index.php'), 'class' => 'btn-secondary']]
        ); ?>

        <?php if ($formError !== null): ?>
            <p class="alert alert-error"><?= e($formError) ?></p>
        <?php endif; ?>

        <form method="post"
              action="<?= e(baseUrl('admin/news/form.php' . ($isEdit ? '?id=' . $news['ID'] : ''))) ?>"
              class="data-form admin-form-sectioned"
              enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= (int) $news['ID'] ?>">
            <?php endif; ?>

            <?php renderAdminSectionCard('ຂໍ້ມູນທົ່ວໄປ', function () use ($news): void { ?>
                <label for="title">ຫົວຂໍ້ຂ່າວ</label>
                <input type="text" id="title" name="title" required
                       value="<?= e((string) ($news['title'] ?? '')) ?>">

                <label for="activity_date">ວັນທີກິດຈະກຳ</label>
                <input type="date" id="activity_date" name="activity_date"
                       value="<?= e($news['activity_date'] ?? '') ?>">
            <?php }, 'ຫົວຂໍ້ ແລະ ວັນທີຂອງຂ່າວສານ'); ?>

            <?php renderAdminSectionCard('ເນື້ອຫາ', function () use ($news): void { ?>
                <label for="detail">ລາຍລະອຽດ</label>
                <textarea id="detail" name="detail" rows="6" required><?= e((string) ($news['detail'] ?? '')) ?></textarea>
            <?php }, 'ເນື້ອຫາສະບັບເຕັມຂອງຂ່າວສານ'); ?>

            <?php renderAdminSectionCard('ຮູບພາບ', function () use ($news, $isEdit): void { ?>
                <?php if ($isEdit && !empty($news['image'])): ?>
                    <div class="current-image">
                        <img src="<?= e(uploadUrl('news/' . $news['image'])) ?>" alt="">
                    </div>
                <?php endif; ?>
                <label for="image">ຮູບພາບປະກອບຂ່າວ</label>
                <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp">
                <small>ອະນຸຍາດສະເພາະໄຟລ໌ jpg, jpeg, png, webp ຂະໜາດບໍ່ເກີນ 2 MB</small>
            <?php }, 'ຮູບພາບປະກອບຂ່າວ (ບໍ່ບັງຄັບ)'); ?>

            <?php renderAdminSectionCard('ສະຖານະ', function () use ($news): void { ?>
                <label for="status">ສະຖານະການເຜີຍແຜ່</label>
                <select id="status" name="status">
                    <option value="Published" <?= ($news['status'] ?? 'Published') === 'Published' ? 'selected' : '' ?>>Published</option>
                    <option value="Draft" <?= ($news['status'] ?? '') === 'Draft' ? 'selected' : '' ?>>Draft</option>
                </select>
            <?php }, 'ກຳນົດວ່າຂ່າວນີ້ເຜີຍແຜ່ເທິງເວັບໄຊທ໌ແລ້ວຫລືບໍ່'); ?>

            <?php renderAdminSectionCard('ການດຳເນີນການ', function () use ($isEdit): void { ?>
                <div class="admin-form-actions">
                    <button type="submit" class="btn-primary"><?= $isEdit ? 'ບັນທຶກການແກ້ໄຂ' : 'ເພີ່ມຂ່າວສານ' ?></button>
                    <a href="<?= e(baseUrl('admin/news/index.php')) ?>" class="btn-ghost">ຍົກເລີກ</a>
                </div>
            <?php }, 'ກວດສອບຂໍ້ມູນໃຫ້ຖືກຕ້ອງກ່ອນບັນທຶກ'); ?>
        </form>
    </main>
</div>

<?php require APP_PATH . '/includes/admin_footer.php'; ?>
</body>
</html>
