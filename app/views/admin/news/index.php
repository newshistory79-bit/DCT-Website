<?php

declare(strict_types=1);

/** @var array $newsItems */
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

    return baseUrl('admin/news/index.php?' . http_build_query($query));
};

$sortIndicator = function (string $column) use ($sort, $direction): string {
    if ($sort !== $column) {
        return '';
    }

    return strtolower($direction) === 'asc' ? ' &#9650;' : ' &#9660;';
};

$columns = [
    'id'            => 'ID',
    'title'         => 'ຫົວຂໍ້ຂ່າວ',
    'activity_date' => 'ວັນທີກິດຈະກຳ',
    'status'        => 'ສະຖານະ',
    'created_at'    => 'ວັນທີສ້າງ',
];

// Page Header — ดึง title/description จาก Single Source of Truth เดียวกับ Sidebar/Breadcrumb (Stage DS2/DS3 Pattern)
$adminMenuItems  = require APP_PATH . '/config/admin_menu.php';
$currentMenuItem = findAdminMenuItemByUrl($adminMenuItems, 'admin/news/index.php');
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
            can('news', 'create') ? [['label' => '+ ເພີ່ມຂ່າວສານ', 'url' => baseUrl('admin/news/form.php')]] : [],
            '<span class="stat-icon stat-icon-blue">' . icon('news', 22) . '</span>'
        ); ?>

        <?php if ($successMessage !== null): ?>
            <p class="alert alert-success"><?= e($successMessage) ?></p>
        <?php endif; ?>
        <?php if ($errorMessage !== null): ?>
            <p class="alert alert-error"><?= e($errorMessage) ?></p>
        <?php endif; ?>

        <form method="get" action="<?= e(baseUrl('admin/news/index.php')) ?>" class="filter-bar">
            <div class="search-input-icon">
                <?= icon('search', 16) ?>
                <input type="text" name="keyword" value="<?= e($keyword) ?>" placeholder="ຄົ້ນຫາຫົວຂໍ້ຂ່າວຫລືລາຍລະອຽດ" aria-label="ຄົ້ນຫາຂ່າວສານ">
            </div>

            <select name="status">
                <option value="">ສະຖານະທັງໝົດ</option>
                <option value="Published" <?= $status === 'Published' ? 'selected' : '' ?>>Published</option>
                <option value="Draft" <?= $status === 'Draft' ? 'selected' : '' ?>>Draft</option>
            </select>

            <select name="per_page">
                <?php foreach ($perPageOptions as $option): ?>
                    <option value="<?= (int) $option ?>" <?= $perPage === $option ? 'selected' : '' ?>><?= (int) $option ?> ລາຍການ/ໜ້າ</option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="btn-secondary">ຄົ້ນຫາ</button>
        </form>

        <?php if (empty($newsItems)): ?>
            <?php renderAdminEmptyState(
                'ບໍ່ພົບຂໍ້ມູນຂ່າວ ລອງປັບຄຳຄົ້ນຫາຫລືຕົວກອງ',
                'news',
                can('news', 'create') ? ['url' => baseUrl('admin/news/form.php'), 'label' => '+ ເພີ່ມຂ່າວສານ'] : null
            ); ?>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="data-table data-table-zebra">
                    <thead>
                        <tr>
                            <th>ຮູບ</th>
                            <?php foreach ($columns as $col => $label): ?>
                                <th><a href="<?= e($sortUrl($col)) ?>"><?= e($label) . $sortIndicator($col) ?></a></th>
                            <?php endforeach; ?>
                            <th>ລາຍລະອຽດ</th>
                            <th>ຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($newsItems as $item): ?>
                            <?php $itemTitle = mb_strimwidth((string) $item['title'], 0, 40, '...'); ?>
                            <tr>
                                <td>
                                    <?php if (!empty($item['image'])): ?>
                                        <img src="<?= e(uploadUrl('news/' . $item['image'])) ?>" alt="" class="avatar-thumb">
                                    <?php else: ?>
                                        <span class="avatar-placeholder">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= (int) $item['ID'] ?></td>
                                <td><?= e((string) $item['title']) ?></td>
                                <td><?= e($item['activity_date'] ?? '-') ?></td>
                                <td><?php renderBadge($item['status'], $item['status'] === 'Published' ? 'success' : 'muted'); ?></td>
                                <td><?= e($item['created_at']) ?></td>
                                <td class="truncate"><?= e(mb_strimwidth((string) $item['detail'], 0, 60, '...')) ?></td>
                                <td class="actions">
                                    <?php if (can('news', 'edit')): ?>
                                        <a href="<?= e(baseUrl('admin/news/form.php?id=' . $item['ID'])) ?>" class="btn-icon" title="ແກ້ໄຂ" aria-label="ແກ້ໄຂຂ່າວ <?= e($itemTitle) ?>"><?= icon('edit', 16) ?></a>
                                    <?php endif; ?>
                                    <?php if (can('news', 'delete')): ?>
                                        <form method="post"
                                              action="<?= e(baseUrl('admin/news/delete.php')) ?>"
                                              class="inline-form"
                                              data-confirm-modal="ຢືນຢັນການລຶບຂ່າວ &quot;<?= e($itemTitle) ?>&quot; ແທ້ບໍ?">
                                            <input type="hidden" name="id" value="<?= (int) $item['ID'] ?>">
                                            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                                            <button type="submit" class="btn-icon btn-danger" title="ລຶບ" aria-label="ລຶບຂ່າວ <?= e($itemTitle) ?>"><?= icon('trash', 16) ?></button>
                                        </form>
                                    <?php endif; ?>
                                    <?php if (!can('news', 'edit') && !can('news', 'delete')): ?>
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

                return baseUrl('admin/news/index.php?' . http_build_query($pageQuery));
            }); ?>
        <?php endif; ?>
    </main>
</div>

<?php require APP_PATH . '/includes/admin_footer.php'; ?>
</body>
</html>
