<?php

declare(strict_types=1);

// ตัวแปรที่ View ต้นทางกำหนดไว้ก่อน require ไฟล์นี้ (ใช้ ?? กันกรณีไม่ได้ตั้งค่า)
// $pageTitle         string   หัวข้อหน้า แสดงใน <title>
// $metaDescription   string   คำอธิบายหน้าใน <meta name="description"> / og:description
// $metaKeywords      string   คำค้นหา (ไม่บังคับ - มีค่า Default)
// $ogType            string   'website' หรือ 'article' (ไม่บังคับ - Default 'website')
// $ogImage           ?string  URL รูปภาพสำหรับ og:image (ไม่บังคับ - ถ้าไม่มีรูปจริงจะไม่แสดง Tag นี้ ไม่ใช้รูป Dummy)
// $activeNav         string   Key ของเมนูที่ Active ปัจจุบัน

$resolvedTitle       = (($pageTitle ?? '') !== '' ? $pageTitle . ' - ' : '') . APP_NAME;
$resolvedDescription = $metaDescription ?? ('เว็บไซต์ทางการของ ' . APP_NAME . ' ข้อมูลข่าวสาร บุคลากร แผนก เอกสาร และกิจกรรมของหน่วยงาน');
$resolvedKeywords    = $metaKeywords ?? (APP_NAME . ', เทคโนโลยีดิจิทัล, ข่าวประชาสัมพันธ์, สะหวันนะเขต');
$resolvedOgType      = $ogType ?? 'website';
$resolvedOgImage     = $ogImage ?? null;
$resolvedOgUrl       = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http')
    . '://' . (string) ($_SERVER['HTTP_HOST'] ?? '') . (string) ($_SERVER['REQUEST_URI'] ?? '');

$navItems = [
    'home'        => ['label' => 'หน้าแรก', 'url' => baseUrl('')],
    'about'       => ['label' => 'เกี่ยวกับหน่วยงาน', 'url' => baseUrl('about.php')],
    'news'        => ['label' => 'ข่าวประชาสัมพันธ์', 'url' => baseUrl('news/index.php')],
    'activities'  => ['label' => 'กิจกรรม', 'url' => baseUrl('activities/index.php')],
    'gallery'     => ['label' => 'คลังภาพ', 'url' => baseUrl('gallery/index.php')],
    'departments' => ['label' => 'แผนก', 'url' => baseUrl('departments/index.php')],
    'employees'   => ['label' => 'บุคลากร', 'url' => baseUrl('employees/index.php')],
    'documents'   => ['label' => 'ดาวน์โหลดเอกสาร', 'url' => baseUrl('documents/index.php')],
    'contact'     => ['label' => 'ติดต่อเรา', 'url' => baseUrl('contact.php')],
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
    <link rel="stylesheet" href="<?= e(baseUrl('assets/css/public.css')) ?>">
</head>
<body>

<div class="top-bar">
    <div class="container">
        <div class="top-bar-contacts">
            <a href="tel:0425111123"><?= icon('phone', 14) ?> 042-511-123</a>
            <a href="mailto:info@dtcsav.gov.la"><?= icon('mail', 14) ?> info@dtcsav.gov.la</a>
            <span><?= icon('clock', 14) ?> จันทร์ - ศุกร์ 08:00 - 16:30 น.</span>
        </div>

        <div class="top-bar-right">
            <div class="top-bar-links">
                <a href="<?= e(baseUrl('documents/index.php')) ?>">บริการออนไลน์</a>
                <span class="sep">|</span>
                <a href="<?= e(baseUrl('contact.php')) ?>">คำถามที่พบบ่อย</a>
                <span class="sep">|</span>
                <a href="<?= e(baseUrl('sitemap.php')) ?>">แผนผังเว็บไซต์</a>
            </div>

            <form class="search-box" action="<?= e(baseUrl('search.php')) ?>" method="get">
                <input type="text" name="q" placeholder="ค้นหาข้อมูล..." value="<?= e((string) ($_GET['q'] ?? '')) ?>">
                <button type="submit" aria-label="ค้นหา"><?= icon('search', 14) ?></button>
            </form>
        </div>
    </div>
</div>

<header class="site-header">
    <div class="container">
        <a href="<?= e(baseUrl('')) ?>" class="site-brand">
            <span class="site-brand-mark">DTC</span>
            <span class="site-brand-text">
                <span class="site-brand-title">พะแนก เทคโนโลยีและการสื่อสาร<br>แขวงสะหวันนะเขต</span>
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
