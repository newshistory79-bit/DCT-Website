<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\BaseModel;
use PDO;

// Model สำหรับอ่านข้อมูล Activity Log เท่านั้น (List/Search/Filter/Sort/Pagination)
// การ Insert ทำผ่าน App\Core\ActivityLogger โดยตรง - ไม่มี create/update/delete ใน Model นี้
// เพราะ Log ต้องไม่ถูกแก้ไขหรือลบโดยผู้ใช้ระบบ (Immutable/Append-only)
class ActivityLogModel extends BaseModel
{
    // Whitelist คีย์ Sort ที่ยอมรับจาก URL -> ชื่อคอลัมน์จริงในตาราง (ป้องกัน SQL Injection ผ่าน ORDER BY)
    private const SORTABLE_COLUMNS = [
        'id'         => 'id',
        'username'   => 'username',
        'module'     => 'module',
        'action'     => 'action',
        'created_at' => 'created_at',
    ];

    public function paginate(array $filters, string $sortKey, string $sortDirection, int $page, int $perPage): array
    {
        $sortColumn    = self::SORTABLE_COLUMNS[$sortKey] ?? 'created_at';
        $sortDirection = strtoupper($sortDirection) === 'ASC' ? 'ASC' : 'DESC';

        [$where, $params] = $this->buildWhere($filters);

        $countStmt = $this->db->prepare('SELECT COUNT(*) FROM activity_logs ' . $where);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $page   = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $sql = 'SELECT id, user_id, username, role, module, action, description, ip_address, created_at
                FROM activity_logs
                ' . $where . '
                ORDER BY `' . $sortColumn . '` ' . $sortDirection . '
                LIMIT :limit OFFSET :offset';

        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data'  => $stmt->fetchAll(),
            'total' => $total,
        ];
    }

    private function buildWhere(array $filters): array
    {
        $conditions = ['1 = 1'];
        $params     = [];

        if (!empty($filters['keyword'])) {
            $conditions[]             = '(username LIKE :kw_username OR description LIKE :kw_description)';
            $params['kw_username']    = '%' . $filters['keyword'] . '%';
            $params['kw_description'] = '%' . $filters['keyword'] . '%';
        }

        if (!empty($filters['module'])) {
            $conditions[]     = 'module = :module';
            $params['module'] = $filters['module'];
        }

        if (!empty($filters['action'])) {
            $conditions[]     = 'action = :action';
            $params['action'] = $filters['action'];
        }

        if (!empty($filters['date_from'])) {
            $conditions[]        = 'created_at >= :date_from';
            $params['date_from'] = $filters['date_from'] . ' 00:00:00';
        }

        if (!empty($filters['date_to'])) {
            $conditions[]      = 'created_at <= :date_to';
            $params['date_to'] = $filters['date_to'] . ' 23:59:59';
        }

        return [' WHERE ' . implode(' AND ', $conditions), $params];
    }
}
