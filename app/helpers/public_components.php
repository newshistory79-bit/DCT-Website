<?php

declare(strict_types=1);

// Shared Public Components (Public Website Stage 2) — ทุกโมดูล (News/Legislation/Documents/Gallery/Departments/Employees)
// เรียกใช้ฟังก์ชันชุดนี้ร่วมกัน แทนการ Copy Markup ไปวางซ้ำในแต่ละ View เพื่อให้ดีไซน์เหมือนกัน 100% ทั้งเว็บไซต์
// ไม่เกี่ยวกับ Admin (ไฟล์นี้ใช้เฉพาะฝั่ง Public เท่านั้น)

// Breadcrumb แบบ Semantic HTML — $items = [['label'=>string, 'url'=>?string], ...]
// รายการที่ url เป็น null หรือเป็นตัวสุดท้าย = หน้าปัจจุบัน (aria-current="page")
function renderBreadcrumb(array $items): void
{
    $lastIndex = count($items) - 1;
    ?>
    <nav aria-label="breadcrumb" class="breadcrumb">
        <ol>
            <?php foreach ($items as $index => $item): ?>
                <?php if ($item['url'] !== null && $index !== $lastIndex): ?>
                    <li><a href="<?= e($item['url']) ?>"><?= e($item['label']) ?></a></li>
                <?php else: ?>
                    <li aria-current="page"><?= e($item['label']) ?></li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ol>
    </nav>
    <?php
}

