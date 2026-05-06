<?php
class Product
{
    private $db;
    private $table = "products";

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Đếm tổng số sản phẩm có áp dụng bộ lọc (để tính tổng số trang)
     */
    public function countAll($search = '', $min_price = 0, $max_price = 0, $category_id = 0, $supplier_id = 0) {
        $query = "
            SELECT COUNT(*) as total
            FROM (
                SELECT p.id, MIN(pv.price) as min_price, MAX(pv.price) as max_price
                FROM {$this->table} p
                LEFT JOIN product_variants pv ON p.id = pv.product_id
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN suppliers s ON p.supplier_id = s.id
        ";

        $params = [];
        $where_clauses = [];

        if (!empty($search)) {
            $where_clauses[] = "(p.name LIKE :search1 OR p.sku LIKE :search2)";
            $params[':search1'] = '%' . $search . '%';
            $params[':search2'] = '%' . $search . '%';
        }
        if ($category_id > 0) {
            $where_clauses[] = "p.category_id = :category_id";
            $params[':category_id'] = $category_id;
        }
        if ($supplier_id > 0) {
            $where_clauses[] = "p.supplier_id = :supplier_id";
            $params[':supplier_id'] = $supplier_id;
        }

        if (!empty($where_clauses)) {
            $query .= " WHERE " . implode(' AND ', $where_clauses);
        }

        $query .= " GROUP BY p.id, c.name, s.name ";

        $having_clauses = [];
        if ($min_price > 0) {
            $having_clauses[] = "MIN(pv.price) >= :min_price";
            $params[':min_price'] = $min_price;
        }
        if ($max_price > 0) {
            $having_clauses[] = "MAX(pv.price) <= :max_price";
            $params[':max_price'] = $max_price;
        }

        if (!empty($having_clauses)) {
            $query .= " HAVING " . implode(' AND ', $having_clauses);
        }

        $query .= ") AS filtered_products";

        $stmt = $this->db->query($query, $params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }

    /**
     * Lấy danh sách sản phẩm (Phân trang + Lọc)
     */
    public function getAll($search = '', $min_price = 0, $max_price = 0, $category_id = 0, $supplier_id = 0, $limit = 10, $offset = 0) {
        $query = "
            SELECT 
                p.*, c.name AS category_name, s.name AS supplier_name,
                SUM(pv.stock) as total_stock,
                MIN(pv.price) as min_price,
                MAX(pv.price) as max_price
            FROM {$this->table} p
            LEFT JOIN product_variants pv ON p.id = pv.product_id
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN suppliers s ON p.supplier_id = s.id
        ";

        $params = [];
        $where_clauses = [];

        if (!empty($search)) {
            $where_clauses[] = "(p.name LIKE :search1 OR p.sku LIKE :search2)";
            $params[':search1'] = '%' . $search . '%';
            $params[':search2'] = '%' . $search . '%';
        }
        if ($category_id > 0) {
            $where_clauses[] = "p.category_id = :category_id";
            $params[':category_id'] = $category_id;
        }
        if ($supplier_id > 0) {
            $where_clauses[] = "p.supplier_id = :supplier_id";
            $params[':supplier_id'] = $supplier_id;
        }

        if (!empty($where_clauses)) {
            $query .= " WHERE " . implode(' AND ', $where_clauses);
        }

        $query .= " GROUP BY p.id, c.name, s.name ";

        $having_clauses = [];
        if ($min_price > 0) {
            $having_clauses[] = "min_price >= :min_price";
            $params[':min_price'] = $min_price;
        }
        if ($max_price > 0) {
            $having_clauses[] = "max_price <= :max_price";
            $params[':max_price'] = $max_price;
        }

        if (!empty($having_clauses)) {
            $query .= " HAVING " . implode(' AND ', $having_clauses);
        }

        $query .= " ORDER BY p.created_at DESC LIMIT $limit OFFSET $offset";

        $stmt = $this->db->query($query, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByID($id) {
        $query = "
            SELECT p.*, c.name AS category_name, s.name AS supplier_name
            FROM {$this->table} p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN suppliers s ON p.supplier_id = s.id
            WHERE p.id = :id LIMIT 1
        ";
        return $this->db->query($query, [':id' => $id])->fetch(PDO::FETCH_ASSOC);
    }

    public function findBySKU($sku) {
        $query = "SELECT * FROM {$this->table} WHERE sku = :sku LIMIT 1";
        return $this->db->query($query, [':sku' => $sku])->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                    (sku, name, description, category_id, supplier_id, image) 
                  VALUES 
                    (:sku, :name, :description, :category_id, :supplier_id, :image)";
        return $this->db->query($query, $data);
    }

    public function update($data) {
        $query = "UPDATE {$this->table}
                  SET name = :name, description = :description, 
                      category_id = :category_id, supplier_id = :supplier_id, 
                      image = :image, updated_at = NOW()
                  WHERE id = :id";
        return $this->db->query($query, $data);
    }

    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        return $this->db->query($query, [':id' => $id]);
    }

    public function getDB() {
        return $this->db;
    }

    public function getAllForExport() {
        $sql = "SELECT p.id, p.sku, p.name, p.description, p.category_id, p.supplier_id,
                    c.name as category_name, s.name as supplier_name,
                    v.color, v.storage, v.price, v.stock
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN suppliers s ON p.supplier_id = s.id
                LEFT JOIN product_variants v ON p.id = v.product_id
                ORDER BY p.id DESC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLastId() {
        return $this->db->lastInsertId();
    }
}
?>