<?php
class Category
{
    private $db;
    private $table = "categories";

    public function __construct() {
        $this->db = new Database();
    }

    public function getAll() {
        $query = "SELECT * FROM {$this->table} ORDER BY name ASC";
        
        $stmt = $this->db->query($query);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>