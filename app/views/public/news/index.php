<?php require APP_PATH . '/includes/public_header.php'; ?>

<section class="section">
    <div class="container">
        <?php renderBreadcrumb($breadcrumb); ?>

        <?php renderPageHeader('ຂ່າວສານ', 'ຕິດຕາມຂ່າວສານ ແລະ ປະກາດຈາກ ' . APP_NAME); ?>

        <?php if (empty($newsItems)): ?>
            <?php renderEmptyState('news'); ?>
        <?php else: ?>
            <div class="card-grid card-grid-news">
                <?php foreach ($newsItems as $item):
                    $dateSource    = $item['activity_date'] ?? $item['created_at'];
                    $dateTimestamp = strtotime((string) $dateSource);
                    $detail        = (string) ($item['detail'] ?? '');
                    $excerpt       = mb_substr($detail, 0, 90) . (mb_strlen($detail) > 90 ? '…' : '');

                    renderCard([
                        'url'             => baseUrl('news/detail.php?id=' . $item['ID']),
                        'image'           => !empty($item['image']) ? uploadUrl('news/' . $item['image']) : null,
                        'icon'            => 'news',
                        'dateBadgeInline' => $dateTimestamp !== false ? date('d.m.Y', $dateTimestamp) : null,
                        'title'           => $item['title'],
                        'excerpt'         => $excerpt,
                        'actionLabel'     => 'ອ່ານຕໍ່',
                    ]);
                endforeach; ?>
            </div>

            <?php renderPagination($currentPage, $totalPages, baseUrl('news/index.php')); ?>
        <?php endif; ?>
    </div>
</section>

<?php require APP_PATH . '/includes/public_footer.php'; ?>
