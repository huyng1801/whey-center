<?php

class OrderItemModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllOrderItems() {
        $stmt = $this->pdo->query("SELECT * FROM order_item");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderItemById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM order_item WHERE order_item_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addOrderItem($data) {
        $stmt = $this->pdo->prepare("INSERT INTO order_item (quantity, unit_price, order_id, product_flavor_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data['quantity'], $data['unit_price'], $data['order_id'], $data['product_flavor_id']]);
    }

    public function updateOrderItem($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE order_item SET quantity = ?, unit_price = ?, order_id = ?, product_flavor_id = ? WHERE order_item_id = ?");
        $stmt->execute([$data['quantity'], $data['unit_price'], $data['order_id'], $data['product_flavor_id'], $id]);
    }

    public function deleteOrderItem($id) {
        $stmt = $this->pdo->prepare("DELETE FROM order_item WHERE order_item_id = ?");
        $stmt->execute([$id]);
    }
    public function getOrderItemsByOrderId($orderId) {
        $stmt = $this->pdo->prepare("
            SELECT oi.*, p.product_id, p.product_name, pf.flavor_name 
            FROM order_item oi 
            JOIN product_flavor pf ON oi.product_flavor_id = pf.product_flavor_id 
            JOIN product p ON pf.product_id = p.product_id 
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
}
