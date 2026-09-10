<?php include __DIR__ . '/../layout/header.php'; ?>

<h2><i class="bi bi-arrow-right-circle"></i> 借书操作</h2>

<div class="row mt-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="/borrow/doBorrow">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">读者 ID *</label>
                        <input type="number" class="form-control" name="reader_id" required
                               placeholder="输入读者ID（如：1=张三）">
                        <small class="text-muted">去「读者管理」页面可查看每个读者的 ID</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">图书 ID *</label>
                        <input type="number" class="form-control" name="book_id" required
                               placeholder="输入图书ID（如：1=Go语言程序设计）">
                        <small class="text-muted">去「图书管理」页面可查看每本书的 ID</small>
                    </div>
                     <?= csrf_field() ?>
                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        <i class="bi bi-check-circle"></i> 确认借书
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="alert alert-info">
            <h5>借书规则</h5>
            <ul>
                <li>借阅期限 <strong>30 天</strong></li>
                <li>每个读者最多同时借阅 <strong>5-10 本</strong>（按读者类型）</li>
                <li>逾期罚款 <strong>每天 0.5 元</strong></li>
                <li>同一本书 <strong>库存为 0 时不能借</strong></li>
            </ul>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>