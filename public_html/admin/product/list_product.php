<?php


require_once '../../config/config.php';
require_once '../../models/ProductModel.php';
require_once '../../models/ProductCategoryModel.php';
require_once '../../models/ProductFlavorModel.php';
require_once '../../models/ProductImageModel.php';
require_once '../../models/OriginModel.php';
require_once '../../models/ManufacturerModel.php';

$productModel = new ProductModel($pdo);
$productCategoryModel = new ProductCategoryModel($pdo);
$productFlavorModel = new ProductFlavorModel($pdo);
$productImageModel = new ProductImageModel($pdo);
$originModel = new OriginModel($pdo);
$manufacturerModel = new ManufacturerModel($pdo);

$errors = [];
$success = $_SESSION['success'] ?? '';
$page_number = isset($_GET['page_number']) ? (int)$_GET['page_number'] : 1;

// Handle actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $productId = $_POST['product_id'] ?? null;
    $productName = $_POST['product_name'];
    $description = $_POST['description'];
    $originalPrice = $_POST['original_price'];
    $unitPrice = $_POST['unit_price'];
    $isVisible = isset($_POST['is_visible']) ? 1 : 0;
    $originId = $_POST['origin_id'];
    $manufacturerId = $_POST['manufacturer_id'];
    $page_number = isset($_POST['page_number']) ? (int)$_POST['page_number'] : 1;

    if (empty($errors)) {
        $data = [
            'product_name' => $productName,
            'description' => $description,
            'original_price' => $originalPrice,
            'unit_price' => $unitPrice,
            'is_visible' => $isVisible,
            'origin_id' => $originId,
            'manufacturer_id' => $manufacturerId,
        ];

        if ($action == 'create') {
            $productModel->addProduct($data);
            $_SESSION['success'] = 'Thêm sản phẩm thành công';
        } elseif ($action == 'edit' && $productId) {
            $productModel->updateProduct($productId, $data);
            $_SESSION['success'] = 'Cập nhật sản phẩm thành công';
        }

        header('Location: list_product.php?page_number=' . $page_number);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['delete'])) {
    $productId = $_GET['delete'];
    $productModel->deleteProduct($productId);
    $_SESSION['success'] = 'Xóa sản phẩm thành công';
    header('Location: list_product.php');
    exit;
}

$origins = $originModel->getAllOrigins();
$manufacturers = $manufacturerModel->getAllManufacturers();


$itemsPerPage = 5; // Number of items per page
$offset = ($page_number - 1) * $itemsPerPage;

$products = $productModel->getProductsLimit($itemsPerPage, $offset);
$totalItems = $productModel->getTotalProducts();
$totalPages = ceil($totalItems / $itemsPerPage);
?>

<?php require_once '../include/header.php'; ?>

<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#productModal" data-action="create"><i class="fas fa-plus"></i> Thêm mới</button>
        <!-- <div class="search-box">
            <input type="text" id="searchInput" placeholder="Tìm kiếm..." value="<?php echo htmlspecialchars($search); ?>">
            <i class='fas fa-search' id="searchButton"></i>
        </div> -->
    </div>

    <table class="table table-striped">
    <thead>
        <tr>
            <th>Tên sản phẩm</th>
            <th>Xuất xứ</th>
            <th>Nhà sản xuất</th>
            <th class="text-end">Giá gốc</th>
            <th class="text-end">Giá bán</th>
            <th class="text-center">Hiển thị</th>
            <th class="text-center">Ngày tạo</th>
            <th class="text-center">Ngày cập nhật</th>
            <th class="text-center">Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $product): ?>
        <tr>
            <td><?php echo htmlspecialchars($product['product_name']); ?></td>
            <td><?php echo htmlspecialchars($product['origin_name']); ?></td>
            <td><?php echo htmlspecialchars($product['manufacturer_name']); ?></td>
            <td class="text-end"><?php echo number_format($product['original_price'], 0, ',', '.'); ?></td>
            <td class="text-end"><?php echo number_format($product['unit_price'], 0, ',', '.'); ?></td>
            <td class="text-center">
                <?php 
                    if ($product['is_visible']) {
                        echo '<span class="badge badge-success">Có</span>'; 
                    } else {
                        echo '<span class="badge badge-secondary">Không</span>'; 
                    }
                ?>
            </td>
            <td class="text-center"><?php echo (new DateTime($product['created_at']))->format('d/m/Y H:i:s'); ?></td>
            <td class="text-center"><?php echo (new DateTime($product['updated_at']))->format('d/m/Y H:i:s'); ?></td>
            <td class="text-center">
                <button type="button" class="btn-icon text-info" data-toggle="modal" data-target="#productModal" data-action="edit" data-id="<?php echo $product['product_id']; ?>" data-name="<?php echo $product['product_name']; ?>" data-description='<?php echo $product['description']; ?>' data-original_price="<?php echo $product['original_price']; ?>" data-unit_price="<?php echo $product['unit_price']; ?>" data-is_visible="<?php echo $product['is_visible']; ?>" data-origin_id="<?php echo $product['origin_id']; ?>" data-manufacturer_id="<?php echo $product['manufacturer_id']; ?>"><i class="fas fa-edit"></i></button> |
                <button type="button" class="btn-icon text-primary" data-toggle="modal" data-target="#categoryModal" data-id="<?php echo $product['product_id']; ?>"><i class="fas fa-list"></i></button> |
                <button type="button" class="btn-icon text-success" data-toggle="modal" data-target="#flavorModal" data-id="<?php echo $product['product_id']; ?>"><i class="fas fa-flask"></i></button> |
                <button type="button" class="btn-icon text-warning" data-toggle="modal" data-target="#imageModal" data-id="<?php echo $product['product_id']; ?>"><i class="fas fa-image"></i></button> |
                <button type="button" class="btn-icon text-danger" data-toggle="modal" data-target="#deleteModal" data-id="<?php echo $product['product_id']; ?>" data-name="<?php echo $product['product_name']; ?>"><i class="fas fa-trash-alt"></i></button>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

    <?php if ($totalPages > 1): ?>
