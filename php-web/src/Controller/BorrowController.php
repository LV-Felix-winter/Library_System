<?php

namespace App\Controller;

use App\Service\BorrowService;

class BorrowController
{
    private BorrowService $borrowService;

    public function __construct(BorrowService $borrowService)
    {
        $this->borrowService = $borrowService;
    }

    /**
     * 借书页面
     */
    public function borrow(): void
    {
        $this->requireAdmin();

        $pageTitle = '借书操作 - 图书馆借阅系统';
        $error     = $_GET['error'] ?? null;
        $success   = $_GET['success'] ?? null;

        include __DIR__ . '/../../templates/borrow/borrow.php';
    }

    /**
     * 处理借书提交
     */
    public function doBorrow(): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/borrow');
            return;
        }

         if (!csrf_check()) {
            $this->redirect('XXX?error=' . urlencode('安全校验失败，请重新提交'));
            return;
        }

        $readerId = (int)($_POST['reader_id'] ?? 0);
        $bookId   = (int)($_POST['book_id'] ?? 0);

        $result = $this->borrowService->borrow($readerId, $bookId);

        if (($result['code'] ?? 500) === 200) {
            $this->redirect('/borrow?success=' . urlencode('借阅成功！'));
        } else {
            $this->redirect('/borrow?error=' . urlencode($result['message'] ?? '借阅失败'));
        }
    }

    /**
     * 还书页面
     */
    public function return(): void
    {
        $this->requireAdmin();

        $pageTitle = '还书操作 - 图书馆借阅系统';
        $error     = $_GET['error'] ?? null;
        $success   = $_GET['success'] ?? null;

        include __DIR__ . '/../../templates/borrow/return_book.php';
    }

    /**
     * 处理还书提交
     */
    public function doReturn(): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/borrow/return');
            return;
        }

         if (!csrf_check()) {
            $this->redirect('XXX?error=' . urlencode('安全校验失败，请重新提交'));
            return;
        }

        $borrowId = (int)($_POST['borrow_id'] ?? 0);

        $result = $this->borrowService->returnBook($borrowId);

        if (($result['code'] ?? 500) === 200) {
            $this->redirect('/borrow/return?success=' . urlencode('归还成功！'));
        } else {
            $this->redirect('/borrow/return?error=' . urlencode($result['message'] ?? '归还失败'));
        }
    }

    /**
     * 续借
     */
    public function renew(): void
    {
        $this->requireAdmin();

        $borrowId = (int)($_GET['id'] ?? 0);

        $result = $this->borrowService->renew($borrowId);

        if (($result['code'] ?? 500) === 200) {
            $this->redirect('/readers/detail?id=' . ($_GET['reader_id'] ?? 0) . '&success=' . urlencode('续借成功'));
        } else {
            $this->redirect('/readers/detail?id=' . ($_GET['reader_id'] ?? 0) . '&error=' . urlencode($result['message'] ?? '续借失败'));
        }
    }

    /**
     * 逾期列表
     */
    public function overdue(): void
    {
        $this->requireAdmin();

        $result  = $this->borrowService->overdue();
        $records = $result['data'] ?? [];

        $pageTitle = '逾期未还 - 图书馆借阅系统';

        include __DIR__ . '/../../templates/borrow/overdue.php';
    }

     /**
     * 借阅记录列表（分页）
     */
    public function records(): void
    {
        $this->requireAdmin();

        $page       = (int)($_GET['page'] ?? 1);
        $result     = $this->borrowService->allRecords($page);
        $records    = $result['data'] ?? [];
        $total      = $result['total'] ?? 0;
        $totalPages = (int)ceil($total / 10);

        $pageTitle = '借阅记录 - 图书馆借阅系统';

        include __DIR__ . '/../../templates/borrow/records.php';
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