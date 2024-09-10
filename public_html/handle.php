<?php
session_start();
require_once 'config/config.php';
require_once 'models/CategoryModel.php';
require_once 'models/ProductModel.php';
require_once 'models/ProductFlavorModel.php';
require_once 'models/ProductImageModel.php';
require_once 'models/ProductCategoryModel.php';

$categoryModel = new CategoryModel($pdo);
$productModel = new ProductModel($pdo);
$productFlavorModel = new ProductFlavorModel($pdo);
$productImageModel = new ProductImageModel($pdo);
$productCategoryModel = new ProductCategoryModel($pdo);

$action = isset($_POST['action']) ? $_POST['action'] : null;

$response = ['success' => false, 'message' => 'Invalid request'];
function calculateCartTotal() {
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['quantity'] * $item['product']['unit_price'];
    }
    return $total;
}
switch ($action) {
    case 'getTotalQuantity':
        $totalQuantity = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
        $response = ['success' => true, 'totalQuantity' => $totalQuantity];
        break;

    case 'getCategories':
        $categories = $categoryModel->getAllCategories();
        if ($categories) {
            $response = ['success' => true, 'categories' => $categories];
        } else {
            $response = ['success' => false, 'message' => 'No categories found'];
        }
        break;

    case 'addToCart':
        $productId = isset($_POST['productId']) ? (int)$_POST['productId'] : null;
        $flavorId = isset($_POST['flavorId']) ? (int)$_POST['flavorId'] : null;
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

        if ($productId && $flavorId) {
            $product = $productModel->getProductById($productId);
            $flavor = $productFlavorModel->getFlavorById($flavorId);
            $productImages = $productImageModel->getImagesByProductId($productId);
            $firstImage = !empty($productImages) ? $productImages[0]['image_url'] : 'placeholder.png';

            if ($product && $flavor) {
                if (!isset($_SESSION['cart'])) {
                    $_SESSION['cart'] = [];
                }
                $cartItemKey = $productId . '_' . $flavorId;
                if (isset($_SESSION['cart'][$cartItemKey])) {
                    $_SESSION['cart'][$cartItemKey]['quantity'] += $quantity;
                } else {
                    $_SESSION['cart'][$cartItemKey] = [
                        'product' => $product,
                        'flavor' => $flavor,
                        'quantity' => $quantity,
                        'image_url' => $firstImage
                    ];
                }
                $response = ['success' => true, 'message' => 'Product added to cart'];
            } else {
                $response = ['success' => false, 'message' => 'Product or flavor not found'];
            }
        } else {
            $response = ['success' => false, 'message' => 'Invalid product or flavor ID'];
        }
        break;

    case 'removeFromCart':
        $itemKey = isset($_POST['itemKey']) ? $_POST['itemKey'] : null;

        if ($itemKey && isset($_SESSION['cart'][$itemKey])) {
            unset($_SESSION['cart'][$itemKey]);
            $response = ['success' => true, 'message' => 'Item removed from cart'];
        } else {
            $response = ['success' => false, 'message' => 'Invalid cart item'];
        }
        break;

    case 'increaseQuantity':
        $itemKey = isset($_POST['itemKey']) ? $_POST['itemKey'] : null;

        if ($itemKey && isset($_SESSION['cart'][$itemKey])) {
            $_SESSION['cart'][$itemKey]['quantity']++;
            $response = ['success' => true, 'message' => 'Quantity increased', 'newQuantity' => $_SESSION['cart'][$itemKey]['quantity'],
                'newTotal' => $_SESSION['cart'][$itemKey]['quantity'] * $_SESSION['cart'][$itemKey]['product']['unit_price'],
            'cartTotal' => calculateCartTotal()];
        } else {
            $response = ['success' => false, 'message' => 'Invalid cart item'];
        }
        break;

    case 'decreaseQuantity':
        $itemKey = isset($_POST['itemKey']) ? $_POST['itemKey'] : null;

        if ($itemKey && isset($_SESSION['cart'][$itemKey]) && $_SESSION['cart'][$itemKey]['quantity'] > 1) {
            $_SESSION['cart'][$itemKey]['quantity']--;
            $response = ['success' => true, 'message' => 'Quantity decreased', 'newQuantity' => $_SESSION['cart'][$itemKey]['quantity'],
            'newTotal' => $_SESSION['cart'][$itemKey]['quantity'] * $_SESSION['cart'][$itemKey]['product']['unit_price'],
        'cartTotal' => calculateCartTotal()];
        } else {
            $response = ['success' => false, 'message' => 'Invalid cart item or quantity'];
        }
        break;

    case 'getCartItems':
            if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                $cartItems = [];
                foreach ($_SESSION['cart'] as $key => $item) {
                    $cartItems[] = [
                        'item_key' => $key,
                        'product_id' => isset($item['product']['product_id']) ? $item['product']['product_id'] : null,
                        'product_name' => isset($item['product']['product_name']) ? $item['product']['product_name'] : null,
                        'flavor_id' => isset($item['flavor']['product_flavor_id']) ? $item['flavor']['product_flavor_id'] : null,
                        'flavor_name' => isset($item['flavor']['flavor_name']) ? $item['flavor']['flavor_name'] : null,
                        'quantity' => isset($item['quantity']) ? $item['quantity'] : 0,
                        'image_url' => isset($item['image_url']) ? $item['image_url'] : 'placeholder.png',
                        'unit_price' => isset($item['product']['unit_price']) ? $item['product']['unit_price'] : 0
                    ];
                }
        
                $totalPrice = array_sum(array_map(function ($item) {
                    return $item['unit_price'] * $item['quantity'];
                }, $cartItems));
        
                $response = [
                    'success' => true,
                    'cartItems' => $cartItems,
                    'totalPrice' => number_format($totalPrice, 0, ',', '.')
                ];
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Cart is empty'
                ];
            }
            break;
    case 'getProductDetails':
        $productId = isset($_POST['productId']) ? (int)$_POST['productId'] : null;
        
        if ($productId) {
            $product = $productModel->getProductById($productId);
            $productImages = $productImageModel->getImagesByProductId($productId);
            $productFlavors = $productFlavorModel->getFlavorsByProductId($productId);
            $productCategories = $productCategoryModel->getProductCategoriesByProductId($productId);
        
            if ($product) {
                $response = [
                    'success' => true,
                    'product' => $product,
                    'images' => $productImages,
                    'flavors' => $productFlavors,
                    'categories' => $productCategories
                ];
            } else {
                $response = ['success' => false, 'message' => 'Product not found'];
            }
        } else {
            $response = ['success' => false, 'message' => 'Invalid product ID'];
        }
            break;    
         
    default:
        $response = ['success' => false, 'message' => 'Unknown action'];
        break;
}

header('Content-Type: application/json');
echo json_encode($response);
exit;