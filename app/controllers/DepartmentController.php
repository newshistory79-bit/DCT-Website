<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\ActivityLogger;
use App\Core\BaseController;
use App\Middleware\AuthMiddleware;
use App\Models\DepartmentModel;

class DepartmentController extends BaseController
{
    private const MODULE          = 'departments';
    private const PER_PAGE_OPTIONS = [10, 25, 50, 100];

    public function index(): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'view');

        $model = new DepartmentModel();

        $keyword   = trim((string) ($_GET['keyword'] ?? ''));
        $status    = (string) ($_GET['status'] ?? '');
        $sort      = (string) ($_GET['sort'] ?? 'id');
        $direction = (string) ($_GET['direction'] ?? 'asc');
        $perPage   = (int) ($_GET['per_page'] ?? 10);
        $page      = max(1, (int) ($_GET['page'] ?? 1));

        if (!in_array($perPage, self::PER_PAGE_OPTIONS, true)) {
            $perPage = 10;
        }

        $result = $model->paginate(
            ['keyword' => $keyword, 'status' => $status],
            $sort,
            $direction,
            $page,
            $perPage
        );

        $this->render('admin/departments/index', [
            'departments'    => $result['data'],
            'total'          => $result['total'],
            'totalPages'     => max(1, (int) ceil($result['total'] / $perPage)),
            'currentPage'    => $page,
            'perPage'        => $perPage,
            'perPageOptions' => self::PER_PAGE_OPTIONS,
            'keyword'        => $keyword,
            'status'         => $status,
            'sort'           => $sort,
            'direction'      => $direction,
            'csrfToken'      => generateCsrfToken(),
            'successMessage' => $this->getFlashMessage('department_success'),
            'errorMessage'   => $this->getFlashMessage('department_error'),
        ]);
    }

    public function showCreateForm(): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'create');

        $this->render('admin/departments/form', [
            'department' => null,
            'csrfToken'  => generateCsrfToken(),
            'formError'  => $this->getFlashMessage('department_form_error'),
        ]);
    }

    public function showEditForm(int $id): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'edit');

        $model      = new DepartmentModel();
        $department = $model->find($id);

        if ($department === null) {
            $this->setFlashMessage('department_error', 'ບໍ່ພົບຂໍ້ມູນພະແນກທີ່ຕ້ອງການແກ້ໄຂ');
            $this->redirect('admin/departments/index.php');
            return;
        }

        $this->render('admin/departments/form', [
            'department' => $department,
            'csrfToken'  => generateCsrfToken(),
            'formError'  => $this->getFlashMessage('department_form_error'),
        ]);
    }

    public function store(): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'create');

        $model = new DepartmentModel();
        $data  = $this->validate($_POST, null, $model);

        if ($data === null) {
            $this->redirect('admin/departments/form.php');
            return;
        }

        $model->create($data);

        ActivityLogger::log('departments', 'create', 'ເພີ່ມພະແນກ: ' . $data['name']);

        $this->setFlashMessage('department_success', 'ເພີ່ມຂໍ້ມູນພະແນກສຳເລັດ');
        $this->redirect('admin/departments/index.php');
    }

    public function update(int $id): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'edit');

        $model      = new DepartmentModel();
        $department = $model->find($id);

        if ($department === null) {
            $this->setFlashMessage('department_error', 'ບໍ່ພົບຂໍ້ມູນພະແນກທີ່ຕ້ອງການແກ້ໄຂ');
            $this->redirect('admin/departments/index.php');
            return;
        }

        $data = $this->validate($_POST, $id, $model);

        if ($data === null) {
            $this->redirect('admin/departments/form.php?id=' . $id);
            return;
        }

        $model->update($id, $data);

        ActivityLogger::log('departments', 'update', 'ແກ້ໄຂພະແນກ: ' . $data['name']);

        $this->setFlashMessage('department_success', 'ແກ້ໄຂຂໍ້ມູນພະແນກສຳເລັດ');
        $this->redirect('admin/departments/index.php');
    }

    public function destroy(int $id): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'delete');

        $token = (string) ($_POST['csrf_token'] ?? '');

        if (!verifyCsrfToken($token)) {
            $this->setFlashMessage('department_error', 'ຄຳຮ້ອງຂໍບໍ່ຖືກຕ້ອງ ກະລຸນາລອງໃໝ່ອີກຄັ້ງ');
            $this->redirect('admin/departments/index.php');
            return;
        }

        $model      = new DepartmentModel();
        $department = $model->find($id);

        if ($department === null) {
            $this->setFlashMessage('department_error', 'ບໍ່ພົບຂໍ້ມູນພະແນກທີ່ຕ້ອງການລຶບ');
            $this->redirect('admin/departments/index.php');
            return;
        }

        $model->softDelete($id);

        ActivityLogger::log('departments', 'delete', 'ລຶບພະແນກ: ' . $department['name']);

        $this->setFlashMessage('department_success', 'ລຶບຂໍ້ມູນພະແນກສຳເລັດ');
        $this->redirect('admin/departments/index.php');
    }

    // Validate ข้อมูลฟอร์ม คืนค่า null พร้อมตั้ง Flash Error หาก Validation ไม่ผ่าน
    private function validate(array $input, ?int $excludeId, DepartmentModel $model): ?array
    {
        $token = (string) ($input['csrf_token'] ?? '');

        if (!verifyCsrfToken($token)) {
            $this->setFlashMessage('department_form_error', 'ຄຳຮ້ອງຂໍບໍ່ຖືກຕ້ອງ ກະລຸນາລອງໃໝ່ອີກຄັ້ງ');
            return null;
        }

        $code        = trim((string) ($input['code'] ?? ''));
        $name        = trim((string) ($input['name'] ?? ''));
        $description = trim((string) ($input['description'] ?? ''));
        $status      = (string) ($input['status'] ?? '');
        $sortOrderRaw = trim((string) ($input['sort_order'] ?? '0'));

        if ($code === '' || $name === '') {
            $this->setFlashMessage('department_form_error', 'ກະລຸນາປ້ອນລະຫັດພະແນກ ແລະ ຊື່ພະແນກໃຫ້ຄົບຖ້ວນ');
            return null;
        }

        if (mb_strlen($code) > 20) {
            $this->setFlashMessage('department_form_error', 'ລະຫັດພະແນກຕ້ອງບໍ່ເກີນ 20 ໂຕອັກສອນ');
            return null;
        }

        if (!preg_match('/^[A-Z0-9\-]+$/', $code)) {
            $this->setFlashMessage('department_form_error', 'ລະຫັດພະແນກຕ້ອງເປັນໂຕອັກສອນ A-Z ໂຕເລກ 0-9 ແລະ ເຄື່ອງໝາຍ - ເທົ່ານັ້ນ');
            return null;
        }

        if (mb_strlen($name) > 255) {
            $this->setFlashMessage('department_form_error', 'ຊື່ພະແນກຕ້ອງບໍ່ເກີນ 255 ໂຕອັກສອນ');
            return null;
        }

        if (!ctype_digit($sortOrderRaw)) {
            $this->setFlashMessage('department_form_error', 'ລຳດັບການສະແດງຜົນຕ້ອງເປັນຕົວເລກຈຳນວນເຕັມຕັ້ງແຕ່ 0 ຂຶ້ນໄປ');
            return null;
        }

        $sortOrder = (int) $sortOrderRaw;

        if ($sortOrder < 0) {
            $this->setFlashMessage('department_form_error', 'ລຳດັບການສະແດງຜົນຕ້ອງຫລາຍກວ່າຫລືເທົ່າກັບ 0');
            return null;
        }

        if (!in_array($status, ['Active', 'Inactive'], true)) {
            $this->setFlashMessage('department_form_error', 'ສະຖານະບໍ່ຖືກຕ້ອງ ຕ້ອງເປັນ Active ຫລື Inactive ເທົ່ານັ້ນ');
            return null;
        }

        if ($model->codeExists($code, $excludeId)) {
            $this->setFlashMessage('department_form_error', 'ລະຫັດພະແນກນີ້ຖືກນຳໃຊ້ແລ້ວ');
            return null;
        }

        if ($model->nameExists($name, $excludeId)) {
            $this->setFlashMessage('department_form_error', 'ຊື່ພະແນກນີ້ຖືກນຳໃຊ້ແລ້ວ');
            return null;
        }

        return [
            'code'        => $code,
            'name'        => $name,
            'description' => $description !== '' ? $description : null,
            'status'      => $status,
            'sort_order'  => $sortOrder,
        ];
    }
}
