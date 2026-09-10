<?php

namespace App\Controller;

use App\Service\AuthService;

class AuthController
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * 登录页面
     */
    public function login(): void
    {
        $pageTitle = '管理员登录 - 图书馆借阅系统';
        $error     = $_GET['error'] ?? null;

        include __DIR__ . '/../../templates/auth/login.php';
    }

    /**
     * 处理登录提交
     */
    public function doLogin(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
            return;
        }

         if (!csrf_check()) {
            $this->redirect('XXX?error=' . urlencode('安全校验失败，请重新提交'));
            return;
        }

        $username = $_POST['username'] ?? '';
        $_SESSION['role'] = 'admin';
        $password = $_POST['password'] ?? '';

        $result = $this->authService->login($username, $password);

        if (($result['code'] ?? 500) === 200) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['token']    = $result['data']['token'] ?? '';
            $_SESSION['username'] = $username;

            $this->redirect('/');
        } else {
            $this->redirect('/login?error=' . urlencode($result['message'] ?? '登录失败'));
        }
    }

    /**
     * 登出
     */
    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        $this->redirect('/login?success=' . urlencode('已成功退出'));
    }

    /**
     * 修改密码页面
     */
    public function changePassword(): void
    {
        if (empty($_SESSION['token'])) {
            $this->redirect('/login?error=' . urlencode('请先登录'));
        }
        $pageTitle = '修改密码 - 图书馆借阅系统';
        $error     = $_GET['error'] ?? null;
        $success   = $_GET['success'] ?? null;
        include __DIR__ . '/../../templates/auth/change_password.php';
    }

    /**
     * 处理修改密码提交
     */
    public function doChangePassword(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/change-password');
            return;
        }
        if (!csrf_check()) {
            $this->redirect('/change-password?error=' . urlencode('安全校验失败，请重试'));
            return;
        }
        $result = $this->authService->changePassword(
            $_POST['old_password'] ?? '',
            $_POST['new_password'] ?? ''
        );
        if (($result['code'] ?? 500) === 200) {
            $this->redirect('/change-password?success=' . urlencode('密码修改成功'));
        } else {
            $this->redirect('/change-password?error=' . urlencode($result['message'] ?? '修改失败'));
        }
    }

    private function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}