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

// Action Badge Variant (Presentation เท่านั้น ไม่กระทบ Logic การบันทึก Log จริง)
$actionVariant = static function (string $action): string {
    return match ($action) {
        'create', 'login' => 'success',
        'update'           => 'info',
        'delete', 'login_failed' => 'danger',
        default            => 'muted',
    };
};

// Page Header — ดึง title/description จาก Single Source of Truth เดียวกับ Sidebar/Breadcrumb (Stage DS2-DS4 Pattern)
$adminMenuItems  = require APP_PATH . '/config/admin_menu.php';
$currentMenuItem = findAdminMenuItemByUrl($adminMenuItems, 'admin/activity-log/index.php');
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
        <?php renderAdminPageHeader($currentMenuItem['title'], $currentMenuItem['description']); ?>

        <form method="get" action="<?= e(baseUrl('admin/activity-log/index.php')) ?>" class="filter-bar">
            <div class="search-input-icon">
                <?= icon('search', 16) ?>
                <input type="text" name="keyword" value="<?= e($keyword) ?>" placeholder="ค้นหาผู้ใช้งานหรือรายละเอียด" aria-label="ค้นหาประวัติการใช้งาน">
            </div>

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

            <input type="date" name="date_from" value="<?= e($dateFrom) ?>" title="จากวันที่" aria-label="จากวันที่">
            <input type="date" name="date_to" value="<?= e($dateTo) ?>" title="ถึงวันที่" aria-label="ถึงวันที่">

            <select name="per_page">
                <?php foreach ($perPageOptions as $option): ?>
                    <option value="<?= (int) $option ?>" <?= $perPage === $option ? 'selected' : '' ?>><?= (int) $option ?> รายการ/หน้า</option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="btn-secondary">ค้นหา</button>
        </form>

        <?php if (empty($logs)): ?>
            <?php renderAdminEmptyState('ไม่พบประวัติการใช้งานตามเงื่อนไขที่เลือก', 'log'); ?>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="data-table data-table-zebra">
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
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><?= e($log['created_at']) ?></td>
                                <td><?= e($log['username']) ?></td>
                                <td><?= e($log['module']) ?></td>
                                <td><?php renderBadge($log['action'], $actionVariant($log['action'])); ?></td>
                                <td><?= e($log['role']) ?></td>
                                <td class="truncate"><?= e($log['description']) ?></td>
                                <td><?= e($log['ip_address'] ?? '-') ?></td>
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

                return baseUrl('admin/activity-log/index.php?' . http_build_query($pageQuery));
            }); ?>
        <?php endif; ?>
    </main>
</div>

<?php require APP_PATH . '/includes/admin_footer.php'; ?>
</body>
</html>
