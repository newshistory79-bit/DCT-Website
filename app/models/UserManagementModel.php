<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\BaseModel;
use PDO;

// Model สำหรับ Users Module (Admin CRUD) แยกจาก App\Models\User ที่ใช้เฉพาะ Authentication (Phase 2)
// เพื่อไม่ให้การแก้ไขโมดูลนี้กระทบระบบ Login เดิม
class UserManagementModel extends BaseModel
{
    // Whitelist คีย์ Sort ที่ยอมรับจาก URL -> ชื่อคอลัมน์จริงในตาราง (ป้องกัน SQL Injection ผ่าน ORDER BY)
    private const SORTABLE_COLUMNS = [
        'id'         => 'id',
        'username'   => 'username',
        'full_name'  => 'full_name',
        'role'       => 'role',
        'status'     => 'status',
        'created_at' => 'created_at',
    ];

    public function paginate(array $filters, string $sortKey, string $sortDirection, int $page, int $perPage): array
    {
        $sortColumn    = self::SORTABLE_COLUMNS[$sortKey] ?? 'id';
        $sortDirection = strtoupper($sortDirection) === 'DESC' ? 'DESC' : 'ASC';

        [$where, $params] = $this->buildWhere($filters);

        $countStmt = $this->db->prepare('SELECT COUNT(*) FROM users ' . $where);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $page   = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $sql = 'SELECT id, username, full_name, email, role, status, first_login, last_login_at, created_at, updated_at
                FROM users
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
            'SELECT id, username, full_name, email, role, status, first_login, last_login_at, created_at, updated_at
             FROM users
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
            'INSERT INTO users (username, password, full_name, email, role, status, first_login)
             VALUES (:username, :password, :full_name, :email, :role, :status, 1)'
        );
        $stmt->execute([
            'username'  => $data['username'],
            'password'  => $data['password'],
            'full_name' => $data['full_name'],
            'email'     => $data['email'],
            'role'      => $data['role'],
            'status'    => $data['status'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        // เปลี่ยนรหัสผ่านเฉพาะกรณีมีการกรอกใหม่เท่านั้น (Edit ไม่บังคับเปลี่ยนรหัสผ่าน)
        if ($data['password'] !== null) {
            $stmt = $this->db->prepare(
                'UPDATE users
                 SET username = :username, password = :password, full_name = :full_name,
                     email = :email, role = :role, status = :status
                 WHERE id = :id AND deleted_at IS NULL'
            );

            return $stmt->execute([
                'username'  => $data['username'],
                'password'  => $data['password'],
                'full_name' => $data['full_name'],
                'email'     => $data['email'],
                'role'      => $data['role'],
                'status'    => $data['status'],
                'id'        => $id,
            ]);
        }

        $stmt = $this->db->prepare(
            'UPDATE users
             SET username = :username, full_name = :full_name, email = :email, role = :role, status = :status
             WHERE id = :id AND deleted_at IS NULL'
        );

        return $stmt->execute([
            'username'  => $data['username'],
            'full_name' => $data['full_name'],
            'email'     => $data['email'],
            'role'      => $data['role'],
            'status'    => $data['status'],
            'id'        => $id,
        ]);
    }

    public function softDelete(int $id): bool
    {
        // อัปเดตเฉพาะ deleted_at เท่านั้น ห้ามแก้ไข/ลบข้อมูลจริง
        $stmt = $this->db->prepare(
            'UPDATE users SET deleted_at = NOW() WHERE id = :id AND deleted_at IS NULL'
        );

        return $stmt->execute(['id' => $id]);
    }

    public function usernameExists(string $username, ?int $excludeId = null): bool
    {
        $sql    = 'SELECT COUNT(*) FROM users WHERE username = :username AND deleted_at IS NULL';
        $params = ['username' => $username];

        if ($excludeId !== null) {
            $sql .= ' AND id != :id';
            $params['id'] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        $sql    = 'SELECT COUNT(*) FROM users WHERE email = :email AND deleted_at IS NULL';
        $params = ['email' => $email];

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
            $keyword = '%' . $filters['keyword'] . '%';
            // ใช้ placeholder แยกกันคนละตัว (PDO::ATTR_EMULATE_PREPARES=false ไม่รองรับชื่อ Parameter ซ้ำในคิวรีเดียว)
            $conditions[]          = '(username LIKE :kw_username OR full_name LIKE :kw_fullname OR email LIKE :kw_email)';
            $params['kw_username'] = $keyword;
            $params['kw_fullname'] = $keyword;
            $params['kw_email']    = $keyword;
        }

        if (!empty($filters['role']) && in_array($filters['role'], ['Admin', 'Editor', 'Staff'], true)) {
            $conditions[]   = 'role = :role';
            $params['role'] = $filters['role'];
        }

        if (!empty($filters['status']) && in_array($filters['status'], ['Active', 'Inactive'], true)) {
            $conditions[]     = 'status = :status';
            $params['status'] = $filters['status'];
        }

        return [' WHERE ' . implode(' AND ', $conditions), $params];
    }
}
