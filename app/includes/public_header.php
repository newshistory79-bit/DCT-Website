<?php

declare(strict_types=1);

// ตัวแปรที่ View ต้นทางกำหนดไว้ก่อน require ไฟล์นี้ (ใช้ ?? กันกรณีไม่ได้ตั้งค่า)
// $pageTitle         string   หัวข้อหน้า แสดงใน <title>
// $metaDescription   string   คำอธิบายหน้าใน <meta name="description"> / og:description
// $metaKeywords      string   คำค้นหา (ไม่บังคับ - มีค่า Default)
// $ogType            string   'website' หรือ 'article' (ไม่บังคับ - Default 'website')
// $ogImage           ?string  URL รูปภาพสำหรับ og:image/twitter:image (ไม่บังคับ - ถ้าไม่มีรูปจริงจะไม่แสดง Tag นี้ ไม่ใช้รูป Dummy)
// $breadcrumb        array    รายการเดียวกับที่ส่งให้ renderBreadcrumb() ในหน้า View (ไม่บังคับ - ถ้ามีจะสร้าง JSON-LD BreadcrumbList ให้อัตโนมัติ ไม่ต้องกำหนดข้อมูลซ้ำสองที่)
// $activeNav         string   Key ของเมนูที่ Active ปัจจุบัน

$resolvedTitle       = (($pageTitle ?? '') !== '' ? $pageTitle . ' - ' : '') . APP_NAME;
$resolvedDescription = $metaDescription ?? ('เว็บไซต์ทางการของ ' . APP_NAME . ' ข้อมูลข่าวสาร บุคลากร แผนก เอกสาร และกิจกรรมของหน่วยงาน');
$resolvedKeywords    = $metaKeywords ?? (APP_NAME . ', เทคโนโลยีดิจิทัล, ข่าวประชาสัมพันธ์, สะหวันนะเขต');
$resolvedOgType      = $ogType ?? 'website';
$resolvedOgImage     = $ogImage ?? null;
$resolvedOgUrl       = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http')
    . '://' . (string) ($_SERVER['HTTP_HOST'] ?? '') . (string) ($_SERVER['REQUEST_URI'] ?? '');
$resolvedCanonical   = $resolvedOgUrl;
$resolvedTwitterCard = $resolvedOgImage !== null ? 'summary_large_image' : 'summary';
$resolvedBreadcrumb  = $breadcrumb ?? [];

// JSON-LD ฝัง Static เพื่อไม่ให้ Title/Description ที่มาจาก DB ทำลาย <script> Context (json_encode ด้วย Flag ป้องกัน HTML/Unicode Escape เอง)
$jsonLdFlags = JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;

$organizationJsonLd = [
    '@context' => 'https://schema.org',
    '@type'    => 'GovernmentOrganization',
    'name'     => APP_NAME,
    'url'      => baseUrl(''),
];

$breadcrumbJsonLd = null;
if (!empty($resolvedBreadcrumb)) {
    $breadcrumbJsonLd = [
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => array_values(array_map(
            static fn (int $index, array $item): array => [
                '@type'    => 'ListItem',
                'position' => $index + 1,
                'name'     => $item['label'],
                'item'     => $item['url'] ?? $resolvedOgUrl,
            ],
            array_keys($resolvedBreadcrumb),
            $resolvedBreadcrumb
        )),
    ];
}

