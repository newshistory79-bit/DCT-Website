<?php

declare(strict_types=1);
?>
</main>

<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <img src="<?= e(baseUrl('assets/images/logo.jpg')) ?>" alt="<?= e(APP_NAME) ?>" class="site-brand-mark">
                <div class="footer-brand-text">
                    <h3><?= e(APP_NAME) ?></h3>
                    <p>
                        ພະແນກລັດຖະບານດ້ານເຕັກໂນໂລຊີດີຈີຕອນ ແລະ ການສື່ສານ ມຸ່ງໝັ້ນພັດທະນາລະບົບເຕັກໂນໂລຊີ
                        ແລະ ການສື່ສານເພື່ອບໍລິການປະຊາຊົນຂອງແຂວງສະຫວັນນະເຂດຢ່າງມີປະສິດທິພາບ
                    </p>
                </div>
            </div>

            <div class="footer-col">
                <h4>ລິ້ງດ່ວນ</h4>
                <ul>
                    <li><a href="<?= e(baseUrl('about.php')) ?>">ພາລະບົດບາດຂອງພະແນກ</a></li>
                    <li><a href="<?= e(baseUrl('news/index.php')) ?>">ຂ່າວສານ</a></li>
                    <li><a href="<?= e(baseUrl('activities/index.php')) ?>">ກິດຈະກຳ</a></li>
                    <li><a href="<?= e(baseUrl('departments/index.php')) ?>">ພະແນກ</a></li>
                    <li><a href="<?= e(baseUrl('documents/index.php')) ?>">ນິຕິກຳ</a></li>
                    <li><a href="<?= e(baseUrl('sitemap.php')) ?>">ແຜນຜັງເວັບໄຊທ໌</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>ຕຶດຕໍ່ເຮົາ</h4>
                <ul>
                    <li><?= icon('pin', 16) ?> &nbsp;ແຂວງສະຫວັນນະເຂດ ສປປ.ລາວ</li>
                    <li><?= icon('phone', 16) ?> &nbsp;042-511-123</li>
                    <li><?= icon('mail', 16) ?> &nbsp;info@dtcsav.gov.la</li>
                    <li><?= icon('clock', 16) ?> &nbsp;ວັນຈັນ–ວັນສຸກ 08:00–16:30</li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            &copy; <?= e((string) date('Y')) ?> <?= e(APP_NAME) ?> — ສະຫງວນລິຂະສິດ
        </div>
    </div>
</footer>

<script src="<?= e(baseUrl('assets/js/public.js')) ?>" defer></script>
</body>
</html>
