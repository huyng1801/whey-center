<?php

require_once '../../config/config.php';
require_once '../../models/OrderModel.php';
require_once '../../models/OrderItemModel.php';

$orderModel = new OrderModel($pdo);
$orderItemModel = new OrderItemModel($pdo);

$orders = $orderModel->getAllOrders();
?>

<?php require_once '../include/header.php'; ?>

<div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Mã đơn hàng</th>
                <th>Tên khách hàng</th>
                <th>Email</th>
                <th>Số điện thoại</th>
                <th>Tổng tiền</th>
                <th>Đã giao hàng</th>
                <th>Đã thanh toán</th>
                <th>Ngày tạo</th>
                <th>Ngày cập nhật</th>
                <th class="text-center">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                <td><?php echo htmlspecialchars($order['customer_first_name']) . ' ' . htmlspecialchars($order['customer_last_name']); ?></td>
                <td><?php echo htmlspecialchars($order['email']); ?></td>
                <td><?php echo htmlspecialchars($order['phone']); ?></td>
                <td><?php echo number_format($order['total_price'], 0, ',', '.'); ?></td>
                <td class="text-center">
                    <input type="checkbox" class="form-check-input order-status" id="delivery-status-<?php echo $order['order_id']; ?>" data-order-id="<?php echo $order['order_id']; ?>" data-status-type="delivery" <?php echo $order['is_delivered'] == 1 ? 'checked' : ''; ?> title="<?php echo $order['is_delivered'] == 1 ? 'Đã giao' : 'Chưa giao'; ?>">
                </td>
                <td  class="text-center">
                    <input type="checkbox" class="form-check-input order-status" id="payment-status-<?php echo $order['order_id']; ?>" data-order-id="<?php echo $order['order_id']; ?>" data-status-type="payment" <?php echo $order['is_paid'] == 1 ? 'checked' : ''; ?> title="<?php echo $order['is_paid'] == 1 ? 'Đã thanh toán' : 'Chưa thanh toán'; ?>">
                </td>


                <td><?php echo (new DateTime($order['created_at']))->format('d/m/Y H:i:s'); ?></td>
                <td><?php echo (new DateTime($order['updated_at']))->format('d/m/Y H:i:s'); ?></td>
                <td class="text-center">
                    <button type="button" class="btn-icon text-info view-order-items" data-toggle="modal" data-target="#orderItemsModal" data-order-id="<?php echo $order['order_id']; ?>"><i class="fa-solid fa-table-list"></i></button> |
                    <button type="button" class="btn-icon text-danger delete-order" data-order-id="<?php echo $order['order_id']; ?>"><i class="fa-solid fa-trash-can"></i></button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Order Items Modal -->
<div class="modal fade" id="orderItemsModal" tabindex="-1" aria-labelledby="orderItemsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderItemsModalLabel">Chi tiết đơn hàng</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div id="orderDetails" class="modal-body">
               
            </div>
        </div>
    </div>
</div>
<!-- Delete Order Modal -->
<div class="modal fade" id="deleteOrderModal" tabindex="-1" aria-labelledby="deleteOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteOrderModalLabel">Xóa đơn hàng</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa đơn hàng này không?
            </div>
            <div class="modal-footer">
                <form method="POST" id="deleteOrderForm">
                    <input type="hidden" name="action" value="deleteOrder">
                    <input type="hidden" name="order_id" id="deleteOrderId">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../include/footer.php'; ?>

<script>
$(document).ready(function () {
    // View order items
    $('.view-order-items').on('click', function () {
        var orderId = $(this).data('order-id');
        $.ajax({
            url: 'handle_order.php',
            type: 'GET',
            data: { order_id: orderId },
            success: function (data) {
                $('#orderDetails').html(data);
            },
            error: function () {
                alert('Error loading order items.');
            }
        });
    });

    // Change order status
    $('.order-status').on('change', function () {
        var orderId = $(this).data('order-id');
        var statusType = $(this).data('status-type');
        var statusValue = $(this).is(':checked') ? 1 : 0;
        $.ajax({
            url: 'handle_order.php',
            type: 'POST',
            data: {
                action: 'changeStatus',
                order_id: orderId,
                status_type: statusType,
                status_value: statusValue
            },
            success: function (response) {
                var result = JSON.parse(response);
                if (result.success) {
                    alert('Order status updated.');
                    var label = statusType === 'delivery' ? (statusValue ? 'Đã giao' : 'Chưa giao') : (statusValue ? 'Đã thanh toán' : 'Chưa thanh toán');
                    $('label[for="' + $(this).attr('id') + '"]').text(label);
                } else {
                    alert('Error updating order status.');
                }
            }.bind(this),
            error: function () {
                alert('Error updating order status.');
            }
        });
    });

   // Open delete order modal
   $('.delete-order').on('click', function () {
        var orderId = $(this).data('order-id');
        var orderName = $(this).data('name');
        $('#deleteOrderId').val(orderId);
        $('#deleteOrderModal').modal('show');
    });

    // Handle delete order form submission
    $('#deleteOrderForm').on('submit', function (event) {
        event.preventDefault();
        var form = $(this);
        $.ajax({
            url: 'handle_order.php',
            type: 'POST',
            data: form.serialize(),
            success: function (response) {
                var result = JSON.parse(response);
                if (result.success) {
                    alert('Order deleted.');
                    location.reload();
                } else {
                    alert('Error deleting order.');
                }
            },
            error: function () {
                alert('Error deleting order.');
            }
        });
    });
});
</script>
