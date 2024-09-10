<?php

class ProductImageModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllImages() {
        $stmt = $this->pdo->query("SELECT * FROM product_image");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getImageById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM product_image WHERE product_image_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getImagesByProductId($productId) {
        $stmt = $this->pdo->prepare("
            SELECT * 
            FROM product_image 
            WHERE product_id = ?
            ORDER BY is_main DESC, product_image_id ASC
        ");
        $stmt->execute([$productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function addImage($data) {
        $stmt = $this->pdo->prepare("INSERT INTO product_image (image_url, product_id) VALUES (?, ?)");
        $stmt->execute([$data['image_url'], $data['product_id']]);
    }

    public function updateImage($productId, $imageId) {
        $this->pdo->beginTransaction();
    
        try {
            $stmt = $this->pdo->prepare("UPDATE product_image SET is_main = 0 WHERE product_id = ?");
            $stmt->execute([$productId]);
    
            $stmt = $this->pdo->prepare("UPDATE product_image SET is_main = 1 WHERE product_image_id = ?");
            $stmt->execute([$imageId]);
    
            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
    
    public function getMainImageId($productId) {
        $stmt = $this->pdo->prepare("SELECT product_image_id FROM product_image WHERE product_id = ? AND is_main = 1");
        $stmt->execute([$productId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['product_image_id'] : null;
    }
    

    public function deleteImage($id) {
        $stmt = $this->pdo->prepare("DELETE FROM product_image WHERE product_image_id = ?");
        $stmt->execute([$id]);
    }
}
