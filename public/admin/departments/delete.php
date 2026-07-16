<?php

declare(strict_types=1);

require_once dirname(__DIR__, 3) . '/app/core/bootstrap.php';

use App\Controllers\DepartmentController;

$id = (int) ($_POST['id'] ?? 0);

(new DepartmentController())->destroy($id);
