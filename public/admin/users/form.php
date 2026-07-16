<?php

declare(strict_types=1);

require_once dirname(__DIR__, 3) . '/app/core/bootstrap.php';

use App\Controllers\UserManagementController;

$controller = new UserManagementController();

$id = null;
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
} elseif (isset($_POST['id'])) {
    $id = (int) $_POST['id'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($id !== null) {
        $controller->update($id);
    } else {
        $controller->store();
    }
} else {
    if ($id !== null) {
        $controller->showEditForm($id);
    } else {
        $controller->showCreateForm();
    }
}
