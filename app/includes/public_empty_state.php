<?php

declare(strict_types=1);

/** @var string $iconName กำหนดจาก renderEmptyState() ก่อนเรียกไฟล์นี้เสมอ */
?>
<div class="empty-state">
    <?= icon($iconName, 48) ?>
    <p>ยังไม่มีข้อมูลในขณะนี้</p>
    <a href="<?= e(baseUrl('')) ?>" class="btn btn-primary empty-state-action">กลับหน้าหลัก</a>
</div>
