<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\NewsModel;

// Controller หน้า Public สำหรับ News (Public Website Stage 2.1)
// Reuse App\Models\NewsModel เดิมทั้งหมด (ตัวเดียวกับฝั่ง Admin) - ไม่สร้าง Model ซ้ำ
// ทุก Query บังคับ status='Published' เท่านั้น (NewsModel::find()/paginate() กรอง deleted_at IS NULL ให้อยู่แล้วเป็นค่าพื้นฐาน)
class PublicNewsController extends BaseController
{
    private const PER_PAGE = 9;

    public function index(): void
    {
        $model = new NewsModel();

        $page = max(1, (int) ($_GET['page'] ?? 1));

        $result = $model->paginate(
            ['status' => 'Published'],
            'created_at',
            'desc',
            $page,
            self::PER_PAGE
        );

        $this->render('public/news/index', [
            'pageTitle'       => 'ຂ່າວສານ',
            'metaDescription' => 'ຂ່າວສານ ແລະ ປະກາດຈາກ ' . APP_NAME,
            'metaKeywords'    => 'ຂ່າວສານ, ປະກາດ, ' . APP_NAME,
            'ogType'          => 'website',
            'activeNav'       => 'news',
            'breadcrumb'      => [
                ['label' => 'ຫນ້າຫຼັກ', 'url' => baseUrl('')],
                ['label' => 'ຂ່າວສານ', 'url' => null],
            ],
            'newsItems'       => $result['data'],
            'total'           => $result['total'],
            'totalPages'      => max(1, (int) ceil($result['total'] / self::PER_PAGE)),
            'currentPage'     => $page,
        ]);
    }

    public function detail(int $id): void
    {
        $model = new NewsModel();
        $news  = $model->find($id);

        // แสดงเฉพาะข่าวที่เผยแพร่แล้วเท่านั้น ใช้หน้า 404 กลางร่วมกับ Public Controller อื่นทั้งหมด
        if ($news === null || $news['status'] !== 'Published') {
            renderNotFound();
            return;
        }

        // ดึงรายการ ID เรียงลำดับเดียวกับหน้า List (Reuse paginate() เดิม ไม่เพิ่ม Query ใหม่) เพื่อหา Prev/Next และ Related
        $listResult   = $model->paginate(['status' => 'Published'], 'created_at', 'desc', 1, 1000);
        $orderedItems = $listResult['data'];
        $orderedIds   = array_column($orderedItems, 'ID');

        $adjacent = findAdjacent($orderedIds, $id);
        $prev     = $this->findNewsById($orderedItems, $adjacent['prev']);
        $next     = $this->findNewsById($orderedItems, $adjacent['next']);

        $relatedItems = array_slice(
            array_filter($orderedItems, static fn (array $item): bool => (int) $item['ID'] !== $id),
            0,
            3
        );

        $this->render('public/news/detail', [
            'pageTitle'       => $news['title'],
            'metaDescription' => mb_substr((string) ($news['detail'] ?? $news['title']), 0, 160),
            'metaKeywords'    => $news['title'] . ', ຂ່າວສານ, ' . APP_NAME,
            'ogType'          => 'article',
            'ogImage'         => !empty($news['image']) ? uploadUrl('news/' . $news['image']) : null,
            'activeNav'       => 'news',
            'breadcrumb'      => [
                ['label' => 'ຫນ້າຫຼັກ', 'url' => baseUrl('')],
                ['label' => 'ຂ່າວສານ', 'url' => baseUrl('news/index.php')],
                ['label' => $news['title'], 'url' => null],
            ],
            'news'            => $news,
            'prevItem'        => $prev !== null ? ['url' => baseUrl('news/detail.php?id=' . $prev['ID']), 'title' => $prev['title']] : null,
            'nextItem'        => $next !== null ? ['url' => baseUrl('news/detail.php?id=' . $next['ID']), 'title' => $next['title']] : null,
            'relatedItems'    => array_map([$this, 'mapNewsToCard'], $relatedItems),
        ]);
    }

    private function findNewsById(array $items, ?int $id): ?array
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

    private function mapNewsToCard(array $item): array
    {
        $detail  = (string) ($item['detail'] ?? '');
        $excerpt = mb_substr($detail, 0, 90) . (mb_strlen($detail) > 90 ? '…' : '');

        return [
            'url'         => baseUrl('news/detail.php?id=' . $item['ID']),
            'image'       => !empty($item['image']) ? uploadUrl('news/' . $item['image']) : null,
            'icon'        => 'news',
            'dateBadge'   => thaiDateParts((string) ($item['activity_date'] ?? $item['created_at'])),
            'title'       => $item['title'],
            'excerpt'     => $excerpt,
            'actionLabel' => 'ອ່ານຕໍ່',
        ];
    }
}
