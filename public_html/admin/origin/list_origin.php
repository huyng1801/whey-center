<?php


require_once '../../config/config.php';
require_once '../../models/OriginModel.php';

$originModel = new OriginModel($pdo);

$errors = [];
$success = $_SESSION['success'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $originId = $_POST['origin_id'] ?? null;
    $country = $_POST['country'];

    // Validate input
    if (empty($country)) {
        $errors['country'] = 'Tên quốc gia là bắt buộc';
    } else {
        // Check if country name already exists
        if ($action == 'create' && $originModel->isOriginNameExists($country)) {
            $errors['country'] = 'Tên quốc gia đã tồn tại';
        } elseif ($action == 'edit' && $originModel->isOriginNameExistsExcept($country, $originId)) {
            $errors['country'] = 'Tên quốc gia đã tồn tại';
        }
    }

    if (empty($errors)) {
        $data = [
            'country' => $country
        ];

        if ($action == 'create') {
            $originModel->addOrigin($data);
            $_SESSION['success'] = 'Thêm quốc gia thành công';
        } elseif ($action == 'edit' && $originId) {
            $originModel->updateOrigin($originId, $data);
            $_SESSION['success'] = 'Cập nhật quốc gia thành công';
        }
        header('Location: list_origin.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['delete'])) {
    $originId = $_GET['delete'];
    $originModel->deleteOrigin($originId);
    $_SESSION['success'] = 'Xóa quốc gia thành công';
    header('Location: list_origin.php');
    exit;
}

$origins = $originModel->getAllOrigins();
?>

<?php require_once '../include/header.php'; ?>

<div>
    <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#originModal" data-action="create"><i class="fas fa-plus"></i> Thêm quốc gia</button>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Tên quốc gia</th>
                <th class="text-center">Ngày tạo</th>
                <th class="text-center">Ngày cập nhật</th>
                <th class="text-center">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($origins as $origin): ?>
            <tr>
                <td><?php echo htmlspecialchars($origin['country']); ?></td>
                <td class="text-center"><?php echo (new DateTime($origin['created_at']))->format('d/m/Y H:i:s'); ?></td>
                <td class="text-center"><?php echo (new DateTime($origin['updated_at']))->format('d/m/Y H:i:s'); ?></td>
                <td class="text-center">
                    <button type="button" class="btn-icon text-info" data-toggle="modal" data-target="#originModal" data-action="edit" data-id="<?php echo $origin['origin_id']; ?>" data-country="<?php echo $origin['country']; ?>"><i class="fas fa-edit"></i></button> |
                    <button type="button" class="btn-icon text-danger" data-toggle="modal" data-target="#deleteModal" data-id="<?php echo $origin['origin_id']; ?>" data-country="<?php echo $origin['country']; ?>"><i class="fas fa-trash-alt"></i></button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="originModal" tabindex="-1" aria-labelledby="originModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="originForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="originModalLabel">Thêm quốc gia</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" id="originAction" value="create">
                    <input type="hidden" name="origin_id" id="originId">
                    <div class="form-group">
                        <label for="country">Tên quốc gia</label>
                        <input type="text" class="form-control" id="country" name="country" required 
                               minlength="2" maxlength="50"
                               oninput="this.setCustomValidity('')" 
                               oninvalid="this.setCustomValidity('Tên quốc gia phải có từ 2 đến 50 ký tự')">
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
                <h5 class="modal-title" id="deleteModalLabel">Xóa quốc gia</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa quốc gia <strong id="deleteCountryName"></strong> này không?
            </div>
            <div class="modal-footer">
                <form method="GET" id="deleteForm">
                    <input type="hidden" name="delete" id="deleteOriginId">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../include/footer.php'; ?>

<script>
$('#originModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var action = button.data('action');
    var modal = $(this);
    var form = modal.find('#originForm');
    form[0].reset();

    if (action === 'edit') {
        var originId = button.data('id');
        var country = button.data('country');

        modal.find('.modal-title').text('Sửa quốc gia');
        modal.find('#originAction').val('edit');
        modal.find('#originId').val(originId);
        modal.find('#country').val(country);
    } else {
        modal.find('.modal-title').text('Thêm quốc gia');
        modal.find('#originAction').val('create');
    }
});

$('#deleteModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var originId = button.data('id');
    var country = button.data('country');
    var modal = $(this);
    modal.find('#deleteOriginId').val(originId);
    modal.find('#deleteCountryName').text(country);
});
</script>
