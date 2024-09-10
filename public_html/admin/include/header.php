<?php
session_start();

// Function to check if a user is logged in
// function isLoggedIn() {
//     return isset($_SESSION['user_id']);
// }

// // Check if user is logged in
// if (!isLoggedIn()) {
//     // Redirect to login page
//     header('Location: ../login.php');
//     exit();
// }
$fullName = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="../assets/css/styles.css" rel="stylesheet">
        <!-- CKEditor -->
    <script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>
    <!-- CKFinder -->
    <script src="https://cdn.cksource.com/ckfinder/3.5.1/ckfinder.js"></script>

</head>
<body>
    <div class="sidebar">
        <div class="logo-details my-5">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" fill="#FFFFFF"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M96 64c0-17.7 14.3-32 32-32l32 0c17.7 0 32 14.3 32 32l0 160 0 64 0 160c0 17.7-14.3 32-32 32l-32 0c-17.7 0-32-14.3-32-32l0-64-32 0c-17.7 0-32-14.3-32-32l0-64c-17.7 0-32-14.3-32-32s14.3-32 32-32l0-64c0-17.7 14.3-32 32-32l32 0 0-64zm448 0l0 64 32 0c17.7 0 32 14.3 32 32l0 64c17.7 0 32 14.3 32 32s-14.3 32-32 32l0 64c0 17.7-14.3 32-32 32l-32 0 0 64c0 17.7-14.3 32-32 32l-32 0c-17.7 0-32-14.3-32-32l0-160 0-64 0-160c0-17.7 14.3-32 32-32l32 0c17.7 0 32 14.3 32 32zM416 224l0 64-192 0 0-64 192 0z"/></svg>
            <span class="logo_name">WHEYCENTER.VN</span>
        </div>
        <ul class="nav-links">
    <?php 
    // Base folder for admin pages

    $show_dashboard = true;
    $show_admin_users = true;
    $show_banners = true;
    $show_categories = true;
    $show_origins = true;
    $show_manufacturers = true;
    $show_products = true;
    $show_orders = true;
    $show_change_password = false;
    $show_profile = false;
    
    // Current page path
    $current_page = $_SERVER['REQUEST_URI'];
    $current_page_path = parse_url($current_page, PHP_URL_PATH);
    
    // Define page names and visibility settings
    $page_names = [
        '../dashboard/dashboard' => ['Tổng quan', 'fas fa-tachometer-alt', $show_dashboard],
        '../admin_user/list_admin_user' => ['Người dùng', 'fas fa-users', $show_admin_users],
        '../banner/list_banner' => ['Quảng cáo', 'fas fa-bullhorn', $show_banners],
        '../category/list_category' => ['Danh mục', 'fas fa-list', $show_categories],
        '../origin/list_origin' => ['Xuất xứ', 'fas fa-globe', $show_origins],
        '../manufacturer/list_manufacturer' => ['Nhà sản xuất', 'fas fa-industry', $show_manufacturers],
        '../product/list_product' => ['Sản phẩm', 'fas fa-box', $show_products],
        '../order/list_order' => ['Đơn hàng', 'fas fa-file-invoice-dollar', $show_orders],
        '../admin_user/change_password' => ['Đổi mật khẩu', 'fas fa-key', $show_change_password],
        '../admin_user/profile' => ['Cập nhật thông tin', 'fas fa-user-edit', $show_profile]
    ];
    
    foreach ($page_names as $page => $info) {
        if ($info[2]) {
            $page_substring = substr($page, 3);  
            $active = (strpos($current_page_path, $page_substring)) ? 'class="active"' : '';
            echo '<li>
                    <a href="'.$page.'" '.$active.'>
                        <i class="'.$info[1].'"></i>
                        <span class="links_name">'.$info[0].'</span>
                    </a>
                  </li>';
        }
    }
    ?>
    <li class="log_out">
        <a href="../logout.php">
            <i class='fas fa-sign-out-alt'></i>
            <span class="links_name">Đăng Xuất</span>
        </a>
    </li>
</ul>

    </div>
    <section class="home-section pb-3">
        <nav>
            <div class="sidebar-button">
                <i class='fas fa-bars sidebarBtn'></i>
                <?php 
                    $current_page_name = '';
                    foreach ($page_names as $page => $info) {
                            $page_substring = substr($page, 3);  
                        if (strpos($current_page_path, $page_substring)) {
                            $current_page_name = $info[0];
                            break;
                        }
                    }
                ?>
                <span class="dashboard"><?php echo $current_page_name; ?></span>
            </div>

            <div class="profile-details" onclick="toggleDropdown()">
                <span class="admin_name"><?php echo htmlspecialchars($fullName); ?></span>
                <i class='fas fa-chevron-down'></i>
                <div class="dropdown-menu">
                    <a href="../admin_user/profile.php">Thông tin cá nhân</a>
                    <a href="../admin_user/change_password.php">Đổi mật khẩu</a>
                    <a href="../logout.php">Đăng xuất</a>
                </div>
            </div>


        </nav>
        <div class="home-content px-4">
            <!-- Your Page Content Goes Here -->
            <!-- Display Success Message -->
            <?php if (isset($success) && $success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $success; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php unset($_SESSION['success']); // Clear the success message after displaying ?>
            <?php endif; ?>

                <!-- Display Errors -->
            <?php if (isset($errors) && !empty($errors)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php 
                    $errorMessages = '';
                    foreach ($errors as $index => $error) {
                        if ($index < count($errors) - 1) {
                            $errorMessages .= htmlspecialchars($error);            
                        }
                        else {
                            $errorMessages .= htmlspecialchars($error);
                            $errorMessages .= "<br/>";
                        }
                    }
                    echo $errorMessages;
                    ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>