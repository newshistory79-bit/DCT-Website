<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\BaseModel;
use PDO;

class GalleryModel extends BaseModel
{
    // Whitelist คีย์ Sort ที่ยอมรับจาก URL -> ชื่อคอลัมน์จริงในตาราง (ป้องกัน SQL Injection ผ่าน ORDER BY)
    private const SORTABLE_COLUMNS = [
        'id'         => 'id',
        'title'      => 'title',
        'created_at' => 'created_at',
        'status'     => 'status',
    ];

    public function paginate(array $filters, string $sortKey, string $sortDirection, int $page, int $perPage): array
    {
        $sortColumn    = self::SORTABLE_COLUMNS[$sortKey] ?? 'id';
        $sortDirection = strtoupper($sortDirection) === 'DESC' ? 'DESC' : 'ASC';

        [$where, $params] = $this->buildWhere($filters);

        $countStmt = $this->db->prepare('SELECT COUNT(*) FROM gallery ' . $where);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $page   = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $sql = 'SELECT id, title, description, image, status, created_at, updated_at
                FROM gallery
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
            'SELECT id, title, description, image, status, created_at, updated_at
             FROM gallery
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
            'INSERT INTO gallery (title, description, image, status)
             VALUES (:title, :description, :image, :status)'
        );
        $stmt->execute([
            'title'       => $data['title'],
            'description' => $data['description'],
            'image'       => $data['image'],
            'status'      => $data['status'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE gallery
             SET title = :title, description = :description, image = :image, status = :status
             WHERE id = :id AND deleted_at IS NULL'
        );

        return $stmt->execute([
            'title'       => $data['title'],
            'description' => $data['description'],
            'image'       => $data['image'],
            'status'      => $data['status'],
            'id'          => $id,
        ]);
    }

    public function softDelete(int $id): bool
    {
        // อัปเดตเฉพาะ deleted_at เท่านั้น ห้ามแก้ไข/ลบข้อมูลจริงหรือไฟล์รูป
        $stmt = $this->db->prepare(
            'UPDATE gallery SET deleted_at = NOW() WHERE id = :id AND deleted_at IS NULL'
        );

        return $stmt->execute(['id' => $id]);
    }

    private function buildWhere(array $filters): array
    {
        $conditions = ['deleted_at IS NULL'];
        $params     = [];

        if (!empty($filters['keyword'])) {
            $keyword = '%' . $filters['keyword'] . '%';
            // ใช้ placeholder แยกกันคนละตัว (PDO::ATTR_EMULATE_PREPARES=false ไม่รองรับชื่อ Parameter ซ้ำในคิวรีเดียว)
            $conditions[]         = '(title LIKE :kw_title OR description LIKE :kw_desc)';
            $params['kw_title'] = $keyword;
            $params['kw_desc']  = $keyword;
        }

        if (!empty($filters['status']) && in_array($filters['status'], ['Draft', 'Published'], true)) {
            $conditions[]     = 'status = :status';
            $params['status'] = $filters['status'];
        }

        return [' WHERE ' . implode(' AND ', $conditions), $params];
    }
}
