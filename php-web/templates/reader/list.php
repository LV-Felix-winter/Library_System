<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="row mb-3">
    <div class="col">
        <h2><i class="bi bi-people"></i> 读者管理</h2>
    </div>
    <div class="col text-end">
        <a href="/readers/create" class="btn btn-success">
            <i class="bi bi-person-plus"></i> 注册新读者
        </a>
    </div>
</div>

<table class="table table-striped table-hover">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>借阅证号</th>
            <th>姓名</th>
            <th>性别</th>
            <th>手机号</th>
            <th>读者类型</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($readers)): ?>
        <tr><td colspan="8" class="text-center">暂无读者数据</td></tr>
        <?php else: ?>
        <?php foreach ($readers as $reader): ?>
        <tr>
            <td><?= $reader['id'] ?></td>
            <td><?= htmlspecialchars($reader['card_no']) ?></td>
            <td><?= htmlspecialchars($reader['name']) ?></td>
            <td>
                <?php
                $genders = [0 => '未知', 1 => '男', 2 => '女'];
                echo $genders[$reader['gender'] ?? 0] ?? '未知';
                ?>
            </td>
            <td><?= htmlspecialchars($reader['phone'] ?? '-') ?></td>
            <td>
                <?php
                $types = [1 => '学生', 2 => '教师', 3 => '社会读者'];
                echo $types[$reader['reader_type'] ?? 1] ?? '未知';
                ?>
            </td>
            <td>
                <span class="badge bg-<?= ($reader['status'] ?? 1) == 1 ? 'success' : 'danger' ?>">
                    <?= ($reader['status'] ?? 1) == 1 ? '正常' : '停用' ?>
                </span>
            </td>
            <td>
                <a href="/readers/detail?id=<?= $reader['id'] ?>" class="btn btn-sm btn-outline-primary">详情</a>
                <?php if (($reader['status'] ?? 1) == 1): ?>
                <a href="/readers/toggleStatus?id=<?= $reader['id'] ?>&status=0" class="btn btn-sm btn-outline-warning"
                   onclick="return confirm('确定停用该读者吗？')">停用</a>
                <?php else: ?>
                <a href="/readers/toggleStatus?id=<?= $reader['id'] ?>&status=1" class="btn btn-sm btn-outline-success"
                   onclick="return confirm('确定启用该读者吗？')">启用</a>
                <?php endif; ?>
                <a href="/readers/delete?id=<?= $reader['id'] ?>" class="btn btn-sm btn-outline-danger"
                   onclick="return confirm('确定删除该读者吗？删除后不可恢复。')">删除</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php if (($totalPages ?? 1) > 1): ?>
<nav>
    <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?= $i === ($page ?? 1) ? 'active' : '' ?>">
            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
        </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<?php include __DIR__ . '/../layout/footer.php'; ?>