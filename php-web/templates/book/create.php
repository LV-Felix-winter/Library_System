<?php include __DIR__ . '/../layout/header.php'; ?>

<h2><i class="bi bi-plus-circle"></i> 新书录入</h2>

<form method="POST" action="/books/store" class="mt-3">
    <?= csrf_field() ?>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">ISBN *</label>
            <input type="text" class="form-control" name="isbn" required placeholder="如：978-7-111-68888-8">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">书名 *</label>
            <input type="text" class="form-control" name="title" required>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">作者 *</label>
            <input type="text" class="form-control" name="author" required>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">出版社</label>
            <input type="text" class="form-control" name="publisher">
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">出版年份</label>
            <input type="number" class="form-control" name="publish_year" min="1900" max="2099">
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">分类</label>
            <select class="form-select" name="category_id">
                <option value="0">请选择分类</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">总册数</label>
            <input type="number" class="form-control" name="total_copies" value="1" min="1">
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">定价（元）</label>
            <input type="number" class="form-control" name="price" step="0.01" min="0" placeholder="0.00">
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">书架位置</label>
            <input type="text" class="form-control" name="shelf_location" placeholder="如：A-01-01">
        </div>
        <div class="col-12 mb-3">
            <label class="form-label">内容简介</label>
            <textarea class="form-control" name="description" rows="3"></textarea>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-lg"></i> 提交
    </button>
    <a href="/books" class="btn btn-secondary">
        <i class="bi bi-x-lg"></i> 取消
    </a>
</form>

<?php include __DIR__ . '/../layout/footer.php'; ?>