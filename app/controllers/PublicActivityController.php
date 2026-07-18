<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\ActivityModel;

// Controller หน้า Public สำหรับ Activities Module (Phase 13 Stage 4)
// Reuse App\Models\ActivityModel เดิมทั้งหมด (ตัวเดียวกับฝั่ง Admin) - ไม่สร้าง Model ซ้ำ
// ทุก Query บังคับ status='Published' เท่านั้น (ActivityModel::find()/paginate() กรอง deleted_at IS NULL ให้อยู่แล้วเป็นค่าพื้นฐาน)
class PublicActivityController extends BaseController
{
    private const PER_PAGE = 9;

    public function index(): void
    {
        $model = new ActivityModel();

        $page = max(1, (int) ($_GET['page'] ?? 1));

        $result = $model->paginate(
            ['status' => 'Published'],
            'activity_date',
            'desc',
            $page,
            self::PER_PAGE
        );

        $this->render('public/activities/index', [
            'pageTitle'       => 'กิจกรรม',
            'metaDescription' => 'กิจกรรมและโครงการต่างๆ ของ ' . APP_NAME,
            'metaKeywords'    => 'กิจกรรม, โครงการ, ' . APP_NAME,
            'ogType'          => 'website',
            'activeNav'       => 'activities',
            'activities'      => $result['data'],
            'total'           => $result['total'],
            'totalPages'      => max(1, (int) ceil($result['total'] / self::PER_PAGE)),
            'currentPage'     => $page,
        ]);
    }

    public function detail(int $id): void
    {
        $model    = new ActivityModel();
        $activity = $model->find($id);

        // แสดงเฉพาะกิจกรรมที่เผยแพร่แล้วเท่านั้น (find() กรอง Soft Delete ให้แล้ว แต่ยังไม่กรอง Draft)
        // ใช้หน้า 404 กลางร่วมกับ Public Controller อื่นทั้งหมด (renderNotFound() ใน app/helpers/functions.php)
        if ($activity === null || $activity['status'] !== 'Published') {
            renderNotFound();
            return;
        }

        $this->render('public/activities/detail', [
            'pageTitle'       => $activity['title'],
            'metaDescription' => mb_substr((string) ($activity['description'] ?? $activity['title']), 0, 160),
            'metaKeywords'    => $activity['title'] . ', กิจกรรม, ' . APP_NAME,
            'ogType'          => 'article',
            'ogImage'         => !empty($activity['image']) ? uploadUrl('activities/' . $activity['image']) : null,
            'activeNav'       => 'activities',
            'activity'        => $activity,
        ]);
    }
}
