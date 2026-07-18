<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Middleware\AuthMiddleware;
use App\Models\ActivityLogModel;
use App\Models\DashboardModel;

class DashboardController extends BaseController
{
    public function index(): void
    {
        // ต้อง Login และผ่าน Role Authorization ก่อนเสมอ (Admin, Editor, Staff เข้าดู Dashboard ได้ทุก Role)
        AuthMiddleware::requireRole(['Admin', 'Editor', 'Staff']);

        $model = new DashboardModel();

        $stats        = $model->getModuleCounts();
        $recentLogins = $model->getRecentLogins();

        // กิจกรรมล่าสุด/กราฟสถิติดึงจาก activity_logs ซึ่งมี Permission module `activity_log` action `view`
        // เฉพาะ Admin เท่านั้น (ตาม Phase 11) - ต้องเช็คสิทธิ์ก่อนเสมอ ห้ามให้ Editor/Staff เห็นข้อมูล Log
        // ผ่าน Dashboard เป็นช่องทางอ้อม มิฉะนั้นจะเป็นการข้าม Permission ที่ตั้งใจไว้
        $recentActivity = [];
        $dailyCounts    = [];

        if (can('activity_log', 'view')) {
            $logModel        = new ActivityLogModel();
            $recentActivity  = $logModel->paginate([], 'created_at', 'desc', 1, 6)['data'];
            $dailyCounts     = $logModel->getDailyCounts(7);
        }

        $this->render('admin/dashboard', [
            'stats'          => $stats,
            'recentLogins'   => $recentLogins,
            'recentActivity' => $recentActivity,
            'dailyCounts'    => $dailyCounts,
        ]);
    }
}
