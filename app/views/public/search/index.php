<?php require APP_PATH . '/includes/public_header.php'; ?>

<section class="section">
    <div class="container">
        <?php renderBreadcrumb($breadcrumb); ?>

        <?php renderPageHeader('ຄົ້ນຫາ', $keyword !== '' ? 'ຜົນການຄົ້ນຫາສຳລັບ "' . $keyword . '"' : 'ຄົ້ນຫາຂໍ້ມູນຂ່າວສານ ກິດຈະກຳ ນິຕິກຳ ພະແນກ ແລະ ພະນັກງານຂອງ ' . APP_NAME); ?>

        <form method="get" action="<?= e(baseUrl('search.php')) ?>" class="search-box gallery-search" role="search" aria-label="ຄົ້ນຫາຂໍ້ມູນທົ່ວທັງເວັບໄຊທ໌">
            <input type="text" name="q" placeholder="ຄົ້ນຫາຂໍ້ມູນ..." value="<?= e($keyword) ?>" aria-label="ຄຳຄົ້ນຫາ" maxlength="150">
            <button type="submit" aria-label="ຄົ້ນຫາ"><?= icon('search', 14) ?></button>
        </form>

        <?php if ($keyword === '' || empty($sections)): ?>
            <?php renderEmptyState('search'); ?>
        <?php else: ?>
            <p class="text-muted">ພົບທັງໝົດ <?= $totalResults ?> ລາຍການ</p>

            <?php foreach ($sections as $key => $section): ?>
                <section class="related-items" aria-labelledby="search-section-<?= e($key) ?>">
                    <h2 id="search-section-<?= e($key) ?>" class="related-items-title"><?= e($section['label']) ?> (<?= $section['total'] ?>)</h2>

                    <div class="card-grid">
                        <?php foreach ($section['items'] as $item): ?>
                            <?php if ($section['isDocument']): ?>
                                <?php renderDocumentCard($item); ?>
                            <?php else: ?>
                                <?php renderCard($item); ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>

                    <?php renderPagination($section['currentPage'], $section['totalPages'], baseUrl('search.php'), $section['extraQuery'], $section['pageParam']); ?>
                </section>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php require APP_PATH . '/includes/public_footer.php'; ?>
