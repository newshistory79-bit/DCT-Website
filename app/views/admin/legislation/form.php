<?php

declare(strict_types=1);

/** @var array|null $legislation */
/** @var string $csrfToken */
/** @var string|null $formError */

$isEdit = $legislation !== null;
$title  = $isEdit ? 'แก้ไขกฎหมาย/ระเบียบ' : 'เพิ่มกฎหมาย/ระเบียบ';
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
            $isEdit ? 'แก้ไขข้อมูล "' . $legislation['title'] . '"' : 'กรอกข้อมูลเพื่อเพิ่มกฎหมาย/ระเบียบใหม่เข้าสู่ระบบ',
            [['label' => 'กลับไปรายการ', 'url' => baseUrl('admin/legislation/index.php'), 'class' => 'btn-secondary']]
        ); ?>

        <?php if ($formError !== null): ?>
            <p class="alert alert-error"><?= e($formError) ?></p>
        <?php endif; ?>

        <form method="post"
              action="<?= e(baseUrl('admin/legislation/form.php' . ($isEdit ? '?id=' . $legislation['ID'] : ''))) ?>"
              class="data-form admin-form-sectioned">
            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= (int) $legislation['ID'] ?>">
            <?php endif; ?>

            <?php renderAdminSectionCard('ข้อมูลทั่วไป', function () use ($legislation): void { ?>
                <label for="title">หัวข้อ</label>
                <input type="text" id="title" name="title" maxlength="255" required
                       value="<?= e((string) ($legislation['title'] ?? '')) ?>">

                <label for="document_number">เลขที่ประกาศ/ระเบียบ</label>
                <input type="text" id="document_number" name="document_number" maxlength="50"
                       value="<?= e($legislation['document_number'] ?? '') ?>">

                <label for="detail">รายละเอียด</label>
                <textarea id="detail" name="detail" rows="6" required><?= e((string) ($legislation['detail'] ?? '')) ?></textarea>

                <label for="effective_date">วันที่มีผลบังคับใช้</label>
                <input type="date" id="effective_date" name="effective_date"
                       value="<?= e($legislation['effective_date'] ?? '') ?>">
            <?php }, 'หัวข้อและเนื้อหาของกฎหมาย/ระเบียบ'); ?>

            <?php renderAdminSectionCard('สถานะ', function () use ($legislation): void { ?>
                <label for="status">สถานะการเผยแพร่</label>
                <select id="status" name="status">
                    <option value="Draft" <?= ($legislation['status'] ?? 'Draft') === 'Draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="Published" <?= ($legislation['status'] ?? '') === 'Published' ? 'selected' : '' ?>>Published</option>
                </select>
            <?php }, 'กำหนดว่ารายการนี้เผยแพร่บนเว็บไซต์แล้วหรือไม่'); ?>

            <div class="admin-form-actions">
                <button type="submit" class="btn-primary"><?= $isEdit ? 'บันทึกการแก้ไข' : 'เพิ่มกฎหมาย/ระเบียบ' ?></button>
                <a href="<?= e(baseUrl('admin/legislation/index.php')) ?>" class="btn-ghost">ยกเลิก</a>
            </div>
        </form>
    </main>
</div>

<?php require APP_PATH . '/includes/admin_footer.php'; ?>
</body>
</html>