<!-- Pagination -->
<div aria-label="Page navigation example">
    <ul class="pagination justify-content-center">
        <?php 
        // First button
        if ($page_number == 1): ?>
            <li class="page-item disabled">
                <a class="page-link" href="#" aria-label="First" tabindex="-1" aria-disabled="true">
                    <span aria-hidden="true">««</span>
                </a>
            </li>
        <?php else: ?>
            <li class="page-item">
                <a class="page-link" href="?page_number=1" aria-label="First">
                    <span aria-hidden="true">««</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- Previous button -->
        <?php if ($page_number == 1): ?>
            <li class="page-item disabled">
                <a class="page-link" href="#" aria-label="Previous" tabindex="-1" aria-disabled="true">
                    <span aria-hidden="true">«</span>
                </a>
            </li>
        <?php else: ?>
            <li class="page-item">
                <a class="page-link" href="?page_number=<?php echo $page_number - 1; ?>" aria-label="Previous">
                    <span aria-hidden="true">«</span>
                </a>
            </li>
        <?php endif; ?>

        <?php 
            // Calculate start and end pages for display
            $maxVisiblePages = 5; // Total pages to show (odd number for symmetry)
            $halfVisiblePages = floor($maxVisiblePages / 2);
            $startPage = max(1, $page_number - $halfVisiblePages);
            $endPage = min($totalPages, $page_number + $halfVisiblePages);

            // Adjust start and end if necessary to ensure $maxVisiblePages
            if ($endPage - $startPage + 1 < $maxVisiblePages) {
                $startPage = max(1, $endPage - $maxVisiblePages + 1);
            }
        ?>

        <!-- Page number buttons -->
        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
            <li class="page-item <?php echo $i == $page_number ? 'active disabled' : ''; ?>"> 
                <a class="page-link <?php echo $i == $page_number ? 'bg-primary text-white' : ''; ?>" 
                   href="<?php echo $i == $page_number ? '#' : '?page_number=' . $i; ?>" 
                   <?php echo $i == $page_number ? 'tabindex="-1" aria-disabled="true"' : ''; ?>>
                    <?php echo $i; ?>
                </a>
            </li>
        <?php endfor; ?>

        <?php 
        // ... (rest of your pagination logic for ellipsis) ... 
        ?>

        <!-- Next button -->
        <?php if ($page_number == $totalPages): ?>
            <li class="page-item disabled">
                <a class="page-link" href="#" aria-label="Next" tabindex="-1" aria-disabled="true">
                    <span aria-hidden="true">»</span>
                </a>
            </li>
        <?php else: ?>
            <li class="page-item">
                <a class="page-link" href="?page_number=<?php echo $page_number + 1; ?>" aria-label="Next">
                    <span aria-hidden="true">»</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- Last button -->
        <?php if ($page_number == $totalPages): ?>
            <li class="page-item disabled">
                <a class="page-link" href="#" aria-label="Last" tabindex="-1" aria-disabled="true">
                    <span aria-hidden="true">»»</span>
                </a>
            </li>
        <?php else: ?>
            <li class="page-item">
                <a class="page-link" href="?page_number=<?php echo $totalPages; ?>" aria-label="Last">
                    <span aria-hidden="true">»»</span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</div>
