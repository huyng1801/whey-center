<?php
require_once 'config/config.php';
require_once 'models/ProductModel.php';
require_once 'models/ProductImageModel.php';
require_once 'models/ProductFlavorModel.php';
require_once 'models/ProductCategoryModel.php';

$productModel = new ProductModel($pdo);
$productImageModel = new ProductImageModel($pdo);
$productFlavorModel = new ProductFlavorModel($pdo);
$productCategoryModel = new ProductCategoryModel($pdo);

$productDetails = $productModel->getProductById($_GET['productId']);
$productImages = $productImageModel->getImagesByProductId($productDetails['product_id']);
$productFlavors = $productFlavorModel->getFlavorsByProductId($productDetails['product_id']);
$relatedProducts = $productModel->getRelatedProducts($productDetails['product_id']);
$productCategories = $productCategoryModel->getProductCategoriesByProductId($productDetails['product_id']);

?>

<?php include 'header.php'; ?>

<div class="product-detail-container mt-5">
    <div class="row">
        <div class="col-md-4 product-detail-left">
            <div id="thumbnailCarousel" class="carousel-thumbnails">
                <div class="carousel-inner-thumbnails">
                    <?php if (!empty($productImages)): ?>
                        <?php foreach ($productImages as $index => $image): ?>
                            <div class="carousel-thumbnails-item">
                                <img class="img-thumbnail clickable-thumbnail <?php echo $index === 0 ? 'selected' : ''; ?>" src="uploads/<?php echo htmlspecialchars($image['image_url']); ?>" alt="Product Thumbnail" data-index="<?php echo $index; ?>">
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div id="productImageCarousel" class="carousel">
                <div class="carousel-inner-main">
                    <?php if (!empty($productImages)): ?>
                        <?php
                        // Get the first and last image for loop effect
                        $firstImage = reset($productImages);
                        $lastImage = end($productImages);
                        ?>

                        <!-- Display the last image at the start for loop effect -->
                        <div class="carousel-item-main">
                            <img class="d-block w-100 main-img" src="uploads/<?php echo htmlspecialchars($lastImage['image_url']); ?>" alt="Product Image">
                        </div>

                        <!-- Display all product images -->
                        <?php foreach ($productImages as $image): ?>
                            <div class="carousel-item-main">
                                <img class="d-block w-100 main-img" src="uploads/<?php echo htmlspecialchars($image['image_url']); ?>" alt="Product Image">
                            </div>
                        <?php endforeach; ?>

                        <!-- Display the first image again at the end for loop effect -->
                        <div class="carousel-item-main">
                            <img class="d-block w-100 main-img" src="uploads/<?php echo htmlspecialchars($firstImage['image_url']); ?>" alt="Product Image">
                        </div>
                    <?php else: ?>
                        <div class="carousel-item-main">
                            <img class="d-block w-100 main-img" src="uploads/placeholder.png" alt="No Image Available">
                        </div>
                    <?php endif; ?>
                </div>
                <button class="carousel-control-prev" type="button"><i class="fa-solid fa-chevron-left"></i></button>
                <button class="carousel-control-next" type="button"><i class="fa-solid fa-chevron-right"></i></button>
            </div>

        </div>
        <div class="col-md-8">
            <h2 id="productName" class="product-name"><?php echo htmlspecialchars($productDetails['product_name']); ?></h2>
            <div class="is-devide"></div>
            <div class="d-flex justify-content-left">
                <?php if ($productDetails['original_price'] != $productDetails['unit_price']): ?>
                    <span class="original-price"><?php echo number_format($productDetails['original_price'], 0, ',', '.'); ?>₫</span>
                <?php endif; ?>
                <span class="unit-price"><?php echo number_format($productDetails['unit_price'], 0, ',', '.'); ?>₫</span>
            </div>
            <form id="addToCartForm" method="POST" class="add-to-cart-form">
                <input type="hidden" name="productId" value="<?php echo htmlspecialchars($productDetails['product_id']); ?>">
                <input type="hidden" id="flavorId" name="flavorId" value="<?php echo htmlspecialchars($productFlavors[0]['product_flavor_id']); ?>">
                <div class="form-group mt-2">
                    <div class="flavor-options">
                        <?php foreach ($productFlavors as $index => $flavor): ?>
                            <button type="button" class="flavor-button <?php echo $index === 0 ? 'selected' : ''; ?>" data-flavor-id="<?php echo htmlspecialchars($flavor['product_flavor_id']); ?>" data-stock="<?php echo htmlspecialchars($flavor['stock_quantity']); ?>">
                                <?php echo htmlspecialchars($flavor['flavor_name']); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="d-flex justify-content-left align-items-center mb-4">
                    <div class="quantity-container">
                        <button type="button" class="btn-quantity" id="decreaseQty">-</button>
                        <input type="text" id="quantity" class="input-quantity" name="quantity" value="1" pattern="[0-9]*" title="Please enter a number" inputmode="numeric">
                        <button type="button" class="btn-quantity" id="increaseQty">+</button>
                    </div>
                    <button type="submit" id="btn-add-to-cart" class="add-to-cart-btn" name="btn-add-to-cart">Thêm vào giỏ hàng</button>
                </div>
            </form>
            <div class="devide-group">
                <p class="product-origin">Xuất xứ: <?php echo htmlspecialchars($productDetails['origin_country']); ?></p>
                <p class="product-manufacturer">Nhà sản xuất: <?php echo htmlspecialchars($productDetails['manufacturer_name']); ?></p>
                <p class="product-categories">Danh mục:
                    <?php 
                    echo htmlspecialchars(implode(', ', array_column($productCategories, 'category_name'))); 
                    ?>
                </p>
            </div>
        </div>
    </div>
</div>
<div class="product-description-container mt-5">
    <div class="accordion-description">
        <div class="accordion-description-toggle active">
            <a><i class="fa-solid fa-chevron-down icon-toggle"></i> <span class="ml-2">Mô tả</span></a>
        </div>
        <div class="accordion-additional-information">
            <?php echo htmlspecialchars_decode($productDetails['description']); ?>
        </div>
    </div>
</div>
<div class="relative-product-container mt-3">
    <h2>Sản phẩm tương tự</h2>
    <div id="relativeProduct" class="row">
        <?php foreach ($relatedProducts as $product): ?>
            <?php include '_product_card.php'; ?>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
<script src="assets/js/product_detail.js"></script>