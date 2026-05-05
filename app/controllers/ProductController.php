<?php
// app/controllers/ProductController.php

class ProductController extends Controller {
    private $productModel;
    private $categoryModel;
    private $supplierModel;
    private $variantModel;
    private $imageModel;

    public function __construct() {
        Middleware::auth();
        // Khởi tạo các model cần thiết
        $this->productModel  = $this->model('Product');
        $this->categoryModel = $this->model('Category');
        $this->supplierModel = $this->model('Supplier');
        $this->variantModel  = $this->model('ProductVariant');
        $this->imageModel    = $this->model('ProductImage');
    }

    // ============================================================
    // VIEW ACTIONS (Hiển thị trang)
    // ============================================================

    public function index() {
        $data = [
            'page_title' => "Quản lý Sản phẩm",
            'categories' => $this->categoryModel->getAll(),
            'suppliers'  => $this->supplierModel->getAll(),
            'page_js_file' => 'assets/js/product-index.js'
        ];
        $this->view('products/index', $data);
    }

    public function create() {
        $data = [
            'page_title' => "Thêm Sản phẩm mới",
            'categories' => $this->categoryModel->getAll(),
            'suppliers'  => $this->supplierModel->getAll(),
            'page_js_file' => 'assets/js/product-index.js'
        ];
        $this->view('products/create', $data);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?url=product/create");
            exit;
        }

        try {
            // ✅ BƯỚC 1: Lấy dữ liệu sản phẩm từ form
            $name = trim($_POST['name'] ?? '');
            $sku = trim($_POST['sku'] ?? '');
            $description = $_POST['description'] ?? '';
            $category_id = (int)($_POST['category_id'] ?? 0);
            $supplier_id = (int)($_POST['supplier_id'] ?? 0);
            
            // ✅ BƯỚC 2: Validate dữ liệu sản phẩm
            if (empty($name) || empty($sku)) {
                throw new Exception('Tên sản phẩm và SKU không được để trống');
            }

            if ($category_id <= 0 || $supplier_id <= 0) {
                throw new Exception('Vui lòng chọn danh mục và nhà cung cấp');
            }

            // ✅ BƯỚC 3: Kiểm tra SKU có bị trùng không
            if ($this->productModel->findBySKU($sku)) {
                throw new Exception('SKU này đã tồn tại');
            }

            // ✅ BƯỚC 4: Xử lý upload ảnh (nếu có)
            $image = '';
            if (!empty($_FILES['image']['name'])) {
                $uploadDir = '../public/assets/images/products/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $file = $_FILES['image'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (!in_array($ext, $allowedExt)) {
                    throw new Exception('Chỉ hỗ trợ file jpg, jpeg, png, gif, webp');
                }

                $fileName = uniqid('product_') . '.' . $ext;
                $filePath = $uploadDir . $fileName;

                if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                    throw new Exception('Lỗi upload file');
                }

