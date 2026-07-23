<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\ActivityLogger;
use App\Core\BaseController;
use App\Core\UploadHelper;
use App\Middleware\AuthMiddleware;
use App\Models\NewsModel;
use DateTime;

class NewsController extends BaseController
{
    private const MODULE             = 'news';
    private const PER_PAGE_OPTIONS   = [10, 25, 50, 100];
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'jfif'];
    private const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
    private const MAX_FILE_SIZE      = 2097152; // 2 MB

    public function index(): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'view');

        $model = new NewsModel();

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

        $this->render('admin/news/index', [
            'newsItems'      => $result['data'],
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
            'successMessage' => $this->getFlashMessage('news_success'),
            'errorMessage'   => $this->getFlashMessage('news_error'),
        ]);
    }

    public function showCreateForm(): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'create');

        $this->render('admin/news/form', [
            'news'      => null,
            'csrfToken' => generateCsrfToken(),
            'formError' => $this->getFlashMessage('news_form_error'),
        ]);
    }

    public function showEditForm(int $id): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'edit');

        $model = new NewsModel();
        $news  = $model->find($id);

        if ($news === null) {
            $this->setFlashMessage('news_error', 'ບໍ່ພົບຂ່າວທີ່ຕ້ອງການແກ້ໄຂ');
            $this->redirect('admin/news/index.php');
            return;
        }

        $this->render('admin/news/form', [
            'news'      => $news,
            'csrfToken' => generateCsrfToken(),
            'formError' => $this->getFlashMessage('news_form_error'),
        ]);
    }

    public function store(): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'create');

        $data = $this->validate($_POST);

        if ($data === null) {
            $this->redirect('admin/news/form.php');
            return;
        }

        $imageResult = $this->handleImageUpload($_FILES['image'] ?? null);

        if ($imageResult === false) {
            $this->redirect('admin/news/form.php');
            return;
        }

        $data['image'] = $imageResult;

        $model = new NewsModel();
        $model->create($data);

        ActivityLogger::log('news', 'create', 'ເພີ່ມຂ່າວ: ' . $data['title']);

        $this->setFlashMessage('news_success', 'ເພີ່ມຂ່າວສຳເລັດ');
        $this->redirect('admin/news/index.php');
    }

    public function update(int $id): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'edit');

        $model = new NewsModel();
        $news  = $model->find($id);

        if ($news === null) {
            $this->setFlashMessage('news_error', 'ບໍ່ພົບຂ່າວທີ່ຕ້ອງການແກ້ໄຂ');
            $this->redirect('admin/news/index.php');
            return;
        }

        $data = $this->validate($_POST);

        if ($data === null) {
            $this->redirect('admin/news/form.php?id=' . $id);
            return;
        }

        $data['image'] = $news['image'];

        $hasNewImage = isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE;

        if ($hasNewImage) {
            $imageResult = $this->handleImageUpload($_FILES['image']);

            if ($imageResult === false) {
                $this->redirect('admin/news/form.php?id=' . $id);
                return;
            }

            $data['image'] = $imageResult;
        }

        $model->update($id, $data);

        // ลบไฟล์รูปเก่าออกหลังจากอัปโหลดไฟล์ใหม่และบันทึกฐานข้อมูลสำเร็จแล้วเท่านั้น
        if ($hasNewImage) {
            UploadHelper::delete(self::uploadDirectory(), $news['image']);
        }

        ActivityLogger::log('news', 'update', 'ແກ້ໄຂຂ່າວ: ' . $data['title']);

        $this->setFlashMessage('news_success', 'ແກ້ໄຂຂ່າວສຳເລັດ');
        $this->redirect('admin/news/index.php');
    }

    public function destroy(int $id): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'delete');

        $token = (string) ($_POST['csrf_token'] ?? '');

        if (!verifyCsrfToken($token)) {
            $this->setFlashMessage('news_error', 'ຄຳຮ້ອງຂໍບໍ່ຖືກຕ້ອງ ກະລຸນາລອງໃໝ່ອີກຄັ້ງ');
            $this->redirect('admin/news/index.php');
            return;
        }

        $model = new NewsModel();
        $news  = $model->find($id);

        if ($news === null) {
            $this->setFlashMessage('news_error', 'ບໍ່ພົບຂ່າວທີ່ຕ້ອງການລຶບ');
            $this->redirect('admin/news/index.php');
            return;
        }

        // Soft Delete เท่านั้น - ไม่ลบไฟล์รูปจริง เพื่อรักษาประวัติข้อมูลตามที่อนุมัติ
        $model->softDelete($id);

        ActivityLogger::log('news', 'delete', 'ລຶບຂ່າວ: ' . $news['title']);

        $this->setFlashMessage('news_success', 'ລຶບຂ່າວສຳເລັດ');
        $this->redirect('admin/news/index.php');
    }

    private static function uploadDirectory(): string
    {
        return ROOT_PATH . '/uploads/news';
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
            $this->setFlashMessage('news_form_error', $result['error']);
            return false;
        }

        return $result['filename'];
    }

    private function validate(array $input): ?array
    {
        $token = (string) ($input['csrf_token'] ?? '');

        if (!verifyCsrfToken($token)) {
            $this->setFlashMessage('news_form_error', 'ຄຳຮ້ອງຂໍບໍ່ຖືກຕ້ອງ ກະລຸນາລອງໃໝ່ອີກຄັ້ງ');
            return null;
        }

        $title        = trim((string) ($input['title'] ?? ''));
        $detail       = trim((string) ($input['detail'] ?? ''));
        $activityDate = trim((string) ($input['activity_date'] ?? ''));
        $status       = (string) ($input['status'] ?? '');

        if ($title === '') {
            $this->setFlashMessage('news_form_error', 'ກະລຸນາປ້ອນຫົວຂໍ້ຂ່າວ');
            return null;
        }

        if ($detail === '') {
            $this->setFlashMessage('news_form_error', 'ກະລຸນາປ້ອນລາຍລະອຽດຂ່າວ');
            return null;
        }

        if (!in_array($status, ['Draft', 'Published'], true)) {
            $this->setFlashMessage('news_form_error', 'ສະຖານະບໍ່ຖືກຕ້ອງ ຕ້ອງເປັນ Draft ຫລື Published ເທົ່ານັ້ນ');
            return null;
        }

        if ($activityDate !== '') {
            $parsedDate = DateTime::createFromFormat('Y-m-d', $activityDate);

            if (!$parsedDate || $parsedDate->format('Y-m-d') !== $activityDate) {
                $this->setFlashMessage('news_form_error', 'ຮູບແບບວັນທີບໍ່ຖືກຕ້ອງ (YYYY-MM-DD)');
                return null;
            }
        }

        return [
            'title'         => $title,
            'detail'        => $detail,
            'activity_date' => $activityDate !== '' ? $activityDate : null,
            'status'        => $status,
        ];
    }
}
