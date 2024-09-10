<?php
session_start();

require_once '../../config/config.php';
require_once '../../models/ProductCategoryModel.php';
require_once '../../models/ProductFlavorModel.php';
require_once '../../models/ProductImageModel.php';
require_once '../../models/CategoryModel.php';
require_once '../../models/OriginModel.php';
require_once '../../models/ManufacturerModel.php';

$productCategoryModel = new ProductCategoryModel($pdo);
$productFlavorModel = new ProductFlavorModel($pdo);
$productImageModel = new ProductImageModel($pdo);
$categoryModel = new CategoryModel($pdo);
$originModel = new OriginModel($pdo);
$manufacturerModel = new ManufacturerModel($pdo);

$action = $_POST['action'] ?? $_GET['action'] ?? '';

$response = [];

switch ($action) {
    case 'add_category':
        $productId = $_POST['product_id'];
        $categoryId = $_POST['category_id'];
        $productCategoryModel->addProductCategory(['product_id' => $productId, 'category_id' => $categoryId]);
        $response = [
            'success' => true,
            'message' => 'Thêm danh mục thành công'
        ];
        echo json_encode($response);
        break;

    case 'add_flavor':
        $productId = $_POST['product_id'];
        $flavorName = $_POST['flavor_name'];
        $stockQuantity = $_POST['stock_quantity'];
        $productFlavorModel->addFlavor([
            'flavor_name' => $flavorName,
            'stock_quantity' => $stockQuantity,
            'product_id' => $productId
        ]);
        $response = [
            'success' => true,
            'message' => 'Thêm hương vị thành công'
        ];
        echo json_encode($response);
        break;

        case 'add_images':
            $productId = $_POST['product_id'];
            $uploadDir = '../../uploads/';
            $response = ['success' => false, 'message' => ''];
        
            if (!empty($_FILES['images']['name'][0])) { // Check if at least one file was uploaded
                $fileCount = count($_FILES['images']['name']);
                $uploadedImages = [];
                
                for ($i = 0; $i < $fileCount; $i++) {
                    $imageName = $_FILES['images']['name'][$i];
                    $imageTmpName = $_FILES['images']['tmp_name'][$i];
                    $imageExt = pathinfo($imageName, PATHINFO_EXTENSION);
                    $newImageName = uniqid() . '.' . $imageExt;
                    $uploadPath = $uploadDir . $newImageName;
        
                    if (move_uploaded_file($imageTmpName, $uploadPath)) {
                        $productImageModel->addImage([
                            'image_url' => $newImageName,
                            'product_id' => $productId
                        ]);
                        $uploadedImages[] = $newImageName;
                    } else {
                        $response['message'] = 'Tải lên hình ảnh thất bại cho một số hình ảnh';
                        echo json_encode($response);
                        return;
                    }
                }
        
                $response = [
                    'success' => true,
                    'message' => 'Thêm hình ảnh thành công',
                    'uploaded_images' => $uploadedImages
                ];
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Vui lòng chọn ít nhất một hình ảnh'
                ];
            }
        
            echo json_encode($response);
            break;
        

    case 'update_flavor_quantity':
            $flavorId = $_POST['flavor_id'];
            $quantity = $_POST['quantity'];
            $productFlavorModel->updateFlavorQuantity($flavorId, $quantity);
            $response = [
                'success' => true,
                'message' => 'Cập nhật số lượng thành công'
            ];
            echo json_encode($response);
            break;
    case 'delete_category':
        $productId = $_POST['product_id'];
        $categoryId = $_POST['category_id'];
        $productCategoryModel->deleteProductCategory($productId, $categoryId);
        $response = [
            'success' => true,
            'message' => 'Xóa danh mục thành công'
        ];
        echo json_encode($response);
        break;

    case 'delete_flavor':
        $flavorId = $_POST['flavor_id'];
        $productFlavorModel->deleteFlavor($flavorId);
        $response = [
            'success' => true,
            'message' => 'Xóa hương vị thành công'
        ];
        echo json_encode($response);
        break;

    case 'delete_image':
        $imageId = $_POST['image_id'];
        $image = $productImageModel->getImageById($imageId);
        $uploadDir = '../../uploads/';
        if ($image) {
            $imagePath = $uploadDir . $image['image_url'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
            $productImageModel->deleteImage($imageId);
            $response = [
                'success' => true,
                'message' => 'Xóa hình ảnh thành công'
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Không tìm thấy hình ảnh'
            ];
        }
        echo json_encode($response);
        break;

    case 'get_categories':
        $productId = $_GET['product_id'];
        $productCategories = $productCategoryModel->getProductCategoriesByProductId($productId);
        $allCategories = $categoryModel->getCategoriesNotInProduct($productId); // This method gets categories not already associated with the product
        ?>
        <ul class="list-group">
            <?php foreach ($productCategories as $category): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?php echo htmlspecialchars($category['category_name']); ?>
                    <button class="btn btn-danger btn-sm delete-category" data-product-id="<?php echo $productId; ?>" data-category-id="<?php echo $category['category_id']; ?>">Xóa</button>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="mt-3">
            <select class="form-control" id="categorySelect">
                <option value="">Chọn danh mục</option>
                <?php foreach ($allCategories as $category): ?>
                    <option value="<?php echo $category['category_id']; ?>"><?php echo htmlspecialchars($category['category_name']); ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-primary mt-2" id="addCategoryBtn" data-product-id="<?php echo $productId; ?>">Thêm danh mục</button>
        </div>
        <?php
        break;

    case 'get_flavors':
        $productId = $_GET['product_id'];
        $flavors = $productFlavorModel->getFlavorsByProductId($productId);
        ?>
        <ul class="list-group">
            <?php foreach ($flavors as $flavor): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                <span><?php echo htmlspecialchars($flavor['flavor_name']); ?> (Số lượng: <input type="number" class="quantity-input" value="<?php echo htmlspecialchars($flavor['stock_quantity']); ?>" min="0" style="width: 60px;">)</span>
                <div>
                    <button class="btn btn-info btn-sm update-quantity" data-flavor-id="<?php echo $flavor['product_flavor_id']; ?>">Sửa</button>
                    <button class="btn btn-danger btn-sm delete-flavor" data-flavor-id="<?php echo $flavor['product_flavor_id']; ?>">Xóa</button>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
        <div class="mt-3">
            <input type="text" class="form-control" id="flavorName" placeholder="Tên hương vị">
            <input type="number" class="form-control mt-2" id="stockQuantity" placeholder="Số lượng" min="0">
            <button class="btn btn-primary mt-2" id="addFlavorBtn">Thêm hương vị</button>
        </div>
        <?php
        break;

        case 'get_images':
            $productId = $_GET['product_id'];
            $images = $productImageModel->getImagesByProductId($productId);
            $mainImageId = $productImageModel->getMainImageId($productId); // Method to get the ID of the main image
            ?>
            <div class="row">
                <?php foreach ($images as $image): ?>
                    <div class="col-md-4 position-relative">
                        <div class="position-absolute" style="top: 10px; right: 24px; z-index: 999">
                            <button class="btn btn-danger btn-sm delete-image" data-image-id="<?php echo $image['product_image_id']; ?>">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                        <div class="card mb-3" id="image-card-<?php echo $image['product_image_id']; ?>" 
                             style="border: 2px solid <?php echo ($image['product_image_id'] == $mainImageId) ? 'blue' : 'transparent'; ?>; cursor: pointer;">
                            <img src="../../uploads/<?php echo htmlspecialchars($image['image_url']); ?>" class="card-img-top" alt="Product Image">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="mt-3">
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="imageFile" aria-describedby="addImageButton" multiple>
                    <label class="custom-file-label" for="imageFile">Chọn hình ảnh</label>
                </div>
                <div id="imagePreview" class="mt-2 row"></div>
                <button class="btn btn-primary mt-2" id="addImageButton">Thêm hình ảnh</button>
            </div>
            <?php
            break;
        
        
    case 'set_main_image':
        $productId = $_POST['product_id'];
        $mainImageId = $_POST['main_image_id'];

        try {
            $productImageModel->updateImage($productId, $mainImageId);
            echo json_encode(['success' => true, 'message' => 'Hình chính đã được cập nhật thành công.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra khi cập nhật hình chính.']);
        }
        break;

        

    default:
        $response = [
            'success' => false,
            'message' => 'Hành động không hợp lệ'
        ];
        echo json_encode($response);
        break;
}
?>
