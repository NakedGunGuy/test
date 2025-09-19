<?php start_section('title'); ?>
Payment Successful - <?= htmlspecialchars($_ENV['APP_NAME']) ?>
<?php end_section('title'); ?>

<div class="maintenance-success-container">
    <div class="maintenance-success-content">
        <div class="success-icon">
            ✅
        </div>
        
        <h1 class="success-title">Payment Successful!</h1>
        
        <p class="success-message">
            Thank you for your payment! Your order has been processed successfully and you will receive a confirmation email with your order details.
        </p>
        
        <div class="store-status-notice">
            <?php if ($store_status === 'maintenance'): ?>
                <div class="notice-icon">🔧</div>
                <div class="notice-content">
                    <h3>Store Under Maintenance</h3>
                    <p>Our store is currently undergoing maintenance to improve your experience. While you completed your purchase successfully, browsing is temporarily limited.</p>
                </div>
            <?php elseif ($store_status === 'closed'): ?>
                <div class="notice-icon">🚫</div>
                <div class="notice-content">
                    <h3>Store Temporarily Closed</h3>
                    <p>Our store is temporarily closed. However, your payment was processed successfully and your order will be fulfilled normally.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="order-details">
            <div class="detail-item">
                <span class="detail-icon">📦</span>
                <span class="detail-text">Your order is being processed</span>
            </div>
            
            <div class="detail-item">
                <span class="detail-icon">📧</span>
                <span class="detail-text">Confirmation email sent</span>
            </div>
            
            <div class="detail-item">
                <span class="detail-icon">🚚</span>
                <span class="detail-text">Shipping will proceed normally</span>
            </div>
        </div>
        
        <div class="maintenance-actions">
            <button onclick="window.close()" class="btn blue">Close Window</button>
            <a href="<?= url('admin') ?>" class="btn black">Admin Access</a>
        </div>
        
        <div class="maintenance-footer">
            <p>Your order is secure and will be processed. Thank you for your patience!</p>
        </div>
    </div>
</div>

<style>
body {
    background: linear-gradient(135deg, #1E3A2E 0%, #2A4A3A 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.maintenance-success-container {
    max-width: 600px;
    padding: 20px;
    text-align: center;
}

.maintenance-success-content {
    background: #07070A;
    border: 1px solid #28A74533;
    border-radius: 20px;
    padding: 60px 40px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
}

.success-icon {
    font-size: 80px;
    margin-bottom: 30px;
    animation: bounce 2s ease-in-out infinite;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
    40% { transform: translateY(-10px); }
    60% { transform: translateY(-5px); }
}

.success-title {
    color: #28A745;
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 20px;
}

.success-message {
    color: #C0C0D1;
    font-size: 18px;
    line-height: 1.6;
    margin-bottom: 30px;
}

.store-status-notice {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    background: #1E1E27;
    border: 1px solid #FFC10733;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 30px;
    text-align: left;
}

.notice-icon {
    font-size: 24px;
    margin-top: 5px;
}

.notice-content h3 {
    color: #FFC107;
    font-size: 16px;
    font-weight: 600;
    margin: 0 0 8px 0;
}

.notice-content p {
    color: #C0C0D1;
    font-size: 14px;
    margin: 0;
    line-height: 1.5;
}

.order-details {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-bottom: 40px;
    text-align: left;
}

.detail-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 20px;
    background: #1E1E27;
    border-radius: 10px;
    border: 1px solid #28A74533;
}

.detail-icon {
    font-size: 20px;
}

.detail-text {
    color: #C0C0D1;
    font-size: 14px;
}

.maintenance-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-bottom: 30px;
    flex-wrap: wrap;
}

.btn {
    padding: 12px 24px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 120px;
}

.btn.blue {
    background: #01AFFC;
    color: #fff;
}

.btn.blue:hover {
    background: #0298d3;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(1, 175, 252, 0.3);
}

.btn.black {
    background: #1E1E27;
    color: #fff;
    border: 1px solid #C0C0D133;
}

.btn.black:hover {
    background: #2A2A35;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(255, 255, 255, 0.1);
}

.maintenance-footer {
    color: #666;
    font-size: 14px;
    border-top: 1px solid #28A74533;
    padding-top: 20px;
}

@media (max-width: 768px) {
    .maintenance-success-content {
        padding: 40px 30px;
    }
    
    .success-title {
        font-size: 28px;
    }
    
    .success-message {
        font-size: 16px;
    }
    
    .store-status-notice {
        flex-direction: column;
        text-align: center;
    }
    
    .maintenance-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .btn {
        width: 200px;
    }
}
</style>