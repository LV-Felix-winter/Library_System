<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body text-center">
                <i class="bi bi-journal-bookmark" style="font-size: 2rem;"></i>
                <h3 class="mt-2"><?= $stats['total_books'] ?></h3>
                <p class="mb-0">馆藏图书</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body text-center">
                <i class="bi bi-check-circle" style="font-size: 2rem;"></i>
                <h3 class="mt-2"><?= $stats['available_books'] ?></h3>
                <p class="mb-0">可借图书</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body text-center">
                <i class="bi bi-book" style="font-size: 2rem;"></i>
                <h3 class="mt-2"><?= $stats['active_borrows'] ?></h3>
                <p class="mb-0">借阅中</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-danger">
            <div class="card-body text-center">
                <i class="bi bi-exclamation-triangle" style="font-size: 2rem;"></i>
                <h3 class="mt-2"><?= $stats['overdue_borrows'] ?></h3>
                <p class="mb-0">逾期</p>
            </div>
        </div>
    </div>
</div>
<?php if (!empty($reminders)): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-warning">
            <div class="card-header bg-warning text-dark">
                <i class="bi bi-bell"></i> 归还期限提醒（3 天内到期）
            </div>
            <ul class="list-group list-group-flush">
                <?php foreach ($reminders as $r): ?>
                <li class="list-group-item d-flex justify-content-between">
                    <span>
                        <strong><?= htmlspecialchars($r['book']['title'] ?? '未知图书') ?></strong>
                        — 读者：<?= htmlspecialchars($r['reader']['name'] ?? '-') ?>
                    </span>
                    <span class="text-danger">应还：<?= htmlspecialchars($r['due_date'] ?? '') ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
<?php endif; ?>


<div class="row">
    <div class="col-md-6">
        <h4>快捷操作</h4>
        <div class="list-group">
            <a href="/books" class="list-group-item list-group-item-action">
                <i class="bi bi-journal-bookmark"></i> 查看所有图书
            </a>
            <a href="/books/create" class="list-group-item list-group-item-action">
                <i class="bi bi-plus-circle"></i> 录入新书
            </a>
            <a href="/borrow" class="list-group-item list-group-item-action">
                <i class="bi bi-arrow-right-circle"></i> 借书操作
            </a>
            <a href="/borrow/return" class="list-group-item list-group-item-action">
                <i class="bi bi-arrow-left-circle"></i> 还书操作
            </a>
            <a href="/borrow/overdue" class="list-group-item list-group-item-action">
                <i class="bi bi-exclamation-triangle"></i> 查看逾期列表
            </a>
        </div>
    </div>
    <div class="col-md-6">
        <h4>系统说明</h4>
        <div class="card">
            <div class="card-body">
                <ul>
                    <li>每个读者最多借阅 <strong>5-10</strong> 本（按读者类型）</li>
                    <li>借阅期限 <strong>30 天</strong>，可续借最多 2 次</li>
                    <li>逾期罚款 <strong>每天 0.5 元</strong></li>
                    <li>管理员后台支持图书录入、读者注册、借还书操作</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>