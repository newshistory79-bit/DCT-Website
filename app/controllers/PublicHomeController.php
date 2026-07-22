<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\ActivityModel;
use App\Models\DepartmentModel;
use App\Models\DocumentModel;
use App\Models\EmployeeModel;
use App\Models\NewsModel;

class PublicHomeController extends BaseController
{
    private const LATEST_NEWS_LIMIT = 4;

    public function index(): void
    {
        $newsModel = new NewsModel();

        $latestNews = $newsModel->paginate(
            ['status' => 'Published'],
            'created_at',
            'desc',
            1,
            self::LATEST_NEWS_LIMIT
        )['data'];

        $this->render('public/home', [
            'pageTitle'       => '',
            'metaDescription' => 'เว็บไซต์ทางการของ ' . APP_NAME . ' — ข่าวสาร แผนก บุคลากร เอกสารดาวน์โหลด และกิจกรรมของหน่วยงาน',
            'metaKeywords'    => APP_NAME . ', เทคโนโลยีดิจิทัล, ข่าวประชาสัมพันธ์, กิจกรรม, สะหวันนะเขต, DTC Savannakhet',
            'ogType'          => 'website',
            'activeNav'       => 'home',
            'latestNews'      => $latestNews,
            'stats'           => $this->getStats($newsModel),
        ]);
    }

    // นับจำนวนข้อมูลจริงที่เผยแพร่แล้วของแต่ละโมดูล (Reuse paginate() เดิมของแต่ละ Model, perPage=1 เอาแค่ total)
    // ไม่มีการนับ "ผู้เข้าชมเว็บไซต์" เพราะระบบยังไม่มีตาราง Visitor Tracking (ห้ามใช้ Dummy Data)
    private function getStats(NewsModel $newsModel): array
    {
        $newsTotal = $newsModel->paginate(['status' => 'Published'], 'id', 'asc', 1, 1)['total'];

        $departmentsTotal = (new DepartmentModel())->paginate(['status' => 'Active'], 'id', 'asc', 1, 1)['total'];
        $employeesTotal   = (new EmployeeModel())->paginate([], 'id', 'asc', 1, 1)['total'];
        $activitiesTotal  = (new ActivityModel())->paginate(['status' => 'Published'], 'id', 'asc', 1, 1)['total'];
        $documentsTotal   = (new DocumentModel())->paginate(['status' => 'Published'], 'id', 'asc', 1, 1)['total'];

        return [
            ['icon' => 'news', 'value' => $newsTotal, 'label' => 'ລາຍການຂ່າວສານ'],
            ['icon' => 'department', 'value' => $departmentsTotal, 'label' => 'ພະແນກ'],
            ['icon' => 'employee', 'value' => $employeesTotal, 'label' => 'ພະນັກງານ'],
            ['icon' => 'activity', 'value' => $activitiesTotal, 'label' => 'ກິດຈະກຳໂຄງການ'],
            ['icon' => 'download', 'value' => $documentsTotal, 'label' => 'ເອກະສານເຜີຍແຜ່'],
        ];
    }
}
