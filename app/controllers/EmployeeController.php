<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\UploadHelper;
use App\Middleware\AuthMiddleware;
use App\Models\EmployeeModel;
use DateTime;

class EmployeeController extends BaseController
{
    private const MODULE            = 'employees';
    private const PER_PAGE_OPTIONS  = [10, 25, 50, 100];
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];
    private const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
    private const MAX_FILE_SIZE      = 2097152; // 2 MB

    public function index(): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'view');

        $model = new EmployeeModel();

        $keyword   = trim((string) ($_GET['keyword'] ?? ''));
        $gender    = (string) ($_GET['gender'] ?? '');
        $sort      = (string) ($_GET['sort'] ?? 'id');
        $direction = (string) ($_GET['direction'] ?? 'asc');
        $perPage   = (int) ($_GET['per_page'] ?? 10);
        $page      = max(1, (int) ($_GET['page'] ?? 1));

        if (!in_array($perPage, self::PER_PAGE_OPTIONS, true)) {
            $perPage = 10;
        }

        $result = $model->paginate(
            ['keyword' => $keyword, 'gender' => $gender],
            $sort,
            $direction,
            $page,
            $perPage
        );

        $this->render('admin/employees/index', [
            'employees'      => $result['data'],
            'total'          => $result['total'],
            'totalPages'     => max(1, (int) ceil($result['total'] / $perPage)),
            'currentPage'    => $page,
            'perPage'        => $perPage,
            'perPageOptions' => self::PER_PAGE_OPTIONS,
            'keyword'        => $keyword,
            'gender'         => $gender,
            'sort'           => $sort,
            'direction'      => $direction,
            'csrfToken'      => generateCsrfToken(),
            'successMessage' => $this->getFlashMessage('employee_success'),
            'errorMessage'   => $this->getFlashMessage('employee_error'),
        ]);
    }

    public function showCreateForm(): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'create');

        $this->render('admin/employees/form', [
            'employee'  => null,
            'csrfToken' => generateCsrfToken(),
            'formError' => $this->getFlashMessage('employee_form_error'),
        ]);
    }

    public function showEditForm(int $id): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'edit');

        $model    = new EmployeeModel();
        $employee = $model->find($id);

        if ($employee === null) {
            $this->setFlashMessage('employee_error', 'ไม่พบข้อมูลพนักงานที่ต้องการแก้ไข');
            $this->redirect('admin/employees/index.php');
            return;
        }

        $this->render('admin/employees/form', [
            'employee'  => $employee,
            'csrfToken' => generateCsrfToken(),
            'formError' => $this->getFlashMessage('employee_form_error'),
        ]);
    }

    public function store(): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'create');

        $data = $this->validate($_POST);

        if ($data === null) {
            $this->redirect('admin/employees/form.php');
            return;
        }

        $imageResult = $this->handleImageUpload($_FILES['image'] ?? null);

        if ($imageResult === false) {
            $this->redirect('admin/employees/form.php');
            return;
        }

        $data['image'] = $imageResult;

        $model = new EmployeeModel();
        $model->create($data);

        $this->setFlashMessage('employee_success', 'เพิ่มข้อมูลพนักงานสำเร็จ');
        $this->redirect('admin/employees/index.php');
    }

    public function update(int $id): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'edit');

        $model    = new EmployeeModel();
        $employee = $model->find($id);

        if ($employee === null) {
            $this->setFlashMessage('employee_error', 'ไม่พบข้อมูลพนักงานที่ต้องการแก้ไข');
            $this->redirect('admin/employees/index.php');
            return;
        }

        $data = $this->validate($_POST);

        if ($data === null) {
            $this->redirect('admin/employees/form.php?id=' . $id);
            return;
        }

        $data['image'] = $employee['image'];

        $hasNewImage = isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE;

        if ($hasNewImage) {
            $imageResult = $this->handleImageUpload($_FILES['image']);

            if ($imageResult === false) {
                $this->redirect('admin/employees/form.php?id=' . $id);
                return;
            }

            $data['image'] = $imageResult;
        }

        $model->update($id, $data);

        // ลบไฟล์รูปเก่าออกหลังจากอัปโหลดไฟล์ใหม่และบันทึกฐานข้อมูลสำเร็จแล้วเท่านั้น
        if ($hasNewImage) {
            UploadHelper::delete(self::uploadDirectory(), $employee['image']);
        }

        $this->setFlashMessage('employee_success', 'แก้ไขข้อมูลพนักงานสำเร็จ');
        $this->redirect('admin/employees/index.php');
    }

    public function destroy(int $id): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'delete');

        $token = (string) ($_POST['csrf_token'] ?? '');

        if (!verifyCsrfToken($token)) {
            $this->setFlashMessage('employee_error', 'คำขอไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง');
            $this->redirect('admin/employees/index.php');
            return;
        }

        $model    = new EmployeeModel();
        $employee = $model->find($id);

        if ($employee === null) {
            $this->setFlashMessage('employee_error', 'ไม่พบข้อมูลพนักงานที่ต้องการลบ');
            $this->redirect('admin/employees/index.php');
            return;
        }

        // Soft Delete เท่านั้น - ไม่ลบไฟล์รูปจริง เพื่อรักษาประวัติข้อมูลตามที่อนุมัติ
        $model->softDelete($id);

        $this->setFlashMessage('employee_success', 'ลบข้อมูลพนักงานสำเร็จ');
        $this->redirect('admin/employees/index.php');
    }

    private static function uploadDirectory(): string
    {
        return ROOT_PATH . '/uploads/employees';
    }

    // จัดการอัปโหลดรูป คืนค่า: string (ชื่อไฟล์ใหม่), null (ไม่มีไฟล์แนบ), false (ตรวจสอบไม่ผ่าน)
    private function handleImageUpload(?array $file): string|false|null
    {
        if ($file === null || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $result = UploadHelper::upload(
            $file,
            self::uploadDirectory(),
            self::ALLOWED_EXTENSIONS,
            self::ALLOWED_MIME_TYPES,
            self::MAX_FILE_SIZE
        );

        if (!$result['success']) {
            $this->setFlashMessage('employee_form_error', $result['error']);
            return false;
        }

        return $result['filename'];
    }

    private function validate(array $input): ?array
    {
        $token = (string) ($input['csrf_token'] ?? '');

        if (!verifyCsrfToken($token)) {
            $this->setFlashMessage('employee_form_error', 'คำขอไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง');
            return null;
        }

        $fname     = trim((string) ($input['fname'] ?? ''));
        $lname     = trim((string) ($input['lname'] ?? ''));
        $birthDate = trim((string) ($input['birth_date'] ?? ''));
        $gender    = (string) ($input['gender'] ?? '');
        $phone     = trim((string) ($input['phone'] ?? ''));
        $email     = trim((string) ($input['email'] ?? ''));
        $position  = trim((string) ($input['position'] ?? ''));
        $address   = trim((string) ($input['address'] ?? ''));

        if ($fname === '' || $lname === '') {
            $this->setFlashMessage('employee_form_error', 'กรุณากรอกชื่อและนามสกุลให้ครบถ้วน');
            return null;
        }

        if (mb_strlen($fname) > 255 || mb_strlen($lname) > 255) {
            $this->setFlashMessage('employee_form_error', 'ชื่อหรือนามสกุลต้องไม่เกิน 255 ตัวอักษร');
            return null;
        }

        if (!in_array($gender, ['Male', 'Female', 'Other'], true)) {
            $this->setFlashMessage('employee_form_error', 'เพศไม่ถูกต้อง ต้องเป็น Male, Female หรือ Other เท่านั้น');
            return null;
        }

        if ($email !== '') {
            if (mb_strlen($email) > 100) {
                $this->setFlashMessage('employee_form_error', 'อีเมลต้องไม่เกิน 100 ตัวอักษร');
                return null;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->setFlashMessage('employee_form_error', 'รูปแบบอีเมลไม่ถูกต้อง');
                return null;
            }
        }

        if ($phone !== '' && !preg_match('/^[0-9\-+() ]{6,20}$/', $phone)) {
            $this->setFlashMessage('employee_form_error', 'รูปแบบเบอร์โทรศัพท์ไม่ถูกต้อง');
            return null;
        }

        if (mb_strlen($position) > 100) {
            $this->setFlashMessage('employee_form_error', 'ตำแหน่งงานต้องไม่เกิน 100 ตัวอักษร');
            return null;
        }

        if ($birthDate !== '') {
            $parsedDate = DateTime::createFromFormat('Y-m-d', $birthDate);

            if (!$parsedDate || $parsedDate->format('Y-m-d') !== $birthDate) {
                $this->setFlashMessage('employee_form_error', 'รูปแบบวันเกิดไม่ถูกต้อง (YYYY-MM-DD)');
                return null;
            }
        }

        return [
            'fname'      => $fname,
            'lname'      => $lname,
            'birth_date' => $birthDate !== '' ? $birthDate : null,
            'gender'     => $gender,
            'phone'      => $phone !== '' ? $phone : null,
            'email'      => $email !== '' ? $email : null,
            'position'   => $position !== '' ? $position : null,
            'address'    => $address !== '' ? $address : null,
        ];
    }
}
