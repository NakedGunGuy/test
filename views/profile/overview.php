<?php start_section('title'); ?>
Profile - <?= htmlspecialchars($_ENV['APP_NAME']) ?>
<?php end_section('title'); ?>

<div class="container wide">
    <div class="container profile">
        <!-- Profile Info -->
        <div>
            <!-- User Header -->
            <div class="section">
                <div class="user-header">
                    <div class="user-avatar">
                        <?= is_array($user) ? strtoupper(substr($user['username'], 0, 1)) : 'U' ?>
                    </div>
                    <div class="user-details">
                        <h1 class="product-title"><?= is_array($user) ? htmlspecialchars($user['username']) : 'Unknown User' ?></h1>
                        <p class="user-email"><?= is_array($user) ? htmlspecialchars($user['email'] ?? '') : '' ?></p>
                        <p class="meta-label"><?= t('profile.member_since') ?> <?= date('F Y', strtotime($user['created_at'] ?? 'now')) ?></p>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="section">
                <h3 class="section-subtitle"><?= t('profile.account_overview') ?></h3>
                <div class="grid stats" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
                    <div class="card stat">
                        <?= icon('package', 'icon') ?>
                        <div class="info">
                            <div class="number"><?= $stats['total_orders'] ?? 0 ?></div>
                            <div class="label"><?= t('profile.total_orders') ?></div>
                        </div>
                    </div>
                    <div class="card stat">
                        <?= icon('credit-card', 'icon') ?>
                        <div class="info">
                            <div class="number">€<?= number_format($stats['total_spent'] ?? 0, 2) ?></div>
                            <div class="label"><?= t('profile.total_spent') ?></div>
                        </div>
                    </div>
                    <div class="card stat">
                        <?= icon('trophy', 'icon') ?>
                        <div class="info">
                            <div class="number"><?= t('profile.status_' . strtolower($user_status ?? 'new')) ?></div>
                            <div class="label"><?= t('profile.status') ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="section">
            <h2 class="section-title"><?= t('profile.quick_actions') ?></h2>

            <div class="grid actions" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
                <a href="<?= url('profile/settings') ?>" class="card action">
                    <?= icon('settings', 'icon') ?>
                    <div class="content">
                        <div class="title"><?= t('profile.account_settings') ?></div>
                        <div class="desc"><?= t('profile.update_info_password') ?></div>
                    </div>
                    <span class="arrow">→</span>
                </a>

                <a href="<?= url('profile/orders') ?>" class="card action">
                    <?= icon('package', 'icon') ?>
                    <div class="content">
                        <div class="title"><?= t('profile.order_history') ?></div>
                        <div class="desc"><?= t('profile.view_past_orders') ?></div>
                    </div>
                    <span class="arrow">→</span>
                </a>

                <a href="<?= url('discover') ?>" class="card action">
                    <?= icon('search', 'icon') ?>
                    <div class="content">
                        <div class="title"><?= t('profile.continue_shopping') ?></div>
                        <div class="desc"><?= t('profile.browse_collection') ?></div>
                    </div>
                    <span class="arrow">→</span>
                </a>

                <a href="<?= url('cart') ?>" class="card action">
                    <?= icon('shopping-bag', 'icon') ?>
                    <div class="content">
                        <div class="title"><?= t('button.view_cart') ?></div>
                        <div class="desc"><?= t('profile.check_cart_checkout') ?></div>
                    </div>
                    <span class="arrow">→</span>
                </a>
            </div>

            <div class="footer-actions">
                <a href="<?= url('logout') ?>" class="btn text"><?= t('profile.sign_out') ?></a>
            </div>
        </div>
    </div>
</div>