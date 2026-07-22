<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\ActivityLogger;
use App\Core\BaseController;
use App\Core\UploadHelper;
use App\Middleware\AuthMiddleware;
use App\Models\ActivityModel;
use DateTime;

// Controller ของ Activities Module (Phase 13) - จัดการกิจกรรมของหน่วยงาน
// คนละตัวกับ App\Controllers\ActivityLogController ซึ่งเป็น Audit Trail ของ Phase 11 (ไม่เกี่ยวข้องกัน)
// Pattern เดียวกับ NewsController ทุกประการ (รูปภาพไม่บังคับ, activity_date ต้องกรอกและตรวจรูปแบบ)
class ActivityController extends BaseController
{
    private const MODULE             = 'activities';
    private const PER_PAGE_OPTIONS   = [10, 25, 50, 100];
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];
    private const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
    private const MAX_FILE_SIZE      = 2097152; // 2 MB

    public function index(): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'view');

        $model = new ActivityModel();

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

        $this->render('admin/activities/index', [
            'activities'     => $result['data'],
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
            'successMessage' => $this->getFlashMessage('activity_success'),
            'errorMessage'   => $this->getFlashMessage('activity_error'),
        ]);
    }

    public function showCreateForm(): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'create');

        $this->render('admin/activities/form', [
            'activity'  => null,
            'csrfToken' => generateCsrfToken(),
            'formError' => $this->getFlashMessage('activity_form_error'),
        ]);
    }

    public function showEditForm(int $id): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'edit');

        $model    = new ActivityModel();
        $activity = $model->find($id);

        if ($activity === null) {
            $this->setFlashMessage('activity_error', 'ບໍ່ພົບກິດຈະກຳທີ່ຕ້ອງການແກ້ໄຂ');
            $this->redirect('admin/activities/index.php');
            return;
        }

        $this->render('admin/activities/form', [
            'activity'  => $activity,
            'csrfToken' => generateCsrfToken(),
            'formError' => $this->getFlashMessage('activity_form_error'),
        ]);
    }

    public function store(): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'create');

        $data = $this->validate($_POST);

        if ($data === null) {
            $this->redirect('admin/activities/form.php');
            return;
        }

        $imageResult = $this->handleImageUpload($_FILES['image'] ?? null);

        if ($imageResult === false) {
            $this->redirect('admin/activities/form.php');
            return;
        }

        $data['image'] = $imageResult;

        $model = new ActivityModel();
        $model->create($data);

        ActivityLogger::log('activities', 'create', 'ເພີ່ມກິດຈະກຳ: ' . $data['title']);

        $this->setFlashMessage('activity_success', 'ເພີ່ມກິດຈະກຳສຳເລັດ');
        $this->redirect('admin/activities/index.php');
    }

    public function update(int $id): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'edit');

        $model    = new ActivityModel();
        $activity = $model->find($id);

        if ($activity === null) {
            $this->setFlashMessage('activity_error', 'ບໍ່ພົບກິດຈະກຳທີ່ຕ້ອງການແກ້ໄຂ');
            $this->redirect('admin/activities/index.php');
            return;
        }

        $data = $this->validate($_POST);

        if ($data === null) {
            $this->redirect('admin/activities/form.php?id=' . $id);
            return;
        }

        // Edit ไม่บังคับเปลี่ยนรูป - ถ้าไม่แนบใหม่ให้คงรูปเดิมไว้
        $data['image'] = $activity['image'];

        $hasNewImage = isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE;

        if ($hasNewImage) {
            $imageResult = $this->handleImageUpload($_FILES['image']);

            if ($imageResult === false) {
                $this->redirect('admin/activities/form.php?id=' . $id);
                return;
            }

            $data['image'] = $imageResult;
        }

        $model->update($id, $data);

        // ลบไฟล์รูปเก่าออกหลังจากอัปโหลดไฟล์ใหม่และบันทึกฐานข้อมูลสำเร็จแล้วเท่านั้น
        if ($hasNewImage && !empty($activity['image'])) {
            UploadHelper::delete(self::uploadDirectory(), $activity['image']);
        }

        ActivityLogger::log('activities', 'update', 'ແກ້ໄຂກິດຈະກຳ: ' . $data['title']);

        $this->setFlashMessage('activity_success', 'ແກ້ໄຂກິດຈະກຳສຳເລັດ');
        $this->redirect('admin/activities/index.php');
    }

    public function destroy(int $id): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'delete');

        $token = (string) ($_POST['csrf_token'] ?? '');

        if (!verifyCsrfToken($token)) {
            $this->setFlashMessage('activity_error', 'ຄຳຮ້ອງຂໍບໍ່ຖືກຕ້ອງ ກະລຸນາລອງໃໝ່ອີກຄັ້ງ');
            $this->redirect('admin/activities/index.php');
            return;
        }

        $model    = new ActivityModel();
        $activity = $model->find($id);

        if ($activity === null) {
            $this->setFlashMessage('activity_error', 'ບໍ່ພົບກິດຈະກຳທີ່ຕ້ອງການລຶບ');
            $this->redirect('admin/activities/index.php');
            return;
        }

        // Soft Delete เท่านั้น - ไม่ลบไฟล์รูปจริง เพื่อรักษาประวัติข้อมูลตามที่อนุมัติ
        $model->softDelete($id);

        ActivityLogger::log('activities', 'delete', 'ລຶບກິດຈະກຳ: ' . $activity['title']);

        $this->setFlashMessage('activity_success', 'ລຶບກິດຈະກຳສຳເລັດ');
        $this->redirect('admin/activities/index.php');
    }

    private static function uploadDirectory(): string
    {
        return ROOT_PATH . '/uploads/activities';
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
            $this->setFlashMessage('activity_form_error', $result['error']);
            return false;
        }

        return $result['filename'];
    }

    private function validate(array $input): ?array
    {
        $token = (string) ($input['csrf_token'] ?? '');

        if (!verifyCsrfToken($token)) {
            $this->setFlashMessage('activity_form_error', 'ຄຳຮ້ອງຂໍບໍ່ຖືກຕ້ອງ ກະລຸນາລອງໃໝ່ອີກຄັ້ງ');
            return null;
        }

        $title        = trim((string) ($input['title'] ?? ''));
        $description  = trim((string) ($input['description'] ?? ''));
        $activityDate = trim((string) ($input['activity_date'] ?? ''));
        $location     = trim((string) ($input['location'] ?? ''));
        $status       = (string) ($input['status'] ?? '');

        if ($title === '') {
            $this->setFlashMessage('activity_form_error', 'ກະລຸນາປ້ອນຫົວຂໍ້ກິດຈະກຳ');
            return null;
        }

        if (mb_strlen($title) > 255) {
            $this->setFlashMessage('activity_form_error', 'ຫົວຂໍ້ກິດຈະກຳຕ້ອງບໍ່ເກີນ 255 ໂຕອັກສອນ');
            return null;
        }

        if ($activityDate === '') {
            $this->setFlashMessage('activity_form_error', 'ກະລຸນາເລືອກວັນທີຈັດກິດຈະກຳ');
            return null;
        }

        $parsedDate = DateTime::createFromFormat('Y-m-d', $activityDate);

        if (!$parsedDate || $parsedDate->format('Y-m-d') !== $activityDate) {
            $this->setFlashMessage('activity_form_error', 'ຮູບແບບວັນທີຈັດກິດຈະກຳບໍ່ຖືກຕ້ອງ (YYYY-MM-DD)');
            return null;
        }

        if (mb_strlen($location) > 255) {
            $this->setFlashMessage('activity_form_error', 'ສະຖານທີ່ຈັດກິດຈະກຳຕ້ອງບໍ່ເກີນ 255 ໂຕອັກສອນ');
            return null;
        }

        if (!in_array($status, ['Draft', 'Published'], true)) {
            $this->setFlashMessage('activity_form_error', 'ສະຖານະບໍ່ຖືກຕ້ອງ ຕ້ອງເປັນ Draft ຫລື Published ເທົ່ານັ້ນ');
            return null;
        }

        return [
            'title'         => $title,
            'description'   => $description !== '' ? $description : null,
            'activity_date' => $activityDate,
            'location'      => $location !== '' ? $location : null,
            'status'        => $status,
        ];
    }
}
