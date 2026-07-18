<?php require APP_PATH . '/includes/public_header.php'; ?>

<section class="section">
    <div class="container">
        <?php renderBreadcrumb($breadcrumb); ?>

        <?php renderPageHeader('คลังภาพ', 'รวมภาพกิจกรรมและผลงานจาก ' . APP_NAME); ?>

        <form method="get" action="<?= e(baseUrl('gallery/index.php')) ?>" class="search-box gallery-search" role="search" aria-label="ค้นหาภาพในคลังภาพ">
            <input type="text" name="keyword" placeholder="ค้นหาภาพ..." value="<?= e($keyword) ?>">
            <button type="submit" aria-label="ค้นหา"><?= icon('search', 14) ?></button>
        </form>

        <?php if (empty($galleryItems)): ?>
            <?php renderEmptyState('image'); ?>
        <?php else: ?>
            <div class="card-grid">
                <?php foreach ($galleryItems as $item): ?>
                    <?php renderCard($item); ?>
                <?php endforeach; ?>
            </div>

            <?php renderPagination($currentPage, $totalPages, baseUrl('gallery/index.php'), $keyword !== '' ? ['keyword' => $keyword] : []); ?>
        <?php endif; ?>
    </div>
</section>

<div class="lightbox" id="galleryLightbox" role="dialog" aria-modal="true" aria-label="ตัวแสดงภาพขนาดเต็ม" hidden>
    <button type="button" class="lightbox-close" aria-label="ปิดตัวแสดงภาพ">
        <?= icon('close', 20) ?>
    </button>
    <button type="button" class="lightbox-prev" aria-label="ภาพก่อนหน้า">
        <span class="icon-flip"><?= icon('arrow', 22) ?></span>
    </button>
    <button type="button" class="lightbox-next" aria-label="ภาพถัดไป">
        <?= icon('arrow', 22) ?>
    </button>
    <div class="lightbox-content">
        <img class="lightbox-image" src="" alt="">
        <div class="lightbox-caption">
            <h2 class="lightbox-title"></h2>
            <p class="lightbox-description"></p>
        </div>
        <p class="lightbox-counter"></p>
    </div>
</div>

<?php require APP_PATH . '/includes/public_footer.php'; ?>