<?php endif; ?> 
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form method="POST" id="productForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalLabel">Thêm sản phẩm</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" id="productAction" value="create">
                    <input type="hidden" name="page_number" id="pageNumber" value="<?php echo $page_number; ?>">
                    <input type="hidden" name="product_id" id="productId">

                    <div class="form-group">
                        <label for="product_name">Tên sản phẩm</label>
                        <input type="text" class="form-control" id="product_name" name="product_name" required 
                               minlength="2" maxlength="255"
                               oninput="this.setCustomValidity('')" 
                               oninvalid="this.setCustomValidity('Tên sản phẩm phải có từ 2 đến 255 ký tự')">
                    </div>

                    <div class="form-group">
                        <label for="description">Mô tả</label>
                        <textarea class="form-control" id="description" name="description" required
                                  minlength="10"
                                  oninput="this.setCustomValidity('')" 
                                  oninvalid="this.setCustomValidity('Mô tả phải có ít nhất 10 ký tự')"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="original_price">Giá gốc</label>
                        <input type="number" class="form-control" id="original_price" name="original_price" min="0" required
                               oninput="this.setCustomValidity('')" 
                               oninvalid="this.setCustomValidity('Giá gốc phải là số dương')">
                    </div>

                    <div class="form-group">
                        <label for="unit_price">Giá bán</label>
                        <input type="number" class="form-control" id="unit_price" name="unit_price" min="0" required
                               oninput="this.setCustomValidity('')" 
                               oninvalid="this.setCustomValidity('Giá bán phải là số dương')">
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_visible" name="is_visible">
                            <label class="custom-control-label" for="is_visible">Hiển thị</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="origin_id">Xuất xứ</label>
                        <select class="form-control" id="origin_id" name="origin_id" required
                                oninput="this.setCustomValidity('')" 
                                oninvalid="this.setCustomValidity('Vui lòng chọn xuất xứ')">
                            <option value="">Chọn xuất xứ</option>
                            <?php foreach ($origins as $origin): ?>
                                <option value="<?php echo $origin['origin_id']; ?>"><?php echo htmlspecialchars($origin['country']); ?></option>
                            <?php endforeach; ?>
                        </select> 
                    </div>

                    <div class="form-group">
                        <label for="manufacturer_id">Nhà sản xuất</label>
                        <select class="form-control" id="manufacturer_id" name="manufacturer_id" required
                                oninput="this.setCustomValidity('')" 
                                oninvalid="this.setCustomValidity('Vui lòng chọn nhà sản xuất')">
                            <option value="">Chọn nhà sản xuất</option>
                            <?php foreach ($manufacturers as $manufacturer): ?>
                                <option value="<?php echo $manufacturer['manufacturer_id']; ?>"><?php echo htmlspecialchars($manufacturer['manufacturer_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
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
                <h5 class="modal-title" id="deleteModalLabel">Xóa sản phẩm</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa sản phẩm <strong id="deleteProductName"></strong>?
            </div>
            <div class="modal-footer">
                <form method="GET" id="deleteForm">
                    <input type="hidden" name="delete" id="deleteProductId">
                    <input type="hidden" name="page_number" id="pageNumber" value="<?php echo $page_number; ?>">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalLabel">Danh mục sản phẩm</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="categoryModalMessageContainer"></div> <!-- Container for messages -->
                <div id="categoryContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- Flavor Modal -->
<div class="modal fade" id="flavorModal" tabindex="-1" aria-labelledby="flavorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="flavorModalLabel">Hương vị sản phẩm</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="messageFlavorModalContainer"></div> <!-- Container for messages -->
                <div id="flavorContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Hình ảnh sản phẩm</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="messageImageModalContainer"></div> <!-- Container for messages -->
                <div id="imageContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<?php require_once '../include/footer.php'; ?>

<script>
    // Initialize CKEditor with CKFinder
    CKEDITOR.replace('description', {
        filebrowserBrowseUrl: '../../ckfinder_php_3.7.0/ckfinder/ckfinder.html',
        filebrowserUploadUrl: '../../ckfinder_php_3.7.0/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
        filebrowserImageUploadUrl: '../../ckfinder_php_3.7.0/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
        filebrowserFlashUploadUrl: '../../ckfinder_php_3.7.0/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
    });

    $(document).ready(function () {

    $('#productModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var action = button.data('action');
        var modal = $(this);
        var form = modal.find('#productForm');
        form[0].reset();

        if (action === 'edit') {
            var productId = button.data('id');
            var productName = button.data('name');
            var description = button.data('description');
            var originalPrice = button.data('original_price');
            var unitPrice = button.data('unit_price');
            var isVisible = button.data('is_visible');
            var originId = button.data('origin_id');
            var manufacturerId = button.data('manufacturer_id');

            modal.find('.modal-title').text('Sửa sản phẩm');
            modal.find('#productAction').val('edit');
            modal.find('#productId').val(productId);
            modal.find('#product_name').val(productName);
            CKEDITOR.instances['description'].setData(description);
            modal.find('#original_price').val(originalPrice);
            modal.find('#unit_price').val(unitPrice);
            modal.find('#is_visible').prop('checked', isVisible);
            modal.find('#origin_id').val(originId);
            modal.find('#manufacturer_id').val(manufacturerId);
        } else {
            modal.find('.modal-title').text('Thêm sản phẩm');
            modal.find('#productAction').val('create');
            CKEDITOR.instances['description'].setData('');
        }
    });

    $('#deleteModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var productId = button.data('id');
        var productName = button.data('name'); // Get the product name
        var modal = $(this);
        modal.find('#deleteProductId').val(productId);
        modal.find('#deleteProductName').text(productName); // Set the product name
    });


    $('#categoryModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var productId = button.data('id');
        var modal = $(this);

        function loadCategories() {
            $.ajax({
                url: 'handle_product.php',
                type: 'GET',
                data: { action: 'get_categories', product_id: productId },
                success: function (response) {
                    modal.find('#categoryContent').html(response);

                    modal.find('.delete-category').on('click', function () {
                        var categoryId = $(this).data('category-id');
                        $.ajax({
                            url: 'handle_product.php',
                            type: 'POST',
                            data: { action: 'delete_category', product_id: productId, category_id: categoryId },
                            success: function (response) {
                                var result = JSON.parse(response);
                                showMessage(result.message, result.success);
                                loadCategories();
                            }
                        });
                    });

                    $('#addCategoryBtn').off('click').on('click', function () {
                        var categoryId = $('#categorySelect').val();
                        if (categoryId) {
                            $.ajax({
                                url: 'handle_product.php',
                                type: 'POST',
                                data: { action: 'add_category', product_id: productId, category_id: categoryId },
                                success: function (response) {
                                    var result = JSON.parse(response);
                                    showMessage(result.message, result.success);
                                    loadCategories();
                                }
                            });
                        } else {
                            showMessage('Vui lòng chọn một danh mục', false);
                        }
                    });
                }
            });
        }

        function showMessage(message, success) {
            var messageContainer = modal.find('#categoryModalMessageContainer');
            var alertType = success ? 'alert-success' : 'alert-danger';
            messageContainer.html('<div class="alert ' + alertType + ' alert-dismissible fade show" role="alert">' +
                message +
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                '<span aria-hidden="true">&times;</span>' +
                '</button></div>');
        }

        loadCategories();
    });

    $('#flavorModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var productId = button.data('id');
    var modal = $(this);

    function loadFlavors() {
        $.ajax({
            url: 'handle_product.php',
            type: 'GET',
            data: { action: 'get_flavors', product_id: productId },
            success: function (response) {
                modal.find('#flavorContent').html(response);

                modal.find('.delete-flavor').on('click', function () {
                    var flavorId = $(this).data('flavor-id');
                    $.ajax({
                        url: 'handle_product.php',
                        type: 'POST',
                        data: { action: 'delete_flavor', flavor_id: flavorId },
                        success: function (response) {
                            var result = JSON.parse(response);
                            showMessage(result.message, result.success);
                            loadFlavors();
                        }
                    });
                });

           // Handle update flavor quantity action
            modal.find('.update-quantity').on('click', function () {
                    var flavorId = $(this).data('flavor-id');
                    var newQuantity = $(this).closest('li').find('.quantity-input').val();  // Get the quantity from the input field
                    $.ajax({
                        url: 'handle_product.php',
                        type: 'POST',
                        data: { action: 'update_flavor_quantity', flavor_id: flavorId, quantity: newQuantity },
                        success: function (response) {
                            var result = JSON.parse(response);
                            showMessage(result.message, result.success);
                            loadFlavors();
                        }
                    });
                });

                $('#addFlavorBtn').off('click').on('click', function () {
                    var flavorName = $('#flavorName').val();
                    var stockQuantity = $('#stockQuantity').val();
                    if (flavorName && stockQuantity) {
                        $.ajax({
                            url: 'handle_product.php',
                            type: 'POST',
                            data: { action: 'add_flavor', product_id: productId, flavor_name: flavorName, stock_quantity: stockQuantity },
                            success: function (response) {
                                var result = JSON.parse(response);
                                showMessage(result.message, result.success);
                                loadFlavors();
                            }
                        });
                    } else {
                        showMessage('Vui lòng nhập đầy đủ thông tin', false);
                    }
                });
            }
        });
    }

    function showMessage(message, success) {
        var messageContainer = modal.find('#messageFlavorModalContainer');
        var alertType = success ? 'alert-success' : 'alert-danger';
        messageContainer.html('<div class="alert ' + alertType + ' alert-dismissible fade show" role="alert">' +
            message +
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
            '<span aria-hidden="true">&times;</span>' +
            '</button></div>');
    }

    loadFlavors();
});


    $('#imageModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var productId = button.data('id');
        var modal = $(this);

        function loadImages() {
            $.ajax({
                url: 'handle_product.php',
                type: 'GET',
                data: { action: 'get_images', product_id: productId },
                success: function (response) {
                    modal.find('#imageContent').html(response);

                    modal.find('.delete-image').on('click', function () {
                        var imageId = $(this).data('image-id');
                        $.ajax({
                            url: 'handle_product.php',
                            type: 'POST',
                            data: { action: 'delete_image', image_id: imageId },
                            success: function (response) {
                                var result = JSON.parse(response);
                                showMessage(result.message, result.success);
                                loadImages();
                            }
                        });
                    });

                    // Image preview on file input change
                    $('#imageFile').on('change', function () {
                        var files = this.files;
                        var preview = $('#imagePreview');
                        preview.html(''); // Clear any previous previews

                        if (files.length > 0) {
                            var row = $('<div>', { class: 'row' }); // Create a row container

                            $.each(files, function(index, file) {
                                var reader = new FileReader();

                                reader.onload = function (e) {
                                    // Create the card structure
                                    var col = $('<div>', { class: 'col-md-4 mb-3' }); // Column for each card
                                    var card = $('<div>', { class: 'card' });
                                    var imgElement = $('<img>', {
                                        src: e.target.result,
                                        class: 'card-img-top',
                                        alt: 'Selected Image',
                                        style: 'max-width: 100%;' // Ensure the image fits within the card
                                    });

                                    // Append image to card
                                    card.append(imgElement);
                                    // Append card to column
                                    col.append(card);
                                    // Append column to row
                                    row.append(col);
                                };

                                reader.readAsDataURL(file);
                            });

                            // Append the row to the preview container
                            preview.append(row);
                        } else {
                            preview.html(''); // Clear the preview if no files are selected
                        }
                    });


                    $('#addImageButton').off('click').on('click', function () {
                        var formData = new FormData();
                        var imageFiles = $('#imageFile')[0].files;

                        if (imageFiles.length > 0) {
                            // Append each selected image file to the FormData object
                            $.each(imageFiles, function(index, file) {
                                formData.append('images[]', file);
                            });
                            console.log(formData);
                            formData.append('product_id', productId);
                            formData.append('action', "add_images"); // Change the action to reflect multiple images

                            $.ajax({
                                url: 'handle_product.php',
                                type: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function (response) {
                                    var result = JSON.parse(response);
                                    showMessage(result.message, result.success);
                                    if (result.success) {
                                        loadImages(); // Reload images after successful upload
                                    }
                                }
                            });
                        } else {
                            showMessage('Vui lòng chọn ít nhất một hình ảnh', false);
                        }
                    });


                }
            });
        }



        var selectedImageId = null; // Variable to store selected image ID

        // Handle card click for selection
        $(document).on('click', '.card', function() {
            // Remove border from all cards
            $('.card').css('border', '2px solid transparent');
            
            // Add border to the selected card
            $(this).css('border', '2px solid blue');
            
            // Store the selected image ID
            selectedImageId = $(this).attr('id').split('-').pop();

            // Send AJAX request to update the main image
            $.ajax({
                url: 'handle_product.php',
                method: 'POST',
                data: {
                    action: 'set_main_image',
                    product_id: productId,
                    main_image_id: selectedImageId
                },
                success: function(response) {
                    var result = JSON.parse(response);
                    showMessage(result.message, result.success);
                },
                error: function() {
                    showMessage('Có lỗi xảy ra. Vui lòng thử lại.', false);
                }
            });
        });
      

        function showMessage(message, success) {
            var messageContainer = modal.find('#messageImageModalContainer');
            var alertType = success ? 'alert-success' : 'alert-danger';
            messageContainer.html('<div class="alert ' + alertType + ' alert-dismissible fade show" role="alert">' +
                message +
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                '<span aria-hidden="true">&times;</span>' +
                '</button></div>');
        }

        loadImages();
    });
});

</script>
