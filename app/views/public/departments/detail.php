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
                ['icon' => 'department', 'text' => !empty($department['code']) ? 'รหัสแผนก: ' . $department['code'] : ''],
            ]); ?>

            <div class="detail-body">
                <?php if (!empty($department['description'])): ?>
                    <p><?= nl2br(e($department['description'])) ?></p>
                <?php else: ?>
                    <p class="text-muted">ไม่มีรายละเอียดเพิ่มเติม</p>
                <?php endif; ?>
            </div>

            <?php renderPrevNextNav($prevItem, $nextItem); ?>

            <?php renderBackToList(baseUrl('departments/index.php'), 'กลับรายการแผนก'); ?>
        </article>

        <?php renderRelatedItems($relatedItems, 'แผนกอื่นๆ'); ?>
    </div>
</section>

<?php require APP_PATH . '/includes/public_footer.php'; ?>
