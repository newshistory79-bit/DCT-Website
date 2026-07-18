<?php

declare(strict_types=1);
?>
</main>

<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <span class="site-brand-mark">DTC</span>
                <div class="footer-brand-text">
                    <h3><?= e(APP_NAME) ?></h3>
                    <p>
                        หน่วยงานภาครัฐด้านเทคโนโลยีดิจิทัลและการสื่อสาร มุ่งมั่นพัฒนาระบบเทคโนโลยี
                        และการสื่อสารเพื่อบริการประชาชนของแขวงสะหวันนะเขตอย่างมีประสิทธิภาพ
                    </p>
                </div>
            </div>

            <div class="footer-col">
                <h4>ลิงก์ด่วน</h4>
                <ul>
                    <li><a href="<?= e(baseUrl('about.php')) ?>">เกี่ยวกับหน่วยงาน</a></li>
                    <li><a href="<?= e(baseUrl('news/index.php')) ?>">ข่าวประชาสัมพันธ์</a></li>
                    <li><a href="<?= e(baseUrl('activities/index.php')) ?>">กิจกรรม</a></li>
                    <li><a href="<?= e(baseUrl('gallery/index.php')) ?>">คลังภาพ</a></li>
                    <li><a href="<?= e(baseUrl('legislation/index.php')) ?>">กฎหมาย/ระเบียบ</a></li>
                    <li><a href="<?= e(baseUrl('departments/index.php')) ?>">แผนก</a></li>
                    <li><a href="<?= e(baseUrl('documents/index.php')) ?>">ดาวน์โหลดเอกสาร</a></li>
                    <li><a href="<?= e(baseUrl('sitemap.php')) ?>">แผนผังเว็บไซต์</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>ติดต่อเรา</h4>
                <ul>
                    <li><?= icon('pin', 16) ?> &nbsp;แขวงสะหวันนะเขต สปป.ลาว</li>
                    <li><?= icon('phone', 16) ?> &nbsp;042-511-123</li>
                    <li><?= icon('mail', 16) ?> &nbsp;info@dtcsav.gov.la</li>
                    <li><?= icon('clock', 16) ?> &nbsp;จันทร์–ศุกร์ 08:00–16:30</li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            &copy; <?= e((string) date('Y')) ?> <?= e(APP_NAME) ?> — สงวนลิขสิทธิ์
        </div>
    </div>
</footer>

<script src="<?= e(baseUrl('assets/js/public.js')) ?>" defer></script>
</body>
</html>
