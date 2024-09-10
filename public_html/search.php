<?php
session_start();
require_once 'config/config.php';
require_once 'models/ProductModel.php';

$productModel = new ProductModel($pdo);
$searchQuery = isset($_GET['q']) ? $_GET['q'] : '';
$products = $productModel->searchProductsFullInfo($searchQuery);

$pageTitle = "Search Results";

include 'header.php';
?>

<div class="search-page-container">
    <h2 class="mt-3">Kết quả tìm kiếm</h2>
    <div class="row">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="col-md-4 my-3">
                    <div class="card product-card" style="cursor: pointer;">
                        <div class="img-container">
                            <?php if (!empty($product['product_image'])): ?>
                                <img src="uploads/<?php echo $product['product_image']; ?>" class="card-img-top fixed-size-img" alt="<?php echo $product['product_name']; ?>" />
                            <?php else: ?>
                                <img src="uploads/placeholder.png" class="card-img-top fixed-size-img" alt="No Image Available" />
                            <?php endif; ?>
                            <div class="overlay" onclick="window.location.href='product_detail?productId=<?php echo $product['product_id']; ?>'"></div>
                            <button class="product-search-button" data-toggle="modal" data-target="#productModal-<?php echo $product['product_id']; ?>"><i class="fas fa-search"></i></button>
                        </div>
                        <div class="card-body p-3">
                            <p class="card-title product-name" onclick="window.location.href='product_detail?productId=<?php echo $product['product_id']; ?>'" title="<?php echo $product['product_name']; ?>"><?php echo $product['product_name']; ?></p>
                            <p class="card-text product-price">
                                <span class="new-price"><?php echo number_format($product['unit_price'], 0, ',', '.'); ?> đ</span>
                            </p>
                            <a href="product_detail?productId=<?php echo $product['product_id']; ?>" class="btn-select">TÙY CHỌN</a>
                        </div>
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="productModal-<?php echo $product['product_id']; ?>" tabindex="-1" aria-labelledby="productModalLabel-<?php echo $product['product_id']; ?>" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="productModalLabel-<?php echo $product['product_id']; ?>"><?php echo $product['product_name']; ?></h5>
                                <button type="button" class="btn btn-close" data-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                            <div class="modal-body">
                                <?php include '_product_detail.php'; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Không tìm thấy kết quả.</p>
        <?php endif; ?>
    </div>
</div>

<?php
include 'footer.php';
?>
