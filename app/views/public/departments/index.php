<?php require APP_PATH . '/includes/public_header.php'; ?>

<section class="section">
    <div class="container">
        <?php renderBreadcrumb($breadcrumb); ?>

        <?php renderPageHeader('แผนก', 'รวมแผนกและหน่วยงานภายใน ' . APP_NAME); ?>

        <?php if (empty($departmentItems)): ?>
            <?php renderEmptyState('department'); ?>
        <?php else: ?>
            <div class="card-grid">
                <?php foreach ($departmentItems as $item): ?>
                    <?php renderCard($item); ?>
                <?php endforeach; ?>
            </div>

            <?php renderPagination($currentPage, $totalPages, baseUrl('departments/index.php')); ?>
        <?php endif; ?>
    </div>
</section>

<?php require APP_PATH . '/includes/public_footer.php'; ?>
