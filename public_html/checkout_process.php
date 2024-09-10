<?php
session_start();
require_once 'config/config.php';
require_once 'models/OrderModel.php';
require_once 'models/OrderItemModel.php';

$orderModel = new OrderModel($pdo);
$orderItemModel = new OrderItemModel($pdo);

$cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
if (empty($cartItems)) {
    header('Location: cart');
    exit;
}

$customerFirstName = $_POST['customer_first_name'];
$customerLastName = $_POST['customer_last_name'];
$companyName = $_POST['company_name'];
$country = $_POST['country'];
$address = $_POST['address'];
$address2 = $_POST['address2'];
$postalCode = $_POST['postal_code'];
$city = $_POST['city'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$orderNote = $_POST['order_note'];
$paymentMethod = $_POST['payment_method'];
$shippingPrice = 0; 
$totalPrice = array_sum(array_map(function ($item) {
    return $item['product']['unit_price'] * $item['quantity'];
}, $cartItems));

$orderData = [
    'customer_first_name' => $customerFirstName,
    'customer_last_name' => $customerLastName,
    'company_name' => $companyName,
    'country' => $country,
    'address' => $address,
    'address2' => $address2,
    'postal_code' => $postalCode,
    'city' => $city,
    'phone' => $phone,
    'email' => $email,
    'order_note' => $orderNote,
    'payment_method' => $paymentMethod,
    'shipping_price' => $shippingPrice,
    'total_price' => $totalPrice,
    'is_paid' => 0, 
    'paid_at' => null,
    'is_delivered' => 0, 
    'delivered_at' => null
];

$orderModel->addOrder($orderData);
$orderId = $pdo->lastInsertId();

foreach ($cartItems as $item) {
    $orderItemData = [
        'quantity' => $item['quantity'],
        'unit_price' => $item['product']['unit_price'],
        'order_id' => $orderId,
        'product_flavor_id' => $item['flavor']['product_flavor_id']
    ];
    $orderItemModel->addOrderItem($orderItemData);
}

unset($_SESSION['cart']);

header('Location: success');
exit;

