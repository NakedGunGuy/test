<?php start_section('title'); ?>
Order Cancelled - <?= htmlspecialchars($_ENV['APP_NAME']) ?>
<?php end_section('title'); ?>

<?php 
partial('partials/message', [
    'type' => 'warning',
    'icon' => '⚠',
    'title' => 'Payment Cancelled',
    'message' => 'Your payment was cancelled. No charges have been made to your account. Your items are still in your cart if you\'d like to try again.',
    'actions' => [
        [
            'text' => 'Return to Cart',
            'url' => '/cart',
            'class' => 'btn blue'
        ],
        [
            'text' => 'Continue Shopping',
            'url' => '/discover',
            'class' => 'btn black'
        ]
    ]
]);
?>