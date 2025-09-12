<?php start_section('title'); ?>
Order Cancelled - <?= htmlspecialchars($_ENV['APP_NAME']) ?>
<?php end_section('title'); ?>

<?php 
$message = 'Your payment was cancelled. No charges have been made to your account. Your items are still in your cart if you\'d like to try again.';
if (!empty($error)) {
    $message .= "\n\nError: " . htmlspecialchars($error);
}

partial('partials/message', [
    'type' => 'warning',
    'icon' => '⚠',
    'title' => 'Payment Cancelled',
    'message' => $message,
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