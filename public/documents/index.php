<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/core/bootstrap.php';

use App\Controllers\PublicDocumentController;

(new PublicDocumentController())->index();
