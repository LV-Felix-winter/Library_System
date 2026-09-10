<?php include __DIR__ . '/../layout/header.php'; ?>

<h2><i class="bi bi-tags"></i> 分类管理</h2>

<div class="row mt-3">
    <div class="col-md-5">
        <div class="card">
            <div class="card-body">
                <h5>新增分类</h5>
                <form method="POST" action="/categories/store">
                    <?= csrf_field() ?>
                    <div class="mb-2">
                        <input type="text" class="form-control" name="name" placeholder="分类名称" required>
                    </div>
                    <div class="mb-2">
                        <input type="number" class="form-control" name="sort_order" placeholder="排序（数字越小越靠前）" value="0">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">添加</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr><th>ID</th><th>名称</th><th>排序</th><th>操作</th></tr>
            </thead>
            <tbody>
                <?php if (empty($categories)): ?>
                <tr><td colspan="4" class="text-center">暂无分类</td></tr>
                <?php else: ?>
                <?php foreach ($categories as $cat): ?>
                <tr>
                    <td><?= $cat['id'] ?></td>
                    <td><?= htmlspecialchars($cat['name']) ?></td>
                    <td><?= $cat['sort_order'] ?? 0 ?></td>
                    <td>
                        <form method="POST" action="/categories/update" class="d-inline">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                            <input type="text" name="name" value="<?= htmlspecialchars($cat['name']) ?>" style="width:120px">
                            <button type="submit" class="btn btn-sm btn-outline-secondary">改名</button>
                        </form>
                        <a href="/categories/delete?id=<?= $cat['id'] ?>" class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('确定删除该分类吗？')">删除</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>