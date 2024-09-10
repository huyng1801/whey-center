<?php
require_once 'config/config.php';
require_once 'models/ProductModel.php';
require_once 'models/BannerModel.php';

$productModel = new ProductModel($pdo);
$categories = $productModel->getCategoriesWithProducts();

$bannerModel = new BannerModel($pdo);
$banners = $bannerModel->getAllBanners();

$newsList = [];
?>
    <?php include 'header.php'; ?>

    <?php
    $visibleBanners = array_filter($banners, function($banner) {
        return $banner['is_visible'];
    });
    $visibleCount = count($visibleBanners);
    ?>

    <div class="banner-slideshow">
        <div class="banner-container">
            <?php if ($visibleCount > 0): ?>
                <?php 
                $lastBanner = end($visibleBanners); 
                $firstBanner = reset($visibleBanners);
                ?>
                <div class="banner">
                    <img src="uploads/<?php echo htmlspecialchars($lastBanner['image_url']); ?>" alt="<?php echo htmlspecialchars($lastBanner['title']); ?>  - WheyCenter.vn" />
                </div>
                <?php foreach ($visibleBanners as $banner): ?>
                    <div class="banner">
                        <img src="uploads/<?php echo htmlspecialchars($banner['image_url']); ?>" alt="<?php echo htmlspecialchars($banner['title']); ?> - WheyCenter.vn" />
                    </div>
                <?php endforeach; ?>
                <div class="banner">
                    <img src="uploads/<?php echo htmlspecialchars($firstBanner['image_url']); ?>" alt="<?php echo htmlspecialchars($firstBanner['title']); ?> - WheyCenter.vn" />
                </div>
            <?php endif; ?>
        </div>
        <?php if ($visibleCount > 1): ?>
            <button class="prev" onclick="changeSlide(-1)">&#10094;</button>
            <button class="next" onclick="changeSlide(1)">&#10095;</button>
        <?php endif; ?>
        <div class="dot-container">
            <?php for ($i = 0; $i < $visibleCount; $i++): ?>
                <span class="dot" data-slide-to="<?php echo $i; ?>" onclick="currentSlide(<?php echo $i; ?>)"></span>
            <?php endfor; ?>
        </div>
    </div>
    <?php foreach ($categories as $category): ?>
        <section id="<?php echo $category['category_id']; ?>" class="selection-category">
            <h2><a href="product?categoryId=<?php echo $category['category_id']; ?>"> <?php echo $category['category_name']; ?></a></h2>
            <div class="row">
                <?php foreach ($category['products'] as $product): ?>
                    <?php include '_product_card.php'; ?>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endforeach; ?>
    <?php include 'footer.php'; ?>
    <script src="assets/js/index.js"></script>