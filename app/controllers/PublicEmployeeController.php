<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\EmployeeModel;

// Controller หน้า Public สำหรับ Employees (Public Website Stage 2.6)
// Reuse App\Models\EmployeeModel เดิมทั้งหมด (ตัวเดียวกับฝั่ง Admin) - ไม่สร้าง Model ซ้ำ ไม่แก้ไข Model
// ทุก Query กรอง deleted_at IS NULL อยู่แล้วเป็นค่าพื้นฐาน (ตาราง employee ไม่มีคอลัมน์ Status)
//
// Privacy Decision (ยืนยันกับผู้ใช้แล้วก่อนพัฒนา): ไม่แสดง phone/email บน Public เนื่องจาก Schema
// ไม่มี Field แยกระหว่างข้อมูลติดต่อสาธารณะกับข้อมูลภายใน - ด้วยเหตุผลเดียวกัน จึงไม่แสดง address (ที่อยู่)
// และ birth_date (วันเกิด) ต่อสาธารณะเช่นกัน เพราะเป็นข้อมูลส่วนบุคคลที่ละเอียดอ่อนกว่า phone/email เสียอีก
// และไม่มีประโยชน์ต่อผู้ใช้งาน Directory สาธารณะ - แสดงเฉพาะ ชื่อ-นามสกุล/ตำแหน่ง/รูปภาพ เท่านั้น
// หมายเหตุ: employee ไม่มีคอลัมน์ department_id เชื่อมกับ departments จึงไม่มีชื่อแผนกให้แสดง (ไม่ใช้ข้อมูลปลอมทดแทน)
class PublicEmployeeController extends BaseController
{
    private const PER_PAGE = 12;

    public function index(): void
    {
        $model = new EmployeeModel();

        $page = max(1, (int) ($_GET['page'] ?? 1));

        $result = $model->paginate(
            [],
            'fname',
            'asc',
            $page,
            self::PER_PAGE
        );

        $this->render('public/employees/index', [
            'pageTitle'       => 'ພະນັກງານ',
            'metaDescription' => 'ລາຍຊື່ພະນັກງານຂອງ ' . APP_NAME,
            'metaKeywords'    => 'ພະນັກງານ, ເຈົ້າຫນ້າທີ່, ' . APP_NAME,
            'ogType'          => 'website',
            'activeNav'       => 'employees',
            'breadcrumb'      => [
                ['label' => 'ຫນ້າຫຼັກ', 'url' => baseUrl('')],
                ['label' => 'ພະນັກງານ', 'url' => null],
            ],
            'employeeItems'   => array_map([$this, 'mapEmployeeToCard'], $result['data']),
            'total'           => $result['total'],
            'totalPages'      => max(1, (int) ceil($result['total'] / self::PER_PAGE)),
            'currentPage'     => $page,
        ]);
    }

    public function detail(int $id): void
    {
        $model    = new EmployeeModel();
        $employee = $model->find($id);

        if ($employee === null) {
            renderNotFound();
            return;
        }

        $fullName = trim($employee['Fname'] . ' ' . $employee['Lname']);

        // ดึงรายการเรียงลำดับเดียวกับหน้า List (Reuse paginate() เดิม ไม่เพิ่ม Query ใหม่) เพื่อหา Prev/Next และ Related
        $listResult   = $model->paginate([], 'fname', 'asc', 1, 1000);
        $orderedItems = $listResult['data'];
        $orderedIds   = array_column($orderedItems, 'ID');

        $adjacent = findAdjacent($orderedIds, $id);
        $prev     = $this->findEmployeeById($orderedItems, $adjacent['prev']);
        $next     = $this->findEmployeeById($orderedItems, $adjacent['next']);

        $relatedItems = array_slice(
            array_filter($orderedItems, static fn (array $item): bool => (int) $item['ID'] !== $id),
            0,
            3
        );

        $this->render('public/employees/detail', [
            'pageTitle'       => $fullName,
            'metaDescription' => $fullName . (!empty($employee['position']) ? ' - ' . $employee['position'] : '') . ' - ' . APP_NAME,
            'metaKeywords'    => $fullName . ', ພະນັກງານ, ' . APP_NAME,
            'ogType'          => 'profile',
            'ogImage'         => !empty($employee['image']) ? uploadUrl('employees/' . $employee['image']) : null,
            'activeNav'       => 'employees',
            'breadcrumb'      => [
                ['label' => 'ຫນ້າຫຼັກ', 'url' => baseUrl('')],
                ['label' => 'ພະນັກງານ', 'url' => baseUrl('employees/index.php')],
                ['label' => $fullName, 'url' => null],
            ],
            'employee'        => $employee,
            'fullName'        => $fullName,
            'prevItem'        => $prev !== null ? ['url' => baseUrl('employees/detail.php?id=' . $prev['ID']), 'title' => trim($prev['Fname'] . ' ' . $prev['Lname'])] : null,
            'nextItem'        => $next !== null ? ['url' => baseUrl('employees/detail.php?id=' . $next['ID']), 'title' => trim($next['Fname'] . ' ' . $next['Lname'])] : null,
            'relatedItems'    => array_map([$this, 'mapEmployeeToCard'], $relatedItems),
        ]);
    }

    private function findEmployeeById(array $items, ?int $id): ?array
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

    private function mapEmployeeToCard(array $item): array
    {
        return [
            'url'         => baseUrl('employees/detail.php?id=' . $item['ID']),
            'image'       => !empty($item['image']) ? uploadUrl('employees/' . $item['image']) : null,
            'icon'        => 'employee',
            'title'       => trim($item['Fname'] . ' ' . $item['Lname']),
            'excerpt'     => $item['position'],
            'actionLabel' => 'ລາຍລະອຽດ',
        ];
    }
}
