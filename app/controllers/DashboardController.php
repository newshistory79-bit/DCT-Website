<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Middleware\AuthMiddleware;
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

        $this->render('admin/dashboard', [
            'stats'        => $stats,
            'recentLogins' => $recentLogins,
        ]);
    }
}
