var isCartPage = '<?php echo $page; ?>' === 'cart'
let lastScrollY = window.scrollY;
document.addEventListener('DOMContentLoaded', function () {

    function formatPrice(price) {
        return Number(price).toLocaleString('vi-VN', { 
            minimumFractionDigits: 0, 
            maximumFractionDigits: 0 
        }) + '₫';
    }
    
    const alertContainer = document.getElementById('alertContainer');

    function createAlert(type, message) {
        const alertStrip = document.createElement('div');
        alertStrip.className = `alert-strip ${type}`;
    
        const icon = document.createElement('i');
        icon.className = `alert-strip-icon fa ${getIconClass(type)}`; 
    
        const alertMessage = document.createElement('span');
        alertMessage.textContent = message;
    
        const alertClose = document.createElement('span');
        alertClose.className = 'alert-strip-close';
        alertClose.innerHTML = '&times;';
    
        const progressBar = document.createElement('div');
        progressBar.className = 'progress-bar';
        const progressSpan = document.createElement('span');
        progressBar.appendChild(progressSpan);
        alertStrip.appendChild(progressBar);
    
        alertStrip.appendChild(icon); 
        alertStrip.appendChild(alertMessage);
        alertStrip.appendChild(alertClose);
    
        alertContainer.appendChild(alertStrip);
    
        setTimeout(() => {
            alertStrip.classList.add('show');
            progressSpan.style.width = '100%'; 
        }, 10);
    
        setTimeout(() => {
            hideAlert(alertStrip);
        }, 3000); 
    
        alertClose.addEventListener('click', () => hideAlert(alertStrip));
    }
    
    function hideAlert(alertStrip) {
        alertStrip.classList.remove('show');
        setTimeout(() => {
            alertContainer.removeChild(alertStrip);
        }, 300);
    }
    
    function showAlert(type, message) {
        createAlert(type, message);
    }
    
    function getIconClass(type) {
        switch (type) {
            case 'success':
                return 'fa-check-circle'; 
            case 'error':
                return 'fa-times-circle'; 
            case 'info':
                return 'fa-info-circle';
            case 'warning':
                return 'fa-exclamation-circle';
            default:
                return 'fa-info-circle';
        }
    }
    

    const ScrollHandler = {
        header: document.querySelector('header'),
        headerHeight: null,
        scrollToTopBtn: document.getElementById('scrollToTopBtn'),
        viewportHeight: window.innerHeight,
        lastScrollY: 0,
    
        init() {
            this.headerHeight = this.header.offsetHeight;
            this.bindEvents();
        },
    
        bindEvents() {
            document.addEventListener('scroll', this.handleScroll.bind(this));
            this.scrollToTopBtn.addEventListener('click', this.scrollToTop.bind(this));
        },
    
        handleScroll() {
            const isScrollingDown = window.scrollY > this.lastScrollY;
            
            if (document.documentElement.scrollTop > this.viewportHeight / 2) {
                this.scrollToTopBtn.classList.add('show');
            } else {
                this.scrollToTopBtn.classList.remove('show');
            }
    
            if (window.scrollY === 0) {
                if (!isScrollingDown) {
                    this.header.classList.remove('fixed');
                    this.header.style.top = `-${this.headerHeight}px`;
                }
            }
    
            if (window.scrollY > this.viewportHeight / 3) {
                if (isScrollingDown) {
                    this.header.classList.add('fixed');
                    this.header.style.top = '0';
                }
            }
    
            this.lastScrollY = window.scrollY;
        },
    
        scrollToTop() {
            document.documentElement.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }
    };
    
    ScrollHandler.init();
    
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const closeSidebar = document.getElementById('closeSidebar');
    const overlay = document.getElementById('overlay');

    sidebarToggle.addEventListener('click', function() {
        sidebarOverlay.classList.add('show');
        overlay.classList.add('show');
        getCategoriesList();
    });

    closeSidebar.addEventListener('click', function() {
        sidebarOverlay.classList.remove('show');
        overlay.classList.remove('show');
    });

    function getTotalCartQuantity() {
        $.ajax({
            url: 'handle.php',
            type: 'POST',
            data: { action: 'getTotalQuantity' },
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    $('.cart-badge').each(function() {
                        $(this).text(data.totalQuantity);
                    });
                } else {
                    console.error(data.message);
                }
            },
            error: function (xhr, status, error) {
                console.error('Error fetching total quantity:', error);
            }
        });
    }
    
    function getCategoriesList() {
        $.ajax({
            url: 'handle.php', 
            type: 'POST', 
            data: { action: 'getCategories' }, 
            dataType: 'json', 
            success: function (data) {
                if (data.success) {
                    var categoryDropdown = $('#categoryDropdown');
                    var categorySidebar = $('#sidebarListCategories');
                    
                    categoryDropdown.empty();
                    categorySidebar.empty();
                    
                    $.each(data.categories, function (index, category) {
                        var dropdownItem = $('<li>').addClass('dropdown-item');
                        var dropdownLink = $('<a>').attr('href', 'product?categoryId=' + category.category_id).text(category.category_name);
                        dropdownItem.append(dropdownLink);
                        categoryDropdown.append(dropdownItem);
                        
                        var sidebarItem = $('<li>').addClass('sidebar-item');
                        var sidebarLink = $('<a>').attr('href', 'product?categoryId=' + category.category_id).text(category.category_name);
                        sidebarItem.append(sidebarLink);
                        categorySidebar.append(sidebarItem);
                    });
                } else {
                    console.error('Error: ' + data.message);
                }
            },
            error: function (xhr, status, error) {
                console.error('Error fetching categories:', error); 
            }
        });
    }
    

    function getCartItemsDetails() {
        $.ajax({
            url: 'handle.php',
            type: 'POST',
            data: { action: 'getCartItems' },
            dataType: 'json',
            success: function (data) {

                var cartItemsList = $('#cartItemsList');
                var totalPriceElem = $('#totalPrice');
                var viewCartButton = $('#cartDropdownFooter').find('.btn-view-cart');
                var checkoutButton = $('#cartDropdownFooter').find('.btn-checkout-cart-dropdown');
                var emptyCartMessage = $('#emptyCartMessage');

                cartItemsList.empty(); 
                totalPriceElem.empty();

                if (data.success) {
                    if (data.cartItems.length > 0) {
                        var totalPrice = 0; 

                        $.each(data.cartItems, function (index, item) {
                            var listItem = $('<li>').addClass('list-group-item d-flex justify-content-between align-items-center');
                            var itemContent = $('<div>').addClass('d-flex align-items-start');

                            var imageLink = $('<a>').attr('href', `product_detail?productId=${item.product_id}`);
                            var image = $('<img>')
                                .attr('src', `uploads/${item.image_url}`)
                                .attr('alt', item.product_name)
                                .addClass('mr-2')
                                .css('width', '80px');
                            imageLink.append(image);

                            var details = $('<div>');
                            var nameLink = $('<a>')
                                .attr('href', `product_detail?productId=${item.product_id}`)
                                .append($('<h6>').text(item.product_name));
                            details.append(nameLink);
                            details.append($('<small>').text('Hương vị: ' + item.flavor_name));
                            details.append($('<br>'));
                            details.append($('<span>').html(item.quantity + ' x ' + (item.unit_price * item.quantity).toLocaleString('vi-VN') + '₫'));

                            itemContent.append(imageLink).append(details);

                            var removeButton = $('<button>')
                                .addClass('btn-remove-item btn btn-link text-danger')
                                .data('item-key', item.item_key);
                            var icon = $('<i>').addClass('fa-solid fa-xmark fa-2xs');
                            removeButton.append(icon);

                            itemContent.append(removeButton);
                            listItem.append(itemContent);
                            cartItemsList.append(listItem);

                            totalPrice += item.unit_price * item.quantity;
                        });

                        totalPriceElem.html('<strong>Tổng giá: ' + totalPrice.toLocaleString('vi-VN') + '₫</strong>');
                        viewCartButton.show();
                        checkoutButton.show(); 
                        emptyCartMessage.hide();
                    } else {
                        emptyCartMessage.text('Chưa có sản phẩm trong giỏ hàng').show();
                        totalPriceElem.hide(); 
                        viewCartButton.hide(); 
                        checkoutButton.hide();
                    }

                } else {
                    totalPriceElem.hide(); 
                    emptyCartMessage.text('Chưa có sản phẩm trong giỏ hàng').show();
                    viewCartButton.hide();
                    checkoutButton.hide();
                }
            },
            error: function (xhr, status, error) {
                $('#totalPrice').hide(); 
                $('#emptyCartMessage').text('Chưa có sản phẩm trong giỏ hàng').show(); 
                $('#cartDropdownFooter').hide(); 
            }
        });
    }

    getTotalCartQuantity();
    getCategoriesList();
    
    if (!isCartPage) {
        getCartItemsDetails();
    }

    $(document).on('click', '#quickViewModal .btn-remove-item', function () {
        var itemKey = $(this).data('item-key');
        $.ajax({
            url: 'handle.php',
            type: 'POST',
            data: { action: 'removeFromCart', itemKey: itemKey },
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    getTotalCartQuantity();
                    
                    if (!isCartPage) {
                        getCartItemsDetails();
                    }
                }
            },
            error: function (xhr, status, error) {
                console.log('error', 'Lỗi khi xóa sản phẩm!');
                console.error('Lỗi khi xóa mục:', { xhr: xhr, status: status, error: error });
            }
        });
    });

    const PlaceholderHandler = {
        searchInput: document.getElementById('searchInput'),
        placeholders: ["Tìm kiếm sản phẩm", "whey protein"],
        currentPhraseIndex: 0,
        currentIndex: 0,

        init() {
            this.searchInput.placeholder = "";
            this.typePlaceholder();
        },

        typePlaceholder() {
            if (this.currentIndex < this.placeholders[this.currentPhraseIndex].length) {
                this.searchInput.placeholder += this.placeholders[this.currentPhraseIndex].charAt(this.currentIndex);
                this.currentIndex++;
                setTimeout(this.typePlaceholder.bind(this), 100);
            } else {
                setTimeout(this.deletePlaceholder.bind(this), 1000);
            }
        },

        deletePlaceholder() {
            if (this.currentIndex > 0) {
                this.searchInput.placeholder = this.searchInput.placeholder.slice(0, -1);
                this.currentIndex--;
                setTimeout(this.deletePlaceholder.bind(this), 50);
            } else {
                this.currentPhraseIndex = (this.currentPhraseIndex + 1) % this.placeholders.length;
                setTimeout(this.typePlaceholder.bind(this), 200);
            }
        }
    };

    PlaceholderHandler.init();


    document.querySelectorAll('.btn-quick-view').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            getProductDetails(productId);
        });
    });

    document.querySelectorAll('.custom-modal-close').forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.custom-modal').style.display = 'none';
        });
    });

    window.addEventListener('click', function(event) {
        if (event.target.classList.contains('custom-modal')) {
            event.target.style.display = 'none';
        }
    });    

    function getProductDetails(productId) {
        $.ajax({
            url: 'handle.php',
            type: 'POST',
            data: { 
                action: 'getProductDetails', 
                productId: productId 
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    displayProductDetails(response);
                } else {
                    console.error(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Request failed. Returned status of ' + xhr.status + ': ' + error);
            }
        });
    }
    
    function displayProductDetails(data) {
        const product = data.product;
        const flavors = data.flavors;
        const images = data.images;
        const categories = data.categories;
        const modal = document.getElementById('quickViewModal');
        modal.style.display = 'block';

        modal.querySelector('.product-name').textContent = product.product_name;
        modal.querySelector('input[name="productId"]').value = product.product_id;
        modal.querySelector('input[name="flavorId"]').value = flavors.length > 0 ? flavors[0].product_flavor_id : '';
        modal.querySelector('.original-price').textContent = product.original_price != product.unit_price ? formatPrice(product.original_price) : '';
        modal.querySelector('.unit-price').textContent = formatPrice(product.unit_price);
        
        if(product.original_price == product.unit_price) {
            modal.querySelector('.original-price').style.display = "none";
        }
        
        const flavorContainer = modal.querySelector('.flavor-options');
        flavorContainer.innerHTML = '';
        flavors.forEach((flavor, index) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'flavor-button' + (index === 0 ? ' selected' : '');
            button.setAttribute('data-flavor-id', flavor.product_flavor_id);
            button.setAttribute('data-stock', flavor.stock_quantity);
            button.textContent = flavor.flavor_name;
            flavorContainer.appendChild(button);
        });

        const dotContainer = modal.querySelector('.carousel-dot-container');
        const mainImageContainer = modal.querySelector('.carousel-inner-main');
        const prevButton = modal.querySelector('.carousel-control-prev');
        const nextButton = modal.querySelector('.carousel-control-next');
        const dots = [];
        let slideIndex = 1;
    
        dotContainer.innerHTML = '';
        mainImageContainer.innerHTML = '';
    
        if (images.length > 0) {

            const firstImage = images[0];
            const lastImage = images[images.length - 1];
        
            const lastImageDiv = document.createElement('div');
            lastImageDiv.className = 'carousel-item-main';
            lastImageDiv.innerHTML = '<img src="uploads/' + lastImage.image_url + '" class="d-block w-100">';
            mainImageContainer.appendChild(lastImageDiv);

            images.forEach((image, index) => {
                const dot = document.createElement('span');
                dot.className = 'carousel-dot';
                dot.dataset.slideTo = index;
                dotContainer.appendChild(dot);
                dots.push(dot);
    
                const mainImage = document.createElement('div');
                mainImage.className = 'carousel-item-main';
                mainImage.innerHTML = '<img src="uploads/' + image.image_url + '" class="d-block w-100">';
                mainImageContainer.appendChild(mainImage);
            });

            const firstImageDiv = document.createElement('div');
            firstImageDiv.className = 'carousel-item-main';
            firstImageDiv.innerHTML = '<img src="uploads/' + firstImage.image_url + '" class="d-block w-100">';
            mainImageContainer.appendChild(firstImageDiv);
            
            const slides = mainImageContainer.querySelectorAll(".carousel-item-main");
            const totalSlides = slides.length;
            const originalSlidesCount = totalSlides - 2;

            function showSlides(index) {
                slideIndex = index;
                
                if (slideIndex >= totalSlides - 1) {
                    mainImageContainer.style.transition = 'none';
                    slideIndex = 0;
                    mainImageContainer.style.transform = `translateX(${-slideIndex * 100}%)`;
                    requestAnimationFrame(() => {
                        requestAnimationFrame(() => {
                            mainImageContainer.style.transition = 'transform 0.3s ease-in-out';
                            slideIndex++;
                            mainImageContainer.style.transform = `translateX(${-slideIndex * 100}%)`;
                            dots.forEach((dot, index) => {
                                dot.classList.toggle("active", index === ((slideIndex - 1 + originalSlidesCount) % originalSlidesCount));
                            });
                        });
                    });
                } else if (slideIndex <= 0) { 
                    mainImageContainer.style.transition = 'none';
                    slideIndex = totalSlides - 1;
                    mainImageContainer.style.transform = `translateX(${-slideIndex * 100}%)`;
                    requestAnimationFrame(() => {
                        requestAnimationFrame(() => {
                            mainImageContainer.style.transition = 'transform 0.3s ease-in-out';
                            slideIndex--;
                            mainImageContainer.style.transform = `translateX(${-slideIndex * 100}%)`;
                            dots.forEach((dot, index) => {
                                dot.classList.toggle("active", index === ((slideIndex - 1 + originalSlidesCount) % originalSlidesCount));
                            });
                        });
                    });
                } else {
                    mainImageContainer.style.transition = 'transform 0.3s ease-in-out';
                    mainImageContainer.style.transform = `translateX(${-slideIndex * 100}%)`;
                    dots.forEach((dot, index) => {
                        dot.classList.toggle("active", index === ((slideIndex - 1 + originalSlidesCount) % originalSlidesCount));
                    });
                }

                
            }
            prevButton.addEventListener('click', () => {
                showSlides(slideIndex - 1);
            });
    
            nextButton.addEventListener('click', () => {
                showSlides(slideIndex + 1);
            });
    
            dots.forEach(dot => {
                dot.addEventListener('click', () => {
                    showSlides(slideIndex = parseInt(dot.dataset.slideTo, 10) + 1);
                });
            });
    
            showSlides(slideIndex);
        }
        
        modal.querySelector('.product-origin').textContent = `Xuất xứ: ${product.origin_country}`;
        modal.querySelector('.product-manufacturer').textContent = `Nhà sản xuất: ${product.manufacturer_name}`;

        const categoryNames = categories.map(category => category.category_name);
        modal.querySelector('.product-categories').textContent = "Danh mục:" + categoryNames.join(', ');

        modal.querySelectorAll('.flavor-button').forEach(button => {
            button.addEventListener('click', function() {
                modal.querySelectorAll('.flavor-button').forEach(btn => btn.classList.remove('selected'));
                this.classList.add('selected');
                modal.querySelector('input[name="flavorId"]').value = this.getAttribute('data-flavor-id');
            });
        });

        modal.querySelector('.btn-increase-quantity').addEventListener('click', function() {
            const qtyInput = modal.querySelector('input[name="quantity"]');
            qtyInput.value = parseInt(qtyInput.value, 10) + 1;
        });

        modal.querySelector('.btn-decrease-quantity').addEventListener('click', function() {
            const qtyInput = modal.querySelector('input[name="quantity"]');
            if (parseInt(qtyInput.value, 10) > 1) {
                qtyInput.value = parseInt(qtyInput.value, 10) - 1;
            }
        });
    }

    const addToCartForms = document.querySelectorAll(".add-to-cart-form");

    addToCartForms.forEach(form => {
      form.addEventListener('submit', function(e) {
        e.preventDefault();
    
        const formData = new FormData(form);
        formData.append('action', 'addToCart');
    
        $.ajax({
          url: 'handle.php',
          type: 'POST',
          data: formData,
          processData: false,
          contentType: false,
          success: function(response) {
            if (response.success) {
              getTotalCartQuantity();
              getCartItemsDetails();
              if (document.getElementById('quickViewModal') != null) {
                document.getElementById('quickViewModal').style.display = 'none';
              }
              showAlert('success', 'Đã thêm sản phẩm vào giỏ hàng!');
            } else {
              showAlert('error', 'Lỗi khi thêm sản phẩm.');
            }
          },
          error: function() {
            console.error('An error occurred. Please try again.');
          }
        });
      });
    });
});