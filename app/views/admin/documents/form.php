<?php

declare(strict_types=1);

/** @var array|null $document */
/** @var string $csrfToken */
/** @var string|null $formError */

$isEdit = $document !== null;
$title  = $isEdit ? 'ແກ້ໄຂເອກະສານ' : 'ເພີ່ມເອກະສານ';
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
            $isEdit ? 'ແກ້ໄຂຂໍ້ມູນເອກະສານ "' . $document['title'] . '"' : 'ປ້ອນຂໍ້ມູນເພື່ອເພີ່ມເອກະສານໃໝ່ເຂົ້າສູ່ລະບົບ',
            [['label' => 'ກັບຄືນລາຍການ', 'url' => baseUrl('admin/documents/index.php'), 'class' => 'btn-secondary']]
        ); ?>

        <?php if ($formError !== null): ?>
            <p class="alert alert-error"><?= e($formError) ?></p>
        <?php endif; ?>

        <form method="post"
              action="<?= e(baseUrl('admin/documents/form.php' . ($isEdit ? '?id=' . $document['id'] : ''))) ?>"
              class="data-form admin-form-sectioned"
              enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= (int) $document['id'] ?>">
            <?php endif; ?>

            <?php renderAdminSectionCard('ຂໍ້ມູນທົ່ວໄປ', function () use ($document): void { ?>
                <label for="title">ຊື່ເອກະສານ</label>
                <input type="text" id="title" name="title" maxlength="255" required
                       value="<?= e((string) ($document['title'] ?? '')) ?>">

                <label for="description">ລາຍລະອຽດ</label>
                <textarea id="description" name="description" rows="4"><?= e((string) ($document['description'] ?? '')) ?></textarea>

                <label for="category">ປະເພດເອກະສານ</label>
                <select id="category" name="category">
                    <?php foreach (\App\Models\DocumentModel::CATEGORIES as $value => $label): ?>
                        <option value="<?= e($value) ?>" <?= ($document['category'] ?? 'law') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
            <?php }, 'ຊື່ ແລະ ລາຍລະອຽດຂອງເອກະສານ'); ?>

            <?php renderAdminSectionCard('ສະຖານະ', function () use ($document): void { ?>
                <label for="status">ສະຖານະການເຜີຍແຜ່</label>
                <select id="status" name="status">
                    <option value="Draft" <?= ($document['status'] ?? 'Draft') === 'Draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="Published" <?= ($document['status'] ?? '') === 'Published' ? 'selected' : '' ?>>Published</option>
                </select>
            <?php }, 'ກຳນົດວ່າເອກະສານນີ້ເຜີຍແຜ່ເທິງເວັບໄຊທ໌ແລ້ວຫລືບໍ່'); ?>

            <?php renderAdminSectionCard('ໄຟລ໌ເອກະສານ', function () use ($document, $isEdit): void { ?>
                <?php if ($isEdit && !empty($document['original_file_name'])): ?>
                    <div class="current-image">
                        ໄຟລ໌ປັດຈຸບັນ: <a href="<?= e(uploadUrl('documents/' . $document['file_name'])) ?>" target="_blank" rel="noopener"><?= e($document['original_file_name']) ?></a>
                    </div>
                <?php endif; ?>
                <label for="file">ໄຟລ໌ເອກະສານ</label>
                <input type="file" id="file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx" <?= $isEdit ? '' : 'required' ?>>
                <small>
                    ອະນຸຍາດສະເພາະໄຟລ໌ pdf, doc, docx, xls, xlsx, ppt, pptx ຂະໜາດບໍ່ເກີນ 10 MB
                    <?php if ($isEdit): ?>(ບໍ່ຈຳເປັນຕ້ອງເລືອກໄຟລ໌ໃໝ່ຫາກຕ້ອງການຄົງໄຟລ໌ເດີມ)<?php endif; ?>
                </small>
            <?php }, $isEdit ? 'ປ່ຽນໄຟລ໌ເອກະສານ (ບໍ່ບັງຄັບ)' : 'ແນບໄຟລ໌ເອກະສານ (ບັງຄັບ)'); ?>

            <div class="admin-form-actions">
                <button type="submit" class="btn-primary"><?= $isEdit ? 'ບັນທຶກການແກ້ໄຂ' : 'ເພີ່ມເອກະສານ' ?></button>
                <a href="<?= e(baseUrl('admin/documents/index.php')) ?>" class="btn-ghost">ຍົກເລີກ</a>
            </div>
        </form>
    </main>
</div>

<?php require APP_PATH . '/includes/admin_footer.php'; ?>
</body>
</html>
