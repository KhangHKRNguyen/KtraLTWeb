<?php
// File: views/products/create.php

$old = $_SESSION['old_input'] ?? [];
unset($_SESSION['old_input']);
?>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-body">
                <form action="index.php?controller=product&action=store" method="POST" enctype="multipart/form-data">
                    
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

                    <a href="index.php?controller=product&action=index" class="btn btn-secondary">Hủy</a>
                    <button type="submit" class="btn btn-primary">Lưu và tiếp tục (để thêm biến thể)</button>
                </form>
            </div>
        </div>
    </div>
</div>