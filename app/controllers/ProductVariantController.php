<?php
// app/controllers/ProductVariantController.php

class ProductVariantController extends Controller {
    private $productModel;
    private $variantModel;

    // Cấu hình mã màu cho SKU
    private $colorMapSku = [
        'Đen' => 'BLACK', 'Trắng' => 'WHITE', 'Bạc' => 'SILVER',
        'Xám' => 'GRAY', 'Titan Tự nhiên' => 'NATURAL', 'Vàng' => 'GOLD',
        'Đỏ' => 'RED', 'Xanh Dương' => 'BLUE', 'Xanh Lá' => 'GREEN',
        'Tím' => 'PURPLE', 'Hồng' => 'PINK', 'Beige' => 'BEIGE',
        'Platinum' => 'PLATINUM',
    ];

    public function __construct() {
        // Khởi tạo model thông qua hàm model() của lớp cha
        $this->productModel = $this->model('ProductModel');
        $this->variantModel = $this->model('VariantModel');
    }

    // ========== ACTIONS ==========

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product_id = $_POST['product_id'] ?? 0;
            $result = $this->handleStore($product_id, $_POST, $_FILES);
            
            if ($result['success']) {
                $_SESSION['success_message'] = "Thêm biến thể thành công!";
            } else {
                $_SESSION['error_message'] = $result['message'];
            }
            header("Location: index.php?controller=product&action=edit&id=" . $product_id);
        }
    }

    public function ajaxStore() {
        header('Content-Type: application/json');
        try {
            $product_id = $_POST['product_id'] ?? 0;
            $result = $this->handleStore($product_id, $_POST, $_FILES, true);
            echo json_encode($result);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function ajaxUpdate() {
        header('Content-Type: application/json');
        try {
            $id = (int)($_POST['id'] ?? 0);
            $price = filter_var($_POST['price'] ?? 0, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $stock = filter_var($_POST['stock'] ?? 0, FILTER_SANITIZE_NUMBER_INT);

            $existing = $this->variantModel->getByID($id);
            if (!$existing) throw new Exception('Biến thể không tồn tại');

            $imageUrl = $existing['image'];
            if (isset($_FILES['variant_image']) && $_FILES['variant_image']['error'] == 0) {
                $imageUrl = $this->uploadImage($_FILES['variant_image'], $existing['image']);
            }

            $data = [
                'id' => $id,
                'price' => $price,
                'stock' => $stock,
                'image' => $imageUrl
            ];

            if ($this->variantModel->update($data)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Cập nhật thành công!',
                    'price' => number_format($price, 0, ',', '.') . ' đ',
                    'stock' => number_format($stock),
                    'image_url' => $imageUrl
                ]);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function delete() {
        $id = (int)($_GET['id'] ?? 0);
        $variant = $this->variantModel->getByID($id);
        $product_id = $variant['product_id'] ?? 0;

        if ($variant && $this->variantModel->delete($id)) {
            if (!empty($variant['image'])) @unlink($variant['image']);
            $_SESSION['success_message'] = "Xóa thành công!";
        }
        header("Location: index.php?controller=product&action=edit&id=" . $product_id);
    }

    // ========== HELPERS (Private) ==========

    private function handleStore($productId, $postData, $fileData, $isAjax = false) {
        $product = $this->productModel->getByID($productId);
        if (!$product) return ['success' => false, 'message' => 'Sản phẩm không tồn tại'];

        $color = trim($postData['color'] ?? '');
        $storage = trim($postData['storage'] ?? '');
        $price = $postData['price'] ?? 0;
        $stock = $postData['stock'] ?? 0;

        // Logic tạo SKU
        $sku = $this->generateSKU($product['sku'], $color, $storage);

        // Kiểm tra tồn tại
        if ($this->variantModel->findBySKU($sku)) {
            return ['success' => false, 'message' => "Biến thể với SKU $sku đã tồn tại"];
        }

        $imagePath = '';
        if (isset($fileData['image']) && $fileData['image']['error'] == 0) {
            $imagePath = $this->uploadImage($fileData['image']);
        }

        $data = [
            'product_id' => $productId,
            'sku' => $sku,
            'color' => $color,
            'storage' => $storage,
            'price' => $price,
            'stock' => $stock,
            'image' => $imagePath
        ];

        if ($this->variantModel->create($data)) {
            // Nếu là AJAX, bạn có thể render HTML row ở đây hoặc trả về data
            return ['success' => true, 'message' => 'Thành công', 'variant_id' => $productId];
        }

        return ['success' => false, 'message' => 'Lỗi hệ thống'];
    }

    private function generateSKU($baseSku, $color, $storage) {
        if (isset($this->colorMapSku[$color])) {
            $colorSlug = $this->colorMapSku[$color];
        } else {
            $colorSlug = strtoupper(str_replace(' ', '', $this->removeVietnameseAccents($color)));
        }
        $storageSlug = str_replace(' ', '', $storage);
        return "$baseSku-$colorSlug-$storageSlug";
    }

    private function uploadImage($file, $oldImage = null) {
        if ($oldImage && file_exists($oldImage)) @unlink($oldImage);
        
        $targetDir = "uploads/variants/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

        $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        $fileName = uniqid('variant_', true) . '.' . $ext;
        $targetFile = $targetDir . $fileName;

        return move_uploaded_file($file["tmp_name"], $targetFile) ? $targetFile : '';
    }

    private function removeVietnameseAccents($str) {
        $vietnamese = ['à', 'á', 'ạ', 'ả', 'ã', 'â', 'ầ', 'ấ', 'ậ', 'ẩ', 'ẫ', 'ă', 'ằ', 'ắ', 'ặ', 'ẳ', 'ẵ', 'è', 'é', 'ẹ', 'ẻ', 'ẽ', 'ê', 'ề', 'ế', 'ệ', 'ể', 'ễ', 'ì', 'í', 'ị', 'ỉ', 'ĩ', 'ò', 'ó', 'ọ', 'ỏ', 'õ', 'ô', 'ồ', 'ố', 'ộ', 'ổ', 'ỗ', 'ơ', 'ờ', 'ớ', 'ợ', 'ở', 'ỡ', 'ù', 'ú', 'ụ', 'ủ', 'ũ', 'ư', 'ừ', 'ứ', 'ự', 'ử', 'ữ', 'ỳ', 'ý', 'ỵ', 'ỷ', 'ỹ', 'đ', 'À', 'Á', 'Ạ', 'Ả', 'Ã', 'Â', 'Ầ', 'Ấ', 'Ậ', 'Ẩ', 'Ẫ', 'Ă', 'Ằ', 'Ắ', 'Ặ', 'Ẳ', 'Ẵ', 'È', 'É', 'Ẹ', 'Ẻ', 'Ẽ', 'Ê', 'Ề', 'Ế', 'Ệ', 'Ể', 'Ễ', 'Ì', 'Í', 'Ị', 'Ỉ', 'Ĩ', 'Ò', 'Ó', 'Ọ', 'Ỏ', 'Õ', 'Ô', 'Ồ', 'Ố', 'Ộ', 'Ổ', 'Ỗ', 'Ơ', 'Ờ', 'Ớ', 'Ợ', 'Ở', 'Ỡ', 'Ù', 'Ú', 'Ụ', 'Ủ', 'Ũ', 'Ư', 'Ừ', 'Ứ', 'Ự', 'Ử', 'Ữ', 'Ỳ', 'Ý', 'Ỵ', 'Ỷ', 'Ỹ', 'Đ'];
        $latin = ['a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'y', 'y', 'y', 'y', 'y', 'd', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'I', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'Y', 'Y', 'Y', 'Y', 'Y', 'D'];
        return str_replace($vietnamese, $latin, $str);
    }
}