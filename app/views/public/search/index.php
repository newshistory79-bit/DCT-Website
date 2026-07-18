<?php require APP_PATH . '/includes/public_header.php'; ?>

<section class="section">
    <div class="container">
        <?php renderBreadcrumb($breadcrumb); ?>

        <?php renderPageHeader('ค้นหา', $keyword !== '' ? 'ผลการค้นหาสำหรับ "' . $keyword . '"' : 'ค้นหาข่าวสาร กิจกรรม เอกสาร แผนก และบุคลากรของ ' . APP_NAME); ?>

        <form method="get" action="<?= e(baseUrl('search.php')) ?>" class="search-box gallery-search" role="search" aria-label="ค้นหาข้อมูลทั้งเว็บไซต์">
            <input type="text" name="q" placeholder="ค้นหาข้อมูล..." value="<?= e($keyword) ?>" aria-label="คำค้นหา" maxlength="150">
            <button type="submit" aria-label="ค้นหา"><?= icon('search', 14) ?></button>
        </form>

        <?php if ($keyword === '' || empty($sections)): ?>
            <?php renderEmptyState('search'); ?>
        <?php else: ?>
            <p class="text-muted">พบทั้งหมด <?= $totalResults ?> รายการ</p>

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

<?php if (!empty($sections['gallery'])): ?>
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
<?php endif; ?>

<?php require APP_PATH . '/includes/public_footer.php'; ?>
