<?php start_section('title'); ?>
Order Successful - <?= htmlspecialchars($_ENV['APP_NAME'] ?? 'Cardpoint') ?>
<?php end_section('title'); ?>

<?php 
partial('partials/message', [
    'type' => 'success',
    'icon' => '✓',
    'title' => 'Payment Successful!',
    'message' => 'Thank you for your order! Your payment has been processed successfully. You will receive a confirmation email shortly with your order details and tracking information.',
    'actions' => [
        [
            'text' => 'Continue Shopping',
            'url' => '/discover',
            'class' => 'btn blue'
        ],
        [
            'text' => 'View Orders',
            'url' => '/profile',
            'class' => 'btn black'
        ]
    ]
]);
?>