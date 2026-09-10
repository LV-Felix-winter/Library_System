<?php

namespace App\Controller;

use App\Service\ReaderService;

class ReaderAuthController
{
    private ReaderService $readerService;

    public function __construct(ReaderService $readerService)
    {
        $this->readerService = $readerService;
    }

    /** 读者登录页 */
    public function login(): void
    {
        $pageTitle = '读者登录 - 图书馆借阅系统';
        $error     = $_GET['error'] ?? null;
        include __DIR__ . '/../../templates/reader/login.php';
    }

    /** 处理读者登录 */
    public function doLogin(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/reader/login');
            return;
        }
        if (!csrf_check()) {
            $this->redirect('/reader/login?error=' . urlencode('安全校验失败，请重试'));
            return;
        }
        $result = $this->readerService->login(
            $_POST['card_no'] ?? '',
            $_POST['password'] ?? ''
        );
        if (($result['code'] ?? 500) === 200) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['token']   = $result['data']['token'] ?? '';
            $_SESSION['card_no'] = $_POST['card_no'] ?? '';
            $_SESSION['role']    = 'reader';
            $this->redirect('/reader/portal');
        } else {
            $this->redirect('/reader/login?error=' . urlencode($result['message'] ?? '登录失败'));
        }
    }

    /** 读者退出 */
    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        $this->redirect('/reader/login?success=' . urlencode('已退出'));
    }

    /** 个人中心 */
    public function portal(): void
    {
        $this->requireReader();

        $recordsResult = $this->readerService->myRecords();
        $records       = $recordsResult['data'] ?? [];

        $pageTitle = '个人中心 - 图书馆借阅系统';
        $error     = $_GET['error'] ?? null;
        $success   = $_GET['success'] ?? null;

        include __DIR__ . '/../../templates/reader/portal.php';
    }

    /** 自助借书 */
    public function doBorrow(): void
    {
        $this->requireReader();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/reader/portal');
            return;
        }
        if (!csrf_check()) {
            $this->redirect('/reader/portal?error=' . urlencode('安全校验失败，请重试'));
            return;
        }
        $result = $this->readerService->borrowBook((int)($_POST['book_id'] ?? 0));
        if (($result['code'] ?? 500) === 200) {
            $this->redirect('/reader/portal?success=' . urlencode('借阅成功'));
        } else {
            $this->redirect('/reader/portal?error=' . urlencode($result['message'] ?? '借阅失败'));
        }
    }

    /** 自助还书 */
    public function doReturn(): void
    {
        $this->requireReader();
        $borrowId = (int)($_GET['borrow_id'] ?? 0);
        $result   = $this->readerService->returnBook($borrowId);
        if (($result['code'] ?? 500) === 200) {
            $this->redirect('/reader/portal?success=' . urlencode('归还成功'));
        } else {
            $this->redirect('/reader/portal?error=' . urlencode($result['message'] ?? '归还失败'));
        }
    }

    /** 修改自己的密码 */
    public function doChangePassword(): void
    {
        $this->requireReader();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/reader/portal');
            return;
        }
        if (!csrf_check()) {
            $this->redirect('/reader/portal?error=' . urlencode('安全校验失败，请重试'));
            return;
        }
        $result = $this->readerService->changePassword(
            $_POST['old_password'] ?? '',
            $_POST['new_password'] ?? ''
        );
        if (($result['code'] ?? 500) === 200) {
            $this->redirect('/reader/portal?success=' . urlencode('密码修改成功'));
        } else {
            $this->redirect('/reader/portal?error=' . urlencode($result['message'] ?? '修改失败'));
        }
    }

    private function requireReader(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (($_SESSION['role'] ?? '') !== 'reader') {
            $this->redirect('/reader/login?error=' . urlencode('请先登录'));
        }
    }

    private function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}