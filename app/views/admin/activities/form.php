<?php

declare(strict_types=1);

/** @var array|null $activity */
/** @var string $csrfToken */
/** @var string|null $formError */

$isEdit = $activity !== null;
$title  = $isEdit ? 'แก้ไขกิจกรรม' : 'เพิ่มกิจกรรม';
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
            $isEdit ? 'แก้ไขข้อมูลกิจกรรม "' . $activity['title'] . '"' : 'กรอกข้อมูลเพื่อเพิ่มกิจกรรมใหม่เข้าสู่ระบบ',
            [['label' => 'กลับไปรายการ', 'url' => baseUrl('admin/activities/index.php'), 'class' => 'btn-secondary']]
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

            <?php renderAdminSectionCard('ข้อมูลทั่วไป', function () use ($activity): void { ?>
                <label for="title">หัวข้อกิจกรรม</label>
                <input type="text" id="title" name="title" maxlength="255" required
                       value="<?= e((string) ($activity['title'] ?? '')) ?>">

                <label for="activity_date">วันที่จัดกิจกรรม</label>
                <input type="date" id="activity_date" name="activity_date" required
                       value="<?= e((string) ($activity['activity_date'] ?? '')) ?>">

                <label for="location">สถานที่จัดกิจกรรม</label>
                <input type="text" id="location" name="location" maxlength="255"
                       value="<?= e((string) ($activity['location'] ?? '')) ?>">
            <?php }, 'หัวข้อ วันที่ และสถานที่จัดกิจกรรม'); ?>

            <?php renderAdminSectionCard('เนื้อหา', function () use ($activity): void { ?>
                <label for="description">รายละเอียด</label>
                <textarea id="description" name="description" rows="4"><?= e((string) ($activity['description'] ?? '')) ?></textarea>
            <?php }, 'รายละเอียดฉบับเต็มของกิจกรรม'); ?>

            <?php renderAdminSectionCard('รูปภาพ', function () use ($activity, $isEdit): void { ?>
                <?php if ($isEdit && !empty($activity['image'])): ?>
                    <div class="current-image">
                        <img src="<?= e(uploadUrl('activities/' . $activity['image'])) ?>" alt="">
                    </div>
                <?php endif; ?>
                <label for="image">รูปภาพกิจกรรม</label>
                <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp">
                <small>
                    อนุญาตเฉพาะไฟล์ jpg, jpeg, png, webp ขนาดไม่เกิน 2 MB (ไม่บังคับแนบ)
                    <?php if ($isEdit): ?>— หากไม่เลือกไฟล์ใหม่จะคงรูปเดิมไว้<?php endif; ?>
                </small>
            <?php }, 'ภาพประกอบกิจกรรม (ไม่บังคับ)'); ?>

            <?php renderAdminSectionCard('สถานะ', function () use ($activity): void { ?>
                <label for="status">สถานะการเผยแพร่</label>
                <select id="status" name="status">
                    <option value="Draft" <?= ($activity['status'] ?? 'Draft') === 'Draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="Published" <?= ($activity['status'] ?? '') === 'Published' ? 'selected' : '' ?>>Published</option>
                </select>
            <?php }, 'กำหนดว่ากิจกรรมนี้เผยแพร่บนเว็บไซต์แล้วหรือไม่'); ?>

            <?php renderAdminSectionCard('การดำเนินการ', function () use ($isEdit): void { ?>
                <div class="admin-form-actions">
                    <button type="submit" class="btn-primary"><?= $isEdit ? 'บันทึกการแก้ไข' : 'เพิ่มกิจกรรม' ?></button>
                    <a href="<?= e(baseUrl('admin/activities/index.php')) ?>" class="btn-ghost">ยกเลิก</a>
                </div>
            <?php }, 'ตรวจสอบข้อมูลให้ถูกต้องก่อนบันทึก'); ?>
        </form>
    </main>
</div>

<?php require APP_PATH . '/includes/admin_footer.php'; ?>
</body>
</html>
