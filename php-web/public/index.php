<?php

/**
 * 图书馆借阅系统 — PHP Web 入口
 */

// 错误报告（开发环境打开）
error_reporting(E_ALL);
ini_set('display_errors', '1');

// 自动加载
spl_autoload_register(function (string $class) {
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }
    $relativeClass = substr($class, strlen($prefix));
    $file = __DIR__ . '/../src/' . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});
// 加载配置
$apiConfig = require __DIR__ . '/../config/api.php';

// 初始化 ApiClient
$client = new \App\Http\ApiClient($apiConfig['go_api_base_url']);

// 如果已登录，设置 token
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/** 输出 CSRF 隐藏字段（放进每个 <form> 里） */
function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="'
        . htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') . '">';
}

/** 校验 CSRF token（POST 请求调用） */
function csrf_check(): bool {
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        return true;
    }
    $token = $_POST['csrf_token'] ?? '';
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/** 输出转义（防 XSS，替代直接 echo） */
function e(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
if (!empty($_SESSION['token'])) {
    $client->setToken($_SESSION['token']);
}

// 初始化所有 Service
$bookService     = new \App\Service\BookService($client);
$readerService   = new \App\Service\ReaderService($client);
$borrowService   = new \App\Service\BorrowService($client);
$categoryService = new \App\Service\CategoryService($client);
$authService     = new \App\Service\AuthService($client);
$statsService    = new \App\Service\StatsService($client);
$adminService = new \App\Service\AdminService($client);

// 初始化所有 Controller
$bookController      = new \App\Controller\BookController($bookService, $categoryService);
$readerController    = new \App\Controller\ReaderController($readerService, $borrowService);
$borrowController    = new \App\Controller\BorrowController($borrowService);
$authController      = new \App\Controller\AuthController($authService);
$dashboardController = new \App\Controller\DashboardController($statsService, $borrowService);
$adminController     = new \App\Controller\AdminController($adminService);
$categoryController  = new \App\Controller\CategoryController($categoryService);
$readerAuthController = new \App\Controller\ReaderAuthController($readerService);

// 路由解析
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

switch ($uri) {
    // 首页仪表盘
    case '/':
        $dashboardController->index();
        $categoryController = new \App\Controller\CategoryController($categoryService);
        break;

    // 图书相关
    case '/books':
        $bookController->list();
        break;
    case '/books/detail':
        $bookController->detail();
        break;
    case '/books/create':
        $bookController->create();
        break;
    case '/books/store':
        $bookController->store();
        break;
    case '/books/edit':
        $bookController->edit();
        break;
    case '/books/update':
        $bookController->update();
        break;
    case '/books/delete':
        $bookController->delete();
        break;
    case '/books/search':
        $bookController->search();
        break;

    // 读者相关
    case '/readers':
        $readerController->list();
        break;
    case '/readers/detail':
        $readerController->detail();
        break;
    case '/readers/create':
        $readerController->create();
        break;
    case '/readers/store':
        $readerController->store();
        break;
    case '/readers/toggleStatus':
        $readerController->toggleStatus();
        break;
    case '/readers/delete':
        $readerController->delete();
        break;

    // 借阅相关
    case '/borrow':
        $borrowController->borrow();
        break;
    case '/borrow/doBorrow':
        $borrowController->doBorrow();
        break;
    case '/borrow/return':
        $borrowController->return();
        break;
    case '/borrow/doReturn':
        $borrowController->doReturn();
        break;
    case '/borrow/renew':
        $borrowController->renew();
        break;
    case '/borrow/overdue':
        $borrowController->overdue();
        break;
    case '/borrow/records':
        $borrowController->records();
        break;    

    // 登录相关
    case '/login':
        $authController->login();
        break;
    case '/doLogin':
        $authController->doLogin();
        break;
    case '/logout':
        $authController->logout();
        break;
    case '/change-password':
        $authController->changePassword();
        break;
    case '/doChangePassword':
        $authController->doChangePassword();
        break;

    case '/admins':
        $adminController->list();
        break;
    case '/admins/setRole':
        $adminController->setRole();
        break;

    case '/categories':
        $categoryController->list();
        break;
    case '/categories/store':
        $categoryController->store();
        break;
    case '/categories/update':
        $categoryController->update();
        break;
    case '/categories/delete':
        $categoryController->delete();
        break;

     // 读者相关
    case '/reader/login':
        $readerAuthController->login();
        break;
    case '/reader/doLogin':
        $readerAuthController->doLogin();
        break;
    case '/reader/logout':
        $readerAuthController->logout();
        break;
    case '/reader/portal':
        $readerAuthController->portal();
        break;
    case '/reader/doBorrow':
        $readerAuthController->doBorrow();
        break;
    case '/reader/doReturn':
        $readerAuthController->doReturn();
        break;
    case '/reader/doChangePassword':
        $readerAuthController->doChangePassword();
        break;

    // 404
    default:
        http_response_code(404);
        echo '<!DOCTYPE html><html><head><title>404</title>';
        echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">';
        echo '</head><body><div class="container mt-5 text-center">';
        echo '<h1>404</h1><p>页面不存在</p>';
        echo '<a href="/" class="btn btn-primary">返回首页</a>';
        echo '</div></body></html>';
        break;
}