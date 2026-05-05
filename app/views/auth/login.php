<div class="d-flex align-items-center justify-content-center" style="min-height:80vh;">
<div class="card shadow" style="width:100%;max-width:420px;border-radius:16px;">
    <div class="card-body p-4">
        <div class="text-center mb-4">
            <i class="fas fa-boxes-stacked fa-2x text-primary"></i>
            <h4 class="fw-bold mt-2">Đăng nhập</h4>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?></div>
        <?php endif; ?>

        <form action="<?php echo URLROOT; ?>/public/index.php?url=auth/login" method="POST">
            <div class="mb-3">
                <label class="form-label fw-semibold">Tên đăng nhập</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" name="username" class="form-control" placeholder="Nhập tên đăng nhập" required autofocus>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Mật khẩu</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 fw-semibold">
                <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
            </button>
        </form>

        <p class="text-center mt-3 text-muted small">
            Chưa có tài khoản?
            <a href="<?php echo URLROOT; ?>/public/index.php?url=auth/register" class="text-primary fw-semibold">Đăng ký</a>
        </p>
    </div>
</div>
</div>
