<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\UploadHelper;
use App\Middleware\AuthMiddleware;
use App\Models\GalleryModel;

class GalleryController extends BaseController
{
    private const MODULE             = 'gallery';
    private const PER_PAGE_OPTIONS   = [10, 25, 50, 100];
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];
    private const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
    private const MAX_FILE_SIZE      = 2097152; // 2 MB

    public function index(): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'view');

        $model = new GalleryModel();

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

        $this->render('admin/gallery/index', [
            'galleryItems'   => $result['data'],
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
            'successMessage' => $this->getFlashMessage('gallery_success'),
            'errorMessage'   => $this->getFlashMessage('gallery_error'),
        ]);
    }

    public function showCreateForm(): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'create');

        $this->render('admin/gallery/form', [
            'gallery'   => null,
            'csrfToken' => generateCsrfToken(),
            'formError' => $this->getFlashMessage('gallery_form_error'),
        ]);
    }

    public function showEditForm(int $id): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'edit');

        $model = new GalleryModel();
        $item  = $model->find($id);

        if ($item === null) {
            $this->setFlashMessage('gallery_error', 'ไม่พบภาพที่ต้องการแก้ไข');
            $this->redirect('admin/gallery/index.php');
            return;
        }

        $this->render('admin/gallery/form', [
            'gallery'   => $item,
            'csrfToken' => generateCsrfToken(),
            'formError' => $this->getFlashMessage('gallery_form_error'),
        ]);
    }

    public function store(): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'create');

        $data = $this->validate($_POST);

        if ($data === null) {
            $this->redirect('admin/gallery/form.php');
            return;
        }

        $file = $_FILES['image'] ?? null;

        // Create ต้องบังคับแนบรูปภาพเสมอ
        if ($file === null || $file['error'] === UPLOAD_ERR_NO_FILE) {
            $this->setFlashMessage('gallery_form_error', 'กรุณาแนบรูปภาพ');
            $this->redirect('admin/gallery/form.php');
            return;
        }

        $imageResult = $this->handleImageUpload($file);

        if ($imageResult === false) {
            $this->redirect('admin/gallery/form.php');
            return;
        }

        $data['image'] = $imageResult;

        $model = new GalleryModel();
        $model->create($data);

        $this->setFlashMessage('gallery_success', 'เพิ่มภาพสำเร็จ');
        $this->redirect('admin/gallery/index.php');
    }

    public function update(int $id): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'edit');

        $model = new GalleryModel();
        $item  = $model->find($id);

        if ($item === null) {
            $this->setFlashMessage('gallery_error', 'ไม่พบภาพที่ต้องการแก้ไข');
            $this->redirect('admin/gallery/index.php');
            return;
        }

        $data = $this->validate($_POST);

        if ($data === null) {
            $this->redirect('admin/gallery/form.php?id=' . $id);
            return;
        }

        // Edit ไม่บังคับเปลี่ยนรูป - ถ้าไม่แนบใหม่ให้คงรูปเดิมไว้
        $data['image'] = $item['image'];

        $file       = $_FILES['image'] ?? null;
        $hasNewFile = $file !== null && $file['error'] !== UPLOAD_ERR_NO_FILE;

        if ($hasNewFile) {
            $imageResult = $this->handleImageUpload($file);

            if ($imageResult === false) {
                $this->redirect('admin/gallery/form.php?id=' . $id);
                return;
            }

            $data['image'] = $imageResult;
        }

        $model->update($id, $data);

        // ลบรูปเก่าออกหลังจากอัปโหลดรูปใหม่และบันทึกฐานข้อมูลสำเร็จแล้วเท่านั้น
        if ($hasNewFile) {
            UploadHelper::delete(self::uploadDirectory(), $item['image']);
        }

        $this->setFlashMessage('gallery_success', 'แก้ไขภาพสำเร็จ');
        $this->redirect('admin/gallery/index.php');
    }

    public function destroy(int $id): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'delete');

        $token = (string) ($_POST['csrf_token'] ?? '');

        if (!verifyCsrfToken($token)) {
            $this->setFlashMessage('gallery_error', 'คำขอไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง');
            $this->redirect('admin/gallery/index.php');
            return;
        }

        $model = new GalleryModel();
        $item  = $model->find($id);

        if ($item === null) {
            $this->setFlashMessage('gallery_error', 'ไม่พบภาพที่ต้องการลบ');
            $this->redirect('admin/gallery/index.php');
            return;
        }

        // Soft Delete เท่านั้น - ไม่ลบไฟล์รูปจริง เพื่อรักษาประวัติข้อมูลตามที่อนุมัติ
        $model->softDelete($id);

        $this->setFlashMessage('gallery_success', 'ลบภาพสำเร็จ');
        $this->redirect('admin/gallery/index.php');
    }

    private static function uploadDirectory(): string
    {
        return ROOT_PATH . '/uploads/gallery';
    }

    // จัดการอัปโหลดรูป คืนค่า: string (ชื่อไฟล์ใหม่), false (ตรวจสอบไม่ผ่าน)
    private function handleImageUpload(array $file): string|false
    {
        $result = UploadHelper::upload(
            $file,
            self::uploadDirectory(),
            self::ALLOWED_EXTENSIONS,
            self::ALLOWED_MIME_TYPES,
            self::MAX_FILE_SIZE
        );

        if (!$result['success']) {
            $this->setFlashMessage('gallery_form_error', $result['error']);
            return false;
        }

        return $result['filename'];
    }

    private function validate(array $input): ?array
    {
        $token = (string) ($input['csrf_token'] ?? '');

        if (!verifyCsrfToken($token)) {
            $this->setFlashMessage('gallery_form_error', 'คำขอไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง');
            return null;
        }

        $title       = trim((string) ($input['title'] ?? ''));
        $description = trim((string) ($input['description'] ?? ''));
        $status      = (string) ($input['status'] ?? '');

        if ($title === '') {
            $this->setFlashMessage('gallery_form_error', 'กรุณากรอกชื่อภาพ');
            return null;
        }

        if (mb_strlen($title) > 255) {
            $this->setFlashMessage('gallery_form_error', 'ชื่อภาพต้องไม่เกิน 255 ตัวอักษร');
            return null;
        }

        if (!in_array($status, ['Draft', 'Published'], true)) {
            $this->setFlashMessage('gallery_form_error', 'สถานะไม่ถูกต้อง ต้องเป็น Draft หรือ Published เท่านั้น');
            return null;
        }

        return [
            'title'       => $title,
            'description' => $description !== '' ? $description : null,
            'status'      => $status,
        ];
    }
}
