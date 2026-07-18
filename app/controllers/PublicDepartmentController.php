<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\DepartmentModel;

// Controller หน้า Public สำหรับ Departments (Public Website Stage 2.5)
// Reuse App\Models\DepartmentModel เดิมทั้งหมด (ตัวเดียวกับฝั่ง Admin) - ไม่สร้าง Model ซ้ำ ไม่แก้ไข Model
// ทุก Query บังคับ status='Active' เท่านั้น (DepartmentModel::find()/paginate() กรอง deleted_at IS NULL ให้อยู่แล้วเป็นค่าพื้นฐาน)
// หมายเหตุ: DepartmentModel ไม่มีคอลัมน์รูปภาพ และไม่มีความสัมพันธ์กับ Employee (ไม่มี department_id ใน employee)
// จึงไม่มี Contact Box/Related Links/รายชื่อบุคลากรใน Detail Page (Known Limitation - ไม่ใช้ข้อมูลปลอมทดแทน)
class PublicDepartmentController extends BaseController
{
    private const PER_PAGE = 12;

    public function index(): void
    {
        $model = new DepartmentModel();

        $page = max(1, (int) ($_GET['page'] ?? 1));

        $result = $model->paginate(
            ['status' => 'Active'],
            'name',
            'asc',
            $page,
            self::PER_PAGE
        );

        $this->render('public/departments/index', [
            'pageTitle'       => 'แผนก',
            'metaDescription' => 'รวมแผนกและหน่วยงานภายใน ' . APP_NAME,
            'metaKeywords'    => 'แผนก, หน่วยงาน, โครงสร้างองค์กร, ' . APP_NAME,
            'ogType'          => 'website',
            'activeNav'       => 'departments',
            'breadcrumb'      => [
                ['label' => 'หน้าแรก', 'url' => baseUrl('')],
                ['label' => 'แผนก', 'url' => null],
            ],
            'departmentItems' => array_map([$this, 'mapDepartmentToCard'], $result['data']),
            'total'           => $result['total'],
            'totalPages'      => max(1, (int) ceil($result['total'] / self::PER_PAGE)),
            'currentPage'     => $page,
        ]);
    }

    public function detail(int $id): void
    {
        $model      = new DepartmentModel();
        $department = $model->find($id);

        // แสดงเฉพาะแผนกที่ยังเปิดใช้งานอยู่เท่านั้น ใช้หน้า 404 กลางร่วมกับ Public Controller อื่นทั้งหมด
        if ($department === null || $department['status'] !== 'Active') {
            renderNotFound();
            return;
        }

        // ดึงรายการเรียงลำดับเดียวกับหน้า List (Reuse paginate() เดิม ไม่เพิ่ม Query ใหม่) เพื่อหา Prev/Next และ Related
        $listResult   = $model->paginate(['status' => 'Active'], 'name', 'asc', 1, 1000);
        $orderedItems = $listResult['data'];
        $orderedIds   = array_column($orderedItems, 'id');

        $adjacent = findAdjacent($orderedIds, $id);
        $prev     = $this->findDepartmentById($orderedItems, $adjacent['prev']);
        $next     = $this->findDepartmentById($orderedItems, $adjacent['next']);

        $relatedItems = array_slice(
            array_filter($orderedItems, static fn (array $item): bool => (int) $item['id'] !== $id),
            0,
            3
        );

        $this->render('public/departments/detail', [
            'pageTitle'       => $department['name'],
            'metaDescription' => mb_substr((string) ($department['description'] ?? $department['name']), 0, 160),
            'metaKeywords'    => $department['name'] . ', แผนก, หน่วยงาน, ' . APP_NAME,
            'ogType'          => 'article',
            'activeNav'       => 'departments',
            'breadcrumb'      => [
                ['label' => 'หน้าแรก', 'url' => baseUrl('')],
                ['label' => 'แผนก', 'url' => baseUrl('departments/index.php')],
                ['label' => $department['name'], 'url' => null],
            ],
            'department'      => $department,
            'prevItem'        => $prev !== null ? ['url' => baseUrl('departments/detail.php?id=' . $prev['id']), 'title' => $prev['name']] : null,
            'nextItem'        => $next !== null ? ['url' => baseUrl('departments/detail.php?id=' . $next['id']), 'title' => $next['name']] : null,
            'relatedItems'    => array_map([$this, 'mapDepartmentToCard'], $relatedItems),
        ]);
    }

    private function findDepartmentById(array $items, ?int $id): ?array
    {
        if ($id === null) {
            return null;
        }

        foreach ($items as $item) {
            if ((int) $item['id'] === $id) {
                return $item;
            }
        }

        return null;
    }

    private function mapDepartmentToCard(array $item): array
    {
        $description = (string) ($item['description'] ?? '');
        $excerpt     = mb_substr($description, 0, 90) . (mb_strlen($description) > 90 ? '…' : '');

        return [
            'url'     => baseUrl('departments/detail.php?id=' . $item['id']),
            'image'   => null,
            'icon'    => 'department',
            'title'   => $item['name'],
            'excerpt' => $excerpt,
        ];
    }
}
