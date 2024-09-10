<?php

class OriginModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllOrigins() {
        $stmt = $this->pdo->query("SELECT * FROM origin");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOriginById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM origin WHERE origin_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addOrigin($data) {
        $stmt = $this->pdo->prepare("INSERT INTO origin (country, created_at, updated_at) VALUES (?, NOW(), NOW())");
        $stmt->execute([$data['country']]);
    }

    public function updateOrigin($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE origin SET country = ?, updated_at = NOW() WHERE origin_id = ?");
        $stmt->execute([$data['country'], $id]);
    }

    public function deleteOrigin($id) {
        $stmt = $this->pdo->prepare("DELETE FROM origin WHERE origin_id = ?");
        $stmt->execute([$id]);
    }

    public function isOriginNameExists($country) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM origin WHERE country = ?");
        $stmt->execute([$country]);
        return $stmt->fetchColumn() > 0;
    }

    public function isOriginNameExistsExcept($country, $originId) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM origin WHERE country = ? AND origin_id != ?");
        $stmt->execute([$country, $originId]);
        return $stmt->fetchColumn() > 0;
    }
}
?>
