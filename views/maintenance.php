<?php start_section('title'); ?>
Store Under Maintenance - <?= htmlspecialchars($_ENV['APP_NAME']) ?>
<?php end_section('title'); ?>

<div class="maintenance-container">
    <div class="maintenance-content">
        <div class="maintenance-icon">
            🔧
        </div>
        
        <h1 class="maintenance-title">We're Under Maintenance</h1>
        
        <p class="maintenance-message">
            We're currently performing some updates to improve your shopping experience. 
            We'll be back online shortly!
        </p>
        
        <div class="maintenance-details">
            <div class="detail-item">
                <span class="detail-icon">⏰</span>
                <span class="detail-text">Expected downtime: Minimal</span>
            </div>
            
            <div class="detail-item">
                <span class="detail-icon">🔄</span>
                <span class="detail-text">We're working on improvements</span>
            </div>
            
            <div class="detail-item">
                <span class="detail-icon">📧</span>
                <span class="detail-text">Questions? Contact our support team</span>
            </div>
        </div>
        
        <div class="maintenance-actions">
            <a href="/admin" class="btn blue">Admin Access</a>
            <button onclick="location.reload()" class="btn black">Check Again</button>
        </div>
        
        <div class="maintenance-footer">
            <p>Thank you for your patience!</p>
        </div>
    </div>
</div>

<style>
body {
    background: linear-gradient(135deg, #1E1E27 0%, #2A2A35 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.maintenance-container {
    max-width: 600px;
    padding: 20px;
    text-align: center;
}

.maintenance-content {
    background: #07070A;
    border: 1px solid #C0C0D133;
    border-radius: 20px;
    padding: 60px 40px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
}

.maintenance-icon {
    font-size: 80px;
    margin-bottom: 30px;
    animation: rotate 3s linear infinite;
}

@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.maintenance-title {
    color: #fff;
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 20px;
    text-align: center;
}

.maintenance-message {
    color: #C0C0D1;
    font-size: 18px;
    line-height: 1.6;
    margin-bottom: 40px;
}

.maintenance-details {
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
    border: 1px solid #C0C0D133;
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
    border-top: 1px solid #C0C0D133;
    padding-top: 20px;
}

@media (max-width: 768px) {
    .maintenance-content {
        padding: 40px 30px;
    }
    
    .maintenance-title {
        font-size: 28px;
    }
    
    .maintenance-message {
        font-size: 16px;
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