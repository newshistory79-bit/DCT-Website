<?php

declare(strict_types=1);

/** @var array $employees */
/** @var int $total */
/** @var int $totalPages */
/** @var int $currentPage */
/** @var int $perPage */
/** @var array $perPageOptions */
/** @var string $keyword */
/** @var string $gender */
/** @var string $sort */
/** @var string $direction */
/** @var string $csrfToken */
/** @var string|null $successMessage */
/** @var string|null $errorMessage */

$baseQuery = [
    'keyword'  => $keyword,
    'gender'   => $gender,
    'per_page' => $perPage,
];

$sortUrl = function (string $column) use ($sort, $direction, $baseQuery): string {
    $query              = $baseQuery;
    $query['sort']      = $column;
    $query['direction'] = ($sort === $column && strtolower($direction) === 'asc') ? 'desc' : 'asc';

    return baseUrl('admin/employees/index.php?' . http_build_query($query));
};

$sortIndicator = function (string $column) use ($sort, $direction): string {
    if ($sort !== $column) {
        return '';
    }

    return strtolower($direction) === 'asc' ? ' &#9650;' : ' &#9660;';
};

$columns = [
    'id'         => 'ID',
    'fname'      => 'ชื่อ',
    'lname'      => 'นามสกุล',
    'position'   => 'ตำแหน่ง',
    'birth_date' => 'วันเกิด',
    'created_at' => 'วันที่สร้าง',
];

$genderLabels = ['Male' => 'ชาย', 'Female' => 'หญิง', 'Other' => 'อื่นๆ'];

// Page Header — ดึง title/description จาก Single Source of Truth เดียวกับ Sidebar/Breadcrumb (Stage DS2)
$adminMenuItems  = require APP_PATH . '/config/admin_menu.php';
$currentMenuItem = findAdminMenuItemByUrl($adminMenuItems, 'admin/employees/index.php');
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($currentMenuItem['title']) ?> - <?= e(APP_NAME) ?></title>
<link rel="stylesheet" href="<?= e(baseUrl('assets/css/admin.css')) ?>">
<link rel="stylesheet" href="<?= e(baseUrl('assets/css/crud.css')) ?>">
</head>
<body>
<?php require APP_PATH . '/includes/admin_header.php'; ?>

