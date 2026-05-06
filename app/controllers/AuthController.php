<?php
// app/controllers/AuthController.php
require_once '../app/interfaces/AuthInterface.php';

class AuthController extends Controller implements AuthInterface {
    private $userModel;

    public function __construct() {
        $this->userModel = $this->model('User');
    }

    // Hiển thị + xử lý đăng nhập
    public function login() {
        // Đã đăng nhập rồi thì về trang chủ
        if (isset($_SESSION['user'])) {
            header('Location: ' . URLROOT . '/public/index.php?url=product/index');
            exit();
        }

        $data = ['error' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                $data['error'] = 'Vui lòng nhập đầy đủ thông tin.';
            } else {
                // Gọi Model — không viết SQL ở đây
                $user = $this->userModel->findByUsername($username);

                if ($user && password_verify($password, $user['password'])) {
                    session_regenerate_id(true);
                    $_SESSION['user'] = [
                        'id'        => $user['id'],
                        'username'  => $user['username'],
                        'full_name' => $user['full_name'],
                        'role'      => $user['role'],
                    ];
                    header('Location: ' . URLROOT . '/public/index.php?url=product/index');
                    exit();
                } else {
                    $data['error'] = 'Sai tài khoản hoặc mật khẩu!';
                }
            }
        }

        $this->view('auth/login', $data);
    }

    // Hiển thị + xử lý đăng ký
    public function register() {
        if (isset($_SESSION['user'])) {
            header('Location: ' . URLROOT . '/public/index.php?url=product/index');
            exit();
        }

        $data = ['error' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username  = trim($_POST['username']  ?? '');
            $email     = trim($_POST['email']     ?? '');
            $password  = $_POST['password']       ?? '';
            $password2 = $_POST['password2']      ?? '';
            $fullName  = trim($_POST['full_name'] ?? '');

            // Validate
            if (empty($username) || empty($email) || empty($password)) {
                $data['error'] = 'Vui lòng điền đầy đủ các trường bắt buộc.';
            } elseif (strlen($password) < 6) {
                $data['error'] = 'Mật khẩu phải từ 6 ký tự trở lên.';
            } elseif ($password !== $password2) {
                $data['error'] = 'Mật khẩu xác nhận không khớp.';
            } elseif ($this->userModel->findByUsername($username)) {
                $data['error'] = 'Tên đăng nhập đã tồn tại.';
            } elseif ($this->userModel->findByEmail($email)) {
                $data['error'] = 'Email này đã được sử dụng.';
            } else {
                // Hash password ở Controller, giao Model lưu
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $newId  = $this->userModel->createUser($username, $email, $hashed, $fullName);

                if ($newId) {
                    $_SESSION['success_message'] = 'Đăng ký thành công! Vui lòng đăng nhập.';
                    header('Location: ' . URLROOT . '/public/index.php?url=auth/login');
                    exit();
                } else {
                    $data['error'] = 'Đã xảy ra lỗi. Vui lòng thử lại.';
                }
            }
        }

        $this->view('auth/register', $data);
    }

    // Đăng xuất
    public function logout() {
        unset($_SESSION['user']);
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        header('Location: ' . URLROOT . '/public/index.php?url=auth/login');
        exit();
    }
}
