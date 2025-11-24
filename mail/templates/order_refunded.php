<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Refunded</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #DC3545; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
        .order-details { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; border: 1px solid #ddd; }
        .item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
        .item:last-child { border-bottom: none; }
        .total { font-weight: bold; font-size: 18px; margin-top: 15px; padding-top: 15px; border-top: 2px solid #DC3545; }
        .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
        .refund-info { background: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #ffc107; }
        .reason-box { background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 15px 0; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Order Refunded</h1>
        <p>Your order has been cancelled and refunded</p>
    </div>

    <div class="content">
        <h2>Hi <?= htmlspecialchars($order['customer_name']) ?>,</h2>

        <p>We're writing to inform you that your order has been cancelled and a full refund has been processed.</p>

        <div class="refund-info">
            <h3>Refund Information</h3>
            <p><strong>Refund Amount:</strong> &euro;<?= number_format($order['total_amount'], 2) ?></p>
            <p><small>The refund will be processed back to your original payment method within 5-10 business days.</small></p>
        </div>

        <?php if (!empty($refund_reason)): ?>
        <div class="reason-box">
            <h4>Reason for Cancellation:</h4>
            <p><?= nl2br(htmlspecialchars($refund_reason)) ?></p>
        </div>
        <?php endif; ?>

        <div class="order-details">
            <h3>Order #<?= $order['id'] ?></h3>
            <p><strong>Order Date:</strong> <?= date('M j, Y g:i A', strtotime($order['created_at'])) ?></p>
            <p><strong>Status:</strong> Cancelled (Refunded)</p>

            <?php if (!empty($shipping_address)): ?>
            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 15px 0;">
                <h4>Original Shipping Address:</h4>
                <p>
                    <?= htmlspecialchars($shipping_address['full_name']) ?><br>
                    <?= htmlspecialchars($shipping_address['address']) ?><br>
                    <?= htmlspecialchars($shipping_address['city']) ?>, <?= htmlspecialchars($shipping_address['state']) ?> <?= htmlspecialchars($shipping_address['zip']) ?>
                </p>
            </div>
            <?php endif; ?>

            <h4>Refunded Items:</h4>
            <?php foreach ($items as $item): ?>
            <div class="item">
                <div>
                    <strong><?= htmlspecialchars($item['name']) ?></strong>
                    <?php if ($item['card_details']): ?>
                        <br><small><?= htmlspecialchars($item['card_details']) ?></small>
                    <?php endif; ?>
                </div>
                <div>
                    <?= $item['quantity'] ?> × &euro;<?= number_format($item['price'], 2) ?> = &euro;<?= number_format($item['quantity'] * $item['price'], 2) ?>
                </div>
            </div>
            <?php endforeach; ?>

            <div class="total">
                Refund Total: &euro;<?= number_format($order['total_amount'], 2) ?>
            </div>
        </div>

        <p>We apologize for any inconvenience this may have caused. If you have any questions about this refund or would like to place a new order, please don't hesitate to contact us.</p>

        <div class="footer">
            <p>Thank you for your understanding.</p>
            <p>- The Cardpoint Team</p>
            <p><small>This is an automated message, please do not reply to this email.</small></p>
        </div>
    </div>
</body>
</html>