// หัวข้อหน้ามาตรฐาน (Page Header) ใช้ร่วมกันทุกหน้า List/Detail
function renderPageHeader(string $title, string $subtitle = ''): void
{
    ?>
    <div class="section-head">
        <div>
            <h2><?= e($title) ?></h2>
            <?php if ($subtitle !== ''): ?>
                <p><?= e($subtitle) ?></p>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

// การ์ดมาตรฐาน ใช้ร่วมกันทุกโมดูล — Controller ต้อง Map ข้อมูลของตัวเองให้เข้ารูปนี้ก่อนเรียก
// $card = [
//   'url'             => string (ลิงก์ปลายทาง),
//   'image'           => ?string (URL รูปเต็ม ผ่าน uploadUrl() แล้ว, null = ไม่มีรูป),
//   'icon'            => string (ชื่อ Icon สำรองเมื่อไม่มีรูป ผ่าน icon() Helper),
//   'dateBadge'       => ?array ['day'=>, 'month'=>, 'year'=>] (null = ไม่แสดง Badge วันที่),
//   'dateBadgeInline' => ?string (ทางเลือกแทน dateBadge — วันที่บรรทัดเดียว เช่น "18.07.2026", ใช้แทนกันได้)
//   'badge'           => ?array ['label'=>string, 'variant'=>string] (มุมขวาบนของรูป เช่น ตำแหน่งพนักงาน, null = ไม่แสดง)
//   'title'           => string,
//   'excerpt'         => ?string,
// ]
function renderCard(array $card): void
{
    $image           = $card['image'] ?? null;
    $dateBadge       = $card['dateBadge'] ?? null;
    $dateBadgeInline = $card['dateBadgeInline'] ?? null;
    $badge           = $card['badge'] ?? null;
    $excerpt         = $card['excerpt'] ?? null;
    $attrs           = $card['attrs'] ?? [];
    $actionLabel     = $card['actionLabel'] ?? 'อ่านต่อ';

    $attrsHtml = '';
    foreach ($attrs as $attrName => $attrValue) {
        $attrsHtml .= ' ' . $attrName . '="' . e((string) $attrValue) . '"';
    }
    ?>
    <a href="<?= e($card['url']) ?>" class="card"<?= $attrsHtml ?>>
        <div class="card-thumb">
            <?php if ($dateBadgeInline !== null): ?>
                <span class="card-date-badge card-date-badge-inline"><?= e($dateBadgeInline) ?></span>
            <?php elseif ($dateBadge !== null): ?>
                <span class="card-date-badge">
                    <span class="day"><?= e($dateBadge['day']) ?></span>
                    <span class="month"><?= e($dateBadge['month']) ?></span>
                    <span class="year"><?= e($dateBadge['year']) ?></span>
                </span>
            <?php endif; ?>

            <?php if ($badge !== null): ?>
                <span class="card-badge card-badge-<?= e($badge['variant']) ?>"><?= e($badge['label']) ?></span>
            <?php endif; ?>

            <?php if ($image !== null): ?>
                <img src="<?= e($image) ?>" alt="<?= e($card['title']) ?>" loading="lazy">
            <?php else: ?>
                <?= icon($card['icon'], 40) ?>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <h3 class="card-title"><?= e($card['title']) ?></h3>
            <?php if ($excerpt !== null && $excerpt !== ''): ?>
                <p class="card-excerpt"><?= e($excerpt) ?></p>
            <?php endif; ?>
            <span class="card-readmore"><?= e($actionLabel) ?> <?= icon('arrow', 12) ?></span>
        </div>
    </a>
    <?php
}

// Badge สีตามคำสำคัญในตำแหน่งพนักงาน (Employees List/Detail — Public Redesign Stage 2)
// Presentation ล้วน อ่านจาก 'position' ที่ Controller ส่งมาอยู่แล้วเท่านั้น (ไม่ Query เพิ่ม ไม่ใช่ Business Logic ใหม่)
// คืนค่า null หากไม่มีตำแหน่ง (View ต้องเช็คก่อนส่งเข้า renderCard()/ใช้แสดงเอง)
function employeePositionBadge(string $position): ?array
{
    $position = trim($position);

    if ($position === '') {
        return null;
    }

    // เรียงจากคำเฉพาะเจาะจงไปคำกว้าง เจอคำไหนก่อนใช้คำนั้น (ตรวจแบบ Substring ไม่สนตัวพิมพ์เล็ก-ใหญ่)
    $keywordVariants = [
        'network'        => 'purple',
        'developer'      => 'orange',
        'engineer'       => 'blue',
        'support'        => 'green',
        'account'        => 'teal',
        'human resource' => 'pink',
        'hr'             => 'pink',
        'admin'          => 'yellow',
        'manager'        => 'indigo',
        'director'       => 'indigo',
        'head'           => 'indigo',
        'officer'        => 'brown',
    ];

    $lower = mb_strtolower($position);

    foreach ($keywordVariants as $keyword => $variant) {
        if (str_contains($lower, $keyword)) {
            return ['label' => $position, 'variant' => $variant];
        }
    }

    return ['label' => $position, 'variant' => 'gray'];
}

// Pagination มาตรฐาน — $baseUrl ไม่ต้องมี Query String (ฟังก์ชันเติม ?page=N ให้เอง)
// $extraQuery สำหรับกรณีต้องคง Query อื่นไว้ด้วย (เช่น Keyword ค้นหา)
// $pageParam ใช้กรณีต้องมี Pagination หลายชุดในหน้าเดียวกัน แยกกันอิสระ (เช่น Global Search
// แต่ละ Module มีพารามิเตอร์ Page ของตัวเอง เช่น news_page/legislation_page ไม่ชนกัน) - Default 'page' เดิมไม่กระทบของเดิม
function renderPagination(int $currentPage, int $totalPages, string $baseUrl, array $extraQuery = [], string $pageParam = 'page'): void
{
    if ($totalPages <= 1) {
        return;
    }
    ?>
    <div class="pagination">
        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <?php if ($p === $currentPage): ?>
                <span class="current"><?= $p ?></span>
            <?php else: ?>
                <?php $query = array_merge($extraQuery, [$pageParam => $p]); ?>
                <a href="<?= e($baseUrl . '?' . http_build_query($query)) ?>"><?= $p ?></a>
            <?php endif; ?>
        <?php endfor; ?>
    </div>
    <?php
}

// แถว Meta ใต้หัวข้อ Detail (ไอคอน+ข้อความ) ใช้ร่วมกันทุกโมดูล
// $items = [['icon'=>string, 'text'=>string], ...] — ข้ามรายการที่ text ว่างให้อัตโนมัติ
function renderDetailMeta(array $items): void
{
    $items = array_filter($items, static fn (array $item): bool => trim($item['text']) !== '');

    if (empty($items)) {
        return;
    }
    ?>
    <div class="detail-meta">
        <?php foreach ($items as $item): ?>
            <span><?= icon($item['icon'], 16) ?> <?= e($item['text']) ?></span>
        <?php endforeach; ?>
    </div>
    <?php
}

// หา ID ก่อนหน้า/ถัดไปจากรายการ ID ที่เรียงลำดับแล้ว (เรียงแบบเดียวกับหน้า List ค่าเริ่มต้น)
// $orderedIds ต้องมาจาก paginate() เดิมของแต่ละ Model เท่านั้น (ไม่ Query ตรงเพิ่ม ไม่แก้ Model)
// คืนค่า ['prev' => ?int, 'next' => ?int]
function findAdjacent(array $orderedIds, int $currentId): array
{
    $index = array_search($currentId, $orderedIds, true);

    if ($index === false) {
        return ['prev' => null, 'next' => null];
    }

    return [
        'prev' => $orderedIds[$index - 1] ?? null,
        'next' => $orderedIds[$index + 1] ?? null,
    ];
}

// ปุ่มก่อนหน้า/ถัดไปมาตรฐานสำหรับ Detail Page ทุกโมดูล
// $prev/$next = null หรือ ['url'=>string, 'title'=>string]
function renderPrevNextNav(?array $prev, ?array $next): void
{
    if ($prev === null && $next === null) {
        return;
    }
    ?>
    <nav class="prev-next-nav" aria-label="ລາຍການກ່ອນໜ້າ ແລະ ຖັດໄປ">
        <?php if ($prev !== null): ?>
            <a href="<?= e($prev['url']) ?>" class="prev-next-link prev-next-prev">
                <span class="prev-next-icon icon-flip"><?= icon('arrow', 16) ?></span>
                <span>
                    <span class="prev-next-label">ກ່ອນໜ້າ</span>
                    <span class="prev-next-title"><?= e($prev['title']) ?></span>
                </span>
            </a>
        <?php else: ?>
            <span aria-hidden="true"></span>
        <?php endif; ?>

        <?php if ($next !== null): ?>
            <a href="<?= e($next['url']) ?>" class="prev-next-link prev-next-next">
                <span>
                    <span class="prev-next-label">ຖັດໄປ</span>
                    <span class="prev-next-title"><?= e($next['title']) ?></span>
                </span>
                <span class="prev-next-icon"><?= icon('arrow', 16) ?></span>
            </a>
        <?php endif; ?>
    </nav>
    <?php
}

// รายการที่เกี่ยวข้อง ใต้ Detail Page — $items คือ Array ของ Card (โครงสร้างเดียวกับ renderCard())
function renderRelatedItems(array $items, string $title = 'รายการที่เกี่ยวข้อง'): void
{
    if (empty($items)) {
        return;
    }
    ?>
    <section class="related-items">
        <h2 class="related-items-title"><?= e($title) ?></h2>
        <div class="card-grid">
            <?php foreach ($items as $item): ?>
                <?php renderCard($item); ?>
            <?php endforeach; ?>
        </div>
    </section>
    <?php
}

// ปุ่มกลับไปหน้ารายการ มาตรฐานเดียวกันทุกโมดูล
function renderBackToList(string $url, string $label): void
{
    ?>
    <a href="<?= e($url) ?>" class="btn btn-outline">&larr; <?= e($label) ?></a>
    <?php
}

// การ์ดเอกสาร (Documents) — ไม่มี Detail Page จึงไม่ใช้โครงสร้าง renderCard() ทั้งใบเป็นลิงก์เดียว
// $doc = [
//   'title'         => string,
//   'description'   => ?string,
//   'extension'     => string (ต่อท้าย . เอง เช่น 'pdf'),
//   'sizeLabel'     => string (ผ่าน formatFileSize() มาแล้ว),
//   'dateLabel'     => ?string (วันที่เผยแพร่ ข้อความสำเร็จรูปแล้ว),
//   'downloadUrl'   => ?string (ผ่าน uploadUrl() มาแล้ว, null = ไฟล์หาย),
//   'downloadName'  => string (ชื่อไฟล์ต้นฉบับสำหรับ Attribute download),
// ]
function renderDocumentCard(array $doc): void
{
    $description = $doc['description'] ?? null;
    $dateLabel   = $doc['dateLabel'] ?? null;
    $downloadUrl = $doc['downloadUrl'] ?? null;
    ?>
    <div class="card doc-card">
        <div class="card-thumb">
            <span class="badge doc-ext-badge">.<?= e(strtoupper($doc['extension'])) ?></span>
        </div>
        <div class="card-body">
            <h3 class="card-title"><?= e($doc['title']) ?></h3>
            <?php if ($description !== null && $description !== ''): ?>
                <p class="card-excerpt"><?= e($description) ?></p>
            <?php endif; ?>

            <div class="doc-meta">
                <?php if ($dateLabel !== null): ?>
                    <span><?= icon('clock', 14) ?> <?= e($dateLabel) ?></span>
                <?php endif; ?>
                <span><?= icon('download', 14) ?> <?= e($doc['sizeLabel']) ?></span>
            </div>

            <?php if ($downloadUrl !== null): ?>
                <a href="<?= e($downloadUrl) ?>" class="btn btn-primary btn-sm btn-block" download="<?= e($doc['downloadName']) ?>">
                    <?= icon('download', 14) ?> ດາວໂຫລດ
                </a>
            <?php else: ?>
                <span class="doc-missing"><?= icon('close', 14) ?> ໄຟລ໌ບໍ່ພ້ອມໃຫ້ບໍລິການ</span>
            <?php endif; ?>
        </div>
    </div>
    <?php
}
