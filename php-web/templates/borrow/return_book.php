<?php include __DIR__ . '/../layout/header.php'; ?>

<h2><i class="bi bi-arrow-left-circle"></i> 还书操作</h2>

<div class="row mt-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="/borrow/doReturn">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">借阅记录 ID *</label>
                        <input type="number" class="form-control" name="borrow_id" required
                               placeholder="输入借阅记录ID">
                        <small class="text-muted">
                            去「读者管理」→ 点击读者「详情」→ 查看该读者的借阅记录中找到相应的借阅记录 ID
                        </small>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        <i class="bi bi-check-circle"></i> 确认还书
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="alert alert-warning">
            <h5>还书说明</h5>
            <ul>
                <li>还书后库存自动恢复</li>
                <li><strong>逾期会自动计算罚款</strong>（每天 0.5 元）</li>
                <li>罚款记录会写入 fines 表</li>
                <li>丢失的书请联系管理员处理</li>
            </ul>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
