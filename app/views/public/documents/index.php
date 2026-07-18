<?php require APP_PATH . '/includes/public_header.php'; ?>

<section class="section">
    <div class="container">
        <?php renderBreadcrumb($breadcrumb); ?>

        <?php renderPageHeader('ดาวน์โหลดเอกสาร', 'รวมเอกสาร แบบฟอร์ม และไฟล์เผยแพร่จาก ' . APP_NAME); ?>

        <?php if (empty($documentItems)): ?>
            <?php renderEmptyState('download'); ?>
        <?php else: ?>
            <div class="card-grid">
                <?php foreach ($documentItems as $doc): ?>
                    <?php renderDocumentCard($doc); ?>
                <?php endforeach; ?>
            </div>

            <?php renderPagination($currentPage, $totalPages, baseUrl('documents/index.php')); ?>
        <?php endif; ?>
    </div>
</section>

<?php require APP_PATH . '/includes/public_footer.php'; ?>
