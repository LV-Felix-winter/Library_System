<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-body p-4">
                <h3 class="text-center mb-4">修改密码</h3>
                <form method="POST" action="/doChangePassword">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">旧密码</label>
                        <input type="password" class="form-control" name="old_password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">新密码</label>
                        <input type="password" class="form-control" name="new_password" required minlength="6">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">确认修改</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>