<div class="admin-layout">
    <?php require APP_PATH . '/includes/admin_sidebar.php'; ?>

    <main class="admin-content">
        <?php renderAdminPageHeader(
            $currentMenuItem['title'],
            $currentMenuItem['description'],
            can('employees', 'create') ? [['label' => '+ เพิ่มพนักงาน', 'url' => baseUrl('admin/employees/form.php')]] : []
        ); ?>

        <?php if ($successMessage !== null): ?>
            <p class="alert alert-success"><?= e($successMessage) ?></p>
        <?php endif; ?>
        <?php if ($errorMessage !== null): ?>
            <p class="alert alert-error"><?= e($errorMessage) ?></p>
        <?php endif; ?>

        <form method="get" action="<?= e(baseUrl('admin/employees/index.php')) ?>" class="filter-bar">
            <div class="search-input-icon">
                <?= icon('search', 16) ?>
                <input type="text" name="keyword" value="<?= e($keyword) ?>" placeholder="ค้นหาชื่อ, นามสกุล, อีเมล, เบอร์โทร, ตำแหน่ง" aria-label="ค้นหาพนักงาน">
            </div>

            <select name="gender">
                <option value="">เพศทั้งหมด</option>
                <option value="Male" <?= $gender === 'Male' ? 'selected' : '' ?>>ชาย</option>
                <option value="Female" <?= $gender === 'Female' ? 'selected' : '' ?>>หญิง</option>
                <option value="Other" <?= $gender === 'Other' ? 'selected' : '' ?>>อื่นๆ</option>
            </select>

            <select name="per_page">
                <?php foreach ($perPageOptions as $option): ?>
                    <option value="<?= (int) $option ?>" <?= $perPage === $option ? 'selected' : '' ?>><?= (int) $option ?> รายการ/หน้า</option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="btn-secondary">ค้นหา</button>
        </form>

        <?php if (empty($employees)): ?>
            <?php renderAdminEmptyState(
                'ไม่พบข้อมูลพนักงาน ลองปรับคำค้นหาหรือตัวกรอง',
                'employee',
                can('employees', 'create') ? ['url' => baseUrl('admin/employees/form.php'), 'label' => '+ เพิ่มพนักงาน'] : null
            ); ?>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="data-table data-table-zebra">
                    <thead>
                        <tr>
                            <th>รูป</th>
                            <?php foreach ($columns as $col => $label): ?>
                                <th><a href="<?= e($sortUrl($col)) ?>"><?= e($label) . $sortIndicator($col) ?></a></th>
                            <?php endforeach; ?>
                            <th>เพศ</th>
                            <th>ติดต่อ</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($employees as $emp): ?>
                            <?php $empFullName = trim(($emp['Fname'] ?? '') . ' ' . ($emp['Lname'] ?? '')); ?>
                            <tr>
                                <td>
                                    <?php if (!empty($emp['image'])): ?>
                                        <img src="<?= e(uploadUrl('employees/' . $emp['image'])) ?>" alt="" class="avatar-thumb">
                                    <?php else: ?>
                                        <span class="avatar-placeholder">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= (int) $emp['ID'] ?></td>
                                <td><?= e($emp['Fname'] ?? '') ?></td>
                                <td><?= e($emp['Lname'] ?? '') ?></td>
                                <td>
                                    <?php if (empty($emp['position'])): ?>
                                        <span class="text-muted">-</span>
                                    <?php else: ?>
                                        <?php renderBadge($emp['position'], 'info'); ?>
                                    <?php endif; ?>
                                </td>
                                <td><?= e($emp['birth_date'] ?? '-') ?></td>
                                <td><?= e($emp['created_at']) ?></td>
                                <td><?= e($genderLabels[$emp['gender']] ?? $emp['gender']) ?></td>
                                <td>
                                    <?= e($emp['phone'] ?? '-') ?><br>
                                    <?= e($emp['email'] ?? '-') ?>
                                </td>
                                <td class="actions">
                                    <?php if (can('employees', 'edit')): ?>
                                        <a href="<?= e(baseUrl('admin/employees/form.php?id=' . $emp['ID'])) ?>" class="btn-icon" title="แก้ไข" aria-label="แก้ไขพนักงาน <?= e($empFullName) ?>"><?= icon('edit', 16) ?></a>
                                    <?php endif; ?>
                                    <?php if (can('employees', 'delete')): ?>
                                        <form method="post"
                                              action="<?= e(baseUrl('admin/employees/delete.php')) ?>"
                                              class="inline-form"
                                              data-confirm-modal="ยืนยันการลบพนักงาน &quot;<?= e($empFullName) ?>&quot; ใช่หรือไม่?">
                                            <input type="hidden" name="id" value="<?= (int) $emp['ID'] ?>">
                                            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                                            <button type="submit" class="btn-icon btn-danger" title="ลบ" aria-label="ลบพนักงาน <?= e($empFullName) ?>"><?= icon('trash', 16) ?></button>
                                        </form>
                                    <?php endif; ?>
                                    <?php if (!can('employees', 'edit') && !can('employees', 'delete')): ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php renderAdminPagination($currentPage, $totalPages, $total, function (int $p) use ($baseQuery, $sort, $direction): string {
                $pageQuery              = $baseQuery;
                $pageQuery['sort']      = $sort;
                $pageQuery['direction'] = $direction;
                $pageQuery['page']      = $p;

                return baseUrl('admin/employees/index.php?' . http_build_query($pageQuery));
            }); ?>
        <?php endif; ?>
    </main>
</div>

<?php require APP_PATH . '/includes/admin_footer.php'; ?>
</body>
</html>
