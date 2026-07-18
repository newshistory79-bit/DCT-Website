<?php require APP_PATH . '/includes/public_header.php'; ?>

<section class="hero">
    <div class="container">
        <div class="hero-banner" id="heroSlider">
            <div class="hero-slide-bg hero-slide-bg-1 active"></div>
            <div class="hero-slide-bg hero-slide-bg-2"></div>
            <div class="hero-slide-bg hero-slide-bg-3"></div>
            <div class="hero-slide-bg hero-slide-bg-4"></div>
            <div class="hero-slide-bg hero-slide-bg-5"></div>
            <div class="hero-grid-overlay"></div>
            <div class="hero-overlay"></div>

            <div class="hero-content">
                <h1 class="hero-title">เทคโนโลยีดิจิทัล<br>เพื่อการพัฒนาแขวงสะหวันนะเขต</h1>
                <p class="hero-subtitle">
                    มุ่งมั่นพัฒนาระบบเทคโนโลยีและการสื่อสาร เพื่อบริการประชาชนอย่างมีประสิทธิภาพ
                </p>
                <div class="hero-actions">
                    <a href="<?= e(baseUrl('about.php')) ?>" class="btn btn-primary">เกี่ยวกับหน่วยงาน</a>
                </div>
            </div>

            <div class="hero-dots">
                <button type="button" class="hero-dot active" aria-label="สไลด์ที่ 1"></button>
                <button type="button" class="hero-dot" aria-label="สไลด์ที่ 2"></button>
                <button type="button" class="hero-dot" aria-label="สไลด์ที่ 3"></button>
                <button type="button" class="hero-dot" aria-label="สไลด์ที่ 4"></button>
                <button type="button" class="hero-dot" aria-label="สไลด์ที่ 5"></button>
            </div>
        </div>
    </div>
</section>

<div class="container">
    <div class="quick-menu">
        <a href="<?= e(baseUrl('news/index.php')) ?>" class="quick-menu-item">
            <span class="quick-menu-icon"><?= icon('news') ?></span>
            <span class="quick-menu-title">ข่าวประชาสัมพันธ์</span>
            <span class="quick-menu-desc">ติดตามข่าวสารและประกาศจากหน่วยงาน</span>
        </a>
        <a href="<?= e(baseUrl('departments/index.php')) ?>" class="quick-menu-item">
            <span class="quick-menu-icon"><?= icon('department') ?></span>
            <span class="quick-menu-title">แผนก</span>
            <span class="quick-menu-desc">ข้อมูลแผนกและภารกิจของหน่วยงาน</span>
        </a>
        <a href="<?= e(baseUrl('employees/index.php')) ?>" class="quick-menu-item">
            <span class="quick-menu-icon"><?= icon('employee') ?></span>
            <span class="quick-menu-title">บุคลากร</span>
            <span class="quick-menu-desc">ทำเนียบบุคลากรของหน่วยงาน</span>
        </a>
        <a href="<?= e(baseUrl('activities/index.php')) ?>" class="quick-menu-item">
            <span class="quick-menu-icon"><?= icon('activity') ?></span>
            <span class="quick-menu-title">กิจกรรม</span>
            <span class="quick-menu-desc">กิจกรรมและโครงการของหน่วยงาน</span>
        </a>
        <a href="<?= e(baseUrl('documents/index.php')) ?>" class="quick-menu-item">
            <span class="quick-menu-icon"><?= icon('download') ?></span>
            <span class="quick-menu-title">ดาวน์โหลดเอกสาร</span>
            <span class="quick-menu-desc">เอกสารแบบฟอร์มสำหรับประชาชน</span>
        </a>
        <a href="<?= e(baseUrl('contact.php')) ?>" class="quick-menu-item">
            <span class="quick-menu-icon"><?= icon('contact') ?></span>
            <span class="quick-menu-title">ติดต่อเรา</span>
            <span class="quick-menu-desc">ช่องทางการติดต่อหน่วยงาน</span>
        </a>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="section-head">
            <div>
                <h2>ข่าวประชาสัมพันธ์ล่าสุด</h2>
                <p>ติดตามข่าวสารและความเคลื่อนไหวล่าสุดจากหน่วยงาน</p>
            </div>
            <a href="<?= e(baseUrl('news/index.php')) ?>" class="section-link">ดูทั้งหมด <?= icon('arrow', 14) ?></a>
        </div>

        <?php if (empty($latestNews)): ?>
            <?php renderEmptyState('news'); ?>
        <?php else: ?>
            <div class="card-grid">
                <?php foreach ($latestNews as $item):
                    $dateSource = $item['activity_date'] ?? $item['created_at'];
                    $dateParts  = thaiDateParts((string) $dateSource);
                    $excerpt    = mb_substr((string) $item['detail'], 0, 90);
                ?>
                    <a href="<?= e(baseUrl('news/detail.php?id=' . $item['ID'])) ?>" class="card">
                        <div class="card-thumb">
                            <?php if ($dateParts !== null): ?>
                                <span class="card-date-badge">
                                    <span class="day"><?= e($dateParts['day']) ?></span>
                                    <span class="month"><?= e($dateParts['month']) ?></span>
                                    <span class="year"><?= e($dateParts['year']) ?></span>
                                </span>
                            <?php endif; ?>

                            <?php if (!empty($item['image'])): ?>
                                <img src="<?= e(uploadUrl('news/' . $item['image'])) ?>" alt="<?= e($item['title']) ?>" loading="lazy">
                            <?php else: ?>
                                <?= icon('news', 40) ?>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title"><?= e($item['title']) ?></h3>
                            <p class="card-excerpt"><?= e($excerpt) ?><?= mb_strlen((string) $item['detail']) > 90 ? '…' : '' ?></p>
                            <span class="card-readmore">อ่านต่อ <?= icon('arrow', 12) ?></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="stats-bar">
    <div class="container">
        <div class="stats-grid">
            <?php foreach ($stats as $stat): ?>
                <div class="stat-item">
                    <span class="stat-item-icon"><?= icon($stat['icon'], 22) ?></span>
                    <span>
                        <span class="stat-item-value"><?= e(number_format($stat['value'])) ?></span>
                        <span class="stat-item-label"><?= e($stat['label']) ?></span>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require APP_PATH . '/includes/public_footer.php'; ?>
