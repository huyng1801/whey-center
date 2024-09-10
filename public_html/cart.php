<?php
session_start();
require_once 'config/config.php';
require_once 'models/ProductModel.php';
require_once 'models/ProductFlavorModel.php';

$productModel = new ProductModel($pdo);
$productFlavorModel = new ProductFlavorModel($pdo);

$cartItems = $_SESSION['cart'] ?? [];

$totalPrice = array_sum(array_map(fn($item) => $item['product']['unit_price'] * $item['quantity'], $cartItems));
?>
<?php include 'header.php'; ?>

<div class="cart-container">
    <div class="cart-page-title">
        <div class="container d-flex flex-row flex-wrap justify-content-center align-items-center pb-3">
            <div class="flex-grow-1 text-center">
                <nav class="breadcrumbs d-flex justify-content-center align-items-center text-uppercase">
                    <a href="cart" class="text-primary current mr-2">Giỏ hàng</a>
                    <span class="text-gray divider mx-2 d-none d-sm-inline">
                        <i class="fas fa-angle-right fa-xl"></i>
                    </span>
                    <a href="checkout" class="text-gray checkout d-none d-sm-inline mr-2">Chi tiết thanh toán</a>
                    <span class="text-gray divider mx-2 d-none d-sm-inline">
                        <i class="fas fa-angle-right fa-xl"></i>
                    </span>
                    <a href="#" class="text-gray no-click d-none d-sm-inline">Hoàn tất đặt hàng</a>
                </nav>
            </div>
        </div>
    </div>

   <div class="table-container">
    <?php if (!empty($cartItems)) : ?>
            <div class="row">
                <div class="col-md-8 column-divider">
                    <div id="cartItems" class="cart-items">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="pl-0">Sản phẩm</th>
                                    <th class="mobile-hide">Hương vị</th>
                                    <th class="mobile-hide">Giá</th>
                                    <th>Số lượng</th>
                                    <th class="text-right pr-0 mobile-hide">Tạm tính</th>
                                </tr>
                            </thead>
                            <tbody class="cart-table-body">
                                <?php foreach ($cartItems as $itemKey => $item) : ?>
                                    <tr>
                                        <td class="text-left px-0 mobile-hide">
                                            <button type="button" class="btn-remove-item" data-item-key="<?= $itemKey; ?>">
                                                <i class="fa-solid fa-xmark fa-2xs"></i>
                                            </button>
                                        </td>
                                        <td class="mobile-show desktop-hide product-info">
                                            <button type="button" class="btn-remove-item" data-item-key="<?= $itemKey; ?>">
                                                <i class="fa-solid fa-xmark fa-2xs"></i>
                                            </button>
                                            <a href="product_detail?productId=<?= $item['product']['product_id']; ?>">
                                                <div class="d-flex justify-content-between align-items-center">
                                                  
                                                    <img src="uploads/<?= $item['image_url']; ?>" alt="<?= $item['product']['product_name']; ?>" class="cart-item-img">
                                                    <div><?= $item['product']['product_name']; ?>
                                                    <div class="text-gray font-weight-bold"><?= $item['flavor']['flavor_name']; ?></div>
                                                    <span><?= $item['quantity']; ?> x <b><?= number_format($item['product']['unit_price'], 0, ',', '.'); ?>₫</b></span>
                                                </div>
                                                </div>
                                            </a>
                                        </td>
                                        <td class="mobile-hide">
                                            <a href="product_detail?productId=<?= $item['product']['product_id']; ?>">
                                                <img src="uploads/<?= $item['image_url']; ?>" alt="<?= $item['product']['product_name']; ?>" class="cart-item-img">
                                            </a>
                                        </td>
                                        <td class="mobile-hide">
                                            <a class="product-link" href="product_detail?productId=<?= $item['product']['product_id']; ?>">
                                                <p><?= $item['product']['product_name']; ?></p>
                                            </a>
                                        </td>
                                        <td class="mobile-hide">
                                            <p><?= $item['flavor']['flavor_name']; ?></p>
                                        </td>
                                        <td class="font-weight-bold text-right mobile-hide">
                                            <p><?= number_format($item['product']['unit_price'], 0, ',', '.'); ?>₫</p>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-left align-items-center mb-4">
                                                <div class="quantity-container">
                                                    <button type="button" class="btn-quantity btn-decrease-qty" data-item-key="<?= $itemKey; ?>">-</button>
                                                    <input type="text" class="input-quantity" name="quantity" value="<?= $item['quantity']; ?>" readonly pattern="[0-9]*" title="Please enter a number" inputmode="numeric">
                                                    <button type="button" class="btn-quantity btn-increase-qty" data-item-key="<?= $itemKey; ?>">+</button>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="cart-item-total font-weight-bold text-right mobile-hide">
                                            <?= number_format($item['product']['unit_price'] * $item['quantity'], 0, ',', '.'); ?>₫
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <div class="row justify-content-start align-items-center mt-3">
                            <a href="index" class="btn btn-link cart-continue-shopping">← Tiếp tục xem sản phẩm</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 table-total-containter">
                    <table class="table table-total" id="cartTotal">
                        <thead>
                            <tr>
                                <th colspan="3" class="pl-0 text-left">Cộng giỏ hàng</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="2" class="text-left pl-0">Tạm tính</td>
                                <td class="cart-item-total-hold font-weight-bold text-right pr-0">
                                    <?= number_format($totalPrice, 0, ',', '.'); ?>₫
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="text-left pl-0">Tổng</td>
                                <td class="cart-item-total font-weight-bold text-right pr-0">
                                    <?= number_format($totalPrice, 0, ',', '.'); ?>₫
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div>
                        <a href="checkout" class="btn btn-danger cart-checkout mt-3">Tiến hành thanh toán hàng</a>
                    </div>
                </div>
            </div>
        <?php else : ?>
            <p>Không có sản phẩm trong giỏ hàng của bạn.</p>
        <?php endif; ?>
   </div>
</div>

<?php include 'footer.php'; ?>
<script src="assets/js/cart.js"></script>
