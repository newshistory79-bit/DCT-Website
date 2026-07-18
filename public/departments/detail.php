<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/core/bootstrap.php';

use App\Controllers\PublicDepartmentController;

(new PublicDepartmentController())->detail((int) ($_GET['id'] ?? 0));
