<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/core/bootstrap.php';

use App\Controllers\PublicPageController;

(new PublicPageController())->sitemap();
