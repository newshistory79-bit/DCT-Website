<?php require APP_PATH . '/includes/public_header.php'; ?>

<section class="section">
    <div class="container">
        <?php renderBreadcrumb($breadcrumb); ?>

        <?php renderPageHeader('บุคลากร', 'รายชื่อบุคลากรของ ' . APP_NAME); ?>

        <?php if (empty($employeeItems)): ?>
            <?php renderEmptyState('employee'); ?>
        <?php else: ?>
            <div class="card-grid">
                <?php foreach ($employeeItems as $item): ?>
                    <?php renderCard($item); ?>
                <?php endforeach; ?>
            </div>

            <?php renderPagination($currentPage, $totalPages, baseUrl('employees/index.php')); ?>
        <?php endif; ?>
    </div>
</section>

<?php require APP_PATH . '/includes/public_footer.php'; ?>
