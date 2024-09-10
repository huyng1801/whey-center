<?php if ($product['is_visible']): ?>
    <div class="col-md-3 col-6 my-3">
        <div class="card product-card" style="cursor: pointer;">
            <div class="img-container">
                <img src="uploads/<?php echo !empty($product['product_image']) ? $product['product_image'] : 'placeholder.png'; ?>"
                     onclick="window.location.href='product_detail?productId=<?php echo $product['product_id']; ?>'"
                     class="card-img-top fixed-size-img" 
                     alt="<?php echo htmlspecialchars($product['product_name']) . ' - ' . htmlspecialchars($product['category_name']) . ' tại WheyCenter.vn'; ?>" 
                     loading="lazy"/>
                <button class="btn-quick-view" data-product-id="<?php echo $product['product_id']; ?>">Xem nhanh</button>
            </div>
            <div class="card-body py-2 px-0">
                <p class="card-title product-name"
                   onclick="window.location.href='product_detail?productId=<?php echo $product['product_id']; ?>'"
                   title="<?php echo $product['product_name']; ?>"><?php echo $product['product_name']; ?></p>
                <p class="card-text product-price">
                    <?php if ($product['original_price'] != $product['unit_price']): ?>
                        <span class="old-price"><?php echo number_format($product['original_price'], 0, ',', '.'); ?>₫</span>
                    <?php endif; ?>
                    <span class="new-price"><?php echo number_format($product['unit_price'], 0, ',', '.'); ?>₫</span>
                </p>
            </div>
        </div>
    </div>
<?php endif; ?>


