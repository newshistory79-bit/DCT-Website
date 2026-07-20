<?php

declare(strict_types=1);

/** @var array $stats */
/** @var array $recentLogins */
/** @var array $recentActivity */
/** @var array $dailyCounts */

// Design System v3 — Stage UI-2 (Retrofit: Dashboard)
// ข้อมูล ($stats/$recentLogins/$recentActivity/$dailyCounts) มาจาก DashboardController เดิมทุกประการ
// ไม่มีการแก้ไข Controller/Model ใดๆ — ส่วนที่เพิ่มด้านล่างทั้งหมดเป็น Presentation คำนวณในระดับ View เท่านั้น
// (Pattern เดียวกับ $chartPoints/$chartLinePath เดิมที่มีอยู่ก่อนแล้ว)

$statCards = [
    'news'        => ['label' => 'จำนวนข่าว',           'icon' => 'news',       'color' => 'blue',   'url' => 'admin/news/index.php'],
    'employees'   => ['label' => 'จำนวนพนักงาน',        'icon' => 'employee',   'color' => 'green',  'url' => 'admin/employees/index.php'],
    'departments' => ['label' => 'จำนวนแผนก',           'icon' => 'department', 'color' => 'purple', 'url' => 'admin/departments/index.php'],
    'activities'  => ['label' => 'จำนวนกิจกรรม',        'icon' => 'activity',   'color' => 'orange', 'url' => 'admin/activities/index.php'],
    'documents'   => ['label' => 'จำนวนเอกสาร',         'icon' => 'download',   'color' => 'teal',   'url' => 'admin/documents/index.php'],
    'gallery'     => ['label' => 'จำนวนภาพกิจกรรม',     'icon' => 'image',      'color' => 'pink',   'url' => 'admin/gallery/index.php'],
    'legislation' => ['label' => 'จำนวนกฎหมาย/ระเบียบ', 'icon' => 'news',       'color' => 'indigo', 'url' => 'admin/legislation/index.php'],
];

$quickActions = [
    ['label' => 'เพิ่มข่าว',      'description' => 'สร้างข่าวประชาสัมพันธ์ใหม่', 'icon' => 'news',       'url' => 'admin/news/form.php',        'module' => 'news'],
    ['label' => 'เพิ่มพนักงาน',   'description' => 'เพิ่มข้อมูลบุคลากรใหม่',     'icon' => 'employee',   'url' => 'admin/employees/form.php',   'module' => 'employees'],
    ['label' => 'เพิ่มแผนก',      'description' => 'สร้างหน่วยงานใหม่',          'icon' => 'department', 'url' => 'admin/departments/form.php', 'module' => 'departments'],
    ['label' => 'อัปโหลดเอกสาร', 'description' => 'อัปโหลดเอกสารใหม่',          'icon' => 'download',   'url' => 'admin/documents/form.php',   'module' => 'documents'],
    ['label' => 'จัดการแกลเลอรี', 'description' => 'จัดการภาพกิจกรรม',           'icon' => 'image',      'url' => 'admin/gallery/index.php',    'module' => 'gallery'],
];

// วันที่/เวลาปัจจุบันแบบไทย พ.ศ. สำหรับ Hero Header (เดิมใช้แค่ $todayThai ใน Date Badge เดียว
// ตอนนี้ Hero ต้องการทั้งวันในสัปดาห์ + เวลา จึงเพิ่ม $thaiDaysFull/$nowTimeThai — Presentation ล้วน)
$thaiMonthsFull = [
    1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน', 5 => 'พฤษภาคม', 6 => 'มิถุนายน',
    7 => 'กรกฎาคม', 8 => 'สิงหาคม', 9 => 'กันยายน', 10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม',
];
$thaiDaysFull = [
    0 => 'วันอาทิตย์', 1 => 'วันจันทร์', 2 => 'วันอังคาร', 3 => 'วันพุธ', 4 => 'วันพฤหัสบดี', 5 => 'วันศุกร์', 6 => 'วันเสาร์',
];
$today       = new DateTimeImmutable();
$todayThai   = $thaiDaysFull[(int) $today->format('w')] . ' ที่ ' . (int) $today->format('j') . ' ' . $thaiMonthsFull[(int) $today->format('n')] . ' ' . ((int) $today->format('Y') + 543);
$nowTimeThai = 'เวลา ' . $today->format('H:i') . ' น.';

