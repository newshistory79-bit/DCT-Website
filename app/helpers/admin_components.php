<?php

declare(strict_types=1);

// Admin Panel Design System v2 — Shared Component Layer (Stage DS1)
// ใช้ร่วมกันได้ทุกโมดูล Admin (Dashboard/Departments/Employees/News/Legislation/Activities/Gallery/Documents/Users/Activity Log)
// Reuse CSS Class เดิมจาก admin.css/crud.css ให้มากที่สุด (.btn-*, .badge-*, .data-table, .pagination ฯลฯ)
// ไฟล์นี้เป็น Foundation เท่านั้น — ยังไม่ถูกเรียกใช้จาก View โมดูลใดใน Stage DS1 (รอ Retrofit ใน DS2-DS4)
//
// ตั้งชื่อ renderAdminXxx() ทุกฟังก์ชัน เพื่อไม่ให้สับสน/ชนกับ Public Component Layer
// (app/helpers/public_components.php) ซึ่งใช้ renderXxx() เฉยๆ — สองไฟล์นี้ไม่เกี่ยวข้องกัน ไม่ Reuse ข้ามกัน

// หา Menu Item จาก admin_menu.php ตาม URL ตรงตัว (Stage DS2) — ใช้ให้ View ดึง title/description
// จาก Single Source of Truth เดียวกับ Sidebar/Breadcrumb แทนการ Hardcode ข้อความซ้ำในแต่ละ View
function findAdminMenuItemByUrl(array $menuItems, string $url): ?array
{
    foreach ($menuItems as $menuItem) {
        if ($menuItem['url'] === $url) {
            return $menuItem;
        }
    }

    return null;
}

// หา Menu Item ที่ Active ตาม Path ปัจจุบัน จาก admin_menu.php (Single Source of Truth)
// หมายเหตุ: Logic การจับคู่ Active นี้ตั้งใจ Mirror มาจาก $isMenuActive ใน admin_sidebar.php แบบคำต่อคำ
// (ไม่ได้ดึงมาใช้ร่วมกันโดยตรง) เพราะข้อกำหนด Stage DS1 ห้ามแก้ไข Active Logic ภายใน admin_sidebar.php
// เพื่อความปลอดภัยสูงสุด จึงคงไฟล์นั้นไว้ตามเดิมทั้งหมด และสร้างฟังก์ชันนี้แยกไว้สำหรับ Breadcrumb โดยเฉพาะ
function findActiveAdminMenuItem(array $menuItems, string $currentPath): ?array
{
    foreach ($menuItems as $menuItem) {
        if ($menuItem['url'] === null) {
            continue;
        }

        $urlDir = dirname($menuItem['url']);

        $isActive = $urlDir === 'admin'
            ? str_ends_with($currentPath, '/' . $menuItem['url'])
            : str_contains($currentPath, '/' . $urlDir . '/');

        if ($isActive) {
            return $menuItem;
        }
    }

    return null;
}

// Breadcrumb อัตโนมัติจาก admin_menu.php + URL ปัจจุบัน (ห้าม Hardcode ตามข้อกำหนด)
// เรียกจาก admin_header.php เท่านั้น — ถ้าไม่พบ Menu Item ที่ตรง (เช่นหน้า Login/Change Password) จะไม่แสดงอะไร
function renderAdminBreadcrumb(array $menuItems, string $currentPath): void
{
    $active = findActiveAdminMenuItem($menuItems, $currentPath);

    if ($active === null) {
        return;
    }

    $isDashboard = $active['url'] === 'admin/index.php';
    ?>
    <nav class="admin-breadcrumb" aria-label="breadcrumb">
        <ol>
            <?php if ($isDashboard): ?>
                <li aria-current="page">Dashboard</li>
            <?php else: ?>
                <li><a href="<?= e(baseUrl('admin/index.php')) ?>">Dashboard</a></li>
                <li aria-current="page"><?= e($active['title'] ?? $active['label']) ?></li>
            <?php endif; ?>
        </ol>
    </nav>
    <?php
}

