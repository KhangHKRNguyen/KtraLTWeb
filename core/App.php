<?php

class App {
    protected $controller = "ProductController"; // Controller mặc định
    protected $action = "index";                // Hàm mặc định
    protected $params = [];                     // Tham số mặc định

    public function __construct() {
        $url = $this->parseUrl(); // ví dụ URL: ?url=product ->parseUrl() trả về mảng array['product']

        // 1. Xử lý Controller
        if (isset($url[0]) && file_exists("../app/controllers/" . ucfirst($url[0]) . "Controller.php")) {
            $this->controller = ucfirst($url[0]) . "Controller";
            unset($url[0]);
        }

        require_once "../app/controllers/" . $this->controller . ".php";
        $this->controller = new $this->controller;

        // 2. Xử lý Action (Phương thức trong Class)
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->action = $url[1];
                unset($url[1]);
            }
        }

        // 3. Xử lý Params (Các tham số còn lại trên URL)
        $this->params = $url ? array_values($url) : [];

        // Gọi hàm từ Controller và truyền tham số vào
        call_user_func_array([$this->controller, $this->action], $this->params);
    }

    private function parseUrl() {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return [];
    }
}
?>