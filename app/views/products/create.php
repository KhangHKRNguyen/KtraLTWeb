<?php
// File: views/products/create.php

$old = $_SESSION['old_input'] ?? [];
unset($_SESSION['old_input']);
?>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-body">
                <form action="index.php?url=product/store" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="sku" class="form-label">Mã SKU</label>
                        <input 
                            type="text" 
                            name="sku" 
                            id="sku" 
                            class="form-control" 
                            value="<?= htmlspecialchars($old['sku'] ?? '') ?>" 
                            required
                        >
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Tên sản phẩm</label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="name" 
                            name="name" 
                            value="<?= htmlspecialchars($old['name'] ?? '') ?>" 
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea 
                            class="form-control" 
                            id="description" 
                            name="description" 
                            rows="5"
                        ><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label">Danh mục</label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="">-- Chọn danh mục --</option>
                            <?php foreach ($categories as $category): ?>
                                <option 
                                    value="<?= $category['id'] ?>"
                                    <?= (($old['category_id'] ?? '') == $category['id']) ? 'selected' : '' ?>
                                >
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="supplier_id" class="form-label">Nhà cung cấp</label>
                        <select class="form-select" id="supplier_id" name="supplier_id" required>
                            <option value="">-- Chọn nhà cung cấp --</option>
                            <?php foreach ($suppliers as $supplier): ?>
                                <option 
                                    value="<?= $supplier['id'] ?>"
                                    <?= (($old['supplier_id'] ?? '') == $supplier['id']) ? 'selected' : '' ?>
                                >
                                    <?= htmlspecialchars($supplier['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Ảnh thumbnail</label>
                        <input 
                            type="file" 
                            class="form-control" 
                            id="image" 
                            name="image" 
                            accept="image/*"
                        >
                    </div>

                    <!-- ========== BIẾN THỂ BAN ĐẦU ========== -->
                    <hr>
                    <h5 class="mb-3">Thêm biến thể ban đầu (Tùy chọn)</h5>
                    
                    <div class="mb-3">
                        <label for="color" class="form-label">Màu sắc</label>
                        <select class="form-select" id="color" name="color">
                            <option value="">-- Không chọn (bỏ qua) --</option>
                            <option value="Đen">Đen</option>
                            <option value="Trắng">Trắng</option>
                            <option value="Bạc">Bạc</option>
                            <option value="Xám">Xám</option>
                            <option value="Vàng">Vàng</option>
                            <option value="Đỏ">Đỏ</option>
                            <option value="Xanh Dương">Xanh Dương</option>
                            <option value="Xanh Lá">Xanh Lá</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="storage" class="form-label">Dung lượng</label>
                        <select class="form-select" id="storage" name="storage">
                            <option value="">-- Không chọn (bỏ qua) --</option>
                            <option value="64GB">64GB</option>
                            <option value="128GB">128GB</option>
                            <option value="256GB">256GB</option>
                            <option value="512GB">512GB</option>
                            <option value="1TB">1TB</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="price" class="form-label">Giá (đ)</label>
                        <input 
                            type="number" 
                            class="form-control" 
                            id="price" 
                            name="price" 
                            placeholder="VD: 25000000"
                            min="0"
                        >
                        <small class="text-muted">Để trống nếu không muốn thêm biến thể ngay</small>
                    </div>

                    <div class="mb-3">
                        <label for="stock" class="form-label">Tồn kho</label>
                        <input 
                            type="number" 
                            class="form-control" 
                            id="stock" 
                            name="stock" 
                            placeholder="VD: 50"
                            min="0"
                        >
                    </div>

                    <a href="index.php?url=product" class="btn btn-secondary">Hủy</a>
                    <button type="submit" class="btn btn-primary">Lưu sản phẩm</button>
                </form>
            </div>
        </div>
    </div>
</div>