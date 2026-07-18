<?php

declare(strict_types=1);

// ชุดไอคอนกลางของ Public Website (Inline SVG ล้วน ไม่พึ่ง Library/Font ภายนอกตามข้อกำหนดโปรเจกต์)
// คืนค่าเป็น SVG Markup คงที่ (Static Trusted Markup ไม่ใช่ Input จากผู้ใช้ จึงไม่ต้อง Escape)
function icon(string $name, int $size = 24): string
{
    $paths = [
        'phone'      => '<path d="M4 5c0-.6.4-1 1-1h2.6c.5 0 .9.3 1 .8l.8 3.2c.1.4 0 .9-.4 1.2L7.8 10.5a12 12 0 0 0 5.7 5.7l1.3-1.2c.3-.4.8-.5 1.2-.4l3.2.8c.5.1.8.5.8 1V19c0 .6-.4 1-1 1h-1C10.5 20 4 13.5 4 6V5z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" fill="none"/>',
        'mail'       => '<path d="M4 6h16v12H4z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" fill="none"/><path d="m4.5 6.5 7.5 6 7.5-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" fill="none"/>',
        'search'     => '<circle cx="11" cy="11" r="6.5" stroke="currentColor" stroke-width="1.6" fill="none"/><path d="m20 20-4.3-4.3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>',
        'news'       => '<rect x="4" y="4" width="16" height="16" rx="2" stroke="currentColor" stroke-width="1.6" fill="none"/><path d="M8 9h8M8 13h8M8 17h4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>',
        'department' => '<path d="M4 20V9l8-5 8 5v11" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" fill="none"/><path d="M9 20v-6h6v6" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" fill="none"/>',
        'employee'   => '<circle cx="12" cy="8" r="3.4" stroke="currentColor" stroke-width="1.6" fill="none"/><path d="M5 20c1-3.8 4-5.6 7-5.6s6 1.8 7 5.6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" fill="none"/>',
        'activity'   => '<rect x="4" y="5" width="16" height="15" rx="2" stroke="currentColor" stroke-width="1.6" fill="none"/><path d="M4 9.5h16M8 3v3.4M16 3v3.4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><circle cx="9" cy="13.5" r="1.1" fill="currentColor"/><circle cx="13" cy="13.5" r="1.1" fill="currentColor"/><circle cx="9" cy="17" r="1.1" fill="currentColor"/>',
        'download'   => '<path d="M12 4v11" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><path d="m7.5 11.5 4.5 4 4.5-4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" fill="none"/><path d="M5 19h14" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>',
        'contact'    => '<path d="M4 7.5C4 6 5.2 5 6.5 5H8l1.6 4-1.9 1.3a10.5 10.5 0 0 0 5 5l1.3-1.9 4 1.6v1.5c0 1.3-1 2.5-2.5 2.5C9.6 19 4 13.4 4 7.5z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" fill="none"/>',
        'menu'       => '<path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>',
        'close'      => '<path d="m6 6 12 12M18 6 6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>',
        'chevron'    => '<path d="m9 6 6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" fill="none"/>',
        'arrow'      => '<path d="M4 12h15M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" fill="none"/>',
        'image'      => '<rect x="4" y="4" width="16" height="16" rx="2" stroke="currentColor" stroke-width="1.6" fill="none"/><circle cx="9" cy="9.5" r="1.4" fill="currentColor"/><path d="m5 17 4.5-5 3.5 3.8L16 12l3.5 5.5" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" fill="none"/>',
        'building'   => '<path d="M6 20V5.5C6 4.7 6.7 4 7.5 4h9c.8 0 1.5.7 1.5 1.5V20" stroke="currentColor" stroke-width="1.6" fill="none"/><path d="M3 20h18M9 8h1M14 8h1M9 12h1M14 12h1M9 16h1M14 16h1" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>',
        'pin'        => '<path d="M12 21s7-6.6 7-11.5A7 7 0 0 0 5 9.5C5 14.4 12 21 12 21z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" fill="none"/><circle cx="12" cy="9.5" r="2.3" stroke="currentColor" stroke-width="1.6" fill="none"/>',
        'clock'      => '<circle cx="12" cy="12" r="8.5" stroke="currentColor" stroke-width="1.6" fill="none"/><path d="M12 7.5V12l3 2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" fill="none"/>',
        'dashboard'  => '<rect x="4" y="4" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.6" fill="none"/><rect x="13" y="4" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.6" fill="none"/><rect x="4" y="13" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.6" fill="none"/><rect x="13" y="13" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.6" fill="none"/>',
        'users'      => '<circle cx="8.5" cy="8" r="3" stroke="currentColor" stroke-width="1.6" fill="none"/><circle cx="16" cy="9" r="2.4" stroke="currentColor" stroke-width="1.6" fill="none"/><path d="M3.5 19c.7-3.2 3-4.8 5-4.8s4.3 1.6 5 4.8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" fill="none"/><path d="M15 14.5c2 .2 3.7 1.7 4.3 4.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" fill="none"/>',
        'settings'   => '<circle cx="12" cy="12" r="2.8" stroke="currentColor" stroke-width="1.6" fill="none"/><path d="M12 3.5v2.2M12 18.3v2.2M20.5 12h-2.2M5.7 12H3.5M17.7 6.3l-1.6 1.6M7.9 16.1l-1.6 1.6M17.7 17.7l-1.6-1.6M7.9 7.9 6.3 6.3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>',
        'log'        => '<path d="M6 3.5h9l3.5 3.5V20a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V4.5a1 1 0 0 1 1-1z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" fill="none"/><path d="M8.5 11h7M8.5 14.5h7M8.5 18h4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>',
        'logout'     => '<path d="M9 4H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" fill="none"/><path d="M15 8l4 4-4 4M19 12H9" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" fill="none"/>',

        // เพิ่มสำหรับ Admin Panel Design System v2 (Stage DS1) — Entry ใหม่เท่านั้น ไม่แก้ Entry เดิมด้านบน
        'edit'            => '<path d="M4 20l1-4L16 5l3 3L8 19l-4 1z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" fill="none"/><path d="m14 7 3 3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>',
        'trash'           => '<path d="M5 7h14M9 7V5a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2m-9 0 1 12a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1l1-12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" fill="none"/>',
        'plus'            => '<path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>',
        'eye'             => '<path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" fill="none"/><circle cx="12" cy="12" r="2.6" stroke="currentColor" stroke-width="1.6" fill="none"/>',
        'spinner'         => '<circle cx="12" cy="12" r="8.5" stroke="currentColor" stroke-width="1.8" fill="none" opacity="0.25"/><path d="M20.5 12a8.5 8.5 0 0 0-8.5-8.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" fill="none"/>',
        'filter'          => '<path d="M4 5h16M7 12h10M10 19h4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>',
        'sort'            => '<path d="M8 5v14M8 5l-3 3M8 5l3 3M16 19V5M16 19l-3-3M16 19l3-3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" fill="none"/>',
        'alert-triangle'  => '<path d="M12 4 2.5 20h19L12 4z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" fill="none"/><path d="M12 10v4.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><circle cx="12" cy="17.3" r="0.9" fill="currentColor"/>',
        'check'           => '<path d="m5 12.5 4.5 4.5L19 7.5" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" fill="none"/>',
    ];

    $path = $paths[$name] ?? $paths['chevron'];

    return '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">' . $path . '</svg>';
}