$navItems = [
    'home'        => ['label' => 'ຫນ້າຫຼັກ', 'url' => baseUrl('')],
    'about'       => ['label' => 'ພາລະບົດບາດຂອງພະແນກ', 'url' => baseUrl('about.php')],
    'news'        => ['label' => 'ຂ່າວສານ', 'url' => baseUrl('news/index.php')],
    'activities'  => ['label' => 'ກິດຈະກຳ', 'url' => baseUrl('activities/index.php')],
    'departments' => ['label' => 'ພະແນກ', 'url' => baseUrl('departments/index.php')],
    'employees'   => ['label' => 'ພະນັກງານ', 'url' => baseUrl('employees/index.php')],
    'documents'   => ['label' => 'ນິຕິກຳ', 'url' => baseUrl('documents/index.php')],
    'contact'     => ['label' => 'ຕຶດຕໍ່ເຮົາ', 'url' => baseUrl('contact.php')],
];
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($resolvedTitle) ?></title>
    <meta name="description" content="<?= e($resolvedDescription) ?>">
    <meta name="keywords" content="<?= e($resolvedKeywords) ?>">
    <meta property="og:title" content="<?= e($resolvedTitle) ?>">
    <meta property="og:description" content="<?= e($resolvedDescription) ?>">
    <meta property="og:type" content="<?= e($resolvedOgType) ?>">
    <?php if ($resolvedOgImage !== null): ?>
    <meta property="og:image" content="<?= e($resolvedOgImage) ?>">
    <?php endif; ?>
    <meta property="og:url" content="<?= e($resolvedOgUrl) ?>">
    <meta name="twitter:card" content="<?= e($resolvedTwitterCard) ?>">
    <meta name="twitter:title" content="<?= e($resolvedTitle) ?>">
    <meta name="twitter:description" content="<?= e($resolvedDescription) ?>">
    <?php if ($resolvedOgImage !== null): ?>
    <meta name="twitter:image" content="<?= e($resolvedOgImage) ?>">
    <?php endif; ?>
    <link rel="canonical" href="<?= e($resolvedCanonical) ?>">
    <script type="application/ld+json"><?= json_encode($organizationJsonLd, $jsonLdFlags) ?></script>
    <?php if ($breadcrumbJsonLd !== null): ?>
    <script type="application/ld+json"><?= json_encode($breadcrumbJsonLd, $jsonLdFlags) ?></script>
    <?php endif; ?>
    <link rel="stylesheet" href="<?= e(baseUrl('assets/css/public.css')) ?>">
</head>
<body>

<div class="top-bar">
    <div class="container">
        <div class="top-bar-contacts">
            <a href="tel:0425111123"><?= icon('phone', 14) ?> 042-511-123</a>
            <a href="mailto:info@dtcsav.gov.la"><?= icon('mail', 14) ?> info@dtcsav.gov.la</a>
            <span><?= icon('clock', 14) ?> ວັນຈັນ - ວັນສຸກ 08:00 - 16:30 ໂມງ</span>
        </div>

        <div class="top-bar-right">
            <div class="top-bar-links">
                <a href="<?= e(baseUrl('sitemap.php')) ?>">ແຜນຜັງເວັບໄຊທ໌</a>
            </div>
        </div>
    </div>
</div>

<header class="site-header">
    <div class="container">
        <a href="<?= e(baseUrl('')) ?>" class="site-brand">
            <img src="<?= e(baseUrl('assets/images/logo.jpg')) ?>" alt="<?= e(APP_NAME) ?>" class="site-brand-mark">
            <span class="site-brand-text">
                <span class="site-brand-title">ພະແນກເຕັກໂນໂລຊີ ແລະ ການສື່ສານ<br>ແຂວງສະຫວັນນະເຂດ</span>
                <span class="site-brand-subtitle"><?= e(APP_NAME) ?></span>
            </span>
        </a>

        <button type="button" id="navToggle" class="nav-toggle" aria-label="เปิด/ปิดเมนู" aria-expanded="false">
            <?= icon('menu') ?>
        </button>

        <nav class="main-nav" id="mainNav" aria-label="เมนูหลัก">
            <?php foreach ($navItems as $key => $item): ?>
                <a href="<?= e($item['url']) ?>"<?= (($activeNav ?? '') === $key) ? ' class="active" aria-current="page"' : '' ?>><?= e($item['label']) ?></a>
            <?php endforeach; ?>
        </nav>
    </div>
</header>

<main id="mainContent">
