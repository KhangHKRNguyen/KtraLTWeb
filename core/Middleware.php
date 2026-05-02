<?php

class Middleware {
    /**
     * Kiểm tra xem người dùng đã đăng nhập chưa
     * Nếu chưa, đẩy về trang login
     */
    public static function auth() {
        // 1. Kiểm tra xem controller hiện tại có phải là 'auth' không
        $currentController = $_GET['controller'] ?? '';
        
        // 2. Nếu đã đăng nhập thì không làm gì cả (hợp lệ)
        if (isset($_SESSION['user'])) {
            return;
        }

        // 3. Nếu chưa đăng nhập VÀ không phải đang ở trang auth/login thì mới redirect
        if ($currentController !== 'auth') {
            $_SESSION['error_message'] = "Vui lòng đăng nhập để tiếp tục.";
            header("Location: index.php?controller=auth&action=login");
            exit();
        }
    }

    /**
     * Kiểm tra quyền Admin
     */
    public static function admin() {
        self::auth();
        
        if ($_SESSION['user']['role'] !== 'admin') {
            die("Bạn không có quyền truy cập vào khu vực này.");
        }
    }
}
?>