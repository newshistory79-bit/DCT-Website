<?php

declare(strict_types=1);

namespace App\Core;

abstract class BaseController
{
    // Render View พร้อมส่งข้อมูลเข้าไปในตัวแปรตามชื่อ key ของ $data
    protected function render(string $view, array $data = []): void
    {
        extract($data);

        require APP_PATH . '/views/' . $view . '.php';
    }

    // Redirect โดยใช้ helper เดิม (app/helpers/functions.php) เพื่อพฤติกรรม/URL เหมือนเดิมทุกประการ
    protected function redirect(string $path): void
    {
        redirect($path);
    }

    // เก็บข้อความแจ้งเตือนไว้ใน Session ตาม key ที่ระบุ (Flash Message)
    protected function setFlashMessage(string $key, string $message): void
    {
        $_SESSION[$key] = $message;
    }

    // ดึงข้อความแจ้งเตือนออกมาแล้วลบทิ้งทันที (อ่านได้ครั้งเดียว)
    protected function getFlashMessage(string $key): ?string
    {
        if (empty($_SESSION[$key])) {
            return null;
        }

        $message = $_SESSION[$key];
        unset($_SESSION[$key]);

        return $message;
    }
}
