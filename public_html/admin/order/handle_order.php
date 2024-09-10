<?php
require_once '../../config/config.php';
require_once '../../models/OrderItemModel.php';
require_once '../../models/ProductImageModel.php';
require_once '../../models/OrderModel.php';

// Initialize models
$orderItemModel = new OrderItemModel($pdo);
$productImageModel = new ProductImageModel($pdo);
$orderModel = new OrderModel($pdo);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle POST requests for different actions
    $action = $_POST['action'] ?? null;
    $orderId = $_POST['order_id'] ?? null;
    $response = ['success' => false, 'message' => 'Invalid request'];

    switch ($action) {
        case 'changeStatus':
            if ($orderId) {
                $statusType = $_POST['status_type'] ?? '';
                $statusValue = $_POST['status_value'] ?? '';
                if ($statusType == 'delivery') {
                    $orderModel->updateShippingStatus($orderId, $statusValue);
                } elseif ($statusType == 'payment') {
                    $orderModel->updatePaymentStatus($orderId, $statusValue);
                }
                $response = ['success' => true, 'message' => 'Order status updated'];
            }
            break;
        
        case 'deleteOrder':
            if ($orderId) {
                $orderModel->deleteOrder($orderId);
                $response = ['success' => true, 'message' => 'Order deleted'];
            }
            break;

        default:
            $response['message'] = 'Unknown action';
            break;
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Handle GET requests to display order details and items
    $orderId = $_GET['order_id'] ?? null;
    if ($orderId) {
        // Fetch order details
        $order = $orderModel->getOrderById($orderId);
        $orderItems = $orderItemModel->getOrderItemsByOrderId($orderId);

        if ($order):
?>
        <div class="row mb-4">
                    <div class="col-md-4">
                        <p><strong>Mã đơn hàng:</strong> <?php echo htmlspecialchars($order['order_id']); ?></p>
                        <p><strong>Tên khách hàng:</strong> <?php echo htmlspecialchars($order['customer_first_name']) . ' ' . htmlspecialchars($order['customer_last_name']); ?></p>
                        <p><strong>Công ty:</strong> <?php echo htmlspecialchars($order['company_name']) ?: 'N/A'; ?></p>
                        <p><strong>Quốc gia:</strong> <?php echo htmlspecialchars($order['country']); ?></p>
                        <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($order['address']); ?><?php echo $order['address2'] ? ', ' . htmlspecialchars($order['address2']) : ''; ?></p>
                        <p><strong>Mã bưu điện:</strong> <?php echo htmlspecialchars($order['postal_code']) ?: 'N/A'; ?></p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Thành phố:</strong> <?php echo htmlspecialchars($order['city']); ?></p>
                        <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>

                        <p><strong>Phương thức thanh toán:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
                        <p><strong>Chi phí vận chuyển:</strong> <?php echo number_format($order['shipping_price'], 0, ',', '.'); ?> đ</p>
                        <p><strong>Tổng tiền:</strong> <?php echo number_format($order['total_price'], 0, ',', '.'); ?> đ</p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Đã thanh toán:</strong> <?php echo $order['is_paid'] ? 'Có' : 'Không'; ?></p>
                        <p><strong>Ngày thanh toán:</strong> <?php echo $order['paid_at'] ? (new DateTime($order['paid_at']))->format('d/m/Y H:i:s') : 'Chưa thanh toán'; ?></p>
                        <p><strong>Đã giao hàng:</strong> <?php echo $order['is_delivered'] ? 'Có' : 'Không'; ?></p>
                        <p><strong>Ngày giao hàng:</strong> <?php echo $order['delivered_at'] ? (new DateTime($order['delivered_at']))->format('d/m/Y H:i:s') : 'Chưa giao'; ?></p>
                        <p><strong>Ngày tạo:</strong> <?php echo (new DateTime($order['created_at']))->format('d/m/Y H:i:s'); ?></p>
                        <p><strong>Ngày cập nhật:</strong> <?php echo (new DateTime($order['updated_at']))->format('d/m/Y H:i:s'); ?></p>
                    </div>
                    <div class="col-md-12">
                        <p><strong>Ghi chú đơn hàng:</strong> <?php echo htmlspecialchars($order['order_note']) ?: 'N/A'; ?></p>
                    </div>
                </div>


            <!-- Order items -->
            
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Ảnh sản phẩm</th>
                        <th>Tên sản phẩm</th>
                        <th>Số lượng</th>
                        <th>Giá</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($orderItems): ?>
                        <?php foreach ($orderItems as $item): ?>
                            <?php
                                $productImages = $productImageModel->getImagesByProductId($item['product_id']);
                                $firstImage = !empty($productImages) ? $productImages[0]['image_url'] : 'placeholder.png';
                            ?>
                            <tr>
                                <td><img src="../../uploads/<?php echo htmlspecialchars($firstImage); ?>" alt="Product Image" style="max-width: 80px;"></td>
                                <td><?php echo htmlspecialchars($item['product_name']); ?> (<?php echo htmlspecialchars($item['flavor_name']); ?>)</td>
                                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                <td><?php echo number_format($item['unit_price'] * $item['quantity'], 0, ',', '.'); ?> đ</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">Không có sản phẩm trong đơn hàng này.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
<?php
        endif;
    }
}

?>
