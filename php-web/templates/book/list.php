<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="row mb-3">
    <div class="col-md-8">
        <h2><i class="bi bi-journal-bookmark"></i> 图书列表</h2>
    </div>
    <div class="col-md-4 text-end">
        <a href="/books/create" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> 录入新书
        </a>
    </div>
</div>

<!-- 搜索和筛选 -->
<form class="row g-3 mb-4" method="GET" action="/books">
    <div class="col-md-6">
        <input type="text" class="form-control" name="keyword" placeholder="输入书名或作者搜索..."
               value="<?= htmlspecialchars($keyword ?? '') ?>">
    </div>
    <div class="col-md-3">
        <select class="form-select" name="category_id">
            <option value="0">全部分类</option>
            <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= ($categoryId ?? 0) == $cat['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['name']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3">
        <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-search"></i> 搜索
        </button>
    </div>
</form>

<!-- 图书列表 -->
<div class="row">
    <?php if (empty($books)): ?>
    <div class="col-12">
        <div class="alert alert-info">暂无图书数据。</div>
    </div>
    <?php else: ?>
    <?php foreach ($books as $book): ?>
    <div class="col-md-3 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">
                    <a href="/books/detail?id=<?= $book['id'] ?>" class="text-decoration-none">
                        <?= htmlspecialchars($book['title']) ?>
                    </a>
                </h5>
                <p class="card-text text-muted"><?= htmlspecialchars($book['author']) ?></p>
                <p class="card-text small">
                    <span class="badge bg-secondary"><?= htmlspecialchars($book['publisher'] ?? '未知') ?></span>
                    <span class="badge bg-info"><?= htmlspecialchars($book['publish_year'] ?? '-') ?></span>
                </p>
                <p class="card-text">
                    <span class="text-<?= ($book['available_copies'] ?? 0) > 0 ? 'success' : 'danger' ?>">
                        <?= ($book['available_copies'] ?? 0) > 0 ? '📗 可借 ' . $book['available_copies'] . ' 本' : '📕 暂无可借' ?>
                    </span>
                </p>
            </div>
            <div class="card-footer">
                <a href="/books/detail?id=<?= $book['id'] ?>" class="btn btn-sm btn-outline-primary">详情</a>
                <a href="/books/edit?id=<?= $book['id'] ?>" class="btn btn-sm btn-outline-secondary">编辑</a>
                <a href="/books/delete?id=<?= $book['id'] ?>" class="btn btn-sm btn-outline-danger"
                   onclick="return confirm('确定下架这本书吗？')">下架</a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- 分页 -->
<?php if (($totalPages ?? 1) > 1): ?>
<nav>
    <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?= $i === ($page ?? 1) ? 'active' : '' ?>">
            <a class="page-link" href="?page=<?= $i ?>&keyword=<?= urlencode($keyword ?? '') ?>&category_id=<?= $categoryId ?? 0 ?>">
                <?= $i ?>
            </a>
        </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<?php include __DIR__ . '/../layout/footer.php'; ?>