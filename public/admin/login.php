<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/core/bootstrap.php';

use App\Controllers\AuthController;

$controller = new AuthController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->login();
} else {
    $controller->showLoginForm();
}
