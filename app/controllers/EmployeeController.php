<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\ActivityLogger;
use App\Core\BaseController;
use App\Core\UploadHelper;
use App\Middleware\AuthMiddleware;
use App\Models\EmployeeModel;
use DateTime;

class EmployeeController extends BaseController
{
    private const MODULE            = 'employees';
    private const PER_PAGE_OPTIONS  = [10, 25, 50, 100];
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'jfif'];
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
            $this->setFlashMessage('employee_error', 'ບໍ່ພົບຂໍ້ມູນພະນັກງານທີ່ຕ້ອງການແກ້ໄຂ');
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

        ActivityLogger::log('employees', 'create', 'ເພີ່ມພະນັກງານ: ' . $data['fname'] . ' ' . $data['lname']);

        $this->setFlashMessage('employee_success', 'ເພີ່ມຂໍ້ມູນພະນັກງານສຳເລັດ');
        $this->redirect('admin/employees/index.php');
    }

    public function update(int $id): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'edit');

        $model    = new EmployeeModel();
        $employee = $model->find($id);

        if ($employee === null) {
            $this->setFlashMessage('employee_error', 'ບໍ່ພົບຂໍ້ມູນພະນັກງານທີ່ຕ້ອງການແກ້ໄຂ');
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

        ActivityLogger::log('employees', 'update', 'ແກ້ໄຂພະນັກງານ: ' . $data['fname'] . ' ' . $data['lname']);

        $this->setFlashMessage('employee_success', 'ແກ້ໄຂຂໍ້ມູນພະນັກງານສຳເລັດ');
        $this->redirect('admin/employees/index.php');
    }

    public function destroy(int $id): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'delete');

        $token = (string) ($_POST['csrf_token'] ?? '');

        if (!verifyCsrfToken($token)) {
            $this->setFlashMessage('employee_error', 'ຄຳຮ້ອງຂໍບໍ່ຖືກຕ້ອງ ກະລຸນາລອງໃໝ່ອີກຄັ້ງ');
            $this->redirect('admin/employees/index.php');
            return;
        }

        $model    = new EmployeeModel();
        $employee = $model->find($id);

        if ($employee === null) {
            $this->setFlashMessage('employee_error', 'ບໍ່ພົບຂໍ້ມູນພະນັກງານທີ່ຕ້ອງການລຶບ');
            $this->redirect('admin/employees/index.php');
            return;
        }

        // Soft Delete เท่านั้น - ไม่ลบไฟล์รูปจริง เพื่อรักษาประวัติข้อมูลตามที่อนุมัติ
        $model->softDelete($id);

        ActivityLogger::log('employees', 'delete', 'ລຶບພະນັກງານ: ' . $employee['Fname'] . ' ' . $employee['Lname']);

        $this->setFlashMessage('employee_success', 'ລຶບຂໍ້ມູນພະນັກງານສຳເລັດ');
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
            $this->setFlashMessage('employee_form_error', 'ຄຳຮ້ອງຂໍບໍ່ຖືກຕ້ອງ ກະລຸນາລອງໃໝ່ອີກຄັ້ງ');
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
            $this->setFlashMessage('employee_form_error', 'ກະລຸນາປ້ອນຊື່ ແລະ ນາມສະກຸນໃຫ້ຄົບຖ້ວນ');
            return null;
        }

        if (mb_strlen($fname) > 255 || mb_strlen($lname) > 255) {
            $this->setFlashMessage('employee_form_error', 'ຊື່ຫລືນາມສະກຸນຕ້ອງບໍ່ເກີນ 255 ໂຕອັກສອນ');
            return null;
        }

        if (!in_array($gender, ['Male', 'Female', 'Other'], true)) {
            $this->setFlashMessage('employee_form_error', 'ເພດບໍ່ຖືກຕ້ອງ ຕ້ອງເປັນ Male, Female ຫລື Other ເທົ່ານັ້ນ');
            return null;
        }

        if ($email !== '') {
            if (mb_strlen($email) > 100) {
                $this->setFlashMessage('employee_form_error', 'ອີເມວຕ້ອງບໍ່ເກີນ 100 ໂຕອັກສອນ');
                return null;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->setFlashMessage('employee_form_error', 'ຮູບແບບອີເມວບໍ່ຖືກຕ້ອງ');
                return null;
            }
        }

        if ($phone !== '' && !preg_match('/^[0-9\-+() ]{6,20}$/', $phone)) {
            $this->setFlashMessage('employee_form_error', 'ຮູບແບບເບີໂທລະສັບບໍ່ຖືກຕ້ອງ');
            return null;
        }

        if (mb_strlen($position) > 100) {
            $this->setFlashMessage('employee_form_error', 'ຕຳແໜ່ງງານຕ້ອງບໍ່ເກີນ 100 ໂຕອັກສອນ');
            return null;
        }

        if ($birthDate !== '') {
            $parsedDate = DateTime::createFromFormat('Y-m-d', $birthDate);

            if (!$parsedDate || $parsedDate->format('Y-m-d') !== $birthDate) {
                $this->setFlashMessage('employee_form_error', 'ຮູບແບບວັນເກີດບໍ່ຖືກຕ້ອງ (YYYY-MM-DD)');
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
