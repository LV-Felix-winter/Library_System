<?php include __DIR__ . '/../layout/header.php'; ?>

<h2><i class="bi bi-person"></i> 我的个人中心</h2>
<p class="text-muted">借书证号：<?= htmlspecialchars($_SESSION['card_no'] ?? '') ?></p>

<div class="row mt-3">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">自助借书</div>
            <div class="card-body">
                <form method="POST" action="/reader/doBorrow">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">图书 ID</label>
                        <input type="number" class="form-control" name="book_id" required>
                    </div>
                    <button class="btn btn-primary">确认借书</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">修改密码</div>
            <div class="card-body">
                <form method="POST" action="/reader/doChangePassword">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">旧密码</label>
                        <input type="password" class="form-control" name="old_password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">新密码</label>
                        <input type="password" class="form-control" name="new_password" required>
                    </div>
                    <button class="btn btn-warning">修改</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <h4>我的借阅记录</h4>
        <table class="table table-striped">
            <thead>
                <tr><th>书名</th><th>借阅日期</th><th>应还日期</th><th>状态</th><th>操作</th></tr>
            </thead>
            <tbody>
            <?php foreach ($records as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['book']['title'] ?? '未知图书') ?></td>
                    <td><?= htmlspecialchars($r['borrow_date'] ?? '') ?></td>
                    <td><?= htmlspecialchars($r['due_date'] ?? '') ?></td>
                    <td>
                        <?php if (($r['status'] ?? 0) == 1): ?>借阅中
                        <?php elseif (($r['status'] ?? 0) == 2): ?>已还
                        <?php else: ?>逾期<?php endif; ?>
                    </td>
                    <td>
                        <?php if (($r['status'] ?? 0) == 1): ?>
                        <a class="btn btn-sm btn-success" href="/reader/doReturn?borrow_id=<?= (int)$r['id'] ?>">归还</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>