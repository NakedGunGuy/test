<?php start_section('title'); ?>
Checkout - <?= htmlspecialchars($_ENV['APP_NAME']) ?>
<?php end_section('title'); ?>

<div class="section" style="margin-bottom: 2rem;">
    <a href="/cart" class="btn text back">← Back to Cart</a>
    <h1 class="section-title" style="margin-top: 0;">Checkout</h1>
    <p style="color: #C0C0D1;">Review your order and complete your purchase</p>
</div>

<div class="checkout-container">
    <!-- Order Summary -->
    <div class="checkout-order">
        <div class="section">
            <h2 class="section-subtitle">Order Summary</h2>
            <div class="order-items">
                <?php foreach ($cart as $item): ?>
                    <div class="order-item">
                        <div class="item-info">
                            <div class="item-name"><?= htmlspecialchars($item['name']) ?></div>
                            <div class="item-quantity">Qty: <?= $item['quantity'] ?></div>
                        </div>
                        <div class="item-total">$<?= number_format($item['price'] * $item['quantity'], 2) ?></div>
                    </div>
                <?php endforeach; ?>
                
                <div class="order-total">
                    <div class="total-row">
                        <span>Subtotal:</span>
                        <span>$<?= number_format($total, 2) ?></span>
                    </div>
                    <div class="total-row">
                        <span>Shipping:</span>
                        <span>Free</span>
                    </div>
                    <div class="total-row final">
                        <span>Total:</span>
                        <span>$<?= number_format($total, 2) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Checkout Form -->
    <div class="checkout-form">
        <form method="POST" action="/checkout">
            <div class="section">
                <h2 class="section-subtitle">Shipping Information</h2>
                <div class="grid form">
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-input" value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-input" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group span-full">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-input" placeholder="123 Main Street" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">State</label>
                        <input type="text" name="state" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">ZIP Code</label>
                        <input type="text" name="zip" class="form-input" required>
                    </div>
                </div>
            </div>

            <div class="section">
                <h2 class="section-subtitle">Payment</h2>
                <div class="payment-info">
                    <div class="payment-note">
                        <div class="icon">💳</div>
                        <div class="content">
                            <div class="title">Secure Payment</div>
                            <div class="desc">This is a demo store. No actual payment will be processed.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="checkout-actions">
                <a href="/cart" class="btn black">← Back to Cart</a>
                <button type="submit" class="btn blue">Complete Order</button>
            </div>
        </form>
    </div>
</div>