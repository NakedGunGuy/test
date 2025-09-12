<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #01AFFC; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
        .order-details { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; border: 1px solid #ddd; }
        .item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
        .item:last-child { border-bottom: none; }
        .total { font-weight: bold; font-size: 18px; margin-top: 15px; padding-top: 15px; border-top: 2px solid #01AFFC; }
        .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
        .button { display: inline-block; padding: 12px 24px; background: #01AFFC; color: white; text-decoration: none; border-radius: 5px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Order Confirmation</h1>
        <p>Thank you for your order!</p>
    </div>
    
    <div class="content">
        <h2>Hi <?= htmlspecialchars($order['customer_name']) ?>,</h2>
        
        <p>We've received your order and it's being processed. Here are the details:</p>
        
        <div class="order-details">
            <h3>Order #<?= $order['id'] ?></h3>
            <p><strong>Order Date:</strong> <?= date('M j, Y g:i A', strtotime($order['created_at'])) ?></p>
            <p><strong>Status:</strong> <?= ucfirst($order['status']) ?></p>
            
            <?php if (!empty($shipping_address)): ?>
            <h4>Shipping Address:</h4>
            <p>
                <?= htmlspecialchars($shipping_address['full_name']) ?><br>
                <?= htmlspecialchars($shipping_address['address']) ?><br>
                <?= htmlspecialchars($shipping_address['city']) ?>, <?= htmlspecialchars($shipping_address['state']) ?> <?= htmlspecialchars($shipping_address['zip']) ?>
            </p>
            <?php endif; ?>
            
            <h4>Items Ordered:</h4>
            <?php foreach ($items as $item): ?>
            <div class="item">
                <div>
                    <strong><?= htmlspecialchars($item['name']) ?></strong>
                    <?php if ($item['card_details']): ?>
                        <br><small><?= htmlspecialchars($item['card_details']) ?></small>
                    <?php endif; ?>
                </div>
                <div>
                    <?= $item['quantity'] ?> × €<?= number_format($item['price'], 2) ?> = €<?= number_format($item['quantity'] * $item['price'], 2) ?>
                </div>
            </div>
            <?php endforeach; ?>
            
            <div class="total">
                Total: €<?= number_format($order['total_amount'], 2) ?>
            </div>
        </div>
        
        <p>We'll send you another email when your order ships. If you have any questions, feel free to contact us.</p>
        
        <div class="footer">
            <p>Thank you for shopping with Cardpoint!</p>
            <p><small>This is an automated message, please do not reply to this email.</small></p>
        </div>
    </div>
</body>
</html>