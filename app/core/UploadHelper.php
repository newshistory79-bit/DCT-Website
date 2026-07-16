<?php

declare(strict_types=1);

namespace App\Core;

class UploadHelper
{
    // อัปโหลดไฟล์ตามกติกาที่กำหนด คืนค่า ['success'=>bool,'filename'=>?string,'error'=>?string]
    // เป็น Reusable Component ใช้ได้กับทุกโมดูลที่ต้องอัปโหลดไฟล์ (Employees, Documents, Gallery ฯลฯ)
    public static function upload(
        array $file,
        string $targetDir,
        array $allowedExtensions,
        array $allowedMimeTypes,
        int $maxSizeBytes
    ): array {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'filename' => null, 'error' => 'ไม่พบไฟล์ที่อัปโหลด หรือเกิดข้อผิดพลาดระหว่างอัปโหลด'];
        }

        if ($file['size'] > $maxSizeBytes) {
            $maxMb = round($maxSizeBytes / 1024 / 1024, 1);

            return ['success' => false, 'filename' => null, 'error' => "ขนาดไฟล์ต้องไม่เกิน {$maxMb} MB"];
        }

        $extension = strtolower((string) pathinfo((string) $file['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedExtensions, true)) {
            return [
                'success'  => false,
                'filename' => null,
                'error'    => 'นามสกุลไฟล์ไม่ได้รับอนุญาต (อนุญาตเฉพาะ ' . implode(', ', $allowedExtensions) . ')',
            ];
        }

        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedMimeTypes, true)) {
            return ['success' => false, 'filename' => null, 'error' => 'ประเภทไฟล์ไม่ถูกต้อง (ตรวจพบ MIME Type: ' . $mimeType . ')'];
        }

        if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
            return ['success' => false, 'filename' => null, 'error' => 'ไม่สามารถสร้างโฟลเดอร์จัดเก็บไฟล์ได้'];
        }

        $newFilename = bin2hex(random_bytes(16)) . '.' . $extension;
        $destination = rtrim($targetDir, '/\\') . DIRECTORY_SEPARATOR . $newFilename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return ['success' => false, 'filename' => null, 'error' => 'ไม่สามารถบันทึกไฟล์ลงเซิร์ฟเวอร์ได้'];
        }

        return ['success' => true, 'filename' => $newFilename, 'error' => null];
    }

    // ลบไฟล์เดิมออกจากโฟลเดอร์ที่กำหนด (เงียบหากไม่พบไฟล์ - ไม่ throw)
    public static function delete(string $targetDir, ?string $filename): void
    {
        if ($filename === null || $filename === '') {
            return;
        }

        $path = rtrim($targetDir, '/\\') . DIRECTORY_SEPARATOR . $filename;

        if (is_file($path)) {
            unlink($path);
        }
    }
}
