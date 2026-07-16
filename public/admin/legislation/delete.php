<?php

declare(strict_types=1);

require_once dirname(__DIR__, 3) . '/app/core/bootstrap.php';

use App\Controllers\LegislationController;

$id = (int) ($_POST['id'] ?? 0);

(new LegislationController())->destroy($id);
