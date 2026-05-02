<?php
// app/models/User.php
class User {
    private $db;
    private $table = 'users';

    public function __construct() {
        $this->db = new Database();
    }

    public function findByUsername(string $username) {
        $stmt = $this->db->query(
            "SELECT id, username, email, password, full_name, role
             FROM {$this->table} WHERE username = :username LIMIT 1",
            [':username' => $username]
        );
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findByEmail(string $email) {
        $stmt = $this->db->query(
            "SELECT id FROM {$this->table} WHERE email = :email LIMIT 1",
            [':email' => $email]
        );
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function createUser(string $username, string $email, string $hashedPassword, string $fullName = ''): int {
        $this->db->query(
            "INSERT INTO {$this->table} (username, email, password, full_name, role)
             VALUES (:username, :email, :password, :full_name, 'user')",
            [
                ':username'  => $username,
                ':email'     => $email,
                ':password'  => $hashedPassword,
                ':full_name' => $fullName,
            ]
        );
        return (int) $this->db->lastInsertId();
    }
}
