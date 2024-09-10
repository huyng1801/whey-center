<?php

class OrderModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllOrders() {
        $stmt = $this->pdo->query("SELECT * FROM `order`");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllOrdersLimit($limit, $offset) {
        $stmt = $this->pdo->prepare("SELECT * FROM `order` LIMIT ? OFFSET ?");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalOrders() {
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM `order`");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function getOrderById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM `order` WHERE order_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addOrder($data) {
        $stmt = $this->pdo->prepare("INSERT INTO `order` (customer_first_name, customer_last_name, company_name, country, address, address2, postal_code, city, phone, email, order_note, payment_method, shipping_price, total_price, is_paid, paid_at, is_delivered, delivered_at, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->execute([
            $data['customer_first_name'], $data['customer_last_name'], $data['company_name'], $data['country'], $data['address'], $data['address2'], $data['postal_code'], $data['city'], $data['phone'], $data['email'], $data['order_note'], $data['payment_method'], $data['shipping_price'], $data['total_price'], $data['is_paid'], $data['paid_at'], $data['is_delivered'], $data['delivered_at']
        ]);
    }

    public function updateOrder($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE `order` SET customer_first_name = ?, customer_last_name = ?, company_name = ?, country = ?, address = ?, address2 = ?, postal_code = ?, city = ?, phone = ?, email = ?, order_note = ?, payment_method = ?, shipping_price = ?, total_price = ?, is_paid = ?, paid_at = ?, is_delivered = ?, delivered_at = ?, updated_at = NOW() WHERE order_id = ?");
        $stmt->execute([
            $data['customer_first_name'], $data['customer_last_name'], $data['company_name'], $data['country'], $data['address'], $data['address2'], $data['postal_code'], $data['city'], $data['phone'], $data['email'], $data['order_note'], $data['payment_method'], $data['shipping_price'], $data['total_price'], $data['is_paid'], $data['paid_at'], $data['is_delivered'], $data['delivered_at'], $id
        ]);
    }


    public function deleteOrder($id) {
        $stmt = $this->pdo->prepare("DELETE FROM `order` WHERE order_id = ?");
        $stmt->execute([$id]);
    }
    public function getDailyRevenue($date) {
        $stmt = $this->pdo->prepare("SELECT SUM(total_price) as revenue FROM `order` WHERE DATE(created_at) = ?");
        $stmt->execute([$date]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['revenue'] ?? 0;
    }

    public function getMonthlyRevenue($year, $month) {
        $stmt = $this->pdo->prepare("SELECT SUM(total_price) as revenue FROM `order` WHERE YEAR(created_at) = ? AND MONTH(created_at) = ?");
        $stmt->execute([$year, $month]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['revenue'] ?? 0;
    }

    public function getYearlyRevenue($year) {
        $stmt = $this->pdo->prepare("SELECT SUM(total_price) as revenue FROM `order` WHERE YEAR(created_at) = ?");
        $stmt->execute([$year]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['revenue'] ?? 0;
    }

    public function getMonthlyRevenuesByYear($year) {
        $stmt = $this->pdo->prepare("
            SELECT MONTH(created_at) as month, SUM(total_price) as revenue 
            FROM `order` 
            WHERE YEAR(created_at) = ? 
            GROUP BY MONTH(created_at)
        ");
        $stmt->execute([$year]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateShippingStatus($id, $isDelivered) {
        $stmt = $this->pdo->prepare("
            UPDATE `order` 
            SET is_delivered = ?, 
                delivered_at = CASE WHEN ? THEN NOW() ELSE NULL END, 
                updated_at = NOW() 
            WHERE order_id = ?");
        $stmt->execute([$isDelivered, $isDelivered, $id]);
    }
    
    public function updatePaymentStatus($id, $isPaid) {
        $stmt = $this->pdo->prepare("
            UPDATE `order` 
            SET is_paid = ?, 
                paid_at = CASE WHEN ? THEN NOW() ELSE NULL END, 
                updated_at = NOW() 
            WHERE order_id = ?");
        $stmt->execute([$isPaid, $isPaid, $id]);
    }
    
    
}
?>
