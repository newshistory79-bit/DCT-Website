<?php

declare(strict_types=1);

/** @var string $iconName กำหนดจาก renderEmptyState() ก่อนเรียกไฟล์นี้เสมอ */
?>
<div class="empty-state">
    <?= icon($iconName, 48) ?>
    <p>ຍັງບໍ່ມີຂໍ້ມູນໃນຂະນະນີ້</p>
    <a href="<?= e(baseUrl('')) ?>" class="btn btn-primary empty-state-action">ກັບຄືນຫນ້າຫຼັກ</a>
</div>
