<?php

declare(strict_types=1);

require_once dirname(__DIR__, 3) . '/app/core/bootstrap.php';

use App\Controllers\ActivityLogController;

(new ActivityLogController())->index();
