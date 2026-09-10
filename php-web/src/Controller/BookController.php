<?php

namespace App\Controller;

use App\Service\BookService;
use App\Service\CategoryService;

class BookController
{
    private BookService $bookService;
    private CategoryService $categoryService;

    public function __construct(BookService $bookService, CategoryService $categoryService)
    {
        $this->bookService     = $bookService;
        $this->categoryService = $categoryService;
    }

    /**
     * 图书列表页
     */
    public function list(): void
    {
        $page       = (int)($_GET['page'] ?? 1);
        $keyword    = $_GET['keyword'] ?? '';
        $categoryId = (int)($_GET['category_id'] ?? 0);

        $result       = $this->bookService->list($page, 12, $keyword, $categoryId);
        $books        = $result['data'] ?? [];
        $total        = $result['total'] ?? 0;
        $totalPages   = (int)ceil($total / 12);

        $catResult    = $this->categoryService->all();
        $categories   = $catResult['data'] ?? [];

        $pageTitle = '图书列表 - 图书馆借阅系统';
        $error     = $_GET['error'] ?? null;
        $success   = $_GET['success'] ?? null;

        include __DIR__ . '/../../templates/book/list.php';
    }

    /**
     * 图书详情页
     */
    public function detail(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            $this->redirect('/');
            return;
        }

        $result = $this->bookService->detail($id);
        $book   = $result['data'] ?? null;

        if (!$book) {
            $this->redirect('/');
            return;
        }

        $pageTitle = ($book['title'] ?? '图书详情') . ' - 图书馆借阅系统';
        $success   = $_GET['success'] ?? null;
        $error     = $_GET['error'] ?? null;

        include __DIR__ . '/../../templates/book/detail.php';
    }

    /**
     * 新书录入页面
     */
    public function create(): void
    {
        $this->requireAdmin();

        $catResult  = $this->categoryService->all();
        $categories = $catResult['data'] ?? [];

        $pageTitle = '新书录入 - 图书馆借阅系统';
        $error     = $_GET['error'] ?? null;

        include __DIR__ . '/../../templates/book/create.php';
    }

    /**
     * 处理新书录入提交
     */
    public function store(): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/books/create');
            return;
        }

         if (!csrf_check()) {
            $this->redirect('XXX?error=' . urlencode('安全校验失败，请重新提交'));
            return;
        }

        $data = [
            'isbn'             => $_POST['isbn'] ?? '',
            'title'            => $_POST['title'] ?? '',
            'author'           => $_POST['author'] ?? '',
            'publisher'        => $_POST['publisher'] ?? '',
            'publish_year'     => (int)($_POST['publish_year'] ?? 0),
            'category_id'      => (int)($_POST['category_id'] ?? 0),
            'total_copies'     => (int)($_POST['total_copies'] ?? 1),
            'available_copies' => (int)($_POST['total_copies'] ?? 1),
            'price'            => (float)($_POST['price'] ?? 0),
            'shelf_location'   => $_POST['shelf_location'] ?? '',
            'description'      => $_POST['description'] ?? '',
        ];

        $result = $this->bookService->create($data);

        if (($result['code'] ?? 500) === 200) {
            $this->redirect('/books?success=' . urlencode('图书添加成功'));
        } else {
            $this->redirect('/books/create?error=' . urlencode($result['message'] ?? '添加失败'));
        }
    }

    /**
     * 编辑图书页面
     */
    public function edit(): void
    {
        $this->requireAdmin();

        $id     = (int)($_GET['id'] ?? 0);
        $result = $this->bookService->detail($id);
        $book   = $result['data'] ?? null;

        if (!$book) {
            $this->redirect('/books');
            return;
        }

        $catResult  = $this->categoryService->all();
        $categories = $catResult['data'] ?? [];

        $pageTitle = '编辑图书 - 图书馆借阅系统';
        $error     = null;

        include __DIR__ . '/../../templates/book/edit.php';
    }

    /**
     * 处理编辑图书提交
     */
    public function update(): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/books');
            return;
        }

         if (!csrf_check()) {
            $this->redirect('XXX?error=' . urlencode('安全校验失败，请重新提交'));
            return;
        }

        $id   = (int)($_POST['id'] ?? 0);
        $data = [
            'isbn'             => $_POST['isbn'] ?? '',
            'title'            => $_POST['title'] ?? '',
            'author'           => $_POST['author'] ?? '',
            'publisher'        => $_POST['publisher'] ?? '',
            'publish_year'     => (int)($_POST['publish_year'] ?? 0),
            'category_id'      => (int)($_POST['category_id'] ?? 0),
            'total_copies'     => (int)($_POST['total_copies'] ?? 1),
            'available_copies' => (int)($_POST['available_copies'] ?? 0),
            'price'            => (float)($_POST['price'] ?? 0),
            'shelf_location'   => $_POST['shelf_location'] ?? '',
            'description'      => $_POST['description'] ?? '',
        ];

        $result = $this->bookService->update($id, $data);

        if (($result['code'] ?? 500) === 200) {
            $this->redirect('/books/detail?id=' . $id . '&success=' . urlencode('图书更新成功'));
        } else {
            $this->redirect('/books/edit?id=' . $id . '&error=' . urlencode($result['message'] ?? '更新失败'));
        }
    }

    /**
     * 下架图书
     */
    public function delete(): void
    {
        $this->requireAdmin();

        $id     = (int)($_GET['id'] ?? 0);
        $result = $this->bookService->delete($id);

        if (($result['code'] ?? 500) === 200) {
            $this->redirect('/books?success=' . urlencode('图书已下架'));
        } else {
            $this->redirect('/books?error=' . urlencode($result['message'] ?? '下架失败'));
        }
    }

    /**
     * 搜索图书
     */
    public function search(): void
    {
        $keyword = $_GET['keyword'] ?? '';
        $result  = $this->bookService->search($keyword);
        $books   = $result['data'] ?? [];

        $pageTitle = '搜索结果：' . htmlspecialchars($keyword);
        include __DIR__ . '/../../templates/book/search.php';
    }

    /**
     * 跳转到指定 URL
     */
    private function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    /**
     * 检查是否已登录（管理员权限）
     */
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