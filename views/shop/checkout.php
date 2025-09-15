<?php start_section('title'); ?>
Checkout - <?= htmlspecialchars($_ENV['APP_NAME']) ?>
<?php end_section('title'); ?>
<?php start_section('page_title'); ?>Checkout<?php end_section('page_title'); ?>

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
                
                <div class="order-total" id="order-total">
                    <div class="total-row">
                        <span>Subtotal:</span>
                        <span>$<?= number_format($total, 2) ?></span>
                    </div>
                    <div class="total-row">
                        <span>Weight:</span>
                        <span><?= calculate_cart_weight($cart) ?>g (<?= count($cart) ? array_sum(array_column($cart, 'quantity')) : 0 ?> cards)</span>
                    </div>
                    <div class="total-row">
                        <span>Shipping:</span>
                        <span id="shipping-cost">Select country first</span>
                    </div>
                    <div class="total-row final">
                        <span>Total:</span>
                        <span id="final-total">$<?= number_format($total, 2) ?></span>
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
                <div class="grid form" style="grid-template-columns: 1fr 1fr;">
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
                        <label class="form-label">State/Province</label>
                        <input type="text" name="state" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">ZIP/Postal Code</label>
                        <input type="text" name="zip" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Country</label>
                        <select name="country" id="country-select" class="form-input" required 
                                hx-post="/checkout/calculate-shipping" 
                                hx-target="#shipping-cost" 
                                hx-trigger="change"
                                hx-include="closest form">
                            <option value="">Select Country</option>
                            <?php foreach (get_shipping_countries() as $country): ?>
                            <option value="<?= $country['country_code'] ?>"><?= htmlspecialchars($country['country_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-help" id="shipping-estimate">Select a country to see shipping cost and delivery estimate</div>
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
                <button type="submit" class="btn blue" id="complete-order-btn">Complete Order</button>
            </div>
        </form>
    </div>
</div>

<script>
const subtotal = <?= $total ?>;

// Update total when shipping cost is calculated
document.body.addEventListener('htmx:afterRequest', function(evt) {
    if (evt.detail.elt.id === 'country-select') {
        const response = evt.detail.xhr.responseText.trim();
        
        if (response.includes('$')) {
            // Extract shipping cost from response
            const match = response.match(/\$(\d+\.?\d*)/);
            if (match) {
                const shippingCost = parseFloat(match[1]);
                const finalTotal = subtotal + shippingCost;
                document.getElementById('final-total').textContent = '$' + finalTotal.toFixed(2);
                
                // Update shipping estimate
                document.getElementById('shipping-estimate').innerHTML = '✅ ' + response;
            }
        } else {
            // No shipping available or error
            document.getElementById('shipping-estimate').innerHTML = '❌ ' + response;
        }
    }
});
</script>