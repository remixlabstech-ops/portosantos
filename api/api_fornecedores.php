<?php

// Assuming we are using PDO for database operations

class SuppliersAPI {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    // Create a new supplier
    public function createSupplier($data) {
        $sql = "INSERT INTO suppliers (name, email, address) VALUES (:name, :email, :address)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    // Read a supplier by ID
    public function getSupplier($id) {
        $sql = "SELECT * FROM suppliers WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // Update a supplier
    public function updateSupplier($id, $data) {
        $data[':id'] = $id;
        $sql = "UPDATE suppliers SET name = :name, email = :email, address = :address WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    // Delete a supplier
    public function deleteSupplier($id) {
        $sql = "DELETE FROM suppliers WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    // Search for suppliers
    public function searchSuppliers($query) {
        $sql = "SELECT * FROM suppliers WHERE name LIKE :query";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':query' => "%$query%"]);
        return $stmt->fetchAll();
    }
}

?>