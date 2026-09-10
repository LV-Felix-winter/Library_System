<?php include __DIR__ . '/../layout/header.php'; ?>

<h2><i class="bi bi-clock-history"></i> 借阅记录</h2>

<table class="table table-striped table-hover mt-3">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>读者</th>
            <th>图书</th>
            <th>借书日期</th>
            <th>应还日期</th>
            <th>状态</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($records)): ?>
        <tr><td colspan="6" class="text-center">暂无借阅记录</td></tr>
        <?php else: ?>
        <?php foreach ($records as $r): ?>
        <?php
        $statusMap   = [1 => '借阅中', 2 => '已还', 3 => '逾期', 4 => '丢失'];
        $statusClass = [1 => 'primary', 2 => 'success', 3 => 'danger', 4 => 'dark'];
        $s = $r['status'] ?? 1;
        ?>
        <tr>
            <td><?= $r['id'] ?></td>
            <td><?= htmlspecialchars($r['reader']['name'] ?? '-') ?></td>
            <td><?= htmlspecialchars($r['book']['title'] ?? '-') ?></td>
            <td><?= $r['borrow_date'] ?></td>
            <td><?= $r['due_date'] ?></td>
            <td><span class="badge bg-<?= $statusClass[$s] ?>"><?= $statusMap[$s] ?></span></td>
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