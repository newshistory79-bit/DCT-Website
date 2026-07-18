<?php require APP_PATH . '/includes/public_header.php'; ?>

<section class="section">
    <div class="container">
        <nav aria-label="breadcrumb" class="breadcrumb">
            <ol>
                <li><a href="<?= e(baseUrl('')) ?>">หน้าแรก</a></li>
                <li aria-current="page">กิจกรรม</li>
            </ol>
        </nav>

        <div class="section-head">
            <div>
                <h2>กิจกรรมของหน่วยงาน</h2>
                <p>ติดตามกิจกรรมและโครงการต่างๆ ของ <?= e(APP_NAME) ?></p>
            </div>
        </div>

        <?php if (empty($activities)): ?>
            <?php renderEmptyState('activity'); ?>
        <?php else: ?>
            <div class="card-grid">
                <?php foreach ($activities as $item):
                    $dateParts = thaiDateParts((string) $item['activity_date']);
                    $excerpt   = mb_substr((string) ($item['description'] ?? ''), 0, 90);
                ?>
                    <a href="<?= e(baseUrl('activities/detail.php?id=' . $item['id'])) ?>" class="card">
                        <div class="card-thumb">
                            <?php if ($dateParts !== null): ?>
                                <span class="card-date-badge">
                                    <span class="day"><?= e($dateParts['day']) ?></span>
                                    <span class="month"><?= e($dateParts['month']) ?></span>
                                    <span class="year"><?= e($dateParts['year']) ?></span>
                                </span>
                            <?php endif; ?>

                            <?php if (!empty($item['image'])): ?>
                                <img src="<?= e(uploadUrl('activities/' . $item['image'])) ?>" alt="<?= e($item['title']) ?>" loading="lazy">
                            <?php else: ?>
                                <?= icon('activity', 40) ?>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title"><?= e($item['title']) ?></h3>
                            <p class="card-excerpt"><?= e($excerpt) ?><?= mb_strlen((string) ($item['description'] ?? '')) > 90 ? '…' : '' ?></p>
                            <span class="card-readmore">อ่านต่อ <?= icon('arrow', 12) ?></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                        <?php if ($p === $currentPage): ?>
                            <span class="current"><?= $p ?></span>
                        <?php else: ?>
                            <a href="<?= e(baseUrl('activities/index.php?page=' . $p)) ?>"><?= $p ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php require APP_PATH . '/includes/public_footer.php'; ?>
