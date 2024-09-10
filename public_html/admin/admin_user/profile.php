<?php
require_once '../include/header.php';
require_once '../../config/config.php';
require_once '../../models/AdminUserModel.php';

$userModel = new AdminUserModel($pdo);

// Assume user ID is stored in session and user is logged in
$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    header('Location: ../login.php');
    exit;
}

// Fetch user data for the form
$user = $userModel->getUserById($userId);

// Handle form submission
$success = $_SESSION['success'] ?? null;
$errors = $_SESSION['errors'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['first_name'] ?? '';
    $lastName = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';

    // Validate inputs
    $errors = [];

    if (empty($firstName) || empty($lastName) || empty($email)) {
        $errors[] = 'Tất cả các trường đều yêu cầu.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Địa chỉ email không hợp lệ.';
    } elseif ($userModel->checkEmailExists($email, $userId)) {
        $errors[] = 'Email đã được sử dụng bởi một tài khoản khác.';
    } else {
        // Update user data excluding 'role' and 'is_active'
        $data = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email
        ];

        $userModel->updateProfile($userId, $data);
        $_SESSION['success'] = 'Thông tin đã được cập nhật thành công.';
    }
}
?>

<div>
    <div class="profile-box">
        <?php if (isset($success) && $success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($success); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php 
                foreach ($errors as $error) {
                    echo htmlspecialchars($error) . '<br>';
                }
                ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <form action="profile.php" method="post">
            <div class="form-group">
                <label for="first_name">Họ</label>
                <input type="text" id="first_name" name="first_name" class="form-control" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="last_name">Tên</label>
                <input type="text" id="last_name" name="last_name" class="form-control" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <!-- Role and is_active fields are not included in the form -->
            <button type="submit" class="btn btn-primary">Cập nhật</button>
        </form>
    </div>
</div>

<?php require_once '../include/footer.php'; ?>
