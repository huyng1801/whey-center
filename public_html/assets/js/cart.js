$(document).ready(function () {
    function showAlert(type, message) {
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
    
        document.getElementById('alertContainer').appendChild(alertStrip);
    
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
            alertStrip.parentNode.removeChild(alertStrip);
        }, 300);
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

    function updateCart(action, itemKey = null) {
        $.ajax({
            url: 'handle.php',
            type: 'POST',
            data: {
                action: action,
                itemKey: itemKey
            },
            success: function (response) {
                if (response.success) {
                    if (action === 'removeFromCart') {
                        $(`#cartItems [data-item-key="${itemKey}"]`).closest('tr').remove();
                        showAlert('success', 'Xóa sản phẩm thành công!');

                        // Check if cart is empty
                        if ($('#cartItems tbody tr').length === 0) {
                            $('.table-container').hide();
                            $('.cart-container').append('<p>Không có sản phẩm trong giỏ hàng của bạn.</p>');
                        }

                    } else if (action === 'decreaseQuantity' || action === 'increaseQuantity') {
                        const cartItemRow = $(`#cartItems [data-item-key="${itemKey}"]`).closest('tr');
                        cartItemRow.find('.input-quantity').val(response.newQuantity);
                        cartItemRow.find('.cart-item-total').text(`${response.newTotal.toLocaleString()}₫`);
                        $('#cartTotal').find('.cart-item-total').text(`${response.cartTotal.toLocaleString()}₫`);
                        $('#cartTotal').find('.cart-item-total-hold').text(`${response.cartTotal.toLocaleString()}₫`);
                    }
                } else {
                    showAlert('error', response.message);
                }
            },
            error: function () {
                showAlert('error', 'An error occurred. Please try again.');
            }
        });
    }

    $(document).on('click', '#cartItems .btn-remove-item', function () {
        const itemKey = $(this).data('item-key');
        updateCart('removeFromCart', itemKey);
    });

    $(document).on('click', '.btn-decrease-qty', function () {
        const itemKey = $(this).data('item-key');
        updateCart('decreaseQuantity', itemKey);
    });

    $(document).on('click', '.btn-increase-qty', function () {
        const itemKey = $(this).data('item-key');
        updateCart('increaseQuantity', itemKey);
    });
});
