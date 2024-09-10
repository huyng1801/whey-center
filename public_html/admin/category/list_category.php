<?php


require_once '../../config/config.php';
require_once '../../models/CategoryModel.php';

$categoryModel = new CategoryModel($pdo);

$errors = [];
$success = $_SESSION['success'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $categoryId = $_POST['category_id'] ?? null;
    $categoryName = $_POST['category_name'];

    // Check if category name already exists
    if ($action == 'create' && $categoryModel->isCategoryNameExists($categoryName)) {
        $errors['category_name'] = 'Tên danh mục đã tồn tại';
    } elseif ($action == 'edit' && $categoryModel->isCategoryNameExistsExcept($categoryName, $categoryId)) {
        $errors['category_name'] = 'Tên danh mục đã tồn tại';
    }

    if (empty($errors)) {
        if ($action == 'create') {
            $categoryModel->addCategory(['category_name' => $categoryName]);
            $_SESSION['success'] = 'Thêm danh mục thành công';
        } elseif ($action == 'edit' && $categoryId) {
            $categoryModel->updateCategory($categoryId, ['category_name' => $categoryName]);
            $_SESSION['success'] = 'Cập nhật danh mục thành công';
        }
        header('Location: list_category.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['delete'])) {
    $categoryId = $_GET['delete'];
    $categoryModel->deleteCategory($categoryId);
    $_SESSION['success'] = 'Xóa danh mục thành công';
    header('Location: list_category.php');
    exit;
}

$categories = $categoryModel->getAllCategories();
?>

<?php require_once '../include/header.php'; ?>

<div>
    <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#categoryModal" data-action="create"><i class="fas fa-plus"></i> Thêm mới</button>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Tên danh mục</th>
                <th class="text-center">Ngày tạo</th>
                <th class="text-center">Ngày cập nhật</th>
                <th class="text-center">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $category): ?>
            <tr>
                <td><?php echo htmlspecialchars($category['category_name']); ?></td>
                <td class="text-center"><?php echo (new DateTime($category['created_at']))->format('d/m/Y H:i:s'); ?></td>
                <td class="text-center"><?php echo (new DateTime($category['updated_at']))->format('d/m/Y H:i:s'); ?></td>
                <td class="text-center">
                    <button type="button" class="btn-icon text-info" data-toggle="modal" data-target="#categoryModal" data-action="edit" data-id="<?php echo $category['category_id']; ?>" data-name="<?php echo $category['category_name']; ?>"><i class="fas fa-edit"></i></button> |
                    <button type="button" class="btn-icon text-danger" data-toggle="modal" data-target="#deleteModal" data-id="<?php echo $category['category_id']; ?>" data-name="<?php echo $category['category_name']; ?>"><i class="fas fa-trash-alt"></i></button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="categoryForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalLabel">Thêm danh mục</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" id="categoryAction" value="create">
                    <input type="hidden" name="category_id" id="categoryId">
                    <div class="form-group">
                        <label for="categoryName">Tên danh mục</label>
                        <input type="text" class="form-control" id="categoryName" name="category_name" required
                               minlength="2" maxlength="100"
                               oninput="this.setCustomValidity('')" 
                               oninvalid="this.setCustomValidity('Tên danh mục phải có từ 2 đến 100 ký tự')">
                        <div class="invalid-feedback">
                            Vui lòng nhập tên danh mục hợp lệ.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary" id="saveCategoryButton">Lưu thay đổi</button>
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
                <h5 class="modal-title" id="deleteModalLabel">Xóa danh mục</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa danh mục <strong id="deleteCategoryName"></strong> này không?
            </div>
            <div class="modal-footer">
                <form method="GET" id="deleteForm">
                    <input type="hidden" name="delete" id="deleteCategoryId">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../include/footer.php'; ?>

<script>
$('#categoryModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var action = button.data('action');
    var modal = $(this);
    var form = modal.find('#categoryForm');
    form[0].reset();

    if (action === 'edit') {
        var categoryId = button.data('id');
        var categoryName = button.data('name');
        modal.find('.modal-title').text('Sửa danh mục');
        modal.find('#categoryAction').val('edit');
        modal.find('#categoryId').val(categoryId);
        modal.find('#categoryName').val(categoryName);
    } else {
        modal.find('.modal-title').text('Thêm danh mục');
        modal.find('#categoryAction').val('create');
    }
});

$('#deleteModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var categoryId = button.data('id');
    var categoryName = button.data('name');
    var modal = $(this);
    modal.find('#deleteCategoryId').val(categoryId);
    modal.find('#deleteCategoryName').text(categoryName);
});
</script>
