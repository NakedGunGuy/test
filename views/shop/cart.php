<?php start_section('title'); ?>
Cart - <?= htmlspecialchars($_ENV['APP_NAME']) ?>
<?php end_section('title'); ?>
<?php start_section('page_title'); ?>Cart<?php end_section('page_title'); ?>

<?php if (!empty($error)): ?>
    <div class="error-message" style="background: #fee; border: 1px solid #fcc; color: #c00; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
        ⚠️ <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<?php partial('shop/partials/cart_list', ['cart' => $cart]); ?>
