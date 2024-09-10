<?php

class CategoryModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllCategories() {
        $stmt = $this->pdo->query("SELECT * FROM category");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoriesNotInProduct($productId) {
        $stmt = $this->pdo->prepare("
            SELECT c.* 
            FROM category c
            LEFT JOIN product_category pc ON c.category_id = pc.category_id AND pc.product_id = ?
            WHERE pc.category_id IS NULL
        ");
        $stmt->execute([$productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getCategoryById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM category WHERE category_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addCategory($data) {
        $stmt = $this->pdo->prepare("INSERT INTO category (category_name, created_at, updated_at) VALUES (?, NOW(), NOW())");
        $stmt->execute([$data['category_name']]);
    }

    public function updateCategory($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE category SET category_name = ?, updated_at = NOW() WHERE category_id = ?");
        $stmt->execute([$data['category_name'], $id]);
    }

    public function deleteCategory($id) {
        $stmt = $this->pdo->prepare("DELETE FROM category WHERE category_id = ?");
        $stmt->execute([$id]);
    }

    public function isCategoryNameExists($categoryName) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM category WHERE category_name = ?");
        $stmt->execute([$categoryName]);
        return $stmt->fetchColumn() > 0;
    }
    
    public function isCategoryNameExistsExcept($categoryName, $categoryId) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM category WHERE category_name = ? AND category_id != ?");
        $stmt->execute([$categoryName, $categoryId]);
        return $stmt->fetchColumn() > 0;
    }

    public function getCategoryWithProducts($categoryId, $sortBy = 'name_asc') {
        $orderBy = '';
    
        switch ($sortBy) {
            case 'name_asc':
                $orderBy = 'p.product_name ASC';
                break;
            case 'name_desc':
                $orderBy = 'p.product_name DESC';
                break;
            case 'price_asc':
                $orderBy = 'p.unit_price ASC';
                break;
            case 'price_desc':
                $orderBy = 'p.unit_price DESC';
                break;
            case 'newest':
                $orderBy = 'p.created_at DESC';
                break;
            default:
                $orderBy = 'p.product_name ASC';
        }
    
        $stmt = $this->pdo->prepare("
            SELECT 
                c.category_id, 
                c.category_name, 
                p.product_id, 
                p.product_name, 
                p.description,
                p.original_price,  
                p.unit_price, 
                p.is_visible, 
                COALESCE(pi_main.image_url, pi_first.image_url, 'placeholder.png') AS product_image
            FROM 
                category c
            LEFT JOIN 
                product_category pc ON c.category_id = pc.category_id
            LEFT JOIN 
                product p ON pc.product_id = p.product_id
            LEFT JOIN 
                product_image pi_main ON p.product_id = pi_main.product_id AND pi_main.is_main = TRUE
            LEFT JOIN 
                product_image pi_first ON p.product_id = pi_first.product_id
                AND pi_first.product_image_id = (
                    SELECT MIN(product_image_id) FROM product_image WHERE product_id = p.product_id
                )
            WHERE 
                c.category_id = ?
            ORDER BY 
                $orderBy
        ");
        $stmt->execute([$categoryId]);
    
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        if (empty($results)) {
            return null;
        }
    
        $category = [
            'category_id' => $results[0]['category_id'],
            'category_name' => $results[0]['category_name'],
            'products' => []
        ];
    
        foreach ($results as $row) {
            if ($row['product_id']) {
                $category['products'][] = [
                    'product_id' => $row['product_id'],
                    'product_name' => $row['product_name'],
                    'description' => $row['description'],
                    'unit_price' => $row['unit_price'],
                    'original_price' => $row['original_price'],
                    'is_visible' => $row['is_visible'],
                    'product_image' => $row['product_image']
                ];
            }
        }
    
        return $category;
    }    
}
