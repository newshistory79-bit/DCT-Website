<?php

declare(strict_types=1);

/** @var array|null $gallery */
/** @var string $csrfToken */
/** @var string|null $formError */

$isEdit = $gallery !== null;
$title  = $isEdit ? 'แก้ไขภาพ' : 'เพิ่มภาพ';
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
            $isEdit ? 'แก้ไขข้อมูลภาพ "' . $gallery['title'] . '"' : 'กรอกข้อมูลเพื่อเพิ่มภาพใหม่เข้าสู่คลังภาพ',
            [['label' => 'กลับไปรายการ', 'url' => baseUrl('admin/gallery/index.php'), 'class' => 'btn-secondary']]
        ); ?>

        <?php if ($formError !== null): ?>
            <p class="alert alert-error"><?= e($formError) ?></p>
        <?php endif; ?>

        <form method="post"
              action="<?= e(baseUrl('admin/gallery/form.php' . ($isEdit ? '?id=' . $gallery['id'] : ''))) ?>"
              class="data-form admin-form-sectioned"
              enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= (int) $gallery['id'] ?>">
            <?php endif; ?>

            <?php renderAdminSectionCard('ข้อมูลทั่วไป', function () use ($gallery): void { ?>
                <label for="title">ชื่อภาพ</label>
                <input type="text" id="title" name="title" maxlength="255" required
                       value="<?= e((string) ($gallery['title'] ?? '')) ?>">

                <label for="description">คำอธิบาย</label>
                <textarea id="description" name="description" rows="4"><?= e((string) ($gallery['description'] ?? '')) ?></textarea>
            <?php }, 'ชื่อและคำอธิบายของภาพ'); ?>

            <?php renderAdminSectionCard('สถานะ', function () use ($gallery): void { ?>
                <label for="status">สถานะการเผยแพร่</label>
                <select id="status" name="status">
                    <option value="Draft" <?= ($gallery['status'] ?? 'Draft') === 'Draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="Published" <?= ($gallery['status'] ?? '') === 'Published' ? 'selected' : '' ?>>Published</option>
                </select>
            <?php }, 'กำหนดว่าภาพนี้เผยแพร่บนเว็บไซต์แล้วหรือไม่'); ?>

            <?php renderAdminSectionCard('รูปภาพ', function () use ($gallery, $isEdit): void { ?>
                <?php if ($isEdit && !empty($gallery['image'])): ?>
                    <div class="current-image">
                        <img src="<?= e(uploadUrl('gallery/' . $gallery['image'])) ?>" alt="">
                    </div>
                <?php endif; ?>
                <label for="image">ไฟล์ภาพ</label>
                <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp" <?= $isEdit ? '' : 'required' ?>>
                <small>
                    อนุญาตเฉพาะไฟล์ jpg, jpeg, png, webp ขนาดไม่เกิน 2 MB
                    <?php if ($isEdit): ?>(ไม่จำเป็นต้องเลือกไฟล์ใหม่หากต้องการคงรูปเดิม)<?php endif; ?>
                </small>
            <?php }, $isEdit ? 'เปลี่ยนรูปภาพ (ไม่บังคับ)' : 'แนบไฟล์ภาพ (บังคับ)'); ?>

            <div class="admin-form-actions">
                <button type="submit" class="btn-primary"><?= $isEdit ? 'บันทึกการแก้ไข' : 'เพิ่มภาพ' ?></button>
                <a href="<?= e(baseUrl('admin/gallery/index.php')) ?>" class="btn-ghost">ยกเลิก</a>
            </div>
        </form>
    </main>
</div>

<?php require APP_PATH . '/includes/admin_footer.php'; ?>
</body>
</html>
