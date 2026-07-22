<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;

class PublicPageController extends BaseController
{
    // แผนผังเว็บไซต์ (Static Links รวมทุกหมวดของเว็บไซต์ - ไม่มี Database เกี่ยวข้อง)
    public function sitemap(): void
    {
        $this->render('public/sitemap', [
            'pageTitle'       => 'ແຜນຜັງເວັບໄຊທ໌',
            'metaDescription' => 'ແຜນຜັງເວັບໄຊທ໌ ' . APP_NAME . ' ລວມລິ້ງທຸກໝວດໝູ່ຂອງເວັບໄຊທ໌',
            'metaKeywords'    => 'ແຜນຜັງເວັບໄຊທ໌, ' . APP_NAME,
            'ogType'          => 'website',
            'activeNav'       => '',
        ]);
    }

    // ພາລະບົດບາດ ແລະ ໂຄງຮ່າງການຈັດຕັ້ງ (Static Content - ไม่มี Database เกี่ยวข้อง)
    // ໂຄງຮ່າງການຈັດຕັ້ງ ใช้เป็นรูปภาพ, ພາລະບົດບາດ ใช้เป็นไฟล์ PDF ที่ Admin นำไปวางเองที่
    // public/assets/images/org-structure.png และ public/assets/documents/roles-and-duties.pdf
    // (ยังไม่มี Field ในระบบสำหรับ 2 รายการนี้ - ถ้าไม่พบไฟล์จะแสดง Empty State แทน)
    public function about(): void
    {
        $orgChartPath = ROOT_PATH . '/public/assets/images/org-structure.png';
        $rolesPdfPath = ROOT_PATH . '/public/assets/documents/roles-and-duties.pdf';

        $this->render('public/about', [
            'pageTitle'       => 'ພາລະບົດບາດຂອງພະແນກ',
            'metaDescription' => 'ພາລະບົດບາດ ແລະ ໂຄງຮ່າງການຈັດຕັ້ງ ຂອງ ' . APP_NAME,
            'metaKeywords'    => 'ພາລະບົດບາດ, ໂຄງຮ່າງການຈັດຕັ້ງ, ' . APP_NAME,
            'ogType'          => 'website',
            'activeNav'       => 'about',
            'breadcrumb'      => [
                ['label' => 'ຫນ້າຫຼັກ', 'url' => baseUrl('')],
                ['label' => 'ພາລະບົດບາດຂອງພະແນກ', 'url' => null],
            ],
            'orgChartImage'   => is_file($orgChartPath) ? baseUrl('assets/images/org-structure.png') : null,
            'rolesPdfUrl'     => is_file($rolesPdfPath) ? baseUrl('assets/documents/roles-and-duties.pdf') : null,
        ]);
    }
}
