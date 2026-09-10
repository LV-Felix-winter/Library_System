<?php include __DIR__ . '/../layout/header.php'; ?>

<h2><i class="bi bi-person-plus"></i> 读者注册</h2>

<form method="POST" action="/readers/store" class="mt-3">
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">姓名 *</label>
            <input type="text" class="form-control" name="name" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">性别</label>
            <select class="form-select" name="gender">
                <option value="0">未知</option>
                <option value="1">男</option>
                <option value="2">女</option>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">手机号</label>
            <input type="text" class="form-control" name="phone">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">邮箱</label>
            <input type="email" class="form-control" name="email">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">身份证号</label>
            <input type="text" class="form-control" name="id_card">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">读者类型</label>
            <select class="form-select" name="reader_type">
                <option value="1">学生</option>
                <option value="2">教师</option>
                <option value="3">社会读者</option>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">最大借阅数量</label>
            <input type="number" class="form-control" name="max_borrow" value="10" min="1" max="20">
        </div>
    </div>

    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-lg"></i> 注册
    </button>
    <a href="/readers" class="btn btn-secondary">取消</a>
</form>

<?php include __DIR__ . '/../layout/footer.php'; ?>