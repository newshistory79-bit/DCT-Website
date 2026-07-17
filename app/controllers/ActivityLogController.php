<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Middleware\AuthMiddleware;
use App\Models\ActivityLogModel;
use DateTime;

class ActivityLogController extends BaseController
{
    private const MODULE           = 'activity_log';
    private const PER_PAGE_OPTIONS = [10, 25, 50, 100];

    private const MODULE_OPTIONS = [
        'auth', 'departments', 'employees', 'news', 'legislation', 'documents', 'gallery', 'users',
    ];

    private const ACTION_OPTIONS = [
        'create', 'update', 'delete', 'login', 'login_failed', 'logout',
    ];

    // เฉพาะ index() เท่านั้น - ไม่มี store/update/destroy เพราะ Log ถูกสร้างโดยระบบเองผ่าน ActivityLogger
    public function index(): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'view');

        $model = new ActivityLogModel();

        $keyword   = trim((string) ($_GET['keyword'] ?? ''));
        $module    = (string) ($_GET['module'] ?? '');
        $action    = (string) ($_GET['action'] ?? '');
        $dateFrom  = trim((string) ($_GET['date_from'] ?? ''));
        $dateTo    = trim((string) ($_GET['date_to'] ?? ''));
        $sort      = (string) ($_GET['sort'] ?? 'created_at');
        $direction = (string) ($_GET['direction'] ?? 'desc');
        $perPage   = (int) ($_GET['per_page'] ?? 10);
        $page      = max(1, (int) ($_GET['page'] ?? 1));

        if (!in_array($perPage, self::PER_PAGE_OPTIONS, true)) {
            $perPage = 10;
        }

        if (!in_array($module, self::MODULE_OPTIONS, true)) {
            $module = '';
        }

        if (!in_array($action, self::ACTION_OPTIONS, true)) {
            $action = '';
        }

        if ($dateFrom !== '' && !$this->isValidDate($dateFrom)) {
            $dateFrom = '';
        }

        if ($dateTo !== '' && !$this->isValidDate($dateTo)) {
            $dateTo = '';
        }

        $result = $model->paginate(
            [
                'keyword'   => $keyword,
                'module'    => $module,
                'action'    => $action,
                'date_from' => $dateFrom,
                'date_to'   => $dateTo,
            ],
            $sort,
            $direction,
            $page,
            $perPage
        );

        $this->render('admin/activity-log/index', [
            'logs'           => $result['data'],
            'total'          => $result['total'],
            'totalPages'     => max(1, (int) ceil($result['total'] / $perPage)),
            'currentPage'    => $page,
            'perPage'        => $perPage,
            'perPageOptions' => self::PER_PAGE_OPTIONS,
            'keyword'        => $keyword,
            'module'         => $module,
            'action'         => $action,
            'dateFrom'       => $dateFrom,
            'dateTo'         => $dateTo,
            'sort'           => $sort,
            'direction'      => $direction,
            'moduleOptions'  => self::MODULE_OPTIONS,
            'actionOptions'  => self::ACTION_OPTIONS,
        ]);
    }

    private function isValidDate(string $date): bool
    {
        $parsed = DateTime::createFromFormat('Y-m-d', $date);

        return $parsed !== false && $parsed->format('Y-m-d') === $date;
    }
}
