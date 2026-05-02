<?php
// File: views/products/edit.php
?>

<input type="hidden" id="product-id" value="<?php echo $product['id']; ?>">

<div class="main-container fade-in">
    <div class="modern-card mb-4">
        <div class="card-header-modern">
            <h5><i class="fas fa-edit"></i> Thông tin sản phẩm</h5>
            <a href="index.php?controller=product&action=index" class="btn btn-light btn-sm">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
        <div class="card-body-ajax">
            <form action="index.php?controller=product&action=update" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $product['id']; ?>">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">SKU <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($product['sku']); ?>" disabled>
                        <small class="text-muted">SKU không thể thay đổi</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tên sản phẩm <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control"
                            value="<?php echo htmlspecialchars($product['name']); ?>" required>
                    </div>

                    <div class="col-md-12">
    <label class="form-label fw-bold">Mô tả</label>
    <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($product['description']); ?></textarea>
</div>

<div class="col-md-6">
    <label class="form-label fw-bold">Danh mục <span class="text-danger">*</span></label>
    <select name="category_id" class="form-select" required>
        <option value="">-- Chọn danh mục --</option>
        <?php foreach ($categories as $category): ?>
            <option value="<?php echo $category['id']; ?>"
                <?php echo ($product['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($category['name']); ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<div class="col-md-6">
    <label class="form-label fw-bold">Nhà cung cấp <span class="text-danger">*</span></label>
    <select name="supplier_id" class="form-select" required>
        <option value="">-- Chọn nhà cung cấp --</option>
        <?php foreach ($suppliers as $supplier): ?>
            <option value="<?php echo $supplier['id']; ?>"
                <?php echo ($product['supplier_id'] == $supplier['id']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($supplier['name']); ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<div class="col-md-12">
    <label class="form-label fw-bold">Ảnh đại diện</label>
    <?php if (!empty($product['image'])): ?>
        <div class="mb-2">
            <img src="<?php echo htmlspecialchars($product['image']); ?>"
                alt="Current Image"
                class="img-thumbnail"
                style="max-height: 150px;">
        </div>
    <?php endif; ?>
    <input type="file" name="image" class="form-control" accept="image/*">
    <small class="text-muted">Để trống nếu không muốn thay đổi ảnh</small>
</div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary-modern">
                            <i class="fas fa-save"></i> Cập nhật sản phẩm
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modern-card mb-4">
        <div class="card-header-modern">
            <h5><i class="fas fa-boxes"></i> Biến thể sản phẩm</h5>
            <span class="badge bg-light text-dark"><?php echo count($variants); ?> biến thể</span>
        </div>
        <div class="card-body-ajax">

            <?php
            // (MỚI) Định nghĩa các tùy chọn (cho cả form và bảng)
            $color_map = [
                'Đen' => ['#000000', '#FFFFFF'],
                'Trắng' => ['#FFFFFF', '#000000'],
                'Bạc' => ['#C0C0C0', '#000000'],
                'Xám' => ['#808080', '#FFFFFF'],
                'Titan Tự nhiên' => ['#A6A199', '#FFFFFF'],
                'Vàng' => ['#FFD700', '#000000'],
                'Đỏ' => ['#E74C3C', '#FFFFFF'],
                'Xanh Dương' => ['#3498DB', '#FFFFFF'],
                'Xanh Lá' => ['#2ECC71', '#FFFFFF'],
                'Tím' => ['#9B59B6', '#FFFFFF'],
                'Hồng' => ['#FADADD', '#000000']
            ];
            $storage_options = ['64GB', '128GB', '256GB', '512GB', '1TB'];
            ?>

            <div class="search-form-modern mb-4">
                <h6 class="mb-3"><i class="fas fa-plus-circle"></i> Thêm biến thể mới</h6>
                <form id="variant-form" enctype="multipart/form-data">
                    <input type="hidden" name="controller" value="variant">
                    <input type="hidden" name="action" value="ajax_store">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Màu sắc <span class="text-danger">*</span></label>
                            <select name="color" class="form-select" required>
                                <option value="" disabled selected>-- Chọn màu --</option>
                                <?php foreach ($color_map as $name => list($bg_hex, $text_hex)): ?>
                                    <option value="<?php echo htmlspecialchars($name); ?>"
                                        style="background-color: <?php echo $bg_hex; ?>; color: <?php echo $text_hex; ?>;">
                                        <?php echo htmlspecialchars($name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Dung lượng <span class="text-danger">*</span></label>
                            <select name="storage" class="form-select" required>
                                <option value="" disabled selected>-- Chọn --</option>
                                <?php foreach ($storage_options as $storage): ?>
                                    <option value="<?php echo htmlspecialchars($storage); ?>">
                                        <?php echo htmlspecialchars($storage); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Giá <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control" placeholder="0" min="0" required>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Tồn kho <span class="text-danger">*</span></label>
                            <input type="number" name="stock" class="form-control" placeholder="0" min="0" required>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Ảnh biến thể</label>
                            <input type="file" name="image" id="variant-image" class="form-control" accept="image/*">
                        </div>

                        <div class="col-md-1">
                            <button type="submit" class="btn btn-success-modern w-100">
                                <i class="fas fa-plus"></i> Thêm
                            </button>
                        </div>
                    </div>

                    <!-- Ảnh preview -->
                    <div id="variant-preview" class="mt-2 text-center"></div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Ảnh</th>
                            <th style="width: 150px;">SKU</th>
                            <th>Màu sắc</th>
                            <th>Dung lượng</th>
                            <th style="width: 150px;">Giá (đ)</th>
                            <th style="width: 120px;">Tồn kho</th>
                            <th style="width: 180px;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="variant-table-body">
                        <?php if (!empty($variants)): ?>
                            <?php foreach ($variants as $v): ?>
                                <tr id="variant-<?php echo $v['id']; ?>">
                                    <td>
                                        <?php if (!empty($v['image'])): ?>
                                            <img src="<?php echo htmlspecialchars($v['image']); ?>"
                                                class="variant-thumbnail"
                                                alt="<?php echo htmlspecialchars($v['color']); ?>">
                                        <?php else: ?>
                                            <i class="fas fa-image text-muted"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($v['sku']); ?></span>
                                    </td>
                                    <td>
                                        <?php
                                        // Lấy mã màu, nếu không có thì dùng màu trắng
                                        $bg_hex = $color_map[$v['color']][0] ?? '#FFFFFF';
                                        $border = ($bg_hex == '#FFFFFF') ? 'border: 1px solid #ccc;' : '';
                                        ?>
                                        <span class="color-circle" style="background-color: <?php echo $bg_hex; ?>; <?php echo $border; ?>"></span>
                                        <?php echo htmlspecialchars($v['color']); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($v['storage']); ?></td>
                                    <td>
                                        <span id="variant-price-<?php echo $v['id']; ?>">
                                            <?php echo number_format($v['price']); ?> đ
                                        </span>
                                    </td>
                                    <td>
                                        <span id="variant-stock-<?php echo $v['id']; ?>">
                                            <?php echo $v['stock']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button"
                                            class="btn btn-warning-modern btn-sm btn-edit-variant"
                                            data-id="<?php echo $v['id']; ?>"
                                            data-color="<?php echo htmlspecialchars($v['color']); ?>"
                                            data-storage="<?php echo htmlspecialchars($v['storage']); ?>"
                                            data-price="<?php echo $v['price']; ?>"
                                            data-stock="<?php echo $v['stock']; ?>">
                                            <i class="fas fa-edit"></i> Sửa
                                        </button>
                                        <button type="button"
                                            class="btn btn-danger-modern btn-sm btn-delete-variant"
                                            data-id="<?php echo $v['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($v['color'] . ' - ' . $v['storage']); ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr id="no-variants-row">
                                <td colspan="7" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-box"></i>
                                        <h5>Chưa có biến thể nào</h5>
                                        <p>Hãy thêm biến thể mới ở form bên trên</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modern-card mb-4">
        <div class="card-header-modern">
            <h5><i class="fas fa-images"></i> Thư viện ảnh</h5>
            <span class="badge bg-light text-dark"><?php echo count($images); ?> ảnh</span>
        </div>
        <div class="card-body-ajax">
            <div class="search-form-modern mb-4">
                <h6 class="mb-3"><i class="fas fa-upload"></i> Upload ảnh mới</h6>
                <form id="image-form" enctype="multipart/form-data">
                    <input type="hidden" name="controller" value="image">
                    <input type="hidden" name="action" value="ajax_store">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

                    <div class="row g-3">
                        <div class="col-md-10">
                            <input type="file"
                                id="image_url"
                                name="image_url[]"
                                class="form-control"
                                accept="image/*"
                                multiple
                                required>
                            <small class="form-text text-muted">Chọn tối đa 5 ảnh (JPG, PNG, GIF, WEBP - Max 5MB/ảnh)</small>

                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-success-modern w-100">
                                <i class="fas fa-upload"></i> Upload
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="row g-3" id="image-gallery">
                <?php if (!empty($images)): ?>
                    <?php foreach ($images as $img): ?>
                        <div class="col-md-3 image-item" id="image-<?php echo $img['id']; ?>">
                            <div class="card shadow-sm">
                                <img src="<?php echo htmlspecialchars($img['image_url']); ?>"
                                    class="card-img-top"
                                    alt="Product Image"
                                    style="height: 200px; object-fit: cover;">
                                <div class="card-body p-2">
                                    <button type="button"
                                        class="btn btn-danger-modern btn-sm w-100 btn-delete-image"
                                        data-id="<?php echo $img['id']; ?>">
                                        <i class="fas fa-trash"></i> Xóa
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5" id="no-images-row">
                        <div class="empty-state">
                            <i class="fas fa-images"></i>
                            <h5>Chưa có ảnh nào</h5>
                            <p>Hãy upload ảnh mới ở form bên trên</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editVariantModal" tabindex="-1" aria-labelledby="editVariantModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="edit-variant-form" enctype="multipart/form-data">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editVariantModalLabel">
                        <i class="fas fa-edit"></i> Chỉnh sửa Biến thể
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit-variant-id" name="id">

                    <!-- Current Image Display -->
                    <div class="mb-3 text-center" id="current-variant-image-container">
                        <label class="form-label">Ảnh hiện tại:</label>
                        <div id="current-variant-image">
                            <i class="fas fa-image fa-3x text-muted"></i>
                        </div>
                    </div>

                    <!-- Variant Info (Read-only) -->
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label text-muted small">Màu sắc:</label>
                            <div class="d-flex align-items-center">
                                <span class="color-circle me-2" id="edit-variant-color-circle"></span>
                                <strong id="edit-variant-color"></strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-muted small">Dung lượng:</label>
                            <div><strong id="edit-variant-storage"></strong></div>
                        </div>
                    </div>

                    <hr>

                    <!-- Editable Fields -->
                    <div class="mb-3">
                        <label for="edit-variant-price" class="form-label">
                            Giá <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                            <input type="number"
                                id="edit-variant-price"
                                name="price"
                                class="form-control"
                                required
                                min="0"
                                step="1000"
                                placeholder="0">
                            <span class="input-group-text">đ</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit-variant-stock" class="form-label">
                            Tồn kho <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-boxes"></i></span>
                            <input type="number"
                                id="edit-variant-stock"
                                name="stock"
                                class="form-control"
                                required
                                min="0"
                                placeholder="0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit-variant-image" class="form-label">
                            <i class="fas fa-image"></i> Thay đổi ảnh (tùy chọn)
                        </label>
                        <input type="file"
                            id="edit-variant-image"
                            name="variant_image"
                            class="form-control"
                            accept="image/*">
                        <small class="form-text text-muted">Để trống nếu không muốn thay đổi ảnh</small>
                        <!-- Image Preview -->
                        <div id="new-variant-image-preview" class="mt-2" style="display: none;">
                            <img id="new-variant-image-preview-img" src="" alt="Preview" style="max-width: 100%; max-height: 200px; border-radius: 8px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Đóng
                    </button>
                    <button type="submit" class="btn btn-primary-modern">
                        <i class="fas fa-save"></i> Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* (Giữ nguyên style cũ của bạn) */
    .variant-thumbnail {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 6px;
    }

    .image-item {
        transition: transform 0.3s ease;
    }

    .image-item:hover {
        transform: translateY(-5px);
    }

    #current-variant-image img {
        max-width: 200px;
        max-height: 200px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    #edit-variant-color-circle {
        width: 24px;
        height: 24px;
        display: inline-block;
        border-radius: 50%;
        vertical-align: middle;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    #image-preview img {
        transition: transform 0.2s ease;
    }

    #image-preview img:hover {
        transform: scale(1.05);
    }
</style>

<script>
    $(document).ready(function() {
        // Upload nhiều ảnh
        $('#image-form').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            $.ajax({
                url: 'index.php?controller=image&action=ajax_store',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.success) {
                        Swal.fire({
                            title: 'Thành công',
                            text: res.message,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        // 🧠 Ngăn trình duyệt re-submit khi F5
                        window.history.replaceState({}, document.title, window.location.href);

                        // Reset form sau khi upload
                        $('#image-form')[0].reset();
                    } else {
                        Swal.fire('Lỗi', res.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Lỗi', 'Không thể upload ảnh.', 'error');
                }
            });
        });
    });

    // Image preview for modal
    $(document).ready(function() {
        $('#edit-variant-image').on('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#new-variant-image-preview-img').attr('src', e.target.result);
                    $('#new-variant-image-preview').show();
                };
                reader.readAsDataURL(file);
            } else {
                $('#new-variant-image-preview').hide();
            }
        });
    });
    $(document).ready(function() {
        // Hiển thị preview ảnh khi chọn file
        $('#variant-image').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    $('#variant-preview').html(
                        `<img src="${ev.target.result}" class="img-thumbnail" style="max-width:120px;">`
                    );
                };
                reader.readAsDataURL(file);
            } else {
                $('#variant-preview').empty();
            }
        });

        // Gửi AJAX thêm biến thể (kèm ảnh)
        $('#variant-form').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            $.ajax({
                url: 'index.php?controller=variant&action=ajax_store',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.success) {
                        $('#variant-table-body').append(res.variant_html);
                        $('#variant-form')[0].reset();
                        $('#variant-preview').empty();
                        Swal.fire('Thành công', res.message, 'success');
                        $('#no-variants-row').remove();
                    } else {
                        Swal.fire('Lỗi', res.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Lỗi', 'Không thể thêm biến thể.', 'error');
                }
            });
        });
    });

    // Hiển thị preview nhiều ảnh trước khi upload
    $('#image_url').on('change', function(e) {
        const files = e.target.files;
        const previewContainer = $('#image-preview');
        previewContainer.remove(); // Xóa cũ
        const preview = $('<div id="image-preview" class="row mt-3"></div>');
        $(this).closest('.search-form-modern').append(preview);

        if (files.length > 0) {
            [...files].slice(0, 5).forEach(file => {
                if (!file.type.startsWith('image/')) return;
                const reader = new FileReader();
                reader.onload = function(ev) {
                    const col = $(`
                    <div class="col-md-2 col-4 mb-2">
                        <img src="${ev.target.result}" class="img-thumbnail" 
                             style="width:100%; height:100px; object-fit:cover; border-radius:8px;">
                    </div>
                `);
                    preview.append(col);
                };
                reader.readAsDataURL(file);
            });
        }
    });
</script>