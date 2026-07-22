<?php require APP_PATH . '/includes/public_header.php'; ?>

<section class="section">
    <div class="container">
        <?php renderBreadcrumb($breadcrumb); ?>

        <?php renderPageHeader('ພາລະບົດບາດຂອງພະແນກ', 'ພາລະບົດບາດ ແລະ ໂຄງຮ່າງການຈັດຕັ້ງຂອງ ' . APP_NAME); ?>

        <article class="detail-article about-article">
            <section class="about-section">
                <h2 class="related-items-title">ພາລະບົດບາດ</h2>

                <?php if ($rolesPdfUrl !== null): ?>
                    <div class="detail-body">
                        <a href="<?= e($rolesPdfUrl) ?>" class="btn btn-primary" download>
                            <?= icon('download', 16) ?> ດາວໂຫລດເອກະສານ ພາລະບົດບາດ (PDF)
                        </a>
                    </div>
                <?php else: ?>
                    <div class="detail-body">
                        <p class="text-muted">ຍັງບໍ່ມີຂໍ້ມູນໃນຂະນະນີ້</p>
                    </div>
                <?php endif; ?>
            </section>

            <section class="about-section related-items">
                <h2 class="related-items-title">ໂຄງຮ່າງການຈັດຕັ້ງ</h2>

                <?php if ($orgChartImage !== null): ?>
                    <div class="about-org-chart">
                        <img src="<?= e($orgChartImage) ?>" alt="ໂຄງຮ່າງການຈັດຕັ້ງ ຂອງ <?= e(APP_NAME) ?>" loading="lazy">
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <?= icon('image', 48) ?>
                        <p>ຍັງບໍ່ມີຮູບພາບໂຄງຮ່າງການຈັດຕັ້ງ</p>
                    </div>
                <?php endif; ?>
            </section>
        </article>
    </div>
</section>

<?php require APP_PATH . '/includes/public_footer.php'; ?>
