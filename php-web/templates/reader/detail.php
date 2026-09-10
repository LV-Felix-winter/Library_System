<?php include __DIR__ . '/../layout/header.php'; ?>

<?php if (empty($reader)): ?>
<div class="alert alert-warning">读者不存在。</div>
<?php else: ?>
<h2><i class="bi bi-person"></i> <?= htmlspecialchars($reader['name']) ?> 的信息</h2>

<div class="row mt-3">
    <div class="col-md-6">
        <table class="table table-bordered">
            <tr><th style="width: 120px;">借阅证号</th><td><?= htmlspecialchars($reader['card_no']) ?></td></tr>
            <tr><th>性别</th><td><?= [0 => '未知', 1 => '男', 2 => '女'][$reader['gender'] ?? 0] ?></td></tr>
            <tr><th>手机号</th><td><?= htmlspecialchars($reader['phone'] ?? '-') ?></td></tr>
            <tr><th>邮箱</th><td><?= htmlspecialchars($reader['email'] ?? '-') ?></td></tr>
            <tr><th>读者类型</th><td><?= [1 => '学生', 2 => '教师', 3 => '社会读者'][$reader['reader_type'] ?? 1] ?></td></tr>
            <tr><th>最大借阅</th><td><?= $reader['max_borrow'] ?? 5 ?> 本</td></tr>
            <tr><th>状态</th>
                <td><span class="badge bg-<?= ($reader['status'] ?? 1) == 1 ? 'success' : 'danger' ?>">
                    <?= ($reader['status'] ?? 1) == 1 ? '正常' : '停用' ?></span>
                </td>
            </tr>
        </table>
        <a href="/readers" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> 返回列表</a>
    </div>

    <!-- 借阅记录 -->
    <div class="col-md-6">
        <h4>借阅记录</h4>
        <?php if (empty($borrowRecords)): ?>
        <div class="alert alert-info">暂无借阅记录。</div>
        <?php else: ?>
        <div class="list-group">
            <?php foreach ($borrowRecords as $record): ?>
            <div class="list-group-item">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong><?= htmlspecialchars($record['book']['title'] ?? '未知图书') ?></strong>
                        <br>
                        <small class="text-muted">
                            借书：<?= $record['borrow_date'] ?> | 应还：<?= $record['due_date'] ?>
                        </small>
                    </div>
                    <div>
                        <?php
                        $statusMap = [1 => '借阅中', 2 => '已还', 3 => '逾期', 4 => '丢失'];
                        $statusClass = [1 => 'primary', 2 => 'success', 3 => 'danger', 4 => 'dark'];
                        $s = $record['status'] ?? 1;
                        ?>
                        <span class="badge bg-<?= $statusClass[$s] ?>"><?= $statusMap[$s] ?></span>
                        <?php if ($s == 1 || $s == 3): ?>
                        <a href="/borrow/renew?id=<?= $record['id'] ?>&reader_id=<?= $reader['id'] ?>"
                           class="btn btn-sm btn-outline-success">续借</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../layout/footer.php'; ?>