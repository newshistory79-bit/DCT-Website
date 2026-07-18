<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/core/bootstrap.php';

use App\Controllers\PublicEmployeeController;

(new PublicEmployeeController())->detail((int) ($_GET['id'] ?? 0));
