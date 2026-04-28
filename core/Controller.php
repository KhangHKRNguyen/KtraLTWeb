<?php
// core/Controller.php
class Controller {
    // Hàm gọi Model
    public function model($model) {
        require_once '../app/models/' . $model . '.php';
        return new $model();
    }

    // Hàm gọi View và truyền dữ liệu
    public function view($view, $data = []) {
        if (file_exists('../app/views/' . $view . '.php')) {
            require_once '../app/views/' . $view . '.php';
        } else {
            die("View không tồn tại: " . $view);
        }
    }
}