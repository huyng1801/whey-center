<?php
class ProductModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllProducts() {
        $stmt = $this->pdo->prepare("
                SELECT 
                    p.product_id, 
                    p.product_name, 
                    p.original_price, 
                    p.unit_price, 
                    p.is_visible, 
                    p.created_at, 
                    p.updated_at,
                    o.country AS origin_name,
                    m.manufacturer_name
                FROM 
                    product p
                LEFT JOIN 
                    origin o ON p.origin_id = o.origin_id
                LEFT JOIN 
                    manufacturer m ON p.manufacturer_id = m.manufacturer_id
            ");
            $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductsLimit($limit, $offset) {
        $limit = (int)$limit;
        $offset = (int)$offset;
    
        $stmt = $this->pdo->prepare("
            SELECT 
                p.*, 
                o.country AS origin_name, 
                m.manufacturer_name 
            FROM 
                product p
            JOIN 
                origin o ON p.origin_id = o.origin_id
            JOIN 
                manufacturer m ON p.manufacturer_id = m.manufacturer_id
            ORDER BY
                p.created_at, p.updated_at
            LIMIT 
                :limit 
            OFFSET 
                :offset
        ");
    
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function getTotalProducts() {
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM product");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function getProductById($id) {
        $stmt = $this->pdo->prepare("
            SELECT 
                p.product_id, 
                p.product_name, 
                p.description, 
                p.original_price, 
                p.unit_price, 
                p.is_visible, 
                p.created_at, 
                p.updated_at, 
                o.country AS origin_country, 
                m.manufacturer_name
            FROM 
                product p
            JOIN 
                origin o ON p.origin_id = o.origin_id
            JOIN 
                manufacturer m ON p.manufacturer_id = m.manufacturer_id
            WHERE 
                p.product_id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }    

    public function getProductsByCategoryId($categoryId) {
        $stmt = $this->pdo->prepare("
            SELECT p.* FROM product p
            JOIN product_category pc ON p.product_id = pc.product_id
            WHERE pc.category_id = ?
        ");
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addProduct($data) {
        $stmt = $this->pdo->prepare("INSERT INTO product (product_name, description, original_price, unit_price, is_visible, created_at, updated_at, origin_id, manufacturer_id) VALUES (?, ?, ?, ?, ?, NOW(), NOW(), ?, ?)");
        $stmt->execute([$data['product_name'], $data['description'], $data['original_price'], $data['unit_price'], $data['is_visible'], $data['origin_id'], $data['manufacturer_id']]);
    }

    public function updateProduct($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE product SET product_name = ?, description = ?, original_price = ?, unit_price = ?, is_visible = ?, updated_at = NOW(), origin_id = ?, manufacturer_id = ? WHERE product_id = ?");
        $stmt->execute([$data['product_name'], $data['description'], $data['original_price'], $data['unit_price'], $data['is_visible'], $data['origin_id'], $data['manufacturer_id'], $id]);
    }

    public function deleteProduct($id) {
        $stmt = $this->pdo->prepare("DELETE FROM product WHERE product_id = ?");
        $stmt->execute([$id]);
    }

    public function searchProductsByName($name, $limit, $offset) {
        $limit = (int)$limit;
        $offset = (int)$offset;
        $stmt = $this->pdo->prepare("SELECT * FROM product WHERE product_name LIKE ? LIMIT $limit OFFSET $offset");
        $stmt->execute(["%$name%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countProductsByName($name) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM product WHERE product_name LIKE ?");
        $stmt->execute(["%$name%"]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    public function getCategoriesWithProducts() {
        $stmt = $this->pdo->query("
            SELECT 
                c.category_id, 
                c.category_name, 
                p.product_id, 
                p.product_name, 
                p.description, 
                p.unit_price, 
                p.original_price, 
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
                p.product_id IS NOT NULL
            ORDER BY 
                c.category_id, p.product_id
        ");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        $categories = [];
        foreach ($results as $row) {
            $categoryId = $row['category_id'];
            if (!isset($categories[$categoryId])) {
                $categories[$categoryId] = [
                    'category_id' => $row['category_id'],
                    'category_name' => $row['category_name'],
                    'products' => []
                ];
            }
            if ($row['product_id']) {
                $categories[$categoryId]['products'][] = [
                    'product_id' => $row['product_id'],
                    'product_name' => $row['product_name'],
                    'original_price' => $row['original_price'],
                    'description' => $row['description'],
                    'unit_price' => $row['unit_price'],
                    'is_visible' => $row['is_visible'],
                    'product_image' => $row['product_image']
                ];
            }
        }
    
        // Remove categories with no products
        foreach ($categories as $categoryId => $category) {
            if (empty($category['products'])) {
                unset($categories[$categoryId]);
            }
        }
    
        return $categories;
    }
    
    
    
    
    public function searchProductsFullInfo($searchQuery) {
        $stmt = $this->pdo->prepare("
            SELECT 
                p.product_id, 
                p.product_name, 
                p.description, 
                p.unit_price, 
                p.is_visible, 
                p.original_price,
                COALESCE(main_image.image_url, first_image.image_url) AS product_image, 
                c.category_name,
                o.country,
                m.manufacturer_name
            FROM 
                product p
            LEFT JOIN 
                (SELECT 
                    pi.product_id, 
                    pi.image_url
                FROM 
                    product_image pi
                INNER JOIN 
                    (SELECT 
                        product_id, 
                        MIN(product_image_id) AS min_image_id
                    FROM 
                        product_image
                    WHERE 
                        is_main = 0
                    GROUP BY 
                        product_id
                    ) AS first_image_subquery
                ON 
                    pi.product_id = first_image_subquery.product_id 
                    AND pi.product_image_id = first_image_subquery.min_image_id
                ) AS first_image
            ON 
                p.product_id = first_image.product_id
            LEFT JOIN 
                (SELECT 
                    pi.product_id, 
                    pi.image_url
                FROM 
                    product_image pi
                WHERE 
                    pi.is_main = 1
                ) AS main_image
            ON 
                p.product_id = main_image.product_id
            LEFT JOIN 
                product_category pc ON p.product_id = pc.product_id
            LEFT JOIN 
                category c ON pc.category_id = c.category_id
            LEFT JOIN 
                origin o ON p.origin_id = o.origin_id
            LEFT JOIN 
                manufacturer m ON p.manufacturer_id = m.manufacturer_id
            WHERE 
                LOWER(p.product_name) LIKE LOWER(?) OR LOWER(p.description) LIKE LOWER(?)
            GROUP BY 
                p.product_id
        ");
        $stmt->execute(['%' . strtolower($searchQuery) . '%', '%' . strtolower($searchQuery) . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    
    

    public function getRelatedProducts($productId) {
        // Fetch the category ID of the given product
        $stmt = $this->pdo->prepare("
            SELECT pc.category_id
            FROM product_category pc
            WHERE pc.product_id = ?
        ");
        $stmt->execute([$productId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $categoryId = $result['category_id'];
    
        // Fetch related products based on the category ID
        $stmt = $this->pdo->prepare("
            SELECT 
                p.product_id, 
                p.product_name, 
                p.description, 
                p.unit_price, 
                p.original_price,
                p.is_visible, 
                COALESCE(pi_main.image_url, pi_first.image_url, 'placeholder.png') AS product_image
            FROM 
                product p
            LEFT JOIN 
                product_image pi_main ON p.product_id = pi_main.product_id AND pi_main.is_main = TRUE
            LEFT JOIN 
                product_image pi_first ON p.product_id = pi_first.product_id
                AND pi_first.product_image_id = (
                    SELECT MIN(product_image_id) FROM product_image WHERE product_id = p.product_id
                )
            LEFT JOIN 
                product_category pc ON p.product_id = pc.product_id
            WHERE 
                pc.category_id = ? AND p.product_id != ?
            GROUP BY 
                p.product_id
            ORDER BY 
                RAND()
            LIMIT 4
        ");
        $stmt->execute([$categoryId, $productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getTopSellingProducts($limit = 10) {
        $limit = (int)$limit;  // Ensure limit is an integer
    
        $stmt = $this->pdo->prepare("
            SELECT 
                p.product_id, 
                p.product_name, 
                p.unit_price, 
                SUM(oi.quantity) as total_quantity,
                pi.image_url AS product_image
            FROM 
                order_item oi
            JOIN 
                product_flavor pf ON oi.product_flavor_id = pf.product_flavor_id
            JOIN 
                product p ON pf.product_id = p.product_id
            LEFT JOIN 
                (
                    SELECT product_id, MIN(product_image_id) as min_image_id
                    FROM product_image
                    GROUP BY product_id
                ) min_img ON p.product_id = min_img.product_id
            LEFT JOIN 
                product_image pi ON min_img.min_image_id = pi.product_image_id
            GROUP BY 
                p.product_id
            ORDER BY 
                total_quantity DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}
