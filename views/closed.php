<?php start_section('title'); ?>
Store Temporarily Closed - <?= htmlspecialchars($_ENV['APP_NAME']) ?>
<?php end_section('title'); ?>

<div class="closed-container">
    <div class="closed-content">
        <div class="closed-icon">
            🚫
        </div>
        
        <h1 class="closed-title">Store Temporarily Closed</h1>
        
        <p class="closed-message">
            We're currently closed and not accepting new orders at this time. 
            Please check back later or contact us directly for assistance.
        </p>
        
        <div class="closed-details">
            <div class="detail-item">
                <span class="detail-icon">📅</span>
                <span class="detail-text">Temporary closure - we'll reopen soon</span>
            </div>
            
            <div class="detail-item">
                <span class="detail-icon"><?= icon('cart') ?></span>
                <span class="detail-text">No new orders are being accepted</span>
            </div>
            
            <div class="detail-item">
                <span class="detail-icon">💬</span>
                <span class="detail-text">Contact us for urgent inquiries</span>
            </div>
            
            <div class="detail-item">
                <span class="detail-icon">🔄</span>
                <span class="detail-text">Existing orders will be processed normally</span>
            </div>
        </div>
        
        <div class="closed-actions">
            <a href="<?= url('admin') ?>" class="btn blue">Admin Access</a>
            <button onclick="location.reload()" class="btn black">Check Status</button>
        </div>
        
        <div class="closed-footer">
            <p>Thank you for your understanding. We appreciate your patience!</p>
        </div>
    </div>
</div>

<style>
body {
    background: linear-gradient(135deg, #2A1810 0%, #3A2520 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.closed-container {
    max-width: 600px;
    padding: 20px;
    text-align: center;
}

.closed-content {
    background: #07070A;
    border: 1px solid #D4544233;
    border-radius: 20px;
    padding: 60px 40px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
}

.closed-icon {
    font-size: 80px;
    margin-bottom: 30px;
    filter: grayscale(0.3);
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.05); opacity: 0.8; }
}

.closed-title {
    color: #fff;
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 20px;
    text-align: center;
}

.closed-message {
    color: #C0C0D1;
    font-size: 18px;
    line-height: 1.6;
    margin-bottom: 40px;
}

.closed-details {
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
    border: 1px solid #D4544233;
}

.detail-icon {
    font-size: 20px;
}

.detail-text {
    color: #C0C0D1;
    font-size: 14px;
}

.closed-actions {
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

.closed-footer {
    color: #666;
    font-size: 14px;
    border-top: 1px solid #D4544233;
    padding-top: 20px;
}

@media (max-width: 768px) {
    .closed-content {
        padding: 40px 30px;
    }
    
    .closed-title {
        font-size: 28px;
    }
    
    .closed-message {
        font-size: 16px;
    }
    
    .closed-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .btn {
        width: 200px;
    }
}
</style>