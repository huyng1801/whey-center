<?php


require_once '../../config/config.php';
require_once '../../models/BannerModel.php';

$bannerModel = new BannerModel($pdo);

$errors = [];
$success = $_SESSION['success'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $bannerId = $_POST['banner_id'] ?? null;
    $title = $_POST['title'];
    $link = $_POST['link'];
    $isVisible = isset($_POST['is_visible']) ? 1 : 0;

    // Handle file upload
    $newFilename = $_POST['existing_image'] ?? '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $fileTmp = $_FILES['image']['tmp_name'];
        $fileSize = $_FILES['image']['size'];
        $fileExt = pathinfo($filename, PATHINFO_EXTENSION);

        if (!in_array($fileExt, $allowed)) {
            $errors['image'] = 'Loại tệp không hợp lệ';
        }

        if ($fileSize > 5000000) { // 5MB
            $errors['image'] = 'Kích thước tệp vượt quá giới hạn';
        }

        if (empty($errors)) {
            $newFilename = uniqid() . '.' . $fileExt;
            $uploadDir = '../../uploads/';
            $uploadFile = $uploadDir . $newFilename;

            if (!move_uploaded_file($fileTmp, $uploadFile)) {
                $errors['image'] = 'Tải lên tệp thất bại';
            } else {
                // Delete the old image if a new one is uploaded
                if ($action == 'edit' && !empty($_POST['existing_image'])) {
                    $oldImage = $uploadDir . $_POST['existing_image'];
                    if (file_exists($oldImage)) {
                        unlink($oldImage);
                    }
                }
            }
        }
    }

    if (empty($errors)) {
        $data = [
            'title' => $title,
            'image_url' => $newFilename,
            'link' => $link,
            'is_visible' => $isVisible
        ];

        if ($action == 'create') {
            $bannerModel->addBanner($data);
            $_SESSION['success'] = 'Thêm banner thành công';
        } elseif ($action == 'edit' && $bannerId) {
            $bannerModel->updateBanner($bannerId, $data);
            $_SESSION['success'] = 'Cập nhật banner thành công';
        }

        header('Location: list_banner.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['delete'])) {
    $bannerId = $_GET['delete'];
    $banner = $bannerModel->getBannerById($bannerId);

    if ($banner) {
        $uploadDir = '../../uploads/';
        $imagePath = $uploadDir . $banner['image_url'];

        // Delete the image file
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        $bannerModel->deleteBanner($bannerId);
        $_SESSION['success'] = 'Xóa banner thành công';
    }

    header('Location: list_banner.php');
    exit;
}

$banners = $bannerModel->getAllBanners();
?>

<?php require_once '../include/header.php'; ?>

<div>
    <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#bannerModal" data-action="create"><i class="fas fa-plus"></i> Thêm mới</button>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">Hình ảnh</th>
                <th>Tiêu đề</th>
                <th>Liên kết</th>
                <th class="text-center">Hiển thị</th>
                <th class="text-center">Ngày tạo</th>
                <th class="text-center">Ngày cập nhật</th>
                <th class="text-center">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($banners as $banner): ?>
            <tr>
                <td class="text-center"><img src="../../uploads/<?php echo htmlspecialchars($banner['image_url']); ?>" alt="Banner Image" width="100"></td>
                <td><?php echo htmlspecialchars($banner['title']); ?></td>
                <td><?php echo htmlspecialchars($banner['link']); ?></td>
                <td class="text-center">
                    <?php 
                        if ($banner['is_visible']) {
                            echo '<span class="badge badge-success">Có</span>'; 
                        } else {
                            echo '<span class="badge badge-secondary">Không</span>'; 
                        }
                    ?>
                </td>
                <td class="text-center"><?php echo (new DateTime($banner['created_at']))->format('d/m/Y H:i:s'); ?></td>
                <td class="text-center"><?php echo (new DateTime($banner['updated_at']))->format('d/m/Y H:i:s'); ?></td>
                <td class="text-center">
                    <button type="button" class="btn-icon text-info" data-toggle="modal" data-target="#bannerModal" data-action="edit" data-id="<?php echo $banner['banner_id']; ?>" data-title="<?php echo $banner['title']; ?>" data-image="<?php echo $banner['image_url']; ?>" data-link="<?php echo $banner['link']; ?>" data-is_visible="<?php echo $banner['is_visible']; ?>"><i class="fas fa-edit"></i></button> |
                    <button type="button" class="btn-icon text-danger" data-toggle="modal" data-target="#deleteModal" data-id="<?php echo $banner['banner_id']; ?>" data-title="<?php echo $banner['title']; ?>"><i class="fas fa-trash-alt"></i></button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="bannerModal" tabindex="-1" aria-labelledby="bannerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data" id="bannerForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="bannerModalLabel">Thêm quảng cáo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" id="bannerAction" value="create">
                    <input type="hidden" name="banner_id" id="bannerId">
                    <div class="form-group">
                        <label for="title">Tiêu đề</label>
                        <input type="text" class="form-control" id="title" name="title" required
                                minlength="2" maxlength="100"
                               oninput="this.setCustomValidity('')" 
                               oninvalid="this.setCustomValidity('Tiêu đề phải có từ 2 đến 100 ký tự')">
                    </div>
                    <div class="form-group">
                        <label for="image">Hình ảnh</label>
                        <input type="file" class="form-control-file" id="image" name="image" 
                               oninput="this.setCustomValidity('')" 
                               oninvalid="this.setCustomValidity('Vui lòng chọn hình ảnh')"> 
                        <input type="hidden" name="existing_image" id="existingImage">
                    </div>
                    <div class="form-group">
                        <label for="link">Liên kết</label>
                        <input type="text" class="form-control" id="link" name="link">
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_visible" name="is_visible">
                            <label class="custom-control-label" for="is_visible">Hiển thị</label>
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
                <h5 class="modal-title" id="deleteModalLabel">Xóa quảng cáo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa quảng cáo <strong id="deleteBannerTitle"></strong> này không?
            </div>
            <div class="modal-footer">
                <form method="GET" id="deleteForm">
                    <input type="hidden" name="delete" id="deleteBannerId">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../include/footer.php'; ?>

<script>
$('#bannerModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var action = button.data('action');
    var modal = $(this);
    var form = modal.find('#bannerForm');
    form[0].reset();

    var image = modal.find('#image');

    if (action === 'edit') {
        var bannerId = button.data('id');
        var title = button.data('title');
        var existingImage = button.data('image');
        var link = button.data('link');
        var isVisible = button.data('is_visible');

        
        modal.find('.modal-title').text('Sửa quảng cáo');
        modal.find('#bannerAction').val('edit');
        modal.find('#bannerId').val(bannerId);
        modal.find('#title').val(title);
        modal.find('#existingImage').val(existingImage);
        modal.find('#link').val(link);
        modal.find('#is_visible').prop('checked', isVisible);

        image.removeAttr('required');
    } else {
        modal.find('.modal-title').text('Thêm quảng cáo');
        modal.find('#bannerAction').val('create');

        image.attr('required', 'required');
    }
});

$('#deleteModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var bannerId = button.data('id');
    var title = button.data('title');
    var modal = $(this);
    modal.find('#deleteBannerId').val(bannerId);
    modal.find('#deleteBannerTitle').text(title);
});
</script>
