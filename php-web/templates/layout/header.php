<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? '图书馆借阅系统') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
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