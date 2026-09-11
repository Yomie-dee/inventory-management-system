<?php
require_once 'database.php';

class InventoryManager {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function addItem($itemName, $quantity, $supplier, $category) {
        if ($this->checkDuplicate($itemName, $supplier)) {
            throw new Exception("Duplicate entry: Item '{$itemName}' from supplier '{$supplier}' already exists.");
        }

        try {
            $this->db->beginTransaction();

            $query = "INSERT INTO warehouse_item (item_name, quantity, supplier, category) 
                      VALUES (:item_name, :quantity, :supplier, :category)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':item_name', $itemName);
            $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
            $stmt->bindParam(':supplier', $supplier);
            $stmt->bindParam(':category', $category);
            $stmt->execute();

            $this->appendToCSV([$itemName, $quantity, $supplier, $category, date('Y-m-d H:i:s')]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Failed to add item: " . $e->getMessage());
        }
    }

    private function checkDuplicate($itemName, $supplier) {
        $query = "SELECT COUNT(*) FROM warehouse_item WHERE LOWER(item_name) = LOWER(:item_name) AND LOWER(supplier) = LOWER(:supplier)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':item_name', $itemName);
        $stmt->bindParam(':supplier', $supplier);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    private function appendToCSV($data) {
        $file = fopen("backup.csv", "a");
        if ($file === false) {
            throw new Exception("Unable to open backup.csv");
        }
        fputcsv($file, $data);
        fclose($file);
    }

    public function searchBySupplier($supplier) {
        $query = "SELECT * FROM warehouse_item WHERE supplier LIKE :supplier ORDER BY id DESC";
        $stmt = $this->db->prepare($query);
        $searchTerm = "%" . $supplier . "%";
        $stmt->bindParam(':supplier', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAllItems() {
        $query = "SELECT id, item_name, quantity, supplier, category, created_at FROM warehouse_item ORDER BY id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>