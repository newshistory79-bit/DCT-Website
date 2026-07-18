<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\ActivityModel;
use App\Models\DepartmentModel;
use App\Models\DocumentModel;
use App\Models\EmployeeModel;
use App\Models\GalleryModel;
use App\Models\LegislationModel;
use App\Models\NewsModel;

// Controller หน้า Public สำหรับ Global Search (Public Website Stage 2.7)
// Reuse Model เดิมทั้ง 7 ตัวทั้งหมด (ตัวเดียวกับฝั่ง Admin/โมดูลอื่น) - ไม่สร้าง Model ใหม่ ไม่แก้ไข Model ใดๆ
// ทุก Query บังคับ Published/Active เท่านั้น (paginate() ของทุก Model กรอง deleted_at IS NULL ให้อยู่แล้วเป็นค่าพื้นฐาน)
// Pagination แยกอิสระต่อ Section ผ่าน $pageParam ของ renderPagination() (เช่น news_page, gallery_page)
// เพื่อไม่ให้การเปลี่ยนหน้าของ Section หนึ่งไปรีเซ็ตหน้าของ Section อื่น - คงค่า q ไว้เสมอทุก Section
class PublicSearchController extends BaseController
{
    private const PER_SECTION       = 6;
    private const MAX_KEYWORD_LENGTH = 150;

    public function index(): void
    {
        $keyword = $this->normalizeKeyword((string) ($_GET['q'] ?? ''));

        $sections = [];

        if ($keyword !== '') {
            $sections = [
                'activities'  => $this->searchActivities($keyword),
                'news'        => $this->searchNews($keyword),
                'legislation' => $this->searchLegislation($keyword),
                'documents'   => $this->searchDocuments($keyword),
                'gallery'     => $this->searchGallery($keyword),
                'departments' => $this->searchDepartments($keyword),
                'employees'   => $this->searchEmployees($keyword),
            ];

            // ตัด Section ที่ไม่มีผลลัพธ์ออก (ข้อกำหนดข้อ 2 - ไม่ต้องแสดง Section ที่ไม่มีผลลัพธ์)
            $sections = array_filter($sections, static fn (array $section): bool => $section['total'] > 0);
        }

        $totalResults = array_sum(array_column($sections, 'total'));

        $this->render('public/search/index', [
            'pageTitle'       => $keyword !== '' ? 'ผลการค้นหา: ' . $keyword : 'ค้นหา',
            'metaDescription' => $keyword !== ''
                ? ($totalResults > 0
                    ? 'พบ ' . $totalResults . ' รายการจากการค้นหา "' . $keyword . '" ใน ' . APP_NAME
                    : 'ไม่พบผลลัพธ์จากการค้นหา "' . $keyword . '" ใน ' . APP_NAME)
                : 'ค้นหาข้อมูลข่าวสาร กิจกรรม เอกสาร และบุคลากรของ ' . APP_NAME,
            'metaKeywords'    => 'ค้นหา, ' . $keyword . ', ' . APP_NAME,
            'ogType'          => 'website',
            'activeNav'       => 'search',
            'breadcrumb'      => [
                ['label' => 'หน้าแรก', 'url' => baseUrl('')],
                ['label' => 'ค้นหา', 'url' => null],
            ],
            'keyword'         => $keyword,
            'sections'        => $sections,
            'totalResults'    => $totalResults,
        ]);
    }

    // ตัดช่องว่างหัวท้าย + ยุบช่องว่างซ้ำให้เหลือช่องเดียว (รองรับกรณี Space หลายตัว) + จำกัดความยาวป้องกัน Keyword ยาวผิดปกติ
    private function normalizeKeyword(string $raw): string
    {
        $normalized = trim(preg_replace('/\s+/u', ' ', $raw) ?? '');

        return mb_substr($normalized, 0, self::MAX_KEYWORD_LENGTH);
    }

    private function sectionPage(string $pageParam): int
    {
        return max(1, (int) ($_GET[$pageParam] ?? 1));
    }

