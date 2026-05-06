<?php
// core/Controller.php
abstract class Controller {
    public function model($model) {
    if (!class_exists($model)) {
        die("Model $model không tồn tại");
    }
    return new $model();
}

    // Hàm gọi View và truyền dữ liệu
    public function view($view, $data = []) {
    // Giải nén mảng data thành các biến riêng lẻ (ví dụ: $data['products'] thành $products)
    extract($data);

    // 1. Nhúng Header
    if (file_exists('../app/views/layouts/header.php')) {
        require_once '../app/views/layouts/header.php';
    }

    // 2. Nhúng View chính (ví dụ: products/index.php)
    if (file_exists('../app/views/' . $view . '.php')) {
        require_once '../app/views/' . $view . '.php';
    } else {
        die("View không tồn tại.");
    }

    // 3. Nhúng Footer
    if (file_exists('../app/views/layouts/footer.php')) {
        require_once '../app/views/layouts/footer.php';
    }
}
}