<?php
// app/controllers/ProductController.php

class ProductController extends Controller {
    private $productModel;
    private $categoryModel;
    private $supplierModel;
    private $variantModel;
    private $imageModel;

    public function __construct() {
        // Middleware::auth();
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
            error_log("DEBUG ajax_list: params = " . json_encode($params));
            
            $limit = 10;
            $offset = ($params['page'] - 1) * $limit;
            error_log("DEBUG ajax_list: page=" . $params['page'] . ", limit=" . $limit . ", offset=" . $offset);

            $totalRecords = $this->productModel->countAll(
                $params['search'], $params['min_price'], $params['max_price'], 
                $params['category_id'], $params['supplier_id']
            );
            error_log("DEBUG ajax_list: totalRecords = " . $totalRecords);
            
            if ($totalRecords === false) {
                throw new Exception('Không thể lấy số lượng sản phẩm từ database');
            }
            
            $products = $this->productModel->getAll(
                $params['search'], $params['min_price'], $params['max_price'], 
                $params['category_id'], $params['supplier_id'], $limit, $offset
            );
            error_log("DEBUG ajax_list: retrieved " . count($products) . " products");
            
            if ($products === false) {
                throw new Exception('Không thể lấy danh sách sản phẩm từ database');
            }

            $tableHtml = '';
            foreach ($products as $row) {
                $tableHtml .= $this->renderProductRow($row);
            }

            $paginationHtml = $this->renderPagination($totalRecords, $limit, $params);
            error_log("DEBUG ajax_list: pagination HTML length = " . strlen($paginationHtml));

            echo json_encode([
                'success' => true,
                'table_html' => $tableHtml ?: $this->renderEmptyState(),
                'pagination_html' => $paginationHtml,
                'total_products' => $totalRecords
            ]);
        } catch (Exception $e) {
            error_log("ERROR ajax_list: " . $e->getMessage());
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
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Header
        $headers = ['ID', 'Mã SKU', 'Tên sản phẩm', 'Danh mục', 'Nhà cung cấp', 'Màu sắc', 'Dung lượng', 'Giá bán', 'Tồn kho'];
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $sheet->getStyle($column . '1')->getFont()->setBold(true);
            $column++;
        }

        // Lấy dữ liệu (Sử dụng Query trực tiếp hoặc qua Model)
        $products = $this->productModel->getAllForExport();

        $rowNum = 2;
        foreach ($products as $p) {
            $sheet->setCellValue('A' . $rowNum, $p['id']);
            $sheet->setCellValue('B' . $rowNum, $p['sku']);
            $sheet->setCellValue('C' . $rowNum, $p['name']);
            $sheet->setCellValue('D' . $rowNum, $p['category_name']);
            $sheet->setCellValue('E' . $rowNum, $p['supplier_name']);
            $sheet->setCellValue('F' . $rowNum, $p['color']);
            $sheet->setCellValue('G' . $rowNum, $p['storage']);
            $sheet->setCellValue('H' . $rowNum, $p['price']);
            $sheet->setCellValue('I' . $rowNum, $p['stock']);
            $rowNum++;
        }

        $filename = 'danh_sach_san_pham_' . date('Ymd_His') . '.xlsx';
        $path = 'exports/' . $filename;
        
        if (!is_dir('exports')) mkdir('exports', 0777, true);
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($path);

        // Trả về view thông báo tải xuống
        $this->view('products/export_success', ['filename' => $filename]);
    }   

    public function importProcess() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['excel_file'])) {
            header("Location: index.php?controller=product");
            exit;
        }

        $file = $_FILES['excel_file']['tmp_name'];
        $db = $this->productModel->getDB();
        $logs = [];

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
            $data = $spreadsheet->getActiveSheet()->toArray();
            
            $db->beginTransaction();

            // Bỏ qua dòng đầu tiên (header)
            for ($i = 1; $count = count($data), $i < $count; $i++) {
                $sku         = trim($data[$i][0] ?? '');
                $name        = trim($data[$i][1] ?? '');
                $categoryId  = (int)($data[$i][2] ?? 0);
                $supplierId  = (int)($data[$i][3] ?? 0);
                $description = $data[$i][4] ?? '';
                $color       = trim($data[$i][5] ?? '');
                $storage     = trim($data[$i][6] ?? '');
                $price       = (float)($data[$i][7] ?? 0);
                $stock       = (int)($data[$i][8] ?? 0);

                if (empty($sku) || empty($name)) continue;

                // 1. Kiểm tra/Tạo sản phẩm
                $existingProduct = $this->productModel->findBySKU($sku);
                if ($existingProduct) {
                    $productId = $existingProduct['id'];
                } else {
                    $productData = [
                        ':sku' => $sku,
                        ':name' => $name,
                        ':description' => $description,
                        ':category_id' => $categoryId,
                        ':supplier_id' => $supplierId,
                        ':image' => '' // Mặc định trống khi import
                    ];
                    $this->productModel->create($productData);
                    $productId = $this->productModel->getLastId();
                    $logs[] = "Dòng $i: Tạo mới sản phẩm $name (SKU: $sku).";
                }

                // 2. Kiểm tra biến thể đã tồn tại chưa (Dựa trên Model ProductVariant)
                // Sử dụng hàm variantExists bạn đã viết trong ProductVariant model
                $isVariantExist = $this->variantModel->variantExists($productId, $color, $storage);

                if (!$isVariantExist) {
                    $variantData = [
                        ':product_id' => $productId,
                        ':sku'        => $sku . '-' . strtoupper($color), // Tạo SKU biến thể tạm thời
                        ':color'      => $color,
                        ':storage'    => $storage,
                        ':price'      => $price,
                        ':stock'      => $stock,
                        ':image'      => ''
                    ];
                    $this->variantModel->create($variantData);
                    $logs[] = "Dòng $i: Thêm biến thể ($color - $storage) cho SKU $sku.";
                } else {
                    $logs[] = "Dòng $i: Biến thể ($color - $storage) đã tồn tại, bỏ qua.";
                }
            }

            $db->commit();
            $_SESSION['success_message'] = "Import hoàn tất!";
            $_SESSION['import_logs'] = $logs;

        } catch (Exception $e) {
            if ($db->beginTransaction()) $db->rollBack(); // Kiểm tra nếu đang trong transaction thì rollback
            $_SESSION['error_message'] = "Lỗi Import: " . $e->getMessage();
        }

        header("Location: index.php?controller=product");
        exit;
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

       $currentPage = isset($params['page']) ? (int)$params['page'] : 1;
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
// <a class="page-link" href="javascript:void(0)" data-page="2">2</a>
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