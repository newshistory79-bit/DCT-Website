<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\LegislationModel;

// Controller หน้า Public สำหรับ Legislation (Public Website Stage 2.2)
// Reuse App\Models\LegislationModel เดิมทั้งหมด (ตัวเดียวกับฝั่ง Admin) - ไม่สร้าง Model ซ้ำ
// ทุก Query บังคับ status='Published' เท่านั้น (LegislationModel::find()/paginate() กรอง deleted_at IS NULL ให้อยู่แล้วเป็นค่าพื้นฐาน)
class PublicLegislationController extends BaseController
{
    private const PER_PAGE = 9;

    public function index(): void
    {
        $model = new LegislationModel();

        $page = max(1, (int) ($_GET['page'] ?? 1));

        $result = $model->paginate(
            ['status' => 'Published'],
            'created_at',
            'desc',
            $page,
            self::PER_PAGE
        );

        $this->render('public/legislation/index', [
            'pageTitle'         => 'กฎหมาย/ระเบียบ',
            'metaDescription'   => 'กฎหมาย ระเบียบ และประกาศที่เกี่ยวข้องจาก ' . APP_NAME,
            'metaKeywords'      => 'กฎหมาย, ระเบียบ, ประกาศ, ' . APP_NAME,
            'ogType'            => 'website',
            'activeNav'         => 'legislation',
            'breadcrumb'        => [
                ['label' => 'หน้าแรก', 'url' => baseUrl('')],
                ['label' => 'กฎหมาย/ระเบียบ', 'url' => null],
            ],
            'legislationItems'  => $result['data'],
            'total'             => $result['total'],
            'totalPages'        => max(1, (int) ceil($result['total'] / self::PER_PAGE)),
            'currentPage'       => $page,
        ]);
    }

    public function detail(int $id): void
    {
        $model       = new LegislationModel();
        $legislation = $model->find($id);

        // แสดงเฉพาะรายการที่เผยแพร่แล้วเท่านั้น ใช้หน้า 404 กลางร่วมกับ Public Controller อื่นทั้งหมด
        if ($legislation === null || $legislation['status'] !== 'Published') {
            renderNotFound();
            return;
        }

        // ดึงรายการ ID เรียงลำดับเดียวกับหน้า List (Reuse paginate() เดิม ไม่เพิ่ม Query ใหม่) เพื่อหา Prev/Next และ Related
        $listResult   = $model->paginate(['status' => 'Published'], 'created_at', 'desc', 1, 1000);
        $orderedItems = $listResult['data'];
        $orderedIds   = array_column($orderedItems, 'ID');

        $adjacent = findAdjacent($orderedIds, $id);
        $prev     = $this->findLegislationById($orderedItems, $adjacent['prev']);
        $next     = $this->findLegislationById($orderedItems, $adjacent['next']);

        $relatedItems = array_slice(
            array_filter($orderedItems, static fn (array $item): bool => (int) $item['ID'] !== $id),
            0,
            3
        );

        $this->render('public/legislation/detail', [
            'pageTitle'       => $legislation['title'],
            'metaDescription' => mb_substr((string) ($legislation['detail'] ?? $legislation['title']), 0, 160),
            'metaKeywords'    => $legislation['title'] . ', กฎหมาย, ระเบียบ, ' . APP_NAME,
            'ogType'          => 'article',
            'activeNav'       => 'legislation',
            'breadcrumb'      => [
                ['label' => 'หน้าแรก', 'url' => baseUrl('')],
                ['label' => 'กฎหมาย/ระเบียบ', 'url' => baseUrl('legislation/index.php')],
                ['label' => $legislation['title'], 'url' => null],
            ],
            'legislation'     => $legislation,
            'prevItem'        => $prev !== null ? ['url' => baseUrl('legislation/detail.php?id=' . $prev['ID']), 'title' => $prev['title']] : null,
            'nextItem'        => $next !== null ? ['url' => baseUrl('legislation/detail.php?id=' . $next['ID']), 'title' => $next['title']] : null,
            'relatedItems'    => array_map([$this, 'mapLegislationToCard'], $relatedItems),
        ]);
    }

    private function findLegislationById(array $items, ?int $id): ?array
    {
        if ($id === null) {
            return null;
        }

        foreach ($items as $item) {
            if ((int) $item['ID'] === $id) {
                return $item;
            }
        }

        return null;
    }

    private function mapLegislationToCard(array $item): array
    {
        $detail  = (string) ($item['detail'] ?? '');
        $excerpt = mb_substr($detail, 0, 90) . (mb_strlen($detail) > 90 ? '…' : '');

        return [
            'url'       => baseUrl('legislation/detail.php?id=' . $item['ID']),
            'image'     => null,
            'icon'      => 'news',
            'dateBadge' => thaiDateParts((string) ($item['effective_date'] ?? $item['created_at'])),
            'title'     => $item['title'],
            'excerpt'   => $excerpt,
        ];
    }
}
