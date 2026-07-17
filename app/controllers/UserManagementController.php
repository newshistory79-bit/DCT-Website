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
            $this->setFlashMessage('user_error', 'ไม่พบผู้ใช้ที่ต้องการแก้ไข');
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

        ActivityLogger::log('users', 'create', 'สร้างผู้ใช้งาน: ' . $data['username'] . ' (Role: ' . $data['role'] . ')');

        $this->setFlashMessage('user_success', 'เพิ่มผู้ใช้งานสำเร็จ');
        $this->redirect('admin/users/index.php');
    }

    public function update(int $id): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'edit');

        $model = new UserManagementModel();
        $user  = $model->find($id);

        if ($user === null) {
            $this->setFlashMessage('user_error', 'ไม่พบผู้ใช้ที่ต้องการแก้ไข');
            $this->redirect('admin/users/index.php');
            return;
        }

        $data = $this->validate($_POST, $id, $model, false);

        if ($data === null) {
            $this->redirect('admin/users/form.php?id=' . $id);
            return;
        }

        $model->update($id, $data);

        ActivityLogger::log('users', 'update', 'แก้ไขผู้ใช้งาน: ' . $data['username']);

        $this->setFlashMessage('user_success', 'แก้ไขผู้ใช้งานสำเร็จ');
        $this->redirect('admin/users/index.php');
    }

    public function destroy(int $id): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'delete');

        $token = (string) ($_POST['csrf_token'] ?? '');

        if (!verifyCsrfToken($token)) {
            $this->setFlashMessage('user_error', 'คำขอไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง');
            $this->redirect('admin/users/index.php');
            return;
        }

        $model = new UserManagementModel();
        $user  = $model->find($id);

        if ($user === null) {
            $this->setFlashMessage('user_error', 'ไม่พบผู้ใช้ที่ต้องการลบ');
            $this->redirect('admin/users/index.php');
            return;
        }

        // ป้องกันไม่ให้ผู้ใช้ลบบัญชีของตัวเอง เพื่อไม่ให้ระบบขาดผู้ดูแลที่ใช้งานได้
        if ((int) ($_SESSION['user_id'] ?? 0) === $id) {
            $this->setFlashMessage('user_error', 'ไม่สามารถลบบัญชีของตัวเองได้');
            $this->redirect('admin/users/index.php');
            return;
        }

        // Soft Delete เท่านั้น - ไม่ลบข้อมูลจริง
        $model->softDelete($id);

        ActivityLogger::log('users', 'delete', 'ลบผู้ใช้งาน: ' . $user['username']);

        $this->setFlashMessage('user_success', 'ลบผู้ใช้งานสำเร็จ');
        $this->redirect('admin/users/index.php');
    }

    private function validate(array $input, ?int $excludeId, UserManagementModel $model, bool $isCreate): ?array
    {
        $token = (string) ($input['csrf_token'] ?? '');

        if (!verifyCsrfToken($token)) {
            $this->setFlashMessage('user_form_error', 'คำขอไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง');
            return null;
        }

        $username = trim((string) ($input['username'] ?? ''));
        $fullName = trim((string) ($input['full_name'] ?? ''));
        $email    = trim((string) ($input['email'] ?? ''));
        $role     = (string) ($input['role'] ?? '');
        $status   = (string) ($input['status'] ?? '');
        $password = trim((string) ($input['password'] ?? ''));

        if ($username === '') {
            $this->setFlashMessage('user_form_error', 'กรุณากรอกชื่อผู้ใช้');
            return null;
        }

        if (mb_strlen($username) > 50) {
            $this->setFlashMessage('user_form_error', 'ชื่อผู้ใช้ต้องไม่เกิน 50 ตัวอักษร');
            return null;
        }

        if ($fullName === '') {
            $this->setFlashMessage('user_form_error', 'กรุณากรอกชื่อ-นามสกุล');
            return null;
        }

        if (mb_strlen($fullName) > 255) {
            $this->setFlashMessage('user_form_error', 'ชื่อ-นามสกุลต้องไม่เกิน 255 ตัวอักษร');
            return null;
        }

        if ($email !== '') {
            if (mb_strlen($email) > 100) {
                $this->setFlashMessage('user_form_error', 'อีเมลต้องไม่เกิน 100 ตัวอักษร');
                return null;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->setFlashMessage('user_form_error', 'รูปแบบอีเมลไม่ถูกต้อง');
                return null;
            }
        }

        if (!in_array($role, ['Admin', 'Editor', 'Staff'], true)) {
            $this->setFlashMessage('user_form_error', 'สิทธิ์ไม่ถูกต้อง ต้องเป็น Admin, Editor หรือ Staff เท่านั้น');
            return null;
        }

        if (!in_array($status, ['Active', 'Inactive'], true)) {
            $this->setFlashMessage('user_form_error', 'สถานะไม่ถูกต้อง ต้องเป็น Active หรือ Inactive เท่านั้น');
            return null;
        }

        if ($isCreate && $password === '') {
            $this->setFlashMessage('user_form_error', 'กรุณากรอกรหัสผ่าน');
            return null;
        }

        if ($password !== '' && mb_strlen($password) < 8) {
            $this->setFlashMessage('user_form_error', 'รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร');
            return null;
        }

        if ($model->usernameExists($username, $excludeId)) {
            $this->setFlashMessage('user_form_error', 'ชื่อผู้ใช้นี้ถูกใช้งานแล้ว');
            return null;
        }

        if ($email !== '' && $model->emailExists($email, $excludeId)) {
            $this->setFlashMessage('user_form_error', 'อีเมลนี้ถูกใช้งานแล้ว');
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
