<?php
// File: views/products/index.php
?>
<div class="page-header fade-in">
    <div class="d-flex justify-content-between align-items-center">

        <div>
            <div class="d-flex align-items-center" style="gap: 16px;">

                <div>
                    <img src="<?php echo URLROOT; ?>/public/assets/images/logo2.png" alt="Logo Công Ty" style="height: 150px; width: 150px;">
                </div>

                <div>
                    <h1>Quản lý Sản phẩm</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
                            <li class="breadcrumb-item active">Sản phẩm</li>
                        </ol>
                    </nav>
                </div>

            </div>
        </div>

        <div class="d-flex gap-2">
            <div class="dropdown">
                <button class="btn btn-success-modern dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-file-download"></i> Xuất dữ liệu
                </button>
                <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                    <li>
                        <a class="dropdown-item" href="index.php?url=product/exportTemplate">

                            <i class="fas fa-file-alt me-2 text-secondary"></i> Tải Template
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="index.php?url=product/exportProducts">

                            <i class="fas fa-file-excel me-2 text-success"></i> Xuất toàn bộ sản phẩm
                        </a>
                    </li>
                </ul>
            </div>


            <a href="index.php?url=product/import" class="btn btn-info-modern">
                <i class="fas fa-file-excel"></i> Import Excel
            </a>

            <a href="index.php?url=product/create" class="btn btn-primary-modern">
                <i class="fas fa-plus"></i> Thêm mới
            </a>
        </div>
    </div>
</div>

<div class="modal fade" id="productDetailModal" tabindex="-1" aria-labelledby="productDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="productDetailModalLabel">
                    <i class="fas fa-eye"></i> Chi tiết sản phẩm
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="product-detail-content" style="background-color: #f8f9fa;">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Đóng
                </button>
                <a href="#" id="modal-edit-button" class="btn btn-primary-modern">
                    <i class="fas fa-edit"></i> Chỉnh sửa sản phẩm
                </a>
            </div>
        </div>
    </div>
</div>

<div class="modern-card fade-in" style="animation-delay: 0.1s;">
    <div class="card-header-modern">
        <h5><i class="fas fa-list"></i> Danh sách sản phẩm</h5>
        <div id="product-count" class="badge bg-light text-dark">
            Đang tải...
        </div>
    </div>

    <div class="card-body-ajax">
        <div class="loading-overlay">
            <div class="loading-spinner"></div>
        </div>

        <form id="search-form" class="search-form-modern">
    <div class="row g-3">
        <div class="col-md-3">
            <label class="form-label small text-muted mb-1">
                <i class="fas fa-search"></i> Tìm kiếm
            </label>
            <input type="text"
                id="search"
                class="form-control"
                placeholder="Nhập tên hoặc SKU sản phẩm...">
        </div>

        <div class="col-md-2">
            <label class="form-label small text-muted mb-1">
                <i class="fas fa-list"></i> Danh mục
            </label>
            <select id="category_id" class="form-select">
                <option value="0">-- Tất cả --</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label small text-muted mb-1">
                <i class="fas fa-truck"></i> Nhà cung cấp
            </label>
            <select id="supplier_id" class="form-select">
                <option value="0">-- Tất cả --</option>
                <?php foreach ($suppliers as $supplier): ?>
                    <option value="<?php echo $supplier['id']; ?>">
                        <?php echo htmlspecialchars($supplier['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label small text-muted mb-1">
                <i class="fas fa-dollar-sign"></i> Giá từ
            </label>
            <input type="number"
                id="min_price"
                class="form-control"
                placeholder="0">
        </div>

        <div class="col-md-2">
            <label class="form-label small text-muted mb-1">
                <i class="fas fa-dollar-sign"></i> Giá đến
            </label>
            <input type="number"
                id="max_price"
                class="form-control"
                placeholder="999999999">
        </div>

        <div class="col-md-1">
            <label class="form-label small text-muted mb-1">&nbsp;</label>
            <button type="submit"
                id="btn-search"
                class="btn btn-success-modern w-100">
                <i class="fas fa-filter"></i>
            </button>
        </div>
    </div>
</form>

        <div class="table-responsive">
            <table class="table table-modern">
                <thead>
    <tr>
        <th style="width: 80px; text-align: center;">Ảnh</th>
        <th style="width: 120px; text-align: center;">SKU</th>
        <th>Tên sản phẩm</th>
        <th style="width: 160px; text-align: center;">Danh mục</th>
        <th style="width: 180px; text-align: center;">Nhà cung cấp</th>
        <th style="width: 220px; text-align: center;">Giá (Min-Max)</th>
        <th style="width: 120px;">Tồn kho</th>
        <th style="width: 120px; text-align: center;">Ngày tạo</th>
        <th style="width: 280px; text-align: center;">Hành động</th>
    </tr>
</thead>
                <tbody id="product-table-body">
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Đang tải...</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <nav id="pagination-container" class="d-flex justify-content-center">
        </nav>
    </div>
</div>
