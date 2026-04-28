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
                        <a class="dropdown-item" href="index.php?controller=product&action=export_template">
                            <i class="fas fa-file-alt me-2 text-secondary"></i> Tải Template
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="index.php?controller=product&action=export_products">
                            <i class="fas fa-file-excel me-2 text-success"></i> Xuất toàn bộ sản phẩm
                        </a>
                    </li>
                </ul>
            </div>


            <a href="index.php?controller=product&action=import" class="btn btn-info-modern">
                <i class="fas fa-file-excel"></i> Import Excel
            </a>

            <a href="index.php?controller=product&action=create" class="btn btn-primary-modern">
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

<script>
$(document).ready(function() {
    const modalElement = document.getElementById('productDetailModal');
    const modalBody = $('#product-detail-content');
    const modalEditButton = $('#modal-edit-button');

    let currentPage = 1;

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function loadProducts(page = 1) {
        currentPage = page;

        $('.loading-overlay').addClass('show');

        $.ajax({
            url: 'index.php?controller=product&action=ajax_list',
            type: 'GET',
            dataType: 'json',
            data: {
                search: $('#search').val(),
                category_id: $('#category_id').val(),
                supplier_id: $('#supplier_id').val(),
                min_price: $('#min_price').val(),
                max_price: $('#max_price').val(),
                page: page
            },
            success: function(response) {
                if (response.success) {
                    $('#product-table-body').html(response.table_html);
                    $('#pagination-container').html(response.pagination_html);
                    $('#product-count').text(response.total_products + ' sản phẩm');
                } else {
                    $('#product-table-body').html(`
                        <tr>
                            <td colspan="9" class="text-center text-danger py-4">
                                Không thể tải dữ liệu
                            </td>
                        </tr>
                    `);
                }
            },
            error: function() {
                $('#product-table-body').html(`
                    <tr>
                        <td colspan="9" class="text-center text-danger py-4">
                            Có lỗi xảy ra khi tải dữ liệu
                        </td>
                    </tr>
                `);
            },
            complete: function() {
                $('.loading-overlay').removeClass('show');
            }
        });
    }

    loadProducts(1);

    $('#search-form').on('submit', function(e) {
        e.preventDefault();
        loadProducts(1);
    });

    $(document).on('click', '#pagination-container .page-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        loadProducts(page);
    });

    modalElement.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const productId = button.getAttribute('data-id');
        const editUrl = `index.php?controller=product&action=edit&id=${productId}`;
        modalEditButton.attr('href', editUrl);

        modalBody.html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Đang tải...</span>
                </div>
            </div>
        `);

        $.ajax({
            url: 'index.php?controller=product&action=ajax_get_details',
            type: 'GET',
            data: { id: productId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    buildModalContent(response.product, response.variants);
                } else {
                    modalBody.html(`<div class="alert alert-danger m-3">Lỗi: ${escapeHtml(response.message)}</div>`);
                }
            },
            error: function() {
                modalBody.html('<div class="alert alert-danger m-3">Lỗi: Không thể tải dữ liệu. Vui lòng thử lại.</div>');
            }
        });
    });

    function buildModalContent(product, variants) {
    const BASE_URL = "<?php echo URLROOT; ?>/public/assets/images/"; // Gốc đến thư mục images

    let productImage = product.image;
    if (!productImage) {
        // Sử dụng file placeholder.png nằm trong thư mục team mà tôi thấy trong ảnh của bạn
        productImage = BASE_URL + "team/placeholder.png"; 
    } else {
        if (!productImage.startsWith('http')) {
            // Nếu product.image chỉ lưu "iphone.jpg", ta nối thêm folder "products/"
            productImage = BASE_URL + "products/" + productImage;
        }
    }
        let productHtml = `
            <div class="modern-card mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3 text-center">
                            <img src="${escapeHtml(product.image || '<?php echo URLROOT; ?>/public/assets/images/default-product.png')}"
                                class="img-fluid rounded shadow-sm"
                                alt="${escapeHtml(product.name)}"
                                style="max-height: 150px; object-fit: cover;">
                        </div>
                        <div class="col-md-9">
                            <h4 class="mb-2">${escapeHtml(product.name)}</h4>
                            <span class="badge bg-secondary fs-6 mb-3">${escapeHtml(product.sku)}</span>
                            <p class="text-muted small mb-2">
                                ${escapeHtml(product.description || 'Chưa có mô tả.')}
                            </p>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Danh mục:</strong> ${escapeHtml(product.category_name || 'Chưa có')}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Nhà cung cấp:</strong> ${escapeHtml(product.supplier_name || 'Chưa có')}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        let variantsHtml = `
            <div class="modern-card">
                <div class="card-header-modern">
                    <h5><i class="fas fa-boxes"></i> Danh sách biến thể</h5>
                    <span class="badge bg-light text-dark">${variants.length} biến thể</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Màu</th>
                                    <th>Dung lượng</th>
                                    <th>Giá</th>
                                    <th>Tồn kho</th>
                                </tr>
                            </thead>
                            <tbody>
        `;

        if (variants.length > 0) {
            variants.forEach(v => {
                const price = new Intl.NumberFormat('vi-VN').format(v.price);
                const stock = new Intl.NumberFormat('vi-VN').format(v.stock);
                variantsHtml += `
                    <tr>
                        <td>${escapeHtml(v.color)}</td>
                        <td>${escapeHtml(v.storage)}</td>
                        <td>${price} đ</td>
                        <td>${stock}</td>
                    </tr>
                `;
            });
        } else {
            variantsHtml += '<tr><td colspan="4" class="text-center p-3">Chưa có biến thể nào.</td></tr>';
        }

        variantsHtml += '</tbody></table></div></div></div>';

        modalBody.html(productHtml + variantsHtml);
    }
});
</script>