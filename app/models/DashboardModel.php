<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\BaseModel;
use PDO;

class DashboardModel extends BaseModel
{
    // Mapping ของสถิติที่ต้องแสดงบน Dashboard -> ชื่อตารางจริงในฐานข้อมูล
    // ตารางใดยังไม่ถูกออกแบบ/อนุมัติ ให้ใส่ null ไว้ (ห้ามเดาชื่อตาราง)
    private const MODULE_TABLES = [
        'news'        => 'news',
        'employees'   => 'employee',
        'departments' => 'departments',
        'activities'  => null,
        'documents'   => 'documents',
        'gallery'     => 'gallery',
        'legislation' => 'legislation',
    ];

    // คืนค่าจำนวนข้อมูลของแต่ละโมดูล หรือ null หากยังไม่มีตารางรองรับ
    public function getModuleCounts(): array
    {
        $counts = [];

        foreach (self::MODULE_TABLES as $label => $table) {
            if ($table === null || !$this->tableExists($table)) {
                $counts[$label] = null;
                continue;
            }

            $stmt = $this->db->query('SELECT COUNT(*) FROM `' . $table . '`');
            $counts[$label] = (int) $stmt->fetchColumn();
        }

        return $counts;
    }

    // ดึงรายชื่อผู้ใช้ที่ Login ล่าสุด เรียงตามเวลาล่าสุดก่อน
    public function getRecentLogins(int $limit = 5): array
    {
        $stmt = $this->db->prepare(
            'SELECT username, full_name, role, last_login_at
             FROM users
             WHERE last_login_at IS NOT NULL AND deleted_at IS NULL
             ORDER BY last_login_at DESC
             LIMIT :limit'
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // ตรวจสอบว่าตารางมีอยู่จริงในฐานข้อมูลปัจจุบันหรือไม่ (กันกรณีตารางถูกเปลี่ยน/ลบไปโดยไม่คาดคิด)
    private function tableExists(string $table): bool
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM information_schema.tables
             WHERE table_schema = DATABASE() AND table_name = :table'
        );
        $stmt->execute(['table' => $table]);

        return (int) $stmt->fetchColumn() > 0;
    }
}
