<?php require APP_PATH . '/includes/public_header.php'; ?>

<section class="section">
    <div class="container">
        <?php renderBreadcrumb($breadcrumb); ?>

        <?php renderPageHeader('ພະນັກງານ', 'ລາຍຊື່ພະນັກງານຂອງ ' . APP_NAME); ?>

        <?php if (empty($employeeItems)): ?>
            <?php renderEmptyState('employee'); ?>
        <?php else: ?>
            <div class="card-grid card-grid-employees">
                <?php foreach ($employeeItems as $item):
                    $item['badge'] = employeePositionBadge((string) ($item['excerpt'] ?? ''));
                    renderCard($item);
                endforeach; ?>
            </div>

            <?php renderPagination($currentPage, $totalPages, baseUrl('employees/index.php')); ?>
        <?php endif; ?>
    </div>
</section>

<?php require APP_PATH . '/includes/public_footer.php'; ?>