$hour = (int) $today->format('G');
if ($hour < 12) {
    $heroGreeting = 'สวัสดีตอนเช้า';
} elseif ($hour < 17) {
    $heroGreeting = 'สวัสดีตอนบ่าย';
} else {
    $heroGreeting = 'สวัสดีตอนเย็น';
}
$heroName = (string) ($_SESSION['full_name'] ?? $_SESSION['username'] ?? '');
$heroRole = (string) ($_SESSION['role'] ?? '');

// Hero Aside (Trusted HTML ที่ View นี้สร้างเอง ไม่ใช่ User Input) — Reuse renderBadge()/.date-badge เดิม
// ซ้อนกัน 3 อัน (Role/วันที่/เวลา) แทนการสร้าง Component ใหม่ - ดู renderAdminHero() ที่ app/helpers/admin_components.php
ob_start();
if ($heroRole !== '') {
    renderBadge($heroRole, 'info');
}
?>
<span class="date-badge"><?= icon('clock', 16) ?> <?= e($todayThai) ?></span>
<span class="date-badge"><?= icon('clock', 16) ?> <?= e($nowTimeThai) ?></span>
<?php
$heroAsideHtml = ob_get_clean();

// คำนวณจุดของ SVG Line Chart จาก $dailyCounts (key=Y-m-d, value=count) - Presentation คำนวณในระดับ View เท่านั้น
$chartWidth  = 280;
$chartHeight = 110;
$chartPoints = [];
if (!empty($dailyCounts)) {
    $values  = array_values($dailyCounts);
    $maxVal  = max(1, max($values));
    $count   = count($values);
    $stepX   = $count > 1 ? $chartWidth / ($count - 1) : 0;

    foreach (array_values($dailyCounts) as $i => $val) {
        $x = $count > 1 ? $i * $stepX : $chartWidth / 2;
        $y = $chartHeight - (($val / $maxVal) * ($chartHeight - 20)) - 5;
        $chartPoints[] = ['x' => round($x, 1), 'y' => round($y, 1), 'value' => $val];
    }
}
$chartLinePath = '';
$chartAreaPath = '';
foreach ($chartPoints as $i => $p) {
    $chartLinePath .= ($i === 0 ? 'M' : 'L') . $p['x'] . ',' . $p['y'] . ' ';
}
if (!empty($chartPoints)) {
    $chartAreaPath = $chartLinePath . 'L' . end($chartPoints)['x'] . ',' . $chartHeight . ' L0,' . $chartHeight . ' Z';
}

// Segmented Control ของ Analytics (7/30/90 วัน) — UI เท่านั้นตามที่ตกลงไว้ใน Stage UI-2 (ยังไม่เชื่อม Backend)
// DashboardController/ActivityLogModel ยังคำนวณให้แค่ 7 วันเหมือนเดิมทุกประการ ปุ่ม 30/90 จึงสลับไปแสดง
// Panel ว่าง (renderAdminEmptyState เดิม) แทนการดึงข้อมูลจริง — initSegmentedControl() ใน admin.js (Stage
// UI-1/UI-2) เป็นตัวสลับ Panel ให้ ไม่มี JavaScript เฉพาะหน้านี้
ob_start();
renderAdminSegmentedControl('chartRange', ['7' => '7 วัน', '30' => '30 วัน', '90' => '90 วัน'], '7');
$chartRangeControlHtml = ob_get_clean();

// Summary Cards ใต้กราฟ — คำนวณจาก $dailyCounts ที่ Controller ส่งมาให้อยู่แล้ว (Presentation ล้วน ไม่ใช่
// Query ใหม่) ผลรวม/สูงสุด/เฉลี่ยของ 7 วันล่าสุดที่มีอยู่ในหน้านี้เท่านั้น
$activityTotal   = !empty($dailyCounts) ? array_sum($dailyCounts) : 0;
$activityPeakVal = !empty($dailyCounts) ? max($dailyCounts) : 0;
$activityPeakKey = !empty($dailyCounts) ? (string) array_search($activityPeakVal, $dailyCounts, true) : '';
$activityAvg     = !empty($dailyCounts) ? (int) round($activityTotal / count($dailyCounts)) : 0;
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard - <?= e(APP_NAME) ?></title>
<link rel="stylesheet" href="<?= e(baseUrl('assets/css/admin.css')) ?>">
<link rel="stylesheet" href="<?= e(baseUrl('assets/css/crud.css')) ?>">
</head>
<body>
<?php require APP_PATH . '/includes/admin_header.php'; ?>

