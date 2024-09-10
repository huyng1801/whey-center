    </main>
    <!-- Modal Template -->
    <div class="custom-modal" id="quickViewModal" style="display: none;">
        <div class="custom-modal-content">
            <span class="custom-modal-close">&times;</span>
            <div class="custom-modal-body">
                <div class="product-detail-container">
                    <div class="row">
                        <div class="col-md-6 d-flex justify-content-between align-items-start">
                            <div id="productModalImageCarousel" class="carousel">
                                <div class="carousel-inner-main"></div>
                                <button class="carousel-control-prev" type="button"><i class="fa-solid fa-chevron-left"></i></button>
                                <button class="carousel-control-next" type="button"><i class="fa-solid fa-chevron-right"></i></button>
                                <div class="carousel-dot-container"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h2 class="product-name"></h2>
                            <div class="is-devide"></div>
                            <div class="d-flex justify-content-left">
                                <span class="original-price"></span>
                                <span class="unit-price"></span>
                            </div>
                            <form id="addToCartForm" method="POST" class="add-to-cart-form">
                                <input type="hidden" name="productId">
                                <input type="hidden" name="flavorId">
                                <div class="form-group mt-2">
                                    <div class="flavor-options"></div>
                                </div>
                                <div class="d-flex justify-content-left align-items-center mb-4">
                                    <div class="quantity-container">
                                        <button type="button" class="btn-quantity btn-decrease-quantity">-</button>
                                        <input type="text" class="input-quantity" name="quantity" value="1" pattern="[0-9]*" title="Please enter a number" inputmode="numeric">
                                        <button type="button" class="btn-quantity btn-increase-quantity">+</button>
                                    </div>
                                    <button type="submit" class="add-to-cart-btn" name="btn-add-to-cart">Thêm vào giỏ hàng</button>
                                </div>
                            </form>
                            <div class="devide-group">
                                <p class="product-origin"></p>
                                <p class="product-manufacturer"></p>
                                <p class="product-categories"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Sidebar Overlay -->
    <div id="sidebarOverlay" class="sidebar-overlay desktop-hide">
        <div class="sidebar-content">
            <div class="sidebar-search">
                <form action="search" method="GET" id="sidebarSearchForm">
                        <input type="text" id="sidebarSearchInput" name="q" placeholder="Tìm kiếm...." title="Nhập từ khóa tìm kiếm">
                        <button type="submit" class="btn-search" id="sidebarSearchBtn" title="Tìm kiếm">
                            <i class="fas fa-search fa-lg"></i>
                        </button>
                </form>
            </div>
            <div class="sidebar-categories">
               <ul id="sidebarListCategories">
                 <!-- Categories will be injected here -->
               </ul>
            </div>
        </div>
    </div>
    <div class="overlay  desktop-hide" id="overlay">
        <button class="close-btn" id="closeSidebar">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>
    <button id="scrollToTopBtn" class="scroll-to-top"><i class="fa-solid fa-chevron-up"></i></button>
    <div id="alertContainer" class="alert-container"></div>
    <footer class="footer pt-5 pb-3">
        <div class="container">
            <div class="row">
                <!-- Logo and Address -->
                <div class="col-md-3 mb-3">
                    <img src="assets/images/wheycenter-logo.png" alt="WheyCenter - Shop thực phẩm bổ sung gym và Whey Protein" class="mb-2">
                    <p class="intro">WheyCenter.vn - Chuyên cung cấp thực phẩm bổ sung cho gymer.</p>
                    <p class="address"><i class="fa-solid fa-location-dot"></i> 71/1 đường số 1, P.Tân Tạo A, Q.Bình Tân</p>
                    <p class="phone"><i class="fas fa-phone-alt"></i> 0375 430 512</p>
                </div>

                <!-- Quick Links -->
                <div class="col-md-3 mb-3">
                    <h5>Truy cập nhanh</h5>
                    <ul class="list-unstyled">
                        <li><i class="fa-solid fa-chevron-right"></i><a href="#">Xem đơn hàng</a></li>
                        <li><i class="fa-solid fa-chevron-right"></i><a href="#">Tài khoản</a></li>
                        <li><i class="fa-solid fa-chevron-right"></i><a href="#">Danh sách yêu thích</a></li>
                        <li><i class="fa-solid fa-chevron-right"></i><a href="#">Đổi quà tặng</a></li>
                    </ul>
                </div>

                <!-- Information -->
                <div class="col-md-3 mb-3">
                    <h5>Thông tin</h5>
                    <ul class="list-unstyled">
                        <li><i class="fa-solid fa-chevron-right"></i><a href="#">Hướng dẫn mua hàng</a></li>
                        <li><i class="fa-solid fa-chevron-right"></i><a href="#">Hướng dẫn thanh toán</a></li>
                        <li><i class="fa-solid fa-chevron-right"></i><a href="#">Vận chuyển - đổi trả</a></li>
                        <li><i class="fa-solid fa-chevron-right"></i><a href="#">Hướng dẫn luyện tập</a></li>
                    </ul>
                </div>

                <!-- About Us -->
                <div class="col-md-3 mb-3">
                    <h5>Về chúng tôi</h5>
                    <ul class="list-unstyled">
                        <li><i class="fa-solid fa-chevron-right"></i><a href="#">Giới thiệu</a></li>
                        <li><i class="fa-solid fa-chevron-right"></i><a href="#">Sản phẩm</a></li>
                        <li><i class="fa-solid fa-chevron-right"></i><a href="#">Tin tức</a></li>
                        <li><i class="fa-solid fa-chevron-right"></i><a href="#">Liên hệ</a></li>
                    </ul>
                </div>
            </div>
        <div class="row px-3">
            <div class="col-12 divide d-flex justify-content-between px-0">
                <div class="social-icons">
                    <a href="https://www.facebook.com/teonguyen0207" target="_blank"><i class="fab fa-facebook"></i></a>
                    <a href="#" target="_blank"><i class="fab fa-tiktok"></i></a>
                    <a href="#" target="_blank"><i class="fab fa-youtube"></i></a>
                </div>
                <p class="text-center">© Bản quyền thuộc về WheyCenter.vn | Phát triển bởi <a href="https://www.facebook.com/giacathuy1801" class="developer" target="_blank">Huy Nguyễn</a></p>
            </div>
            </div>
        </div>
    </footer>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>
