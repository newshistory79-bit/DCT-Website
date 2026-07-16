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
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>จัดการผู้ใช้งาน - <?= e(APP_NAME) ?></title>
<link rel="stylesheet" href="<?= e(baseUrl('assets/css/admin.css')) ?>">
<link rel="stylesheet" href="<?= e(baseUrl('assets/css/crud.css')) ?>">
</head>
<body>
<?php require APP_PATH . '/includes/admin_header.php'; ?>

<div class="admin-layout">
    <?php require APP_PATH . '/includes/admin_sidebar.php'; ?>

    <main class="admin-content">
        <div class="page-heading">
            <h1>จัดการผู้ใช้งาน</h1>
            <?php if (can('users', 'create')): ?>
                <a href="<?= e(baseUrl('admin/users/form.php')) ?>" class="btn-primary">+ เพิ่มผู้ใช้งาน</a>
            <?php endif; ?>
        </div>

        <?php if ($successMessage !== null): ?>
            <p class="alert alert-success"><?= e($successMessage) ?></p>
        <?php endif; ?>
        <?php if ($errorMessage !== null): ?>
            <p class="alert alert-error"><?= e($errorMessage) ?></p>
        <?php endif; ?>

        <form method="get" action="<?= e(baseUrl('admin/users/index.php')) ?>" class="filter-bar">
            <input type="text" name="keyword" value="<?= e($keyword) ?>" placeholder="ค้นหาชื่อผู้ใช้, ชื่อ-นามสกุล หรืออีเมล">

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

        <div class="table-wrapper">
            <table class="data-table">
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
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="8" class="empty-row">ไม่พบข้อมูลผู้ใช้งาน</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td><?= (int) $u['id'] ?></td>
                                <td><?= e($u['username']) ?></td>
                                <td><?= e($u['full_name']) ?></td>
                                <td><?= e($u['role']) ?></td>
                                <td>
                                    <span class="badge badge-<?= $u['status'] === 'Active' ? 'success' : 'muted' ?>">
                                        <?= e($u['status']) ?>
                                    </span>
                                </td>
                                <td><?= e($u['created_at']) ?></td>
                                <td><?= e($u['email'] ?? '-') ?></td>
                                <td class="actions">
                                    <?php if (can('users', 'edit')): ?>
                                        <a href="<?= e(baseUrl('admin/users/form.php?id=' . $u['id'])) ?>" class="btn-link">แก้ไข</a>
                                    <?php endif; ?>
                                    <?php if (can('users', 'delete') && (int) $u['id'] !== $currentUserId): ?>
                                        <form method="post"
                                              action="<?= e(baseUrl('admin/users/delete.php')) ?>"
                                              class="inline-form"
                                              data-confirm="ยืนยันการลบผู้ใช้ &quot;<?= e($u['username']) ?>&quot; ใช่หรือไม่?">
                                            <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                                            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                                            <button type="submit" class="btn-link btn-danger">ลบ</button>
                                        </form>
                                    <?php endif; ?>
                                    <?php if (!can('users', 'edit') && !(can('users', 'delete') && (int) $u['id'] !== $currentUserId)): ?>
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
                    <a href="<?= e(baseUrl('admin/users/index.php?' . http_build_query($pageQuery))) ?>"
                       class="<?= $p === $currentPage ? 'active' : '' ?>"><?= $p ?></a>
                <?php endfor; ?>
            </div>
        </div>
    </main>
</div>

<?php require APP_PATH . '/includes/admin_footer.php'; ?>
</body>
</html>
