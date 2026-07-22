<?php
require APP_PATH . '/includes/public_header.php';

$dateLabel = formatDateNumeric((string) $activity['activity_date']);
?>

<section class="section">
    <div class="container">
        <nav aria-label="breadcrumb" class="breadcrumb">
            <ol>
                <li><a href="<?= e(baseUrl('')) ?>">ຫນ້າຫຼັກ</a></li>
                <li><a href="<?= e(baseUrl('activities/index.php')) ?>">ກິດຈະກຳ</a></li>
                <li aria-current="page"><?= e($activity['title']) ?></li>
            </ol>
        </nav>

        <article class="detail-article">
            <div class="detail-image">
                <?php if (!empty($activity['image'])): ?>
                    <img src="<?= e(uploadUrl('activities/' . $activity['image'])) ?>" alt="<?= e($activity['title']) ?>" loading="lazy">
                <?php else: ?>
                    <?= icon('activity', 56) ?>
                <?php endif; ?>
            </div>

            <h1 class="detail-title"><?= e($activity['title']) ?></h1>

            <div class="detail-meta">
                <?php if ($dateLabel !== null): ?>
                    <span><?= icon('clock', 16) ?> ວັນທີຈັດກິດຈະກຳ: <?= e($dateLabel) ?></span>
                <?php endif; ?>
                <?php if (!empty($activity['location'])): ?>
                    <span><?= icon('pin', 16) ?> ສະຖານທີ່: <?= e($activity['location']) ?></span>
                <?php endif; ?>
            </div>

            <div class="detail-body">
                <?php if (!empty($activity['description'])): ?>
                    <p><?= nl2br(e($activity['description'])) ?></p>
                <?php else: ?>
                    <p class="text-muted">ບໍ່ມີລາຍລະອຽດເພີ່ມເຕີມ</p>
                <?php endif; ?>
            </div>

            <a href="<?= e(baseUrl('activities/index.php')) ?>" class="btn btn-outline">&larr; ກັບຄືນລາຍການກິດຈະກຳ</a>
        </article>
    </div>
</section>

<?php require APP_PATH . '/includes/public_footer.php'; ?>
