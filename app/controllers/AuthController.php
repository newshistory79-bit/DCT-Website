<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\ActivityLogger;
use App\Core\BaseController;
use App\Models\User;

class AuthController extends BaseController
{
    public function showLoginForm(): void
    {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('admin/index.php');
            return;
        }

        $this->render('admin/login', ['csrfToken' => generateCsrfToken()]);
    }

    public function login(): void
    {
        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $token    = (string) ($_POST['csrf_token'] ?? '');

        if (!verifyCsrfToken($token)) {
            $this->setFlashMessage('login_error', 'คำขอไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง');
            $this->redirect('admin/login.php');
            return;
        }

        if ($username === '' || $password === '') {
            $this->setFlashMessage('login_error', 'กรุณากรอกชื่อผู้ใช้และรหัสผ่าน');
            $this->redirect('admin/login.php');
            return;
        }

        $userModel = new User();
        $user      = $userModel->findByUsername($username);

        if ($user === null || !password_verify($password, $user['password'])) {
            ActivityLogger::log(
                'auth',
                'login_failed',
                'เข้าสู่ระบบล้มเหลว (ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง): ' . $username,
                null,
                $username,
                '-'
            );
            $this->setFlashMessage('login_error', 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง');
            $this->redirect('admin/login.php');
            return;
        }

        if ($user['status'] !== 'Active') {
            ActivityLogger::log(
                'auth',
                'login_failed',
                'เข้าสู่ระบบล้มเหลว (บัญชีถูกระงับการใช้งาน): ' . $user['username'],
                (int) $user['id'],
                $user['username'],
                $user['role']
            );
            $this->setFlashMessage('login_error', 'บัญชีนี้ถูกระงับการใช้งาน กรุณาติดต่อผู้ดูแลระบบ');
            $this->redirect('admin/login.php');
            return;
        }

        session_regenerate_id(true);

        $_SESSION['user_id']       = (int) $user['id'];
        $_SESSION['username']      = $user['username'];
        $_SESSION['full_name']     = $user['full_name'];
        $_SESSION['role']          = $user['role'];
        $_SESSION['first_login']   = (bool) $user['first_login'];
        $_SESSION['last_activity'] = time();

        $userModel->updateLastLogin((int) $user['id']);

        ActivityLogger::log('auth', 'login', 'เข้าสู่ระบบสำเร็จ: ' . $user['username']);

        if ($_SESSION['first_login']) {
            $this->redirect('admin/change-password.php');
            return;
        }

        $this->redirect('admin/index.php');
    }

    public function logout(): void
    {
        if (isset($_SESSION['user_id'])) {
            ActivityLogger::log(
                'auth',
                'logout',
                'ออกจากระบบ: ' . ($_SESSION['username'] ?? ''),
                (int) $_SESSION['user_id'],
                $_SESSION['username'] ?? null,
                $_SESSION['role'] ?? null
            );
        }

        $_SESSION = [];
        session_destroy();
        $this->redirect('admin/login.php');
    }
}
