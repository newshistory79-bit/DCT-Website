<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\BaseModel;
use PDO;

class DepartmentModel extends BaseModel
{
    // Whitelist คอลัมน์ที่อนุญาตให้ Sort เพื่อป้องกัน SQL Injection ผ่าน ORDER BY
    private const SORTABLE_COLUMNS = ['id', 'name', 'status', 'created_at'];

    public function paginate(array $filters, string $sortColumn, string $sortDirection, int $page, int $perPage): array
    {
        $sortColumn    = in_array($sortColumn, self::SORTABLE_COLUMNS, true) ? $sortColumn : 'id';
        $sortDirection = strtoupper($sortDirection) === 'DESC' ? 'DESC' : 'ASC';

        [$where, $params] = $this->buildWhere($filters);

        $countStmt = $this->db->prepare('SELECT COUNT(*) FROM departments ' . $where);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $page   = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $sql = 'SELECT id, name, description, status, sort_order, created_at, updated_at
                FROM departments
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

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, name, description, status, sort_order, created_at, updated_at
             FROM departments
             WHERE id = :id AND deleted_at IS NULL
             LIMIT 1'
        );
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO departments (name, description, status, sort_order)
             VALUES (:name, :description, :status, :sort_order)'
        );
        $stmt->execute([
            'name'        => $data['name'],
            'description' => $data['description'],
            'status'      => $data['status'],
            'sort_order'  => $data['sort_order'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE departments
             SET name = :name, description = :description, status = :status, sort_order = :sort_order
             WHERE id = :id AND deleted_at IS NULL'
        );

        return $stmt->execute([
            'name'        => $data['name'],
            'description' => $data['description'],
            'status'      => $data['status'],
            'sort_order'  => $data['sort_order'],
            'id'          => $id,
        ]);
    }

    public function softDelete(int $id): bool
    {
        // อัปเดตเฉพาะ deleted_at เท่านั้น ห้ามแก้ไข name ของข้อมูลจริง
        // การป้องกัน UNIQUE Constraint ชนกับแถวที่ถูก Soft Delete ต้องแก้ที่ระดับ Schema (รอเสนอ/อนุมัติแยก)
        $stmt = $this->db->prepare(
            'UPDATE departments SET deleted_at = NOW() WHERE id = :id AND deleted_at IS NULL'
        );

        return $stmt->execute(['id' => $id]);
    }

    public function nameExists(string $name, ?int $excludeId = null): bool
    {
        $sql    = 'SELECT COUNT(*) FROM departments WHERE name = :name AND deleted_at IS NULL';
        $params = ['name' => $name];

        if ($excludeId !== null) {
            $sql .= ' AND id != :id';
            $params['id'] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn() > 0;
    }

    private function buildWhere(array $filters): array
    {
        $conditions = ['deleted_at IS NULL'];
        $params     = [];

        if (!empty($filters['keyword'])) {
            $conditions[]            = 'name LIKE :keyword_name';
            $params['keyword_name'] = '%' . $filters['keyword'] . '%';
        }

        if (!empty($filters['status']) && in_array($filters['status'], ['Active', 'Inactive'], true)) {
            $conditions[]      = 'status = :status';
            $params['status'] = $filters['status'];
        }

        return [' WHERE ' . implode(' AND ', $conditions), $params];
    }
}
