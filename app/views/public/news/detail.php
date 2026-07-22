<?php
require APP_PATH . '/includes/public_header.php';

$dateSource = $news['activity_date'] ?? $news['created_at'];
$dateLabel  = formatDateNumeric((string) $dateSource);
?>

<section class="section">
    <div class="container">
        <?php renderBreadcrumb($breadcrumb); ?>

        <article class="detail-article">
            <div class="detail-image">
                <?php if (!empty($news['image'])): ?>
                    <img src="<?= e(uploadUrl('news/' . $news['image'])) ?>" alt="<?= e($news['title']) ?>" loading="lazy">
                <?php else: ?>
                    <?= icon('news', 56) ?>
                <?php endif; ?>
            </div>

            <h1 class="detail-title"><?= e($news['title']) ?></h1>

            <?php renderDetailMeta([
                ['icon' => 'clock', 'text' => $dateLabel !== null ? 'ວັນທີ: ' . $dateLabel : ''],
            ]); ?>

            <div class="detail-body">
                <?php if (!empty($news['detail'])): ?>
                    <p><?= nl2br(e($news['detail'])) ?></p>
                <?php else: ?>
                    <p class="text-muted">ບໍ່ມີລາຍລະອຽດເພີ່ມເຕີມ</p>
                <?php endif; ?>
            </div>

            <?php renderPrevNextNav($prevItem, $nextItem); ?>

            <?php renderBackToList(baseUrl('news/index.php'), 'ກັບຄືນລາຍການຂ່າວ'); ?>
        </article>

        <?php renderRelatedItems($relatedItems, 'ຂ່າວທີ່ກ່ຽວຂ້ອງ'); ?>
    </div>
</section>

<?php require APP_PATH . '/includes/public_footer.php'; ?>
