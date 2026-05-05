<div class="d-flex align-items-center justify-content-center" style="min-height:80vh;">
<div class="card shadow" style="width:100%;max-width:460px;border-radius:16px;">
    <div class="card-body p-4">
        <div class="text-center mb-4">
            <i class="fas fa-user-plus fa-2x text-primary"></i>
            <h4 class="fw-bold mt-2">Tạo tài khoản mới</h4>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="<?php echo URLROOT; ?>/public/index.php?url=auth/register" method="POST">
            <div class="mb-3">
                <label class="form-label fw-semibold">Họ và tên</label>
                <input type="text" name="full_name" class="form-control" placeholder="Nguyễn Văn A">
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Tên đăng nhập <span class="text-danger">*</span></label>
                <input type="text" name="username" class="form-control" placeholder="Tối thiểu 3 ký tự" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" placeholder="example@email.com" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Mật khẩu <span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control" placeholder="Tối thiểu 6 ký tự" required>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                <input type="password" name="password2" class="form-control" placeholder="Nhập lại mật khẩu" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 fw-semibold">
                <i class="fas fa-user-plus me-2"></i>Đăng ký
            </button>
        </form>

        <p class="text-center mt-3 text-muted small">
            Đã có tài khoản?
            <a href="<?php echo URLROOT; ?>/public/index.php?url=auth/login" class="text-primary fw-semibold">Đăng nhập</a>
        </p>
    </div>
</div>
</div>
