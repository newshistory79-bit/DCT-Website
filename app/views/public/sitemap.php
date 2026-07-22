<?php require APP_PATH . '/includes/public_header.php'; ?>

<section class="section">
    <div class="container">
        <nav aria-label="breadcrumb" class="breadcrumb">
            <ol>
                <li><a href="<?= e(baseUrl('')) ?>">ຫນ້າຫຼັກ</a></li>
                <li aria-current="page">ແຜນຜັງເວັບໄຊທ໌</li>
            </ol>
        </nav>

        <div class="section-head">
            <div>
                <h2>ແຜນຜັງເວັບໄຊທ໌</h2>
                <p>ລວມລິ້ງທຸກໝວດໝູ່ຂອງເວັບໄຊທ໌ <?= e(APP_NAME) ?></p>
            </div>
        </div>

        <div class="quick-menu">
            <a href="<?= e(baseUrl('about.php')) ?>" class="quick-menu-item">
                <span class="quick-menu-icon"><?= icon('building') ?></span>
                <span class="quick-menu-title">ກ່ຽວກັບຫນ່ວຍງານ</span>
            </a>
            <a href="<?= e(baseUrl('news/index.php')) ?>" class="quick-menu-item">
                <span class="quick-menu-icon"><?= icon('news') ?></span>
                <span class="quick-menu-title">ຂ່າວສານ</span>
            </a>
            <a href="<?= e(baseUrl('activities/index.php')) ?>" class="quick-menu-item">
                <span class="quick-menu-icon"><?= icon('activity') ?></span>
                <span class="quick-menu-title">ກິດຈະກຳ</span>
            </a>
            <a href="<?= e(baseUrl('departments/index.php')) ?>" class="quick-menu-item">
                <span class="quick-menu-icon"><?= icon('department') ?></span>
                <span class="quick-menu-title">ພະແນກ</span>
            </a>
            <a href="<?= e(baseUrl('employees/index.php')) ?>" class="quick-menu-item">
                <span class="quick-menu-icon"><?= icon('employee') ?></span>
                <span class="quick-menu-title">ພະນັກງານ</span>
            </a>
            <a href="<?= e(baseUrl('documents/index.php')) ?>" class="quick-menu-item">
                <span class="quick-menu-icon"><?= icon('download') ?></span>
                <span class="quick-menu-title">ນິຕິກຳ</span>
            </a>
            <a href="<?= e(baseUrl('search.php')) ?>" class="quick-menu-item">
                <span class="quick-menu-icon"><?= icon('search') ?></span>
                <span class="quick-menu-title">ຄົ້ນຫາຂໍ້ມູນ</span>
            </a>
            <a href="<?= e(baseUrl('contact.php')) ?>" class="quick-menu-item">
                <span class="quick-menu-icon"><?= icon('contact') ?></span>
                <span class="quick-menu-title">ຕິດຕໍ່ເຮົາ</span>
            </a>
        </div>
    </div>
</section>

<?php require APP_PATH . '/includes/public_footer.php'; ?>
