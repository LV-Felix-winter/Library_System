<?php include __DIR__ . '/../layout/header.php'; ?>

<?php if (empty($book)): ?>
<div class="alert alert-warning">图书不存在或已下架。</div>
<?php else: ?>
<div class="row">
    <div class="col-md-4 text-center mb-3">
        <div class="bg-light rounded p-4" style="min-height: 250px;">
            <i class="bi bi-book" style="font-size: 6rem; color: #ccc;"></i>
            <p class="text-muted mt-2">暂无封面</p>
        </div>
    </div>
    <div class="col-md-8">
        <h2><?= htmlspecialchars($book['title']) ?></h2>
        <table class="table table-bordered mt-3">
            <tr><th style="width: 120px;">作者</th><td><?= htmlspecialchars($book['author']) ?></td></tr>
            <tr><th>ISBN</th><td><?= htmlspecialchars($book['isbn']) ?></td></tr>
            <tr><th>出版社</th><td><?= htmlspecialchars($book['publisher'] ?? '未知') ?></td></tr>
            <tr><th>出版年份</th><td><?= htmlspecialchars($book['publish_year'] ?? '-') ?></td></tr>
            <tr><th>定价</th><td>¥<?= number_format($book['price'] ?? 0, 2) ?></td></tr>
            <tr><th>总册数</th><td><?= $book['total_copies'] ?? 0 ?> 册</td></tr>
            <tr>
                <th>可借册数</th>
                <td>
                    <span class="badge bg-<?= ($book['available_copies'] ?? 0) > 0 ? 'success' : 'danger' ?> fs-6">
                        <?= $book['available_copies'] ?? 0 ?> 册
                    </span>
                </td>
            </tr>
            <tr><th>书架位置</th><td><?= htmlspecialchars($book['shelf_location'] ?? '未指定') ?></td></tr>
            <tr><th>简介</th><td><?= htmlspecialchars($book['description'] ?? '暂无简介') ?></td></tr>
        </table>

        <div class="mt-3">
            <a href="/books/edit?id=<?= $book['id'] ?>" class="btn btn-warning">
                <i class="bi bi-pencil"></i> 编辑
            </a>
            <a href="/books/delete?id=<?= $book['id'] ?>" class="btn btn-danger"
               onclick="return confirm('确定下架这本书吗？')">
                <i class="bi bi-trash"></i> 下架
            </a>
            <a href="/books" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> 返回列表
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../layout/footer.php'; ?>