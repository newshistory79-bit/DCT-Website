<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\DocumentModel;

// Controller หน้า Public สำหรับ Documents (Public Website Stage 2.3)
// Reuse App\Models\DocumentModel เดิมทั้งหมด (ตัวเดียวกับฝั่ง Admin) - ไม่สร้าง Model ซ้ำ
// ทุก Query บังคับ status='Published' เท่านั้น - ไม่มี Detail Page (ดาวน์โหลดตรงจากหน้า List)
class PublicDocumentController extends BaseController
{
    private const PER_PAGE = 12;

    public function index(): void
    {
        $model = new DocumentModel();

        $page = max(1, (int) ($_GET['page'] ?? 1));

        $result = $model->paginate(
            ['status' => 'Published'],
            'created_at',
            'desc',
            $page,
            self::PER_PAGE
        );

        $this->render('public/documents/index', [
            'pageTitle'       => 'ນິຕິກຳ',
            'metaDescription' => 'ລວມເອກະສານ, ແບບຟອມ ແລະ ໄຟລ໌ເຜີຍແຜ່ຈາກ ' . APP_NAME,
            'metaKeywords'    => 'ເອກະສານ, ດາວໂຫລດ, ແບບຟອມ, ' . APP_NAME,
            'ogType'          => 'website',
            'activeNav'       => 'documents',
            'breadcrumb'      => [
                ['label' => 'ຫນ້າຫຼັກ', 'url' => baseUrl('')],
                ['label' => 'ນິຕິກຳ', 'url' => null],
            ],
            'documentItems'   => array_map([$this, 'mapDocumentToCard'], $result['data']),
            'total'           => $result['total'],
            'totalPages'      => max(1, (int) ceil($result['total'] / self::PER_PAGE)),
            'currentPage'     => $page,
        ]);
    }

    private function mapDocumentToCard(array $item): array
    {
        $fileName = (string) ($item['file_name'] ?? '');
        $exists   = $fileName !== '' && is_file(self::uploadDirectory() . '/' . $fileName);

        return [
            'title'        => $item['title'],
            'description'  => $item['description'],
            'extension'    => (string) ($item['file_extension'] ?? ''),
            'sizeLabel'    => formatFileSize($item['file_size'] !== null ? (int) $item['file_size'] : null),
            'dateLabel'    => $this->formatPublishedDate((string) ($item['created_at'] ?? '')),
            'downloadUrl'  => $exists ? uploadUrl('documents/' . $fileName) : null,
            'downloadName' => (string) ($item['original_file_name'] ?? $item['title']),
        ];
    }

    private function formatPublishedDate(string $date): ?string
    {
        return formatDateNumeric($date);
    }

    private static function uploadDirectory(): string
    {
        return ROOT_PATH . '/uploads/documents';
    }
}