<div class="admin-layout">
    <?php require APP_PATH . '/includes/admin_sidebar.php'; ?>

    <main class="admin-content">
        <?php renderAdminHero(
            $heroGreeting,
            $heroName,
            'จัดการระบบและติดตามข้อมูลสำคัญของหน่วยงานได้จากที่นี่',
            $heroAsideHtml
        ); ?>

        <section class="stat-grid">
            <?php foreach ($statCards as $key => $card): ?>
                <div class="stat-card">
                    <span class="stat-icon stat-icon-<?= e($card['color']) ?>"><?= icon($card['icon'], 22) ?></span>
                    <span class="stat-text">
                        <span class="stat-label"><?= e($card['label']) ?></span>
                        <span class="stat-value">
                            <?= $stats[$key] === null ? 'ยังไม่มีโมดูล' : e((string) $stats[$key]) ?>
                        </span>
                        <?php if ($stats[$key] !== null): ?>
                            <span class="stat-trend stat-trend-flat">– ยังไม่มีข้อมูลเปรียบเทียบ</span>
                        <?php endif; ?>
                    </span>
                    <?php if ($card['url'] !== null && $stats[$key] !== null): ?>
                        <a href="<?= e(baseUrl($card['url'])) ?>" class="stat-link">ดูทั้งหมด &rarr;</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </section>

        <section class="quick-actions">
            <?php foreach ($quickActions as $action): ?>
                <?php if (can($action['module'], 'create')): ?>
                    <a href="<?= e(baseUrl($action['url'])) ?>" class="quick-action-card">
                        <span class="quick-action-card-icon"><?= icon($action['icon'], 20) ?></span>
                        <span class="quick-action-card-text">
                            <span class="quick-action-card-title"><?= e($action['label']) ?></span>
                            <span class="quick-action-card-desc"><?= e($action['description']) ?></span>
                        </span>
                        <span class="quick-action-card-arrow"><?= icon('arrow', 18) ?></span>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </section>

        <section class="dashboard-info">
            <?php renderAdminSectionCard('กิจกรรมล่าสุด', function () use ($recentActivity): void { ?>
                <?php if (!can('activity_log', 'view')): ?>
                    <p>ชื่อผู้ใช้: <strong><?= e($_SESSION['full_name'] ?? '') ?></strong></p>
                    <p>Role: <strong><?= e($_SESSION['role'] ?? '') ?></strong></p>
                    <p>เวลาปัจจุบัน: <strong><?= e(date('d/m/Y H:i:s')) ?></strong></p>
                <?php elseif (empty($recentActivity)): ?>
                    <?php renderAdminEmptyState('ยังไม่มีกิจกรรม', 'log'); ?>
                <?php else: ?>
                    <div class="timeline-scroll">
                        <ul class="timeline">
                            <?php foreach ($recentActivity as $index => $log): ?>
                                <li>
                                    <span class="timeline-dot-col">
                                        <span class="timeline-dot"><?= icon('log', 14) ?></span>
                                        <?php if ($index < count($recentActivity) - 1): ?>
                                            <span class="timeline-line"></span>
                                        <?php endif; ?>
                                    </span>
                                    <span class="timeline-body">
                                        <span class="timeline-desc"><strong><?= e($log['username'] ?? 'ระบบ') ?></strong> <?= e($log['description']) ?></span>
                                        <span class="timeline-meta">
                                            <?php renderBadge($log['module'], 'muted'); ?>
                                            <?= e($log['created_at']) ?>
                                        </span>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            <?php }); ?>

            <?php if (can('activity_log', 'view')): ?>
                <?php renderAdminSectionCard('สถิติการใช้งาน', function () use ($chartPoints, $chartWidth, $chartHeight, $chartAreaPath, $chartLinePath, $dailyCounts): void { ?>
                    <?php if (empty($chartPoints)): ?>
                        <?php renderAdminEmptyState('ยังไม่มีข้อมูลเพียงพอสำหรับแสดงกราฟ', 'log'); ?>
                    <?php else: ?>
                        <div data-segmented-panel="chartRange" data-segmented-value="7">
                            <div class="chart-wrapper">
                                <svg viewBox="0 0 <?= $chartWidth ?> <?= $chartHeight + 24 ?>" preserveAspectRatio="none">
                                    <defs>
                                        <linearGradient id="chartGradient" x1="0" y1="0" x2="0" y2="1">
                                            <stop offset="0%" stop-color="#2563eb" stop-opacity="0.35"/>
                                            <stop offset="100%" stop-color="#2563eb" stop-opacity="0"/>
                                        </linearGradient>
                                    </defs>
                                    <path class="chart-area" d="<?= e($chartAreaPath) ?>"></path>
                                    <path class="chart-line" d="<?= e($chartLinePath) ?>"></path>
                                    <?php foreach ($chartPoints as $i => $p): ?>
                                        <circle class="chart-dot" cx="<?= $p['x'] ?>" cy="<?= $p['y'] ?>" r="3.5"></circle>
                                        <text class="chart-value-label" x="<?= $p['x'] ?>" y="<?= max(10, $p['y'] - 8) ?>"><?= (int) $p['value'] ?></text>
                                        <text class="chart-axis-label" x="<?= $p['x'] ?>" y="<?= $chartHeight + 18 ?>"><?= e(date('d/m', strtotime((string) array_keys($dailyCounts)[$i]))) ?></text>
                                    <?php endforeach; ?>
                                </svg>
                            </div>
                        </div>
                        <div data-segmented-panel="chartRange" data-segmented-value="30" hidden>
                            <?php renderAdminEmptyState('ยังไม่รองรับช่วงเวลานี้ในขณะนี้', 'log'); ?>
                        </div>
                        <div data-segmented-panel="chartRange" data-segmented-value="90" hidden>
                            <?php renderAdminEmptyState('ยังไม่รองรับช่วงเวลานี้ในขณะนี้', 'log'); ?>
                        </div>
                    <?php endif; ?>
                <?php }, '', $chartRangeControlHtml); ?>

                <?php if (!empty($chartPoints)): ?>
                    <section class="summary-card-grid">
                        <div class="stat-card">
                            <span class="stat-icon stat-icon-blue"><?= icon('users', 22) ?></span>
                            <span class="stat-text">
                                <span class="stat-label">กิจกรรมรวม (7 วันล่าสุด)</span>
                                <span class="stat-value"><?= e((string) $activityTotal) ?></span>
                            </span>
                        </div>
                        <div class="stat-card">
                            <span class="stat-icon stat-icon-purple"><?= icon('trend-up', 22) ?></span>
                            <span class="stat-text">
                                <span class="stat-label">วันที่มีกิจกรรมสูงสุด</span>
                                <span class="stat-value"><?= e((string) $activityPeakVal) ?></span>
                                <?php if ($activityPeakKey !== ''): ?>
                                    <span class="stat-trend stat-trend-flat"><?= e(date('d/m/Y', strtotime($activityPeakKey))) ?></span>
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="stat-card">
                            <span class="stat-icon stat-icon-teal"><?= icon('activity', 22) ?></span>
                            <span class="stat-text">
                                <span class="stat-label">ค่าเฉลี่ยต่อวัน</span>
                                <span class="stat-value"><?= e((string) $activityAvg) ?></span>
                                <span class="stat-trend stat-trend-flat">ใน 7 วันที่ผ่านมา</span>
                            </span>
                        </div>
                    </section>
                <?php endif; ?>
            <?php else: ?>
                <?php renderAdminSectionCard('ผู้ใช้ Login ล่าสุด', function () use ($recentLogins): void { ?>
                    <?php if (empty($recentLogins)): ?>
                        <?php renderAdminEmptyState('ยังไม่มีข้อมูลการ Login', 'users'); ?>
                    <?php else: ?>
                        <table class="simple-table data-table-zebra">
                            <thead>
                                <tr>
                                    <th>ชื่อผู้ใช้</th>
                                    <th>ชื่อ-นามสกุล</th>
                                    <th>Role</th>
                                    <th>เวลา Login</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentLogins as $login): ?>
                                    <tr>
                                        <td><?= e($login['username']) ?></td>
                                        <td><?= e($login['full_name']) ?></td>
                                        <td><?= e($login['role']) ?></td>
                                        <td><?= e($login['last_login_at']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                <?php }); ?>
            <?php endif; ?>
        </section>
    </main>
</div>

<?php require APP_PATH . '/includes/admin_footer.php'; ?>
</body>
</html>
