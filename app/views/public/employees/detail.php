<?php require APP_PATH . '/includes/public_header.php'; ?>

<section class="section">
    <div class="container">
        <?php renderBreadcrumb($breadcrumb); ?>

        <article class="detail-article employee-detail">
            <div class="employee-profile-card">
                <div class="employee-profile-image">
                    <?php if (!empty($employee['image'])): ?>
                        <img src="<?= e(uploadUrl('employees/' . $employee['image'])) ?>" alt="<?= e($fullName) ?>" loading="lazy">
                    <?php else: ?>
                        <?= icon('employee', 56) ?>
                    <?php endif; ?>
                </div>

                <div class="employee-profile-info">
                    <h1 class="employee-profile-name"><?= e($fullName) ?></h1>

                    <?php if (!empty($employee['position'])): ?>
                        <p class="employee-profile-position"><?= e($employee['position']) ?></p>
                    <?php endif; ?>

                    <p class="employee-profile-dept"><?= e(APP_NAME) ?></p>
                </div>
            </div>

            <?php renderPrevNextNav($prevItem, $nextItem); ?>

            <?php renderBackToList(baseUrl('employees/index.php'), 'ກັບຄືນລາຍຊື່ພະນັກງານ'); ?>
        </article>

        <?php renderRelatedItems($relatedItems, 'ພະນັກງານທ່ານອື່ນ'); ?>
    </div>
</section>

<?php require APP_PATH . '/includes/public_footer.php'; ?>
