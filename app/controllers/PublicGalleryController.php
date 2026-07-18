<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\GalleryModel;

// Controller หน้า Public สำหรับ Gallery (Public Website Stage 2.4)
// Reuse App\Models\GalleryModel เดิมทั้งหมด (ตัวเดียวกับฝั่ง Admin) - ไม่สร้าง Model ซ้ำ
// ทุก Query บังคับ status='Published' เท่านั้น - ไม่มี Detail Page (แสดงภาพเต็มผ่าน Lightbox แทน)
class PublicGalleryController extends BaseController
{
    private const PER_PAGE = 12;

    public function index(): void
    {
        $model = new GalleryModel();

        $keyword = trim((string) ($_GET['keyword'] ?? ''));
        $page    = max(1, (int) ($_GET['page'] ?? 1));

        $result = $model->paginate(
            ['status' => 'Published', 'keyword' => $keyword],
            'created_at',
            'desc',
            $page,
            self::PER_PAGE
        );

        $this->render('public/gallery/index', [
            'pageTitle'       => 'คลังภาพ',
            'metaDescription' => 'รวมภาพกิจกรรมและผลงานจาก ' . APP_NAME,
            'metaKeywords'    => 'คลังภาพ, รูปภาพ, กิจกรรม, ' . APP_NAME,
            'ogType'          => 'website',
            'activeNav'       => 'gallery',
            'breadcrumb'      => [
                ['label' => 'หน้าแรก', 'url' => baseUrl('')],
                ['label' => 'คลังภาพ', 'url' => null],
            ],
            'galleryItems'    => array_map([$this, 'mapGalleryToCard'], $result['data']),
            'total'           => $result['total'],
            'totalPages'      => max(1, (int) ceil($result['total'] / self::PER_PAGE)),
            'currentPage'     => $page,
            'keyword'         => $keyword,
        ]);
    }

    private function mapGalleryToCard(array $item): array
    {
        $imageFile = (string) ($item['image'] ?? '');
        $exists    = $imageFile !== '' && is_file(self::uploadDirectory() . '/' . $imageFile);
        $imageUrl  = $exists ? uploadUrl('gallery/' . $imageFile) : null;

        $card = [
            'url'         => $imageUrl ?? '#',
            'image'       => $imageUrl,
            'icon'        => 'image',
            'dateBadge'   => thaiDateParts((string) ($item['created_at'] ?? '')),
            'title'       => $item['title'],
            'excerpt'     => $item['description'],
            'actionLabel' => $imageUrl !== null ? 'ดูภาพขนาดเต็ม' : 'ไม่มีรูปภาพ',
        ];

        // มีข้อมูลรูปจริงเท่านั้นถึงจะผูก Lightbox (รูปหาย/ไม่มีรูป = แสดงไอคอนสำรอง ไม่เปิด Lightbox)
        if ($imageUrl !== null) {
            $card['attrs'] = [
                'data-lightbox-image'       => $imageUrl,
                'data-lightbox-title'       => $item['title'],
                'data-lightbox-description' => (string) ($item['description'] ?? ''),
            ];
        }

        return $card;
    }

    private static function uploadDirectory(): string
    {
        return ROOT_PATH . '/uploads/gallery';
    }
}
