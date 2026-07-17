<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\ActivityLogger;
use App\Core\BaseController;
use App\Core\UploadHelper;
use App\Middleware\AuthMiddleware;
use App\Models\DocumentModel;

class DocumentController extends BaseController
{
    private const MODULE             = 'documents';
    private const PER_PAGE_OPTIONS   = [10, 25, 50, 100];
    private const ALLOWED_EXTENSIONS = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
    private const ALLOWED_MIME_TYPES = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        // ไฟล์ .docx/.xlsx/.pptx เป็น ZIP Container ภายใน บาง libmagic version ตรวจ MIME ได้แค่ระดับ container
        // จึงอนุญาต application/zip ไว้ด้วย โดยยังคงถูกกรองซ้ำด้วย Extension Whitelist อยู่เสมอ
        'application/zip',
        // ไฟล์ .doc/.xls/.ppt (รูปแบบเก่า) ใช้ OLE Compound File Binary Format ร่วมกันทั้งหมด
        // libmagic ตรวจได้แค่ระดับ Container (ไม่ Parse Stream ภายในเพื่อแยก Word/Excel/PowerPoint)
        // จึงต้องอนุญาต MIME กลางนี้ไว้ด้วย โดยยังคงถูกกรองซ้ำด้วย Extension Whitelist อยู่เสมอ
        'application/CDFV2',
        'application/x-cfb',
    ];
    private const MAX_FILE_SIZE = 10485760; // 10 MB

    public function index(): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'view');

        $model = new DocumentModel();

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

        $this->render('admin/documents/index', [
            'documents'      => $result['data'],
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
            'successMessage' => $this->getFlashMessage('document_success'),
            'errorMessage'   => $this->getFlashMessage('document_error'),
        ]);
    }

    public function showCreateForm(): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'create');

        $this->render('admin/documents/form', [
            'document'  => null,
            'csrfToken' => generateCsrfToken(),
            'formError' => $this->getFlashMessage('document_form_error'),
        ]);
    }

    public function showEditForm(int $id): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'edit');

        $model    = new DocumentModel();
        $document = $model->find($id);

        if ($document === null) {
            $this->setFlashMessage('document_error', 'ไม่พบเอกสารที่ต้องการแก้ไข');
            $this->redirect('admin/documents/index.php');
            return;
        }

        $this->render('admin/documents/form', [
            'document'  => $document,
            'csrfToken' => generateCsrfToken(),
            'formError' => $this->getFlashMessage('document_form_error'),
        ]);
    }

    public function store(): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'create');

        $data = $this->validate($_POST);

        if ($data === null) {
            $this->redirect('admin/documents/form.php');
            return;
        }

        $file = $_FILES['file'] ?? null;

        // Create ต้องบังคับแนบไฟล์เสมอ
        if ($file === null || $file['error'] === UPLOAD_ERR_NO_FILE) {
            $this->setFlashMessage('document_form_error', 'กรุณาแนบไฟล์เอกสาร');
            $this->redirect('admin/documents/form.php');
            return;
        }

        $uploadResult = $this->handleUpload($file);

        if (!$uploadResult['success']) {
            $this->redirect('admin/documents/form.php');
            return;
        }

        $data['file_name']          = $uploadResult['filename'];
        $data['original_file_name'] = $uploadResult['original'];
        $data['file_extension']     = $uploadResult['extension'];
        $data['file_size']          = $uploadResult['size'];

        $model = new DocumentModel();
        $model->create($data);

        ActivityLogger::log('documents', 'create', 'เพิ่มเอกสาร: ' . $data['title']);

        $this->setFlashMessage('document_success', 'เพิ่มเอกสารสำเร็จ');
        $this->redirect('admin/documents/index.php');
    }

    public function update(int $id): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'edit');

        $model    = new DocumentModel();
        $document = $model->find($id);

        if ($document === null) {
            $this->setFlashMessage('document_error', 'ไม่พบเอกสารที่ต้องการแก้ไข');
            $this->redirect('admin/documents/index.php');
            return;
        }

        $data = $this->validate($_POST);

        if ($data === null) {
            $this->redirect('admin/documents/form.php?id=' . $id);
            return;
        }

        // Edit ไม่บังคับเปลี่ยนไฟล์ - ถ้าไม่แนบใหม่ให้คงไฟล์เดิมไว้
        $data['file_name']          = $document['file_name'];
        $data['original_file_name'] = $document['original_file_name'];
        $data['file_extension']     = $document['file_extension'];
        $data['file_size']          = $document['file_size'];

        $file        = $_FILES['file'] ?? null;
        $hasNewFile  = $file !== null && $file['error'] !== UPLOAD_ERR_NO_FILE;

        if ($hasNewFile) {
            $uploadResult = $this->handleUpload($file);

            if (!$uploadResult['success']) {
                $this->redirect('admin/documents/form.php?id=' . $id);
                return;
            }

            $data['file_name']          = $uploadResult['filename'];
            $data['original_file_name'] = $uploadResult['original'];
            $data['file_extension']     = $uploadResult['extension'];
            $data['file_size']          = $uploadResult['size'];
        }

        $model->update($id, $data);

        // ลบไฟล์เก่าออกหลังจากอัปโหลดไฟล์ใหม่และบันทึกฐานข้อมูลสำเร็จแล้วเท่านั้น
        if ($hasNewFile) {
            UploadHelper::delete(self::uploadDirectory(), $document['file_name']);
        }

        ActivityLogger::log('documents', 'update', 'แก้ไขเอกสาร: ' . $data['title']);

        $this->setFlashMessage('document_success', 'แก้ไขเอกสารสำเร็จ');
        $this->redirect('admin/documents/index.php');
    }

    public function destroy(int $id): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'delete');

        $token = (string) ($_POST['csrf_token'] ?? '');

        if (!verifyCsrfToken($token)) {
            $this->setFlashMessage('document_error', 'คำขอไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง');
            $this->redirect('admin/documents/index.php');
            return;
        }

        $model    = new DocumentModel();
        $document = $model->find($id);

        if ($document === null) {
            $this->setFlashMessage('document_error', 'ไม่พบเอกสารที่ต้องการลบ');
            $this->redirect('admin/documents/index.php');
            return;
        }

        // Soft Delete เท่านั้น - ไม่ลบไฟล์จริง เพื่อรักษาประวัติข้อมูลตามที่อนุมัติ
        $model->softDelete($id);

        ActivityLogger::log('documents', 'delete', 'ลบเอกสาร: ' . $document['title']);

        $this->setFlashMessage('document_success', 'ลบเอกสารสำเร็จ');
        $this->redirect('admin/documents/index.php');
    }

    private static function uploadDirectory(): string
    {
        return ROOT_PATH . '/uploads/documents';
    }

    // จัดการอัปโหลดไฟล์ผ่าน UploadHelper (ไม่แก้ไข Class เดิม ส่งค่าพารามิเตอร์เท่านั้น)
    // คืนค่า ['success' => bool, 'filename' => ?string, 'original' => ?string, 'extension' => ?string, 'size' => ?int]
    private function handleUpload(array $file): array
    {
        $result = UploadHelper::upload(
            $file,
            self::uploadDirectory(),
            self::ALLOWED_EXTENSIONS,
            self::ALLOWED_MIME_TYPES,
            self::MAX_FILE_SIZE
        );

        if (!$result['success']) {
            $this->setFlashMessage('document_form_error', $result['error']);
            return ['success' => false, 'filename' => null, 'original' => null, 'extension' => null, 'size' => null];
        }

        return [
            'success'   => true,
            'filename'  => $result['filename'],
            'original'  => (string) $file['name'],
            'extension' => strtolower((string) pathinfo((string) $file['name'], PATHINFO_EXTENSION)),
            'size'      => (int) $file['size'],
        ];
    }

    private function validate(array $input): ?array
    {
        $token = (string) ($input['csrf_token'] ?? '');

        if (!verifyCsrfToken($token)) {
            $this->setFlashMessage('document_form_error', 'คำขอไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง');
            return null;
        }

        $title       = trim((string) ($input['title'] ?? ''));
        $description = trim((string) ($input['description'] ?? ''));
        $status      = (string) ($input['status'] ?? '');

        if ($title === '') {
            $this->setFlashMessage('document_form_error', 'กรุณากรอกชื่อเอกสาร');
            return null;
        }

        if (mb_strlen($title) > 255) {
            $this->setFlashMessage('document_form_error', 'ชื่อเอกสารต้องไม่เกิน 255 ตัวอักษร');
            return null;
        }

        if (!in_array($status, ['Draft', 'Published'], true)) {
            $this->setFlashMessage('document_form_error', 'สถานะไม่ถูกต้อง ต้องเป็น Draft หรือ Published เท่านั้น');
            return null;
        }

        return [
            'title'       => $title,
            'description' => $description !== '' ? $description : null,
            'status'      => $status,
        ];
    }
}
