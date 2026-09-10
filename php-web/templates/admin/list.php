<?php include __DIR__ . '/../layout/header.php'; ?>

<h2><i class="bi bi-person-gear"></i> 管理员管理</h2>

<table class="table table-striped table-hover mt-3">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>用户名</th>
            <th>姓名</th>
            <th>当前权限</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($admins as $a): ?>
        <tr>
            <td><?= $a['id'] ?></td>
            <td><?= htmlspecialchars($a['username']) ?></td>
            <td><?= htmlspecialchars($a['real_name'] ?? '-') ?></td>
            <td>
                <span class="badge bg-<?= ($a['role'] ?? 2) == 1 ? 'primary' : 'secondary' ?>">
                    <?= ($a['role'] ?? 2) == 1 ? '管理员' : '普通用户' ?>
                </span>
            </td>
            <td>
                <?php if (($a['role'] ?? 2) == 1): ?>
                <a href="/admins/setRole?id=<?= $a['id'] ?>&role=2" class="btn btn-sm btn-outline-secondary"
                   onclick="return confirm('设为普通用户？')">设为普通用户</a>
                <?php else: ?>
                <a href="/admins/setRole?id=<?= $a['id'] ?>&role=1" class="btn btn-sm btn-outline-primary"
                   onclick="return confirm('设为管理员？')">设为管理员</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include __DIR__ . '/../layout/footer.php'; ?>