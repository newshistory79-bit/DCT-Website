<?php
require APP_PATH . '/includes/public_header.php';

$dateSource = $news['activity_date'] ?? $news['created_at'];
$dateParts  = thaiDateParts((string) $dateSource);
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
                ['icon' => 'clock', 'text' => $dateParts !== null ? 'วันที่: ' . $dateParts['day'] . ' ' . $dateParts['month'] . ' ' . $dateParts['year'] : ''],
            ]); ?>

            <div class="detail-body">
                <?php if (!empty($news['detail'])): ?>
                    <p><?= nl2br(e($news['detail'])) ?></p>
                <?php else: ?>
                    <p class="text-muted">ไม่มีรายละเอียดเพิ่มเติม</p>
                <?php endif; ?>
            </div>

            <?php renderPrevNextNav($prevItem, $nextItem); ?>

            <?php renderBackToList(baseUrl('news/index.php'), 'กลับรายการข่าว'); ?>
        </article>

        <?php renderRelatedItems($relatedItems, 'ข่าวที่เกี่ยวข้อง'); ?>
    </div>
</section>

<?php require APP_PATH . '/includes/public_footer.php'; ?>
