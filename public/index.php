<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/core/bootstrap.php';

use App\Controllers\PublicHomeController;

// Public Front Controller — หน้าแรกของเว็บไซต์ (Home)
(new PublicHomeController())->index();
