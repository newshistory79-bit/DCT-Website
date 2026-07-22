<?php

declare(strict_types=1);

/** @var array $documents */
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

    return baseUrl('admin/documents/index.php?' . http_build_query($query));
};

$sortIndicator = function (string $column) use ($sort, $direction): string {
    if ($sort !== $column) {
        return '';
    }

    return strtolower($direction) === 'asc' ? ' &#9650;' : ' &#9660;';
};

$columns = [
    'id'         => 'ID',
    'title'      => 'ຊື່ເອກະສານ',
    'status'     => 'ສະຖານະ',
    'created_at' => 'ວັນທີສ້າງ',
];

// Page Header — ดึง title/description จาก Single Source of Truth เดียวกับ Sidebar/Breadcrumb (Stage DS2-DS4 Pattern)
$adminMenuItems  = require APP_PATH . '/config/admin_menu.php';
$currentMenuItem = findAdminMenuItemByUrl($adminMenuItems, 'admin/documents/index.php');
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
            can('documents', 'create') ? [['label' => '+ ເພີ່ມເອກະສານ', 'url' => baseUrl('admin/documents/form.php')]] : []
        ); ?>

        <?php if ($successMessage !== null): ?>
            <p class="alert alert-success"><?= e($successMessage) ?></p>
        <?php endif; ?>
        <?php if ($errorMessage !== null): ?>
            <p class="alert alert-error"><?= e($errorMessage) ?></p>
        <?php endif; ?>

        <form method="get" action="<?= e(baseUrl('admin/documents/index.php')) ?>" class="filter-bar">
            <div class="search-input-icon">
                <?= icon('search', 16) ?>
                <input type="text" name="keyword" value="<?= e($keyword) ?>" placeholder="ຄົ້ນຫາຊື່ເອກະສານຫລືລາຍລະອຽດ" aria-label="ຄົ້ນຫາເອກະສານ">
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

        <?php if (empty($documents)): ?>
            <?php renderAdminEmptyState(
                'ບໍ່ພົບຂໍ້ມູນເອກະສານ ລອງປັບຄຳຄົ້ນຫາຫລືຕົວກອງ',
                'download',
                can('documents', 'create') ? ['url' => baseUrl('admin/documents/form.php'), 'label' => '+ ເພີ່ມເອກະສານ'] : null
            ); ?>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="data-table data-table-zebra">
                    <thead>
                        <tr>
                            <?php foreach ($columns as $col => $label): ?>
                                <th><a href="<?= e($sortUrl($col)) ?>"><?= e($label) . $sortIndicator($col) ?></a></th>
                            <?php endforeach; ?>
                            <th>ໄຟລ໌</th>
                            <th>ລາຍລະອຽດ</th>
                            <th>ຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($documents as $doc): ?>
                            <?php $docTitle = mb_strimwidth((string) $doc['title'], 0, 40, '...'); ?>
                            <tr>
                                <td><?= (int) $doc['id'] ?></td>
                                <td><?= e((string) $doc['title']) ?></td>
                                <td><?php renderBadge($doc['status'], $doc['status'] === 'Published' ? 'success' : 'muted'); ?></td>
                                <td><?= e($doc['created_at']) ?></td>
                                <td>
                                    <a href="<?= e(uploadUrl('documents/' . $doc['file_name'])) ?>" target="_blank" rel="noopener">
                                        <?= e(strtoupper((string) $doc['file_extension'])) ?>
                                    </a>
                                    <br>
                                    <small><?= e(formatFileSize($doc['file_size'] !== null ? (int) $doc['file_size'] : null)) ?></small>
                                </td>
                                <td class="truncate"><?= e(mb_strimwidth((string) ($doc['description'] ?? '-'), 0, 60, '...')) ?></td>
                                <td class="actions">
                                    <?php if (can('documents', 'edit')): ?>
                                        <a href="<?= e(baseUrl('admin/documents/form.php?id=' . $doc['id'])) ?>" class="btn-icon" title="ແກ້ໄຂ" aria-label="ແກ້ໄຂເອກະສານ <?= e($docTitle) ?>"><?= icon('edit', 16) ?></a>
                                    <?php endif; ?>
                                    <?php if (can('documents', 'delete')): ?>
                                        <form method="post"
                                              action="<?= e(baseUrl('admin/documents/delete.php')) ?>"
                                              class="inline-form"
                                              data-confirm-modal="ຢືນຢັນການລຶບເອກະສານ &quot;<?= e($docTitle) ?>&quot; ແທ້ບໍ?">
                                            <input type="hidden" name="id" value="<?= (int) $doc['id'] ?>">
                                            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                                            <button type="submit" class="btn-icon btn-danger" title="ລຶບ" aria-label="ລຶບເອກະສານ <?= e($docTitle) ?>"><?= icon('trash', 16) ?></button>
                                        </form>
                                    <?php endif; ?>
                                    <?php if (!can('documents', 'edit') && !can('documents', 'delete')): ?>
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

                return baseUrl('admin/documents/index.php?' . http_build_query($pageQuery));
            }); ?>
        <?php endif; ?>
    </main>
</div>

<?php require APP_PATH . '/includes/admin_footer.php'; ?>
</body>
</html>
