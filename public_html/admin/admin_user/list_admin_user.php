<?php

require_once '../../config/config.php';
require_once '../../models/AdminUserModel.php';

$adminUserModel = new AdminUserModel($pdo);

$errors = [];
$success = $_SESSION['success'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $userId = $_POST['user_id'] ?? null;
    $lastName = $_POST['last_name'];
    $firstName = $_POST['first_name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    $hashPassword = $_POST['hash_password'];

    // Check if email already exists
    if ($adminUserModel->checkEmailExists($email, $userId)) {
        $errors['email'] = 'Email đã tồn tại';
    }

    if (empty($errors)) {
        $data = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'role' => $role,
            'is_active' => $isActive
        ];

        if ($action == 'create') {
            $data['hash_password'] = password_hash($hashPassword, PASSWORD_BCRYPT);
            $adminUserModel->addUser($data);
            $_SESSION['success'] = 'Thêm người dùng thành công';
        } elseif ($action == 'edit' && $userId) {
            if (!empty($hashPassword)) {
                $data['hash_password'] = password_hash($hashPassword, PASSWORD_BCRYPT);
            }
            $adminUserModel->updateUser($userId, $data);
            $_SESSION['success'] = 'Cập nhật người dùng thành công';
        }

        header('Location: list_admin_user.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['delete'])) {
    $userId = $_GET['delete'];
    $adminUserModel->deleteUser($userId);
    $_SESSION['success'] = 'Xóa người dùng thành công';
    header('Location: list_admin_user.php');
    exit;
}

$users = $adminUserModel->getAllUsers();
?>


<?php require_once '../include/header.php'; ?>

<div>
    <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#adminUserModal" data-action="create"><i class="fas fa-plus"></i> Thêm mới</button>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Họ</th>
                <th>Tên</th>
                <th>Email</th>
                <th class="text-center">Vai trò</th>
                <th class="text-center">Hoạt động</th>
                <th class="text-center">Ngày tạo</th>
                <th class="text-center">Ngày cập nhật</th>
                <th class="text-center">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['last_name']); ?></td>
                <td><?php echo htmlspecialchars($user['first_name']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td class="text-center"><?php echo $user['role'] === 0 ? 'Quản trị viên' : 'Nhân viên'; ?></td>
                <td class="text-center">
                    <?php
                    if ($user['is_active']) {
                        echo '<span class="badge badge-success">Hoạt động</span>'; 
                    } else {
                        echo '<span class="badge badge-secondary">Vô hiệu hóa</span>'; 
                    }
                    ?>
                </td>
                <td class="text-center"><?php echo (new DateTime($user['created_at']))->format('d/m/Y H:i:s'); ?></td>
                <td class="text-center"><?php echo (new DateTime($user['updated_at']))->format('d/m/Y H:i:s'); ?></td>
                <td class="text-center">
                    <button type="button" class="btn-icon text-info" data-toggle="modal" data-target="#adminUserModal" data-action="edit" data-id="<?php echo $user['user_id']; ?>" data-first_name="<?php echo $user['first_name']; ?>" data-last_name="<?php echo $user['last_name']; ?>" data-email="<?php echo $user['email']; ?>" data-role="<?php echo $user['role']; ?>" data-is_active="<?php echo $user['is_active']; ?>"><i class="fas fa-edit"></i></button> |
                    <button type="button" class="btn-icon text-danger" data-toggle="modal" data-target="#deleteModal" data-id="<?php echo $user['user_id']; ?>" data-first_name="<?php echo $user['first_name']; ?>" data-last_name="<?php echo $user['last_name']; ?>"><i class="fas fa-trash-alt"></i></button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="adminUserModal" tabindex="-1" aria-labelledby="adminUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="adminUserForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="adminUserModalLabel">Thêm người dùng</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" id="adminUserAction" value="create">
                    <input type="hidden" name="user_id" id="adminUserId">
                    
                    <div class="form-group">
                        <label for="last_name">Họ</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" required 
                               minlength="2" maxlength="50"
                               oninput="this.setCustomValidity('')" 
                               oninvalid="this.setCustomValidity('Họ phải có từ 2 đến 50 ký tự')">
                    </div>
                    
                    <div class="form-group">
                        <label for="first_name">Tên</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" required 
                               minlength="2" maxlength="50"
                               oninput="this.setCustomValidity('')" 
                               oninvalid="this.setCustomValidity('Tên phải có từ 2 đến 50 ký tự')">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required 
                               maxlength="100"
                               oninput="this.setCustomValidity('')" 
                               oninvalid="this.setCustomValidity('Email không hợp lệ hoặc vượt quá 100 ký tự')">
                    </div>   
                    <div class="form-group">
                        <label for="role">Vai trò</label>
                        <select class="form-control" id="role" name="role" required 
                                oninput="this.setCustomValidity('')" 
                                oninvalid="this.setCustomValidity('Vui lòng chọn vai trò')">
                            <option value="1">Nhân viên</option>
                            <option value="0">Quản trị viên</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="hash_password">Mật khẩu</label>
                        <input type="password" class="form-control" id="hash_password" name="hash_password"
                               minlength="8" maxlength="16"
                               oninput="this.setCustomValidity('')" 
                               oninvalid="this.setCustomValidity('Mật khẩu phải có từ 8 đến 16 ký tự')">
                    </div>
                    
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active">
                            <label class="custom-control-label" for="is_active">Hoạt động</label>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Xóa người dùng</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa người dùng <strong id="deleteUserName"></strong> này không?
            </div>
            <div class="modal-footer">
                <form method="GET" id="deleteForm">
                    <input type="hidden" name="delete" id="deleteUserId">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../include/footer.php'; ?>

<script>
$('#adminUserModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var action = button.data('action');
    var modal = $(this);
    var form = modal.find('#adminUserForm');
    form[0].reset();

    var passwordField = modal.find('#hash_password');

    if (action === 'edit') {
        var userId = button.data('id');
        var firstName = button.data('first_name');
        var lastName = button.data('last_name');
        var email = button.data('email');
        var role = button.data('role');
        var isActive = button.data('is_active');

        modal.find('.modal-title').text('Sửa người dùng');
        modal.find('#adminUserAction').val('edit');
        modal.find('#adminUserId').val(userId);
        modal.find('#first_name').val(firstName);
        modal.find('#last_name').val(lastName);
        modal.find('#email').val(email);
        modal.find('#role').val(role);
        modal.find('#is_active').prop('checked', isActive);

        passwordField.removeAttr('required');
    } else {
        modal.find('.modal-title').text('Thêm người dùng');
        modal.find('#adminUserAction').val('create');
        passwordField.attr('required', 'required');
    }
});

$('#deleteModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var userId = button.data('id');
    var firstName = button.data('first_name');
    var lastName = button.data('last_name');
    var modal = $(this);
    modal.find('#deleteUserId').val(userId);
    modal.find('#deleteUserName').text(lastName + ' ' + firstName);
});
</script>
