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
            return ['success' => false, 'filename' => null, 'error' => 'ບໍ່ພົບໄຟລ໌ທີ່ອັບໂຫລດ ຫລືເກີດຂໍ້ຜິດພາດລະຫວ່າງອັບໂຫລດ'];
        }

        if ($file['size'] > $maxSizeBytes) {
            $maxMb = round($maxSizeBytes / 1024 / 1024, 1);

            return ['success' => false, 'filename' => null, 'error' => "ຂະໜາດໄຟລ໌ຕ້ອງບໍ່ເກີນ {$maxMb} MB"];
        }

        $extension = strtolower((string) pathinfo((string) $file['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedExtensions, true)) {
            return [
                'success'  => false,
                'filename' => null,
                'error'    => 'ນາມສະກຸນໄຟລ໌ບໍ່ໄດ້ຮັບອະນຸຍາດ (ອະນຸຍາດສະເພາະ ' . implode(', ', $allowedExtensions) . ')',
            ];
        }

        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedMimeTypes, true)) {
            return ['success' => false, 'filename' => null, 'error' => 'ປະເພດໄຟລ໌ບໍ່ຖືກຕ້ອງ (ກວດພົບ MIME Type: ' . $mimeType . ')'];
        }

        if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
            return ['success' => false, 'filename' => null, 'error' => 'ບໍ່ສາມາດສ້າງໂຟນເດີຈັດເກັບໄຟລ໌ໄດ້'];
        }

        // .jfif เป็นไฟล์ JPEG จริง แค่คนละนามสกุล — เก็บเป็น .jpg แทน เพราะ Apache mime.types ไม่รู้จัก .jfif
        // ทำให้เสิร์ฟไฟล์ static แล้วไม่ได้ Content-Type ที่ถูกต้อง
        $storedExtension = $extension === 'jfif' ? 'jpg' : $extension;
        $newFilename     = bin2hex(random_bytes(16)) . '.' . $storedExtension;
        $destination = rtrim($targetDir, '/\\') . DIRECTORY_SEPARATOR . $newFilename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return ['success' => false, 'filename' => null, 'error' => 'ບໍ່ສາມາດບັນທຶກໄຟລ໌ລົງເຊີບເວີໄດ້'];
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
