<?php
require_once 'config/config.php';
require_once 'models/ProductModel.php';
require_once 'models/CategoryModel.php';

$productModel = new ProductModel($pdo);
$categoryModel = new CategoryModel($pdo);

$categoryId = $_GET['categoryId'] ?? null;
$sortBy = $_GET['sortBy'] ?? 'name_asc';

$category = $categoryModel->getCategoryWithProducts($categoryId, $sortBy);
$currentSort = $sortBy;

?>

<?php include 'header.php'; ?>

<div class="product-category-container">
    <?php if ($category): ?>
        <h2><?php echo htmlspecialchars($category['category_name']); ?></h2>
        <div class="sort-options">
            <span>Sắp xếp theo:</span>
            <div class="list-option-container">
                <div class="list-option">
                    <a class="sort-link <?php echo $currentSort == 'name_asc' ? 'active' : ''; ?>" href="product?categoryId=<?php echo $categoryId; ?>&sortBy=name_asc">
                        <i class="fas fa-sort-alpha-down"></i> Tên A → Z
                    </a>
                    <a class="sort-link <?php echo $currentSort == 'name_desc' ? 'active' : ''; ?>" href="product?categoryId=<?php echo $categoryId; ?>&sortBy=name_desc">
                        <i class="fas fa-sort-alpha-up"></i> Tên Z → A
                    </a>
                    <a class="sort-link <?php echo $currentSort == 'price_asc' ? 'active' : ''; ?>" href="product?categoryId=<?php echo $categoryId; ?>&sortBy=price_asc">
                        <i class="fas fa-sort-amount-up-alt"></i> Giá tăng dần
                    </a>
                    <a class="sort-link <?php echo $currentSort == 'price_desc' ? 'active' : ''; ?>" href="product?categoryId=<?php echo $categoryId; ?>&sortBy=price_desc">
                        <i class="fas fa-sort-amount-down"></i> Giá giảm dần
                    </a>
                    <a class="sort-link <?php echo $currentSort == 'newest' ? 'active' : ''; ?>" href="product?categoryId=<?php echo $categoryId; ?>&sortBy=newest">
                        <i class="fas fa-sort-numeric-down"></i> Hàng mới
                    </a>
                </div>
            </div>
            
        </div>
        <div class="row mt-3">
            <?php foreach ($category['products'] as $product): ?>
                <?php include '_product_card.php'; ?>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Không tìm thấy sản phẩm.</p>
    <?php endif; ?>
</div>
<?php include 'footer.php'; ?>
