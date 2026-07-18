<?php require APP_PATH . '/includes/public_header.php'; ?>

<section class="section">
    <div class="container">
        <?php renderBreadcrumb($breadcrumb); ?>

        <article class="detail-article">
            <div class="detail-image">
                <?php if (!empty($employee['image'])): ?>
                    <img src="<?= e(uploadUrl('employees/' . $employee['image'])) ?>" alt="<?= e($fullName) ?>" loading="lazy">
                <?php else: ?>
                    <?= icon('employee', 56) ?>
                <?php endif; ?>
            </div>

            <h1 class="detail-title"><?= e($fullName) ?></h1>

            <?php renderDetailMeta([
                ['icon' => 'employee', 'text' => (string) ($employee['position'] ?? '')],
            ]); ?>

            <?php renderPrevNextNav($prevItem, $nextItem); ?>

            <?php renderBackToList(baseUrl('employees/index.php'), 'กลับรายชื่อบุคลากร'); ?>
        </article>

        <?php renderRelatedItems($relatedItems, 'บุคลากรท่านอื่น'); ?>
    </div>
</section>

<?php require APP_PATH . '/includes/public_footer.php'; ?>
