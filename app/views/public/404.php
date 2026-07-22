<?php require APP_PATH . '/includes/public_header.php'; ?>

<section class="section">
    <div class="container">
        <div class="empty-state">
            <?= icon('search', 48) ?>
            <p>ບໍ່ພົບໜ້າທີ່ທ່ານຕ້ອງການ</p>
            <div class="empty-state-actions">
                <a href="<?= e(baseUrl('')) ?>" class="btn btn-primary">ກັບຄືນຫນ້າຫຼັກ</a>
                <a href="<?= e(baseUrl('')) ?>" class="btn btn-outline js-back-link" data-fallback="<?= e(baseUrl('')) ?>">ກັບຄືນໜ້າກ່ອນໜ້າ</a>
            </div>
        </div>
    </div>
</section>

<?php require APP_PATH . '/includes/public_footer.php'; ?>
