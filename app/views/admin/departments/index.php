<?php

declare(strict_types=1);

/** @var array $departments */
/** @var int $total */
/** @var int $totalPages */
/** @var int $currentPage */
/** @var int $perPage */
/** @var array $perPageOptions */
/** @var string $keyword */
/** @var string $status */
/** @var string $sort */
/** @var string $direction */
/** @var string $csrfToken */
/** @var string|null $successMessage */
/** @var string|null $errorMessage */

$baseQuery = [
    'keyword'  => $keyword,
    'status'   => $status,
    'per_page' => $perPage,
];

$sortUrl = function (string $column) use ($sort, $direction, $baseQuery): string {
    $query              = $baseQuery;
    $query['sort']      = $column;
    $query['direction'] = ($sort === $column && strtolower($direction) === 'asc') ? 'desc' : 'asc';

    return baseUrl('admin/departments/index.php?' . http_build_query($query));
};

$sortIndicator = function (string $column) use ($sort, $direction): string {
    if ($sort !== $column) {
        return '';
    }

    return strtolower($direction) === 'asc' ? ' &#9650;' : ' &#9660;';
};

$columns = [
    'id'         => 'ID',
    'code'       => 'รหัสแผนก',
    'name'       => 'ชื่อแผนก',
    'status'     => 'สถานะ',
    'created_at' => 'วันที่สร้าง',
];

// Page Header — ดึง title/description จาก Single Source of Truth เดียวกับ Sidebar/Breadcrumb (Stage DS2)
$adminMenuItems  = require APP_PATH . '/config/admin_menu.php';
$currentMenuItem = findAdminMenuItemByUrl($adminMenuItems, 'admin/departments/index.php');
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
            can('departments', 'create') ? [['label' => '+ เพิ่มแผนก', 'url' => baseUrl('admin/departments/form.php')]] : []
        ); ?>

        <?php if ($successMessage !== null): ?>
            <p class="alert alert-success"><?= e($successMessage) ?></p>
        <?php endif; ?>
        <?php if ($errorMessage !== null): ?>
            <p class="alert alert-error"><?= e($errorMessage) ?></p>
        <?php endif; ?>

        <form method="get" action="<?= e(baseUrl('admin/departments/index.php')) ?>" class="filter-bar">
            <div class="search-input-icon">
                <?= icon('search', 16) ?>
                <input type="text" name="keyword" value="<?= e($keyword) ?>" placeholder="ค้นหารหัสหรือชื่อแผนก" aria-label="ค้นหารหัสหรือชื่อแผนก">
            </div>

            <select name="status">
                <option value="">สถานะทั้งหมด</option>
                <option value="Active" <?= $status === 'Active' ? 'selected' : '' ?>>Active</option>
                <option value="Inactive" <?= $status === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>

            <select name="per_page">
                <?php foreach ($perPageOptions as $option): ?>
                    <option value="<?= (int) $option ?>" <?= $perPage === $option ? 'selected' : '' ?>><?= (int) $option ?> รายการ/หน้า</option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="btn-secondary">ค้นหา</button>
        </form>

        <?php if (empty($departments)): ?>
            <?php renderAdminEmptyState(
                'ไม่พบข้อมูลแผนก ลองปรับคำค้นหาหรือตัวกรอง',
                'department',
                can('departments', 'create') ? ['url' => baseUrl('admin/departments/form.php'), 'label' => '+ เพิ่มแผนก'] : null
            ); ?>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="data-table data-table-zebra">
                    <thead>
                        <tr>
                            <?php foreach ($columns as $col => $label): ?>
                                <th><a href="<?= e($sortUrl($col)) ?>"><?= e($label) . $sortIndicator($col) ?></a></th>
                            <?php endforeach; ?>
                            <th>คำอธิบาย</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($departments as $dept): ?>
                            <tr>
                                <td><?= (int) $dept['id'] ?></td>
                                <td><?= e($dept['code']) ?></td>
                                <td><?= e($dept['name']) ?></td>
                                <td><?php renderBadge($dept['status'], $dept['status'] === 'Active' ? 'success' : 'muted'); ?></td>
                                <td><?= e($dept['created_at']) ?></td>
                                <td class="truncate"><?= e($dept['description'] ?? '-') ?></td>
                                <td class="actions">
                                    <?php if (can('departments', 'edit')): ?>
                                        <a href="<?= e(baseUrl('admin/departments/form.php?id=' . $dept['id'])) ?>" class="btn-icon" title="แก้ไข" aria-label="แก้ไขแผนก <?= e($dept['name']) ?>"><?= icon('edit', 16) ?></a>
                                    <?php endif; ?>
                                    <?php if (can('departments', 'delete')): ?>
                                        <form method="post"
                                              action="<?= e(baseUrl('admin/departments/delete.php')) ?>"
                                              class="inline-form"
                                              data-confirm-modal="ยืนยันการลบแผนก &quot;<?= e($dept['name']) ?>&quot; ใช่หรือไม่?">
                                            <input type="hidden" name="id" value="<?= (int) $dept['id'] ?>">
                                            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                                            <button type="submit" class="btn-icon btn-danger" title="ลบ" aria-label="ลบแผนก <?= e($dept['name']) ?>"><?= icon('trash', 16) ?></button>
                                        </form>
                                    <?php endif; ?>
                                    <?php if (!can('departments', 'edit') && !can('departments', 'delete')): ?>
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

                return baseUrl('admin/departments/index.php?' . http_build_query($pageQuery));
            }); ?>
        <?php endif; ?>
    </main>
</div>

<?php require APP_PATH . '/includes/admin_footer.php'; ?>
</body>
</html>
