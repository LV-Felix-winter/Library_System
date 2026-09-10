<?php

namespace App\Controller;

use App\Service\CategoryService;

class CategoryController
{
    private CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * 分类管理页
     */
    public function list(): void
    {
        $this->requireAdmin();

        $result     = $this->categoryService->all();
        $categories = $result['data'] ?? [];

        $pageTitle = '分类管理 - 图书馆借阅系统';
        $error     = $_GET['error'] ?? null;
        $success   = $_GET['success'] ?? null;

        include __DIR__ . '/../../templates/category/list.php';
    }

    /**
     * 新增分类
     */
    public function store(): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/categories');
            return;
        }
        if (!csrf_check()) {
            $this->redirect('/categories?error=' . urlencode('安全校验失败'));
            return;
        }
        $data = [
            'name'      => $_POST['name'] ?? '',
            'parent_id' => (int)($_POST['parent_id'] ?? 0),
            'sort_order'=> (int)($_POST['sort_order'] ?? 0),
        ];
        $result = $this->categoryService->create($data);
        if (($result['code'] ?? 500) === 200) {
            $this->redirect('/categories?success=' . urlencode('分类已添加'));
        } else {
            $this->redirect('/categories?error=' . urlencode($result['message'] ?? '添加失败'));
        }
    }

    /**
     * 更新分类
     */
    public function update(): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/categories');
            return;
        }
        if (!csrf_check()) {
            $this->redirect('/categories?error=' . urlencode('安全校验失败'));
            return;
        }
        $id   = (int)($_POST['id'] ?? 0);
        $data = [
            'name'      => $_POST['name'] ?? '',
            'parent_id' => (int)($_POST['parent_id'] ?? 0),
            'sort_order'=> (int)($_POST['sort_order'] ?? 0),
        ];
        $result = $this->categoryService->update($id, $data);
        if (($result['code'] ?? 500) === 200) {
            $this->redirect('/categories?success=' . urlencode('分类已更新'));
        } else {
            $this->redirect('/categories?error=' . urlencode($result['message'] ?? '更新失败'));
        }
    }

    /**
     * 删除分类
     */
    public function delete(): void
    {
        $this->requireAdmin();

        $id     = (int)($_GET['id'] ?? 0);
        $result = $this->categoryService->delete($id);
        if (($result['code'] ?? 500) === 200) {
            $this->redirect('/categories?success=' . urlencode('分类已删除'));
        } else {
            $this->redirect('/categories?error=' . urlencode($result['message'] ?? '删除失败'));
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