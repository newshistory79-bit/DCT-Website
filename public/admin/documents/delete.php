<?php

declare(strict_types=1);

require_once dirname(__DIR__, 3) . '/app/core/bootstrap.php';

use App\Controllers\DocumentController;

$id = (int) ($_POST['id'] ?? 0);

(new DocumentController())->destroy($id);
