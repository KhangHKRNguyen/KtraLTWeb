<?php
class ProductImage {
    private $db;
    private $table = "product_images";

    public function __construct() {
        $this->db = new Database();
    }

    public function getByProductID($product_id) {
        $query = "SELECT * FROM {$this->table} WHERE product_id = :product_id ORDER BY created_at ASC";
        
        $stmt = $this->db->query($query, [':product_id' => $product_id]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addImage($product_id, $image_url) {
        $query = "INSERT INTO {$this->table} (product_id, image_url) VALUES (:product_id, :image_url)";
        
        // Trả về kết quả thực thi (True/False)
        return $this->db->query($query, [
            ':product_id' => $product_id,
            ':image_url' => $image_url
        ]);
    }

    public function getByID($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        
        $stmt = $this->db->query($query, [':id' => $id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteByID($id) {
        $img = $this->getByID($id);
        
        // Xóa file vật lý nếu tồn tại
        if ($img && !empty($img['image_url']) && file_exists($img['image_url'])) {
            unlink($img['image_url']); 
        }
        
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        
        return $this->db->query($query, [':id' => $id]);
    }
}
?>