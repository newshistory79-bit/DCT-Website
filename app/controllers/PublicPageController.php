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
            'pageTitle'       => 'แผนผังเว็บไซต์',
            'metaDescription' => 'แผนผังเว็บไซต์ ' . APP_NAME . ' รวมลิงก์ทุกหมวดหมู่ของเว็บไซต์',
            'metaKeywords'    => 'แผนผังเว็บไซต์, ' . APP_NAME,
            'ogType'          => 'website',
            'activeNav'       => '',
        ]);
    }
}
