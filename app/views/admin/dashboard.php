<?php

declare(strict_types=1);

/** @var array $stats */
/** @var array $recentLogins */
/** @var array $recentActivity */
/** @var array $dailyCounts */

$statCards = [
    'news'        => ['label' => 'จำนวนข่าว',           'icon' => 'news',       'color' => 'blue',   'url' => 'admin/news/index.php'],
    'employees'   => ['label' => 'จำนวนพนักงาน',        'icon' => 'employee',   'color' => 'green',  'url' => 'admin/employees/index.php'],
    'departments' => ['label' => 'จำนวนแผนก',           'icon' => 'department', 'color' => 'purple', 'url' => 'admin/departments/index.php'],
    'activities'  => ['label' => 'จำนวนกิจกรรม',        'icon' => 'activity',   'color' => 'orange', 'url' => 'admin/activities/index.php'],
    'documents'   => ['label' => 'จำนวนเอกสาร',         'icon' => 'download',   'color' => 'blue',   'url' => 'admin/documents/index.php'],
    'gallery'     => ['label' => 'จำนวนภาพกิจกรรม',     'icon' => 'image',      'color' => 'green',  'url' => 'admin/gallery/index.php'],
    'legislation' => ['label' => 'จำนวนกฎหมาย/ระเบียบ', 'icon' => 'news',       'color' => 'purple', 'url' => 'admin/legislation/index.php'],
];

$quickActions = [
    ['label' => 'เพิ่มข่าว',      'icon' => 'news',       'url' => 'admin/news/form.php',       'module' => 'news'],
    ['label' => 'เพิ่มพนักงาน',   'icon' => 'employee',   'url' => 'admin/employees/form.php',  'module' => 'employees'],
    ['label' => 'เพิ่มแผนก',      'icon' => 'department', 'url' => 'admin/departments/form.php', 'module' => 'departments'],
    ['label' => 'อัปโหลดเอกสาร', 'icon' => 'download',   'url' => 'admin/documents/form.php',  'module' => 'documents'],
    ['label' => 'จัดการแกลเลอรี', 'icon' => 'image',      'url' => 'admin/gallery/index.php',   'module' => 'gallery'],
];

// วันที่ปัจจุบันแบบไทย พ.ศ. (เฉพาะ Badge วันที่บน Dashboard - คนละส่วนกับวันที่บนเว็บ Public)
$thaiMonthsFull = [
    1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน', 5 => 'พฤษภาคม', 6 => 'มิถุนายน',
    7 => 'กรกฎาคม', 8 => 'สิงหาคม', 9 => 'กันยายน', 10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม',
];
$today      = new DateTimeImmutable();
$todayThai  = (int) $today->format('j') . ' ' . $thaiMonthsFull[(int) $today->format('n')] . ' ' . ((int) $today->format('Y') + 543);

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
        <div class="dashboard-topline">
            <div class="dashboard-welcome">
                <h1>Dashboard</h1>
                <p>ยินดีต้อนรับ, <?= e($_SESSION['full_name'] ?? $_SESSION['username'] ?? '') ?></p>
            </div>
            <span class="date-badge"><?= icon('clock', 16) ?> <?= e($todayThai) ?></span>
        </div>

        <section class="stat-grid">
            <?php foreach ($statCards as $key => $card): ?>
                <div class="stat-card">
                    <span class="stat-icon stat-icon-<?= e($card['color']) ?>"><?= icon($card['icon'], 22) ?></span>
                    <span class="stat-text">
                        <span class="stat-label"><?= e($card['label']) ?></span>
                        <span class="stat-value">
                            <?= $stats[$key] === null ? 'ยังไม่มีโมดูล' : e((string) $stats[$key]) ?>
                        </span>
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
                    <a href="<?= e(baseUrl($action['url'])) ?>" class="quick-action-btn">
                        <span class="quick-action-icon"><?= icon($action['icon'], 20) ?></span>
                        <?= e($action['label']) ?>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </section>

        <section class="dashboard-info">
            <div class="info-box">
                <h2>กิจกรรมล่าสุด</h2>
                <?php if (!can('activity_log', 'view')): ?>
                    <p>ชื่อผู้ใช้: <strong><?= e($_SESSION['full_name'] ?? '') ?></strong></p>
                    <p>Role: <strong><?= e($_SESSION['role'] ?? '') ?></strong></p>
                    <p>เวลาปัจจุบัน: <strong><?= e(date('d/m/Y H:i:s')) ?></strong></p>
                <?php elseif (empty($recentActivity)): ?>
                    <p>ยังไม่มีกิจกรรม</p>
                <?php else: ?>
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
                                        <span class="badge badge-muted"><?= e($log['module']) ?></span>
                                        <?= e($log['created_at']) ?>
                                    </span>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <?php if (can('activity_log', 'view')): ?>
                <div class="info-box">
                    <h2>สถิติการใช้งาน (7 วันล่าสุด)</h2>
                    <?php if (empty($chartPoints)): ?>
                        <p>ยังไม่มีข้อมูลเพียงพอสำหรับแสดงกราฟ</p>
                    <?php else: ?>
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
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="info-box">
                    <h2>ผู้ใช้ Login ล่าสุด</h2>
                    <?php if (empty($recentLogins)): ?>
                        <p>ยังไม่มีข้อมูลการ Login</p>
                    <?php else: ?>
                        <table class="simple-table">
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
                </div>
            <?php endif; ?>
        </section>
    </main>
</div>

<?php require APP_PATH . '/includes/admin_footer.php'; ?>
</body>
</html>
