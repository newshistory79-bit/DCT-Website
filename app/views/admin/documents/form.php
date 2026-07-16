<?php

declare(strict_types=1);

/** @var array|null $document */
/** @var string $csrfToken */
/** @var string|null $formError */

$isEdit = $document !== null;
$title  = $isEdit ? 'แก้ไขเอกสาร' : 'เพิ่มเอกสาร';
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
            <a href="<?= e(baseUrl('admin/documents/index.php')) ?>" class="btn-secondary">กลับไปรายการ</a>
        </div>

        <?php if ($formError !== null): ?>
            <p class="alert alert-error"><?= e($formError) ?></p>
        <?php endif; ?>

        <form method="post"
              action="<?= e(baseUrl('admin/documents/form.php' . ($isEdit ? '?id=' . $document['id'] : ''))) ?>"
              class="data-form"
              enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= (int) $document['id'] ?>">
            <?php endif; ?>

            <label for="title">ชื่อเอกสาร</label>
            <input type="text" id="title" name="title" maxlength="255" required
                   value="<?= e((string) ($document['title'] ?? '')) ?>">

            <label for="description">รายละเอียด</label>
            <textarea id="description" name="description" rows="4"><?= e((string) ($document['description'] ?? '')) ?></textarea>

            <label for="status">สถานะ</label>
            <select id="status" name="status">
                <option value="Draft" <?= ($document['status'] ?? 'Draft') === 'Draft' ? 'selected' : '' ?>>Draft</option>
                <option value="Published" <?= ($document['status'] ?? '') === 'Published' ? 'selected' : '' ?>>Published</option>
            </select>

            <label for="file">ไฟล์เอกสาร</label>
            <?php if ($isEdit && !empty($document['original_file_name'])): ?>
                <div class="current-image">
                    ไฟล์ปัจจุบัน: <a href="<?= e(uploadUrl('documents/' . $document['file_name'])) ?>" target="_blank" rel="noopener"><?= e($document['original_file_name']) ?></a>
                </div>
            <?php endif; ?>
            <input type="file" id="file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx" <?= $isEdit ? '' : 'required' ?>>
            <small>
                อนุญาตเฉพาะไฟล์ pdf, doc, docx, xls, xlsx, ppt, pptx ขนาดไม่เกิน 10 MB
                <?php if ($isEdit): ?>(ไม่จำเป็นต้องเลือกไฟล์ใหม่หากต้องการคงไฟล์เดิม)<?php endif; ?>
            </small>

            <button type="submit" class="btn-primary"><?= $isEdit ? 'บันทึกการแก้ไข' : 'เพิ่มเอกสาร' ?></button>
        </form>
    </main>
</div>

<?php require APP_PATH . '/includes/admin_footer.php'; ?>
</body>
</html>
