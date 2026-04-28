<?php
class ProductVariant
{
    private $db;
    private $table = "product_variants";

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getByProductID($product_id)
    {
        $query = "SELECT * FROM {$this->table} WHERE product_id = :product_id ORDER BY color, storage";
        $stmt = $this->db->query($query, [':product_id' => $product_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByID($id)
    {
        $query = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->db->query($query, [':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findBySKU($sku)
    {
        $query = "SELECT * FROM {$this->table} WHERE sku = :sku LIMIT 1";
        $stmt = $this->db->query($query, [':sku' => $sku]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $query = "INSERT INTO {$this->table} 
                    (product_id, sku, color, storage, price, stock, image) 
                  VALUES 
                    (:product_id, :sku, :color, :storage, :price, :stock, :image)";
        return $this->db->query($query, $data);
    }

    public function update($data)
    {
        $query = "UPDATE {$this->table}
                  SET sku=:sku, color=:color, storage=:storage, price=:price, 
                      stock=:stock, image=:image, updated_at=NOW()
                  WHERE id=:id";
        return $this->db->query($query, $data);
    }

    public function delete($id)
    {
        $query = "DELETE FROM {$this->table} WHERE id=:id";
        return $this->db->query($query, [':id' => $id]);
    }

    public function findByProductColorStorage($product_id, $color, $storage)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE product_id = :product_id 
                AND color = :color 
                AND storage = :storage 
                LIMIT 1";

        $params = [
            ':product_id' => $product_id,
            ':color' => trim($color),
            ':storage' => trim($storage)
        ];

        $stmt = $this->db->query($sql, $params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function variantExists($product_id, $color, $storage) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE product_id = :product_id 
                AND LOWER(TRIM(color)) = LOWER(TRIM(:color))
                AND TRIM(REPLACE(storage, ' ', '')) = TRIM(REPLACE(:storage, ' ', ''))";
        
        $params = [
            ':product_id' => (int)$product_id,
            ':color' => $color,
            ':storage' => $storage
        ];
        
        $stmt = $this->db->query($sql, $params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($result['count'] > 0);
    }
}
?>