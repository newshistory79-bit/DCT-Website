<?php

declare(strict_types=1);

/** @var array $stats */
/** @var array $recentLogins */

$statLabels = [
    'news'        => 'จำนวนข่าว',
    'employees'   => 'จำนวนพนักงาน',
    'departments' => 'จำนวนแผนก',
    'activities'  => 'จำนวนกิจกรรม',
    'documents'   => 'จำนวนเอกสาร',
    'gallery'     => 'จำนวนภาพกิจกรรม',
    'legislation' => 'จำนวนกฎหมาย/ระเบียบ',
];
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard - <?= e(APP_NAME) ?></title>
<link rel="stylesheet" href="<?= e(baseUrl('assets/css/admin.css')) ?>">
</head>
<body>
<?php require APP_PATH . '/includes/admin_header.php'; ?>

<div class="admin-layout">
    <?php require APP_PATH . '/includes/admin_sidebar.php'; ?>

    <main class="admin-content">
        <h1>Dashboard</h1>

        <section class="stat-grid">
            <?php foreach ($statLabels as $key => $label): ?>
                <div class="stat-card">
                    <span class="stat-label"><?= e($label) ?></span>
                    <span class="stat-value">
                        <?= $stats[$key] === null ? 'ยังไม่มีโมดูล' : e((string) $stats[$key]) ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </section>

        <section class="dashboard-info">
            <div class="info-box">
                <h2>ข้อมูลการเข้าสู่ระบบ</h2>
                <p>ชื่อผู้ใช้: <strong><?= e($_SESSION['full_name'] ?? '') ?></strong></p>
                <p>Role: <strong><?= e($_SESSION['role'] ?? '') ?></strong></p>
                <p>เวลาปัจจุบัน: <strong><?= e(date('d/m/Y H:i:s')) ?></strong></p>
            </div>

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
        </section>
    </main>
</div>

<?php require APP_PATH . '/includes/admin_footer.php'; ?>
</body>
</html>
