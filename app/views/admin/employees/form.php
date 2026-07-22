<?php

declare(strict_types=1);

/** @var array|null $employee */
/** @var string $csrfToken */
/** @var string|null $formError */

$isEdit = $employee !== null;
$title  = $isEdit ? 'ແກ້ໄຂຂໍ້ມູນພະນັກງານ' : 'ເພີ່ມພະນັກງານ';
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
            $isEdit ? 'ແກ້ໄຂຂໍ້ມູນຂອງ "' . trim(($employee['Fname'] ?? '') . ' ' . ($employee['Lname'] ?? '')) . '"' : 'ປ້ອນຂໍ້ມູນເພື່ອເພີ່ມພະນັກງານໃໝ່ເຂົ້າສູ່ລະບົບ',
            [['label' => 'ກັບຄືນລາຍການ', 'url' => baseUrl('admin/employees/index.php'), 'class' => 'btn-secondary']]
        ); ?>

        <?php if ($formError !== null): ?>
            <p class="alert alert-error"><?= e($formError) ?></p>
        <?php endif; ?>

        <form method="post"
              action="<?= e(baseUrl('admin/employees/form.php' . ($isEdit ? '?id=' . $employee['ID'] : ''))) ?>"
              class="data-form admin-form-sectioned"
              enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= (int) $employee['ID'] ?>">
            <?php endif; ?>

            <?php renderAdminSectionCard('ຂໍ້ມູນທົ່ວໄປ', function () use ($employee): void { ?>
                <label for="fname">ຊື່</label>
                <input type="text" id="fname" name="fname" maxlength="255" required
                       value="<?= e($employee['Fname'] ?? '') ?>">

                <label for="lname">ນາມສະກຸນ</label>
                <input type="text" id="lname" name="lname" maxlength="255" required
                       value="<?= e($employee['Lname'] ?? '') ?>">

                <label for="birth_date">ວັນເກີດ</label>
                <input type="date" id="birth_date" name="birth_date"
                       value="<?= e($employee['birth_date'] ?? '') ?>">

                <label for="gender">ເພດ</label>
                <select id="gender" name="gender">
                    <option value="Male" <?= ($employee['gender'] ?? 'Male') === 'Male' ? 'selected' : '' ?>>ຊາຍ</option>
                    <option value="Female" <?= ($employee['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>ຍິງ</option>
                    <option value="Other" <?= ($employee['gender'] ?? '') === 'Other' ? 'selected' : '' ?>>ອື່ນໆ</option>
                </select>

                <label for="phone">ເບີໂທລະສັບ</label>
                <input type="text" id="phone" name="phone" maxlength="20"
                       value="<?= e($employee['phone'] ?? '') ?>">

                <label for="email">ອີເມວ</label>
                <input type="email" id="email" name="email" maxlength="100"
                       value="<?= e($employee['email'] ?? '') ?>">

                <label for="position">ຕຳແໜ່ງ</label>
                <input type="text" id="position" name="position" maxlength="100"
                       value="<?= e($employee['position'] ?? '') ?>">

                <label for="address">ທີ່ຢູ່</label>
                <textarea id="address" name="address" rows="3"><?= e($employee['address'] ?? '') ?></textarea>
            <?php }, 'ຂໍ້ມູນສ່ວນຕົວ ແລະ ຊ່ອງທາງຕິດຕໍ່ຂອງພະນັກງານ'); ?>

            <?php renderAdminSectionCard('ຮູບພາບ', function () use ($employee, $isEdit): void { ?>
                <?php if ($isEdit && !empty($employee['image'])): ?>
                    <div class="current-image">
                        <img src="<?= e(uploadUrl('employees/' . $employee['image'])) ?>" alt="">
                    </div>
                <?php endif; ?>
                <label for="image">ຮູບພາບພະນັກງານ</label>
                <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp,.jfif">
                <small>ອະນຸຍາດສະເພາະໄຟລ໌ jpg, jpeg, png, webp, jfif ຂະໜາດບໍ່ເກີນ 2 MB</small>
            <?php }, 'ຮູບໂປຣໄຟລ໌ຂອງພະນັກງານ (ບໍ່ບັງຄັບ)'); ?>

            <?php renderAdminSectionCard('ການດຳເນີນການ', function () use ($isEdit): void { ?>
                <div class="admin-form-actions">
                    <button type="submit" class="btn-primary"><?= $isEdit ? 'ບັນທຶກການແກ້ໄຂ' : 'ເພີ່ມພະນັກງານ' ?></button>
                    <a href="<?= e(baseUrl('admin/employees/index.php')) ?>" class="btn-ghost">ຍົກເລີກ</a>
                </div>
            <?php }, 'ກວດສອບຂໍ້ມູນໃຫ້ຖືກຕ້ອງກ່ອນບັນທຶກ'); ?>
        </form>
    </main>
</div>

<?php require APP_PATH . '/includes/admin_footer.php'; ?>
</body>
</html>