                $image = $fileName;
            }

            // ✅ BƯỚC 5: Lưu sản phẩm vào database
            $productData = [
                ':sku' => $sku,
                ':name' => $name,
                ':description' => $description,
                ':category_id' => $category_id,
                ':supplier_id' => $supplier_id,
                ':image' => $image
            ];

            if (!$this->productModel->create($productData)) {
                throw new Exception('Không thể lưu sản phẩm');
            }

            // ✅ BƯỚC 6: Lấy ID sản phẩm vừa tạo
            $productId = $this->productModel->getLastId();

            // ✅ BƯỚC 7: Nếu người dùng nhập giá → Tạo biến thể ban đầu
            $color = trim($_POST['color'] ?? '');
            $storage = trim($_POST['storage'] ?? '');
            $price = (int)($_POST['price'] ?? 0);
            $stock = (int)($_POST['stock'] ?? 0);

            // Nếu có giá trị → Tạo biến thể
            if (!empty($color) && !empty($storage) && $price > 0) {
                $variantSku = $sku . '-' . strtoupper(substr($color, 0, 2));
                $variantData = [
                    ':product_id' => $productId,
                    ':sku' => $variantSku,
                    ':color' => $color,
                    ':storage' => $storage,
                    ':price' => $price,
                    ':stock' => $stock,
                    ':image' => ''
                ];
                $this->variantModel->create($variantData);
            }

            // ✅ BƯỚC 8: Chuyển hướng tới trang edit để thêm biến thể tiếp
            $_SESSION['success_message'] = "Thêm sản phẩm '$name' thành công!";
            header("Location: index.php?url=product/edit&id=" . $productId);
            exit;

        } catch (Exception $e) {
            $_SESSION['old_input'] = $_POST;
            $_SESSION['error_message'] = $e->getMessage();
            header("Location: index.php?url=product/create");
            exit;
        }
    }

    public function edit() {
        $id = (int)($_GET['id'] ?? 0);
        $product = $this->productModel->getByID($id);

        if (!$product) die('Không tìm thấy sản phẩm.');

        $data = [
            'page_title' => "Chỉnh sửa: " . $product['name'],
            'product'    => $product,
            'variants'   => $this->variantModel->getByProductID($id),
            'images'     => $this->imageModel->getByProductID($id),
            'categories' => $this->categoryModel->getAll(),
            'suppliers'  => $this->supplierModel->getAll()
        ];
        $this->view('products/edit', $data);
    }

    // ✅ THÊM METHOD UPDATE
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?url=product");
            exit;
        }

        try {
            // ✅ BƯỚC 1: Lấy dữ liệu từ form
            $id = (int)($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $description = $_POST['description'] ?? '';
            $category_id = (int)($_POST['category_id'] ?? 0);
            $supplier_id = (int)($_POST['supplier_id'] ?? 0);

            // ✅ BƯỚC 2: Validate
            if ($id <= 0) {
                throw new Exception('ID sản phẩm không hợp lệ');
            }

            if (empty($name)) {
                throw new Exception('Tên sản phẩm không được để trống');
            }

            if ($category_id <= 0 || $supplier_id <= 0) {
                throw new Exception('Vui lòng chọn danh mục và nhà cung cấp');
            }

            // ✅ BƯỚC 3: Lấy sản phẩm cũ để kiểm tra
            $product = $this->productModel->getByID($id);
            if (!$product) {
                throw new Exception('Sản phẩm không tồn tại');
            }

            // ✅ BƯỚC 4: Xử lý upload ảnh (nếu có)
            $image = $product['image'];  // Giữ ảnh cũ mặc định
            if (!empty($_FILES['image']['name'])) {
                $uploadDir = '../public/assets/images/products/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $file = $_FILES['image'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (!in_array($ext, $allowedExt)) {
                    throw new Exception('Chỉ hỗ trợ file jpg, jpeg, png, gif, webp');
                }

                $fileName = uniqid('product_') . '.' . $ext;
                $filePath = $uploadDir . $fileName;

                if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                    throw new Exception('Lỗi upload file');
                }

                // Xóa ảnh cũ nếu có
                if (!empty($product['image'])) {
                    $oldPath = '../public/assets/images/products/' . $product['image'];
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                }

                $image = $fileName;
            }

            // ✅ BƯỚC 5: Cập nhật sản phẩm
            $updateData = [
                ':id' => $id,
                ':name' => $name,
                ':description' => $description,
                ':category_id' => $category_id,
                ':supplier_id' => $supplier_id,
                ':image' => $image
            ];

            if (!$this->productModel->update($updateData)) {
                throw new Exception('Không thể cập nhật sản phẩm');
            }

            // ✅ BƯỚC 6: Thành công - quay lại trang edit
            $_SESSION['success_message'] = "Cập nhật sản phẩm thành công!";
            header("Location: index.php?url=product/edit&id=" . $id);
            exit;

        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            header("Location: index.php?url=product/edit&id=" . ($_POST['id'] ?? 0));
            exit;
        }
    }

    // ============================================================
    // AJAX ACTIONS
    // ============================================================

    public function ajax_list() {
        set_time_limit(60);
        
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        header('Access-Control-Allow-Origin: *');
        
        try {
            $params = $this->getFilterParams();
            $limit = 10;
            $offset = ($params['page'] - 1) * $limit;

            $totalRecords = $this->productModel->countAll(
                $params['search'], $params['min_price'], $params['max_price'], 
                $params['category_id'], $params['supplier_id']
            );
            
            if ($totalRecords === false) {
                throw new Exception('Không thể lấy số lượng sản phẩm từ database');
            }
            
            $products = $this->productModel->getAll(
                $params['search'], $params['min_price'], $params['max_price'], 
                $params['category_id'], $params['supplier_id'], $limit, $offset
            );
            
            if ($products === false) {
                throw new Exception('Không thể lấy danh sách sản phẩm từ database');
            }

            $tableHtml = '';
            foreach ($products as $row) {
                $tableHtml .= $this->renderProductRow($row);
            }

            echo json_encode([
                'success' => true,
                'table_html' => $tableHtml ?: $this->renderEmptyState(),
                'pagination_html' => $this->renderPagination($totalRecords, $limit, $params),
                'total_products' => $totalRecords
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false, 
                'message' => 'Lỗi kết nối cơ sở dữ liệu: ' . $e->getMessage()
            ]);
        }
        exit;
    }

    public function ajaxDelete() {
        set_time_limit(30);
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        
        $db = $this->productModel->getDB();
        try {
            $id = (int)($_POST['id'] ?? 0);
            
            if ($id <= 0) {
                throw new Exception('ID sản phẩm không hợp lệ');
            }
            
            $product = $this->productModel->getByID($id);
            if (!$product) throw new Exception('Sản phẩm không tồn tại');

            $db->beginTransaction();

            // 1. Xóa ảnh liên quan & file vật lý
            $images = $this->imageModel->getByProductID($id);
            foreach ($images as $img) { $this->imageModel->deleteByID($img['id']); }

            // 2. Xóa biến thể
            $variants = $this->variantModel->getByProductID($id);
            foreach ($variants as $var) { $this->variantModel->delete($var['id']); }

            // 3. Xóa ảnh đại diện
            if (!empty($product['image']) && file_exists($product['image'])) {
                @unlink($product['image']);
            }

            // 4. Xóa chính sản phẩm
            if ($this->productModel->delete($id)) {
                $db->commit();
                echo json_encode([
                    'success' => true, 
                    'message' => "Đã xóa sản phẩm: {$product['name']}"
                ]);
            } else {
                throw new Exception('Không thể xóa sản phẩm');
            }
        } catch (Exception $e) {
            if ($db->inTransaction()) $db->rollBack();
            http_response_code(500);
            echo json_encode([
                'success' => false, 
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }
    
    public function ajax_get_details() {
        while (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Type: application/json');

        try {
            $id = (int)($_GET['id'] ?? 0);

            $product = $this->productModel->getByID($id);
            $variants = $this->variantModel->getByProductID($id);

            echo json_encode([
                'success' => true,
                'product' => $product,
                'variants' => $variants
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }

        exit;
    }

    // ============================================================
    // EXPORT & IMPORT (EXCEL)
    // ============================================================
    public function import() {
        $this->view('products/import', ['page_title' => 'Import sản phẩm từ Excel']);
    }
    public function exportTemplate() {
        // Tắt hiển thị lỗi để tránh làm hỏng file Excel
        error_reporting(0);
        ini_set('display_errors', 0);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import San Pham');

        // Thiết lập tiêu đề cột
        $headers = [
            'A1' => 'Mã SKU',
            'B1' => 'Tên sản phẩm',
            'C1' => 'ID Danh mục',
            'D1' => 'ID Nhà cung cấp',
            'E1' => 'Mô tả',
            'F1' => 'Màu sắc',
            'G1' => 'Dung lượng',
            'H1' => 'Giá bán',
            'I1' => 'Số lượng tồn'
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
            $sheet->getStyle($cell)->getFont()->setBold(true);
        }

        // Tự động giãn độ rộng cột
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'template_import_products_' . time() . '.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }



public function exportProducts() {
    error_reporting(0);
    ini_set('display_errors', 0);
    while (ob_get_level()) ob_end_clean();

    try {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Danh sach san pham');

        // Header
        $headers = ['ID', 'Mã SKU', 'Tên sản phẩm', 'Danh mục', 'Nhà cung cấp', 
                    'Màu sắc', 'Dung lượng', 'SKU biến thể', 'Giá bán (VND)', 'Tồn kho', 'Ngày tạo'];
        foreach ($headers as $i => $label) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue($col . '1', $label);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
        }
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Dữ liệu
        // Header khớp với template import (9 cột A-I)
$headers = [
    'A' => 'Mã SKU',
    'B' => 'Tên sản phẩm',
    'C' => 'ID Danh mục',
    'D' => 'ID Nhà cung cấp',
    'E' => 'Mô tả',
    'F' => 'Màu sắc',
    'G' => 'Dung lượng',
    'H' => 'Giá',
    'I' => 'Tồn kho',
];
foreach ($headers as $col => $label) {
    $sheet->setCellValue($col . '1', $label);
    $sheet->getStyle($col . '1')->getFont()->setBold(true);
}

        // Dữ liệu — mỗi biến thể 1 dòng, dùng import lại được
        $products = $this->productModel->getAllForExport();
        $rowNum = 2;
        foreach ($products as $p) {
            $sheet->setCellValue('A' . $rowNum, $p['sku']);           // SKU sản phẩm (không phải SKU biến thể)
            $sheet->setCellValue('B' . $rowNum, $p['name']);
            $sheet->setCellValue('C' . $rowNum, $p['category_id']);   // ID để import lại được
            $sheet->setCellValue('D' . $rowNum, $p['supplier_id']);   // ID để import lại được
            $sheet->setCellValue('E' . $rowNum, $p['description'] ?? '');
            $sheet->setCellValue('F' . $rowNum, $p['color'] ?? '');
            $sheet->setCellValue('G' . $rowNum, $p['storage'] ?? '');
            $sheet->setCellValue('H' . $rowNum, $p['price'] ?? 0);
            $sheet->setCellValue('I' . $rowNum, $p['stock'] ?? 0);
            $rowNum++;
        }

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->freezePane('A2');
        if ($rowNum > 2) $sheet->setAutoFilter('A1:I' . ($rowNum - 1));

        // Stream thẳng xuống trình duyệt — KHÔNG save ra file
        $filename = 'danh_sach_san_pham_' . date('Ymd_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0, no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');  // ← stream trực tiếp, không cần view

    } catch (Exception $e) {
        http_response_code(500);
        die('Lỗi xuất Excel: ' . $e->getMessage());
    }
    exit;
}

    public function importProcess() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['excel_file'])) {
            header("Location: index.php?url=product/import");
            exit;
        }

        $file = $_FILES['excel_file']['tmp_name'];
        $db = $this->productModel->getDB();
        
        $stats = [
            'total_rows' => 0,
            'products_created' => 0,
            'products_updated' => 0,
            'variants_created' => 0,
            'variants_updated' => 0,
            'skipped_rows' => 0,
            'errors' => []
        ];

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
            $data = $spreadsheet->getActiveSheet()->toArray();
            
            $db->beginTransaction();

            // Bỏ qua dòng đầu tiên (header)
            for ($i = 1; $count = count($data), $i < $count; $i++) {
                $rowNum = $i + 1;
                $sku         = trim($data[$i][0] ?? '');
                $name        = trim($data[$i][1] ?? '');
                $categoryId  = (int)($data[$i][2] ?? 0);
                $supplierId  = (int)($data[$i][3] ?? 0);
                $description = $data[$i][4] ?? '';
                $color       = trim($data[$i][5] ?? '');
                $storage     = trim($data[$i][6] ?? '');
                $price       = (float)($data[$i][7] ?? 0);
                $stock       = (int)($data[$i][8] ?? 0);

                $stats['total_rows']++;

                if (empty($sku) || empty($name)) {
                    $stats['skipped_rows']++;
                    $stats['errors'][] = [
                        'row' => $rowNum,
                        'data' => compact('sku', 'name'),
                        'message' => 'SKU hoặc Tên sản phẩm không được để trống'
                    ];
                    continue;
                }

                try {
                    // 1. Kiểm tra/Tạo sản phẩm
                    $existingProduct = $this->productModel->findBySKU($sku);
                    if ($existingProduct) {
                        $productId = $existingProduct['id'];
                        $stats['products_updated']++;
                    } else {
                        $productData = [
                            ':sku' => $sku,
                            ':name' => $name,
                            ':description' => $description,
                            ':category_id' => $categoryId,
                            ':supplier_id' => $supplierId,
                            ':image' => ''
                        ];
                        $this->productModel->create($productData);
                        $productId = $this->productModel->getLastId();
                        $stats['products_created']++;
                    }

                    // 2. Kiểm tra biến thể đã tồn tại chưa
                    $isVariantExist = $this->variantModel->variantExists($productId, $color, $storage);

                    if (!$isVariantExist) {
                        $variantData = [
                            ':product_id' => $productId,
                            ':sku'        => $sku . '-' . strtoupper($color) . '-' . strtoupper(str_replace(' ', '', $storage)),
                            ':color'      => $color,
                            ':storage'    => $storage,
                            ':price'      => $price,
                            ':stock'      => $stock,
                            ':image'      => ''
                        ];
                        $this->variantModel->create($variantData);
                        $stats['variants_created']++;
                    } else {
                        $stats['variants_updated']++;
                    }
                } catch (Exception $e) {
                    $stats['skipped_rows']++;
                    $stats['errors'][] = [
                        'row' => $rowNum,
                        'data' => compact('sku', 'name'),
                        'message' => $e->getMessage()
                    ];
                }
            }

            $db->commit();
            $_SESSION['import_stats'] = $stats;
            header("Location: index.php?url=product/importResult");

        } catch (Exception $e) {
            if ($db->inTransaction()) $db->rollBack();
            $_SESSION['error_message'] = "Lỗi Import: " . $e->getMessage();
            header("Location: index.php?url=product/import");
        }
        exit;
    }

    public function importResult() {
        $stats = $_SESSION['import_stats'] ?? null;
        unset($_SESSION['import_stats']);
        
        $data = [
            'page_title' => 'Kết quả Import',
            'stats' => $stats
        ];
        $this->view('products/import_result', $data);
    }

    // ============================================================
    // HELPERS (Các hàm bổ trợ riêng tư)
    // ============================================================

    private function getFilterParams() {
        return [
            'search'      => $_GET['search'] ?? '',
            'min_price'   => (int)($_GET['min_price'] ?? 0),
            'max_price'   => (int)($_GET['max_price'] ?? 0),
            'category_id' => (int)($_GET['category_id'] ?? 0),
            'supplier_id' => (int)($_GET['supplier_id'] ?? 0),
            'page'        => max(1, (int)($_GET['page'] ?? 1))
        ];
    }

    private function renderProductRow($row) {
        $base_url = URLROOT . '/public/';
    
        if (!empty($row['image'])) {
            // Đơn giản - nếu chứa 'assets/images', prepend URLROOT/public/
            // Nếu không, giả sử là path đầy đủ từ public/
            if (strpos($row['image'], 'assets/images') !== false) {
                $src = $base_url . $row['image'];
            } else {
                $src = $base_url . 'assets/images/products/' . $row['image'];
            }
            $image = '<img src="' . htmlspecialchars($src) . '" class="product-thumbnail" alt="Product">';
        } else {
            $image = '<div class="product-thumbnail bg-light d-flex align-items-center justify-content-center"><i class="fas fa-image text-muted"></i></div>';
        }

        return '
            <tr class="fade-in">
                <td>' . $image . '</td>
                <td><span class="product-sku">' . htmlspecialchars($row['sku']) . '</span></td>
                <td>' . htmlspecialchars($row['name']) . '</td>
                <td>' . htmlspecialchars($row['category_name'] ?? 'N/A') . '</td>
                <td>' . htmlspecialchars($row['supplier_name'] ?? 'N/A') . '</td>
                <td>
                    <span class="price-badge">
                        ' . number_format($row['min_price'] ?? 0) . ' - ' . number_format($row['max_price'] ?? 0) . ' đ
                    </span>
                </td>
                <td><span class="stock-badge">' . intval($row['total_stock']) . '</span></td>
                <td>' . (!empty($row['created_at']) ? date('d/m/Y', strtotime($row['created_at'])) : 'N/A') . '</td>
                <td>
                    <div class="action-buttons">
                        <a href="index.php?url=product/edit&id=' . $row['id'] . '" class="btn btn-warning-modern btn-sm">
                            <i class="fas fa-edit"></i> Sửa
                        </a>
                        <button class="btn btn-danger-modern btn-sm btn-delete-product" data-id="' . $row['id'] . '">
                            <i class="fas fa-trash"></i> Xóa
                        </button>
                    </div>
                </td>
            </tr>';
    }

    private function renderEmptyState() {
        return '<tr><td colspan="9" class="text-center py-5">Không tìm thấy sản phẩm</td></tr>';
    }

        /**
     * Vẽ thanh phân trang cho AJAX
     * * @param int $total Tổng số bản ghi
     * @param int $limit Số bản ghi trên mỗi trang
     * @param array $params Các tham số lọc hiện tại (để lấy trang hiện tại)
     * @return string Chuỗi HTML của phân trang
     */
    private function renderPagination($total, $limit, $params) {
        $totalPages = ceil($total / $limit);
        
        // Nếu chỉ có 1 trang thì không hiện phân trang
        if ($totalPages <= 1) return '';

        $currentPage = $params['page'];
        $html = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center mb-0">';

        // --- Nút TRƯỚC (PREVIOUS) ---
        $disabledPrev = ($currentPage <= 1) ? 'disabled' : '';
        $prevPage = ($currentPage > 1) ? $currentPage - 1 : 1;
        $html .= '
            <li class="page-item ' . $disabledPrev . '">
                <a class="page-link" href="javascript:void(0)" data-page="' . $prevPage . '" aria-label="Previous">
                    <span aria-hidden="true"><i class="fas fa-chevron-left"></i></span>
                </a>
            </li>';

        // --- Các số trang ---
        // Hiển thị tối đa 5 trang xung quanh trang hiện tại để tránh bị tràn giao diện nếu có quá nhiều trang
        $start = max(1, $currentPage - 2);
        $end = min($totalPages, $currentPage + 2);

        if ($start > 1) {
            $html .= '<li class="page-item"><a class="page-link" href="javascript:void(0)" data-page="1">1</a></li>';
            if ($start > 2) $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }

        for ($i = $start; $i <= $end; $i++) {
            $active = ($i == $currentPage) ? 'active' : '';
            $html .= '<li class="page-item ' . $active . '">
                        <a class="page-link" href="javascript:void(0)" data-page="' . $i . '">' . $i . '</a>
                    </li>';
        }

        if ($end < $totalPages) {
            if ($end < $totalPages - 1) $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            $html .= '<li class="page-item"><a class="page-link" href="javascript:void(0)" data-page="' . $totalPages . '">' . $totalPages . '</a></li>';
        }

        // --- Nút SAU (NEXT) ---
        $disabledNext = ($currentPage >= $totalPages) ? 'disabled' : '';
        $nextPage = ($currentPage < $totalPages) ? $currentPage + 1 : $totalPages;
        $html .= '
            <li class="page-item ' . $disabledNext . '">
                <a class="page-link" href="javascript:void(0)" data-page="' . $nextPage . '" aria-label="Next">
                    <span aria-hidden="true"><i class="fas fa-chevron-right"></i></span>
                </a>
            </li>';

        $html .= '</ul></nav>';

        return $html;
    }
}
?>