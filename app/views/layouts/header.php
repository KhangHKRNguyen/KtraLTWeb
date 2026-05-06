<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $page_title ?? 'Quản lý Bán hàng'; ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/assets/css/style.css">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="<?php echo URLROOT; ?>/public/assets/js/darkmode.js"></script>
</head>
<body style="font-family: 'Inter', sans-serif;">

    <script>
        const SITE_URL = "index.php";
    </script>

    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>

    <div class="main-container">

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 12px; box-shadow: var(--shadow-md);">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo $_SESSION['success_message'];
                unset($_SESSION['success_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 12px; box-shadow: var(--shadow-md);">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo $_SESSION['error_message'];
                unset($_SESSION['error_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <div class="d-flex justify-content-end align-items-center mb-3 gap-2">
            <?php if (isset($_SESSION['user'])): ?>
                <span class="text-muted small">
                    <i class="fas fa-user-circle me-1"></i>
                    Xin chào, <strong><?php echo htmlspecialchars($_SESSION['user']['full_name'] ?: $_SESSION['user']['username']); ?></strong>
                </span>
                <a href="<?php echo URLROOT; ?>/public/index.php?url=auth/logout"
                   class="btn btn-outline-danger btn-sm"
                   onclick="return confirm('Bạn có chắc muốn đăng xuất?')">
                    <i class="fas fa-sign-out-alt me-1"></i>Đăng xuất
                </a>
            <?php endif; ?>
            <button id="toggle-dark-mode" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-moon"></i> Dark Mode
            </button>
        </div>