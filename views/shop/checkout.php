<?php start_section('title'); ?>
<?= t('checkout.title') ?> - <?= htmlspecialchars($_ENV['APP_NAME']) ?>
<?php end_section('title'); ?>
<?php start_section('page_title'); ?><?= t('checkout.title') ?><?php end_section('page_title'); ?>

<div class="section" style="margin-bottom: 2rem;">
    <a href="<?= url('cart') ?>" class="btn text back"><?= t('button.back_to_cart') ?></a>
    <h1 class="section-title" style="margin-top: 0;"><?= t('checkout.title') ?></h1>
    <p style="color: #C0C0D1;"><?= t('checkout.review_order') ?></p>
</div>

<div class="checkout-container">
    <!-- Order Summary -->
    <div class="checkout-order">
        <div class="section">
            <h2 class="section-subtitle"><?= t('checkout.order_summary') ?></h2>
            <div class="order-items">
                <?php foreach ($cart as $item): ?>
                    <div class="order-item">
                        <div class="item-info">
                            <div class="item-name">
                                <a href="<?= url('product/' . $item['product_id']) ?>" class="product-link">
                                    <?= htmlspecialchars($item['name']) ?>
                                </a>
                            </div>
                            <div class="item-quantity"><?= t('checkout.qty', ['quantity' => $item['quantity']]) ?></div>
                        </div>
                        <div class="item-total">€<?= number_format($item['price'] * $item['quantity'], 2) ?></div>
                    </div>
                <?php endforeach; ?>

                <div class="order-total" id="order-total">
                    <div class="total-row">
                        <span><?= t('cart.subtotal') ?>:</span>
                        <span>€<?= number_format($total, 2) ?></span>
                    </div>
                    <div class="total-row">
                        <span><?= t('cart.weight') ?>:</span>
                        <span><?= calculate_cart_weight($cart) ?>g (<?= count($cart) ? array_sum(array_column($cart, 'quantity')) : 0 ?> <?= t('cart.cards') ?>)</span>
                    </div>
                    <div class="total-row">
                        <span><?= t('cart.shipping') ?>:</span>
                        <span id="shipping-cost"><?= t('cart.select_country_first') ?></span>
                    </div>
                    <div class="total-row final">
                        <span><?= t('cart.total') ?>:</span>
                        <span id="final-total">€<?= number_format($total, 2) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Checkout Form -->
    <div class="checkout-form">
        <form method="POST" action="<?= url('checkout') ?>">
            <div class="section">
                <h2 class="section-subtitle"><?= t('checkout.shipping_info') ?></h2>
                <div class="grid form" style="grid-template-columns: 1fr 1fr;">
                    <div class="form-group">
                        <label class="form-label"><?= t('form.full_name') ?></label>
                        <input type="text" name="full_name" class="form-input" value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><?= t('form.email_address') ?></label>
                        <input type="email" name="email" class="form-input" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                    </div>

                    <div class="form-group span-full">
                        <label class="form-label"><?= t('form.address') ?></label>
                        <input type="text" name="address" class="form-input" placeholder="<?= t('placeholder.address_example') ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><?= t('form.city') ?></label>
                        <input type="text" name="city" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><?= t('form.state') ?></label>
                        <input type="text" name="state" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><?= t('form.zip') ?></label>
                        <input type="text" name="zip" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><?= t('form.country') ?></label>
                        <select name="country" id="country-select" class="form-input" required
                                hx-post="<?= url('checkout/calculate-shipping') ?>"
                                hx-target="#shipping-cost"
                                hx-trigger="change"
                                hx-include="closest form">
                            <option value=""><?= t('placeholder.select_country') ?></option>
                            <?php foreach (get_shipping_countries() as $country): ?>
                            <option value="<?= $country['country_code'] ?>"><?= htmlspecialchars($country['country_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-help" id="shipping-estimate"><?= t('checkout.select_country_help') ?></div>
                    </div>
                </div>
            </div>

            <div class="section">
                <h2 class="section-subtitle"><?= t('checkout.payment') ?></h2>
                <div class="payment-info">
                    <div class="payment-note">
                        <?= icon('credit-card') ?>
                        <div class="content">
                            <div class="title"><?= t('checkout.secure_payment') ?></div>
                            <div class="desc"><?= t('checkout.demo_notice') ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="checkout-actions">
                <a href="<?= url('cart') ?>" class="btn black"><?= t('button.back_to_cart') ?></a>
                <button type="submit" class="btn blue" id="complete-order-btn"><?= t('button.complete_order') ?></button>
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
        
        if (response.includes('€')) {
            // Extract shipping cost from response
            const match = response.match(/€(\d+\.?\d*)/);
            if (match) {
                const shippingCost = parseFloat(match[1]);
                const finalTotal = subtotal + shippingCost;
                document.getElementById('final-total').textContent = '€' + finalTotal.toFixed(2);
                
                // Update shipping estimate
                document.getElementById('shipping-estimate').innerHTML = '<?= icon('check') ?> ' + response;
            }
        } else {
            // No shipping available or error
            document.getElementById('shipping-estimate').innerHTML = '<?= icon('x') ?> ' + response;
        }
    }
});
</script>