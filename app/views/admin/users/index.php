<?php

declare(strict_types=1);

/** @var array $users */
/** @var int $total */
/** @var int $totalPages */
/** @var int $currentPage */
/** @var int $perPage */
/** @var array $perPageOptions */
/** @var string $keyword */
/** @var string $role */
/** @var string $status */
/** @var string $sort */
/** @var string $direction */
/** @var string $csrfToken */
/** @var string|null $successMessage */
/** @var string|null $errorMessage */

$baseQuery = [
    'keyword'  => $keyword,
    'role'     => $role,
    'status'   => $status,
    'per_page' => $perPage,
];

$sortUrl = function (string $column) use ($sort, $direction, $baseQuery): string {
    $query              = $baseQuery;
    $query['sort']      = $column;
    $query['direction'] = ($sort === $column && strtolower($direction) === 'asc') ? 'desc' : 'asc';

    return baseUrl('admin/users/index.php?' . http_build_query($query));
};

$sortIndicator = function (string $column) use ($sort, $direction): string {
    if ($sort !== $column) {
        return '';
    }

    return strtolower($direction) === 'asc' ? ' &#9650;' : ' &#9660;';
};

$columns = [
    'id'         => 'ID',
    'username'   => 'ชื่อผู้ใช้',
    'full_name'  => 'ชื่อ-นามสกุล',
    'role'       => 'สิทธิ์',
    'status'     => 'สถานะ',
    'created_at' => 'วันที่สร้าง',
];

$currentUserId = (int) ($_SESSION['user_id'] ?? 0);

// Role Badge Variant — Admin=danger (สิทธิ์สูงสุด เตือนสายตา), Editor=info, Staff=muted (Presentation เท่านั้น ไม่กระทบ Permission จริง)
$roleVariant = static function (string $role): string {
    return match ($role) {
        'Admin'  => 'danger',
        'Editor' => 'info',
        default  => 'muted',
    };
};

// Page Header — ดึง title/description จาก Single Source of Truth เดียวกับ Sidebar/Breadcrumb (Stage DS2-DS4 Pattern)
$adminMenuItems  = require APP_PATH . '/config/admin_menu.php';
$currentMenuItem = findAdminMenuItemByUrl($adminMenuItems, 'admin/users/index.php');
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
            can('users', 'create') ? [['label' => '+ เพิ่มผู้ใช้งาน', 'url' => baseUrl('admin/users/form.php')]] : []
        ); ?>

        <?php if ($successMessage !== null): ?>
            <p class="alert alert-success"><?= e($successMessage) ?></p>
        <?php endif; ?>
        <?php if ($errorMessage !== null): ?>
            <p class="alert alert-error"><?= e($errorMessage) ?></p>
        <?php endif; ?>

        <form method="get" action="<?= e(baseUrl('admin/users/index.php')) ?>" class="filter-bar">
            <div class="search-input-icon">
                <?= icon('search', 16) ?>
                <input type="text" name="keyword" value="<?= e($keyword) ?>" placeholder="ค้นหาชื่อผู้ใช้, ชื่อ-นามสกุล หรืออีเมล" aria-label="ค้นหาผู้ใช้งาน">
            </div>

            <select name="role">
                <option value="">สิทธิ์ทั้งหมด</option>
                <option value="Admin" <?= $role === 'Admin' ? 'selected' : '' ?>>Admin</option>
                <option value="Editor" <?= $role === 'Editor' ? 'selected' : '' ?>>Editor</option>
                <option value="Staff" <?= $role === 'Staff' ? 'selected' : '' ?>>Staff</option>
            </select>

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

        <?php if (empty($users)): ?>
            <?php renderAdminEmptyState(
                'ไม่พบข้อมูลผู้ใช้งาน ลองปรับคำค้นหาหรือตัวกรอง',
                'users',
                can('users', 'create') ? ['url' => baseUrl('admin/users/form.php'), 'label' => '+ เพิ่มผู้ใช้งาน'] : null
            ); ?>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="data-table data-table-zebra">
                    <thead>
                        <tr>
                            <?php foreach ($columns as $col => $label): ?>
                                <th><a href="<?= e($sortUrl($col)) ?>"><?= e($label) . $sortIndicator($col) ?></a></th>
                            <?php endforeach; ?>
                            <th>อีเมล</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                            <?php $canDeleteThisUser = can('users', 'delete') && (int) $u['id'] !== $currentUserId; ?>
                            <tr>
                                <td><?= (int) $u['id'] ?></td>
                                <td><?= e($u['username']) ?></td>
                                <td><?= e($u['full_name']) ?></td>
                                <td><?php renderBadge($u['role'], $roleVariant($u['role'])); ?></td>
                                <td><?php renderBadge($u['status'], $u['status'] === 'Active' ? 'success' : 'muted'); ?></td>
                                <td><?= e($u['created_at']) ?></td>
                                <td><?= e($u['email'] ?? '-') ?></td>
                                <td class="actions">
                                    <?php if (can('users', 'edit')): ?>
                                        <a href="<?= e(baseUrl('admin/users/form.php?id=' . $u['id'])) ?>" class="btn-icon" title="แก้ไข" aria-label="แก้ไขผู้ใช้ <?= e($u['username']) ?>"><?= icon('edit', 16) ?></a>
                                    <?php endif; ?>
                                    <?php if ($canDeleteThisUser): ?>
                                        <form method="post"
                                              action="<?= e(baseUrl('admin/users/delete.php')) ?>"
                                              class="inline-form"
                                              data-confirm-modal="ยืนยันการลบผู้ใช้ &quot;<?= e($u['username']) ?>&quot; ใช่หรือไม่?">
                                            <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                                            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                                            <button type="submit" class="btn-icon btn-danger" title="ลบ" aria-label="ลบผู้ใช้ <?= e($u['username']) ?>"><?= icon('trash', 16) ?></button>
                                        </form>
                                    <?php endif; ?>
                                    <?php if (!can('users', 'edit') && !$canDeleteThisUser): ?>
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

                return baseUrl('admin/users/index.php?' . http_build_query($pageQuery));
            }); ?>
        <?php endif; ?>
    </main>
</div>

<?php require APP_PATH . '/includes/admin_footer.php'; ?>
</body>
</html>
