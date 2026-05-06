<?php
error_reporting(E_ALL); //Bật debug và hiện lỗi ra màn hình
ini_set('display_errors', 1);

require_once '../config/config.php';
require_once '../app/helpers/utils.php';
require_once '../vendor/autoload.php';

session_start();

// Bộ tự động nạp Class (Autoload)
spl_autoload_register(function($className) {
    // Tìm trong thư mục core
    if (file_exists("../core/$className.php")) {
        require_once "../core/$className.php";
    }
    // Tìm trong thư mục interfaces
    elseif (file_exists("../app/interfaces/$className.php")) {
        require_once "../app/interfaces/$className.php";
    }
    // Tìm trong thư mục controllers
    elseif (file_exists("../app/controllers/$className.php")) {
        require_once "../app/controllers/$className.php";
    }
    // Tìm trong thư mục models
    elseif (file_exists("../app/models/$className.php")) {
        require_once "../app/models/$className.php";
    }
});

/*
|------------------------------------------------------------
| Route mặc định
| - Nếu chưa đăng nhập: vào auth/login
| - Nếu đã đăng nhập: vào product
|------------------------------------------------------------
*/
if (!isset($_GET['url']) || trim($_GET['url']) === '') {
    if (isset($_SESSION['user']) || isset($_SESSION['user_id'])) {
        $_GET['url'] = 'product';
    } else {
        $_GET['url'] = 'auth/login';
    }
}

// Khởi tạo đối tượng App để bắt đầu Routing
$app = new App();// <- app sẽ tự động gọi controller+Action phù hợp
?>