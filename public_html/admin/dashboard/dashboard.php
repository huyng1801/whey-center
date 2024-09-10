<?php

require_once '../../config/config.php';
require_once '../../models/OrderModel.php';
require_once '../../models/ProductModel.php';
require_once '../../models/ProductImageModel.php';

$orderModel = new OrderModel($pdo);
$productModel = new ProductModel($pdo);
$productImageModel = new ProductImageModel($pdo);

$orders = $orderModel->getAllOrders();
$totalOrders = count($orders);
$totalRevenue = array_sum(array_column($orders, 'total_price'));
$totalProfit = $totalRevenue * 0.3; // Assuming 30% profit margin
$totalReturns = array_sum(array_map(function ($order) {
    return $order['is_delivered'] == 0 ? 1 : 0;
}, $orders));

// Fetch top-selling products
$topSellingProducts = $productModel->getTopSellingProducts();

// Fetch revenue data
$currentDate = date('Y-m-d');
$currentYear = date('Y');
$currentMonth = date('m');

$dailyRevenue = $orderModel->getDailyRevenue($currentDate);
$monthlyRevenue = $orderModel->getMonthlyRevenue($currentYear, $currentMonth);
$yearlyRevenue = $orderModel->getYearlyRevenue($currentYear);

$monthlyRevenues = $orderModel->getMonthlyRevenuesByYear($currentYear);
$monthlyRevenueData = array_fill(1, 12, 0); // Initialize array with 12 months, default 0 revenue

foreach ($monthlyRevenues as $revenue) {
    $monthlyRevenueData[(int)$revenue['month']] = (float)$revenue['revenue'];
}

// Filter out months with zero revenue
$filteredMonthlyRevenueData = array_filter($monthlyRevenueData, function($revenue) {
    return $revenue > 0;
});

// Get the corresponding month labels
$monthLabels = array_keys($filteredMonthlyRevenueData);
$monthNames = ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'];
$filteredMonthLabels = array_map(function($month) use ($monthNames) {
    return $monthNames[$month - 1];
}, $monthLabels);
?>

<?php require_once '../include/header.php'; ?>

<div>
    <div class="overview-boxes">
        <div class="box">
            <div class="right-side">
                <div class="box-topic">Tổng đơn hàng</div>
                <div class="number"><?php echo number_format($totalOrders); ?></div>
                <!-- <div class="indicator">
                    <i class='fas fa-arrow-up'></i>
                    <span class="text">Tăng từ hôm qua</span>
                </div> -->
            </div>
            <i class='fas fa-shopping-cart cart'></i>
        </div>
        <div class="box">
            <div class="right-side">
                <div class="box-topic">Doanh thu hôm nay</div>
                <div class="number"><?php echo number_format($dailyRevenue, 0, ',', '.'); ?> đ</div>
                <!-- <div class="indicator">
                    <i class='fas fa-arrow-up'></i>
                    <span class="text">Tăng từ hôm qua</span>
                </div> -->
            </div>
            <i class='fas fa-dollar-sign cart two'></i>
        </div>
        <div class="box">
            <div class="right-side">
                <div class="box-topic">Doanh thu tháng này</div>
                <div class="number"><?php echo number_format($monthlyRevenue, 0, ',', '.'); ?> đ</div>
                <!-- <div class="indicator">
                    <i class='fas fa-arrow-up'></i>
                    <span class="text">Tăng từ tháng trước</span>
                </div> -->
            </div>
            <i class='fas fa-calendar-alt cart three'></i>
        </div>
        <div class="box">
            <div class="right-side">
                <div class="box-topic">Doanh thu năm nay</div>
                <div class="number"><?php echo number_format($yearlyRevenue, 0, ',', '.'); ?> đ</div>
                <!-- <div class="indicator">
                    <i class='fas fa-arrow-up'></i>
                    <span class="text">Tăng từ năm trước</span>
                </div> -->
            </div>
            <i class='fas fa-chart-line cart four'></i>
        </div>
    </div>

    <div class="sales-boxes">
        <div class="top-sales box">
            <div class="title">Sản phẩm bán chạy nhất</div>
            <ul class="top-sales-details">
                <?php foreach ($topSellingProducts as $product): ?>
                <li>
                    <a href="#">
                        <?php $firstImage = !empty($product['product_image']) ? $product['product_image'] : 'placeholder.png'; ?>
                        <img src="../../uploads/<?php echo $firstImage; ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                        <span class="product"><?php echo htmlspecialchars($product['product_name']); ?></span>
                    </a>
                    <span class="quantity">Đã bán: <?php echo htmlspecialchars($product['total_quantity']); ?> </span>
                    <span class="price"><?php echo number_format($product['unit_price'], 0, ',', '.'); ?> đ</span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <div class="revenue-charts">
        <canvas id="revenueChart"></canvas>
    </div>
</div>

<?php require_once '../include/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($filteredMonthLabels); ?>,
        datasets: [{
            label: 'Doanh thu (đ)',
            data: <?php echo json_encode(array_values($filteredMonthlyRevenueData)); ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
