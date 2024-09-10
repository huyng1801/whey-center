<?php

class BannerModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllBanners() {
        $stmt = $this->pdo->query("SELECT * FROM banner");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBannerById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM banner WHERE banner_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addBanner($data) {
        $stmt = $this->pdo->prepare("INSERT INTO banner (title, image_url, link, is_visible, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
        $stmt->execute([$data['title'], $data['image_url'], $data['link'], $data['is_visible']]);
    }

    public function updateBanner($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE banner SET title = ?, image_url = ?, link = ?, is_visible = ?, updated_at = NOW() WHERE banner_id = ?");
        $stmt->execute([$data['title'], $data['image_url'], $data['link'], $data['is_visible'], $id]);
    }

    public function deleteBanner($id) {
        $stmt = $this->pdo->prepare("DELETE FROM banner WHERE banner_id = ?");
        $stmt->execute([$id]);
    }
}
?>
