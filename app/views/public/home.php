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
                <h1 class="hero-title">ເຕັກໂນໂລຊີດີຈີຕອນ<br>ເພື່ອການພັດທະນາແຂວງສະຫວັນນະເຂດ</h1>
                <p class="hero-subtitle">
                    ມຸ່ງໝັ້ນພັດທະນາລະບົບເຕັກໂນໂລຊີ ແລະ ການສື່ສານ ເພື່ອບໍລິການປະຊາຊົນຢ່າງມີປະສິດທິພາບ
                </p>
            </div>

            <div class="hero-dots">
                <button type="button" class="hero-dot active" aria-label="ສະໄລ້ທີ 1"></button>
                <button type="button" class="hero-dot" aria-label="ສະໄລ້ທີ 2"></button>
                <button type="button" class="hero-dot" aria-label="ສະໄລ້ທີ 3"></button>
                <button type="button" class="hero-dot" aria-label="ສະໄລ້ທີ 4"></button>
                <button type="button" class="hero-dot" aria-label="ສະໄລ້ທີ 5"></button>
            </div>
        </div>
    </div>
</section>

<div class="container">
    <div class="quick-menu">
        <a href="<?= e(baseUrl('news/index.php')) ?>" class="quick-menu-item">
            <span class="quick-menu-icon"><?= icon('news') ?></span>
            <span class="quick-menu-title">ຂ່າວສານ</span>
            <span class="quick-menu-desc">ຕິດຕາມຂ່າວສານ ແລະ ປະກາດຈາກພະແນກ</span>
        </a>
        <a href="<?= e(baseUrl('departments/index.php')) ?>" class="quick-menu-item">
            <span class="quick-menu-icon"><?= icon('department') ?></span>
            <span class="quick-menu-title">ພະແນກ</span>
            <span class="quick-menu-desc">ຂໍ້ມູນພະແນກ ແລະ ພາລະກິດຂອງພະແນກ</span>
        </a>
        <a href="<?= e(baseUrl('employees/index.php')) ?>" class="quick-menu-item">
            <span class="quick-menu-icon"><?= icon('employee') ?></span>
            <span class="quick-menu-title">ພະນັກງານ</span>
            <span class="quick-menu-desc">ທຳນຽບພະນັກງານຂອງພະແນກ</span>
        </a>
        <a href="<?= e(baseUrl('activities/index.php')) ?>" class="quick-menu-item">
            <span class="quick-menu-icon"><?= icon('activity') ?></span>
            <span class="quick-menu-title">ກິດຈະກຳ</span>
            <span class="quick-menu-desc">ກິດຈະກຳ ແລະ ໂຄງການຂອງພະແນກ</span>
        </a>
        <a href="<?= e(baseUrl('documents/index.php')) ?>" class="quick-menu-item">
            <span class="quick-menu-icon"><?= icon('download') ?></span>
            <span class="quick-menu-title">ນິຕິກຳ</span>
            <span class="quick-menu-desc">ເອກະສານແບບຟອມສຳລັບປະຊາຊົນ</span>
        </a>
        <a href="<?= e(baseUrl('contact.php')) ?>" class="quick-menu-item">
            <span class="quick-menu-icon"><?= icon('contact') ?></span>
            <span class="quick-menu-title">ຕຶດຕໍ່ເຮົາ</span>
            <span class="quick-menu-desc">ຊ່ອງທາງການຕິດຕໍ່ພະແນກ</span>
        </a>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="section-head">
            <div>
                <h2>ຂ່າວສານລ່າສຸດ</h2>
                <p>ຕິດຕາມຂ່າວສານ ແລະ ຄວາມເຄື່ອນໄຫວລ່າສຸດຈາກພະແນກ</p>
            </div>
            <a href="<?= e(baseUrl('news/index.php')) ?>" class="section-link">ເບິ່ງທັງໝົດ <?= icon('arrow', 14) ?></a>
        </div>

        <?php if (empty($latestNews)): ?>
            <?php renderEmptyState('news'); ?>
        <?php else: ?>
            <div class="card-grid card-grid-news">
                <?php foreach ($latestNews as $item):
                    $dateSource    = $item['activity_date'] ?? $item['created_at'];
                    $dateTimestamp = strtotime((string) $dateSource);
                    $dateNumeric   = $dateTimestamp !== false ? date('d.m.Y', $dateTimestamp) : null;
                    $excerpt       = mb_substr((string) $item['detail'], 0, 90);
                ?>
                    <a href="<?= e(baseUrl('news/detail.php?id=' . $item['ID'])) ?>" class="card">
                        <div class="card-thumb">
                            <?php if ($dateNumeric !== null): ?>
                                <span class="card-date-badge card-date-badge-inline"><?= e($dateNumeric) ?></span>
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
                            <span class="card-readmore">ອ່ານຕໍ່ <?= icon('arrow', 12) ?></span>
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
