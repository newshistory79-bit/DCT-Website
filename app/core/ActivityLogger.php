<?php

declare(strict_types=1);

namespace App\Core;

use Throwable;

// Helper กลางสำหรับบันทึกประวัติการใช้งานระบบ (Audit Trail) ลงตาราง activity_logs
// เรียกจาก Controller ทุกโมดูลหลัง Action สำเร็จ - Insert-only ห้ามมี update/delete
class ActivityLogger
{
    // การบันทึก Log ต้องไม่ทำให้ Business Logic หลักล้มเหลว จึงครอบ try/catch แล้วเขียนลง error_log แทนหากล้มเหลว
    public static function log(
        string $module,
        string $action,
        string $description,
        ?int $userId = null,
        ?string $username = null,
        ?string $role = null
    ): void {
        try {
            $pdo = Database::getInstance()->getConnection();

            $stmt = $pdo->prepare(
                'INSERT INTO activity_logs (user_id, username, role, module, action, description, ip_address)
                 VALUES (:user_id, :username, :role, :module, :action, :description, :ip_address)'
            );

            $stmt->execute([
                'user_id'     => $userId ?? ($_SESSION['user_id'] ?? null),
                'username'    => $username ?? ($_SESSION['username'] ?? 'ບໍ່ຮູ້ຈັກຜູ້ໃຊ້'),
                'role'        => $role ?? ($_SESSION['role'] ?? '-'),
                'module'      => $module,
                'action'      => $action,
                'description' => $description,
                'ip_address'  => $_SERVER['REMOTE_ADDR'] ?? null,
            ]);
        } catch (Throwable $e) {
            error_log('[ActivityLogger] บันทึก Log ล้มเหลว: ' . $e->getMessage());
        }
    }
}
