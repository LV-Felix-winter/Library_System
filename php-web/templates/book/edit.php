<?php include __DIR__ . '/../layout/header.php'; ?>

<?php if (empty($book)): ?>
<div class="alert alert-warning">图书不存在。</div>
<?php else: ?>
<h2><i class="bi bi-pencil"></i> 编辑图书</h2>

<form method="POST" action="/books/update" class="mt-3">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= $book['id'] ?>">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">ISBN *</label>
            <input type="text" class="form-control" name="isbn" required value="<?= htmlspecialchars($book['isbn']) ?>">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">书名 *</label>
            <input type="text" class="form-control" name="title" required value="<?= htmlspecialchars($book['title']) ?>">
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">作者 *</label>
            <input type="text" class="form-control" name="author" required value="<?= htmlspecialchars($book['author']) ?>">
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">出版社</label>
            <input type="text" class="form-control" name="publisher" value="<?= htmlspecialchars($book['publisher'] ?? '') ?>">
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">出版年份</label>
            <input type="number" class="form-control" name="publish_year" min="1900" max="2099"
                   value="<?= $book['publish_year'] ?? '' ?>">
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">分类</label>
            <select class="form-select" name="category_id">
                <option value="0">请选择分类</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= ($book['category_id'] ?? 0) == $cat['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">总册数</label>
            <input type="number" class="form-control" name="total_copies" value="<?= $book['total_copies'] ?? 1 ?>" min="1">
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">可借册数</label>
            <input type="number" class="form-control" name="available_copies" value="<?= $book['available_copies'] ?? 0 ?>" min="0">
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">定价（元）</label>
            <input type="number" class="form-control" name="price" step="0.01" min="0" value="<?= $book['price'] ?? '0.00' ?>">
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">书架位置</label>
            <input type="text" class="form-control" name="shelf_location" value="<?= htmlspecialchars($book['shelf_location'] ?? '') ?>">
        </div>
        <div class="col-12 mb-3">
            <label class="form-label">内容简介</label>
            <textarea class="form-control" name="description" rows="3"><?= htmlspecialchars($book['description'] ?? '') ?></textarea>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-lg"></i> 保存修改
    </button>
    <a href="/books/detail?id=<?= $book['id'] ?>" class="btn btn-secondary">取消</a>
</form>
<?php endif; ?>

<?php include __DIR__ . '/../layout/footer.php'; ?>