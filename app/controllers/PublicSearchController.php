<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\ActivityModel;
use App\Models\DepartmentModel;
use App\Models\DocumentModel;
use App\Models\EmployeeModel;
use App\Models\NewsModel;

// Controller หน้า Public สำหรับ Global Search (Public Website Stage 2.7)
// Reuse Model เดิมทั้งหมด (ตัวเดียวกับฝั่ง Admin/โมดูลอื่น) - ไม่สร้าง Model ใหม่ ไม่แก้ไข Model ใดๆ
// ทุก Query บังคับ Published/Active เท่านั้น (paginate() ของทุก Model กรอง deleted_at IS NULL ให้อยู่แล้วเป็นค่าพื้นฐาน)
// Pagination แยกอิสระต่อ Section ผ่าน $pageParam ของ renderPagination() (เช่น news_page, documents_page)
// เพื่อไม่ให้การเปลี่ยนหน้าของ Section หนึ่งไปรีเซ็ตหน้าของ Section อื่น - คงค่า q ไว้เสมอทุก Section
class PublicSearchController extends BaseController
{
    private const PER_SECTION       = 6;
    private const MAX_KEYWORD_LENGTH = 150;

    // ถ้า Keyword ตรงกับชื่อหมวดหมู่ (หรือคำพ้องความหมาย) ของ Section ใดพอดี ให้ถือว่าผู้ใช้ต้องการดูรายการทั้งหมด
    // ของหมวดนั้น แทนการค้นหาข้อความตรงตัว (เช่น พิมพ์ "ກິດຈະກຳ" แล้วเจอกิจกรรมทั้งหมด ไม่ใช่แค่รายการที่มีคำนี้ในเนื้อหา)
    private const CATEGORY_ALIASES = [
        'activities'  => ['ກິດຈະກຳ', 'ກິດຈະກຳໂຄງການ', 'ໂຄງການ'],
        'news'        => ['ຂ່າວສານ', 'ຂ່າວ', 'ຂ່າວປະຊາສຳພັນ'],
        'documents'   => ['ນິຕິກຳ', 'ເອກະສານ'],
        'departments' => ['ພະແນກ'],
        'employees'   => ['ພະນັກງານ', 'ບຸກຄະລາກອນ'],
    ];

