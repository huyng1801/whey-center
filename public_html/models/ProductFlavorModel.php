<?php

class ProductFlavorModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllFlavors() {
        $stmt = $this->pdo->query("SELECT * FROM product_flavor");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFlavorById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM product_flavor WHERE product_flavor_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getFlavorsByProductId($productId) {
        $stmt = $this->pdo->prepare("SELECT * FROM product_flavor WHERE product_id = ?");
        $stmt->execute([$productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addFlavor($data) {
        $stmt = $this->pdo->prepare("INSERT INTO product_flavor (flavor_name, stock_quantity, product_id) VALUES (?, ?, ?)");
        $stmt->execute([$data['flavor_name'], $data['stock_quantity'], $data['product_id']]);
    }

    public function updateFlavor($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE product_flavor SET flavor_name = ?, stock_quantity = ?, product_id = ? WHERE product_flavor_id = ?");
        $stmt->execute([$data['flavor_name'], $data['stock_quantity'], $data['product_id'], $id]);
    }

    public function updateFlavorQuantity($id, $quantity) {
        $stmt = $this->pdo->prepare("UPDATE product_flavor SET stock_quantity = ? WHERE product_flavor_id = ?");
        $stmt->execute([$quantity, $id]);
    }
    public function deleteFlavor($id) {
        $stmt = $this->pdo->prepare("DELETE FROM product_flavor WHERE product_flavor_id = ?");
        $stmt->execute([$id]);
    }
}
