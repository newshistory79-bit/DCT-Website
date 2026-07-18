<?php
require APP_PATH . '/includes/public_header.php';

$dateSource = $legislation['effective_date'] ?? $legislation['created_at'];
$dateParts  = thaiDateParts((string) $dateSource);
?>

<section class="section">
    <div class="container">
        <?php renderBreadcrumb($breadcrumb); ?>

        <article class="detail-article">
            <div class="detail-image">
                <?= icon('news', 56) ?>
            </div>

            <h1 class="detail-title"><?= e($legislation['title']) ?></h1>

            <?php renderDetailMeta([
                ['icon' => 'log', 'text' => !empty($legislation['document_number']) ? 'เลขที่ประกาศ: ' . $legislation['document_number'] : ''],
                ['icon' => 'clock', 'text' => $dateParts !== null ? 'วันที่มีผลบังคับใช้: ' . $dateParts['day'] . ' ' . $dateParts['month'] . ' ' . $dateParts['year'] : ''],
            ]); ?>

            <div class="detail-body">
                <?php if (!empty($legislation['detail'])): ?>
                    <p><?= nl2br(e($legislation['detail'])) ?></p>
                <?php else: ?>
                    <p class="text-muted">ไม่มีรายละเอียดเพิ่มเติม</p>
                <?php endif; ?>
            </div>

            <?php renderPrevNextNav($prevItem, $nextItem); ?>

            <?php renderBackToList(baseUrl('legislation/index.php'), 'กลับรายการกฎหมาย/ระเบียบ'); ?>
        </article>

        <?php renderRelatedItems($relatedItems, 'รายการที่เกี่ยวข้อง'); ?>
    </div>
</section>

<?php require APP_PATH . '/includes/public_footer.php'; ?>