    // คง Query ของทุก Section อื่น + q ไว้เสมอ เวลาสร้างลิงก์ Pagination ของ Section หนึ่งๆ (ไม่รีเซ็ตหน้าของ Section อื่น)
    private function extraQueryFor(string $keyword, string $excludePageParam): array
    {
        $query = ['q' => $keyword];

        foreach (['activities_page', 'news_page', 'legislation_page', 'documents_page', 'gallery_page', 'departments_page', 'employees_page'] as $param) {
            if ($param === $excludePageParam) {
                continue;
            }

            if (!empty($_GET[$param])) {
                $query[$param] = max(1, (int) $_GET[$param]);
            }
        }

        return $query;
    }

    private function searchActivities(string $keyword): array
    {
        $model = new ActivityModel();
        $page  = $this->sectionPage('activities_page');

        $result = $model->paginate(['status' => 'Published', 'keyword' => $keyword], 'activity_date', 'desc', $page, self::PER_SECTION);

        return $this->buildSection('กิจกรรม', $result, $page, 'activities_page', $keyword, function (array $item): array {
            $detail  = (string) ($item['description'] ?? '');
            $excerpt = mb_substr($detail, 0, 90) . (mb_strlen($detail) > 90 ? '…' : '');

            return [
                'url'       => baseUrl('activities/detail.php?id=' . $item['id']),
                'image'     => !empty($item['image']) ? uploadUrl('activities/' . $item['image']) : null,
                'icon'      => 'activity',
                'dateBadge' => thaiDateParts((string) ($item['activity_date'] ?? $item['created_at'])),
                'title'     => $item['title'],
                'excerpt'   => $excerpt,
            ];
        });
    }

    private function searchNews(string $keyword): array
    {
        $model = new NewsModel();
        $page  = $this->sectionPage('news_page');

        $result = $model->paginate(['status' => 'Published', 'keyword' => $keyword], 'created_at', 'desc', $page, self::PER_SECTION);

        return $this->buildSection('ข่าวประชาสัมพันธ์', $result, $page, 'news_page', $keyword, function (array $item): array {
            $detail  = (string) ($item['detail'] ?? '');
            $excerpt = mb_substr($detail, 0, 90) . (mb_strlen($detail) > 90 ? '…' : '');

            return [
                'url'       => baseUrl('news/detail.php?id=' . $item['ID']),
                'image'     => !empty($item['image']) ? uploadUrl('news/' . $item['image']) : null,
                'icon'      => 'news',
                'dateBadge' => thaiDateParts((string) ($item['activity_date'] ?? $item['created_at'])),
                'title'     => $item['title'],
                'excerpt'   => $excerpt,
            ];
        });
    }

    private function searchLegislation(string $keyword): array
    {
        $model = new LegislationModel();
        $page  = $this->sectionPage('legislation_page');

        $result = $model->paginate(['status' => 'Published', 'keyword' => $keyword], 'created_at', 'desc', $page, self::PER_SECTION);

        return $this->buildSection('กฎหมาย/ระเบียบ', $result, $page, 'legislation_page', $keyword, function (array $item): array {
            $detail         = (string) ($item['detail'] ?? '');
            $excerpt        = mb_substr($detail, 0, 90) . (mb_strlen($detail) > 90 ? '…' : '');
            $documentNumber = (string) ($item['document_number'] ?? '');

            if ($documentNumber !== '') {
                $excerpt = 'เลขที่ประกาศ ' . $documentNumber . ($excerpt !== '' ? ' — ' . $excerpt : '');
            }

            return [
                'url'       => baseUrl('legislation/detail.php?id=' . $item['ID']),
                'image'     => null,
                'icon'      => 'news',
                'dateBadge' => thaiDateParts((string) ($item['effective_date'] ?? $item['created_at'])),
                'title'     => $item['title'],
                'excerpt'   => $excerpt,
            ];
        });
    }

