<?php require APP_PATH . '/includes/public_header.php'; ?>

<section class="section">
    <div class="container">
        <?php renderBreadcrumb($breadcrumb); ?>

        <article class="detail-article">
            <div class="detail-image">
                <?= icon('department', 56) ?>
            </div>

            <h1 class="detail-title"><?= e($department['name']) ?></h1>

            <?php renderDetailMeta([
                ['icon' => 'department', 'text' => !empty($department['code']) ? 'ລະຫັດພະແນກ: ' . $department['code'] : ''],
            ]); ?>

            <div class="detail-body">
                <?php if (!empty($department['description'])): ?>
                    <p><?= nl2br(e($department['description'])) ?></p>
                <?php else: ?>
                    <p class="text-muted">ບໍ່ມີລາຍລະອຽດເພີ່ມເຕີມ</p>
                <?php endif; ?>
            </div>

            <?php renderPrevNextNav($prevItem, $nextItem); ?>

            <?php renderBackToList(baseUrl('departments/index.php'), 'ກັບຄືນລາຍການພະແນກ'); ?>
        </article>

        <?php renderRelatedItems($relatedItems, 'ພະແນກອື່ນໆ'); ?>
    </div>
</section>

<?php require APP_PATH . '/includes/public_footer.php'; ?>
