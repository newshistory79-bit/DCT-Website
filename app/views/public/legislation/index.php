<?php require APP_PATH . '/includes/public_header.php'; ?>

<section class="section">
    <div class="container">
        <?php renderBreadcrumb($breadcrumb); ?>

        <?php renderPageHeader('กฎหมาย/ระเบียบ', 'รวมกฎหมาย ระเบียบ และประกาศจาก ' . APP_NAME); ?>

        <?php if (empty($legislationItems)): ?>
            <?php renderEmptyState('news'); ?>
        <?php else: ?>
            <div class="card-grid">
                <?php foreach ($legislationItems as $item):
                    $dateSource     = $item['effective_date'] ?? $item['created_at'];
                    $detail         = (string) ($item['detail'] ?? '');
                    $excerpt        = mb_substr($detail, 0, 90) . (mb_strlen($detail) > 90 ? '…' : '');
                    $documentNumber = (string) ($item['document_number'] ?? '');

                    if ($documentNumber !== '') {
                        $excerpt = 'เลขที่ประกาศ ' . $documentNumber . ($excerpt !== '' ? ' — ' . $excerpt : '');
                    }

                    renderCard([
                        'url'       => baseUrl('legislation/detail.php?id=' . $item['ID']),
                        'image'     => null,
                        'icon'      => 'news',
                        'dateBadge' => thaiDateParts((string) $dateSource),
                        'title'     => $item['title'],
                        'excerpt'   => $excerpt,
                    ]);
                endforeach; ?>
            </div>

            <?php renderPagination($currentPage, $totalPages, baseUrl('legislation/index.php')); ?>
        <?php endif; ?>
    </div>
</section>

<?php require APP_PATH . '/includes/public_footer.php'; ?>
