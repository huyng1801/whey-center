<?php
class AdminUserModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllUsers() {
        $stmt = $this->pdo->query("SELECT * FROM admin_user");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM admin_user WHERE user_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addUser($data) {
        $stmt = $this->pdo->prepare("INSERT INTO admin_user (first_name, last_name, email, hash_password, role, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->execute([$data['first_name'], $data['last_name'], $data['email'], $data['hash_password'], $data['role'], $data['is_active']]);
    }

    public function updateUser($id, $data) {
        $sql = "UPDATE admin_user SET first_name = ?, last_name = ?, email = ?, role = ?, is_active = ?, updated_at = NOW()";
        $params = [$data['first_name'], $data['last_name'], $data['email'], $data['role'], $data['is_active']];
    
        if (!empty($data['hash_password'])) {
            $sql .= ", hash_password = ?";
            $params[] = $data['hash_password'];
        }
    
        $sql .= " WHERE user_id = ?";
        $params[] = $id;
    
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    }
    
    public function updateIsActive($id, $isActive) {
        $stmt = $this->pdo->prepare("UPDATE admin_user SET is_active = ?, updated_at = NOW() WHERE user_id = ?");
        $stmt->execute([$isActive, $id]);
    }

    public function deleteUser($id) {
        $stmt = $this->pdo->prepare("DELETE FROM admin_user WHERE user_id = ?");
        $stmt->execute([$id]);
    }
    public function checkEmailExists($email, $excludeUserId = null) {
        if ($excludeUserId) {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM admin_user WHERE email = ? AND user_id != ?");
            $stmt->execute([$email, $excludeUserId]);
        } else {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM admin_user WHERE email = ?");
            $stmt->execute([$email]);
        }
        $count = $stmt->fetchColumn();
        return $count > 0;
    }
    public function authenticateUser($email, $password) {
        // Retrieve user by email
        $stmt = $this->pdo->prepare("SELECT * FROM admin_user WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // If user exists and password matches
        if ($user && password_verify($password, $user['hash_password'])) {
            return $user; // Authentication successful
        } else {
            return false; // Authentication failed
        }
    }
    public function changePassword($userId, $newPassword) {
        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        // Prepare the SQL statement
        $stmt = $this->pdo->prepare("UPDATE admin_user SET hash_password = ?, updated_at = NOW() WHERE user_id = ?");
        
        // Execute the statement with the hashed password and user ID
        $stmt->execute([$hashedPassword, $userId]);
    }
    public function updateProfile($id, $data) {
        $sql = "UPDATE admin_user SET first_name = ?, last_name = ?, email = ?, updated_at = NOW()";
        $params = [$data['first_name'], $data['last_name'], $data['email']];
    
        if (isset($data['hash_password']) && !empty($data['hash_password'])) {
            $sql .= ", hash_password = ?";
            $params[] = $data['hash_password'];
        }
    
        if (isset($data['role'])) {
            $sql .= ", role = ?";
            $params[] = $data['role'];
        }
    
        if (isset($data['is_active'])) {
            $sql .= ", is_active = ?";
            $params[] = $data['is_active'];
        }
    
        $sql .= " WHERE user_id = ?";
        $params[] = $id;
    
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    }
    
}
