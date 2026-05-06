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
        // Khởi tạo model đúng theo tên class trong app/models
        $this->productModel = $this->model('Product');
        $this->variantModel = $this->model('ProductVariant');
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
            header("Location: index.php?url=product/edit&id=" . $product_id);
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
                ':id' => $id,
                ':sku' => $existing['sku'],
                ':color' => $existing['color'],
                ':storage' => $existing['storage'],
                ':price' => $price,
                ':stock' => $stock,
                ':image' => $imageUrl
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
        header("Location: index.php?url=product/edit&id=" . $product_id);
    }

    public function ajaxDelete() {
        header('Content-Type: application/json');
        try {
            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new Exception('ID biến thể không hợp lệ');
            }

            $variant = $this->variantModel->getByID($id);
            if (!$variant) {
                throw new Exception('Biến thể không tồn tại');
            }

            if (!$this->variantModel->delete($id)) {
                throw new Exception('Không thể xóa biến thể');
            }

            if (!empty($variant['image']) && file_exists($variant['image'])) {
                @unlink($variant['image']);
            }

            echo json_encode(['success' => true, 'message' => 'Xóa biến thể thành công!']);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    // ========== HELPERS (Private) ==========

    private function handleStore($productId, $postData, $fileData, $isAjax = false) {
        $product = $this->productModel->getByID($productId);
        if (!$product) return ['success' => false, 'message' => 'Sản phẩm không tồn tại'];

        $color = trim($postData['color'] ?? '');
        $storage = trim($postData['storage'] ?? '');
        $price = (float)($postData['price'] ?? 0);
        $stock = (int)($postData['stock'] ?? 0);

        if ($color === '' || $storage === '') {
            return ['success' => false, 'message' => 'Vui lòng chọn màu sắc và dung lượng'];
        }
        if ($price <= 0) {
            return ['success' => false, 'message' => 'Giá bán phải lớn hơn 0'];
        }
        if ($stock < 0) {
            return ['success' => false, 'message' => 'Tồn kho không hợp lệ'];
        }

        // Logic tạo SKU
        $sku = $this->generateSKU($product['sku'], $color, $storage);

        // Kiểm tra tồn tại
        $existingVariant = $this->variantModel->findByProductColorStorage($productId, $color, $storage);
        if ($existingVariant) {
            return [
                'success' => false,
                'message' => "Biến thể {$color} - {$storage} đã tồn tại",
                'type' => 'warning',
                'existing_id' => (int)$existingVariant['id']
            ];
        }

        if ($this->variantModel->findBySKU($sku)) {
            return ['success' => false, 'message' => "Biến thể với SKU {$sku} đã tồn tại"];
        }

        $imagePath = '';
        if (isset($fileData['image']) && $fileData['image']['error'] == 0) {
            $imagePath = $this->uploadImage($fileData['image']);
        }

        $data = [
            ':product_id' => $productId,
            ':sku' => $sku,
            ':color' => $color,
            ':storage' => $storage,
            ':price' => $price,
            ':stock' => $stock,
            ':image' => $imagePath
        ];

        if ($this->variantModel->create($data)) {
            $createdVariant = $this->variantModel->findBySKU($sku);
            return [
                'success' => true,
                'message' => 'Thêm biến thể thành công',
                'variant_id' => (int)($createdVariant['id'] ?? 0),
                'variant_html' => $this->renderVariantRowHtml($createdVariant ?: [
                    'id' => 0,
                    'sku' => $sku,
                    'color' => $color,
                    'storage' => $storage,
                    'price' => $price,
                    'stock' => $stock,
                    'image' => $imagePath
                ])
            ];
        }

        return ['success' => false, 'message' => 'Không thể thêm biến thể'];
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

    private function renderVariantRowHtml($variant) {
        $id = (int)($variant['id'] ?? 0);
        $image = !empty($variant['image'])
            ? '<img src="' . htmlspecialchars($variant['image']) . '" class="variant-thumbnail" alt="' . htmlspecialchars($variant['color']) . '">'
            : '<i class="fas fa-image text-muted"></i>';

        return '
            <tr id="variant-' . $id . '">
                <td>' . $image . '</td>
                <td><span class="badge bg-secondary">' . htmlspecialchars($variant['sku']) . '</span></td>
                <td>' . htmlspecialchars($variant['color']) . '</td>
                <td>' . htmlspecialchars($variant['storage']) . '</td>
                <td><span id="variant-price-' . $id . '">' . number_format((float)$variant['price']) . ' đ</span></td>
                <td><span id="variant-stock-' . $id . '">' . (int)$variant['stock'] . '</span></td>
                <td>
                    <button type="button"
                        class="btn btn-warning-modern btn-sm btn-edit-variant"
                        data-id="' . $id . '"
                        data-color="' . htmlspecialchars($variant['color']) . '"
                        data-storage="' . htmlspecialchars($variant['storage']) . '"
                        data-price="' . (float)$variant['price'] . '"
                        data-stock="' . (int)$variant['stock'] . '">
                        <i class="fas fa-edit"></i> Sửa
                    </button>
                    <button type="button"
                        class="btn btn-danger-modern btn-sm btn-delete-variant"
                        data-id="' . $id . '"
                        data-name="' . htmlspecialchars($variant['color'] . ' - ' . $variant['storage']) . '">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>';
    }
}