    private function searchDocuments(string $keyword): array
    {
        $model = new DocumentModel();
        $page  = $this->sectionPage('documents_page');

        $result = $model->paginate(['status' => 'Published', 'keyword' => $keyword], 'created_at', 'desc', $page, self::PER_SECTION);

        return $this->buildSection('เอกสาร', $result, $page, 'documents_page', $keyword, function (array $item): array {
            $fileName = (string) ($item['file_name'] ?? '');
            $exists   = $fileName !== '' && is_file(ROOT_PATH . '/uploads/documents/' . $fileName);
            $parts    = thaiDateParts((string) ($item['created_at'] ?? ''));

            return [
                'title'        => $item['title'],
                'description'  => $item['description'],
                'extension'    => (string) ($item['file_extension'] ?? ''),
                'sizeLabel'    => formatFileSize($item['file_size'] !== null ? (int) $item['file_size'] : null),
                'dateLabel'    => $parts !== null ? $parts['day'] . ' ' . $parts['month'] . ' ' . $parts['year'] : null,
                'downloadUrl'  => $exists ? uploadUrl('documents/' . $fileName) : null,
                'downloadName' => (string) ($item['original_file_name'] ?? $item['title']),
            ];
        }, true);
    }

    private function searchGallery(string $keyword): array
    {
        $model = new GalleryModel();
        $page  = $this->sectionPage('gallery_page');

        $result = $model->paginate(['status' => 'Published', 'keyword' => $keyword], 'created_at', 'desc', $page, self::PER_SECTION);

        return $this->buildSection('คลังภาพ', $result, $page, 'gallery_page', $keyword, function (array $item): array {
            $imageFile = (string) ($item['image'] ?? '');
            $exists    = $imageFile !== '' && is_file(ROOT_PATH . '/uploads/gallery/' . $imageFile);
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

            if ($imageUrl !== null) {
                $card['attrs'] = [
                    'data-lightbox-image'       => $imageUrl,
                    'data-lightbox-title'       => $item['title'],
                    'data-lightbox-description' => (string) ($item['description'] ?? ''),
                ];
            }

            return $card;
        });
    }

    private function searchDepartments(string $keyword): array
    {
        $model = new DepartmentModel();
        $page  = $this->sectionPage('departments_page');

        $result = $model->paginate(['status' => 'Active', 'keyword' => $keyword], 'name', 'asc', $page, self::PER_SECTION);

        return $this->buildSection('แผนก', $result, $page, 'departments_page', $keyword, function (array $item): array {
            $description = (string) ($item['description'] ?? '');
            $excerpt     = mb_substr($description, 0, 90) . (mb_strlen($description) > 90 ? '…' : '');

            return [
                'url'     => baseUrl('departments/detail.php?id=' . $item['id']),
                'image'   => null,
                'icon'    => 'department',
                'title'   => $item['name'],
                'excerpt' => $excerpt,
            ];
        });
    }

    // Privacy: ไม่แสดง phone/email/address/birth_date บน Public เหมือนกับหน้า Employees List/Detail (Stage 2.6)
    private function searchEmployees(string $keyword): array
    {
        $model = new EmployeeModel();
        $page  = $this->sectionPage('employees_page');

        $result = $model->paginate(['keyword' => $keyword], 'fname', 'asc', $page, self::PER_SECTION);

        return $this->buildSection('บุคลากร', $result, $page, 'employees_page', $keyword, function (array $item): array {
            return [
                'url'     => baseUrl('employees/detail.php?id=' . $item['ID']),
                'image'   => !empty($item['image']) ? uploadUrl('employees/' . $item['image']) : null,
                'icon'    => 'employee',
                'title'   => trim($item['Fname'] . ' ' . $item['Lname']),
                'excerpt' => $item['position'],
            ];
        });
    }

    private function buildSection(string $label, array $result, int $page, string $pageParam, string $keyword, callable $mapper, bool $isDocument = false): array
    {
        return [
            'label'       => $label,
            'items'       => array_map($mapper, $result['data']),
            'total'       => $result['total'],
            'currentPage' => $page,
            'totalPages'  => max(1, (int) ceil($result['total'] / self::PER_SECTION)),
            'pageParam'   => $pageParam,
            'extraQuery'  => $this->extraQueryFor($keyword, $pageParam),
            'isDocument'  => $isDocument,
        ];
    }
}
