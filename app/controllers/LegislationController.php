<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Middleware\AuthMiddleware;
use App\Models\LegislationModel;
use DateTime;

class LegislationController extends BaseController
{
    private const MODULE           = 'legislation';
    private const PER_PAGE_OPTIONS = [10, 25, 50, 100];

    public function index(): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'view');

        $model = new LegislationModel();

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

        $this->render('admin/legislation/index', [
            'legislationItems' => $result['data'],
            'total'            => $result['total'],
            'totalPages'       => max(1, (int) ceil($result['total'] / $perPage)),
            'currentPage'      => $page,
            'perPage'          => $perPage,
            'perPageOptions'   => self::PER_PAGE_OPTIONS,
            'keyword'          => $keyword,
            'status'           => $status,
            'sort'             => $sort,
            'direction'        => $direction,
            'csrfToken'        => generateCsrfToken(),
            'successMessage'   => $this->getFlashMessage('legislation_success'),
            'errorMessage'     => $this->getFlashMessage('legislation_error'),
        ]);
    }

    public function showCreateForm(): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'create');

        $this->render('admin/legislation/form', [
            'legislation' => null,
            'csrfToken'   => generateCsrfToken(),
            'formError'   => $this->getFlashMessage('legislation_form_error'),
        ]);
    }

    public function showEditForm(int $id): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'edit');

        $model       = new LegislationModel();
        $legislation = $model->find($id);

        if ($legislation === null) {
            $this->setFlashMessage('legislation_error', 'ไม่พบข้อมูลกฎหมาย/ระเบียบที่ต้องการแก้ไข');
            $this->redirect('admin/legislation/index.php');
            return;
        }

        $this->render('admin/legislation/form', [
            'legislation' => $legislation,
            'csrfToken'   => generateCsrfToken(),
            'formError'   => $this->getFlashMessage('legislation_form_error'),
        ]);
    }

    public function store(): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'create');

        $data = $this->validate($_POST);

        if ($data === null) {
            $this->redirect('admin/legislation/form.php');
            return;
        }

        $model = new LegislationModel();
        $model->create($data);

        $this->setFlashMessage('legislation_success', 'เพิ่มข้อมูลกฎหมาย/ระเบียบสำเร็จ');
        $this->redirect('admin/legislation/index.php');
    }

    public function update(int $id): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'edit');

        $model       = new LegislationModel();
        $legislation = $model->find($id);

        if ($legislation === null) {
            $this->setFlashMessage('legislation_error', 'ไม่พบข้อมูลกฎหมาย/ระเบียบที่ต้องการแก้ไข');
            $this->redirect('admin/legislation/index.php');
            return;
        }

        $data = $this->validate($_POST);

        if ($data === null) {
            $this->redirect('admin/legislation/form.php?id=' . $id);
            return;
        }

        $model->update($id, $data);

        $this->setFlashMessage('legislation_success', 'แก้ไขข้อมูลกฎหมาย/ระเบียบสำเร็จ');
        $this->redirect('admin/legislation/index.php');
    }

    public function destroy(int $id): void
    {
        AuthMiddleware::requirePermission(self::MODULE, 'delete');

        $token = (string) ($_POST['csrf_token'] ?? '');

        if (!verifyCsrfToken($token)) {
            $this->setFlashMessage('legislation_error', 'คำขอไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง');
            $this->redirect('admin/legislation/index.php');
            return;
        }

        $model       = new LegislationModel();
        $legislation = $model->find($id);

        if ($legislation === null) {
            $this->setFlashMessage('legislation_error', 'ไม่พบข้อมูลกฎหมาย/ระเบียบที่ต้องการลบ');
            $this->redirect('admin/legislation/index.php');
            return;
        }

        // Soft Delete เท่านั้น - อัปเดตเฉพาะ deleted_at ไม่ลบข้อมูลจริง
        $model->softDelete($id);

        $this->setFlashMessage('legislation_success', 'ลบข้อมูลกฎหมาย/ระเบียบสำเร็จ');
        $this->redirect('admin/legislation/index.php');
    }

    private function validate(array $input): ?array
    {
        $token = (string) ($input['csrf_token'] ?? '');

        if (!verifyCsrfToken($token)) {
            $this->setFlashMessage('legislation_form_error', 'คำขอไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง');
            return null;
        }

        $title          = trim((string) ($input['title'] ?? ''));
        $documentNumber = trim((string) ($input['document_number'] ?? ''));
        $detail         = trim((string) ($input['detail'] ?? ''));
        $effectiveDate  = trim((string) ($input['effective_date'] ?? ''));
        $status         = (string) ($input['status'] ?? '');

        if ($title === '') {
            $this->setFlashMessage('legislation_form_error', 'กรุณากรอกหัวข้อกฎหมาย/ระเบียบ');
            return null;
        }

        if (mb_strlen($title) > 255) {
            $this->setFlashMessage('legislation_form_error', 'หัวข้อต้องไม่เกิน 255 ตัวอักษร');
            return null;
        }

        if ($detail === '') {
            $this->setFlashMessage('legislation_form_error', 'กรุณากรอกรายละเอียดกฎหมาย/ระเบียบ');
            return null;
        }

        if (mb_strlen($documentNumber) > 50) {
            $this->setFlashMessage('legislation_form_error', 'เลขที่ประกาศ/ระเบียบต้องไม่เกิน 50 ตัวอักษร');
            return null;
        }

        if (!in_array($status, ['Draft', 'Published'], true)) {
            $this->setFlashMessage('legislation_form_error', 'สถานะไม่ถูกต้อง ต้องเป็น Draft หรือ Published เท่านั้น');
            return null;
        }

        if ($effectiveDate !== '') {
            $parsedDate = DateTime::createFromFormat('Y-m-d', $effectiveDate);

            if (!$parsedDate || $parsedDate->format('Y-m-d') !== $effectiveDate) {
                $this->setFlashMessage('legislation_form_error', 'รูปแบบวันที่มีผลบังคับใช้ไม่ถูกต้อง (YYYY-MM-DD)');
                return null;
            }
        }

        return [
            'title'           => $title,
            'document_number' => $documentNumber !== '' ? $documentNumber : null,
            'detail'          => $detail,
            'effective_date'  => $effectiveDate !== '' ? $effectiveDate : null,
            'status'          => $status,
        ];
    }
}
