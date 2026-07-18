<?php

declare(strict_types=1);

// การแจ้งเตือนดึงจาก activity_logs โดยตรง (Query เบาๆ อ่านอย่างเดียว) เฉพาะ Role ที่มีสิทธิ์ activity_log/view
// เท่านั้น (Admin ตาม Phase 11) เพื่อไม่ให้ Editor/Staff เห็นข้อมูล Audit Log ผ่าน Topbar โดยไม่ได้ตั้งใจ
// Topbar ถูก include ทุกหน้า Admin จึงดึงข้อมูลของตัวเองแทนที่จะพึ่ง Controller ของแต่ละหน้า (Pattern เดียวกับ admin_sidebar.php ที่อ่าน $_SESSION เอง)
$headerNotifications = [];

if (can('activity_log', 'view')) {
    $headerNotifications = (new \App\Models\ActivityLogModel())->paginate([], 'created_at', 'desc', 1, 5)['data'];
}

$currentFullName = (string) ($_SESSION['full_name'] ?? '');
$avatarInitial    = mb_strtoupper(mb_substr($currentFullName !== '' ? $currentFullName : (string) ($_SESSION['username'] ?? 'A'), 0, 1));

// Breadcrumb อัตโนมัติจาก Single Source of Truth เดียวกับ Sidebar (app/config/admin_menu.php) — ห้าม Hardcode
$adminMenuItems  = require APP_PATH . '/config/admin_menu.php';
$adminCurrentPath = (string) parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH);
?>
<header class="admin-topbar">
    <button type="button" id="sidebarToggle" class="sidebar-toggle" aria-label="เปิด/ปิดเมนู">
        <?= icon('menu', 20) ?>
    </button>

    <div class="brand">
        <span class="brand-logo">DTC</span>
        <span class="brand-name"><?= e(APP_NAME) ?></span>
    </div>

    <?php renderAdminBreadcrumb($adminMenuItems, $adminCurrentPath); ?>

    <div class="topbar-actions">
        <?php if (can('activity_log', 'view')): ?>
            <div class="topbar-dropdown" id="notifDropdown">
                <button type="button" class="icon-btn" id="notifToggle" aria-label="การแจ้งเตือน" aria-expanded="false">
                    <?= icon('log', 20) ?>
                    <?php if (!empty($headerNotifications)): ?>
                        <span class="notif-dot"></span>
                    <?php endif; ?>
                </button>
                <div class="dropdown-menu dropdown-menu-wide" id="notifMenu" hidden>
                    <div class="dropdown-header">กิจกรรมล่าสุด</div>
                    <?php if (empty($headerNotifications)): ?>
                        <p class="dropdown-empty">ยังไม่มีกิจกรรม</p>
                    <?php else: ?>
                        <ul class="notif-list">
                            <?php foreach ($headerNotifications as $log): ?>
                                <li>
                                    <span class="notif-desc"><?= e($log['description']) ?></span>
                                    <span class="notif-time"><?= e($log['created_at']) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <a href="<?= e(baseUrl('admin/activity-log/index.php')) ?>" class="dropdown-footer-link">ดูกิจกรรมทั้งหมด</a>
                </div>
            </div>
        <?php endif; ?>

        <div class="topbar-dropdown" id="userDropdown">
            <button type="button" class="user-menu-toggle" id="userToggle" aria-label="เมนูผู้ใช้" aria-expanded="false">
                <span class="user-avatar"><?= e($avatarInitial) ?></span>
                <span class="user-info">
                    <span class="user-name"><?= e($currentFullName) ?></span>
                    <span class="user-role"><?= e($_SESSION['role'] ?? '') ?></span>
                </span>
                <?= icon('chevron', 14) ?>
            </button>
            <div class="dropdown-menu" id="userMenuDropdown" hidden>
                <a href="<?= e(baseUrl('admin/change-password.php')) ?>" class="dropdown-item">เปลี่ยนรหัสผ่าน</a>
                <a href="<?= e(baseUrl('admin/logout.php')) ?>" class="dropdown-item dropdown-item-danger">ออกจากระบบ</a>
            </div>
        </div>
    </div>
</header>
