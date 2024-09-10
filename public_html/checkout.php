<?php
session_start();
require_once 'config/config.php';
require_once 'models/ProductModel.php';
require_once 'models/OrderItemModel.php';
require_once 'models/ProductFlavorModel.php';

$productModel = new ProductModel($pdo);
$productFlavorModel = new ProductFlavorModel($pdo);

$cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

$totalPrice = array_sum(array_map(function ($item) {
    return $item['product']['unit_price'] * $item['quantity'];
}, $cartItems));

if (empty($cartItems)) {
    header('Location: cart');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout - WheyCenter</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            background-color: #fff;
        }
        .form-label {
            font-weight: 600;
        }
        .form-control {
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .form-check-label {
            margin-left: 10px;
        }
        .btn-order {
            background-color: #ff5722;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-order:hover {
            background-color: #e64a19;
        }
        .order-summary {
            background-color: #f8f8f8;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }
        .order-summary table {
            width: 100%;
            margin-bottom: 0;
        }
        .order-summary th, .order-summary td {
            padding: 10px;
            text-align: left;
        }
        .order-summary th {
            background-color: #e8e8e8;
            font-weight: 600;
        }
        .cart-item-img {
            max-width: 80px;
            border-radius: 5px;
            margin-right: 10px;
        }
        .text-end {
            text-align: right !important;
        }
        .checkout-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .checkout-header img {
            max-height: 80px;
        }
        .shipping-info {
            margin-top: 20px;
        }
        .checkout-footer {
            margin-top: 20px;
            padding: 10px 0;
            border-top: 1px solid #ddd;
        }
        .discount-section {
            display: flex;
            align-items: center;
        }
        .discount-section input {
            margin-right: 10px;
        }
        .payment-method-section {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .payment-method-section .form-check {
            margin-right: 20px;
        }
        .order-notes {
            background-color: #fff3cd;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container my-3">
        <div class="checkout-header">
            <img src="assets/images/logo.png" alt="Logo" height="50">
        </div>
        <form id="checkoutForm" action="checkout_process" method="POST">
            <div class="row">
                <!-- Left Column for Customer Info -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label" for="customer_first_name">Họ</label>
                        <input type="text" name="customer_first_name" id="customer_first_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="customer_last_name">Tên</label>
                        <input type="text" name="customer_last_name" id="customer_last_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="company_name">Công ty</label>
                        <input type="text" name="company_name" id="company_name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="country">Quốc gia</label>
                        <input type="text" name="country" id="country" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="address">Địa chỉ</label>
                        <input type="text" name="address" id="address" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="address2">Địa chỉ 2</label>
                        <input type="text" name="address2" id="address2" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="postal_code">Mã bưu điện</label>
                        <input type="text" name="postal_code" id="postal_code" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="city">Thành phố</label>
                        <input type="text" name="city" id="city" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="phone">Số điện thoại</label>
                        <input type="text" name="phone" id="phone" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="order_note">Ghi chú</label>
                        <textarea name="order_note" id="order_note" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phương thức thanh toán</label>
                        <div class="payment-method-section">
                            <div class="form-check">
                                <input type="radio" name="payment_method" id="COD" value="COD" class="form-check-input" checked>
                                <label class="form-check-label" for="COD">Thanh toán khi giao hàng (COD)</label>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Right Column for Order Summary -->
                <div class="col-md-6">
                    <h3 class="checkout-title">Đơn hàng <span>(<?= count($cartItems); ?> sản phẩm)</span></h3>
                    <div class="order-summary">
                        <?php if (!empty($cartItems)) : ?>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>Hương vị</th>
                                        <th>Số lượng</th>
                                        <th>Giá</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cartItems as $item) : ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="uploads/<?php echo $item['image_url']; ?>" alt="<?= $item['product']['product_name']; ?>" class="cart-item-img" />
                                                    <span><?= $item['product']['product_name']; ?></span>
                                                </div>
                                            </td>
                                            <td><?= $item['flavor']['flavor_name']; ?></td>
                                            <td><?= $item['quantity']; ?></td>
                                            <td><?= number_format($item['product']['unit_price'] * $item['quantity'], 0, ',', '.'); ?> đ</td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Tổng tiền</strong></td>
                                        <td><?= number_format($totalPrice, 0, ',', '.'); ?> đ</td>
                                    </tr>
                                </tbody>
                            </table>
                        <?php else : ?>
                            <p>Không có sản phẩm trong giỏ hàng của bạn.</p>
                        <?php endif; ?>
                    </div>
                    <div class="checkout-footer">
                        <div class="d-flex justify-content-between">
                            <span>Tạm tính</span>
                            <strong><?= number_format($totalPrice, 0, ',', '.'); ?> đ</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Phí vận chuyển</span>
                            <strong>-</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Tổng cộng</span>
                            <strong><?= number_format($totalPrice, 0, ',', '.'); ?> đ</strong>
                        </div>
                    </div>
                    <div class="form-group text-end">
                        <button type="submit" class="btn btn-order">Xác nhận đơn hàng</button>
                    </div>
                </div>
            </div>
            <div class="order-notes">
                <p>👉 Đơn hàng của bạn sau khi được xác nhận qua số điện thoại sẽ được đóng gói vào giao cho đơn vị vận chuyển</p>
                <p>👉 Đơn vị vận chuyển sẽ giao tới tận nhà bạn sau 1-2 ngày tùy vùng.</p>
                <p>👉 Cảm ơn bạn đã quan tâm và ủng hộ sản phẩm của shop 😘</p>
            </div>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha384-ZvpUoO/+P2lzz3o8CJ0U1LfsrK4F5gqE1teS1r/suETNDbx3zFs5he0zZhxM7K6M" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
