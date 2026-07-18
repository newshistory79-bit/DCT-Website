<?php

declare(strict_types=1);

/** @var array $galleryItems */
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

    return baseUrl('admin/gallery/index.php?' . http_build_query($query));
};

$sortIndicator = function (string $column) use ($sort, $direction): string {
    if ($sort !== $column) {
        return '';
    }

    return strtolower($direction) === 'asc' ? ' &#9650;' : ' &#9660;';
};

$columns = [
    'id'         => 'ID',
    'title'      => 'ชื่อภาพ',
    'status'     => 'สถานะ',
    'created_at' => 'วันที่สร้าง',
];

// Page Header — ดึง title/description จาก Single Source of Truth เดียวกับ Sidebar/Breadcrumb (Stage DS2-DS4 Pattern)
$adminMenuItems  = require APP_PATH . '/config/admin_menu.php';
$currentMenuItem = findAdminMenuItemByUrl($adminMenuItems, 'admin/gallery/index.php');
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
            can('gallery', 'create') ? [['label' => '+ เพิ่มภาพ', 'url' => baseUrl('admin/gallery/form.php')]] : []
        ); ?>

        <?php if ($successMessage !== null): ?>
            <p class="alert alert-success"><?= e($successMessage) ?></p>
        <?php endif; ?>
        <?php if ($errorMessage !== null): ?>
            <p class="alert alert-error"><?= e($errorMessage) ?></p>
        <?php endif; ?>

        <form method="get" action="<?= e(baseUrl('admin/gallery/index.php')) ?>" class="filter-bar">
            <div class="search-input-icon">
                <?= icon('search', 16) ?>
                <input type="text" name="keyword" value="<?= e($keyword) ?>" placeholder="ค้นหาชื่อภาพหรือคำอธิบาย" aria-label="ค้นหาภาพ">
            </div>

            <select name="status">
                <option value="">สถานะทั้งหมด</option>
                <option value="Published" <?= $status === 'Published' ? 'selected' : '' ?>>Published</option>
                <option value="Draft" <?= $status === 'Draft' ? 'selected' : '' ?>>Draft</option>
            </select>

            <select name="per_page">
                <?php foreach ($perPageOptions as $option): ?>
                    <option value="<?= (int) $option ?>" <?= $perPage === $option ? 'selected' : '' ?>><?= (int) $option ?> รายการ/หน้า</option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="btn-secondary">ค้นหา</button>
        </form>

        <?php if (empty($galleryItems)): ?>
            <?php renderAdminEmptyState(
                'ไม่พบข้อมูลภาพ ลองปรับคำค้นหาหรือตัวกรอง',
                'image',
                can('gallery', 'create') ? ['url' => baseUrl('admin/gallery/form.php'), 'label' => '+ เพิ่มภาพ'] : null
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
                            <th>คำอธิบาย</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($galleryItems as $galleryItem): ?>
                            <?php $galleryTitle = mb_strimwidth((string) $galleryItem['title'], 0, 40, '...'); ?>
                            <tr>
                                <td>
                                    <?php if (!empty($galleryItem['image'])): ?>
                                        <img src="<?= e(uploadUrl('gallery/' . $galleryItem['image'])) ?>" alt="" class="avatar-thumb">
                                    <?php else: ?>
                                        <span class="avatar-placeholder">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= (int) $galleryItem['id'] ?></td>
                                <td><?= e((string) $galleryItem['title']) ?></td>
                                <td><?php renderBadge($galleryItem['status'], $galleryItem['status'] === 'Published' ? 'success' : 'muted'); ?></td>
                                <td><?= e($galleryItem['created_at']) ?></td>
                                <td class="truncate"><?= e(mb_strimwidth((string) ($galleryItem['description'] ?? '-'), 0, 60, '...')) ?></td>
                                <td class="actions">
                                    <?php if (can('gallery', 'edit')): ?>
                                        <a href="<?= e(baseUrl('admin/gallery/form.php?id=' . $galleryItem['id'])) ?>" class="btn-icon" title="แก้ไข" aria-label="แก้ไขภาพ <?= e($galleryTitle) ?>"><?= icon('edit', 16) ?></a>
                                    <?php endif; ?>
                                    <?php if (can('gallery', 'delete')): ?>
                                        <form method="post"
                                              action="<?= e(baseUrl('admin/gallery/delete.php')) ?>"
                                              class="inline-form"
                                              data-confirm-modal="ยืนยันการลบภาพ &quot;<?= e($galleryTitle) ?>&quot; ใช่หรือไม่?">
                                            <input type="hidden" name="id" value="<?= (int) $galleryItem['id'] ?>">
                                            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                                            <button type="submit" class="btn-icon btn-danger" title="ลบ" aria-label="ลบภาพ <?= e($galleryTitle) ?>"><?= icon('trash', 16) ?></button>
                                        </form>
                                    <?php endif; ?>
                                    <?php if (!can('gallery', 'edit') && !can('gallery', 'delete')): ?>
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

                return baseUrl('admin/gallery/index.php?' . http_build_query($pageQuery));
            }); ?>
        <?php endif; ?>
    </main>
</div>

<?php require APP_PATH . '/includes/admin_footer.php'; ?>
</body>
</html>
