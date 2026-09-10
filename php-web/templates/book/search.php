<?php include __DIR__ . '/../layout/header.php'; ?>

<h2>搜索结果：<?= htmlspecialchars($keyword ?? '') ?></h2>

<div class="row mt-3">
    <?php if (empty($books)): ?>
    <div class="col-12">
        <div class="alert alert-info">没有找到相关图书。</div>
    </div>
    <?php else: ?>
    <?php foreach ($books as $book): ?>
    <div class="col-md-3 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <h5>
                    <a href="/books/detail?id=<?= $book['id'] ?>"><?= htmlspecialchars($book['title']) ?></a>
                </h5>
                <p class="text-muted"><?= htmlspecialchars($book['author']) ?></p>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<a href="/books" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> 返回图书列表</a>

<?php include __DIR__ . '/../layout/footer.php'; ?>