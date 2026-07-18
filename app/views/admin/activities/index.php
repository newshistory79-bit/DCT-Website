<?php

declare(strict_types=1);

/** @var array $activities */
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

    return baseUrl('admin/activities/index.php?' . http_build_query($query));
};

$sortIndicator = function (string $column) use ($sort, $direction): string {
    if ($sort !== $column) {
        return '';
    }

    return strtolower($direction) === 'asc' ? ' &#9650;' : ' &#9660;';
};

$columns = [
    'id'            => 'ID',
    'title'         => 'หัวข้อกิจกรรม',
    'activity_date' => 'วันที่จัดกิจกรรม',
    'status'        => 'สถานะ',
    'created_at'    => 'วันที่สร้าง',
];

// Page Header — ดึง title/description จาก Single Source of Truth เดียวกับ Sidebar/Breadcrumb (Stage DS2/DS3 Pattern)
$adminMenuItems  = require APP_PATH . '/config/admin_menu.php';
$currentMenuItem = findAdminMenuItemByUrl($adminMenuItems, 'admin/activities/index.php');
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
            can('activities', 'create') ? [['label' => '+ เพิ่มกิจกรรม', 'url' => baseUrl('admin/activities/form.php')]] : []
        ); ?>

        <?php if ($successMessage !== null): ?>
            <p class="alert alert-success"><?= e($successMessage) ?></p>
        <?php endif; ?>
        <?php if ($errorMessage !== null): ?>
            <p class="alert alert-error"><?= e($errorMessage) ?></p>
        <?php endif; ?>

        <form method="get" action="<?= e(baseUrl('admin/activities/index.php')) ?>" class="filter-bar">
            <div class="search-input-icon">
                <?= icon('search', 16) ?>
                <input type="text" name="keyword" value="<?= e($keyword) ?>" placeholder="ค้นหาหัวข้อ รายละเอียด หรือสถานที่จัดกิจกรรม" aria-label="ค้นหากิจกรรม">
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

        <?php if (empty($activities)): ?>
            <?php renderAdminEmptyState(
                'ไม่พบข้อมูลกิจกรรม ลองปรับคำค้นหาหรือตัวกรอง',
                'activity',
                can('activities', 'create') ? ['url' => baseUrl('admin/activities/form.php'), 'label' => '+ เพิ่มกิจกรรม'] : null
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
                            <th>สถานที่</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($activities as $activity): ?>
                            <?php $activityTitle = mb_strimwidth((string) $activity['title'], 0, 40, '...'); ?>
                            <tr>
                                <td>
                                    <?php if (!empty($activity['image'])): ?>
                                        <img src="<?= e(uploadUrl('activities/' . $activity['image'])) ?>" alt="" class="avatar-thumb">
                                    <?php else: ?>
                                        <span class="avatar-placeholder">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= (int) $activity['id'] ?></td>
                                <td><?= e((string) $activity['title']) ?></td>
                                <td><?= e(date('d/m/Y', strtotime((string) $activity['activity_date']))) ?></td>
                                <td><?php renderBadge($activity['status'], $activity['status'] === 'Published' ? 'success' : 'muted'); ?></td>
                                <td><?= e($activity['created_at']) ?></td>
                                <td class="truncate"><?= e(mb_strimwidth((string) ($activity['location'] ?? '-'), 0, 40, '...')) ?></td>
                                <td class="actions">
                                    <?php if (can('activities', 'edit')): ?>
                                        <a href="<?= e(baseUrl('admin/activities/form.php?id=' . $activity['id'])) ?>" class="btn-icon" title="แก้ไข" aria-label="แก้ไขกิจกรรม <?= e($activityTitle) ?>"><?= icon('edit', 16) ?></a>
                                    <?php endif; ?>
                                    <?php if (can('activities', 'delete')): ?>
                                        <form method="post"
                                              action="<?= e(baseUrl('admin/activities/delete.php')) ?>"
                                              class="inline-form"
                                              data-confirm-modal="ยืนยันการลบกิจกรรม &quot;<?= e($activityTitle) ?>&quot; ใช่หรือไม่?">
                                            <input type="hidden" name="id" value="<?= (int) $activity['id'] ?>">
                                            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                                            <button type="submit" class="btn-icon btn-danger" title="ลบ" aria-label="ลบกิจกรรม <?= e($activityTitle) ?>"><?= icon('trash', 16) ?></button>
                                        </form>
                                    <?php endif; ?>
                                    <?php if (!can('activities', 'edit') && !can('activities', 'delete')): ?>
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

                return baseUrl('admin/activities/index.php?' . http_build_query($pageQuery));
            }); ?>
        <?php endif; ?>
    </main>
</div>

<?php require APP_PATH . '/includes/admin_footer.php'; ?>
</body>
</html>
