<?php
// app/controllers/ProductImageController.php

class ProductImageController extends Controller {
    private $imageModel;

    public function __construct() {
        // Khởi tạo model xử lý ảnh
        $this->imageModel = $this->model('ProductImageModel');
    }

    // ========== ACTIONS ==========

    /**
     * Upload ảnh qua AJAX (Hỗ trợ cả đơn lẻ và nhiều ảnh)
     */
    public function ajaxStore() {
        header('Content-Type: application/json');
        try {
            $productId = $_POST['product_id'] ?? 0;
            if (!$productId) throw new Exception('Thiếu ID sản phẩm.');
            if (!isset($_FILES['image_url'])) throw new Exception('Không có file được upload.');

            // Chuẩn hóa dữ liệu $_FILES (biến single upload thành array format)
            $files = $this->normalizeFilesArray($_FILES['image_url']);
            $uploadedData = [];

            foreach ($files as $file) {
                if ($file['error'] !== 0) continue;

                // Validate (Size, Mime type)
                $this->validateImage($file);

                // Xử lý upload vật lý
                $targetFile = $this->handlePhysicalUpload($file);

                // Lưu vào CSDL qua Model
                $imageId = $this->imageModel->addImage($productId, $targetFile);
                if ($imageId) {
                    $uploadedData[] = [
                        'id' => $imageId,
                        'image_url' => $targetFile,
                        'image_html' => $this->renderImageItemHtml($imageId, $targetFile)
                    ];
                }
            }

            if (empty($uploadedData)) throw new Exception('Không có ảnh hợp lệ được lưu.');

            echo json_encode([
                'success' => true,
                'message' => 'Upload thành công ' . count($uploadedData) . ' ảnh!',
                'images' => $uploadedData
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * Xóa ảnh qua AJAX
     */
    public function ajaxDelete() {
        header('Content-Type: application/json');
        try {
            $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
            if ($id === 0) throw new Exception('ID không hợp lệ');

            // Model nên đảm nhận việc xóa file vật lý trong hàm delete của nó
            if ($this->imageModel->deleteByID($id)) {
                echo json_encode(['success' => true, 'message' => 'Xóa ảnh thành công!']);
            } else {
                throw new Exception('Xóa ảnh thất bại.');
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * Lấy danh sách ảnh qua AJAX
     */
    public function ajaxList() {
        header('Content-Type: application/json');
        try {
            $productId = $_GET['product_id'] ?? 0;
            $images = $this->imageModel->getByProductID($productId);

            $html = '';
            foreach ($images as $img) {
                $html .= $this->renderImageItemHtml($img['id'], $img['image_url']);
            }

            echo json_encode([
                'success' => true,
                'images_html' => $html,
                'total_images' => count($images)
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * Action Store bình thường (không AJAX)
     */
    public function store() {
        $productId = $_POST['product_id'] ?? 0;
        if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] == 0) {
            try {
                $this->validateImage($_FILES['image_url']);
                $path = $this->handlePhysicalUpload($_FILES['image_url']);
                $this->imageModel->addImage($productId, $path);
                $_SESSION['success_message'] = "Thêm ảnh thành công!";
            } catch (Exception $e) {
                $_SESSION['error_message'] = $e->getMessage();
            }
        }
        header("Location: index.php?controller=product&action=edit&id=" . $productId);
    }

    // ========== HELPERS (Private) ==========

    private function normalizeFilesArray($filePost) {
        $fileArray = [];
        if (!is_array($filePost['name'])) {
            $fileArray[] = $filePost;
        } else {
            foreach ($filePost['name'] as $index => $name) {
                $fileArray[] = [
                    'name' => $name,
                    'type' => $filePost['type'][$index],
                    'tmp_name' => $filePost['tmp_name'][$index],
                    'error' => $filePost['error'][$index],
                    'size' => $filePost['size'][$index]
                ];
            }
        }
        return $fileArray;
    }

    private function validateImage($file) {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if ($file['size'] > $maxSize) throw new Exception("File {$file['name']} quá lớn.");
        
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        if (!in_array($mime, $allowedTypes)) throw new Exception("File {$file['name']} không đúng định dạng ảnh.");
    }

    private function handlePhysicalUpload($file) {
        $targetDir = "uploads/images/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newName = uniqid() . '.' . $ext;
        $targetPath = $targetDir . $newName;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new Exception("Không thể upload file {$file['name']}");
        }
        return $targetPath;
    }

    private function renderImageItemHtml($id, $url) {
        // Tách phần HTML này giúp bảo trì dễ hơn, tránh viết lại 2 lần ở Store và List
        $safeUrl = htmlspecialchars($url);
        return '
            <div class="col-md-3 image-item fade-in" id="image-' . $id . '">
                <div class="card shadow-sm">
                    <img src="' . $safeUrl . '" class="card-img-top" style="height: 200px; object-fit: cover;">
                    <div class="card-body p-2">
                        <button type="button" class="btn btn-danger-modern btn-sm w-100 btn-delete-image" data-id="' . $id . '">
                            <i class="fas fa-trash"></i> Xóa
                        </button>
                    </div>
                </div>
            </div>';
    }
}