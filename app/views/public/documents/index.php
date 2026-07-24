<?php require APP_PATH . '/includes/public_header.php'; ?>

<section class="section">
    <div class="container">
        <?php renderBreadcrumb($breadcrumb); ?>

        <?php renderPageHeader('ນິຕິກຳ', 'ລວມເອກະສານ, ແບບຟອມ ແລະ ໄຟລ໌ເຜີຍແຜ່ຈາກ ' . APP_NAME); ?>

        <?php if (empty($documentItems)): ?>
            <?php renderEmptyState('download'); ?>
        <?php else: ?>
            <div class="filter-buttons" role="group" aria-label="ກັ່ນຕອງຕາມປະເພດເອກະສານ">
                <button type="button" class="filter-button active" data-filter="all">
                    ທັງໝົດ <span class="filter-count">0</span>
                </button>
                <?php foreach (\App\Models\DocumentModel::CATEGORIES as $value => $label): ?>
                    <button type="button" class="filter-button" data-filter="<?= e($value) ?>">
                        <?= e($label) ?> <span class="filter-count">0</span>
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="card-grid" id="documentGrid">
                <?php foreach ($documentItems as $doc): ?>
                    <?php renderDocumentCard($doc); ?>
                <?php endforeach; ?>
            </div>

            <p class="text-muted doc-filter-empty" id="documentFilterEmpty" hidden>ບໍ່ພົບເອກະສານໃນປະເພດນີ້</p>

            <?php renderPagination($currentPage, $totalPages, baseUrl('documents/index.php')); ?>
        <?php endif; ?>
    </div>
</section>

<?php require APP_PATH . '/includes/public_footer.php'; ?>
