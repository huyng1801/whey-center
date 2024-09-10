<?php
 require_once '../include/header.php'; ?>
<?php

require_once '../../config/config.php';
require_once '../../models/AdminUserModel.php';

$userModel = new AdminUserModel($pdo);

// Assume user ID is stored in session and user is logged in

$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    header('Location: ../login.php');
    exit;
}
$successMessage = $_SESSION['success'] ?? null;
$errors = $_SESSION['errors'] ?? [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    // Validate and update the password
    $errors = [];

    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $errors[] = 'Tất cả các trường đều yêu cầu.';
    } elseif ($newPassword !== $confirmPassword) {
        $errors[] = 'Mật khẩu mới và xác nhận mật khẩu không khớp.';
    } else {
        $user = $userModel->getUserById($userId);

        if (!password_verify($currentPassword, $user['hash_password'])) {
            $errors[] = 'Mật khẩu hiện tại không chính xác.';
        } else {
            $userModel->changePassword($userId, $newPassword);
            $success = 'Mật khẩu đã được cập nhật thành công.';
        }
    }
}
?>

<div>
    <div class="change-password-box">
    <?php if (isset($success) && $success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $success; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php unset($_SESSION['success']); // Clear the success message after displaying ?>
            <?php endif; ?>

                <!-- Display Errors -->
            <?php if (isset($errors) && !empty($errors)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php 
                    $errorMessages = '';
                    foreach ($errors as $index => $error) {
                        if ($index < count($errors) - 1) {
                            $errorMessages .= htmlspecialchars($error);            
                        }
                        else {
                            $errorMessages .= htmlspecialchars($error);
                            $errorMessages .= "<br/>";
                        }
                    }
                    echo $errorMessages;
                    ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
        <form action="change_password.php" method="post">
            <div class="form-group">
                <label for="current_password">Mật khẩu hiện tại</label>
                <input type="password" id="current_password" name="current_password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="new_password">Mật khẩu mới</label>
                <input type="password" id="new_password" name="new_password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Xác nhận mật khẩu mới</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Thay đổi mật khẩu</button>
        </form>
    </div>
</div>

<?php require_once '../include/footer.php'; ?>
