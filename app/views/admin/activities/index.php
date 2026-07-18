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
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>จัดการกิจกรรม - <?= e(APP_NAME) ?></title>
<link rel="stylesheet" href="<?= e(baseUrl('assets/css/admin.css')) ?>">
<link rel="stylesheet" href="<?= e(baseUrl('assets/css/crud.css')) ?>">
</head>
<body>
<?php require APP_PATH . '/includes/admin_header.php'; ?>

<div class="admin-layout">
    <?php require APP_PATH . '/includes/admin_sidebar.php'; ?>

    <main class="admin-content">
        <div class="page-heading">
            <h1>จัดการกิจกรรม</h1>
            <?php if (can('activities', 'create')): ?>
                <a href="<?= e(baseUrl('admin/activities/form.php')) ?>" class="btn-primary">+ เพิ่มกิจกรรม</a>
            <?php endif; ?>
        </div>

        <?php if ($successMessage !== null): ?>
            <p class="alert alert-success"><?= e($successMessage) ?></p>
        <?php endif; ?>
        <?php if ($errorMessage !== null): ?>
            <p class="alert alert-error"><?= e($errorMessage) ?></p>
        <?php endif; ?>

        <form method="get" action="<?= e(baseUrl('admin/activities/index.php')) ?>" class="filter-bar">
            <input type="text" name="keyword" value="<?= e($keyword) ?>" placeholder="ค้นหาหัวข้อ รายละเอียด หรือสถานที่จัดกิจกรรม">

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

        <div class="table-wrapper">
            <table class="data-table">
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
                    <?php if (empty($activities)): ?>
                        <tr>
                            <td colspan="8" class="empty-row">ไม่พบข้อมูลกิจกรรม</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($activities as $activity): ?>
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
                                <td>
                                    <span class="badge badge-<?= $activity['status'] === 'Published' ? 'success' : 'muted' ?>">
                                        <?= e($activity['status']) ?>
                                    </span>
                                </td>
                                <td><?= e($activity['created_at']) ?></td>
                                <td class="truncate"><?= e(mb_strimwidth((string) ($activity['location'] ?? '-'), 0, 40, '...')) ?></td>
                                <td class="actions">
                                    <?php if (can('activities', 'edit')): ?>
                                        <a href="<?= e(baseUrl('admin/activities/form.php?id=' . $activity['id'])) ?>" class="btn-link">แก้ไข</a>
                                    <?php endif; ?>
                                    <?php if (can('activities', 'delete')): ?>
                                        <form method="post"
                                              action="<?= e(baseUrl('admin/activities/delete.php')) ?>"
                                              class="inline-form"
                                              data-confirm="ยืนยันการลบกิจกรรม &quot;<?= e(mb_strimwidth((string) $activity['title'], 0, 40, '...')) ?>&quot; ใช่หรือไม่?">
                                            <input type="hidden" name="id" value="<?= (int) $activity['id'] ?>">
                                            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                                            <button type="submit" class="btn-link btn-danger">ลบ</button>
                                        </form>
                                    <?php endif; ?>
                                    <?php if (!can('activities', 'edit') && !can('activities', 'delete')): ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="pagination">
            <span>ทั้งหมด <?= (int) $total ?> รายการ</span>
            <div class="pagination-links">
                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <?php
                    $pageQuery              = $baseQuery;
                    $pageQuery['sort']      = $sort;
                    $pageQuery['direction'] = $direction;
                    $pageQuery['page']      = $p;
                    ?>
                    <a href="<?= e(baseUrl('admin/activities/index.php?' . http_build_query($pageQuery))) ?>"
                       class="<?= $p === $currentPage ? 'active' : '' ?>"><?= $p ?></a>
                <?php endfor; ?>
            </div>
        </div>
    </main>
</div>

<?php require APP_PATH . '/includes/admin_footer.php'; ?>
</body>
</html>
