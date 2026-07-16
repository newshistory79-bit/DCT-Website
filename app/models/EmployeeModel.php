<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\BaseModel;
use PDO;

class EmployeeModel extends BaseModel
{
    // Whitelist คีย์ Sort ที่ยอมรับจาก URL -> ชื่อคอลัมน์จริงในตาราง (ป้องกัน SQL Injection ผ่าน ORDER BY)
    // ตาราง employee เป็นตารางเดิมที่ห้ามเปลี่ยนชื่อคอลัมน์ จึงมีชื่อ Mixed Case (ID, Fname, Lname)
    private const SORTABLE_COLUMNS = [
        'id'         => 'ID',
        'fname'      => 'Fname',
        'lname'      => 'Lname',
        'position'   => 'position',
        'birth_date' => 'birth_date',
        'created_at' => 'created_at',
    ];

    public function paginate(array $filters, string $sortKey, string $sortDirection, int $page, int $perPage): array
    {
        $sortColumn    = self::SORTABLE_COLUMNS[$sortKey] ?? 'ID';
        $sortDirection = strtoupper($sortDirection) === 'DESC' ? 'DESC' : 'ASC';

        [$where, $params] = $this->buildWhere($filters);

        $countStmt = $this->db->prepare('SELECT COUNT(*) FROM employee ' . $where);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $page   = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $sql = 'SELECT ID, Fname, Lname, birth_date, gender, phone, email, position, address, image, created_at, updated_at
                FROM employee
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
            'SELECT ID, Fname, Lname, birth_date, gender, phone, email, position, address, image, created_at, updated_at
             FROM employee
             WHERE ID = :id AND deleted_at IS NULL
             LIMIT 1'
        );
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO employee (Fname, Lname, birth_date, gender, phone, email, position, address, image)
             VALUES (:fname, :lname, :birth_date, :gender, :phone, :email, :position, :address, :image)'
        );
        $stmt->execute([
            'fname'      => $data['fname'],
            'lname'      => $data['lname'],
            'birth_date' => $data['birth_date'],
            'gender'     => $data['gender'],
            'phone'      => $data['phone'],
            'email'      => $data['email'],
            'position'   => $data['position'],
            'address'    => $data['address'],
            'image'      => $data['image'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE employee
             SET Fname = :fname, Lname = :lname, birth_date = :birth_date, gender = :gender,
                 phone = :phone, email = :email, position = :position, address = :address, image = :image
             WHERE ID = :id AND deleted_at IS NULL'
        );

        return $stmt->execute([
            'fname'      => $data['fname'],
            'lname'      => $data['lname'],
            'birth_date' => $data['birth_date'],
            'gender'     => $data['gender'],
            'phone'      => $data['phone'],
            'email'      => $data['email'],
            'position'   => $data['position'],
            'address'    => $data['address'],
            'image'      => $data['image'],
            'id'         => $id,
        ]);
    }

    public function softDelete(int $id): bool
    {
        // อัปเดตเฉพาะ deleted_at เท่านั้น ห้ามแก้ไข/ลบข้อมูลจริงหรือไฟล์รูป
        $stmt = $this->db->prepare(
            'UPDATE employee SET deleted_at = NOW() WHERE ID = :id AND deleted_at IS NULL'
        );

        return $stmt->execute(['id' => $id]);
    }

    private function buildWhere(array $filters): array
    {
        $conditions = ['deleted_at IS NULL'];
        $params     = [];

        if (!empty($filters['keyword'])) {
            $keyword = '%' . $filters['keyword'] . '%';
            // ใช้ placeholder แยกกันคนละตัวสำหรับแต่ละคอลัมน์ (PDO::ATTR_EMULATE_PREPARES=false
            // ไม่รองรับการใช้ชื่อ Parameter ซ้ำกันในคิวรีเดียว)
            $conditions[]              = '(Fname LIKE :kw_fname OR Lname LIKE :kw_lname OR email LIKE :kw_email
                                            OR phone LIKE :kw_phone OR position LIKE :kw_position)';
            $params['kw_fname']    = $keyword;
            $params['kw_lname']    = $keyword;
            $params['kw_email']    = $keyword;
            $params['kw_phone']    = $keyword;
            $params['kw_position'] = $keyword;
        }

        if (!empty($filters['gender']) && in_array($filters['gender'], ['Male', 'Female', 'Other'], true)) {
            $conditions[]       = 'gender = :gender';
            $params['gender'] = $filters['gender'];
        }

        return [' WHERE ' . implode(' AND ', $conditions), $params];
    }
}
