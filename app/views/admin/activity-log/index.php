<?php

declare(strict_types=1);

/** @var array $logs */
/** @var int $total */
/** @var int $totalPages */
/** @var int $currentPage */
/** @var int $perPage */
/** @var array $perPageOptions */
/** @var string $keyword */
/** @var string $module */
/** @var string $action */
/** @var string $dateFrom */
/** @var string $dateTo */
/** @var string $sort */
/** @var string $direction */
/** @var array $moduleOptions */
/** @var array $actionOptions */

$baseQuery = [
    'keyword'   => $keyword,
    'module'    => $module,
    'action'    => $action,
    'date_from' => $dateFrom,
    'date_to'   => $dateTo,
    'per_page'  => $perPage,
];

$sortUrl = function (string $column) use ($sort, $direction, $baseQuery): string {
    $query              = $baseQuery;
    $query['sort']      = $column;
    $query['direction'] = ($sort === $column && strtolower($direction) === 'asc') ? 'desc' : 'asc';

    return baseUrl('admin/activity-log/index.php?' . http_build_query($query));
};

$sortIndicator = function (string $column) use ($sort, $direction): string {
    if ($sort !== $column) {
        return '';
    }

    return strtolower($direction) === 'asc' ? ' &#9650;' : ' &#9660;';
};

$columns = [
    'created_at' => 'เวลา',
    'username'   => 'ผู้ใช้',
    'module'     => 'โมดูล',
    'action'     => 'การกระทำ',
];
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ประวัติการใช้งานระบบ - <?= e(APP_NAME) ?></title>
<link rel="stylesheet" href="<?= e(baseUrl('assets/css/admin.css')) ?>">
<link rel="stylesheet" href="<?= e(baseUrl('assets/css/crud.css')) ?>">
</head>
<body>
<?php require APP_PATH . '/includes/admin_header.php'; ?>

<div class="admin-layout">
    <?php require APP_PATH . '/includes/admin_sidebar.php'; ?>

    <main class="admin-content">
        <div class="page-heading">
            <h1>ประวัติการใช้งานระบบ (Activity Log)</h1>
        </div>

        <form method="get" action="<?= e(baseUrl('admin/activity-log/index.php')) ?>" class="filter-bar">
            <input type="text" name="keyword" value="<?= e($keyword) ?>" placeholder="ค้นหาผู้ใช้งานหรือรายละเอียด">

            <select name="module">
                <option value="">โมดูลทั้งหมด</option>
                <?php foreach ($moduleOptions as $option): ?>
                    <option value="<?= e($option) ?>" <?= $module === $option ? 'selected' : '' ?>><?= e($option) ?></option>
                <?php endforeach; ?>
            </select>

            <select name="action">
                <option value="">การกระทำทั้งหมด</option>
                <?php foreach ($actionOptions as $option): ?>
                    <option value="<?= e($option) ?>" <?= $action === $option ? 'selected' : '' ?>><?= e($option) ?></option>
                <?php endforeach; ?>
            </select>

            <input type="date" name="date_from" value="<?= e($dateFrom) ?>" title="จากวันที่">
            <input type="date" name="date_to" value="<?= e($dateTo) ?>" title="ถึงวันที่">

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
                        <?php foreach ($columns as $col => $label): ?>
                            <th><a href="<?= e($sortUrl($col)) ?>"><?= e($label) . $sortIndicator($col) ?></a></th>
                        <?php endforeach; ?>
                        <th>สิทธิ์</th>
                        <th>รายละเอียด</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="7" class="empty-row">ไม่พบประวัติการใช้งาน</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><?= e($log['created_at']) ?></td>
                                <td><?= e($log['username']) ?></td>
                                <td><?= e($log['module']) ?></td>
                                <td><?= e($log['action']) ?></td>
                                <td><?= e($log['role']) ?></td>
                                <td><?= e($log['description']) ?></td>
                                <td><?= e($log['ip_address'] ?? '-') ?></td>
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
                    <a href="<?= e(baseUrl('admin/activity-log/index.php?' . http_build_query($pageQuery))) ?>"
                       class="<?= $p === $currentPage ? 'active' : '' ?>"><?= $p ?></a>
                <?php endfor; ?>
            </div>
        </div>
    </main>
</div>

<?php require APP_PATH . '/includes/admin_footer.php'; ?>
</body>
</html>
