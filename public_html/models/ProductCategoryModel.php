<?php

class ProductCategoryModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllProductCategories() {
        $stmt = $this->pdo->query("SELECT * FROM product_category");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductCategoryById($productId, $categoryId) {
        $stmt = $this->pdo->prepare("SELECT * FROM product_category WHERE product_id = ? AND category_id = ?");
        $stmt->execute([$productId, $categoryId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getProductCategoriesByProductId($productId) {
        $stmt = $this->pdo->prepare("
            SELECT pc.*, c.category_name 
            FROM product_category pc
            JOIN category c ON pc.category_id = c.category_id
            WHERE pc.product_id = ?
        ");
        $stmt->execute([$productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addProductCategory($data) {
        $stmt = $this->pdo->prepare("INSERT INTO product_category (product_id, category_id) VALUES (?, ?)");
        $stmt->execute([$data['product_id'], $data['category_id']]);
    }

    public function deleteProductCategory($productId, $categoryId) {
        $stmt = $this->pdo->prepare("DELETE FROM product_category WHERE product_id = ? AND category_id = ?");
        $stmt->execute([$productId, $categoryId]);
    }
}
