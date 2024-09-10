<?php

class ManufacturerModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllManufacturers() {
        $stmt = $this->pdo->query("SELECT * FROM manufacturer");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getManufacturerById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM manufacturer WHERE manufacturer_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addManufacturer($data) {
        $stmt = $this->pdo->prepare("INSERT INTO manufacturer (manufacturer_name, description, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
        $stmt->execute([$data['manufacturer_name'], $data['description']]);
    }

    public function updateManufacturer($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE manufacturer SET manufacturer_name = ?, description = ?, updated_at = NOW() WHERE manufacturer_id = ?");
        $stmt->execute([$data['manufacturer_name'], $data['description'], $id]);
    }

    public function deleteManufacturer($id) {
        $stmt = $this->pdo->prepare("DELETE FROM manufacturer WHERE manufacturer_id = ?");
        $stmt->execute([$id]);
    }

    public function isManufacturerNameExists($manufacturerName) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM manufacturer WHERE manufacturer_name = ?");
        $stmt->execute([$manufacturerName]);
        return $stmt->fetchColumn() > 0;
    }
    
    public function isManufacturerNameExistsExcept($manufacturerName, $manufacturerId) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM manufacturer WHERE manufacturer_name = ? AND manufacturer_id != ?");
        $stmt->execute([$manufacturerName, $manufacturerId]);
        return $stmt->fetchColumn() > 0;
    }
}
?>
