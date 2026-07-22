<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\ActivityLogger;
use App\Core\BaseController;
use App\Middleware\AuthMiddleware;
use App\Models\UserManagementModel;

class UserManagementController extends BaseController
{
    private const MODULE           = 'users';
    private const PER_PAGE_OPTIONS = [10, 25, 50, 100];

    public function index(): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'view');

        $model = new UserManagementModel();

        $keyword   = trim((string) ($_GET['keyword'] ?? ''));
        $role      = (string) ($_GET['role'] ?? '');
        $status    = (string) ($_GET['status'] ?? '');
        $sort      = (string) ($_GET['sort'] ?? 'id');
        $direction = (string) ($_GET['direction'] ?? 'asc');
        $perPage   = (int) ($_GET['per_page'] ?? 10);
        $page      = max(1, (int) ($_GET['page'] ?? 1));

        if (!in_array($perPage, self::PER_PAGE_OPTIONS, true)) {
            $perPage = 10;
        }

        $result = $model->paginate(
            ['keyword' => $keyword, 'role' => $role, 'status' => $status],
            $sort,
            $direction,
            $page,
            $perPage
        );

        $this->render('admin/users/index', [
            'users'          => $result['data'],
            'total'          => $result['total'],
            'totalPages'     => max(1, (int) ceil($result['total'] / $perPage)),
            'currentPage'    => $page,
            'perPage'        => $perPage,
            'perPageOptions' => self::PER_PAGE_OPTIONS,
            'keyword'        => $keyword,
            'role'           => $role,
            'status'         => $status,
            'sort'           => $sort,
            'direction'      => $direction,
            'csrfToken'      => generateCsrfToken(),
            'successMessage' => $this->getFlashMessage('user_success'),
            'errorMessage'   => $this->getFlashMessage('user_error'),
        ]);
    }

    public function showCreateForm(): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'create');

        $this->render('admin/users/form', [
            'user'      => null,
            'csrfToken' => generateCsrfToken(),
            'formError' => $this->getFlashMessage('user_form_error'),
        ]);
    }

    public function showEditForm(int $id): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'edit');

        $model = new UserManagementModel();
        $user  = $model->find($id);

        if ($user === null) {
            $this->setFlashMessage('user_error', 'ບໍ່ພົບຜູ້ໃຊ້ທີ່ຕ້ອງການແກ້ໄຂ');
            $this->redirect('admin/users/index.php');
            return;
        }

        $this->render('admin/users/form', [
            'user'      => $user,
            'csrfToken' => generateCsrfToken(),
            'formError' => $this->getFlashMessage('user_form_error'),
        ]);
    }

    public function store(): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'create');

        $model = new UserManagementModel();
        $data  = $this->validate($_POST, null, $model, true);

        if ($data === null) {
            $this->redirect('admin/users/form.php');
            return;
        }

        $model->create($data);

        ActivityLogger::log('users', 'create', 'ສ້າງຜູ້ໃຊ້ງານ: ' . $data['username'] . ' (Role: ' . $data['role'] . ')');

        $this->setFlashMessage('user_success', 'ເພີ່ມຜູ້ໃຊ້ງານສຳເລັດ');
        $this->redirect('admin/users/index.php');
    }

    public function update(int $id): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'edit');

        $model = new UserManagementModel();
        $user  = $model->find($id);

        if ($user === null) {
            $this->setFlashMessage('user_error', 'ບໍ່ພົບຜູ້ໃຊ້ທີ່ຕ້ອງການແກ້ໄຂ');
            $this->redirect('admin/users/index.php');
            return;
        }

        $data = $this->validate($_POST, $id, $model, false);

        if ($data === null) {
            $this->redirect('admin/users/form.php?id=' . $id);
            return;
        }

        $model->update($id, $data);

        ActivityLogger::log('users', 'update', 'ແກ້ໄຂຜູ້ໃຊ້ງານ: ' . $data['username']);

        $this->setFlashMessage('user_success', 'ແກ້ໄຂຜູ້ໃຊ້ງານສຳເລັດ');
        $this->redirect('admin/users/index.php');
    }

    public function destroy(int $id): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'delete');

        $token = (string) ($_POST['csrf_token'] ?? '');

        if (!verifyCsrfToken($token)) {
            $this->setFlashMessage('user_error', 'ຄຳຮ້ອງຂໍບໍ່ຖືກຕ້ອງ ກະລຸນາລອງໃໝ່ອີກຄັ້ງ');
            $this->redirect('admin/users/index.php');
            return;
        }

        $model = new UserManagementModel();
        $user  = $model->find($id);

        if ($user === null) {
            $this->setFlashMessage('user_error', 'ບໍ່ພົບຜູ້ໃຊ້ທີ່ຕ້ອງການລຶບ');
            $this->redirect('admin/users/index.php');
            return;
        }

        // ป้องกันไม่ให้ผู้ใช้ลบบัญชีของตัวเอง เพื่อไม่ให้ระบบขาดผู้ดูแลที่ใช้งานได้
        if ((int) ($_SESSION['user_id'] ?? 0) === $id) {
            $this->setFlashMessage('user_error', 'ບໍ່ສາມາດລຶບບັນຊີຂອງຕົນເອງໄດ້');
            $this->redirect('admin/users/index.php');
            return;
        }

        // Soft Delete เท่านั้น - ไม่ลบข้อมูลจริง
        $model->softDelete($id);

        ActivityLogger::log('users', 'delete', 'ລຶບຜູ້ໃຊ້ງານ: ' . $user['username']);

        $this->setFlashMessage('user_success', 'ລຶບຜູ້ໃຊ້ງານສຳເລັດ');
        $this->redirect('admin/users/index.php');
    }

    private function validate(array $input, ?int $excludeId, UserManagementModel $model, bool $isCreate): ?array
    {
        $token = (string) ($input['csrf_token'] ?? '');

        if (!verifyCsrfToken($token)) {
            $this->setFlashMessage('user_form_error', 'ຄຳຮ້ອງຂໍບໍ່ຖືກຕ້ອງ ກະລຸນາລອງໃໝ່ອີກຄັ້ງ');
            return null;
        }

        $username = trim((string) ($input['username'] ?? ''));
        $fullName = trim((string) ($input['full_name'] ?? ''));
        $email    = trim((string) ($input['email'] ?? ''));
        $role     = (string) ($input['role'] ?? '');
        $status   = (string) ($input['status'] ?? '');
        $password = trim((string) ($input['password'] ?? ''));

        if ($username === '') {
            $this->setFlashMessage('user_form_error', 'ກະລຸນາປ້ອນຊື່ຜູ້ໃຊ້');
            return null;
        }

        if (mb_strlen($username) > 50) {
            $this->setFlashMessage('user_form_error', 'ຊື່ຜູ້ໃຊ້ຕ້ອງບໍ່ເກີນ 50 ໂຕອັກສອນ');
            return null;
        }

        if ($fullName === '') {
            $this->setFlashMessage('user_form_error', 'ກະລຸນາປ້ອນຊື່-ນາມສະກຸນ');
            return null;
        }

        if (mb_strlen($fullName) > 255) {
            $this->setFlashMessage('user_form_error', 'ຊື່-ນາມສະກຸນຕ້ອງບໍ່ເກີນ 255 ໂຕອັກສອນ');
            return null;
        }

        if ($email !== '') {
            if (mb_strlen($email) > 100) {
                $this->setFlashMessage('user_form_error', 'ອີເມວຕ້ອງບໍ່ເກີນ 100 ໂຕອັກສອນ');
                return null;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->setFlashMessage('user_form_error', 'ຮູບແບບອີເມວບໍ່ຖືກຕ້ອງ');
                return null;
            }
        }

        if (!in_array($role, ['Admin', 'Editor', 'Staff'], true)) {
            $this->setFlashMessage('user_form_error', 'ສິດທິບໍ່ຖືກຕ້ອງ ຕ້ອງເປັນ Admin, Editor ຫລື Staff ເທົ່ານັ້ນ');
            return null;
        }

        if (!in_array($status, ['Active', 'Inactive'], true)) {
            $this->setFlashMessage('user_form_error', 'ສະຖານະບໍ່ຖືກຕ້ອງ ຕ້ອງເປັນ Active ຫລື Inactive ເທົ່ານັ້ນ');
            return null;
        }

        if ($isCreate && $password === '') {
            $this->setFlashMessage('user_form_error', 'ກະລຸນາປ້ອນລະຫັດຜ່ານ');
            return null;
        }

        if ($password !== '' && mb_strlen($password) < 8) {
            $this->setFlashMessage('user_form_error', 'ລະຫັດຜ່ານຕ້ອງມີຢ່າງໜ້ອຍ 8 ໂຕອັກສອນ');
            return null;
        }

        if ($model->usernameExists($username, $excludeId)) {
            $this->setFlashMessage('user_form_error', 'ຊື່ຜູ້ໃຊ້ນີ້ຖືກນຳໃຊ້ແລ້ວ');
            return null;
        }

        if ($email !== '' && $model->emailExists($email, $excludeId)) {
            $this->setFlashMessage('user_form_error', 'ອີເມວນີ້ຖືກນຳໃຊ້ແລ້ວ');
            return null;
        }

        return [
            'username'  => $username,
            'full_name' => $fullName,
            'email'     => $email !== '' ? $email : null,
            'role'      => $role,
            'status'    => $status,
            'password'  => $password !== '' ? password_hash($password, PASSWORD_BCRYPT) : null,
        ];
    }
}
