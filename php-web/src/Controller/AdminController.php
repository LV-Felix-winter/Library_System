<?php

namespace App\Controller;

use App\Service\AdminService;

class AdminController
{
    private AdminService $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    /**
     * 管理员列表页
     */
    public function list(): void
    {
        $this->requireAdmin();

        $result  = $this->adminService->all();
        $admins  = $result['data'] ?? [];

        $pageTitle = '管理员管理 - 图书馆借阅系统';
        $error     = $_GET['error'] ?? null;
        $success   = $_GET['success'] ?? null;

        include __DIR__ . '/../../templates/admin/list.php';
    }

    /**
     * 修改权限
     */
    public function setRole(): void
    {
        $this->requireAdmin();

        $id   = (int)($_GET['id'] ?? 0);
        $role = (int)($_GET['role'] ?? 2);
        $result = $this->adminService->setRole($id, $role);

        if (($result['code'] ?? 500) === 200) {
            $this->redirect('/admins?success=' . urlencode('权限已更新'));
        } else {
            $this->redirect('/admins?error=' . urlencode($result['message'] ?? '操作失败'));
        }
    }

    private function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    private function requireAdmin(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['token'])) {
            $this->redirect('/login?error=' . urlencode('请先登录'));
        }
    }
}