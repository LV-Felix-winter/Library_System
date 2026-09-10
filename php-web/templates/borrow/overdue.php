<?php include __DIR__ . '/../layout/header.php'; ?>

<h2><i class="bi bi-exclamation-triangle"></i> 逾期未还</h2>

<table class="table table-striped table-hover mt-3">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>读者</th>
            <th>图书</th>
            <th>借书日期</th>
            <th>应还日期</th>
            <th>罚款金额</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($records)): ?>
        <tr><td colspan="7" class="text-center">
            <div class="alert alert-success mb-0">🎉 没有逾期记录！</div>
        </td></tr>
        <?php else: ?>
        <?php foreach ($records as $r): ?>
        <tr>
            <td><?= $r['id'] ?></td>
            <td><?= htmlspecialchars($r['reader']['name'] ?? '-') ?></td>
            <td><?= htmlspecialchars($r['book']['title'] ?? '-') ?></td>
            <td><?= $r['borrow_date'] ?></td>
            <td class="text-danger"><strong><?= $r['due_date'] ?></strong></td>
            <td>¥<?= number_format($r['fine_amount'] ?? 0, 2) ?></td>
            <td>
                <a href="/readers/detail?id=<?= $r['reader_id'] ?>" class="btn btn-sm btn-outline-primary">查看读者</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php include __DIR__ . '/../layout/footer.php'; ?>