<?php require APP_PATH . '/includes/public_header.php'; ?>

<section class="section">
    <div class="container">
        <div class="empty-state">
            <?= icon('search', 48) ?>
            <p>ไม่พบหน้าที่คุณต้องการ</p>
            <div class="empty-state-actions">
                <a href="<?= e(baseUrl('')) ?>" class="btn btn-primary">กลับหน้าหลัก</a>
                <a href="<?= e(baseUrl('')) ?>" class="btn btn-outline js-back-link" data-fallback="<?= e(baseUrl('')) ?>">กลับหน้าก่อนหน้า</a>
            </div>
        </div>
    </div>
</section>

<?php require APP_PATH . '/includes/public_footer.php'; ?>
