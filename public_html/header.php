<?php
header("X-Frame-Options: SAMEORIGIN");

$page = basename($_SERVER['SCRIPT_NAME'], '.php');

$titleMap = [
    'index' => 'Trang chủ - Mua Whey Protein tại WheyCenter.vn - Chất lượng, Uy tín',
    'product' => 'Sản phẩm - Mua Whey Protein tại WheyCenter.vn - Chất lượng, Uy tín',
    'product_detail' => 'Chi tiết sản phẩm - Mua Whey Protein tại WheyCenter.vn - Chất lượng, Uy tín',
    'cart' => 'Giỏ hàng - Mua Whey Protein tại WheyCenter.vn - Chất lượng, Uy tín',
    'checkout' => 'Thanh toán - Mua Whey Protein tại WheyCenter.vn - Chất lượng, Uy tín',
    'search' => 'Tìm kiếm - Mua Whey Protein tại WheyCenter.vn - Chất lượng, Uy tín',
    'success' => 'Thanh toán thành công - Mua Whey Protein tại WheyCenter.vn - Chất lượng, Uy tín',
];

$title = isset($titleMap[$page]) ? $titleMap[$page] : 'WheyCenter.vn';
// Define breadcrumbs
$breadcrumbs = [];

switch ($page) {
    case 'product':
        $breadcrumbs = [
            ['name' => 'Trang chủ', 'url' => 'index'],
            ['name' => 'Sản phẩm', 'url' => 'product'],
        ];
        break;

    case 'product_detail':
        $breadcrumbs = [
            ['name' => 'Trang chủ', 'url' => 'index'],
            ['name' => 'Sản phẩm', 'url' => 'product'],
            ['name' => 'Chi tiết sản phẩm', 'url' => 'product_detail'],
        ];
        break;

    case 'cart':
        $breadcrumbs = [
            ['name' => 'Trang chủ', 'url' => 'index'],
            ['name' => 'Giỏ hàng', 'url' => 'cart'],
        ];
        break;

    case 'search':
        $breadcrumbs = [
            ['name' => 'Trang chủ', 'url' => 'index'],
            ['name' => 'Tìm kiếm', 'url' => 'search'],
        ];
        break;

    case 'success':
        $breadcrumbs = [
            ['name' => 'Trang chủ', 'url' => 'index'],
            ['name' => 'Thanh toán thành công', 'url' => 'success'],
        ];
        break;

    default:
        $breadcrumbs = [
            ['name' => 'Trang chủ', 'url' => 'index'],
        ];
        break;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="robots" content="index,follow">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="WheyCenter.vn - Cung cấp thực phẩm bổ sung, sữa Whey protein chất lượng cao. Khám phá sản phẩm chính hãng từ các thương hiệu hàng đầu.">
    <meta name="keywords" content="Whey protein, thực phẩm bổ sung, WheyCenter, thể hình, sức khỏe, dinh dưỡng, sữa Whey chính hãng">
    <title><?php echo $title; ?></title>
    <link rel="shortcut icon" href="assets/images/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="assets/css/styles.css" rel="stylesheet">
    <link href="assets/css/mobile.css" rel="stylesheet">
</head>
<body>
    <div class="header-wrapper">
        <header>
            <div class="container d-flex align-items-center justify-content-between">
                <a class="sidebar-toggle desktop-hide mobile-show" href="#">
                    <i class="fa-solid fa-bars fa-xl"></i><span class="px-3"></span>
                </a>
                <a href="index">
                    <img src="assets/images/wheycenter-logo.png" alt="WheyCenter - Mua whey protein chính hãng" title="Logo">
                </a>
                <div class="search-container mobile-hide">
                    <form action="search" method="GET" id="searchForm">
                        <div class="input-group search-form">
                            <input type="text" class="form-control" id="searchInput" name="q" placeholder="Nhập vào ô tìm kiếm...." title="Nhập từ khóa tìm kiếm">
                            <button type="submit" class="btn-search" id="btn-search" title="Tìm kiếm">
                                <i class="fas fa-search fa-lg"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="desktop-hide mobile-show">
                    <a href="cart" class="cart-link">
                        <i class="fas fa-cart-shopping cart-icon fa-lg"></i>
                        <span class="cart-badge">0</span>
                    </a>
                </div>
                <div class="info-container mobile-hide">
                    <div>
                        <i class="fa-solid fa-location-dot"></i>
                        <span>71/1 đường số 1, P.Tân Tạo A, Q.Bình Tân</span>
                    </div>
                    <div>
                        <i class="fa-solid fa-phone"></i>
                        <span>0375 430 512</span>
                    </div>
                </div>
            </div>
            <div class="bg-nav py-0 mt-3 mobile-hide">
                <div class="container d-flex justify-content-between align-items-center px-0">
                    <nav class="navbar navbar-expand-sm navbar-light p-0">
                        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav mr-auto" id="categoryNav">
                                <li class="nav-item dropdown mr-2">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa-solid fa-bars fa-xl"></i><span class="px-3">Danh mục sản phẩm</span>
                                    </a>
                                    <ul class="dropdown-menu" id="categoryDropdown" aria-labelledby="navbarDropdown">
                                        <!-- Categories will be injected here -->
                                    </ul>
                                </li>
                                <li class="nav-item mr-2">
                                    <a class="nav-link" href="index">Trang chủ</a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="cart-header-container">
                            <a href="cart" class="cart-link">
                                <i class="fas fa-cart-shopping cart-icon fa-lg"></i>
                                <span id="cartItemCount" class="cart-badge">0</span>
                            </a>
                            <?php if ($page != 'cart'): ?>
                                <div class="cart-dropdown">
                                <p id="emptyCartMessage" class="text-center mb-0" style="display: none;">Chưa có sản phẩm trong giỏ hàng.</p>
                                    <div class="cart-dropdown-body">
                                        <ul id="cartItemsList" class="list-group">
                                            <!-- Cart items will be added here dynamically -->
                                        </ul>
                                    </div>
                                    <div id="totalPrice" class="total-price py-3">
                                        <strong>Tổng giá: 0₫</strong>
                                    </div>
                                    <div id="cartDropdownFooter" class="cart-dropdown-footer d-block flex-column justify-content-center align-items-center">
                                        <a href="cart" class="btn-view-cart">Xem giỏ hàng</a>
                                        <a href="checkout" class="btn-checkout-cart-dropdown">Thanh toán</a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="cart-dropdown-devide"></div>
                        <a href="checkout" class="btn-checkout">Thanh toán</a>
                    </div>
                </div>
            </div>
        </header>
    </div>                      
    <nav aria-label="breadcrumb" class="container px-0">
        <ol class="breadcrumb" id="breadcrumb-container">
        <?php if ($page !== 'index' && $page !== 'cart'): ?>
            <?php foreach ($breadcrumbs as $index => $breadcrumb): ?>
                <?php if ($index === array_key_last($breadcrumbs)): ?>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo $breadcrumb['name']; ?></li>
                <?php else: ?>
                    <li class="breadcrumb-item">
                        <a href="<?php echo $breadcrumb['url']; ?>"><?php echo $breadcrumb['name']; ?></a>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
            <?php endif; ?>
        </ol>
    </nav>
    <main class="container body-content">

 