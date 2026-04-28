<?php
// File: views/products/import_result.php
?>

<div class="main-container fade-in">
    <!-- Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1>
                    <?php if ($stats): ?>
                        <i class="fas fa-check-circle text-success"></i> Kết quả Import
                    <?php else: ?>
                        <i class="fas fa-times-circle text-danger"></i> Import thất bại
                    <?php endif; ?>
                </h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
                        <li class="breadcrumb-item"><a href="index.php?controller=product&action=index">Sản phẩm</a></li>
                        <li class="breadcrumb-item active">Kết quả Import</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="index.php?controller=product&action=import" class="btn btn-info-modern">
                    <i class="fas fa-redo"></i> Import lại
                </a>
                <a href="index.php?controller=product&action=index" class="btn btn-primary-modern">
                    <i class="fas fa-list"></i> Xem sản phẩm
                </a>
            </div>
        </div>
    </div>

    <?php if ($stats): ?>
        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="modern-card text-center">
                    <div class="card-body">
                        <i class="fas fa-file-alt fa-3x text-primary mb-3"></i>
                        <h3 class="mb-1"><?php echo $stats['total_rows']; ?></h3>
                        <p class="text-muted mb-0">Tổng số dòng</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="modern-card text-center">
                    <div class="card-body">
                        <i class="fas fa-plus-circle fa-3x text-success mb-3"></i>
                        <h3 class="mb-1"><?php echo $stats['products_created'] + $stats['variants_created']; ?></h3>
                        <p class="text-muted mb-0">Tạo mới</p>
                        <small class="text-muted">
                            <?php echo $stats['products_created']; ?> SP, 
                            <?php echo $stats['variants_created']; ?> BT
                        </small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="modern-card text-center">
                    <div class="card-body">
                        <i class="fas fa-edit fa-3x text-warning mb-3"></i>
                        <h3 class="mb-1"><?php echo $stats['products_updated'] + $stats['variants_updated']; ?></h3>
                        <p class="text-muted mb-0">Cập nhật</p>
                        <small class="text-muted">
                            <?php echo $stats['products_updated']; ?> SP, 
                            <?php echo $stats['variants_updated']; ?> BT
                        </small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="modern-card text-center">
                    <div class="card-body">
                        <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                        <h3 class="mb-1"><?php echo $stats['skipped_rows']; ?></h3>
                        <p class="text-muted mb-0">Dòng bỏ qua</p>
                        <small class="text-muted"><?php echo count($stats['errors']); ?> lỗi</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Card -->
        <div class="modern-card mb-4">
            <div class="card-header-modern">
                <h5><i class="fas fa-chart-bar"></i> Tổng quan</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="mb-3"><i class="fas fa-box text-primary"></i> Sản phẩm</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-plus text-success me-2"></i>
                                Tạo mới: <strong><?php echo $stats['products_created']; ?></strong> sản phẩm
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-edit text-warning me-2"></i>
                                Cập nhật: <strong><?php echo $stats['products_updated']; ?></strong> sản phẩm
                            </li>
                        </ul>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="mb-3"><i class="fas fa-boxes text-primary"></i> Biến thể</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-plus text-success me-2"></i>
                                Tạo mới: <strong><?php echo $stats['variants_created']; ?></strong> biến thể
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-edit text-warning me-2"></i>
                                Cập nhật: <strong><?php echo $stats['variants_updated']; ?></strong> biến thể
                            </li>
                        </ul>
                    </div>
                </div>
                
                <?php if ($stats['skipped_rows'] > 0): ?>
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Cảnh báo:</strong> Có <?php echo $stats['skipped_rows']; ?> dòng bị bỏ qua do lỗi.
                        Xem chi tiết bên dưới.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Errors List -->
        <?php if (!empty($stats['errors'])): ?>
            <div class="modern-card">
                <div class="card-header-modern">
                    <h5><i class="fas fa-exclamation-circle"></i> Chi tiết lỗi</h5>
                    <span class="badge bg-danger"><?php echo count($stats['errors']); ?> lỗi</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-modern">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">Dòng</th>
                                    <th style="width: 150px;">SKU</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Lỗi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stats['errors'] as $error): ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-danger"><?php echo $error['row']; ?></span>
                                        </td>
                                        <td>
                                            <?php if (isset($error['data']['sku'])): ?>
                                                <code><?php echo htmlspecialchars($error['data']['sku']); ?></code>
                                            <?php else: ?>
                                                <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($error['data']['name'])): ?>
                                                <?php echo htmlspecialchars($error['data']['name']); ?>
                                            <?php else: ?>
                                                <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="text-danger">
                                                <i class="fas fa-times-circle me-1"></i>
                                                <?php echo htmlspecialchars($error['message']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Success Message -->
        <?php if (empty($stats['errors'])): ?>
            <div class="modern-card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-check-circle fa-5x text-success mb-4"></i>
                    <h3 class="text-success mb-3">Import thành công!</h3>
                    <p class="text-muted mb-4">
                        Tất cả dữ liệu đã được import vào hệ thống một cách hoàn hảo.
                    </p>
                    <div class="d-flex justify-content-center gap-2">
                        <a href="index.php?controller=product&action=index" class="btn btn-primary-modern">
                            <i class="fas fa-list"></i> Xem danh sách sản phẩm
                        </a>
                        <a href="index.php?controller=product&action=import" class="btn btn-info-modern">
                            <i class="fas fa-upload"></i> Import thêm
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <!-- No Stats Available -->
        <div class="modern-card">
            <div class="card-body text-center py-5">
                <i class="fas fa-times-circle fa-5x text-danger mb-4"></i>
                <h3 class="text-danger mb-3">Import thất bại!</h3>
                <p class="text-muted mb-4">
                    Không thể hoàn tất quá trình import. Vui lòng kiểm tra file và thử lại.
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <a href="index.php?controller=product&action=import" class="btn btn-primary-modern">
                        <i class="fas fa-redo"></i> Thử lại
                    </a>
                    <a href="index.php?controller=product&action=index" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.modern-card .card-body h3 {
    font-size: 2.5rem;
    font-weight: 700;
}

code {
    background: #f3f4f6;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 13px;
}
</style>