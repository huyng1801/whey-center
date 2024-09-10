<?php

require_once '../../config/config.php';
require_once '../../models/ManufacturerModel.php';

$manufacturerModel = new ManufacturerModel($pdo);

$errors = [];
$success = $_SESSION['success'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $manufacturerId = $_POST['manufacturer_id'] ?? null;
    $manufacturerName = $_POST['manufacturer_name'];
    $description = $_POST['description'];

    // Validate input
    if (empty($manufacturerName)) {
        $errors['manufacturer_name'] = 'Tên nhà sản xuất là bắt buộc';
    } else {
        // Check if manufacturer name already exists
        if ($action == 'create' && $manufacturerModel->isManufacturerNameExists($manufacturerName)) {
            $errors['manufacturer_name'] = 'Tên nhà sản xuất đã tồn tại';
        } elseif ($action == 'edit' && $manufacturerModel->isManufacturerNameExistsExcept($manufacturerName, $manufacturerId)) {
            $errors['manufacturer_name'] = 'Tên nhà sản xuất đã tồn tại';
        }
    }

    if (empty($errors)) {
        $data = [
            'manufacturer_name' => $manufacturerName,
            'description' => $description
        ];

        if ($action == 'create') {
            $manufacturerModel->addManufacturer($data);
            $_SESSION['success'] = 'Thêm nhà sản xuất thành công';
        } elseif ($action == 'edit' && $manufacturerId) {
            $manufacturerModel->updateManufacturer($manufacturerId, $data);
            $_SESSION['success'] = 'Cập nhật nhà sản xuất thành công';
        }
        header('Location: list_manufacturer.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['delete'])) {
    $manufacturerId = $_GET['delete'];
    $manufacturerModel->deleteManufacturer($manufacturerId);
    $_SESSION['success'] = 'Xóa nhà sản xuất thành công';
    header('Location: list_manufacturer.php');
    exit;
}

$manufacturers = $manufacturerModel->getAllManufacturers();

function truncateText($text, $maxLength = 50) {
    if (strlen($text) > $maxLength) {
        $text = substr($text, 0, $maxLength) . '...';
    }
    return $text;
}
?>

<?php require_once '../include/header.php'; ?>

<div>

    <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#manufacturerModal" data-action="create"><i class="fas fa-plus"></i> Thêm mới</button>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Tên nhà sản xuất</th>
                <th>Mô tả</th>
                <th class="text-center">Ngày tạo</th>
                <th class="text-center">Ngày cập nhật</th>
                <th class="text-center">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($manufacturers as $manufacturer): ?>
            <tr>
                <td><?php echo htmlspecialchars($manufacturer['manufacturer_name']); ?></td>
                <td><?php echo htmlspecialchars(truncateText($manufacturer['description'])); ?></td>
                <td class="text-center"><?php echo (new DateTime($manufacturer['created_at']))->format('d/m/Y H:i:s'); ?></td>
                <td class="text-center"><?php echo (new DateTime($manufacturer['updated_at']))->format('d/m/Y H:i:s'); ?></td>
                <td class="text-center">
                    <button type="button" class="btn-icon text-info" data-toggle="modal" data-target="#manufacturerModal" data-action="edit" data-id="<?php echo $manufacturer['manufacturer_id']; ?>" data-name="<?php echo $manufacturer['manufacturer_name']; ?>" data-description="<?php echo $manufacturer['description']; ?>"><i class="fas fa-edit"></i></button> |
                    <button type="button" class="btn-icon text-danger" data-toggle="modal" data-target="#deleteModal" data-id="<?php echo $manufacturer['manufacturer_id']; ?>" data-name="<?php echo $manufacturer['manufacturer_name']; ?>"><i class="fas fa-trash-alt"></i></button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="manufacturerModal" tabindex="-1" aria-labelledby="manufacturerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" id="manufacturerForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="manufacturerModalLabel">Thêm nhà sản xuất</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" id="manufacturerAction" value="create">
                    <input type="hidden" name="manufacturer_id" id="manufacturerId">
                    <div class="form-group">
                        <label for="manufacturer_name">Tên nhà sản xuất</label>
                        <input type="text" class="form-control" id="manufacturer_name" name="manufacturer_name" required
                            minlength="2" maxlength="50"
                            oninput="this.setCustomValidity('')" 
                            oninvalid="this.setCustomValidity('Tên nhà sản xuất phải có từ 2 đến 50 ký tự')">
                    </div>
                    <div class="form-group">
                        <label for="description">Mô Tả</label>
                        <textarea class="form-control" id="description" name="description" rows="12"
                        maxlength="50"
                            oninput="this.setCustomValidity('')" 
                            oninvalid="this.setCustomValidity('Mô tả có độ dài tối đa 1024 ký tự')"></textarea>
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
                <h5 class="modal-title" id="deleteModalLabel">Xóa nhà sản xuất</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa nhà sản xuất <strong id="deleteManufacturerName"></strong> này không?
            </div>
            <div class="modal-footer">
                <form method="GET" id="deleteForm">
                    <input type="hidden" name="delete" id="deleteManufacturerId">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../include/footer.php'; ?>

<script>
$('#manufacturerModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var action = button.data('action');
    var modal = $(this);
    var form = modal.find('#manufacturerForm');
    form[0].reset();

    if (action === 'edit') {
        var manufacturerId = button.data('id');
        var manufacturerName = button.data('name');
        var description = button.data('description');

        modal.find('.modal-title').text('Sửa nhà sản xuất');
        modal.find('#manufacturerAction').val('edit');
        modal.find('#manufacturerId').val(manufacturerId);
        modal.find('#manufacturer_name').val(manufacturerName);
        modal.find('#description').val(description);
    } else {
        modal.find('.modal-title').text('Thêm nhà sản xuất');
        modal.find('#manufacturerAction').val('create');
    }
});

$('#deleteModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var manufacturerId = button.data('id');
    var manufacturerName = button.data('name');
    var modal = $(this);
    modal.find('#deleteManufacturerId').val(manufacturerId);
    modal.find('#deleteManufacturerName').text(manufacturerName);
});
</script>
