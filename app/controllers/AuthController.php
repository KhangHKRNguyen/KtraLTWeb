<?php
class AuthController extends Controller {
    private $userModel;

    public function __construct() {
        $this->userModel = $this->model('User');
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // 1. Lấy dữ liệu từ form
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);

            // 2. Kiểm tra người dùng qua Model
            $user = $this->userModel->login($username, $password);

            if ($user) {
                // 3. TẠO SESSION (Đây là lúc "vé" được cấp)
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'role' => $user['role']
                ];
                header("Location: index.php?controller=product&action=index");
                exit();
            } else {
                $data['error'] = "Sai tài khoản hoặc mật khẩu!";
                $this->view('auth/login', $data);
            }
        } else {
            // Hiển thị form đăng nhập
            $this->view('auth/login');
        }
    }

    public function logout() {
        unset($_SESSION['user']);
        session_destroy();
        header("Location: index.php?controller=auth&action=login");
        exit();
    }
}