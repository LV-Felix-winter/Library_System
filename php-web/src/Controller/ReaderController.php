<?php

namespace App\Controller;

use App\Service\ReaderService;
use App\Service\BorrowService;

class ReaderController
{
    private ReaderService $readerService;
    private BorrowService $borrowService;

    public function __construct(ReaderService $readerService, BorrowService $borrowService)
    {
        $this->readerService = $readerService;
        $this->borrowService = $borrowService;
    }

    /**
     * 读者列表页
     */
    public function list(): void
    {
        $this->requireAdmin();

        $page     = (int)($_GET['page'] ?? 1);
        $result   = $this->readerService->list($page);
        $readers  = $result['data'] ?? [];
        $total    = $result['total'] ?? 0;
        $totalPages = (int)ceil($total / 10);

        $pageTitle = '读者管理 - 图书馆借阅系统';
        $error     = $_GET['error'] ?? null;
        $success   = $_GET['success'] ?? null;

        include __DIR__ . '/../../templates/reader/list.php';
    }

    /**
     * 读者详情页（含借阅记录）
     */
    public function detail(): void
    {
        $this->requireAdmin();

        $id           = (int)($_GET['id'] ?? 0);
        $readerResult = $this->readerService->detail($id);
        $reader       = $readerResult['data'] ?? null;

        if (!$reader) {
            $this->redirect('/readers');
            return;
        }

        $borrowResult  = $this->borrowService->records($id);
        $borrowRecords = $borrowResult['data'] ?? [];

        $pageTitle = ($reader['name'] ?? '读者详情') . ' - 图书馆借阅系统';
        $success   = $_GET['success'] ?? null;
        $error     = $_GET['error'] ?? null;

        include __DIR__ . '/../../templates/reader/detail.php';
    }

    /**
     * 读者注册页面
     */
    public function create(): void
    {
        $this->requireAdmin();

        $pageTitle = '读者注册 - 图书馆借阅系统';
        $error     = $_GET['error'] ?? null;

        include __DIR__ . '/../../templates/reader/create.php';
    }

    /**
     * 处理读者注册提交
     */
    public function store(): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/readers/create');
            return;
        }

         if (!csrf_check()) {
            $this->redirect('XXX?error=' . urlencode('安全校验失败，请重新提交'));
            return;
        }

        $data = [
            'name'        => $_POST['name'] ?? '',
            'gender'      => (int)($_POST['gender'] ?? 0),
            'phone'       => $_POST['phone'] ?? '',
            'email'       => $_POST['email'] ?? '',
            'id_card'     => $_POST['id_card'] ?? '',
            'reader_type' => (int)($_POST['reader_type'] ?? 1),
            'max_borrow'  => (int)($_POST['max_borrow'] ?? 5),
        ];

        $result = $this->readerService->create($data);

        if (($result['code'] ?? 500) === 200) {
            $newId = $result['data']['id'] ?? 0;
            $this->redirect('/readers/detail?id=' . $newId . '&success=' . urlencode('读者注册成功'));
        } else {
            $this->redirect('/readers/create?error=' . urlencode($result['message'] ?? '注册失败'));
        }
    }

    /**
     * 启用/停用读者
     */
    public function toggleStatus(): void
    {
        $this->requireAdmin();

        $id     = (int)($_GET['id'] ?? 0);
        $status = (int)($_GET['status'] ?? 0);
        $result = $this->readerService->updateStatus($id, $status);

        if (($result['code'] ?? 500) === 200) {
            $this->redirect('/readers?success=' . urlencode($status == 1 ? '已启用' : '已停用'));
        } else {
            $this->redirect('/readers?error=' . urlencode($result['message'] ?? '操作失败'));
        }
    }

    /**
     * 删除读者
     */
    public function delete(): void
    {
        $this->requireAdmin();

        $id     = (int)($_GET['id'] ?? 0);
        $result = $this->readerService->delete($id);

        if (($result['code'] ?? 500) === 200) {
            $this->redirect('/readers?success=' . urlencode('读者已删除'));
        } else {
            $this->redirect('/readers?error=' . urlencode($result['message'] ?? '删除失败'));
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