<?php require APP_PATH . '/includes/public_header.php'; ?>

<section class="section">
    <div class="container">
        <?php renderBreadcrumb($breadcrumb); ?>

        <?php renderPageHeader('ພາລະບົດບາດຂອງພະແນກ', 'ພາລະບົດບາດ ແລະ ໂຄງຮ່າງການຈັດຕັ້ງຂອງ ' . APP_NAME); ?>

        <article class="detail-article about-article">
            <section class="about-section">
                <div class="about-card">
                    <h2 class="about-card-heading">ທີ່ຕັ້ງ ແລະ ພາລະບົດບາດ</h2>

                    <?php if ($rolesPdfUrl !== null): ?>
                        <p class="about-doc-title">ຂໍ້ຕົກລົງວ່າດ້ວຍ ການຈັດຕັ້ງ ແລະ ການເຄື່ອນໄຫວຂອງພະແນກເຕັກໂນໂລຊີ ແລະ ການສື່ສານ ແຂວງສະຫວັນນະເຂດ</p>

                        <div class="about-card-actions">
                            <a href="<?= e($rolesPdfUrl) ?>" class="btn btn-primary" download>
                                <?= icon('download', 16) ?> ດາວໂຫລດເອກະສານ
                            </a>
                        </div>

                        <p class="detail-body">ພະແນກ ເຕັກໂນໂລຊີ ແລະ ການສື່ສານ ແຂວງ, ຂຽນເປັນຕົວອັກສອນຫຍໍ້ “ພຕສ” ແລະ ຂຽນເປັນພາສາອັງກິດວ່າ “Department of Technology and Communications” ຂຽນເປັນຕົວອັກສອນຫຍໍ້ “DTC” ແມ່ນກົງຈັກໜຶ່ງຂອງອົງການປົກຄອງແຂວງ, ມີພາລະບົດບາດເປັນເສນາທິການໃຫ້ແກ່ເຈົ້າແຂວງ, ກະຊວງ ເຕັກໂນໂລຊີ ແລະ ການສື່ສານ ໃນການຄຸ້ມຄອງ ແລະ ພັດທະນາວຽກງານເຕັກໂນໂລຊີ ແລະ ການສື່ສານ ຢູ່ແຂວງ, ພາຍໃຕ້ການຊີ້ນຳ-ນໍາພາ ແລະ ຄຸ້ມຄອງດ້ານຊີວິດການເມືອງຂອງອົງການປົກຄອງແຂວງ ແລະ ການຊີ້ນຳ-ນຳພາ ແລະ ກວດກາດ້ານເຕັກນິກວິຊາການຂອງກະຊວງເຕັກໂນໂລຊີ ແລະ ການສື່ສານ.</p>
                    <?php else: ?>
                        <p class="text-muted">ຍັງບໍ່ມີຂໍ້ມູນໃນຂະນະນີ້</p>
                    <?php endif; ?>

                    <p class="detail-body">ພະແນກ ເຕັກໂນໂລຊີ ແລະ ການສື່ສານ ແຂວງສະຫວັນນະເຂດ, ທີ່ຕັ້ງສຳນັກງານ: ບ້ານຫົວເມືອງເໜືອ, ນະຄອນໄກສອນ ພົມວິຫານ, ແຂວງສະຫວັນນະເຂດ ເບີໂທ (041) 252715</p>
                </div>
            </section>

            <section class="about-section related-items">
                <div class="about-card">
                    <h2 class="about-card-heading">ໂຄງຮ່າງການຈັດຕັ້ງ ພະແນກ ເຕັກໂນໂລຊີ ແລະ ການສື່ສານ ແຂວງສະຫວັນນະເຂດ</h2>

                    <?php if ($orgChartImage !== null): ?>
                        <div class="about-org-chart">
                            <img src="<?= e($orgChartImage) ?>" alt="ໂຄງຮ່າງການຈັດຕັ້ງ ຂອງ <?= e(APP_NAME) ?>" loading="lazy">
                        </div>

                        <div class="about-card-actions">
                            <a href="<?= e($orgChartImage) ?>" class="btn btn-outline" target="_blank" rel="noopener">
                                <?= icon('image', 16) ?> ເບິ່ງຮູບເຕັມ
                            </a>
                            <a href="<?= e($orgChartImage) ?>" class="btn btn-primary" download>
                                <?= icon('download', 16) ?> ດາວໂຫລດຮູບ
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <?= icon('image', 48) ?>
                            <p>ຍັງບໍ່ມີຮູບພາບໂຄງຮ່າງການຈັດຕັ້ງ</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </article>
    </div>
</section>

<?php require APP_PATH . '/includes/public_footer.php'; ?>
