<?php

declare(strict_types=1);

/** @var array|null $news */
/** @var string $csrfToken */
/** @var string|null $formError */

$isEdit = $news !== null;
$title  = $isEdit ? 'แก้ไขข่าว' : 'เพิ่มข่าว';
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
            <a href="<?= e(baseUrl('admin/news/index.php')) ?>" class="btn-secondary">กลับไปรายการ</a>
        </div>

        <?php if ($formError !== null): ?>
            <p class="alert alert-error"><?= e($formError) ?></p>
        <?php endif; ?>

        <form method="post"
              action="<?= e(baseUrl('admin/news/form.php' . ($isEdit ? '?id=' . $news['ID'] : ''))) ?>"
              class="data-form"
              enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= (int) $news['ID'] ?>">
            <?php endif; ?>

            <label for="title">หัวข้อข่าว</label>
            <input type="text" id="title" name="title" required
                   value="<?= e((string) ($news['title'] ?? '')) ?>">

            <label for="detail">รายละเอียด</label>
            <textarea id="detail" name="detail" rows="6" required><?= e((string) ($news['detail'] ?? '')) ?></textarea>

            <label for="activity_date">วันที่กิจกรรม</label>
            <input type="date" id="activity_date" name="activity_date"
                   value="<?= e($news['activity_date'] ?? '') ?>">

            <label for="status">สถานะ</label>
            <select id="status" name="status">
                <option value="Published" <?= ($news['status'] ?? 'Published') === 'Published' ? 'selected' : '' ?>>Published</option>
                <option value="Draft" <?= ($news['status'] ?? '') === 'Draft' ? 'selected' : '' ?>>Draft</option>
            </select>

            <label for="image">รูปภาพ</label>
            <?php if ($isEdit && !empty($news['image'])): ?>
                <div class="current-image">
                    <img src="<?= e(uploadUrl('news/' . $news['image'])) ?>" alt="">
                </div>
            <?php endif; ?>
            <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp">
            <small>อนุญาตเฉพาะไฟล์ jpg, jpeg, png, webp ขนาดไม่เกิน 2 MB</small>

            <button type="submit" class="btn-primary"><?= $isEdit ? 'บันทึกการแก้ไข' : 'เพิ่มข่าว' ?></button>
        </form>
    </main>
</div>

<?php require APP_PATH . '/includes/admin_footer.php'; ?>
</body>
</html>