    public function index(): void
    {
        $keyword = $this->normalizeKeyword((string) ($_GET['q'] ?? ''));

        $sections = [];

        if ($keyword !== '') {
            $sections = [
                'activities'  => $this->searchActivities($keyword),
                'news'        => $this->searchNews($keyword),
                'documents'   => $this->searchDocuments($keyword),
                'departments' => $this->searchDepartments($keyword),
                'employees'   => $this->searchEmployees($keyword),
            ];

            // ตัด Section ที่ไม่มีผลลัพธ์ออก (ข้อกำหนดข้อ 2 - ไม่ต้องแสดง Section ที่ไม่มีผลลัพธ์)
            $sections = array_filter($sections, static fn (array $section): bool => $section['total'] > 0);
        }

        $totalResults = array_sum(array_column($sections, 'total'));

        $this->render('public/search/index', [
            'pageTitle'       => $keyword !== '' ? 'ຜົນການຄົ້ນຫາ: ' . $keyword : 'ຄົ້ນຫາ',
            'metaDescription' => $keyword !== ''
                ? ($totalResults > 0
                    ? 'ພົບ ' . $totalResults . ' ລາຍການຈາກການຄົ້ນຫາ "' . $keyword . '" ໃນ ' . APP_NAME
                    : 'ບໍ່ພົບຜົນລັບຈາກການຄົ້ນຫາ "' . $keyword . '" ໃນ ' . APP_NAME)
                : 'ຄົ້ນຫາຂໍ້ມູນຂ່າວສານ ກິດຈະກຳ ນິຕິກຳ ແລະ ພະນັກງານຂອງ ' . APP_NAME,
            'metaKeywords'    => 'ຄົ້ນຫາ, ' . $keyword . ', ' . APP_NAME,
            'ogType'          => 'website',
            'activeNav'       => 'search',
            'breadcrumb'      => [
                ['label' => 'ຫນ້າຫຼັກ', 'url' => baseUrl('')],
                ['label' => 'ຄົ້ນຫາ', 'url' => null],
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

    private function isCategoryKeyword(string $section, string $keyword): bool
    {
        return in_array($keyword, self::CATEGORY_ALIASES[$section] ?? [], true);
    }

    // คง Query ของทุก Section อื่น + q ไว้เสมอ เวลาสร้างลิงก์ Pagination ของ Section หนึ่งๆ (ไม่รีเซ็ตหน้าของ Section อื่น)
    private function extraQueryFor(string $keyword, string $excludePageParam): array
    {
        $query = ['q' => $keyword];

        foreach (['activities_page', 'news_page', 'documents_page', 'departments_page', 'employees_page'] as $param) {
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
        $model  = new ActivityModel();
        $page   = $this->sectionPage('activities_page');
        $filter = ['status' => 'Published'];

        if (!$this->isCategoryKeyword('activities', $keyword)) {
            $filter['keyword'] = $keyword;
        }

        $result = $model->paginate($filter, 'activity_date', 'desc', $page, self::PER_SECTION);

        return $this->buildSection('ກິດຈະກຳ', $result, $page, 'activities_page', $keyword, function (array $item): array {
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
        $model  = new NewsModel();
        $page   = $this->sectionPage('news_page');
        $filter = ['status' => 'Published'];

        if (!$this->isCategoryKeyword('news', $keyword)) {
            $filter['keyword'] = $keyword;
        }

        $result = $model->paginate($filter, 'created_at', 'desc', $page, self::PER_SECTION);

        return $this->buildSection('ຂ່າວສານ', $result, $page, 'news_page', $keyword, function (array $item): array {
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

    private function searchDocuments(string $keyword): array
    {
        $model  = new DocumentModel();
        $page   = $this->sectionPage('documents_page');
        $filter = ['status' => 'Published'];

        if (!$this->isCategoryKeyword('documents', $keyword)) {
            $filter['keyword'] = $keyword;
        }

        $result = $model->paginate($filter, 'created_at', 'desc', $page, self::PER_SECTION);

        return $this->buildSection('ນິຕິກຳ', $result, $page, 'documents_page', $keyword, function (array $item): array {
            $fileName = (string) ($item['file_name'] ?? '');
            $exists   = $fileName !== '' && is_file(ROOT_PATH . '/uploads/documents/' . $fileName);

            return [
                'title'        => $item['title'],
                'description'  => $item['description'],
                'extension'    => (string) ($item['file_extension'] ?? ''),
                'sizeLabel'    => formatFileSize($item['file_size'] !== null ? (int) $item['file_size'] : null),
                'dateLabel'    => formatDateNumeric((string) ($item['created_at'] ?? '')),
                'downloadUrl'  => $exists ? uploadUrl('documents/' . $fileName) : null,
                'downloadName' => (string) ($item['original_file_name'] ?? $item['title']),
            ];
        }, true);
    }

    private function searchDepartments(string $keyword): array
    {
        $model  = new DepartmentModel();
        $page   = $this->sectionPage('departments_page');
        $filter = ['status' => 'Active'];

        if (!$this->isCategoryKeyword('departments', $keyword)) {
            $filter['keyword'] = $keyword;
        }

        $result = $model->paginate($filter, 'name', 'asc', $page, self::PER_SECTION);

        return $this->buildSection('ພະແນກ', $result, $page, 'departments_page', $keyword, function (array $item): array {
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
        $model  = new EmployeeModel();
        $page   = $this->sectionPage('employees_page');
        $filter = [];

        if (!$this->isCategoryKeyword('employees', $keyword)) {
            $filter['keyword'] = $keyword;
        }

        $result = $model->paginate($filter, 'fname', 'asc', $page, self::PER_SECTION);

        return $this->buildSection('ພະນັກງານ', $result, $page, 'employees_page', $keyword, function (array $item): array {
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
