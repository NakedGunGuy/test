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
                        <p class="meta-label">Member since <?= date('F Y', strtotime($user['created_at'] ?? 'now')) ?></p>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="section">
                <h3 class="section-subtitle">Account Overview</h3>
                <div class="grid stats" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
                    <div class="card stat">
                        <div class="icon">📦</div>
                        <div class="info">
                            <div class="number">0</div>
                            <div class="label">Total Orders</div>
                        </div>
                    </div>
                    <div class="card stat">
                        <div class="icon">💳</div>
                        <div class="info">
                            <div class="number">$0.00</div>
                            <div class="label">Total Spent</div>
                        </div>
                    </div>
                    <div class="card stat">
                        <div class="icon">🏆</div>
                        <div class="info">
                            <div class="number">New</div>
                            <div class="label">Status</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="section">
            <h2 class="section-title">Quick Actions</h2>
            
            <div class="grid actions" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
                <a href="/profile/settings" class="card action">
                    <span class="icon">⚙️</span>
                    <div class="content">
                        <div class="title">Account Settings</div>
                        <div class="desc">Update your profile information and password</div>
                    </div>
                    <span class="arrow">→</span>
                </a>
                
                <a href="/profile/orders" class="card action">
                    <span class="icon">📦</span>
                    <div class="content">
                        <div class="title">Order History</div>
                        <div class="desc">View your past orders and track shipments</div>
                    </div>
                    <span class="arrow">→</span>
                </a>
                
                <a href="/discover" class="card action">
                    <span class="icon">🛒</span>
                    <div class="content">
                        <div class="title">Continue Shopping</div>
                        <div class="desc">Browse our collection of trading cards</div>
                    </div>
                    <span class="arrow">→</span>
                </a>
                
                <a href="/cart" class="card action">
                    <span class="icon">🛍️</span>
                    <div class="content">
                        <div class="title">View Cart</div>
                        <div class="desc">Check your current cart and checkout</div>
                    </div>
                    <span class="arrow">→</span>
                </a>
            </div>

            <div class="footer-actions">
                <a href="/logout" class="btn text">Sign Out</a>
            </div>
        </div>
    </div>
</div>