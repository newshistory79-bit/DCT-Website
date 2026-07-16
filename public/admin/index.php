<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/core/bootstrap.php';

use App\Controllers\DashboardController;

// Phase 3: Dashboard จริง - Controller จะตรวจสอบ AuthMiddleware/Role Authorization เอง
(new DashboardController())->index();
