<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? '图书馆借阅系统') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<style>
    /* ===== 全局 ===== */
    body { background-color: #f5f6fa; }
    .page-header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: .5rem; margin-bottom: 1rem; }

    /* ===== 卡片 ===== */
    .card { border: none; border-radius: .75rem; box-shadow: 0 2px 8px rgba(0,0,0,.06); }
    .card .card-title a { color: #212529; }
    .card .card-title a:hover { color: #0d6efd; }

    /* ===== 图书封面 ===== */
    .book-cover { width: 100%; height: 180px; object-fit: cover; border-radius: .5rem .5rem 0 0; background: #e9ecef; }
    .book-cover-placeholder { width: 100%; height: 180px; display: flex; align-items: center; justify-content: center; color: #adb5bd; background: #f1f3f5; border-radius: .5rem .5rem 0 0; }
    .book-cover-placeholder i { font-size: 3rem; }

    /* ===== 导航 ===== */
    .navbar .nav-link.active { font-weight: 600; }
    .navbar .nav-link.active::after { content: ""; display: block; height: 2px; background: #fff; margin-top: 2px; }

    /* ===== 统计卡片 ===== */
    .stat-card { transition: transform .15s ease, box-shadow .15s ease; }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,.12); }
    .stat-card .stat-icon { font-size: 2rem; opacity: .9; }

    /* ===== 表格 ===== */
    .table > :not(caption) > * > * { vertical-align: middle; }
    .table-actions { white-space: nowrap; }
    .table-actions .btn { margin: 1px 0; }

    /* ===== 空状态 ===== */
    .empty-state { text-align: center; padding: 3rem 1rem; color: #6c757d; }
    .empty-state i { font-size: 3rem; display: block; margin-bottom: .5rem; color: #ced4da; }

    /* ===== 表单 ===== */
    .form-label.required::after { content: " *"; color: #dc3545; }
</style>
    </style>
</head>
<body>

<!-- 导航栏 -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand" href="/">
            <i class="bi bi-book"></i> 图书馆借阅系统
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
             <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/"><i class="bi bi-speedometer2"></i> 首页</a>
                </li>
                <?php if (($_SESSION['role'] ?? '') === 'reader'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/books"><i class="bi bi-journal-bookmark"></i> 图书列表</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/reader/portal"><i class="bi bi-person"></i> 个人中心</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/books"><i class="bi bi-journal-bookmark"></i> 图书管理</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/readers"><i class="bi bi-people"></i> 读者管理</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/borrow"><i class="bi bi-arrow-right-circle"></i> 借书</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/borrow/return"><i class="bi bi-arrow-left-circle"></i> 还书</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/borrow/overdue"><i class="bi bi-exclamation-triangle"></i> 逾期</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/borrow/records"><i class="bi bi-clock-history"></i> 借阅记录</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admins"><i class="bi bi-person-gear"></i> 管理员管理</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/categories"><i class="bi bi-tags"></i> 分类管理</a>
                    </li>
                <?php endif; ?>
            </ul>
             <ul class="navbar-nav">
                <?php if (($_SESSION['role'] ?? '') === 'reader'): ?>
                <li class="nav-item">
                    <span class="nav-link text-light">
                        <i class="bi bi-person-check"></i> <?= htmlspecialchars($_SESSION['card_no'] ?? '读者') ?>
                    </span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/reader/logout"><i class="bi bi-box-arrow-right"></i> 退出</a>
                </li>
                <?php elseif (!empty($_SESSION['token'])): ?>
                <li class="nav-item">
                    <span class="nav-link text-light">
                        <i class="bi bi-person-check"></i> <?= htmlspecialchars($_SESSION['username'] ?? '管理员') ?>
                    </span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/change-password"><i class="bi bi-key"></i> 修改密码</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/logout"><i class="bi bi-box-arrow-right"></i> 退出</a>
                </li>
                <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="/reader/login"><i class="bi bi-person"></i> 读者登录</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/login"><i class="bi bi-box-arrow-in-right"></i> 管理员登录</a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <!-- 提示消息 -->
    <?php if (!empty($success)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> <?= htmlspecialchars($success) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-x-circle"></i> <?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>