// Page Header มาตรฐาน — ใช้ร่วมกันได้ทุกโมดูล (List/Form/Dashboard)
// $actions = [['label' => string, 'url' => string, 'class' => ?string (Default 'btn-primary')]]
// $extra (Stage DS4) = HTML String ที่ผู้เรียกสร้างเอง (Trusted Content เท่านั้น เช่น Date Badge ของ Dashboard
// - ไม่ใช่ User Input ต้อง Escape เองก่อนส่งเข้ามา) แสดงคู่กับ Actions ฝั่งขวาของ Header - Optional พารามิเตอร์
// ใหม่ Default '' ไม่กระทบ Call Site เดิมทั้งหมดที่มีอยู่ก่อน (Backward Compatible)
function renderAdminPageHeader(string $title, string $description = '', array $actions = [], string $extra = ''): void
{
    ?>
    <div class="admin-page-header">
        <div class="admin-page-header-text">
            <h1><?= e($title) ?></h1>
            <?php if ($description !== ''): ?>
                <p class="admin-page-description"><?= e($description) ?></p>
            <?php endif; ?>
        </div>
        <?php if (!empty($actions) || $extra !== ''): ?>
            <div class="admin-page-header-actions">
                <?= $extra ?>
                <?php foreach ($actions as $action): ?>
                    <a href="<?= e($action['url']) ?>" class="<?= e($action['class'] ?? 'btn-primary') ?>"><?= e($action['label']) ?></a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

// Section Card ทั่วไป (Filter Card/Table Card/Form Card ฯลฯ) — ครอบเนื้อหาผ่าน Callback
// เพื่อให้ View เดิมยังคุม Markup ภายในได้เต็มที่ (ไม่ต้อง Redesign Logic การ Loop/Query ใดๆ)
// $headExtra (Stage UI-2) = HTML String ที่ผู้เรียกสร้างเอง (Trusted Content เท่านั้น เช่น Segmented Control
// ของการ์ด Analytics - ไม่ใช่ User Input ต้อง Escape เอง) แสดงชิดขวาของหัวการ์ด คู่กับ Title/Description
// Optional พารามิเตอร์ใหม่ Default '' ไม่กระทบ Call Site เดิมทั้งหมด (Pattern เดียวกับ $extra ของ
// renderAdminPageHeader() ที่เพิ่มไว้ตั้งแต่ Stage DS4)
function renderAdminSectionCard(string $title, callable $renderBody, string $description = '', string $headExtra = ''): void
{
    ?>
    <section class="admin-section-card">
        <?php if ($title !== '' || $headExtra !== ''): ?>
            <div class="admin-section-card-head<?= $headExtra !== '' ? ' admin-section-card-head-row' : '' ?>">
                <?php if ($title !== ''): ?>
                    <div class="admin-section-card-head-text">
                        <h2><?= e($title) ?></h2>
                        <?php if ($description !== ''): ?>
                            <p><?= e($description) ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php if ($headExtra !== ''): ?>
                    <div class="admin-section-card-head-extra"><?= $headExtra ?></div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <div class="admin-section-card-body">
            <?php $renderBody(); ?>
        </div>
    </section>
    <?php
}

// Badge มาตรฐาน — Reuse .badge เดิม เพิ่ม Variant ใหม่ (warning/danger/info) ที่ยังไม่มีมาก่อน
// $variant ที่ไม่รู้จักจะ Fallback เป็น 'muted' เสมอ (ปลอดภัย ไม่มี Class แปลกหลุดออกไป)
function renderBadge(string $label, string $variant = 'muted'): void
{
    $allowedVariants = ['success', 'muted', 'warning', 'danger', 'info'];
    $safeVariant     = in_array($variant, $allowedVariants, true) ? $variant : 'muted';
    ?>
    <span class="badge badge-<?= e($safeVariant) ?>"><?= e($label) ?></span>
    <?php
}

// Pagination มาตรฐานฝั่ง Admin — Mirror Markup เดิมทุกประการจาก app/views/admin/departments/index.php
// (.pagination > span + .pagination-links > a.active) เพื่อให้ใช้แทนของเดิมได้แบบ Drop-in ใน DS2+ โดยไม่ต้องแก้ CSS
// ตั้งชื่อ renderAdminPagination() แยกจาก Public renderPagination() ตามข้อกำหนด ห้ามใช้ชื่อซ้ำ
// $buildUrl: callable(int $page): string — แต่ละโมดูลมี Query Parameter ต่างกัน (keyword/status/sort/direction/per_page)
function renderAdminPagination(int $currentPage, int $totalPages, int $total, callable $buildUrl): void
{
    ?>
    <div class="pagination">
        <span>ທັງໝົດ <?= (int) $total ?> ລາຍການ</span>
        <div class="pagination-links">
            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <a href="<?= e($buildUrl($p)) ?>" class="<?= $p === $currentPage ? 'active' : '' ?>"><?= $p ?></a>
            <?php endfor; ?>
        </div>
    </div>
    <?php
}

// Empty State มาตรฐานฝั่ง Admin (ยังไม่เคยมีมาก่อน — ปัจจุบันแต่ละโมดูลเขียนแถวว่างในตารางแยกกันเอง)
// ใช้แทนพื้นที่ทั้ง Table/Card เมื่อไม่มีข้อมูลเลย (ต่างจาก .empty-row เดิมที่ใช้ในแถวตารางเฉยๆ ซึ่งยังคงใช้งานได้ตามปกติ)
// ตั้งชื่อ renderAdminEmptyState() แยกจาก renderEmptyState() ของ Public (app/helpers/functions.php)
// เพื่อป้องกัน Fatal Error "Cannot redeclare" เนื่องจาก PHP ไม่มี Namespace แยกระหว่างสองไฟล์นี้ (Pattern เดียวกับ renderAdminPagination())
function renderAdminEmptyState(string $message, string $iconName = 'search', ?array $action = null): void
{
    ?>
    <div class="admin-empty-state">
        <span class="admin-empty-state-icon"><?= icon($iconName, 40) ?></span>
        <p><?= e($message) ?></p>
        <?php if ($action !== null): ?>
            <a href="<?= e($action['url']) ?>" class="btn-primary"><?= e($action['label']) ?></a>
        <?php endif; ?>
    </div>
    <?php
}

// Modal มาตรฐาน — Progressive Enhancement ผ่าน initModal() ใน admin.js (Stage DS1)
// เปิดจาก Trigger ใดๆ ที่มี data-modal-target="#$id" ปิดผ่านปุ่มที่มี data-modal-close/Esc/คลิกนอกกรอบ
// หาก JavaScript ไม่ทำงาน Modal จะซ่อนด้วย [hidden] เฉยๆ ไม่กระทบการทำงานหลักของหน้า (ไม่ใช่ช่องทางเดียวในการทำงาน)
// $footer: HTML String ที่ผู้เรียกสร้างเอง (Trusted Content เท่านั้น เช่น Markup ปุ่ม - ไม่ใช่ User Input ต้อง Escape เอง)
function renderModal(string $id, string $title, callable $renderBody, string $footer = ''): void
{
    ?>
    <div class="admin-modal" id="<?= e($id) ?>" role="dialog" aria-modal="true" aria-labelledby="<?= e($id) ?>-title" hidden>
        <div class="admin-modal-dialog">
            <div class="admin-modal-head">
                <h2 id="<?= e($id) ?>-title"><?= e($title) ?></h2>
                <button type="button" class="admin-modal-close" data-modal-close aria-label="ປິດ"><?= icon('close', 18) ?></button>
            </div>
            <div class="admin-modal-body">
                <?php $renderBody(); ?>
            </div>
            <?php if ($footer !== ''): ?>
                <div class="admin-modal-footer"><?= $footer ?></div>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

// Confirm Dialog แบบ Custom (Progressive Enhancement ของ data-confirm เดิม) — เรียกครั้งเดียวต่อหน้า (Shared Instance)
// ฟอร์มที่ต้องการใช้ Dialog สวยงามนี้แทน window.confirm() เดิม ให้ใส่ data-confirm-modal="ข้อความ" แทน data-confirm
// *** data-confirm เดิมยังทำงานเหมือนเดิมทุกประการผ่าน window.confirm() ไม่ถูกแตะต้อง สำหรับฟอร์มที่ยังไม่ Retrofit ***
function renderConfirmDialog(): void
{
    ?>
    <div class="admin-modal admin-confirm-dialog" id="adminConfirmDialog" role="alertdialog" aria-modal="true" aria-labelledby="adminConfirmTitle" aria-describedby="adminConfirmMessage" hidden>
        <div class="admin-modal-dialog">
            <div class="admin-modal-head">
                <h2 id="adminConfirmTitle"><?= icon('alert-triangle', 20) ?> ຢືນຢັນການດຳເນີນການ</h2>
            </div>
            <div class="admin-modal-body">
                <p id="adminConfirmMessage"></p>
            </div>
            <div class="admin-modal-footer">
                <button type="button" class="btn-secondary" data-confirm-cancel>ຍົກເລີກ</button>
                <button type="button" class="btn-primary" data-confirm-accept>ຢືນຢັນ</button>
            </div>
        </div>
    </div>
    <?php
}

// Skeleton Loading Rows สำหรับตาราง — Static Markup ล้วน ไม่ต้องพึ่ง JavaScript ก็แสดงผลได้ (Progressive Enhancement)
// $columns = จำนวนคอลัมน์ในตาราง, $rows = จำนวนแถว Skeleton ที่จะแสดง (Default 5)
function renderSkeletonRows(int $columns, int $rows = 5): void
{
    for ($i = 0; $i < $rows; $i++) {
        ?>
        <tr class="admin-skeleton-row">
            <?php for ($c = 0; $c < $columns; $c++): ?>
                <td><span class="admin-skeleton-bar"></span></td>
            <?php endfor; ?>
        </tr>
        <?php
    }
}

// ============================================================
// Design System v3 — Stage UI-1 — Component ใหม่ (Foundation เท่านั้น
// ยังไม่ถูกเรียกใช้จาก View ใดใน Stage UI-1 เอง — รอ Retrofit ใน UI-2 เป็นต้นไป Pattern เดียวกับที่ Stage DS1 เคยทำไว้)
// ============================================================

// Hero Header มาตรฐาน — ใช้แทน renderAdminPageHeader() เฉพาะหน้า Dashboard เมื่อ Retrofit ใน UI-2
// $dateBadgeHtml: HTML String ที่ผู้เรียกสร้างเอง (Trusted Content เท่านั้น เช่น <span class="date-badge">...
// - ไม่ใช่ User Input ต้อง Escape เองก่อนส่งเข้ามา) Pattern เดียวกับ $extra ของ renderAdminPageHeader()
function renderAdminHero(string $greeting, string $name, string $subtitle, string $dateBadgeHtml = ''): void
{
    ?>
    <section class="admin-hero">
        <span class="admin-hero-illustration"><?= icon('building', 160) ?></span>
        <div class="admin-hero-content">
            <p class="admin-hero-greeting"><?= e($greeting) ?></p>
            <h1 class="admin-hero-name"><?= e($name) ?></h1>
            <p class="admin-hero-subtitle"><?= e($subtitle) ?></p>
        </div>
        <?php if ($dateBadgeHtml !== ''): ?>
            <div class="admin-hero-aside"><?= $dateBadgeHtml ?></div>
        <?php endif; ?>
    </section>
    <?php
}

// Segmented Control มาตรฐาน — Markup คู่กับ initSegmentedControl() ใน admin.js (ยิง CustomEvent
// "segmented-control:change" เมื่อสลับตัวเลือก ไม่ผูก Business Logic ใดๆ ในตัวมันเอง)
// $options = [value => label], $selected = value ที่ Active อยู่ตอนโหลดหน้า (Server-rendered ไม่ใช่ JS-only)
function renderAdminSegmentedControl(string $name, array $options, string $selected): void
{
    ?>
    <div class="segmented-control" data-segmented="<?= e($name) ?>">
        <?php foreach ($options as $value => $label): ?>
            <button type="button" data-value="<?= e((string) $value) ?>" class="<?= (string) $value === $selected ? 'active' : '' ?>"><?= e($label) ?></button>
        <?php endforeach; ?>
    </div>
    <?php
}
