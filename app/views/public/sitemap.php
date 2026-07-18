<?php require APP_PATH . '/includes/public_header.php'; ?>

<section class="section">
    <div class="container">
        <nav aria-label="breadcrumb" class="breadcrumb">
            <ol>
                <li><a href="<?= e(baseUrl('')) ?>">หน้าแรก</a></li>
                <li aria-current="page">แผนผังเว็บไซต์</li>
            </ol>
        </nav>

        <div class="section-head">
            <div>
                <h2>แผนผังเว็บไซต์</h2>
                <p>รวมลิงก์ทุกหมวดหมู่ของเว็บไซต์ <?= e(APP_NAME) ?></p>
            </div>
        </div>

        <div class="quick-menu">
            <a href="<?= e(baseUrl('about.php')) ?>" class="quick-menu-item">
                <span class="quick-menu-icon"><?= icon('building') ?></span>
                <span class="quick-menu-title">เกี่ยวกับหน่วยงาน</span>
            </a>
            <a href="<?= e(baseUrl('news/index.php')) ?>" class="quick-menu-item">
                <span class="quick-menu-icon"><?= icon('news') ?></span>
                <span class="quick-menu-title">ข่าวประชาสัมพันธ์</span>
            </a>
            <a href="<?= e(baseUrl('legislation/index.php')) ?>" class="quick-menu-item">
                <span class="quick-menu-icon"><?= icon('news') ?></span>
                <span class="quick-menu-title">กฎหมาย/ระเบียบ</span>
            </a>
            <a href="<?= e(baseUrl('activities/index.php')) ?>" class="quick-menu-item">
                <span class="quick-menu-icon"><?= icon('activity') ?></span>
                <span class="quick-menu-title">กิจกรรม</span>
            </a>
            <a href="<?= e(baseUrl('gallery/index.php')) ?>" class="quick-menu-item">
                <span class="quick-menu-icon"><?= icon('image') ?></span>
                <span class="quick-menu-title">คลังภาพ</span>
            </a>
            <a href="<?= e(baseUrl('departments/index.php')) ?>" class="quick-menu-item">
                <span class="quick-menu-icon"><?= icon('department') ?></span>
                <span class="quick-menu-title">แผนก</span>
            </a>
            <a href="<?= e(baseUrl('employees/index.php')) ?>" class="quick-menu-item">
                <span class="quick-menu-icon"><?= icon('employee') ?></span>
                <span class="quick-menu-title">บุคลากร</span>
            </a>
            <a href="<?= e(baseUrl('documents/index.php')) ?>" class="quick-menu-item">
                <span class="quick-menu-icon"><?= icon('download') ?></span>
                <span class="quick-menu-title">ดาวน์โหลดเอกสาร</span>
            </a>
            <a href="<?= e(baseUrl('search.php')) ?>" class="quick-menu-item">
                <span class="quick-menu-icon"><?= icon('search') ?></span>
                <span class="quick-menu-title">ค้นหาข้อมูล</span>
            </a>
            <a href="<?= e(baseUrl('contact.php')) ?>" class="quick-menu-item">
                <span class="quick-menu-icon"><?= icon('contact') ?></span>
                <span class="quick-menu-title">ติดต่อเรา</span>
            </a>
        </div>
    </div>
</section>

<?php require APP_PATH . '/includes/public_footer.php'; ?>
